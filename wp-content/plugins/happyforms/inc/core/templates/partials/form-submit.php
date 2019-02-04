<div class="happyforms-form__part happyforms-part happyforms-part--submit">
    <input type="submit" class="happyforms-submit happyforms-button--submit" value="<?php echo esc_attr( happyforms_get_form_property( $form, 'submit_button_label' ) ); ?>" <?php if ( happyforms_submit_enabled( $form ) ) : ?> disabled<?php endif; ?>>
</div>