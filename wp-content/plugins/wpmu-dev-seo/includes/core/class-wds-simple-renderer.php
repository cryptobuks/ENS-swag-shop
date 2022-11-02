<?php

class Smartcrawl_Simple_Renderer extends Smartcrawl_Renderable {

	use Smartcrawl_Singleton;

	public static function render( $view, $args = array() ) {
		$instance = self::get();
		$instance->render_view( $view, $args );
	}

	public static function load( $view, $args = array() ) {
		$instance = self::get();

		return $instance->load_view( $view, $args );
	}

	protected function get_view_defaults() {
		return array();
	}
}