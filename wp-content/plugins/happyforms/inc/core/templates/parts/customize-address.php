<script type="text/template" id="happyforms-customize-address-template">
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
			<option value="inside"<%= (instance.label_placement == 'inside') ? ' selected' : '' %>><?php _e( 'Inside', 'happyforms' ); ?></option>
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
	<p>
		<label for="<%= instance.id %>_mode"><?php _e( 'Mode', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_mode" name="mode" data-bind="mode" class="widefat">
			<option value="simple"<%= (instance.mode == 'simple') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
			<option value="autocomplete"<%= (instance.mode == 'autocomplete') ? ' selected' : '' %>><?php _e( 'Full with autocomplete', 'happyforms' ); ?></option>
			<option value="country-city"<%= (instance.mode == 'country-city') ? ' selected' : '' %>><?php _e( 'Country and city', 'happyforms' ); ?></option>
			<option value="country"<%= (instance.mode == 'country') ? ' selected' : '' %>><?php _e( 'Country only', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label>
			<input type="checkbox" name="has_geolocation" class="checkbox" value="1" <% if ( instance.has_geolocation ) { %>checked="checked"<% } %> data-bind="has_geolocation" /> <?php _e( 'Allow geolocation', 'happyforms' ); ?>
		</label>
	</p>
	<div class="address-apikey">
		<p>
			<label for="<%= instance.id %>_apikey"><?php _e( 'Google API Key', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_apikey class="widefat title" value="<%= instance.apikey %>" data-bind="apikey" />
		</p>
		<p class="description">
			<?php printf(
				'%s <a href="https://developers.google.com/places/web-service/get-api-key" target="_blank" class="external">%s</a>.',
				__( 'Address autocompletion requires a', 'happyforms' ),
				__( 'Google Places API key', 'happyforms' )
			); ?>
			<?php printf(
				'%s <a href="https://developers.google.com/maps/documentation/geocoding/start" target="_blank" class="external">%s</a>.',
				__( 'Geolocation requires a', 'happyforms' ),
				__( 'Google Geocoding API key', 'happyforms' )
			); ?>
		</p>
	</div>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'This is required', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
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
			<label for="<%= instance.id %>_css_class"><?php _e( 'Custom CSS class', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%= instance.css_class %>" data-bind="css_class" />
		</p>
	</div>
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-footer.php' ); ?>
</script>