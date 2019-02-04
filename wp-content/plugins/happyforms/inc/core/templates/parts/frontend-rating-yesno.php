<?php $rating_labels = $part['rating_labels_yesno']; ?>

<input class="happyforms-visuallyhidden" type="radio" value="0" id="<?php echo esc_attr( $part['id'] ); ?>_0" name="<?php happyforms_the_part_name( $part, $form ); ?>" checked <?php happyforms_the_part_attributes( $part, $form ); ?>>

<input class="happyforms-visuallyhidden" type="radio" value="1" id="<?php echo esc_attr( $part['id'] ); ?>_1" name="<?php happyforms_the_part_name( $part, $form ); ?>" <?php checked( happyforms_get_part_value( $part, $form ), 1 ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
<label class="happyforms-rating__label" for="<?php echo esc_attr( $part['id'] ); ?>_1">
    <span class="happyforms-rating__item-wrap">
        <?php echo $icons[0]; ?>
        <span class="happyforms-rating__item-label"><?php echo ( ! empty( $rating_labels[0] ) ) ? $rating_labels[0] : '<span class="happyforms-visuallyhidden">'. __( 'No', 'happyforms' ) .'</span>'; ?></span>
    </span>
</label>

<input class="happyforms-visuallyhidden" type="radio" value="2" id="<?php echo esc_attr( $part['id'] ); ?>_2" name="<?php happyforms_the_part_name( $part, $form ); ?>" <?php checked( happyforms_get_part_value( $part, $form ), 2 ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?> />
<label class="happyforms-rating__label" for="<?php echo esc_attr( $part['id'] ); ?>_2">
    <span class="happyforms-rating__item-wrap">
        <?php echo $icons[1]; ?>
        <span class="happyforms-rating__item-label"><?php echo ( ! empty( $rating_labels[1] ) ) ? $rating_labels[1] : '<span class="happyforms-visuallyhidden">'. __( 'Yes', 'happyforms' ) .'</span>'; ?></span>
    </span>
</label>