<?php
$archive_post_type        = empty( $archive_post_type ) ? '' : $archive_post_type;
$archive_post_type_robots = empty( $archive_post_type_robots ) ? '' : $archive_post_type_robots;
$macros                   = array_merge(
	Smartcrawl_Onpage_Settings::get_pt_archive_macros(),
	Smartcrawl_Onpage_Settings::get_general_macros()
);

$this->render_view( 'onpage/onpage-preview' );

$this->render_view(
	'onpage/onpage-general-settings',
	array(
		'title_key'       => 'title-' . $archive_post_type,
		'description_key' => 'metadesc-' . $archive_post_type,
		'macros'          => $macros,
	)
);

$this->render_view(
	'onpage/onpage-og-twitter',
	array(
		'for_type' => $archive_post_type,
		'macros'   => $macros,
	)
);

$this->render_view(
	'onpage/onpage-meta-robots',
	array(
		'items' => $archive_post_type_robots,
	)
);