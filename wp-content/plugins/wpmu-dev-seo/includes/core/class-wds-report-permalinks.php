<?php

class Smartcrawl_Report_Permalinks_Controller extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	const ACTION_QV = 'load-report';

	const ACTION_AUDIT_REPORT = 'seo-audit';

	const ACTION_CRAWL_REPORT = 'sitemap-crawler';

	/**
	 * Child controllers can use this method to initialize.
	 *
	 * @return bool
	 */
	protected function init() {
		add_action( 'wp', array( $this, 'intercept' ) );

		return true;
	}

	public function intercept() {
		if ( ! is_front_page() || ! isset( $_GET[ self::ACTION_QV ] ) ) { // phpcs:ignore
			return;
		}

		$url = false;

		if ( $_GET[ self::ACTION_QV ] === self::ACTION_AUDIT_REPORT ) { // phpcs:ignore
			$url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_HEALTH );
		} elseif ( $_GET[ self::ACTION_QV ] === self::ACTION_CRAWL_REPORT ) { // phpcs:ignore
			$url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_SITEMAP );
		}

		if ( $url ) {
			wp_safe_redirect( apply_filters( 'wds-report-admin-url', $url ) ); // phpcs:ignore
			exit;
		}
	}
}