<input class="happyforms-visuallyhidden" type="radio" value="0" id="<?php echo esc_attr( $part['id'] ); ?>_0" name="<?php happyforms_the_part_name( $part, $form ); ?>" checked <?php happyforms_the_part_attributes( $part, $form ); ?>>

    <?php 
    $label_class = ( 'stars' === $part['rating_visuals'] ) ? 'happyforms-star__label' : '';
    $rating_labels = $part['rating_labels_scale'];
    
    for ( $i = 1; $i <= $part['stars_num']; $i++ ) {
    ?>
    <input class="happyforms-visuallyhidden" type="radio" value="<?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $part['id'] ); ?>_<?php echo esc_attr( $i ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" <?php checked( happyforms_get_part_value( $part, $form ), $i ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />
    <label class="<?php echo $label_class; ?>" for="<?php echo esc_attr( $part['id'] ); ?>_<?php echo esc_attr( $i ); ?>">
        <?php if ( 'stars' === $part['rating_visuals'] ) { ?>
            <span class="happyforms-visuallyhidden"><?php echo esc_attr( $i ); ?> <?php _e( 'Stars', 'happyforms' ); ?></span>
            <svg class="happyforms-star" viewBox="0 0 512 512" fill=""><path class="happyforms-star__star" d="M512 198.525l-176.89-25.704-79.11-160.291-79.108 160.291-176.892 25.704 128 124.769-30.216 176.176 158.216-83.179 158.216 83.179-30.217-176.176 128.001-124.769z"></path></svg>
        <?php } else { ?>
            <span class="happyforms-rating__item-wrap">
                <?php echo $icons[$i-1]; ?>
                <span class="happyforms-rating__item-label"><?php echo ( ! empty( $rating_labels[$i-1] ) ) ? $rating_labels[$i-1] : '<span class="happyforms-visuallyhidden">' . sprintf( __( '%d out of %d', 'happyforms' ), $i, $part['stars_num'] ) .'</span>'; ?></span>
            </span>
        <?php } ?>
    </label>
<?php } ?>