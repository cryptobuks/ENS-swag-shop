<?php

class Smartcrawl_Post_Cache {

	use Smartcrawl_Singleton;

	private $cache = array();

	public function get_post( $post_id ) {
		if ( ! is_numeric( $post_id ) ) {
			return null;
		}

		if ( empty( $this->cache[ $post_id ] ) ) {
			$post = new Smartcrawl_Post( $post_id );
			if ( ! $post->get_wp_post() ) {
				return null;
			}
			$this->cache[ $post_id ] = $post;
		}

		return $this->cache[ $post_id ];
	}

	public function purge( $post_id ) {
		unset( $this->cache[ $post_id ] );
	}

	public function purge_all() {
		$this->cache = array();
	}
}