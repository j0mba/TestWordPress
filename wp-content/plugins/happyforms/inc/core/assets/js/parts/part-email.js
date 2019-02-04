( function( $, _, Backbone, api, settings ) {

	var EmailModel = happyForms.classes.models.Part.extend( {
		defaults: function() {
			return _.extend(
				{},
				settings.formParts.email.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var EmailView = happyForms.classes.views.Part.extend( {
		template: '#customize-happyforms-email-template',

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply(this, arguments);

			this.listenTo( this.model, 'change:confirmation_field', this.onConfirmationChange );
			this.listenTo( this.model, 'change:confirmation_field_label', this.onConfirmationLabelChange );
			this.listenTo( this.model, 'change:autocomplete_domains', this.onAutocompleteDomainsChange );
		},

		/**
		 * Trigger previewer event on 'Require confirmation of the value' checkbox change.
		 * Adds a new confirmation field to preview.
		 *
		 * @since 1.0.0.
		 *
		 * @param {object} e JS event.
		 *
		 * @return void
		 */
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
		onConfirmationLabelChange: function() {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onEmailConfirmationLabelChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		onAutocompleteDomainsChange: function() {
			var model = this.model;

			model.fetchHtml( function( response ) {
				var data = {
					id: model.get( 'id' ),
					html: response,
				};

				happyForms.previewSend( 'happyforms-form-part-refresh', data );
			} );
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onEmailConfirmationLabelChangeCallback: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $label = this.$( '.happyforms-part__label--confirmation .label', $part );

			$label.text( part.get( 'confirmation_field_label' ) );
		},
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'email' === attrs.type ) {
			BaseClass = EmailModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function( func, options, BaseClass ) {
		if ( 'email' === options.type ) {
			BaseClass = EmailView;
		}

		return func( options, BaseClass );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
