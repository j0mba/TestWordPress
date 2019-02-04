<?php

if ( ! function_exists( 'happyforms_months_dropdown' ) ):
/**
 * Output a generic 1-12 dropdown.
 *
 * @since  1.0
 *
 * @return void
 */
function happyforms_months_dropdown() {
	for ( $m = 1; $m <= 12; $m ++ ):
		$t = strtotime( sprintf( '%d months', $m ) );
	?>
	<option value="<?php echo sprintf( '%02d', $m ); ?>"><?php echo sprintf( '%02d', $m ) . '-' . date( 'M', $t ); ?></option>
	<?php endfor;
}

endif;
