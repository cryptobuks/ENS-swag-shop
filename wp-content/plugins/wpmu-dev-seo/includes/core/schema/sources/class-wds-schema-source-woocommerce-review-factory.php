<?php

class Smartcrawl_Schema_Source_Woocommerce_Review_Factory extends Smartcrawl_Schema_Source_Factory {
	/**
	 * @var
	 */
	private $comment;

	/**
	 * @param $post
	 * @param $comment
	 */
	public function __construct( $post, $comment ) {
		parent::__construct( $post );
		$this->comment = $comment;
	}

	/**
	 * @param $source
	 * @param $field
	 * @param $type
	 *
	 * @return Smartcrawl_Schema_Source_Author|Smartcrawl_Schema_Source_Media|Smartcrawl_Schema_Source_Options|Smartcrawl_Schema_Source_Post|Smartcrawl_Schema_Source_Post_Meta|Smartcrawl_Schema_Source_Schema_Settings|Smartcrawl_Schema_Source_SEO_Meta|Smartcrawl_Schema_Source_Site_Settings|Smartcrawl_Schema_Source_Text|Smartcrawl_Schema_Source_Woocommerce|Smartcrawl_Schema_Source_Woocommerce_Review
	 */
	public function create( $source, $field, $type ) {
		if ( empty( $this->comment ) ) {
			return $this->create_default_source();
		}

		if ( Smartcrawl_Schema_Source_Woocommerce_Review::ID === $source ) {
			return new Smartcrawl_Schema_Source_Woocommerce_Review( $this->comment, $field );
		}

		return parent::create( $source, $field, $type );
	}
}