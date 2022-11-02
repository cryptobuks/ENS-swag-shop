<?php

class Smartcrawl_Schema_Fragment_Webpage extends Smartcrawl_Schema_Fragment {
	/**
	 * @var Smartcrawl_Post
	 */
	private $post;
	/**
	 * @var
	 */
	private $type;
	/**
	 * @var
	 */
	private $author_id;
	/**
	 * @var
	 */
	private $publisher_id;
	/**
	 * @var Smartcrawl_Schema_Utils
	 */
	private $utils;

	/**
	 * @param $post
	 * @param $type
	 * @param $author_id
	 * @param $publisher_id
	 */
	public function __construct( $post, $type, $author_id, $publisher_id ) {
		$this->post         = $post;
		$this->type         = $type;
		$this->author_id    = $author_id;
		$this->publisher_id = $publisher_id;
		$this->utils        = Smartcrawl_Schema_Utils::get();
	}

	/**
	 * @return array|mixed|null
	 */
	protected function get_raw() {
		$post_permalink = $this->post->get_permalink();
		$post_fragment  = new Smartcrawl_Schema_Fragment_Post(
			$this->post,
			$this->author_id,
			$this->publisher_id,
			true
		);

		return array(
			'@type'    => $this->type,
			'@id'      => $this->utils->get_webpage_id( $post_permalink ),
			'isPartOf' => $this->utils->get_website_id(),
			'hasPart'  => new Smartcrawl_Schema_Fragment_Menu( $post_permalink ),
			'url'      => $post_permalink,
		) + $post_fragment->get_schema();
	}
}