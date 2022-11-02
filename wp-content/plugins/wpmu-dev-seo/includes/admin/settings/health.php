<?php
/**
 * Health settings
 *
 * @package Smartcrawl
 */

/**
 * Smartcrawl_Health_Settings
 */
class Smartcrawl_Health_Settings extends Smartcrawl_Settings_Admin {

	use Smartcrawl_Singleton;


	/**
	 * Validate.
	 *
	 * @param array $input Input.
	 *
	 * @return array
	 */
	public function validate( $input ) {
		return array();
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public function init() {
		$this->option_name = 'wds_health_options';
		$this->name        = Smartcrawl_Settings::COMP_HEALTH;
		$this->slug        = Smartcrawl_Settings::TAB_HEALTH;
		$this->action_url  = admin_url( 'options.php' );
		$this->page_title  = __( 'SmartCrawl Wizard: SEO Health', 'wds' );

		add_action( 'wp_ajax_wds-save-health-settings', array( $this, 'save_health_settings' ) );

		parent::init();
	}

	/**
	 * Save health settings.
	 *
	 * @return void
	 */
	public function save_health_settings() {
		$data = $this->get_request_data();
		if ( empty( $data ) ) {
			wp_send_json_error();
		}

		Smartcrawl_Lighthouse_Options::save_form_data( $_GET['wds_health_options'] ); // phpcs:ignore -- Sanitized on usage later.

		wp_send_json_success();
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'SEO Health', 'wds' );
	}

	/**
	 * Render the page content.
	 *
	 * @return void
	 */
	public function options_page() {
		wp_enqueue_script( Smartcrawl_Controller_Assets::LIGHTHOUSE_JS );

		$this->render_view( 'lighthouse/lighthouse-settings' );
	}

	/**
	 * Save defaults.
	 *
	 * @return void
	 */
	public function defaults() {
		Smartcrawl_Lighthouse_Options::save_defaults();
	}

	/**
	 * Get view defaults.
	 *
	 * @return array|array[]|string[]
	 */
	protected function get_view_defaults() {
		$mode_defaults = Smartcrawl_Lighthouse_Renderer::get()->view_defaults();

		return array_merge(
			array(
				'active_tab' => $this->get_active_tab( 'tab_lighthouse' ),
			),
			$mode_defaults,
			parent::get_view_defaults()
		);
	}

	/**
	 * Get request data.
	 *
	 * @return array
	 */
	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['_wds_nonce'] ), 'wds-health-nonce' ) ? $_POST : array(); // phpcs:ignore
	}
}