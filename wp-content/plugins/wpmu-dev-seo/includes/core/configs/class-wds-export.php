<?php
/**
 * Handles export
 *
 * @package wpmu-dev-seo
 */

/**
 * Settings export class
 */
class Smartcrawl_Export {

	/**
	 * Model instance
	 *
	 * @var Smartcrawl_Model_IO
	 */
	protected $model;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->model = new Smartcrawl_Model_IO();
	}

	/**
	 * Loads all options
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public static function load() {
		$me = new self();

		$me->load_all();

		return $me->model;
	}

	/**
	 * Loads everything
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_all() {
		$this->model->set_version( SMARTCRAWL_VERSION );
		$this->model->set_url( home_url() );

		foreach ( $this->model->get_sections() as $section ) {
			$method = array( $this, "load_{$section}" );
			if ( ! is_callable( $method ) ) {
				continue;
			}

			call_user_func( $method );
		}

		return $this->model;
	}

	/**
	 * Loads options
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_options() {
		$options = array();

		$components = Smartcrawl_Settings::get_all_components();
		foreach ( $components as $component ) {
			$options[ $this->get_option_name( $component ) ] = Smartcrawl_Settings::get_component_options( $component );
		}

		$options['wds_settings_options'] = Smartcrawl_Settings::get_local_settings();

		$options['wds_blog_tabs'] = get_site_option( 'wds_blog_tabs' );

		$this->model->set( Smartcrawl_Model_IO::OPTIONS, $options );

		return $this->model;
	}

	/**
	 * Gets option name
	 *
	 * @param string $comp Partial.
	 *
	 * @return string Options key
	 */
	public function get_option_name( $comp ) {
		if ( in_array( $comp, Smartcrawl_Settings::get_all_components(), true ) ) {
			return "wds_{$comp}_options";
		}
	}

	/**
	 * Loads ignores
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignores() {
		$model = new Smartcrawl_Model_Ignores();
		$this->model->set( Smartcrawl_Model_IO::IGNORES, $model->get_all() );

		return $this->model;
	}

	/**
	 * Loads extra sitemap URLs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_extra_urls() {
		$this->model->set( Smartcrawl_Model_IO::EXTRA_URLS, Smartcrawl_Sitemap_Utils::get_extra_urls() );

		return $this->model;
	}

	/**
	 * Loads ignore sitemap URLs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignore_urls() {
		$this->model->set( Smartcrawl_Model_IO::IGNORE_URLS, Smartcrawl_Sitemap_Utils::get_ignore_urls() );

		return $this->model;
	}

	/**
	 * Loads extra sitemap post IDs
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_ignore_post_ids() {
		$this->model->set( Smartcrawl_Model_IO::IGNORE_POST_IDS, Smartcrawl_Sitemap_Utils::get_ignore_ids() );

		return $this->model;
	}

	/**
	 * Loads all stored postmeta
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_postmeta() {
		return $this->model;
	}

	/**
	 * Loads all stored taxmeta for the current site
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_taxmeta() {
		return $this->model;
	}

	/**
	 * Loads all stored redirects for the current site
	 *
	 * @return Smartcrawl_Model_IO instance
	 */
	public function load_redirects() {
		$table = Smartcrawl_Redirects_Database_Table::get();
		$this->model->set( Smartcrawl_Model_IO::REDIRECTS, $table->get_deflated_redirects() );

		return $this->model;
	}
}