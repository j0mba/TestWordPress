<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( 1 == $part['autocomplete_domains'] ) : ?>
				<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="<?php happyforms_the_part_value( $part, $form, 0 ); ?>" data-serialize />

				<input type="email" name="<?php happyforms_the_part_id( $part, $form ); ?>_dummy_<?php echo time(); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>" value="<?php happyforms_the_part_value( $part, $form, 0 ); ?>" autocomplete="none" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 == $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 0 ); ?> />
			<?php else: ?>
				<input type="email" name="<?php happyforms_the_part_name( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>" value="<?php happyforms_the_part_value( $part, $form, 0 ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 == $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 0 ); ?> />
			<?php endif; ?>
			<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
				<?php happyforms_the_part_label( $part, $form ); ?>
			<?php endif; ?>
			
			<?php 
			if ( 1 == $part['autocomplete_domains'] ) {
				happyforms_select( array(), $part, $form );
			}
			?>

			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>
	</div>
	<?php if ( 1 === intval( $part['confirmation_field'] ) ) : ?>
	<div class="happyforms-part-wrap happyforms-part-wrap--confirmation" id="<?php happyforms_the_part_id( $part, $form ); ?>-part_confirmation">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_confirmation_label( $part, $form ); ?>
		<?php endif; ?>
		
		<div class="happyforms-part__el">
			<?php if ( 1 == $part['autocomplete_domains'] ) : ?>
				<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="<?php happyforms_the_part_value( $part, $form, 1 ); ?>" data-serialize />

				<input type="email" name="<?php happyforms_the_part_id( $part, $form ); ?>_dummy_<?php echo time(); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>_confirmation" value="<?php happyforms_the_part_value( $part, $form, 1 ); ?>" autocomplete="none" <?php if ( 1 == $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 1 ); ?> />
			<?php else: ?>
				<input type="email" id="<?php happyforms_the_part_id( $part, $form ); ?>_confirmation" name="<?php happyforms_the_part_name( $part, $form ); ?>_confirmation" value="<?php happyforms_the_part_value( $part, $form, 1 ); ?>" class="happyforms-confirmation-input" data-confirmation-of="<?php echo esc_attr( $part['id'] ); ?>" <?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 1 ); ?> />
			<?php endif; ?>

			<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
				<?php happyforms_the_part_confirmation_label( $part, $form ); ?>
			<?php endif; ?>
			
			<?php 
			if ( 1 == $part['autocomplete_domains'] ) {
				happyforms_select( array(), $part, $form );
			}
			?>
		</div>
	</div>
	<?php endif; ?>
</div>