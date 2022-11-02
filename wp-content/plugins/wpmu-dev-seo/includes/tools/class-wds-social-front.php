<?php
/**
 * Class Smartcrawl_Social_Front
 *
 * @package SmartCrawl
 */

/**
 * Class Smartcrawl_Social_Front
 */
class Smartcrawl_Social_Front extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	/**
	 * Should this module run?.
	 *
	 * @return bool
	 */
	public function should_run() {
		return (
			Smartcrawl_Settings::get_setting( 'social' ) &&
			Smartcrawl_Settings_Admin::is_tab_allowed( Smartcrawl_Settings::TAB_SOCIAL )
		);
	}

	/**
	 * Initialize the module.
	 *
	 * @return void
	 */
	protected function init() {
		Smartcrawl_OpenGraph_Printer::run();
		Smartcrawl_Twitter_Printer::run();
		Smartcrawl_Pinterest_Printer::run();
	}
}