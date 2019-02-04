( function( $, _, Backbone, api, settings ) {

	var AddressModel = happyForms.classes.models.Part.extend( {
		defaults: function() {
			return _.extend(
				{},
				settings.formParts.address.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var AddressView = happyForms.classes.views.Part.extend( {
		template: '#happyforms-customize-address-template',

		events: _.extend( {}, happyForms.classes.views.Part.prototype.events, {
			'change [data-bind=apikey]': 'onApiKeyChange',
		} ),

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply( this, arguments );

			this.listenTo( this, 'ready', this.toggleApiKey );
			this.listenTo( this.model, 'change:mode', this.onModeChange );
			this.listenTo( this.model, 'change:has_geolocation', this.onGeolocationChange );
		},

		toggleApiKey: function() {
			if ( 'autocomplete' === this.model.get( 'mode' )
					|| this.model.get( 'has_geolocation' ) ) {
				$( '.address-apikey', this.$el ).show();
			} else {
				$( '.address-apikey', this.$el ).hide();
			}
		},

		onApiKeyChange: function( e ) {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onAddressApiKeyChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		onModeChange: function( model, value ) {
			this.toggleApiKey();

			var $labelPlacementSelect = this.$el.find('[data-bind=label_placement]');

			model.fetchHtml( function( response ) {
				var data = {
					id: model.get( 'id' ),
					html: response,
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			} );
		},

		onGeolocationChange: function( e ) {
			var self = this;
			var model = this.model;

			this.toggleApiKey();

			this.model.fetchHtml( function( response ) {
				var data = {
					id: model.get( 'id' ),
					html: response,
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			} );
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onAddressApiKeyChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );

			$part.attr( 'data-google-apikey', part.get( 'apikey' ) );
		},

		onAddressModeChangeCallback: function( id, html, options, $ ) {
			var part = this.getPartModel( id );
			var $part = $( html );

			$part.happyFormsAddressAutocomplete();
		}
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'address' === attrs.type ) {
			BaseClass = AddressModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function( func, options, BaseClass ) {
		if ( 'address' === options.type ) {
			BaseClass = AddressView;
		}

		return func( options, BaseClass );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
