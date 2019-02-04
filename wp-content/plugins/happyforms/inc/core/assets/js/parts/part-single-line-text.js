( function( $, _, Backbone, api, settings ) {

	var SingleLineTextModel = happyForms.classes.models.Part.extend( {
		defaults: function() {
			return _.extend(
				{},
				settings.formParts.single_line_text.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var SingleLineTextView = happyForms.classes.views.Part.extend( {
		template: '#customize-happyforms-single-line-text-template',

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply(this, arguments);

			this.listenTo( this.model, 'change:use_as_subject', this.onUseAsSubjectChange );
		},

		onUseAsSubjectChange: function( model, value ) {
			if ( 1 === value ) {
				var singleLineParts = happyForms.form.get( 'parts' ).where({ type: 'single_line_text' });

				_(singleLineParts).each(function( partModel ) {
					if ( partModel.id !== model.id ) {
						partModel.set( 'use_as_subject', 0 );
					}
				});
			} else {
				$( '[data-bind=use_as_subject]', this.$el ).removeAttr( 'checked' );
			}
		}
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'single_line_text' === attrs.type ) {
			BaseClass = SingleLineTextModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function( func, options, BaseClass ) {
		if ( 'single_line_text' === options.type ) {
			BaseClass = SingleLineTextView;
		}

		return func( options, BaseClass );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
