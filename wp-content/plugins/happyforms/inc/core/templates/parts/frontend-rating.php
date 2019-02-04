<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<div class="happyforms-rating-wrap">
				<?php
				$icons = happyforms_get_rating_icons( $part );
				
				switch( $part[ 'rating_type' ] ) {
					case 'yesno':
						require( 'frontend-rating-yesno.php' );
						break;
					case 'scale':
						require( 'frontend-rating-scale.php' );
						break;
				}
				?>
			</div>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>