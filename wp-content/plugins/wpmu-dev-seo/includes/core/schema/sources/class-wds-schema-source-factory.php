<?php

class Smartcrawl_Schema_Source_Factory {
	/**
	 * @var
	 */
	private $post;

	/**
	 * @param $post
	 */
	public function __construct( $post ) {
		$this->post = $post;
	}

	/**
	 * @param $source
	 * @param $value
	 * @param $type
	 *
	 * @return Smartcrawl_Schema_Source_Author|Smartcrawl_Schema_Source_Media|Smartcrawl_Schema_Source_Options|Smartcrawl_Schema_Source_Post|Smartcrawl_Schema_Source_Post_Meta|Smartcrawl_Schema_Source_Schema_Settings|Smartcrawl_Schema_Source_SEO_Meta|Smartcrawl_Schema_Source_Site_Settings|Smartcrawl_Schema_Source_Text|Smartcrawl_Schema_Source_Woocommerce
	 */
	public function create( $source, $value, $type ) {
		switch ( $source ) {
			case Smartcrawl_Schema_Source_Author::ID:
			case Smartcrawl_Schema_Source_Post::ID:
			case Smartcrawl_Schema_Source_Post_Meta::ID:
			case Smartcrawl_Schema_Source_Woocommerce::ID:
				return $this->create_post_dependent_source( $source, $value );

			case Smartcrawl_Schema_Source_Media::OBJECT:
				return new Smartcrawl_Schema_Source_Media( $value, Smartcrawl_Schema_Source_Media::OBJECT );

			case Smartcrawl_Schema_Source_Media::URL:
				return new Smartcrawl_Schema_Source_Media( $value, Smartcrawl_Schema_Source_Media::URL );

			case Smartcrawl_Schema_Source_Schema_Settings::ID:
				return new Smartcrawl_Schema_Source_Schema_Settings( $value );

			case Smartcrawl_Schema_Source_SEO_Meta::ID:
				return new Smartcrawl_Schema_Source_SEO_Meta( $value );

			case Smartcrawl_Schema_Source_Site_Settings::ID:
				return new Smartcrawl_Schema_Source_Site_Settings( $value );

			case Smartcrawl_Schema_Source_Text::ID:
			case 'datetime':
			case 'number':
			case 'duration':
				return new Smartcrawl_Schema_Source_Text( $value );

			case Smartcrawl_Schema_Source_Options::ID:
				return new Smartcrawl_Schema_Source_Options( $value, $type );

			default:
				return $this->create_default_source();
		}
	}

	/**
	 * @param $source
	 * @param $value
	 *
	 * @return Smartcrawl_Schema_Source_Author|Smartcrawl_Schema_Source_Post|Smartcrawl_Schema_Source_Post_Meta|Smartcrawl_Schema_Source_Text|Smartcrawl_Schema_Source_Woocommerce
	 */
	private function create_post_dependent_source( $source, $value ) {
		if ( ! $this->post ) {
			return $this->create_default_source();
		}

		switch ( $source ) {
			case Smartcrawl_Schema_Source_Author::ID:
				return new Smartcrawl_Schema_Source_Author( $this->post, $value );

			case Smartcrawl_Schema_Source_Post::ID:
				return new Smartcrawl_Schema_Source_Post( $this->post, $value );

			case Smartcrawl_Schema_Source_Post_Meta::ID:
				return new Smartcrawl_Schema_Source_Post_Meta( $this->post, $value );

			case Smartcrawl_Schema_Source_Woocommerce::ID:
				return new Smartcrawl_Schema_Source_Woocommerce( $this->post, $value );

			default:
				return $this->create_default_source();
		}
	}

	/**
	 * @return Smartcrawl_Schema_Source_Text
	 */
	protected function create_default_source() {
		return new Smartcrawl_Schema_Source_Text( '' );
	}
}