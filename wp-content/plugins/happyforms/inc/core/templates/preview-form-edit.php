<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php wp_title(); ?></title>
		<?php wp_head(); ?>
	</head>
	<body class="happyforms-preview">
		<div id="message" class="updated published notice notice-success">
			<p><?php printf( __( 'This is a preview of your new HappyForm. Once you’ve finished building, you can add this form to any Page, Post and Widget area. Have questions? <a href="%s" class="happyforms-ask-link" target="_blank">Ask for help in our support forums.</a>', 'happyforms' ), 'https://wordpress.org/support/plugin/happyforms' ); ?></p>
		</div>

		<?php global $post; $form = happyforms_get_form_controller()->get( $post->ID ); ?>
		<?php happyforms_the_form_styles( $form ); ?>
		<?php include( happyforms_get_include_folder() . '/core/templates/single-form.php' ); ?>

		<?php wp_footer(); ?>
	</body>
</html>
