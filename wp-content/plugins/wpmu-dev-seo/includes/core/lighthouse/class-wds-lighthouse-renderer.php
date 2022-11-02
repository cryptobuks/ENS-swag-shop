<?php

class Smartcrawl_Lighthouse_Renderer extends Smartcrawl_Renderable {

	use Smartcrawl_Singleton;

	/**
	 * @return void
	 */
	public static function render( $view, $args = array() ) {
		$instance = self::get();
		$instance->render_view( $view, $args );
	}

	/**
	 * @return false|mixed
	 */
	public static function load( $view, $args = array() ) {
		$instance = self::get();

		return $instance->load_view( $view, $args );
	}

	public function view_defaults() {
		return $this->get_view_defaults();
	}

	protected function get_view_defaults() {
		/**
		 * Light house service.
		 *
		 * @var $lighthouse Smartcrawl_Lighthouse_Service
		 */
		$lighthouse = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_LIGHTHOUSE );
		$device     = smartcrawl_get_array_value( $_GET, 'device' ); // phpcs:ignore

		if ( ! in_array( $device, array( 'desktop', 'mobile' ), true ) ) {
			$device = 'desktop';
		}

		return array(
			'lighthouse_start_time' => $lighthouse->get_start_time(),
			'lighthouse_report'     => $lighthouse->get_last_report( $device ),
		);
	}
}