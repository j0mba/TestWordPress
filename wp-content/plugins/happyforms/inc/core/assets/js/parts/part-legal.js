(function ($, _, Backbone, api, settings) {

	var LegalModel = happyForms.classes.models.Part.extend( {
		defaults: function () {
			return _.extend(
				{},
				settings.formParts.legal.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var LegalView = happyForms.classes.views.Part.extend( {
		template: '#happyforms-customize-legal-template',
		editor: null,

		ready: function () {
			happyForms.classes.views.Part.prototype.ready.apply(this, arguments);

			this.listenTo(this, 'refresh', this.onRefresh);

			this.initEditor();
		},

		initEditor: function() {
			var $textarea = $('textarea', this.$el);
			var editorId = $textarea.attr('id');
			var editorSettings = {
				tinymce: {
					toolbar1: 'bold,italic,bullist,numlist,link,hr',
					setup: this.onEditorInit.bind(this)
				},
			};

			wp.editor.initialize(editorId, editorSettings);
		},

		removeEditor: function() {
			var $textarea = $('textarea', this.$el);
			var editorId = $textarea.attr('id');

			wp.editor.remove( editorId );
		},

		onRefresh: function () {
			this.removeEditor();
			this.initEditor();
		},

		/**
		 * Triggere previewer event on each `keyup` and `change` event in the WP editor.
		 *
		 * @since 1.0.0.
		 *
		 * @param {object} editor TinyMCE editor JS object.
		 *
		 * @return void
		 */
		onEditorInit: function ( editor ) {
			editor.on('keyup change', function () {
				this.model.set('legal_text', editor.getContent());

				var data = {
					id: this.model.get('id'),
					callback: 'onLegalTextChange',
				};

				happyForms.previewSend('happyforms-part-dom-update', data);
			}.bind( this ) );
		},

		/**
		 * Add a special treatment for removing WP editor when the part is removed.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		remove: function () {
			var $textarea = $('textarea', this.$el);
			var editorId = $textarea.attr('id');

			wp.editor.remove(editorId);

			happyForms.classes.views.Part.prototype.remove.apply(this, arguments);
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onLegalTextChange: function (id, html, options) {
			var part = this.getPartModel(id);
			var $part = this.getPartElement(html);

			this.$('.happyforms-part__el .label', $part).html(part.get('legal_text'));
		},
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function ( func, attrs, options, BaseClass ) {
		if ( 'legal' === attrs.type ) {
			BaseClass = LegalModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function ( func, options, BaseClass ) {
		if ( 'legal' === options.type ) {
			BaseClass = LegalView;
		}

		return func( options, BaseClass );
	} );

} )( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
