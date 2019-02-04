<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<label class="option-label">
				<input type="checkbox" class="happyforms-visuallyhidden" id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="yes" required aria-required="true" <?php checked( happyforms_get_part_value( $part, $form ), 'yes' ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
				<span class="checkmark"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="currentColor" d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/></svg></span>
				<span class="label"><?php echo html_entity_decode( $part['legal_text'] ); ?></span>
			</label>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>