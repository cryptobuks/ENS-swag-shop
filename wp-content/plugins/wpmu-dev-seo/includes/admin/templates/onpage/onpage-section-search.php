<?php
$meta_robots_search = empty( $meta_robots_search ) ? array() : $meta_robots_search;
$macros             = array_merge(
	Smartcrawl_Onpage_Settings::get_search_macros(),
	Smartcrawl_Onpage_Settings::get_general_macros()
);

$this->render_view( 'onpage/onpage-preview' );

$this->render_view(
	'onpage/onpage-general-settings',
	array(
		'title_key'       => 'title-search',
		'description_key' => 'metadesc-search',
		'macros'          => $macros,
	)
);

$this->render_view(
	'onpage/onpage-og-twitter',
	array(
		'for_type' => 'search',
		'macros'   => $macros,
	)
);

$this->render_view(
	'onpage/onpage-meta-robots',
	array(
		'items' => $meta_robots_search,
	)
);