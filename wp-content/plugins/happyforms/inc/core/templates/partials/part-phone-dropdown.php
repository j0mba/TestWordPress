<?php if ( $this->is_masked( $part ) ) : ?>
	<div class="happyforms-part--phone__country-select happyforms-country-select<?php if ( $part['mask_allow_all_countries'] ) { ?> happyforms-country-select--multiple<?php } ?>">
		<div class="happyforms-country-select__selected-country">
			<a href="#" class="happyforms-country-select-trigger"></a>
			<?php
			$countries = happyforms_get_phone_countries();
			$current_country = 0;

			foreach( $countries as $code => $country ) {
				if ( intval( $code_value ) && $code_value === $country['code'] ) {
					$current_country = $country;
				} else if ( strtolower( $code ) === strtolower( $code_value ) ) {
					$current_country = $country;
				}
			}
			?>
			<div class="happyforms-flag"><?php echo $current_country['flag']; ?></div>
		</div>
		<?php if ( $part['mask_allow_all_countries'] ) { ?>
			<ul class="happyforms-custom-select-dropdown phone">
				<li>
					<input type="text" autocomplete="off" placeholder="Search countries..." class="happyforms-custom-select-dropdown__search">
				</li>
				<?php foreach( $countries as $key => $country ) { ?>
				<li tabindex="0" class="happyforms-custom-select-dropdown__item happyforms-custom-select-dropdown-item" data-country="<?php echo strtolower( $key ); ?>" data-search-string="<?php echo strtolower( $country['name'] ); ?>" data-code="<?php echo $country['code']; ?>">
					<div class="happyforms-flag <?php echo strtolower( $key ); ?>"><?php echo $country['flag']; ?></div>
					<span class="happyforms-custom-select-dropdown-item__label happyforms-custom-select-dropdown-item__label--country"><?php echo ucwords( strtolower( $country['name'] ) ); ?></span>
					<span class="happyforms-custom-select-dropdown-item__label happyforms-custom-select-dropdown-item__label--country-code">+<?php echo $country['code']; ?></span>
				</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
<?php endif; ?>