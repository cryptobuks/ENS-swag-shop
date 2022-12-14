<?php

class Smartcrawl_Canonical_Value_Helper extends Smartcrawl_Type_Traverser {

	/**
	 * @var string
	 */
	private $canonical;

	public function __construct() {
		_deprecated_constructor( __CLASS__, '2.18.0', 'Smartcrawl_Type_Traverser' );

		$this->traverse();
	}

	protected function clear() {
		$this->canonical = '';
	}

	public function get_canonical() {
		_deprecated_function( __FUNCTION__, '2.18.0', 'Smartcrawl_Entity::get_canonical_url()' );

		return apply_filters( 'wds_filter_canonical', $this->canonical );
	}

	public function handle_bp_groups() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->canonical = bp_get_group_permalink( new BP_Groups_Group( bp_get_group_id() ) );
	}

	public function handle_bp_profile() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->canonical = bp_displayed_user_domain();
	}

	public function handle_woo_shop() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->handle_singular( wc_get_page_id( 'shop' ) );
	}

	public function handle_blog_home() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->canonical = trailingslashit( get_bloginfo( 'url' ) );
	}

	public function handle_static_home() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->handle_singular( get_option( 'page_for_posts' ) );
	}

	public function handle_search() {
		_deprecated_function( __FUNCTION__, '2.18.0' );
		// Not needed.
	}

	public function handle_404() {
		_deprecated_function( __FUNCTION__, '2.18.0' );
		// Not needed.
	}

	public function handle_date_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$wp_query = $this->get_query_context();

		$requested_year  = $wp_query->get( 'year' );
		$requested_month = $wp_query->get( 'monthnum' );
		$date_callback   = ! empty( $requested_year ) && empty( $requested_month )
			? 'get_year_link'
			: 'get_month_link';
		$this->canonical = $date_callback( $requested_year, $requested_month );

		$this->append_page_number();
	}

	public function handle_pt_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		/**
		 * Post type.
		 *
		 * @var $post_type WP_Post_Type
		 */
		$post_type = $this->get_queried_object();
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$this->canonical = get_post_type_archive_link( $post_type->name );

			$this->append_page_number();
		}
	}

	public function handle_tax_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$wp_query        = $this->get_query_context();
		$term            = $wp_query->get_queried_object();
		$canonical       = smartcrawl_get_term_meta( $term, $term->taxonomy, 'wds_canonical' );
		$this->canonical = $canonical ? $canonical : get_term_link( $term, $term->taxonomy );

		$this->append_page_number();
	}

	public function handle_author_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$user            = $this->get_queried_object();
		$this->canonical = get_author_posts_url( $user->ID );

		$this->append_page_number();
	}

	public function handle_archive() {
		_deprecated_function( __FUNCTION__, '2.18.0' );
		// Not needed. More specific archives handled already.
	}

	public function handle_singular( $post_id = 0 ) {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$post = $this->get_post_or_fallback( $post_id );
		if ( ! $post ) {
			return;
		}

		$canonical = smartcrawl_get_value( 'canonical', $post->ID );
		if ( empty( $canonical ) ) {
			$canonical = $this->get_post_canonical( $post->ID );
		}

		$this->canonical = $canonical;
	}

	private function get_post_canonical( $id ) {
		$link         = get_permalink( $id );
		$query        = $this->get_query_context();
		$comment_page = $query->get( 'cpage' );
		if ( $comment_page ) {
			$link = get_comments_pagenum_link( $comment_page );
		}

		return $link;
	}

	private function append_page_number() {
		/**
		 * WP Rewrite.
		 *
		 * @var $wp_rewrite WP_Rewrite
		 */
		global $wp_rewrite;

		$wp_query = $this->get_query_context();
		$paged    = $wp_query->get( 'paged', 1 );

		if ( $paged > 1 ) {
			if ( $wp_rewrite->using_permalinks() ) {
				$this->canonical = trailingslashit( $this->canonical ) . sprintf( $wp_rewrite->pagination_base . '/%d/', $paged );
			} else {
				$this->canonical = esc_url_raw( add_query_arg( 'paged', $paged, $this->canonical ) );
			}
		}
	}
}