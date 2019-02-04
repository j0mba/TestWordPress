<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>
		
		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( ! $part['masked'] ) : ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="number" value="<?php happyforms_the_part_value( $part, $form, 0 ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" min="<?php echo esc_attr( $part['min_value'] ); ?>" max="<?php echo esc_attr( $part['max_value'] ); ?>" value="<?php echo esc_attr( $part['min_value'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 0 ); ?> />
			<?php else: ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" class="happyforms-masked-input" type="text" value="<?php happyforms_the_part_value( $part, $form, 0 ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 0 ); ?> />
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
				<?php happyforms_the_part_label( $part, $form ); ?>
			<?php endif; ?>
			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
	<?php if ( 1 === intval( $part['confirmation_field'] ) ) : ?>
	<div class="happyforms-part-wrap happyforms-part-wrap--confirmation" id="<?php happyforms_the_part_id( $part, $form ); ?>-part_confirmation">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_confirmation_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php if ( ! $part['masked'] ) : ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_confirmation" class="happyforms-confirmation-input" type="number" name="<?php happyforms_the_part_name( $part, $form ); ?>_confirmation" value="<?php happyforms_the_part_value( $part, $form, 1 ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" min="<?php echo esc_attr( $part['min_value'] ); ?>" max="<?php echo esc_attr( $part['max_value'] ); ?>" value="<?php echo esc_attr( $part['min_value'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 1 ); ?> />
			<?php else: ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_confirmation" class="happyforms-masked-input happyforms-confirmation-input" type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>_confirmation" value="<?php happyforms_the_part_value( $part, $form, 1 ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 1 ); ?> />
			<?php endif; ?>
			<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
				<?php happyforms_the_part_confirmation_label( $part, $form ); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>