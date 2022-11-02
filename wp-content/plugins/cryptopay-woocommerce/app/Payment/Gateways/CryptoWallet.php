<?php

namespace BeycanPress\CryptoPay\WooCommerce\Payment\Gateways;

use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;

class CryptoWallet extends \WC_Payment_Gateway
{   
    use Helpers;

    public static $gateway = 'cpwcw';

    /**
     * @return void
     */
    public function __construct()
    {
        $this->id = self::$gateway;
        $this->method_title = esc_html__('CryptoPay Wallet Gateway', 'cryptopay');
        $this->method_description = esc_html__('With CryptoPay, your customers can easily pay with their crypto wallet.', 'cryptopay');

        // gateways can support subscriptions, refunds, saved payment methods,
        // but in this tutorial we begin with simple payments
        $this->supports = ['products', 'subscriptions'];

        // Method with all the options fields
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->enabled = $this->get_option('enabled');
        $this->description = $this->get_option('description');
		$this->order_button_text = $this->get_option('order_button_text');

        // This action hook saves the settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * @return void
     */
    public function init_form_fields() : void
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'       => esc_html__('Enable/Disable', 'cryptopay'),
                'label'       => esc_html__('Enable', 'cryptopay'),
                'type'        => 'checkbox',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => esc_html__('Title', 'cryptopay'),
                'type'        => 'text',
                'description' => esc_html__('This controls the title which the user sees during checkout.', 'cryptopay'),
                'default'     => esc_html__('Pay with Crypto Wallet', 'cryptopay')
            ),
            'description' => array(
                'title'       => esc_html__('Description', 'cryptopay'),
                'type'        => 'textarea',
                'description' => esc_html__('This controls the description which the user sees during checkout.', 'cryptopay'),
                'default'     => esc_html__('You can pay with supported networks and cryptocurrencies using crypto wallet.', 'cryptopay'),
            ),
            'order_button_text' => array(
                'title'       => esc_html__('Order button text', 'cryptopay'),
                'type'        => 'text',
                'description' => esc_html__('Pay button on the checkout page', 'cryptopay'),
                'default'     => esc_html__('Proceed to CryptoPay', 'cryptopay'),
            ),
        );
    }

    /**
     * @return mixed
     */
    public function get_icon() : string
    {
        $iconHtml = '<img src="'.esc_url($this->getImageUrl('icon.png')).'" width="25" height="25">';
        return apply_filters('woocommerce_gateway_icon', $iconHtml, $this->id);
    }

    /**
     * @return void
     */
    public function payment_fields() : void
    {
        echo esc_html($this->description);
    }

    /**
     * @param int $orderId
     * @return array
     */
    public function process_payment($orderId) : array
    {
        global $woocommerce;
        $order = new \WC_Order($orderId);

        // update order status
        $order->update_status('wc-pending', esc_html__( 'Payment is awaited.', 'cryptopay'));

        // add note
        $order->add_order_note(esc_html__('Customer has chosen CryptoPay Wallet payment method, payment is pending.', 'cryptopay'));

        // Remove cart
        $woocommerce->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        );  
    }
}