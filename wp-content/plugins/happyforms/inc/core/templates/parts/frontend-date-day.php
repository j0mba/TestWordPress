<?php
$current_timestamp = current_time( 'timestamp', false );

$day_value = ( happyforms_get_part_value( $part, $form, 'day' ) ) ? happyforms_get_part_value( $part, $form, 'day' ) : '';

if ( '' === $day_value && 'current' === $part['default_datetime'] ) {
	$day_value = date( 'j', $current_timestamp );
}
?>
<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap">
	<div class="happyforms-custom-select" data-searchable="true">
		<div class="happyforms-part__select-wrap">
			<?php $placeholder_text = __( 'Day', 'happyforms' ); ?>
			
			<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[day]" value="<?php echo $day_value; ?>" data-serialize />
			
			<input type="text" value="<?php echo $day_value; ?>" placeholder="<?php echo $placeholder_text; ?>" data-searchable="true" autocomplete="off" <?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />
			
			<?php
			$options = array();
			
			for ( $i = 1; $i <= 31; $i++ ) {
				$options[] = array(
					'label' => $i,
					'value' => $i,
					'is_default' => ( intval( $day_value ) === $i )
				);
			}
			
			happyforms_select( $options, $part, $form, $placeholder_text );
			?>
		</div>
	</div>
</div>