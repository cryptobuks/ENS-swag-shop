<div class="wds-metabox-preview">
	<label class="sui-label"><?php esc_html_e( 'Google Preview', 'wds' ); ?></label>

	<?php
	$this->render_view(
		'onpage/onpage-preview',
		array(
			'link'        => '{{- link }}',
			'title'       => '{{- title }}',
			'description' => '{{- description }}',
		)
	);
	?>
</div>