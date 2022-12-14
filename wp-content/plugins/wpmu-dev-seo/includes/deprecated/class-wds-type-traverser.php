<?php

/**
 * TODO: remove (or mark as deprecated)
 */
abstract class Smartcrawl_Type_Traverser
{
	/**
	 * @var string
	 */
	private $hash;

	public function traverse() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$resolver = $this->get_resolver();
		$location = $resolver->get_location();

		if ( ! $this->traversal_required( $location ) ) {
			return;
		}

		$this->clear();

		switch ( $location ) {
			case Smartcrawl_Endpoint_Resolver::L_BP_GROUPS:
				$this->handle_bp_groups();
				break;

			case Smartcrawl_Endpoint_Resolver::L_BP_PROFILE:
				$this->handle_bp_profile();
				break;

			case Smartcrawl_Endpoint_Resolver::L_WOO_SHOP:
				$this->handle_woo_shop();
				break;

			case Smartcrawl_Endpoint_Resolver::L_BLOG_HOME:
				$this->handle_blog_home();
				break;

			case Smartcrawl_Endpoint_Resolver::L_STATIC_HOME:
				$this->handle_static_home();
				break;

			case Smartcrawl_Endpoint_Resolver::L_SEARCH:
				$this->handle_search();
				break;

			case Smartcrawl_Endpoint_Resolver::L_404:
				$this->handle_404();
				break;

			case Smartcrawl_Endpoint_Resolver::L_DATE_ARCHIVE:
				$this->handle_date_archive();
				break;

			case Smartcrawl_Endpoint_Resolver::L_PT_ARCHIVE:
				$this->handle_pt_archive();
				break;

			case Smartcrawl_Endpoint_Resolver::L_TAX_ARCHIVE:
				$this->handle_tax_archive();
				break;

			case Smartcrawl_Endpoint_Resolver::L_AUTHOR_ARCHIVE:
				$this->handle_author_archive();
				break;

			case Smartcrawl_Endpoint_Resolver::L_ARCHIVE:
				$this->handle_archive();
				break;

			case Smartcrawl_Endpoint_Resolver::L_SINGULAR:
				$this->handle_singular();
				break;

			default:
				break;
		}
	}

	private function traversal_required( $location ) {
		$query = $this->get_query_context();
		$query = $query && is_a( $query, 'WP_Query' ) ? $query : array();
		$hash  = md5( wp_json_encode( $query ) . "-{$location}" );
		if ( $this->hash === $hash ) {
			return false;
		}
		$this->hash = $hash;
		return true;
	}

	public function reset() {
		_deprecated_function( __FUNCTION__, '2.18.0' );

		$this->hash = '';
	}

	protected function get_resolver() {
		return Smartcrawl_Endpoint_Resolver::resolve();
	}

	protected function get_queried_object() {
		$query_context  = $this->get_resolver()->get_query_context();
		$queried_object = $query_context->get_queried_object();

		return $queried_object;
	}

	protected function get_query_context() {
		return $this->get_resolver()->get_query_context();
	}

	protected function get_context() {
		return $this->get_resolver()->get_context();
	}

	/**
	 * @param int|WP_Post $post_id Post ID.
	 *
	 * @return null|WP_Post
	 */
	protected function get_post_or_fallback( $post_id ) {
		$post = $post_id
			? get_post( $post_id )
			: null;
		if ( ! $post ) {
			// Try falling back on context object.
			$post = $this->get_context();
		}
		if ( empty( $post->ID ) ) {
			// Still nothing? Try the queried object.
			$query = $this->get_resolver()->get_query_context();
			$post  = $query->get_queried_object();
		}
		return $post;
	}

	abstract protected function clear();

	abstract public function handle_bp_groups();

	abstract public function handle_bp_profile();

	abstract public function handle_woo_shop();

	abstract public function handle_blog_home();

	abstract public function handle_static_home();

	abstract public function handle_search();

	abstract public function handle_404();

	abstract public function handle_date_archive();

	abstract public function handle_pt_archive();

	abstract public function handle_tax_archive();

	abstract public function handle_author_archive();

	abstract public function handle_archive();

	abstract public function handle_singular();
}