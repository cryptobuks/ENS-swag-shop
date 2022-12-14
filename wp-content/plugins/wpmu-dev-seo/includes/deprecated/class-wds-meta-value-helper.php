<?php

class Smartcrawl_Meta_Value_Helper extends Smartcrawl_Type_Traverser {

	private $title = '';

	private $fallback_title = '';

	private $description = '';

	private $fallback_description = '';

	private function __construct() {
		_deprecated_constructor( __CLASS__, '2.18.0', 'Smartcrawl_Type_Traverser' );
	}

	protected function clear() {
		$this->title = '';

		$this->fallback_title = '';

		$this->description = '';

		$this->fallback_description = '';
	}

	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Static instance getter
	 */
	public static function get() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->traverse();

		return self::$instance;
	}

	public function handle_bp_groups() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options( 'bp_groups' );
	}

	public function handle_bp_profile() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options( 'bp_profile' );
	}

	public function handle_woo_shop() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->handle_singular( wc_get_page_id( 'shop' ) );
	}

	public function handle_blog_home() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options( 'home' );
	}

	public function handle_static_home() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->handle_singular( get_option( 'page_for_posts' ) );
	}

	public function handle_search() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options( 'search' );
	}

	public function handle_404() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options( '404' );
	}

	public function handle_date_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->from_options_archive( 'date' );
	}

	public function handle_pt_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$post_type = $this->get_queried_object();
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$location = Smartcrawl_Onpage_Settings::PT_ARCHIVE_PREFIX . $post_type->name;
			$this->from_options_archive( $location );
		}
	}

	public function handle_tax_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		/**
		 * Term.
		 *
		 * @var $term WP_Term
		 */
		$term = $this->get_queried_object();
		if ( is_a( $term, 'WP_Term' ) ) {
			$this->fallback_title       = $this->prepare_value( get_the_archive_title() );
			$this->fallback_description = $this->prepare_value( get_term_field( 'description', $term ) );

			$this->from_options( $term->taxonomy );
			$this->from_term_meta( $term );
		}
	}

	public function handle_author_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		/**
		 * User.
		 *
		 * @var $author WP_User
		 */
		$author = $this->get_queried_object();
		if ( is_a( $author, 'WP_User' ) ) {
			$this->from_options_archive( 'author' );

			$this->from_author_meta( $author );
		}
	}

	public function handle_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		// TODO: Implement handle_archive() method.
	}

	public function handle_singular( $post_id = 0 ) {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$post = $this->get_post_or_fallback( $post_id );
		if ( is_a( $post, 'WP_Post' ) ) {
			$this->fallback_title       = $this->prepare_value( get_the_title( $post ) );
			$this->fallback_description = $this->get_trimmed_excerpt( $post );

			$this->from_options( $this->get_post_type( $post ) );

			// Now apply any overrides from the individual post's meta.
			$this->from_post_meta( $post );
		}
	}

	/**
	 * @param WP_Post $post Post object.
	 *
	 * @return mixed|string
	 */
	private function get_trimmed_excerpt( $post ) {
		$from_meta = get_post_meta( $post->ID, '_wds_trimmed_excerpt', false );
		if ( ! empty( $from_meta ) ) {
			return $from_meta[0];
		}

		return smartcrawl_get_trimmed_excerpt(
			$post->post_excerpt,
			$post->post_content
		);
	}

	private function from_options( $location ) {
		$options = $this->get_options();

		$title       = smartcrawl_get_array_value( $options, 'title-' . $location );
		$description = smartcrawl_get_array_value( $options, 'metadesc-' . $location );

		$title       = $this->prepare_value( $title );
		$description = $this->prepare_value( $description );

		$this->title       = empty( $title ) ? $this->title : $title;
		$this->description = empty( $description ) ? $this->description : $description;
	}

	private function from_term_meta( $term ) {
		$raw_title = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_title' );
		$raw_desc  = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_desc' );

		$title       = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_desc );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
	}

	private function get_options() {
		return Smartcrawl_Settings::get_options();
	}

	/**
	 * @param WP_User $author User object.
	 */
	private function from_author_meta( $author ) {
		$raw_title       = get_the_author_meta( 'wds_title', $author->ID );
		$raw_description = get_the_author_meta( 'wds_metadesc', $author->ID );

		$title       = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_description );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
	}

	/**
	 * @param WP_Post $post Post object.
	 */
	private function from_post_meta( $post ) {
		$post_id = $post->ID;

		$raw_title = smartcrawl_get_value( 'title', $post_id );
		$raw_desc  = smartcrawl_get_value( 'metadesc', $post_id );

		$title       = $this->prepare_value( $raw_title );
		$description = $this->prepare_value( $raw_desc );

		if ( ! empty( $title ) ) {
			$this->title = $title;
		}
		if ( ! empty( $description ) ) {
			$this->description = $description;
		}
	}

	/**
	 * Converts a comma-separated string of keywords into an array
	 *
	 * @param string $string Keywords string.
	 *
	 * @return array List of keywords
	 */
	private function keywords_string_to_array( $string ) {
		$string = trim( strval( $string ) );
		$array  = $string ? explode( ',', $string ) : array();
		$array  = array_map( 'trim', $array );

		return array_filter( array_unique( $array ) );
	}

	private function from_options_archive( $location ) {
		$this->fallback_title = $this->prepare_value( get_the_archive_title() );

		$this->from_options( $location );
	}

	/**
	 * Returns a title string.
	 *
	 * - Value from options/meta is returned
	 * - if it's empty, the passed default argument is returned
	 * - if the passed default is also empty, the fallback value is returned
	 *
	 * @param string $default Default.
	 *
	 * @return string
	 */
	public function get_title( $default = '' ) {
		_deprecated_function( __FUNCTION__, '2.18.0', 'Smartcrawl_Entity::get_meta_title()' );

		$fallback = ! empty( $default )
			? $default
			: $this->fallback_title;

		$title = ! empty( $this->title )
			? $this->title
			: $fallback; // We tried but didn't get anywhere so let's use fallback value.

		return apply_filters( 'wds_title', $title );
	}

	/**
	 * Returns description.
	 *
	 * - Value from options/meta is returned
	 * - if it's empty, the passed default argument is returned
	 * - if the passed default is also empty, the fallback value is returned
	 *
	 * @param string $default Default.
	 *
	 * @return string
	 */
	public function get_description( $default = '' ) {
		_deprecated_function( __FUNCTION__, '2.18.0', 'Smartcrawl_Entity::get_meta_description()' );

		$fallback = ! empty( $default )
			? $default
			: $this->fallback_description;

		$description = ! empty( $this->description )
			? $this->description
			: $fallback; // We tried but didn't get anywhere so let's use default value.

		return apply_filters( 'wds_metadesc', $description );
	}

	public function get_focus_keywords( $post = false ) {
		_deprecated_function( __FUNCTION__, '2.18.0', 'Smartcrawl_Post::get_focus_keywords()' );

		$result = array();
		if ( empty( $post ) ) {
			$post = $this->get_context();
		}

		if ( is_a( $post, 'WP_Post' ) ) {
			$string = smartcrawl_get_value( 'focus-keywords', $post->ID );
			$result = $this->keywords_string_to_array( $string );
		}

		return apply_filters( 'wds_focus_keywords', $result );
	}

	/**
	 * @param string $raw Raw value.
	 *
	 * @return string
	 */
	private function prepare_value( $raw ) {
		$raw = wp_strip_all_tags( trim( strval( $raw ) ) );
		$raw = Smartcrawl_Replacement_Helper::replace( $raw );

		return smartcrawl_normalize_whitespace( $raw );
	}

	/**
	 * When the argument is a revision, returns the post type of the parent.
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return string
	 */
	private function get_post_type( $post ) {
		$post_parent = wp_is_post_revision( $post->ID );
		if ( ! empty( $post_parent ) ) {
			$post = get_post( $post_parent );
		}

		return $post->post_type;
	}
}