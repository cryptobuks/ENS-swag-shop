<?php

namespace BeycanPress\CryptoPay\WooCommerce\Payment;

use \BeycanPress\CryptoPay\WooCommerce\Settings;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;

class Register
{
    use Helpers;
    
    public function __construct()
    {   
        if (function_exists('WC')) {
            if ($this->setting('payment_address') == '') {
                $this->adminNotice(esc_html__('If you did not specify a wallet address in the CryptoPay WooCommerce settings, the plugin will not work. Please specify a wallet address first.', 'cryptopay'), 'error');
            } else {
                // Register gateways
                add_filter('woocommerce_payment_gateways', function($gateways) {
                    $gateways[] = Gateways\CryptoWallet::class;
                    return $gateways;
                });
                
                if (!is_admin()) {
                    new Details();
                    new Checkout();
                } else {
                    if (in_array('walletconnect', Settings::getAcceptedWallets()) && $this->setting('infuraProjectId') == '') {
                        $this->adminNotice(esc_html__('Please enter an infura project id for WalletConnect to work.  - CryptoPay WooCommerce', 'cryptopay'), 'error');
                    }
                }
            }
        } else {
            $this->adminNotice(esc_html__('The “CryptoPay WooCommerce” plugin cannot run without WooCommerce active. Please install and activate WooCommerce plugin.', 'cryptopay'), 'error');
        }
    }
}