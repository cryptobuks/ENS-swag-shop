<?php
/**
 * Initializes plugin front-end behavior
 *
 * @package Smartcrawl
 */

/**
 * Frontend init class.
 *
 * TODO: get rid of this class
 */
class Smartcrawl_Front extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	/**
	 * Initializing method.
	 */
	protected function init() {
		if ( defined( 'SMARTCRAWL_EXPERIMENTAL_FEATURES_ON' ) && SMARTCRAWL_EXPERIMENTAL_FEATURES_ON ) {
			if ( file_exists( SMARTCRAWL_PLUGIN_DIR . 'tools/video-sitemaps.php' ) ) {
				require_once SMARTCRAWL_PLUGIN_DIR . 'tools/video-sitemaps.php';
			}
		}
	}
}