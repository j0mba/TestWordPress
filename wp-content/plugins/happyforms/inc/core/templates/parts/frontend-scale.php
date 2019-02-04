<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php
		$part_name = happyforms_get_part_name( $part, $form );

		if ( 1 === intval( $part['multiple'] ) ) {
			$part_name = $part_name . '[]';
		}
		?>
		<div class="happyforms-part__el">
			<div class="happyforms-part--scale__inputwrap">
				<div class="happyforms-part--scale__labels">
					<span class="label-min"><?php echo $part['min_label']; ?></span>
					<span class="label-max"><?php echo $part['max_label']; ?></span>
				</div>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>"<?php if ( 1 === intval( $part['multiple'] ) ) : ?> multiple<?php endif; ?> type="range" name="<?php echo $part_name; ?>" step="<?php echo esc_attr( $part['step'] ); ?>" <?php if ( 1 === $part['required'] ) : ?>required aria-required="true"<?php endif; ?> min="<?php echo esc_attr( $part['min_value'] ); ?>" max="<?php echo esc_attr( $part['max_value'] ); ?>" value="<?php happyforms_the_part_value( $part, $form ); ?>" <?php happyforms_the_part_attributes( $part, $form ); ?> />
				<output for="<?php happyforms_the_part_id( $part, $form ); ?>">
					<span><?php happyforms_the_part_value( $part, $form ); ?></span>
				</output>
				<?php if ( 1 === intval( $part['multiple'] ) ) : ?>
					<output for="<?php happyforms_the_part_id( $part, $form ); ?>_clone">
						<span><?php happyforms_the_part_value( $part, $form ); ?></span>
					</output>
				<?php endif; ?>
			</div>

			<?php happyforms_print_part_description( $part ); ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
