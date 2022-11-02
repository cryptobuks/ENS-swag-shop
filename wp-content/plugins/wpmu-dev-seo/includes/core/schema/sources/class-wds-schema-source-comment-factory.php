<?php

class Smartcrawl_Schema_Source_Comment_Factory extends Smartcrawl_Schema_Source_Factory {
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
	 * @return Smartcrawl_Schema_Source_Comment|Smartcrawl_Schema_Source_Text
	 */
	public function create( $source, $field, $type ) {
		if ( empty( $this->comment ) ) {
			return $this->create_default_source();
		}

		if ( Smartcrawl_Schema_Source_Comment::ID === $source ) {
			return new Smartcrawl_Schema_Source_Comment( $this->comment, $field );
		}

		return parent::create( $source, $field, $type );
	}
}