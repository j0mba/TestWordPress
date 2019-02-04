<script type="text/template" id="happyforms-customize-number-template">
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-header.php' ); ?>
	<p>
		<label for="<%= instance.id %>_title"><?php _e( 'Title', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%= instance.label %>" data-bind="label" />
	</p>
	<p>
		<label for="<%= instance.id %>_label_placement"><?php _e( 'Title placement', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_label_placement" data-bind="label_placement">
			<option value="above"<%= (instance.label_placement == 'above') ? ' selected' : '' %>><?php _e( 'Above', 'happyforms' ); ?></option>
			<option value="left"<%= (instance.label_placement == 'left') ? ' selected' : '' %>><?php _e( 'Left', 'happyforms' ); ?></option>
			<option value="below"<%= (instance.label_placement == 'below') ? ' selected' : '' %>><?php _e( 'Below', 'happyforms' ); ?></option>
			<option value="inside"<%= (instance.label_placement == 'inside') ? ' selected' : '' %>><?php _e( 'Inside input', 'happyforms' ); ?></option>
			<option value="as_placeholder"<%= (instance.label_placement == 'as_placeholder') ? ' selected' : '' %>><?php _e( 'Display as placeholder', 'happyforms' ); ?></option>
			<option value="hidden"<%= (instance.label_placement == 'hidden') ? ' selected' : '' %>><?php _e( 'Hidden', 'happyforms' ); ?></option>
		</select>
	</p>
	<p class="label_placement-options" style="display: none">
		<label>
			<input type="checkbox" class="checkbox apply-all-check" value="" data-apply-to="label_placement" /> <?php _e( 'Apply to all parts', 'happyforms' ); ?>
		</label>
	</p>
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Description', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>
	<p class="happyforms-description-options" style="display: <%= (instance.description != '') ? 'block' : 'none' %>">
		<label for="<%= instance.id %>_description_mode"><?php _e( 'Description appearance', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_description_mode" data-bind="description_mode">
			<option value=""><?php _e( 'Standard', 'happyforms' ); ?></option>
			<option value="focus-reveal"<%= (instance.description_mode == 'focus-reveal') ? ' selected' : '' %>><?php _e( 'Reveal on focus', 'happyforms' ); ?></option>
			<option value="tooltip"<%= (instance.description_mode == 'tooltip' || instance.tooltip_description ) ? ' selected' : '' %>><?php _e( 'Tooltip', 'happyforms' ); ?></option>
		</select>
	</p>
	<p class="happyforms-placeholder-option" style="display: <%= ( 'as_placeholder' !== instance.label_placement ) ? 'block' : 'none' %>">
		<label for="<%= instance.id %>_placeholder"><?php _e( 'Placeholder', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_placeholder" class="widefat title" value="<%= instance.placeholder %>" data-bind="placeholder" />
	</p>
	<div class="min-max-wrapper happyforms-customize-controls-wrap--side-by-side">
		<p>
			<label for="<%= instance.id %>_min_value"><?php _e( 'Min value', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_min_value" class="widefat title" value="<%= instance.min_value %>" data-bind="min_value" />
		</p>
		<p>
			<label for="<%= instance.id %>_max_value"><?php _e( 'Max value', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_max_value" class="widefat title" value="<%= instance.max_value %>" data-bind="max_value" />
		</p>
	</div>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'This is required', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
		<p>
			<label>
				<input type="checkbox" name="masked" class="checkbox" value="1" <% if ( instance.masked ) { %>checked="checked"<% } %> data-bind="masked" /> <?php _e( 'Format', 'happyforms' ); ?>
			</label>
		</p>
		<div class="mask-wrapper number-options number-options--numeric happyforms-customize-controls-wrap--side-by-side" style="display: <%= (instance.masked == 1) ? 'flex' : 'none' %>">
			<p>
				<label for="<%= instance.id %>_mask_numeric_thousands_delimiter"><?php _e( 'Thousands separator', 'happyforms' ); ?></label>
				<input type="text" id="<%= instance.id %>_mask_numeric_thousands_delimiter" class="widefat title" value="<%= instance.mask_numeric_thousands_delimiter %>" data-bind="mask_numeric_thousands_delimiter" />
			</p>
			<p>
				<label for="<%= instance.id %>_mask_numeric_decimal_mark"><?php _e( 'Decimal<br>separator', 'happyforms' ); ?></label>
				<input type="text" id="<%= instance.id %>_mask_numeric_decimal_mark" class="widefat title" value="<%= instance.mask_numeric_decimal_mark %>" data-bind="mask_numeric_decimal_mark" />
			</p>
		</div>
		<div class="mask-wrapper number-options number-options--numeric" style="display: <%= (instance.masked == 1) ? 'block' : 'none' %>">
			<p>
				<label for="<%= instance.id %>_mask_numeric_prefix"><?php _e( 'Prefix', 'happyforms' ); ?></label>
				<input type="text" id="<%= instance.id %>_mask_numeric_prefix" class="widefat title" value="<%= instance.mask_numeric_prefix %>" data-bind="mask_numeric_prefix" />
			</p>
		</div>
		<p>
			<label for="<%= instance.id %>_width"><?php _e( 'Width', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_width" name="width" data-bind="width" class="widefat">
				<option value="full"<%= (instance.width == 'full') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
				<option value="half"<%= (instance.width == 'half') ? ' selected' : '' %>><?php _e( 'Half', 'happyforms' ); ?></option>
				<option value="third"<%= (instance.width == 'third') ? ' selected' : '' %>><?php _e( 'Third', 'happyforms' ); ?></option>
				<option value="auto"<%= (instance.width == 'auto') ? ' selected' : '' %>><?php _e( 'Auto', 'happyforms' ); ?></option>
			</select>
		</p>
		<p class="width-options" style="display: none">
			<label>
				<input type="checkbox" class="checkbox apply-all-check" value="" data-apply-to="width" /> <?php _e( 'Apply to all parts', 'happyforms' ); ?>
			</label>
		</p>
		<p>
			<label for="<%= instance.id %>_css_class"><?php _e( 'Custom CSS class', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%= instance.css_class %>" data-bind="css_class" />
		</p>
		<p>
			<label>
				<input type="checkbox" class="checkbox confirmation-checkbox" value="1" <% if ( instance.confirmation_field ) { %>checked="checked"<% } %> data-bind="confirmation_field" /> <?php _e( 'Require confirmation of the value', 'happyforms' ); ?>
			</label>
		</p>
		<p class="confirmation-field-setting" style="display: <%= (instance.confirmation_field == 1) ? 'block' : 'none' %>">
			<label for="<%= instance.id %>_confirmation_field_label"><?php _e( 'Confirmation field title', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_confirmation_field_label" class="widefat title" value="<%= instance.confirmation_field_label %>" data-bind="confirmation_field_label" />
		</p>
	</div>
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-footer.php' ); ?>
</script>