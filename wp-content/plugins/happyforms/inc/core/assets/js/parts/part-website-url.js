( function( $, _, Backbone, api, settings ) {

	var WebsiteUrlModel = happyForms.classes.models.Part.extend( {
		defaults: function() {
			return _.extend(
				{},
				settings.formParts.website_url.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var WebsiteUrlView = happyForms.classes.views.Part.extend( {
		template: '#customize-happyforms-website-url-template'
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'website_url' === attrs.type ) {
			BaseClass = WebsiteUrlModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function( func, options, BaseClass ) {
		if ( 'website_url' === options.type ) {
			BaseClass = WebsiteUrlView;
		}

		return func( options, BaseClass );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
