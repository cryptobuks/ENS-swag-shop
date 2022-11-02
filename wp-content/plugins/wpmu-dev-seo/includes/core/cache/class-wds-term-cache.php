<?php

class Smartcrawl_Term_Cache {

	use Smartcrawl_Singleton;

	private $cache = array();

	public function get_term( $term_id ) {
		if ( empty( $this->cache[ $term_id ] ) ) {
			$term = new Smartcrawl_Taxonomy_Term( $term_id );
			if ( ! $term->get_wp_term() ) {
				return null;
			}
			$this->cache[ $term_id ] = $term;
		}

		return $this->cache[ $term_id ];
	}

	public function purge( $term_id ) {
		unset( $this->cache[ $term_id ] );
	}

	public function purge_all() {
		$this->cache = array();
	}
}