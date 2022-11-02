<?php
$meta_robots_author = empty( $meta_robots_author ) ? '' : $meta_robots_author;
$macros             = array_merge(
	Smartcrawl_Onpage_Settings::get_author_macros(),
	Smartcrawl_Onpage_Settings::get_general_macros()
);

$this->render_view( 'onpage/onpage-preview' );

$this->render_view(
	'onpage/onpage-general-settings',
	array(
		'title_key'       => 'title-author',
		'description_key' => 'metadesc-author',
		'macros'          => $macros,
	)
);

$this->render_view(
	'onpage/onpage-og-twitter',
	array(
		'for_type' => 'author',
		'macros'   => $macros,
	)
);

$this->render_view(
	'onpage/onpage-meta-robots',
	array(
		'items' => $meta_robots_author,
	)
);