<?php

class Smartcrawl_Schema_Fragment_Static_Home extends Smartcrawl_Schema_Fragment {
	/**
	 * @var Smartcrawl_Schema_Utils
	 */
	private $utils;
	/**
	 * @var
	 */
	private $posts;
	/**
	 * @var
	 */
	private $title;
	/**
	 * @var
	 */
	private $description;

	/**
	 * @param $posts
	 * @param $title
	 * @param $description
	 */
	public function __construct( $posts, $title, $description ) {
		$this->utils       = Smartcrawl_Schema_Utils::get();
		$this->posts       = $posts;
		$this->title       = $title;
		$this->description = $description;
	}

	/**
	 * @return Smartcrawl_Schema_Fragment_Archive
	 */
	protected function get_raw() {
		$page_for_posts_id = get_option( 'page_for_posts' );
		$url               = get_permalink( $page_for_posts_id );

		return new Smartcrawl_Schema_Fragment_Archive(
			'CollectionPage',
			$url,
			$this->posts,
			$this->title,
			$this->description
		);
	}
}