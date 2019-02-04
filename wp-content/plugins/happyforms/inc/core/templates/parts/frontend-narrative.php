<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<p><?php
			$tokens = happyforms_get_narrative_tokens( $part['format'] );
			$format = str_replace( '[]', '%s', $part['format'] );
			$inputs = array();

			for ( $t = 0; $t < count( $tokens ); $t ++ ) {
				ob_start(); ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[]" value="<?php happyforms_the_part_value( $part, $form, $t ); ?>"<?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form, $t ); ?> /><?php
				$input = ob_get_clean();
				$tokens[$t] = $input;
			}

			vprintf( html_entity_decode( stripslashes( $format ) ), $tokens );
			?></p>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>