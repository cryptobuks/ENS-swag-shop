<?php

namespace BeycanPress\CryptoPay\WooCommerce;

class Loader extends PluginHero\Plugin
{
    public function __construct($pluginFile)
    {
        parent::__construct([
            'pluginFile' => $pluginFile,
            'textDomain' => 'cryptopay',
            'pluginKey' => 'cryptopay_woocommerce',
            'settingKey' => 'cryptopay_woocommerce_settings',
            'pluginVersion' => '2.4.1'
        ]);

        if ($this->setting('license')) {
            add_action('plugins_loaded', function() {
                new Payment\Register();
            });
        } else {
            $this->adminNotice(esc_html__('In order to use the "CryptoPay WooCommerce" Plugin, please enter your license (purchase) code in the license field in the settings section.', 'cryptopay'), 'error');
        }
    }

    public function adminProcess() : void
    {
        new Pages\TransactionList();
        
        add_action('init', function(){
            new Settings;
        }, 9);
    }

    public static function activation() : void
    {
        (new Models\Transaction())->createTable();
    }

    public static function uninstall() : void
    {
        $settings = get_option(self::$instance->settingKey);
        if (isset($settings['dds']) && $settings['dds']) {
            delete_option(self::$instance->settingKey);
            delete_option('woocommerce_'.Payment\Gateways\CryptoWallet::$gateway.'_settings');
            (new Models\Transaction())->drop();
        }
    }
}
