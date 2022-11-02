<?php

namespace BeycanPress\CryptoPay\WooCommerce\Payment;

use \Beycan\MultiChain\Utils;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\CryptoPay\WooCommerce\Models\Transaction;
use \BeycanPress\CryptoPay\WooCommerce\Services\Verifier;

class Details
{
    use Helpers;

    /**
     * @var object
     */
    private $verifier;

    public function __construct()
    {
        $this->verifier = new Verifier();

        // Ürün listesi yüklenmeden önce beklemede olan işlemleri doğrular
        add_action('woocommerce_before_account_orders', function() {
            $this->verifier->verifyPendingTransactions(get_current_user_id());
        });

        // Detay sayfasını teşekkür ve sipariş gösterim sayfasına dahil eder
        add_action('woocommerce_view_order', array($this, 'init'), 4);
        add_action('woocommerce_thankyou_'. Gateways\CryptoWallet::$gateway , array($this, 'init'), 1);
    }

    /**
     * Detay bölümünü yükler
     * @param int $orderId
     * @return void
     */
    public function init($orderId) : void
    {
        // Detay yüklenmeden önce beklemede olan işlemleri doğrular
        $this->verifier->verifyPendingTransactions(get_current_user_id());

        $order = wc_get_order($orderId);

        if (Gateways\CryptoWallet::$gateway == $order->get_payment_method()) {
            $transaction = (new Transaction)->findOneBy([
                'order_id' => $orderId
            ], ['id', 'DESC']);
    
            if ($order->get_status() == 'pending') {
                $this->viewEcho('payment/pending', ['payUrl' => $order->get_checkout_payment_url(true)]);
            } elseif (!is_null($transaction)) {
                $paymentInfo = unserialize($transaction->payment_info);
                $paymentPrice = Utils::toString($paymentInfo->paymentPrice, $paymentInfo->selectedCurrency->decimals);
                $this->viewEcho('payment/details', compact('transaction', 'paymentPrice', 'paymentInfo'));
            }
        }
    }

}