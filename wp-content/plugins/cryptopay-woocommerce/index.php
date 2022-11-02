<?php defined('ABSPATH') || exit;

/**
 * Plugin Name: CryptoPay WooCommerce
 * Version:     2.4.1
 * Plugin URI:  https://cryptopay-woocommerce.beycanpress.com/
 * Description: Payment plugin with her cryptocurrency wallet for WooCommerce
 * Author: BeycanPress
 * Author URI:  https://www.beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.tr.html
 * Text Domain: cryptopay
 * Domain Path: /languages
 * Tags: Cryptopay, Cryptocurrency, WooCommerce, WordPress, MetaMask, Trust, Binance, Wallet, Ethereum, Bitcoin, Binance smart chain, Payment, Plugin, Gateway
 * Requires at least: 5.0
 * Tested up to: 6.0
 * Requires PHP: 7.4
*/

require __DIR__ . '/vendor/autoload.php';
new \BeycanPress\CryptoPay\WooCommerce\Loader(__FILE__);