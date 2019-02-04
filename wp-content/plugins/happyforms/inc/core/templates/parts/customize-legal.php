<script type="text/template" id="happyforms-customize-legal-template">
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-header.php' ); ?>
	<p>
		<label for="<%= instance.id %>_width"><?php _e( 'Width', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_width" name="width" data-bind="width" class="widefat">
			<option value="full"<%= (instance.width == 'full') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
			<option value="half"<%= (instance.width == 'half') ? ' selected' : '' %>><?php _e( 'Half', 'happyforms' ); ?></option>
			<option value="third"<%= (instance.width == 'third') ? ' selected' : '' %>><?php _e( 'Third', 'happyforms' ); ?></option>
			<option value="auto"<%= (instance.width == 'auto') ? ' selected' : '' %>><?php _e( 'Auto', 'happyforms' ); ?></option>
		</select>
    </p>
    <p>
        <label for="<%= instance.id %>_legal_text"><?php _e( 'Legal text', 'happyforms' ); ?></label>
        <textarea id="<%= instance.id %>_legal_text" rows="5" name="legal_text" data-bind="legal_text" class="widefat"><%= instance.legal_text %></textarea>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
		<p>
			<label for="<%= instance.id %>_css_class"><?php _e( 'Custom CSS class', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%= instance.css_class %>" data-bind="css_class" />
		</p>
	</div>
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-footer.php' ); ?>
</script>