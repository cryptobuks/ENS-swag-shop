<?php

class Smartcrawl_Schema_Source_Post_Meta extends Smartcrawl_Schema_Property_Source {
	const ID = 'post_meta';

	/**
	 * @var
	 */
	private $meta_key;
	/**
	 * @var
	 */
	private $post;

	/**
	 * @param $post
	 * @param $meta_key
	 */
	public function __construct( $post, $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
		$this->post     = $post;
	}

	/**
	 * @return bool|float|int|string
	 */
	public function get_value() {
		$meta_value = get_post_meta( $this->post->ID, $this->meta_key, true );
		if ( $meta_value && is_scalar( $meta_value ) ) {
			return $meta_value;
		}

		return '';
	}
}