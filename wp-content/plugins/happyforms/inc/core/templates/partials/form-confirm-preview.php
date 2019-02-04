<div class="happyforms-form__part happyforms-part happyforms-part--submit">
	<input type="submit" class="happyforms-submit happyforms-button--submit" value="<?php echo esc_attr( happyforms_get_form_property( $form, 'submit_button_label' ) ); ?>" data-step="<?php echo happyforms_get_last_step( $form, true ); ?>" />
	<a href="#" class="submit"><?php _e( 'Edit', 'happyforms' ); ?></a>
</div>