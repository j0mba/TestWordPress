( function ( $, _, Backbone, api, settings ) {

	var PhoneModel = happyForms.classes.models.Part.extend( {
		defaults: function () {
			return _.extend(
				{},
				settings.formParts.phone.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var PhoneView = happyForms.classes.views.Part.extend( {
		template: '#happyforms-customize-phone-template',

		events: _.extend({}, happyForms.classes.views.Part.prototype.events, {
			'change [name=masked]': 'onMaskedChange',
		}),

		initialize: function () {
			happyForms.classes.views.Part.prototype.initialize.apply( this, arguments );

			this.listenTo( this.model, 'change:confirmation_field', this.onConfirmationChange );
			this.listenTo( this.model, 'change:confirmation_field_label', this.onConfirmationLabelChange );
			this.listenTo( this.model, 'change:mask_phone_country', this.refreshPhonePart );
			this.listenTo( this.model, 'change:mask_allow_all_countries', this.refreshPhonePart );
		},

		/**
		 * Toggle masked input configuration on `Mask this input` checkbox change.
		 *
		 * @since 1.0.0.
		 *
		 * @param {object} e JS event.
		 *
		 * @return void
		 */
		onMaskedChange: function (e) {
			var $input = $(e.target);
			var attribute = $input.data('bind');
			var $maskWrapper = $('.number-options--phone', this.$el);

			if ($input.is(':checked')) {
				this.model.set(attribute, 1);
				$maskWrapper.show();
			} else {
				this.model.set(attribute, 0);
				$maskWrapper.hide();
			}

			var model = this.model;

			this.model.fetchHtml( function ( response ) {
				var data = {
					id: model.get('id'),
					html: response,
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			} );
		},

		onConfirmationChange: function( e ) {
			$confirmationSettings = $( '.confirmation-field-setting', this.$el );

			if ( this.model.get( 'confirmation_field' ) ) {
				$confirmationSettings.show();
			} else {
				$confirmationSettings.hide();
			}

			var model = this.model;

			model.fetchHtml( function( response ) {
				var data = {
					id: model.get( 'id' ),
					html: response,
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			} );
		},

		/**
		* Send updated confirmation field label value to previewer.
		*
		* @since 1.0.0.
		*
		* @return void
		*/
		onConfirmationLabelChange: function () {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onPhoneConfirmationLabelChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		refreshPhonePart: function( model, value ) {
			model.fetchHtml(function (response) {
				var data = {
					id: model.get('id'),
					html: response
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			});
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onPhoneConfirmationAdd: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );

			$part.append( this.$( options.confirmationHTML ) );
		},

		onPhoneConfirmationRemove: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );

			this.$( '#' + part.id + '_confirmation', $part ).remove();
		},

		onPhoneConfirmationLabelChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $confirmationLabel = this.$( '.happyforms-part__label--confirmation .label', $part );

			$confirmationLabel.text( part.get('confirmation_field_label') );
		},

		onPhoneCountryChangeCallback: function( id, html, options, $ ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );

			$part.attr( 'data-country', part.get( 'mask_phone_country' ) );
			$.fn.happyFormPart.call( $part, 'reinit' );
		},
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function ( func, attrs, options, BaseClass ) {
		if ( 'phone' === attrs.type ) {
			BaseClass = PhoneModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function ( func, options, BaseClass ) {
		if ( 'phone' === options.type ) {
			BaseClass = PhoneView;
		}

		return func( options, BaseClass );
	} );

} )( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
