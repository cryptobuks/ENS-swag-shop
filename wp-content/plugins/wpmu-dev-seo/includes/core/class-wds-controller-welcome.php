<?php

class Smartcrawl_Controller_Welcome extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	const WELCOME_MODAL_DISMISSED_OPTION = 'wds-welcome-modal-dismissed';

	protected function init() {
		add_action( 'wp_ajax_wds-try-latest-features', array( $this, 'json_try_latest_features' ) );
		add_action( 'wp_ajax_wds-skip-latest-features', array( $this, 'json_skip_latest_features' ) );
		//add_action( 'wds-dshboard-after_settings', array( $this, 'add_welcome_modal' ) );
	}

	public function json_try_latest_features() {
		$request_data = $this->get_request_data();
		if ( empty( $request_data ) ) {
			wp_send_json_error();
		}

		// Dismiss the modal.
		Smartcrawl_Settings::update_specific_options( self::WELCOME_MODAL_DISMISSED_OPTION, SMARTCRAWL_VERSION );

		// Redirect the user.
		$redirect_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_SCHEMA ) . '&tab=tab_types&add_type=1';
		wp_send_json_success(
			array(
				'redirect_url' => $redirect_url,
			)
		);
	}

	public function json_skip_latest_features() {
		$request_data = $this->get_request_data();
		if ( empty( $request_data ) ) {
			wp_send_json_error();
		}

		Smartcrawl_Settings::update_specific_options( self::WELCOME_MODAL_DISMISSED_OPTION, SMARTCRAWL_VERSION );
		wp_send_json_success();
	}

	public function add_welcome_modal() {
		/**
		 * Version.
		 *
		 * @var $modal_dismissed_version string
		 */
		$modal_dismissed_version = Smartcrawl_Settings::get_specific_options( self::WELCOME_MODAL_DISMISSED_OPTION );
		$not_dismissed           = version_compare( $modal_dismissed_version, SMARTCRAWL_VERSION, '<' );
		$onboarding_done         = Smartcrawl_Settings::get_specific_options( Smartcrawl_Controller_Onboard::ONBOARDING_DONE_OPTION );
		$is_fresh_install        = ! Smartcrawl_Loader::get_last_version();
		if ( $onboarding_done && $not_dismissed && ! $is_fresh_install ) {
			Smartcrawl_Simple_Renderer::render( 'dashboard/dashboard-welcome-modal' );
		}
	}

	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['_wds_nonce'] ), 'wds-nonce' ) ? stripslashes_deep( $_POST ) : array(); // phpcs:ignore
	}
}