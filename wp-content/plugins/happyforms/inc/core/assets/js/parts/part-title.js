(function ($, _, Backbone, api, settings) {

	var TitleModel = happyForms.classes.models.Part.extend({
		defaults: function () {
			return _.extend(
				{},
				settings.formParts.title.defaults,
				_.result(happyForms.classes.models.Part.prototype, 'defaults'),
			);
		},
	});

	var TitleView = happyForms.classes.views.Part.extend({
		template: '#happyforms-customize-title-template',

		initialize: function () {
			happyForms.classes.views.Part.prototype.initialize.apply(this, arguments);

			this.listenTo(this.model, 'change:required', this.onRequiredChange);
		},

		onRequiredChange: function( model, value ) {
			model.fetchHtml(function (response) {
				var data = {
				id: model.get('id'),
				html: response,
				};

				happyForms.previewSend('happyforms-form-part-refresh', data);
			});
		}
	});

	happyForms.factory.model = _.wrap(happyForms.factory.model, function (func, attrs, options, BaseClass) {
		if ('title' === attrs.type) {
			BaseClass = TitleModel;
		}

		return func(attrs, options, BaseClass);
	});

	happyForms.factory.view = _.wrap(happyForms.factory.view, function (func, options, BaseClass) {
		if ('title' === options.type) {
			BaseClass = TitleView;
		}

		return func(options, BaseClass);
	});

})(jQuery, _, Backbone, wp.customize, _happyFormsSettings);
