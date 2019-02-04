<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php 
		if ( ! empty( $part['label'] ) || happyforms_is_preview() ) {
			happyforms_the_part_label( $part, $form );
		}
		?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php echo html_entity_decode( $part['placeholder_text'] ); ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>
	</div>
</div>