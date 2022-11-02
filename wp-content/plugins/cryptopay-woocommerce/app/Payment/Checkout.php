<?php

namespace BeycanPress\CryptoPay\WooCommerce\Payment;

use \BeycanPress\CryptoPay\WooCommerce\Lang;
use \BeycanPress\CryptoPay\WooCommerce\Settings;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\CryptoPay\WooCommerce\Models\Transaction;

class Checkout
{
    use Helpers;
    
    /**
     * @var object
     */
    private $order;

    /**
     * @var object
     */
    private $api;

    /**
     * @return void
     */
    public function __construct()
    { 
        $this->api = new CheckoutApi();
        add_action('woocommerce_receipt_' . Gateways\CryptoWallet::$gateway, array($this, 'init'), 1);
    }

    /**
     * Ödeme bölümünü yükler
     * @param int $orderId
     * @return void
     */
    public function init($orderId) : void
    {   
        $this->order = wc_get_order($orderId);

        if ($this->order->get_status() != 'pending') {
            echo esc_html__('This order is not waiting for payment.', 'cryptopay');
        } else {

            $transaction = (new Transaction)->findOneBy([
                'order_id' => $orderId
            ], ['id', 'DESC']);

            if (!is_null($transaction) && $transaction->status == 'pending') {
                wp_redirect($this->order->get_checkout_order_received_url()); exit;
            }

            if ($this->setting('only_logged_in_user') && !is_user_logged_in()) {
                echo esc_html__('Please login to make a payment!', 'cryptopay');
            } else {
                $this->loadScripts();
                $this->viewEcho('payment/checkout');
            }
        }
    }

    /**
     * javascript ve css dosyalarını dahil eder
     * @return void
     */
    public function loadScripts() : void
    { 
        $this->addScript('js/multi-chain.min.js');
        $this->addScript('cryptopay/js/chunk-vendors.js');
        $this->addScript('cryptopay/js/app.js');
        $this->addStyle('cryptopay/css/app.css');
        $key = $this->addScript('js/main.js');
        wp_localize_script($key, 'CryptoPayWooCommerce', $this->jsData());
    }
    
    /**
     * Javascript'e gönderilecek dinamik veriyi hazırlar
     * @return array
     */
    private function jsData() : array
    {
        $converterApi = apply_filters(
            "CryptoPay/WooCommerce/ConverterApi", 
            $this->setting('converterApi')
        );

        return [
            'order'=> [
                'id' => (int) $this->order->get_id(),
                'price' => (float) $this->order->get_total(),
                'currency' => strtoupper($this->order->get_currency())
            ],
            'mode' => 'payment',
            'lang' => Lang::get(),
            'acceptedChains' => Settings::getAcceptedChains(),
            'acceptedWallets' => Settings::getAcceptedWallets(),
            'imagesUrl' => $this->pluginUrl . 'assets/images/',
            'tokenDiscounts' => Settings::getTokenDiscounts(),
            'customTokens' => Settings::getCustomTokens(),
            'testnets' => (bool) $this->setting('testnets'),
            'infuraId' => $this->setting('infuraProjectId'),
            'apiUrl' => $this->api->getUrl(),
            'converter' => $converterApi,
        ];
    }
}