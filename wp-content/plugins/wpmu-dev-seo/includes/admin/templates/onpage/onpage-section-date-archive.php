<?php
$meta_robots_date = empty( $meta_robots_date ) ? array() : $meta_robots_date;
$macros           = array_merge(
	Smartcrawl_Onpage_Settings::get_date_macros(),
	Smartcrawl_Onpage_Settings::get_general_macros()
);

$this->render_view( 'onpage/onpage-preview' );

$this->render_view(
	'onpage/onpage-general-settings',
	array(
		'title_key'       => 'title-date',
		'description_key' => 'metadesc-date',
		'macros'          => $macros,
	)
);

$this->render_view(
	'onpage/onpage-og-twitter',
	array(
		'for_type' => 'date',
		'macros'   => $macros,
	)
);

$this->render_view(
	'onpage/onpage-meta-robots',
	array(
		'items' => $meta_robots_date,
	)
);