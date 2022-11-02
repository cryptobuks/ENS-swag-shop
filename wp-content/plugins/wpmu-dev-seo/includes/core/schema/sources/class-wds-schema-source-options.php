<?php

class Smartcrawl_Schema_Source_Options extends Smartcrawl_Schema_Property_Source {
	const ID = 'options';

	/**
	 * @var
	 */
	private $option;
	/**
	 * @var
	 */
	private $type;

	/**
	 * @param $option
	 * @param $type
	 */
	public function __construct( $option, $type ) {
		parent::__construct();

		$this->option = $option;
		$this->type   = $type;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		if ( 'Array' !== $this->type && is_array( $this->option ) ) {
			return join( ',', $this->option );
		}

		return $this->option;
	}
}