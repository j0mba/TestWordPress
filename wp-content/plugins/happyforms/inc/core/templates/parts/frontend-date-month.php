<?php
$current_timestamp = current_time( 'timestamp', false );
$month_value = ( happyforms_get_part_value( $part, $form, 'month' ) ) ? happyforms_get_part_value( $part, $form, 'month' ) : '';

if ( '' === $month_value && 'current' === $part['default_datetime'] ) {
	$month_value = date( 'n', $current_timestamp );
}
?>
<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap">
	<div class="happyforms-custom-select" data-searchable="true">
		<div class="happyforms-part__select-wrap">
			<?php
			$months = happyforms_get_months();
			$placeholder_text = __( 'Month', 'happyforms' );
			?>
			
			<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[month]" value="<?php echo $month_value; ?>" data-serialize />
			
			<input type="text" value="<?php echo ( $month_value ) ? $months[$month_value] : ''; ?>" placeholder="<?php echo $placeholder_text; ?>" data-searchable="true" autocomplete="off" <?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />

			<?php
			$options = array();
			
			for ( $i = 1; $i <= 12; $i++ ) {
				$options[] = array(
					'label' => $months[$i],
					'value' => $i,
					'is_default' => ( intval( $month_value ) === $i )
				);
			}
			
			happyforms_select( $options, $part, $form, $placeholder_text );
			?>
		</div>
	</div>
</div>