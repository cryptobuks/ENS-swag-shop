<?php
$taxonomy      = empty( $taxonomy ) ? new stdClass() : $taxonomy; // phpcs:ignore
$meta_robots   = empty( $meta_robots ) ? array() : $meta_robots;
$singular_name = empty( $taxonomy->labels->singular_name ) ? 'post' : strtolower( $taxonomy->labels->singular_name );

$og_description = esc_html__( 'OpenGraph support enhances how your content appears when shared on social networks such as Facebook. You can set default values here but also customize this for each %s.', 'wds' );
$og_description = sprintf( $og_description, $singular_name );

$twitter_description = esc_html__( 'Twitter Cards support enhances how your content appears when shared on Twitter. You can set default values here but also customize this for each %s.', 'wds' );
$twitter_description = sprintf( $twitter_description, $singular_name );
$macros              = array_merge(
	Smartcrawl_Onpage_Settings::get_term_macros( $taxonomy->name ),
	Smartcrawl_Onpage_Settings::get_general_macros()
);

$this->render_view( 'onpage/onpage-preview' );

$this->render_view(
	'onpage/onpage-general-settings',
	array(
		'title_key'       => 'title-' . $taxonomy->name,
		'description_key' => 'metadesc-' . $taxonomy->name,
		'macros'          => $macros,
	)
);

$this->render_view(
	'onpage/onpage-og-twitter',
	array(
		'for_type'            => $taxonomy->name,
		'social_label_desc'   => esc_html__( 'Enable or disable support for social platforms when this taxonomy is shared on them.', 'wds' ),
		'og_description'      => $og_description,
		'twitter_description' => $twitter_description,
		'macros'              => $macros,
	)
);

$this->render_view(
	'onpage/onpage-meta-robots',
	array(
		'items' => $meta_robots,
	)
);