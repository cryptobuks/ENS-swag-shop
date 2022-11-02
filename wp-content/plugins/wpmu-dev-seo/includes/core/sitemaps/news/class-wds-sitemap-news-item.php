<?php

class Smartcrawl_Sitemap_News_Item {

	private $title;
	/**
	 * @var string
	 */
	private $location = '';
	/**
	 * @var int
	 */
	private $publication_time = 0;
	/**
	 * @var string
	 */
	private $publication = '';

	private $language;

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	public function set_title( $title ) {
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_location() {
		return $this->location;
	}

	public function set_location( $location ) {
		$this->location = $location;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_publication_time() {
		return $this->publication_time;
	}

	public function set_publication_time( $publication_time ) {
		$this->publication_time = $publication_time;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_publication() {
		return $this->publication;
	}

	public function set_publication( $publication ) {
		$this->publication = $publication;

		return $this;
	}

	/**
	 * @return void
	 */
	public function set_language( $language ) {
		$this->language = $language;
	}

	/**
	 * @return mixed
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function to_xml() {
		return sprintf(
			'<url>
				<loc>%s</loc>
				<news:news>
					<news:publication>
						<news:name>%s</news:name>
						<news:language>%s</news:language>
					</news:publication>
					<news:publication_date>%s</news:publication_date>
					<news:title>%s</news:title>
				</news:news>
			</url>',
			esc_url( $this->get_location() ),
			esc_xml( $this->get_publication() ),
			esc_xml( $this->get_language() ),
			Smartcrawl_Sitemap_Utils::format_timestamp( $this->get_publication_time() ),
			esc_xml( $this->get_title() )
		);
	}
}