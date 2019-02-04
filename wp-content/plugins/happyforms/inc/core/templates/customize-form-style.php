<?php
$controller = happyforms_get_form_controller();
$controls = happyforms_get_styles()->get_controls();
?>

<script type="text/template" id="happyforms-form-style-template">
	<div class="happyforms-stack-view happyforms-style-view">
		<ul class="happyforms-form-widgets happyforms-style-controls">
			<?php
			$f = 0;
			foreach ( $controls as $control ) :
				$type = $control['type'];
				$label = $control['label'];
				$class = esc_attr( "happyforms-{$type}-control" );
				$input_class = isset( $control['class'] ) ? esc_attr( $control['class'] ) : '';
				$name = isset( $control['field'] ) ? $control['field'] : '';
				$field = ( '' !== $name ) ? $controller->get_field( $control['field'] ) : '';
				$has_tooltip = ( isset( $control['tooltip'] ) && true === $control['tooltip'] );

				if ( 'divider' === $type ) : ?>

					<?php if ( $f > 0 ): ?></ul></li><?php endif; ?>
					<li class="customize-control control-section <?php echo $class; ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<h3 class="accordion-section-title"><?php echo $label; ?></h3>
					</li>

					<li class="happyforms-style-controls-group">
						<ul>
							<li class="panel-meta customize-info accordion-section">
								<button class="customize-panel-back" tabindex="0">
									<span class="screen-reader-text"><?php _e( 'Back', 'happyforms' ); ?></span>
								</button>
								<div class="accordion-section-title">
									<span class="preview-notice"><?php _e( 'You are customizing', 'happyforms' ); ?> <strong class="panel-title"><?php echo $label; ?></strong></span>
								</div>
							</li>

				<?php elseif ( 'checkbox' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<div class="customize-control-content">
							<label>
								<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $field['value']; ?>" data-attribute="<?php echo $name; ?>" <% if (<?php echo $name; ?>) { %>checked="checked"<% } %>> <?php echo $label; ?>
							</label>
						</div>
					</li>

				<?php elseif ( 'range' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>" data-variable="<?php echo $field['variable']; ?>" data-unit="<?php echo $field['unit']; ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<label class="customize-control-title" for="<?php echo $name; ?>"><?php echo $label; ?></label>
						<input type="number" name="<?php echo $name; ?>" id="<?php echo $name; ?>" min="<?php echo $field[ 'min' ]; ?>" max="<?php echo $field[ 'max' ]; ?>" step="<?php echo $field[ 'step' ]; ?>" value="<%= <?php echo $name; ?> %>" data-attribute="<?php echo $name; ?>">
						<?php if ( isset( $field['include_unit_switch'] ) ) : ?>
						<select name="<?php echo $name; ?>_unit" class="happyforms-unit-switch">
							<?php if ( is_array( $field['units'] ) ) :
								foreach ( $field['units'] as $unit ) : ?>
							<option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
							<?php endforeach; endif; ?>
						</select>
						<?php endif; ?>
						<div class="customize-control-content">
							<div class="happyforms-range-slider" data-slider-id="<?php echo $name; ?>"></div>
						</div>
					</li>

				<?php elseif ( 'buttonset' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>" data-variable="<?php echo ( isset( $field['variable'] ) ) ? esc_attr( $field['variable'] ) : ''; ?>" data-control-id="<?php echo $name; ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<div class="customize-control-content">
							<label class="customize-control-title" for="<?php echo $name; ?>"><?php echo $label; ?></label>
							<div class="happyforms-buttonset-container">
                                <?php foreach ( $field[ 'options' ] as $option_key => $option ) : ?>
                                <input type="radio" class="happyforms-buttonset" name="<?php echo $name; ?>" id="<?php echo $name; ?>_<?php echo esc_attr( $option_key ); ?>" value="<?php echo esc_attr( $option_key ); ?>" data-attribute="<?php echo $name; ?>" <?php echo ( isset( $field['target_control_class'] ) ) ? ' data-target-control-class="'. $field['target_control_class'] .'" ' : ''; ?><% if (<?php echo $name; ?> === '<?php echo esc_attr( $option_key ); ?>') { %>checked="checked"<% } %>>
                                <label for="<?php echo $name; ?>_<?php echo esc_attr( $option_key ); ?>">
                                    <span class="ui-button-text"></span><?php echo esc_attr( $option ); ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
						</div>
					</li>

				<?php elseif ( 'color' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>" data-variable="<?php echo $field['variable']; ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<label class="customize-control-title" for="<?php echo $name; ?>"><?php echo $label; ?></label>
						<div class="customize-control-content">
							<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="happyforms-color-input" data-attribute="<?php echo $name; ?>" value="<%= <?php echo $name; ?> %>" data-default="<?php echo $field['default']; ?>">
						</div>
					</li>

				<?php elseif ( 'text' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<label class="customize-control-title" for="<?php echo $name; ?>"><?php echo $label; ?> <?php if ( true === $has_tooltip ) : ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="<?php echo $name; ?>"></i><?php endif; ?></label>
						<div class="customize-control-content" <?php if ( true === $has_tooltip ) : ?>data-pointer-target<?php endif; ?>>
							<input type="text" name="<?php echo $name; ?>" class="<?php echo $input_class; ?>" id="<?php echo $name; ?>" data-attribute="<?php echo $name; ?>" value="<%= <?php echo $name; ?> %>">
						</div>
					</li>

				<?php elseif ( 'select' === $type ) : ?>

					<li class="customize-control <?php echo $class; ?> <?php echo ( isset( $field['extra_class'] ) ) ? $field['extra_class'] : ''; ?>" data-target="<?php echo esc_attr( $field['target'] ); ?>"<?php if ( isset( $control['condition'] ) ) { echo ' style="display: <%= ' . $control['condition'] . ' ? "block" : "none" %>"'; } ?>>
						<label class="customize-control-title" for="<?php echo $name; ?>"><?php echo $label; ?></label>
						<div class="customize-control-content">
							<select name="<?php echo $name; ?>" id="<?php echo $name; ?>" data-attribute="<?php echo $name; ?>" class="widefat">
                                <?php
                                foreach ( $field['options'] as $option_key => $option ) : ?>
                                    <option value="<?php echo esc_attr( $option_key ); ?>" <% if (<?php echo $name; ?> === '<?php echo esc_attr( $option_key ); ?>') {%><%= 'selected' %><% } %>><?php echo esc_attr( $option ); ?></option>
                                <?php endforeach; ?>
                            </select>
						</div>
					</li>

				<?php elseif ( 'heading' === $type ) : ?>

					<li class="customize-control happyforms-customize-heading">
						<h2><?php echo $label; ?></h2>
					</li>

				<?php endif; ?>
			<?php $f ++; endforeach; ?>
			</ul></li>
		</ul>
	</div>
</script>
<script type="text/template" id="happyforms-pointer-html_id">
<?php _e( 'Add a unique HTML ID to your form. Write without a hash (#) character.', 'happyforms' ); ?>
</script>