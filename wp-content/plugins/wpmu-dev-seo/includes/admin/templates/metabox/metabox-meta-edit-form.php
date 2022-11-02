<?php // phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
$post = empty( $post ) ? null : $post; // phpcs:ignore
if ( $post ) {
	$post_id = $post->ID; // phpcs:ignore
} else {
	return;
}
$macros = array_merge(
	Smartcrawl_Onpage_Settings::get_singular_macros( $post->post_type ),
	Smartcrawl_Onpage_Settings::get_general_macros()
);
?>
<div class="wds-edit-meta">
	<a class="sui-button sui-button-ghost">
		<span class="sui-icon-pencil" aria-hidden="true"></span>

		<?php esc_html_e( 'Edit Meta', 'wds' ); ?>
	</a>

	<div class="sui-border-frame" style="display: none;">
		<?php if ( apply_filters( 'wds-metabox-visible_parts-title_area', true ) ) : ?>
			<div class="sui-form-field">
				<label class="sui-label" for="wds_title">
					<?php esc_html_e( 'SEO Title', 'wds' ); ?>
					<span><?php echo esc_html( sprintf( __( '- Include your focus keywords. %1$d-%2$d characters recommended.', 'wds' ), smartcrawl_title_min_length(), smartcrawl_title_max_length() ) ); ?></span>
				</label>
				<div class="sui-insert-variables wds-allow-macros">
					<input
						type='text'
						id='wds_title'
						name='wds_title'
						value='<?php echo esc_html( smartcrawl_get_value( 'title', $post_id ) ); ?>'
						class='sui-form-control wds-meta-field'
					/>
					<?php $this->render_view( 'macros-dropdown', array( 'macros' => $macros ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'wds-metabox-visible_parts-description_area', true ) ) : ?>
			<div class="sui-form-field">
				<label class="sui-label" for="wds_metadesc">
					<?php esc_html_e( 'Description', 'wds' ); ?>
					<span><?php echo esc_html( sprintf( __( '- Recommended minimum of %1$d characters, maximum %2$d.', 'wds' ), smartcrawl_metadesc_min_length(), smartcrawl_metadesc_max_length() ) ); ?></span>
				</label>
				<div class="sui-insert-variables wds-allow-macros">
					<textarea
						rows='2'
						name='wds_metadesc'
						id='wds_metadesc'
						class='sui-form-control wds-meta-field'
					><?php echo esc_textarea( smartcrawl_get_value( 'metadesc', $post_id ) ); ?></textarea>

					<?php $this->render_view( 'macros-dropdown', array( 'macros' => $macros ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<p class="sui-description"><?php esc_html_e( 'Update or publish this page to save your changes.', 'wds' ); ?></p>
	</div>
</div>