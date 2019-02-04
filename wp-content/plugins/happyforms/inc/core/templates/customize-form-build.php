<script type="text/template" id="happyforms-form-build-template">
	<div class="happyforms-stack-view">
		<div class="customize-control">
			<label for="" class="customize-control-title"><?php _e( 'Form name', 'happyforms' ); ?></label>
			<input type="text" name="post_title" value="<%= post_title %>" id="happyforms-form-name">
		</div>

		<div class="customize-control">
			<label class="customize-control-title">
				<?php _e( 'Form Builder', 'happyforms' ); ?>
				<span class="happyforms-parts-expand-collapse-wrap">
					<a href="#" class="expand-collapse-all <%= ( parts.length > 0 ) ? 'collapse' : 'expand' %>" data-collapse-text="<?php _e( 'Collapse all', 'happyforms' ); ?>" data-expand-text="<?php _e( 'Expand all', 'happyforms' ); ?>"><%= ( parts.length > 0 ) ? '<?php _e( 'Expand all', 'happyforms' ); ?>' : '<?php _e( 'Collapse all', 'happyforms' ); ?>' %></a>
				</span>
			</label>
			<div class="happyforms-parts-placeholder">
				<p><b><?php _e( 'Ready to get started?', 'happyforms' ); ?></b></p>
				<p><?php _e( 'Click any part from the sidebar to add it to your new form. Then, drag parts into order.', 'happyforms' ); ?></p>
				<div class="happyforms-parts-placeholder__placeholder"></div>
				<div class="happyforms-parts-placeholder__placeholder"></div>
				<div class="happyforms-parts-placeholder__placeholder"></div>
			</div>
			<div class="happyforms-form-widgets"></div>
		</div>
	</div>
</script>
