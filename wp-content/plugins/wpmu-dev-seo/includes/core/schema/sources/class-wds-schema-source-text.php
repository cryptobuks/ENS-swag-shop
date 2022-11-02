<?php

class Smartcrawl_Schema_Source_Text extends Smartcrawl_Schema_Property_Source {
	const ID = 'custom_text';

	/**
	 * @var
	 */
	private $text;

	/**
	 * @param $text
	 */
	public function __construct( $text ) {
		parent::__construct();
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function get_value() {
		return $this->text;
	}
}