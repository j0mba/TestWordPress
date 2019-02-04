<script type="text/template" id="happyforms-customize-header-actions">
	<div id="happyforms-save-button-wrapper" class="customize-save-button-wrapper">
		<button id="happyforms-save-button" class="button-primary button" aria-label="<?php _e( 'Save Form', 'happyforms' ); ?>" aria-expanded="false" disabled="disabled" data-text-saved="<?php _e( 'Saved', 'happyforms' ); ?>" data-text-default="<?php _e( 'Save Form', 'happyforms' ); ?>"><?php _e( 'Save Form', 'happyforms' ); ?></button>
	</div>
	<a href="<?php echo esc_url( $wp_customize->get_return_url() ); ?>" id="happyforms-close-link" data-message="<?php _e( 'The changes you made will be lost if you navigate away from this page.', 'happyforms' ); ?>">
		<span class="screen-reader-text"><?php _e( 'Close', 'happyforms' ); ?></span>
	</a>
</script>
