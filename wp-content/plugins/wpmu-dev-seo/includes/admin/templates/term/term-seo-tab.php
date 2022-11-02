<?php
$tax_meta  = empty( $tax_meta ) ? array() : $tax_meta;
$term      = empty( $term ) ? null : $term; // phpcs:ignore
$is_active = empty( $is_active ) ? false : $is_active;
?>
<div class="<?php echo $is_active ? 'active' : ''; ?>">
	<div class="wds-metabox-section">
		<?php $this->render_view( 'metabox/metabox-dummy-preview' ); ?>
		<?php
		$this->render_view(
			'term/term-meta-edit-form',
			array(
				'tax_meta' => $tax_meta,
				'term'     => $term,
			)
		);
		?>
	</div>
</div>