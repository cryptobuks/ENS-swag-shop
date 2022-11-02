<?php $post = empty( $post ) ? null : $post; // phpcs:ignore ?>
<div class="wds-metabox-section">
	<?php
	$this->render_view( 'metabox/metabox-dummy-preview' );
	?>

	<?php
	$this->render_view(
		'metabox/metabox-meta-edit-form',
		array(
			'post' => $post,
		)
	);
	?>
</div>