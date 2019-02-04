<script type="text/template" id="customize-happyforms-checkbox-template">
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
			<input type="checkbox" class="checkbox apply-all-check" value="" /> <?php _e( 'Apply to all parts', 'happyforms' ); ?>
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
	<div class="options">
		<ul class="option-list"></ul>
		<h3><?php _e( 'Options', 'happyforms' ); ?></h3>
		<p class="no-options"><?php _e( 'No options added yet. Add one by clicking <i>Add Option</i> below.', 'happyforms' ); ?></p>
	</div>
	<div class="options-import">
		<h3><?php _e( 'Options', 'happyforms' ); ?></h3>
		<textarea class="option-import-area" cols="30" rows="10" placeholder="<?php _e( 'Type or paste your options here, adding each on a new line.' ); ?>"></textarea>
	</div>
	<p class="links mode-manual">
		<a href="#" class="button add-option"><?php _e( 'Add option', 'happyforms' ); ?></a>
		<span class="centered">
			<a href="#" class="import-options"><?php _e( 'Or, bulk add options', 'happyforms' ); ?></a>
		</span>
	</p>
	<p class="links mode-import">
		<a href="#" class="button import-option"><?php _e( 'Add options', 'happyforms' ); ?></a>
		<span class="centered">
			<a href="#" class="add-options"><?php _e( 'Cancel', 'happyforms' ); ?></a>
		</span>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'This is required', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-part-advanced-settings-wrap">
		<p>
			<label for="<%= instance.id %>_display_type"><?php _e( 'Display options', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_display_type" name="display_type" data-bind="display_type" class="widefat">
				<option value="inline"<%= (instance.display_type == 'inline') ? ' selected' : '' %>><?php _e( 'Horizontal', 'happyforms' ); ?></option>
				<option value="block"<%= (instance.display_type == 'block') ? ' selected' : '' %>><?php _e( 'Vertical', 'happyforms' ); ?></option>
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
				<input type="checkbox" class="checkbox apply-all-check" value="" /> <?php _e( 'Apply to all parts', 'happyforms' ); ?>
			</label>
		</p>
		<p>
			<label for="<%= instance.id %>_css_class"><?php _e( 'Custom CSS class', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%= instance.css_class %>" data-bind="css_class" />
		</p>
	</div>
	<?php include( happyforms_get_include_folder() . '/core/templates/customize-form-part-footer.php' ); ?>
</script>
<script type="text/template" id="customize-happyforms-checkbox-item-template">
	<li data-option-id="<%= id %>">
		<div class="happyforms-part-item-body">
			<div class="happyforms-part-item-handle"></div>
			<label>
				<?php _e( 'Label', 'happyforms' ); ?>
				<input type="text" class="widefat" name="label" value="<%= label %>">
			</label>
			<div class="happyforms-part-item-advanced">
				<label class="happyforms-part-item-description">
					<?php _e( 'Description', 'happyforms' ); ?>
					<input type="text" class="widefat" name="description" value="<%= (typeof description !== 'undefined') ? description : '' %>" data-option-attribute="description">
				</label>
				<label>
					<input type="checkbox" name="is_default" value="1" <% if (is_default == 1) { %> checked="checked"<% } %>> <?php _e( 'Checked by default', 'happyforms' ); ?>
				</label>
			</div>
			<div class="option-actions">
				<a href="#" class="delete-option"><?php _e( 'Delete', 'happyforms' ); ?></a> |
				<a href="#" class="advanced-option"><?php _e( 'Advanced', 'happyforms' ); ?></a>
			</div>
		</div>
	</li>
</script>