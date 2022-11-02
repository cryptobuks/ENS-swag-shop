<?php
/**
 * Class Smartcrawl_Controller_Upgrade_Page
 *
 * @package SmartCrawl
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Smartcrawl_Controller_Upgrade_Page extends Smartcrawl_Admin_Page {

	use Smartcrawl_Singleton;

	protected function init() {
	}

	public function get_menu_slug() {
		return '';
	}
}