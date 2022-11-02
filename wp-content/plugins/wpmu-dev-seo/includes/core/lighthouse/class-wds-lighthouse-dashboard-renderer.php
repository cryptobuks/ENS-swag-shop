<?php

class Smartcrawl_Lighthouse_Dashboard_Renderer extends Smartcrawl_Renderable {

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

	/**
	 * @return array
	 */
	protected function get_view_defaults() {
		/**
		 * @var $lighthouse Smartcrawl_Lighthouse_Service
		 */
		$lighthouse = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_LIGHTHOUSE );
		$device     = Smartcrawl_Lighthouse_Options::dashboard_widget_device();

		if ( ! in_array( $device, array( 'desktop', 'mobile' ), true ) ) {
			$device = 'desktop';
		}

		return array(
			'lighthouse_start_time' => $lighthouse->get_start_time(),
			'lighthouse_report'     => $lighthouse->get_last_report( $device ),
		);
	}
}