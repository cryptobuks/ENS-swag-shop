<?php

abstract class Smartcrawl_Schema_Property_Source {
	/**
	 * @var Smartcrawl_Schema_Utils
	 */
	protected $utils;

	/**
	 *
	 */
	public function __construct() {
		$this->utils = Smartcrawl_Schema_Utils::get();
	}

	/**
	 * @return mixed
	 */
	abstract public function get_value();
}