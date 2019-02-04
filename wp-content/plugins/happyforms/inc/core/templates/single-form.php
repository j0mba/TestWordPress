<?php do_action( 'happyforms_form_before', $form ); ?>

<div class="happyforms-form" id="happyforms-<?php echo esc_attr( $form['ID'] ); ?>">
	<form action="<?php happyforms_form_action( $form['ID'] ); ?>" class="<?php happyforms_the_form_class( $form ); ?>" id="<?php happyforms_the_form_id( $form ); ?>" method="post" <?php happyforms_the_form_attributes( $form ); ?>>
		<?php do_action( 'happyforms_form_open', $form ); ?>

		<?php happyforms_action_field(); ?>
		<?php happyforms_form_field( $form['ID'] ); ?>
		<?php happyforms_nonce_field( $form ); ?>
		<?php happyforms_step_field( $form ); ?>

		<?php do_action( 'happyforms_before_title', $form ); ?>
		<?php happyforms_the_form_title( $form ); ?>
		<?php do_action( 'happyforms_after_title', $form ); ?>

		<div class="happyforms-flex">
			<?php happyforms_message_notices( $form['ID'] ); ?>
			<?php happyforms_honeypot( $form ); ?>
			<?php foreach ( $form['parts'] as $part ) {
				happyforms_the_form_part( $part, $form );
			} ?>
			<?php happyforms_recaptcha( $form ); ?>
			<?php happyforms_submit( $form ); ?>
		</div>

		<?php do_action( 'happyforms_form_close', $form ); ?>
	</form>
</div>

<?php do_action( 'happyforms_form_after', $form ); ?>