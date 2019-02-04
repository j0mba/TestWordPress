<script type="text/template" id="happyforms-customize-date-template">
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
			<option value="tooltip"<%= (instance.description_mode == 'tooltip' || instance.tooltip_description ) ? ' selected' : '' %>><?php _e( 'Tooltip', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label for="<%= instance.id %>_date_type"><?php _e( 'Show', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_date_type" name="date_type" data-bind="date_type" class="widefat">
			<option value="date"<%= (instance.date_type == 'date') ? ' selected' : '' %>><?php _e( 'Date', 'happyforms' ); ?></option>
			<option value="datetime"<%= (instance.date_type == 'datetime') ? ' selected' : '' %>><?php _e( 'Date &amp; Time', 'happyforms' ); ?></option>
			<option value="time"<%= (instance.date_type == 'time') ? ' selected' : '' %>><?php _e( 'Time', 'happyforms' ); ?></option>
			<option value="month_year"<%= (instance.date_type == 'month_year') ? ' selected' : '' %>><?php _e( 'Month &amp; Year', 'happyforms' ); ?></option>
			<option value="month"<%= (instance.date_type == 'month') ? ' selected' : '' %>><?php _e( 'Month only', 'happyforms' ); ?></option>
			<option value="year"<%= (instance.date_type == 'year') ? ' selected' : '' %>><?php _e( 'Year only', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label for="<%= instance.id %>_default_datetime"><?php _e( 'Default value', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_default_datetime" name="default_datetime" data-bind="default_datetime" class="widefat">
			<option value=""<%= (instance.default_datetime == 'blank') ? ' selected' : '' %>><?php _e( 'Blank', 'happyforms' ); ?></option>
			<option value="current"<%= (instance.default_datetime == 'current') ? ' selected' : '' %>><?php _e( 'Current date and time', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'This is required', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
		<p class="date-options" style="margin-bottom: 0<%= (instance.date_type == 'time') ? '; display: none' : '' %>">
			<label for="<%= instance.id %>_years_option"><?php _e( 'Show', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_years_option" name="years_option" data-bind="years_option" class="widefat">
				<option value="all"<%= (instance.years_option == 'all') ? ' selected' : '' %>><?php _e( 'All years', 'happyforms' ); ?></option>
				<option value="past"<%= (instance.years_option == 'past') ? ' selected' : '' %>><?php _e( 'Past years only', 'happyforms' ); ?></option>
				<option value="future"<%= (instance.years_option == 'future') ? ' selected' : '' %>><?php _e( 'Future years only', 'happyforms' ); ?></option>
			</select>
		</p>
		<div class="date-options happyforms-customize-controls-wrap--side-by-side"<%= (instance.date_type == 'time') ? ' style="display: none"' : '' %>>
			<p>
				<label for="<%= instance.id %>_min_year"><?php _e( 'Start from', 'happyforms' ); ?></label>
				<input type="number" id="<%= instance.id %>_min_year" data-bind="min_year" value="<%= instance.min_year %>">
			</p>
			<p>
				<label for="<%= instance.id %>_max_year"><?php _e( 'End at', 'happyforms' ); ?></label>
				<input type="number" id="<%= instance.id %>_max_year" data-bind="max_year" min="<%= instance.min_year %>" max="<?php echo date( 'Y' ) + 2; ?>" value="<%= instance.max_year %>">
			</p>
		</div>
		<p class="date-options" style="margin-top: 0;<%= (instance.date_type == 'time') ? ' display: none' : '' %>">
			<label for="<%= instance.id %>_years_order"><?php _e( 'Years order', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_years_order" name="years_order" data-bind="years_order" class="widefat">
				<option value="desc"<%= (instance.years_order == 'desc') ? ' selected' : '' %>>DESC</option>
				<option value="asc"<%= (instance.years_order == 'asc') ? ' selected' : '' %>>ASC</option>
			</select>
		</p>
		<div class="time-options happyforms-customize-controls-wrap--side-by-side"<%= (instance.date_type == 'date') ? ' style="display: none"' : '' %>>
			<p>
				<label for="<%= instance.id %>_min_hour"><?php _e( 'Min hour', 'happyforms' ); ?></label>
				<input type="number" id="<%= instance.id %>_min_hour" data-bind="min_hour" value="<%= instance.min_hour %>">
			</p>
			<p>
				<label for="<%= instance.id %>_max_hour"><?php _e( 'Max hour', 'happyforms' ); ?></label>
				<input type="number" id="<%= instance.id %>_max_hour" data-bind="max_hour" min="<%= instance.max_hour %>" max="<?php echo date( 'Y' ) + 2; ?>" value="<%= instance.max_hour %>">
			</p>
		</div>
		<p class="time-options">
			<label for="<%= instance.id %>_minute_step"><?php _e( 'Minute step', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_minute_step" min="0" max="30" step="15" data-bind="minute_step" value="<%= instance.minute_step %>">
		</p>
		<p class="time-options"<%= (instance.date_type == 'date') ? ' style="display: none"' : '' %>>
			<label for="<%= instance.id %>_time_format"><?php _e( 'Time format', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_time_format" name="time_format" data-bind="time_format" class="widefat">
				<option value="12"<%= (instance.time_format == '12') ? ' selected' : '' %>><?php _e( '12h', 'happyforms' ); ?></option>
				<option value="24"<%= (instance.time_format == '24') ? ' selected' : '' %>><?php _e( '24h', 'happyforms' ); ?></option>
			</select>
		</p>
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
	</div>
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-footer.php' ); ?>
</script>