<?php

namespace BeycanPress\CryptoPay\WooCommerce\Payment;

use \Beycan\Response;
use \Beycan\CurrencyConverter;
use \BeycanPress\CryptoPay\WooCommerce\Settings;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Api;
use \BeycanPress\CryptoPay\WooCommerce\Models\Transaction;
use \BeycanPress\CryptoPay\WooCommerce\Services\Verifier;

class CheckoutApi extends Api
{
    /**
     * @var object
     */
    private $order;

    /**
     * @var int
     */
    private $userId;

    public function __construct()
    {
        $this->userId = get_current_user_id();
        $this->addRoutes([
            'cryptopay-api/woocommerce' => [
                'check-payment' => [
                    'callback' => 'checkPayment',
                    'methods' => ['POST']
                ],
                'save-transaction' => [
                    'callback' => 'saveTransaction',
                    'methods' => ['POST']
                ],
                'payment-finished' => [
                    'callback' => 'paymentFinished',
                    'methods' => ['POST']
                ],
                'currency-converter' => [
                    'callback' => 'currencyConverter',
                    'methods' => ['POST']
                ]
            ]
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function checkPayment(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));

        if ($this->order->get_status() != 'pending') {
            Response::error(esc_html__('This order is not waiting for payment.', 'cryptopay'), [
                'redirect' => $this->order->get_view_order_url()
            ]);
        }

        if (!$paymentPrice = $this->calculatePaymentPrice($paymentInfo)) {
            Response::error(esc_html__('There was a problem converting currency!', 'cryptopay'), null, 'CCERR');
        }

        $paymentAddress = apply_filters('CryptoPay/WooCommerce/PaymentAddress', $this->setting('payment_address'), $this->order);

        Response::success(null, [
            'paymentPrice' => $paymentPrice,
            'receiver' => $paymentAddress
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function paymentFinished(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        
        if (!isset($paymentInfo->transactionId)) {
            Response::badRequest(esc_html__('Please enter a valid data.', 'cryptopay'));
        }

        $txModel = new Transaction();
        if (!$transaction = $txModel->findOneBy(['transaction_id' => $paymentInfo->transactionId])) {
            Response::error(esc_html__('Transaction record not found!', 'cryptopay'));
        }

        $verifier = new Verifier();

        try {
            $result = $verifier->verifyTransaction($paymentInfo);
        } catch (\Exception $e) {
            Response::error(esc_html__('Payment not verified via Blockchain', 'cryptopay'), [
                'redirect' => 'reload',
                'message' => $e->getMessage()
            ]);
        }

        $paymentInfo->status = $result == 'failed' ? 'failed' : 'verified';

        do_action(
            'CryptoPay/WooCommerce/PaymentFinished', 
            $this->userId, $this->order, $paymentInfo
        );

        if ($result == 'verified') {
            $verifier->updateOrderAsComplete($this->order, $transaction->id);
            Response::success(esc_html__('Payment completed successfully', 'cryptopay'), [
                'redirect' => $this->order->get_checkout_order_received_url()
            ]);
        } else {
            $verifier->updateOrderAsFail($this->order, $transaction->id);
            Response::error(esc_html__('Payment not verified via Blockchain', 'cryptopay'), [
                'redirect' => $this->order->get_view_order_url()
            ]);
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function saveTransaction(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        
        $transactionData = [
            'transaction_id' => $paymentInfo->transactionId,
            'order_id' => $paymentInfo->order->id,
            'user_id' => $this->userId,
            'status' => 'pending',
            'payment_info' => serialize($paymentInfo)
        ];
        
        (new Transaction())->insert($transactionData);

        $this->order->update_meta_data(
            esc_html__('Blockchain network', 'cryptopay'),
            $paymentInfo->usedChain->name
        );

        $this->order->update_meta_data(
            esc_html__('Transaction id', 'cryptopay'),
            $paymentInfo->transactionId
        );

        $this->order->update_meta_data(
            esc_html__('Payment currency', 'cryptopay'),
            $paymentInfo->paymentCurrency
        );

        $this->order->update_meta_data(
            esc_html__('Payment price', 'cryptopay'),
            $paymentInfo->paymentPrice
        );

        if (isset($paymentInfo->discountRate)) {
            $this->order->update_meta_data(
                esc_html__('Discount', 'cryptopay'),
                $paymentInfo->discountRate . ' %'
            );

            $this->order->update_meta_data(
                esc_html__('Real price', 'cryptopay'),
                $paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency
            );
        }

        $this->order->update_meta_data(
            esc_html__('Sender address', 'cryptopay'),
            $paymentInfo->senderAddress
        );

        $this->order->update_status('wc-on-hold');

        $this->order->save();

        do_action(
            'CryptoPay/WooCommerce/PaymentStarted', 
            $this->userId, $this->order, $paymentInfo
        );

        Response::success();
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function currencyConverter(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));

        if (!$paymentPrice = $this->calculatePaymentPrice($paymentInfo)) {
            Response::error(esc_html__('There was a problem converting currency!', 'cryptopay'), null, 'CCERR');
        }

        Response::success(null, [
            'paymentPrice' => $paymentPrice
        ]);
    }

    /**
     * @param string $paymentInfo
     * @return object
     */
    public function validatePaymentInfo(string $paymentInfo) : object
    {
        $paymentInfo = !is_null($paymentInfo) ? $this->parseJson($paymentInfo) : false;
        
        if (!$paymentInfo || !isset($paymentInfo->order)) {
            Response::badRequest(esc_html__('Please enter a valid data.', 'cryptopay'));
        }

        if (!$this->order = wc_get_order($paymentInfo->order->id)) {
            Response::error(esc_html__('The relevant order was not found!', 'cryptopay'), null, 'NOOR');
        }

        return $paymentInfo;
    }

    /**
     * @param object $paymentInfo
     * @return null|float
     */
    public function calculatePaymentPrice(object $paymentInfo) : ?float
    {
        $selectedCurrency = $paymentInfo->selectedCurrency;
        $customTokens = Settings::getCustomTokens();
        $tokenDiscounts = Settings::getTokenDiscounts();

        $orderPrice = (float) $this->order->get_total();
        $orderCurrency = $this->order->get_currency();
        $paymentCurrency = $selectedCurrency->symbol;
        
        $paymentInfo->order->price = $orderPrice;
        $paymentInfo->order->currency = $orderCurrency;

        $paymentPrice = apply_filters(
            "CryptoPay/WooCommerce/CurrencyConverter", 
            "no-custom-converter", 
            $paymentInfo
        );

        if (is_null($paymentPrice)) return null;

        if ($paymentPrice == 'no-custom-converter') {
            if (isset($customTokens[$paymentCurrency])) {
                $customToken = $customTokens[$paymentCurrency];
                if ($customToken[$orderCurrency]) {
                    $paymentPrice = $this->toFixed(($orderPrice / $customToken[$orderCurrency]), 6);
                } else {
                    return null;
                }
            } else {
                try {
                    $converter = new CurrencyConverter('cryptocompare');
                    $paymentPrice = $converter->convert($orderCurrency, $paymentCurrency, $orderPrice);
        
                    if (!$paymentPrice) return null;
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        if (isset($tokenDiscounts[$paymentCurrency])) {
            $discountRate = $tokenDiscounts[$paymentCurrency];
            $discountPrice = ($paymentPrice * $discountRate) / 100;
            return $this->toFixed(($paymentPrice - $discountPrice), 6);
        }

        return $this->toFixed($paymentPrice, 6);
    }
}
