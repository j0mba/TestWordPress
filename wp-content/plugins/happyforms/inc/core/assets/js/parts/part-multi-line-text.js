( function( $, _, Backbone, api, settings ) {

	var MultiLineTextModel = happyForms.classes.models.Part.extend( {
		defaults: function() {
			return _.extend(
				{},
				settings.formParts.multi_line_text.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var MultiLineTextView = happyForms.classes.views.Part.extend( {
		template: '#customize-happyforms-multi-line-text-template',

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply(this, arguments);

			this.listenTo( this.model, 'change:placeholder', this.onPlaceholderChange )
		},

		/**
		 * Send updated placeholder value to previewer. Added as a special method because of 'textarea' selector used instead of 'input'.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onPlaceholderChange: function() {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onMultiLineTextPlaceholderChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onMultiLineTextPlaceholderChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );

			this.$( 'textarea', $part ).attr( 'placeholder', part.get( 'placeholder' ) );
		},
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'multi_line_text' === attrs.type ) {
			BaseClass = MultiLineTextModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function( func, options, BaseClass ) {
		if ( 'multi_line_text' === options.type ) {
			BaseClass = MultiLineTextView;
		}

		return func( options, BaseClass );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
