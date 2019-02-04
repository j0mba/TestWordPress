( function( $, settings ) {

	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.phone = {
		init: function() {
			this.type = this.$el.data( 'happyforms-type' );
			this.$input = $( '.happyforms-part-phone-wrap > input', this.$el );
			this.masked = this.$el.attr( 'data-mask' );
			this.$countryCode = $( 'input.happyforms-phone-code', this.$el );
			this.$country = $( 'input.happyforms-phone-country', this.$el );
			this.prefix = '';

			this.$input.on( 'keyup', this.triggerChange.bind( this ) );
			this.$input.on( 'change', this.triggerChange.bind( this ) );
			this.$input.on( 'blur', this.onBlur.bind(this) );
			this.$input.on( 'focus', this.toggleCountryDropdownClass.bind(this) );
			this.$input.on( 'blur', this.toggleCountryDropdownClass.bind(this) );

			if ( this.masked ) {
				this.initCleave();
				this.initCountryDropdown();
			}

			this.onBlur();
		},

		reinit: function() {
			this.destroyCleave();
			this.init();
		},

		destroyCleave: function() {
			$.each(this.cleaveInstances, function (i, instance) {
				instance.destroy();
			});
		},

		initCleave: function() {
			var self = this;

			if ( this.masked ) {
				this.cleaveInstances = this.$input.not(':hidden').map(function (i, input) {
					var $input = $(input);
					var code = self.$countryCode.val();
					var rawValue = $input.val();

					this.prefix = code;
					
					var cleave = new Cleave($input, {
						phone: true,
						phoneRegionCode: settings.codes[code],
						prefix: '+' + this.prefix + ' ',
						rawValueTrimPrefix: true
					});

					var cleaveValue = '+' + code + rawValue;

					cleave.setRawValue( cleaveValue );
					
					return cleave;
				}.bind(this));
			}
		},

		initCountryDropdown: function() {
			var self = this;

			this.$input.not(':hidden').map(function (i, input) {
				var $input = $(input);

				if ( 'text' === $input.attr('type') ) {
					$input.countryDropdown = new HappyFormsCountryDropdown( $input.prev('.happyforms-country-select') );
					$.extend( $input.countryDropdown, HappyForms.countryDropdownMethods );
					$input.countryDropdown.inputObject = self;
					$input.countryDropdown.init();
				}
			});
		},

		isFilled: function() {
			var prefix = this.prefix;

			var filledInputs = this.$input.filter( function() {
				var value = $( this ).val().replace( prefix, '' ).trim();

				return '' !== value;
			} );

			return filledInputs.length > 0;
		},

		onBlur: function() {
			if ( '' !== this.prefix ) {
				return;
			}

			if ( this.$el.is( '.happyforms-part--label-as_placeholder' ) ) {
				if ( this.isFilled() ) {
					this.$el.addClass( 'happyforms-part--filled' );
				} else {
					this.$el.removeClass( 'happyforms-part--filled' );
				}
			}
		},

		serialize: function() {
			var self = this;

			var serialized = this.$input.map( function( i, input ) {
				var $input = $( input );
				var keyValue = {
					name: $input.attr( 'name' ),
					value: $input.val(),
				};

				if ( self.masked ) {
					self.cleaveInstances.map( function(i, instance) {
						if ( instance.element === input ) {
							keyValue.value = self.cleaveInstances[i].getRawValue();
						}
					});
				}

				return keyValue;
			} ).toArray();

			return serialized;
		},

		toggleCountryDropdownClass: function(e) {
			var $input = $(e.target);

			if ( 'focus' === e.type ) {
				$input.prev('div').addClass('focus');
			} else {
				$input.prev('div').removeClass('focus');
			}
		}
	};

	function HappyFormsCountryDropdown( $el ) {
		this.$el = $el;
	}

	HappyForms.countryDropdownMethods = {
		init: function() {
			this.$countryTrigger = $( '.happyforms-country-select-trigger', this.$el );
			this.$currentCountry = $( '.happyforms-country-select__selected-country', this.$el );
			this.$currentFlag = $( '.happyforms-country-select__selected-country .happyforms-flag', this.inputObject.$el );
			this.$countryDropdown = $( '.happyforms-custom-select-dropdown', this.$el );
			this.$countrySearchField = $( '.happyforms-custom-select-dropdown__search', this.$el );

			this.$countryTrigger.on( 'click', this.showCountryDropdown.bind(this) );
			this.$countrySearchField.on( 'keyup', this.searchCountries.bind(this) );

			$( '.happyforms-custom-select-dropdown__item', this.$countryDropdown ).on( 'click keyup', this.onCountrySelect.bind(this) );
			$( window ).on( 'click', this.maybeCloseCountryDropdown.bind(this) );
		},

		showCountryDropdown: function(e) {
			if ( 'undefined' !== typeof e ) {
				e.preventDefault();
				e.stopPropagation();
			}

			$( '.happyforms-custom-select-dropdown' ).hide();
			$( '.happyforms-country-select__selected-country' ).removeClass( 'open' );
			this.$currentCountry.addClass( 'open' );
			this.$countryDropdown.show();
		},

		hideCountryDropdown: function() {
			this.$currentCountry.removeClass('open');
			this.$countryDropdown.scrollTop(0);
			this.$countryDropdown.hide();

			this.$countrySearchField.val('');
			$( 'li', this.$countryDropdown ).show();
		},

		maybeCloseCountryDropdown: function (e) {
			if ( this.$countryDropdown.is(':visible') && -1 >= e.target.className.indexOf('happyforms-custom-select') ) {
				this.hideCountryDropdown();
			}
		},

		onCountrySelect: function( e ) {
			var $li;
			var $target = $(e.target);

			if ( 'click' === e.type ) {
				if ( ! $target.is('li') ) {
					$li = $(e.target).parent('li');
				} else {
					$li = $target;
				}
			}

			if ( 'keyup' === e.type ) {
				if ( 'Enter' !== e.key ) {
					return false;
				}

				$li = $(e.target);
			}

			var $flag = $( 'img', $li ).clone();

			this.inputObject.cleaveInstances.map(function(i, instance) {
				instance.setRawValue('');
			});
			
			this.$currentFlag.html($flag); // replace flag

			this.inputObject.$countryCode.val($li.attr('data-code'));
			this.inputObject.$country.val($li.attr('data-country'));

			// re-init cleave
			this.inputObject.destroyCleave();
			this.inputObject.initCleave();

			this.hideCountryDropdown();
		},

		searchCountries: function(e) {
			var searchString = this.$countrySearchField.val().toLowerCase();
			var $allItems = $( '.happyforms-custom-select-dropdown__item', this.$countryDropdown );

			if ( !searchString ) {
				$allItems.show();
			} else {
				$allItems.hide();
				$( '.happyforms-custom-select-dropdown__item[data-country*="'+ searchString +'"]', this.$countryDropdown).show();
				$( '.happyforms-custom-select-dropdown__item[data-search-string*="'+ searchString +'"]', this.$countryDropdown ).show();
			}
		}
	};

} )( jQuery, HappyFormsPhoneSettings );