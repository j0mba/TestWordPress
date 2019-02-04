;( function ( $, settings ) {

	HappyForms.parts = HappyForms.parts || {};

	var sources = {
		get: function( mode, context ) {
			if ( 'autocomplete' === mode ) {
				return settings.actionAutocomplete;
			} else {
				return settings.countries;
			}
		}
	}

	HappyForms.parts.address = {
		init: function() {
			this.type = this.$el.data( 'happyforms-type' );

			this.$input = $( 'input', this.$el );
			this.$fullAddress = $( '.address-full', this.$el );
			this.$country = $( '.address-country', this.$el );
			this.$city = $( '.address-city', this.$el );
			this.mode = this.$el.attr('data-mode');

			// Validation
			this.$input.on( 'keyup', this.triggerChange.bind( this ) );
			this.$input.on( 'change', this.triggerChange.bind( this ) );
			this.$input.on( 'blur', this.onBlur.bind( this ) );

			if ( 'simple' !== this.mode ) {
				this.$input = $( '[data-serialize]', this.$el );
				var $visualInput = $( '.happyforms-part--address__autocomplete', this.$el );
				var $select = $( '.happyforms-custom-select-dropdown', this.$el );

				var autocompleteOptions = {
					delay: 500,
					source: sources.get( this.mode )
				};

				if ( 'autocomplete' === this.mode ) {
					autocompleteOptions.url = settings.url;
					autocompleteOptions.apiKey = this.$el.attr('data-google-apikey');
				}

				$visualInput.happyFormsSelect( {
					$input: this.$input,
					$select: $select,
					searchable: 'autocomplete',
					autocompleteOptions: autocompleteOptions
				});
			}

			// Geolocation
			this.$geolocation = $( '.address-geolocate', this.$el );

			if ( this.$geolocation.length ) {
				if ( navigator.geolocation ) {
					$( 'span', this.$geolocation ).text( this.$geolocation.data( 'idle' ) );
					this.$geolocation.click( this.geolocate.bind( this ) );
				} else {
					this.$geolocation.hide();
				}
			}

			this.onBlur();
		},

		geolocate: function( e ) {
			e.preventDefault();

			this.$geolocation.addClass( 'disabled' );
			$( 'span', this.$geolocation ).text( this.$geolocation.data( 'fetching' ) );
			navigator.geolocation.getCurrentPosition( this.geolocationCallback.bind( this ) );
		},

		geolocationCallback: function( position ) {
			var apiKey = this.$el.attr( 'data-google-apikey' );

			$.get( settings.url, {
				action: settings.actionGeocode,
				key: apiKey,
				latitude: position.coords.latitude,
				longitude: position.coords.longitude,
			}, this.applyGeolocationResults.bind( this ) );
		},

		getFullAddress: function( results ) {
			return results.formatted_address;
		},

		getCountry: function( results ) {
			if ( ! results.address_components ) {
				return '';
			}

			var country = results.address_components.filter( function( component ) {
				return component.types.indexOf( 'country' ) >= 0;
			} );

			country = country.length > 0 ? country[0].long_name : '';

			return country;
		},

		getCity: function( results ) {
			if ( ! results.address_components ) {
				return '';
			}

			var city = results.address_components.filter( function( component ) {
				return component.types.indexOf( 'locality' ) >= 0;
			} );

			city = city.length > 0 ? city[0].long_name : '';

			return city;
		},

		applyGeolocationResults: function( results ) {
			var fullAddress = this.getFullAddress( results );
			var country = this.getCountry( results );
			var city = this.getCity( results );

			this.$fullAddress.val( fullAddress ).trigger('change');
			this.$country.val( country ).trigger('change');
			this.$city.val( city ).trigger('change');

			$( 'span', this.$geolocation ).text( this.$geolocation.data( 'idle' ) );
			this.$geolocation.removeClass( 'disabled' );
		},

		serialize: function() {
			var serialized = $( 'input', this.$el ).map( function( i, input ) {
				var $input = $( input );

				return {
					name: $input.attr( 'name' ),
					value: $input.val(),
				}
			} ).toArray();

			return serialized;
		},
	}

} )( jQuery, _happyFormsAddressSettings );