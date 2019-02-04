<?php

if ( ! function_exists( 'happyforms_parse_pixel_value' ) ):
/**
 * Sanitize checkbox values.
 *
 * @since 1.0
 *
 * @param int|string $value The original value.
 *
 * @return int|string       1 if value was 1, or empty string.
 */
function happyforms_parse_pixel_value( $value ) {
	return is_numeric( $value ) ? "{$value}px" : $value;
}

endif;
