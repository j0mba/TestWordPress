<?php
global $message, $form;

if ( ! $form ) {
	return;
}
?>
<table class="form-table happyforms-message-data-table striped">
	<tbody>
	<?php foreach ( $form['parts'] as $p => $part ):
		$part_value = $message['parts'][$part['id']];
		$visible = apply_filters( 'happyforms_message_part_visible', true, $part );

		if ( ! $visible ) {
			continue;
		} ?>
	<tr>
		<th scope="row"><?php echo esc_html( $part['label'] ); ?></th>
		<td><?php happyforms_the_message_part_value( $part_value, $part ); ?></td>
	</tr>
	<?php endforeach; ?>
	<?php if ( intval( $form['unique_id'] ) ): ?>
	<tr>
		<th scope="row"><?php _e( 'Tracking number', 'happyforms' ); ?></th>
		<td><?php echo $message['tracking_id']; ?></td>
	</tr>
	<?php endif; ?>
	</tbody>
</table>
<p class="happyforms-message-nav">
	<?php happyforms_previous_message_edit_link( $post->ID, __( 'Previous response' ) ); ?>
	<span class="divider">|</span>
	<?php happyforms_next_message_edit_link( $post->ID, __( 'Next response' ) ); ?>
</p>
