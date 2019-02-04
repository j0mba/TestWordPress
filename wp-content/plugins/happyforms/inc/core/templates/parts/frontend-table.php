<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

		<div class="happyforms-part__el">
			<?php
			$columns = happyforms_get_part_options( $part['columns'], $part, $form );
			$columns_num = max( count( $columns ), 1 );
			?>
			<div class="happyforms-table">
				<div class="happyforms-table__row happyforms-table__row--head">
					<div class="happyforms-table__cell" style="width: <?php echo 100 / $columns_num; ?>%"></div>
					<?php
					foreach( $columns as $column ) : ?>
						<div class="happyforms-table__cell happyforms-table__cell--column-title" id="<?php echo esc_attr( $column['id'] ); ?>" style="width: <?php echo 100 / $columns_num; ?>%">
							<span><?php echo esc_attr( $column['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php
			$rows = happyforms_get_part_options( $part['rows'], $part, $form );

			foreach( $rows as $row ) : ?>
				<div class="happyforms-table__row happyforms-table__row--body" id="<?php echo esc_attr( $row['id'] ); ?>">
					<div class="happyforms-table__cell happyforms-table__cell--row-title" style="width: <?php echo 100 / $columns_num; ?>%">
						<span class="happyforms-table__row-label"><?php echo esc_attr( $row['label'] ); ?></span>
					</div>
					<?php foreach( $columns as $c => $column ) : ?>
						<?php $value = happyforms_get_part_value( $part, $form, $row['id'] ); ?>
						<div class="happyforms-table__cell" style="width: <?php echo 100 / $columns_num; ?>%">
							<div class="happyforms-table__cell--column-title happyforms-table__cell--column-title-sm"><?php echo esc_attr( $column['label'] ); ?></div>
							<label class="option-label">
							<?php if ( ! $part['allow_multiple_selection'] ) : ?>
								<?php $checked = ! empty( $column['label'] ) ? checked( $value, $c, false ) : ''; ?>
								<input type="radio" class="happyforms-visuallyhidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo esc_attr( $row['id'] ); ?>]" value="<?php echo $c; ?>" <?php echo $checked; ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
								<span class="checkmark"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="currentColor" d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/></svg></span>
								<span class="border"></span>
							<?php else: ?>
								<?php
								$value = happyforms_get_part_value( $part, $form, $row['id'], array() );
								$checked = in_array( $c, $value ) ? 'checked="checked"' : '';
								?>
								<input type="checkbox" class="happyforms-visuallyhidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo esc_attr( $row['id'] ); ?>][]" value="<?php echo $c; ?>" <?php echo $checked; ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
								<span class="checkmark"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="currentColor" d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/></svg></span>
							<?php endif; ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
			</div>

			<?php happyforms_print_part_description( $part ); ?>
			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>

		<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
	</div>
</div>