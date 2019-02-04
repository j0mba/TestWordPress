<?php

if ( ! function_exists( 'happyforms_get_part_label' ) ):
/**
 * Get a non-empty label for the part.
 *
 * @since 1.0
 *
 * @param array $part_data The part data to retrieve the label for.
 *
 * @return string
 */
function happyforms_get_part_label( $part_data ) {
	return ! empty( $part_data['label'] ) ? $part_data['label'] : $part_data['id'];
}

endif;

if ( ! function_exists( 'happyforms_get_csv_header' ) ):
/**
 * Get a non-empty CSV header for the part.
 *
 * @param array $part The part data to retrieve the label for.
 *
 * @return string
 */
function happyforms_get_csv_header( $part ) {
	$part_label = $part['label'];
	$part_id = $part['id'];
	$header = ! empty( $part['label'] ) ? $part_label : "Blank [{$part_id}]";

	return $header;
}

endif;

if ( ! function_exists( 'happyforms_get_csv_value' ) ):
/**
 * Get a CSV response value.
 *
 * @param array $message The message data to retrieve the value for.
 * @param array $part    The part data relative to the current value.
 *
 * @return string
 */
function happyforms_get_csv_value( $value, $message, $part, $form ) {
	$value = happyforms_get_message_part_value( $value, $part );
	$value = htmlspecialchars_decode( $value );
	$value = apply_filters( 'happyforms_get_csv_value', $value, $message, $part, $form );

	return $value;
}

endif;

if ( ! function_exists( 'happyforms_get_message_part_value' ) ):
/**
 * Get the part submission value in a readable format.
 *
 * @since 1.0
 *
 * @param mixed  $value       The original submission value.
 * @param array  $part        Current part data.
 * @param string $destination An optional destination slug.
 *
 * @return string
 */
function happyforms_get_message_part_value( $value, $part = array(), $destination = '' ) {
	$original_value = $value;

	if ( is_string( $value ) ) {
		$value = maybe_unserialize( $value );
	}

	if ( is_array( $value ) ) {
		$value = array_filter( array_values( $value ) );
		$value = implode( ', ', $value );
	}

	$value = wp_unslash( $value );

	$value = apply_filters( 'happyforms_message_part_value', $value, $original_value, $part, $destination );

	return $value;
}

endif;

if ( ! function_exists( 'happyforms_the_message_part_value' ) ):

function happyforms_the_message_part_value( $value, $part = array(), $destination = '' ) {
	$value = happyforms_get_message_part_value( $value, $part, $destination );

	switch ( $part['type'] ) {
		case 'email':
			$value = "<a href=\"mailto:{$value}\">{$value}</a>";
			break;
	}

	echo $value;
}

endif;

if ( ! function_exists( 'happyforms_stringify_part_value' ) ):
/**
 * Transforms a part value into a string.
 *
 * @since 1.0
 *
 * @param mixed $value The original submission value.
 * @param array $part  Current part data.
 * @param array $form  Current form data.
 *
 * @return string
 */
function happyforms_stringify_part_value( $value, $part, $form ) {
	$value = apply_filters( 'happyforms_stringify_part_value', $value, $part, $form );
	$value = maybe_serialize( $value );

	return $value;
}

endif;

if ( ! function_exists( 'happyforms_customizer_url' ) ):
/**
 * Get a formatted url for the Customize screen,
 * complete with a return url.
 *
 * @since 1.0
 *
 * @param string $return_url The url to return to after
 *                           the Customize screen is closed.
 *
 * @return string
 */
function happyforms_customizer_url( $return_url = '' ) {
	if ( '' === $return_url ) {
		$return_url = urlencode( add_query_arg( null, null ) );
	}

	$customize_url = add_query_arg( array(
		'return' => $return_url,
		'happyforms' => 1,
	), 'customize.php' );

	return $customize_url;
}

endif;

if ( ! function_exists( 'happyforms_get_form_edit_link' ) ):
/**
 * Get the admin edit url for a HappyForm post.
 *
 * @since 1.0
 *
 * @param string|int $id         The form ID.
 * @param string     $return_url The url to return to after
 *                               the Customize screen is closed.
 *
 * @return string
 */
function happyforms_get_form_edit_link( $id, $return_url = '', $step = '' ) {
	$return_url = empty( $return_url ) ? happyforms_get_all_form_link() : $return_url;
	$base_url = add_query_arg( array(
		'form_id' => $id,
	), happyforms_customizer_url( $return_url ) );
	$step = in_array( $step, array( 'build', 'setup', 'style' ) ) ? $step : 'build';
	$url = "{$base_url}#{$step}";

	return $url;
}

endif;

if ( ! function_exists( 'happyforms_get_all_form_link' ) ):
/**
 * Get the url of the All Forms admin screen.
 *
 * @since 1.0
 *
 * @return string
 */
function happyforms_get_all_form_link() {
	return admin_url( 'edit.php?post_type=' . happyforms_get_form_controller()->post_type );
}

endif;

if ( ! function_exists( 'happyforms_admin_footer' ) ):
/**
 * Output the Happyforms rating admin footer.
 *
 * @since 1.0
 *
 * @return string
 */
function happyforms_admin_footer() {
	?>
	<?php _e( 'How are we doing? Please rate', 'happyforms' ); ?> <strong>HappyForms</strong> <a href="https://wordpress.org/support/plugin/happyforms/reviews/?filter=5#new-post" target="_blank" rel="noopener">★★★★★</a> <?php _e( 'on', 'happyforms' ); ?> <a href="https://wordpress.org/support/plugin/happyforms/reviews/?filter=5#new-post" target="_blank">WordPress.org</a> <?php _e( 'to help us spread the word.', 'happyforms' ); ?>
	<?php
}

endif;

if ( ! function_exists( 'happyforms_previous_message_edit_link' ) ):
/**
 * Outputs a link to the previous unread message edit screen.
 *
 * @since 1.1
 *
 * @return void
 */
function happyforms_previous_message_edit_link( $post_id, $text ) {
	global $happyforms_message_nav;

	if ( array_search( $post_id, $happyforms_message_nav ) > 0 ) {
		edit_post_link( $text, '', '', $happyforms_message_nav[0] );
	}
}

endif;

if ( ! function_exists( 'happyforms_next_message_edit_link' ) ):
/**
 * Outputs a link to the next unread message edit screen.
 *
 * @since 1.1
 *
 * @return void
 */
function happyforms_next_message_edit_link( $post_id, $text ) {
	global $happyforms_message_nav;

	if ( array_search( $post_id, $happyforms_message_nav )
		=== count( $happyforms_message_nav ) - 2 ) {

		edit_post_link( $text, '', '', $happyforms_message_nav[2] );
	}
}

endif;

if ( ! function_exists( 'happyforms_get_countries' ) ):
/**
 * Outputs an array of country names.
 *
 * @since 1.1
 *
 * @return void
 */
function happyforms_get_countries() {
	return array(
		__( 'Afghanistan', 'happyforms' ),
		__( 'Albania', 'happyforms' ),
		__( 'Algeria', 'happyforms' ),
		__( 'American Samoa', 'happyforms' ),
		__( 'Andorra', 'happyforms' ),
		__( 'Angola', 'happyforms' ),
		__( 'Anguilla', 'happyforms' ),
		__( 'Antarctica', 'happyforms' ),
		__( 'Antigua and Barbuda', 'happyforms' ),
		__( 'Argentina', 'happyforms' ),
		__( 'Armenia', 'happyforms' ),
		__( 'Aruba', 'happyforms' ),
		__( 'Australia', 'happyforms' ),
		__( 'Austria', 'happyforms' ),
		__( 'Azerbaijan', 'happyforms' ),
		__( 'Bahamas', 'happyforms' ),
		__( 'Bahrain', 'happyforms' ),
		__( 'Bangladesh', 'happyforms' ),
		__( 'Barbados', 'happyforms' ),
		__( 'Belarus', 'happyforms' ),
		__( 'Belgium', 'happyforms' ),
		__( 'Belize', 'happyforms' ),
		__( 'Benin', 'happyforms' ),
		__( 'Bermuda', 'happyforms' ),
		__( 'Bhutan', 'happyforms' ),
		__( 'Bolivia', 'happyforms' ),
		__( 'Bosnia and Herzegowina', 'happyforms' ),
		__( 'Botswana', 'happyforms' ),
		__( 'Bouvet Island', 'happyforms' ),
		__( 'Brazil', 'happyforms' ),
		__( 'British Indian Ocean Territory', 'happyforms' ),
		__( 'Brunei Darussalam', 'happyforms' ),
		__( 'Bulgaria', 'happyforms' ),
		__( 'Burkina Faso', 'happyforms' ),
		__( 'Burundi', 'happyforms' ),
		__( 'Cambodia', 'happyforms' ),
		__( 'Cameroon', 'happyforms' ),
		__( 'Canada', 'happyforms' ),
		__( 'Cape Verde', 'happyforms' ),
		__( 'Cayman Islands', 'happyforms' ),
		__( 'Central African Republic', 'happyforms' ),
		__( 'Chad', 'happyforms' ),
		__( 'Chile', 'happyforms' ),
		__( 'China', 'happyforms' ),
		__( 'Christmas Island', 'happyforms' ),
		__( 'Cocos (Keeling) Islands', 'happyforms' ),
		__( 'Colombia', 'happyforms' ),
		__( 'Comoros', 'happyforms' ),
		__( 'Congo', 'happyforms' ),
		__( 'Congo, the Democratic Republic of the', 'happyforms' ),
		__( 'Cook Islands', 'happyforms' ),
		__( 'Costa Rica', 'happyforms' ),
		__( 'Ivory Coast', 'happyforms' ),
		__( 'Croatia (Hrvatska)', 'happyforms' ),
		__( 'Cuba', 'happyforms' ),
		__( 'Cyprus', 'happyforms' ),
		__( 'Czech Republic', 'happyforms' ),
		__( 'Denmark', 'happyforms' ),
		__( 'Djibouti', 'happyforms' ),
		__( 'Dominica', 'happyforms' ),
		__( 'Dominican Republic', 'happyforms' ),
		__( 'East Timor', 'happyforms' ),
		__( 'Ecuador', 'happyforms' ),
		__( 'Egypt', 'happyforms' ),
		__( 'El Salvador', 'happyforms' ),
		__( 'Equatorial Guinea', 'happyforms' ),
		__( 'Eritrea', 'happyforms' ),
		__( 'Estonia', 'happyforms' ),
		__( 'Ethiopia', 'happyforms' ),
		__( 'Falkland Islands (Malvinas)', 'happyforms' ),
		__( 'Faroe Islands', 'happyforms' ),
		__( 'Fiji', 'happyforms' ),
		__( 'Finland', 'happyforms' ),
		__( 'France', 'happyforms' ),
		__( 'France Metropolitan', 'happyforms' ),
		__( 'French Guiana', 'happyforms' ),
		__( 'French Polynesia', 'happyforms' ),
		__( 'French Southern Territories', 'happyforms' ),
		__( 'Gabon', 'happyforms' ),
		__( 'Gambia', 'happyforms' ),
		__( 'Georgia', 'happyforms' ),
		__( 'Germany', 'happyforms' ),
		__( 'Ghana', 'happyforms' ),
		__( 'Gibraltar', 'happyforms' ),
		__( 'Greece', 'happyforms' ),
		__( 'Greenland', 'happyforms' ),
		__( 'Grenada', 'happyforms' ),
		__( 'Guadeloupe', 'happyforms' ),
		__( 'Guam', 'happyforms' ),
		__( 'Guatemala', 'happyforms' ),
		__( 'Guinea', 'happyforms' ),
		__( 'Guinea-Bissau', 'happyforms' ),
		__( 'Guyana', 'happyforms' ),
		__( 'Haiti', 'happyforms' ),
		__( 'Heard and Mc Donald Islands', 'happyforms' ),
		__( 'Holy See (Vatican City State)', 'happyforms' ),
		__( 'Honduras', 'happyforms' ),
		__( 'Hong Kong', 'happyforms' ),
		__( 'Hungary', 'happyforms' ),
		__( 'Iceland', 'happyforms' ),
		__( 'India', 'happyforms' ),
		__( 'Indonesia', 'happyforms' ),
		__( 'Iran (Islamic Republic of)', 'happyforms' ),
		__( 'Iraq', 'happyforms' ),
		__( 'Ireland', 'happyforms' ),
		__( 'Israel', 'happyforms' ),
		__( 'Italy', 'happyforms' ),
		__( 'Jamaica', 'happyforms' ),
		__( 'Japan', 'happyforms' ),
		__( 'Jordan', 'happyforms' ),
		__( 'Kazakhstan', 'happyforms' ),
		__( 'Kenya', 'happyforms' ),
		__( 'Kiribati', 'happyforms' ),
		__( 'Korea, Democratic People\'s Republic of', 'happyforms' ),
		__( 'Korea, Republic of', 'happyforms' ),
		__( 'Kuwait', 'happyforms' ),
		__( 'Kyrgyzstan', 'happyforms' ),
		__( 'Lao, People\'s Democratic Republic', 'happyforms' ),
		__( 'Latvia', 'happyforms' ),
		__( 'Lebanon', 'happyforms' ),
		__( 'Lesotho', 'happyforms' ),
		__( 'Liberia', 'happyforms' ),
		__( 'Libyan Arab Jamahiriya', 'happyforms' ),
		__( 'Liechtenstein', 'happyforms' ),
		__( 'Lithuania', 'happyforms' ),
		__( 'Luxembourg', 'happyforms' ),
		__( 'Macau', 'happyforms' ),
		__( 'Macedonia, The Former Yugoslav Republic of', 'happyforms' ),
		__( 'Madagascar', 'happyforms' ),
		__( 'Malawi', 'happyforms' ),
		__( 'Malaysia', 'happyforms' ),
		__( 'Maldives', 'happyforms' ),
		__( 'Mali', 'happyforms' ),
		__( 'Malta', 'happyforms' ),
		__( 'Marshall Islands', 'happyforms' ),
		__( 'Martinique', 'happyforms' ),
		__( 'Mauritania', 'happyforms' ),
		__( 'Mauritius', 'happyforms' ),
		__( 'Mayotte', 'happyforms' ),
		__( 'Mexico', 'happyforms' ),
		__( 'Micronesia, Federated States of', 'happyforms' ),
		__( 'Moldova, Republic of', 'happyforms' ),
		__( 'Monaco', 'happyforms' ),
		__( 'Mongolia', 'happyforms' ),
		__( 'Montserrat', 'happyforms' ),
		__( 'Morocco', 'happyforms' ),
		__( 'Mozambique', 'happyforms' ),
		__( 'Myanmar', 'happyforms' ),
		__( 'Namibia', 'happyforms' ),
		__( 'Nauru', 'happyforms' ),
		__( 'Nepal', 'happyforms' ),
		__( 'Netherlands', 'happyforms' ),
		__( 'Netherlands Antilles', 'happyforms' ),
		__( 'New Caledonia', 'happyforms' ),
		__( 'New Zealand', 'happyforms' ),
		__( 'Nicaragua', 'happyforms' ),
		__( 'Niger', 'happyforms' ),
		__( 'Nigeria', 'happyforms' ),
		__( 'Niue', 'happyforms' ),
		__( 'Norfolk Island', 'happyforms' ),
		__( 'Northern Mariana Islands', 'happyforms' ),
		__( 'Norway', 'happyforms' ),
		__( 'Oman', 'happyforms' ),
		__( 'Pakistan', 'happyforms' ),
		__( 'Palau', 'happyforms' ),
		__( 'Panama', 'happyforms' ),
		__( 'Papua New Guinea', 'happyforms' ),
		__( 'Paraguay', 'happyforms' ),
		__( 'Peru', 'happyforms' ),
		__( 'Philippines', 'happyforms' ),
		__( 'Pitcairn', 'happyforms' ),
		__( 'Poland', 'happyforms' ),
		__( 'Portugal', 'happyforms' ),
		__( 'Puerto Rico', 'happyforms' ),
		__( 'Qatar', 'happyforms' ),
		__( 'Reunion', 'happyforms' ),
		__( 'Romania', 'happyforms' ),
		__( 'Russian Federation', 'happyforms' ),
		__( 'Rwanda', 'happyforms' ),
		__( 'Saint Kitts and Nevis', 'happyforms' ),
		__( 'Saint Lucia', 'happyforms' ),
		__( 'Saint Vincent and the Grenadines', 'happyforms' ),
		__( 'Samoa', 'happyforms' ),
		__( 'San Marino', 'happyforms' ),
		__( 'Sao Tome and Principe', 'happyforms' ),
		__( 'Saudi Arabia', 'happyforms' ),
		__( 'Senegal', 'happyforms' ),
		__( 'Seychelles', 'happyforms' ),
		__( 'Sierra Leone', 'happyforms' ),
		__( 'Singapore', 'happyforms' ),
		__( 'Slovakia (Slovak Republic)', 'happyforms' ),
		__( 'Slovenia', 'happyforms' ),
		__( 'Solomon Islands', 'happyforms' ),
		__( 'Somalia', 'happyforms' ),
		__( 'South Africa', 'happyforms' ),
		__( 'South Georgia and the South Sandwich Islands', 'happyforms' ),
		__( 'Spain', 'happyforms' ),
		__( 'Sri Lanka', 'happyforms' ),
		__( 'St. Helena', 'happyforms' ),
		__( 'St. Pierre and Miquelon', 'happyforms' ),
		__( 'Sudan', 'happyforms' ),
		__( 'Suriname', 'happyforms' ),
		__( 'Svalbard and Jan Mayen Islands', 'happyforms' ),
		__( 'Swaziland', 'happyforms' ),
		__( 'Sweden', 'happyforms' ),
		__( 'Switzerland', 'happyforms' ),
		__( 'Syrian Arab Republic', 'happyforms' ),
		__( 'Taiwan, Province of China', 'happyforms' ),
		__( 'Tajikistan', 'happyforms' ),
		__( 'Tanzania, United Republic of', 'happyforms' ),
		__( 'Thailand', 'happyforms' ),
		__( 'Togo', 'happyforms' ),
		__( 'Tokelau', 'happyforms' ),
		__( 'Tonga', 'happyforms' ),
		__( 'Trinidad and Tobago', 'happyforms' ),
		__( 'Tunisia', 'happyforms' ),
		__( 'Turkey', 'happyforms' ),
		__( 'Turkmenistan', 'happyforms' ),
		__( 'Turks and Caicos Islands', 'happyforms' ),
		__( 'Tuvalu', 'happyforms' ),
		__( 'Uganda', 'happyforms' ),
		__( 'Ukraine', 'happyforms' ),
		__( 'United Arab Emirates', 'happyforms' ),
		__( 'United Kingdom', 'happyforms' ),
		__( 'United States', 'happyforms' ),
		__( 'United States Minor Outlying Islands', 'happyforms' ),
		__( 'Uruguay', 'happyforms' ),
		__( 'Uzbekistan', 'happyforms' ),
		__( 'Vanuatu', 'happyforms' ),
		__( 'Venezuela', 'happyforms' ),
		__( 'Vietnam', 'happyforms' ),
		__( 'Virgin Islands (British)', 'happyforms' ),
		__( 'Virgin Islands (U.S.)', 'happyforms' ),
		__( 'Wallis and Futuna Islands', 'happyforms' ),
		__( 'Western Sahara', 'happyforms' ),
		__( 'Yemen', 'happyforms' ),
		__( 'Yugoslavia', 'happyforms' ),
		__( 'Zambia', 'happyforms' ),
		__( 'Zimbabwe', 'happyforms' ),
	);
}

endif;

if ( ! function_exists( 'happyforms_unread_messages_badge' ) ):
/**
 * Outputs the unread messages badge, if there are any.
 *
 * @since 1.1
 *
 * @return void
 */
function happyforms_unread_messages_badge() {
	$count = get_transient( 'happyforms_unread_messages' );
	$badge = '';

	if ( $count ) {
		$badge = sprintf(
			' <span class="awaiting-mod count-1"><span class="pending-count">%s</span></span>',
			$count
		);
	}

	return $badge;
}

endif;

if ( ! function_exists( 'happyforms_is_preview' ) ):
/**
 * Returns whether or not we're previewing a HappyForm.
 *
 * @since 1.3
 *
 * @return void
 */
function happyforms_is_preview() {
	$post_type = happyforms_get_form_controller()->post_type;
	$is_happyform = get_post_type() === $post_type;
	$happyform_parameter = isset( $_POST['happyforms'] );

	// Preview frame
	if ( $is_happyform && is_customize_preview() ) {
		return true;
	}

	// Ajax calls
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && $happyform_parameter ) {
		return true;
	}

	return false;
}

endif;

if ( ! function_exists( 'happyforms_get_reply_and_mark_link' ) ):

function happyforms_get_reply_and_mark_link( $id ) {
	$message_controller = happyforms_get_message_controller();

	if ( ! $post = get_post( $id ) ) {
		return;
	}

	if ( $message_controller->post_type !== $post->post_type ) {
		return;
	}

	$action = '&action=edit';
	$post_type_object = get_post_type_object( $post->post_type );
	$link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
	$url = add_query_arg( $message_controller->reply_and_mark_action, 1, $link );

	return $url;
}

endif;

if ( ! function_exists( 'happyforms_get_email_part_label' ) ):

function happyforms_get_email_part_label( $message, $part = array(), $form = array() ) {
	$label = happyforms_get_part_label( $part );
	$label = apply_filters( 'happyforms_email_part_label', $label, $message, $part, $form );

	return $label;
}

endif;

if ( ! function_exists( 'happyforms_get_email_part_value' ) ):

function happyforms_get_email_part_value( $message, $part = array(), $form = array() ) {
	$part_id = $part['id'];
	$value = happyforms_get_message_part_value( $message['parts'][$part_id], $part, 'email' );
	$value = apply_filters( 'happyforms_email_part_value', $value, $message, $part, $form );

	return $value;
}

endif;

if ( ! function_exists( 'happyforms_is_preview_context' ) ) :

function happyforms_is_preview_context() {
	$preview = is_customize_preview();
	$block = happyforms_is_block_context();

	return $preview || $block;
}

endif;

if ( ! function_exists( 'happyforms_is_block_context' ) ) :

function happyforms_is_block_context() {
	$is_block = defined( 'REST_REQUEST' ) && REST_REQUEST;

	return $is_block;
}

endif;

if ( ! function_exists( 'happyforms_is_gutenberg' ) ):

function happyforms_is_gutenberg() {
	global $wp_version;

	$is_50 = version_compare( $wp_version, '5.0-alpha', '>=' );
	$is_plugin = is_plugin_active( 'gutenberg/gutenberg.php' );
	$is_gutenberg = $is_50 || $is_plugin;

	return $is_gutenberg;
}

endif;

if ( ! function_exists( 'happyforms_update_meta' ) ):

function happyforms_update_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
	$meta_key = "_happyforms_{$meta_key}";

	return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
}

endif;

if ( ! function_exists( 'happyforms_get_meta' ) ):

function happyforms_get_meta( $post_id, $key = '', $single = false ) {
	$key = "_happyforms_{$key}";

	return get_post_meta( $post_id, $key, $single );
}

endif;

if ( ! function_exists( 'happyforms_unprefix_meta' ) ):

function happyforms_unprefix_meta( $meta ) {
	$meta = $meta ? $meta : array();
	$meta = array_map( 'reset', $meta );
	$meta = array_map( 'maybe_unserialize', $meta );

	foreach( $meta as $key => $value ) {
		$unprefixed_key = str_replace( '_happyforms_', '', $key );
		unset( $meta[$key] );
		$meta[$unprefixed_key] = $value;
	}

	return $meta;
}

endif;

if ( ! function_exists( 'happyforms_prefix_meta' ) ):

function happyforms_prefix_meta( $meta ) {
	foreach( $meta as $key => $value ) {
		$prefixed_key = "_happyforms_{$key}";
		$meta[$prefixed_key] = $value;
		unset( $meta[$key] );
	}

	return $meta;
}

endif;

if ( ! function_exists( 'happyforms_get_message_title' ) ):

function happyforms_get_message_title( $message_id ) {
	$title = sprintf( __( 'Response #%s', 'happyforms' ), $message_id );

	return $title;
}

endif;
