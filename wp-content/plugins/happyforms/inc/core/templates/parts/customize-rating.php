<script type="text/template" id="happyforms-customize-rating-template">
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
		<label for="<%= instance.id %>_rating_type"><?php _e( 'Rating type', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_rating_type" data-bind="rating_type">
			<option value="yesno"<%= (instance.rating_type == 'yesno') ? ' selected' : '' %>><?php _e( 'No / Yes', 'happyforms' ); ?></option>
			<option value="scale"<%= (instance.rating_type == 'scale') ? ' selected' : '' %>><?php _e( 'Scale of 1 to 5', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label for="<%= instance.id %>_rating_visuals"><?php _e( 'Rating visuals', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_rating_visuals" data-bind="rating_visuals">
			<option class="scale-default" data-allowed-for="scale" value="stars"<%= (instance.rating_visuals == 'stars') ? ' selected' : '' %><%= (instance.rating_type == 'yesno' ) ? ' disabled' : '' %>><?php _e( 'Stars', 'happyforms' ); ?></option>
			<option class="yesno-default" data-allowed-for="scale,yesno" value="smileys"<%= (instance.rating_visuals == 'smileys') ? ' selected' : '' %>><?php _e( 'Smileys', 'happyforms' ); ?></option>
			<option value="thumbs" data-allowed-for="yesno" <%= (instance.rating_visuals == 'thumbs') ? ' selected' : '' %><%= (instance.rating_type == 'scale' ) ? ' disabled' : '' %>><?php _e( 'Thumbs', 'happyforms' ); ?></option>
		</select>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'This is required', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
		<p class="happyforms-rating-labels-scale" style="display: <%= ( instance.rating_type == 'scale' && instance.rating_visuals == 'smileys' ) ? 'block' : 'none' %>">
			<label>
				<?php _e( 'Rating Labels', 'happyforms' ); ?>
				<% if ( instance.rating_labels_scale ) { %>
					<% _.each( instance.rating_labels_scale, function( label, index ) { %>
						<input type="text" class="widefat title happyforms-self-spaced-input rating-label" value="<%= label %>" data-attribute="rating_labels_scale" data-index="<%= index %>">
					<% }); %>
				<% } %>
			</label>
		</p>
		<p class="happyforms-rating-labels-yesno" style="display: <%= ( instance.rating_type == 'yesno' ) ? 'block' : 'none' %>">
			<label>
				<?php _e( 'Rating Labels', 'happyforms' ); ?>
				<% if ( instance.rating_labels_yesno ) { %>
					<% _.each( instance.rating_labels_yesno, function( label, index ) { %>
						<input type="text" class="widefat title happyforms-self-spaced-input rating-label" value="<%= label %>" data-attribute="rating_labels_yesno" data-index="<%= index %>">
					<% }); %>
				<% } %>
			</label>
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