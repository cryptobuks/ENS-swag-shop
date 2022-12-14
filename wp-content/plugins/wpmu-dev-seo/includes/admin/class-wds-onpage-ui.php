<?php

class Smartcrawl_OnPage_UI extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	/**
	 * Should run?.
	 *
	 * @return bool
	 */
	public function should_run() {
		return Smartcrawl_Settings::get_setting( 'onpage' ) && Smartcrawl_Settings_Admin::is_tab_allowed( Smartcrawl_Settings::TAB_ONPAGE );
	}

	/**
	 * Child controllers can use this method to initialize.
	 *
	 * @return mixed
	 */
	protected function init() {
		add_filter( 'wds-sections-metabox-seo', array( $this, 'add_seo_meta_fields' ), 10, 2 );
		add_filter( 'wds-sections-metabox-advanced', array( $this, 'add_advanced_metabox_settings_section' ), 10, 2 );

		return true;
	}

	/**
	 * @param $sections
	 * @param $post
	 *
	 * @return mixed
	 */
	public function add_seo_meta_fields( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$sections['metabox/metabox-seo-meta'] = array(
			'post' => $post,
		);

		return $sections;
	}

	/**
	 * Add advanced metabox for settings sections.
	 *
	 * @param array   $sections Sections.
	 * @param WP_Post $post     Post object.
	 */
	public function add_advanced_metabox_settings_section( $sections, $post = null ) {
		if ( empty( $post ) ) {
			return $sections;
		}

		$all_options                                   = Smartcrawl_Settings::get_options();
		$post_id                                       = $post->ID;
		$sections['metabox/metabox-advanced-indexing'] = array(
			'robots_noindex_value'  => (int) smartcrawl_get_value( 'meta-robots-noindex', $post_id ),
			'robots_nofollow_value' => (int) smartcrawl_get_value( 'meta-robots-nofollow', $post_id ),
			'robots_index_value'    => (int) smartcrawl_get_value( 'meta-robots-index', $post_id ),
			'robots_follow_value'   => (int) smartcrawl_get_value( 'meta-robots-follow', $post_id ),
			'advanced_value'        => explode( ',', smartcrawl_get_value( 'meta-robots-adv', $post_id ) ),
			'post_type_noindexed'   => (bool) smartcrawl_get_array_value( $all_options, sprintf( 'meta_robots-noindex-%s', get_post_type( $post ) ) ),
			'post_type_nofollowed'  => (bool) smartcrawl_get_array_value( $all_options, sprintf( 'meta_robots-nofollow-%s', get_post_type( $post ) ) ),
		);

		$sections['metabox/metabox-advanced-canonical'] = array(
			'canonical_url' => smartcrawl_get_value( 'canonical', $post_id ),
		);

		return $sections;
	}
}