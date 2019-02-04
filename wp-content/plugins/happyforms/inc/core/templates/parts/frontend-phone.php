<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<div class="happyforms-part-phone-wrap">
				<?php
				$countries = happyforms_get_phone_countries();
				$code_value = happyforms_get_part_value( $part, $form, 'code' );
				$country_value = happyforms_get_part_value( $part, $form, 'country' );

				$default_country_code = $part['mask_phone_country'];

				if ( empty( $code_value ) ) {
					if ( intval( $default_country_code ) ) {
						$code_value = $default_country_code;
					} else {
						$code_value = $countries[$default_country_code]['code'];
					}
				}

				if ( empty( $country_value ) ) {
					$country_value = $default_country_code;
				}
				?>

				<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

				<?php if ( 1 === intval( $part['masked'] ) ) : ?>
					<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[code]" class="happyforms-phone-code" value="<?php echo $code_value; ?>" <?php happyforms_the_part_attributes( $part, $form, 'code' ); ?> />
					<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[country]" class="happyforms-phone-country" value="<?php echo $country_value; ?>" <?php happyforms_the_part_attributes( $part, $form, 'country' ); ?> />
				<?php endif; ?>
				<?php include( happyforms_get_include_folder() . '/core/templates/partials/part-phone-dropdown.php' ); ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="text" value="<?php happyforms_the_part_value( $part, $form, 'number' ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[number]" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 'number' ); ?> />
				<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
					<?php happyforms_the_part_label( $part, $form ); ?>
				<?php endif; ?>

				<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
			</div>

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
			<div class="happyforms-part-phone-wrap">
				<?php include( happyforms_get_include_folder() . '/core/templates/partials/part-phone-dropdown.php' ); ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_confirmation" type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[confirmation]" value="<?php happyforms_the_part_value( $part, $form, 'confirmation' ); ?>" class="happyforms-confirmation-input" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?>  <?php happyforms_the_part_attributes( $part, $form, 1 ); ?> />
				<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
					<?php happyforms_the_part_confirmation_label( $part, $form ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>