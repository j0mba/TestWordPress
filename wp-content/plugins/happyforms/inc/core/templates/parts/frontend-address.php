<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( 'simple' === $part['mode'] ) : ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[full]" class="address-full" type="text" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 'full' ); ?> />
						<?php happyforms_geolocation_link( $part ); ?>
						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php elseif ( 'autocomplete' === $part['mode'] ) : ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[full]" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" data-serialize />

						<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_id( $part, $form ); ?>_full_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-full" type="text" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> autocomplete="none" <?php happyforms_the_part_attributes( $part, $form, 'full' ); ?> />
						<?php happyforms_geolocation_link( $part ); ?>
						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
						
						<?php happyforms_select( array(), $part, $form ); ?>
					</div>
				</div>
			<?php elseif ( 'country' === $part['mode'] ) : ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[country]" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" data-serialize />

						<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_id( $part, $form ); ?>_country_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-country" type="text" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" placeholder="<?php _e( 'Country', 'happyforms' ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> autocomplete="off" <?php happyforms_the_part_attributes( $part, $form, 'country' ); ?> />
						<?php happyforms_geolocation_link( $part ); ?>

						<?php happyforms_select( array(), $part, $form ); ?>

						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[country]" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" data-serialize />

						<input id ="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>_country_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-country" type="text" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" placeholder="<?php _e( 'Country', 'happyforms' ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> autocomplete="off" <?php happyforms_the_part_attributes( $part, $form, 'country' ); ?> />
						<?php happyforms_geolocation_link( $part ); ?>

						<?php happyforms_select( array(), $part, $form ); ?>

						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
					</div>
					
					<input name="<?php happyforms_the_part_name( $part, $form ); ?>[city]" class="address-city" type="text" value="<?php happyforms_the_part_value( $part, $form, 'city' ); ?>" placeholder="<?php _e( 'City', 'happyforms' ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, 'city' ); ?> />
				</div>
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>