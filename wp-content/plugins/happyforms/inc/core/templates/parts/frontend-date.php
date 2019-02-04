<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php $current_timestamp = current_time( 'timestamp', false ); ?>
		<?php if ( 'inside' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>
			<?php
			if ( 'datetime' === $part['date_type'] || 'date' === $part['date_type'] ) {
				if ( 'month_first' === happyforms_get_site_date_format() ) {
					require( 'frontend-date-month.php' );
					require( 'frontend-date-day.php' );
				} else {
					require( 'frontend-date-day.php' );
					require( 'frontend-date-month.php' );
				}
			}

			if ( 'month_year' === $part['date_type'] || 'month' === $part['date_type'] ) {
				require( 'frontend-date-month.php' );
			}

			if ( 'time' !== $part['date_type'] && 'month' !== $part['date_type'] ) {
				$year_value = ( happyforms_get_part_value( $part, $form, 'year' ) ) ? happyforms_get_part_value( $part, $form, 'year' ) : '';

				if ( '' === $year_value && 'current' === $part['default_datetime'] ) {
					$year_value = date( 'Y', $current_timestamp );
				}
			?>
				<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap">
					<div class="happyforms-custom-select">
						<div class="happyforms-part__select-wrap">
							<?php $placeholder_text = __( 'Year', 'happyforms' ); ?>

							<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[year]" value="<?php echo $year_value; ?>" data-serialize />

							<input type="text" value="<?php echo $year_value; ?>" placeholder="<?php echo $placeholder_text; ?>" data-searchable="true" autocomplete="off" <?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />

							<?php
								$order = ( isset( $part['years_order'] ) ) ? $part['years_order'] : 'desc';
								$min_year = $part['min_year'];
								$max_year = ( $part['max_year'] > $min_year ) ? $part['max_year'] : date('Y');
								$options = array();

								if ( 'desc' === $order ) {
									for ( $i = $max_year; $i >= $min_year; $i-- ) {
										$options[] = array(
											'label' => $i,
											'value' => $i,
											'is_default' => ( intval( $year_value ) === $i )
										);
									}
								} else {
									for ( $i = $min_year; $i <= $max_year; $i++ ) {
										$options[] = array(
											'label' => $i,
											'value' => $i,
											'is_default' => ( intval( $year_value ) === $i )
										);
									}
								}
							?>

							<?php happyforms_select( $options, $part, $form, $placeholder_text ); ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( 'datetime' === $part['date_type'] || 'time' === $part['date_type'] ) : ?>
				<?php
				if ( 12 == $part['time_format'] ) {
					$hour_pattern = '(0[0-9]|1[0-2])';
					$hour_date_string = 'h';
				} else {
					$hour_pattern = '(0[0-9]|1[0-9]|2[0-3])';
					$hour_date_string = 'H';
				}

				$default_hour = sprintf( '%02d', intval( $part['min_hour'] ) );
				$happyforms_hour_value = ( happyforms_get_part_value( $part, $form, 'hour' ) ) ? happyforms_get_part_value( $part, $form, 'hour' ) : '';
				$hour_value = ( '' === $happyforms_hour_value && 'current' === $part['default_datetime'] ) ? date( $hour_date_string, $current_timestamp ) : $happyforms_hour_value;

				if ( '' === $hour_value ) {
					$hour_value = '00';
				}
				?>
				<div class="happyforms-part--date__input-wrap happyforms-part-date__time-input happyforms-part-date__time-input--hours">
					<input type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[hour]" min="<?php echo $part['min_hour']; ?>" max="<?php echo $part['max_hour']; ?>" maxlength="2" pattern="<?php echo $hour_pattern; ?>" autocomplete="off" value="<?php echo $hour_value; ?>"<?php if ( 1 == $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
					<span class="happyforms-spinner-arrow happyforms-spinner-arrow--up"></span>
					<span class="happyforms-spinner-arrow happyforms-spinner-arrow--down"></span>
				</div>
				<div class="happyforms-part--date__time-separator">
					<span>:</span>
				</div>
				<?php
				$happyforms_minute_value = ( happyforms_get_part_value( $part, $form, 'minute' ) ) ? happyforms_get_part_value( $part, $form, 'minute' ) : '';

				$minute_value = ( '' === $happyforms_minute_value && 'current' === $part['default_datetime'] ) ? date( 'i', $current_timestamp ) : $happyforms_minute_value;

				if ( '' === $minute_value ) {
					$minute_value = '00';
				}
				?>
				<div class="happyforms-part--date__input-wrap happyforms-part-date__time-input happyforms-part-date__time-input--minutes">
					<input type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[minute]" min="0" max="59" step="<?php echo $part['minute_step']; ?>" maxlength="2" pattern="([0-5][0-9])" autocomplete="off" value="<?php echo $minute_value; ?>"<?php if ( 1 == $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
					<span class="happyforms-spinner-arrow happyforms-spinner-arrow--up"></span>
					<span class="happyforms-spinner-arrow happyforms-spinner-arrow--down"></span>
				</div>
				<?php if ( 12 == intval( $part['time_format'] ) ) : ?>
				<?php
				$happyforms_period_value = ( happyforms_get_part_value( $part, $form, 'period' ) ) ? happyforms_get_part_value( $part, $form, 'period' ) : '';
				$period_value = ( 'current' === $part['default_datetime'] && '' === $happyforms_period_value ) ? date( 'A', $current_timestamp ) : 'AM';
				$period_value_label = ( 'AM' === $period_value ) ? __( 'AM', 'happyforms' ) : __( 'PM', 'happyforms' ); ?>
				<div class="happyforms-part--date__input-wrap happyforms-part-date__time-input happyforms-part-date__time-input--period">
					<div class="happyforms-custom-select" data-searchable="false">
						<div class="happyforms-part__select-wrap">
						<?php $placeholder_text = __( 'Period', 'happyforms' ); ?>

						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[period]" value="<?php echo $period_value; ?>" data-serialize />

						<input type="text" value="<?php echo $period_value_label; ?>" placeholder="<?php echo $placeholder_text; ?>" data-default-label="<?php echo $period_value_label; ?>" data-default-value="<?php echo $period_value_label; ?>"<?php if ( 1 === $part['required'] ) : ?> required aria-required="true"<?php endif; ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />

						<?php
						$options = array(
							array(
								'label' => __( 'AM', 'happyforms' ),
								'value' => 'AM',
								'is_default' => ( 'AM' === $period_value )
							),
							array(
								'label' => __( 'PM', 'happyforms' ),
								'value' => 'PM',
								'is_default' => ( 'PM' === $period_value )
							)
						);

						happyforms_select( $options, $part, $form, $placeholder_text );
						?>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_message_notices( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>