( function ( $, _, Backbone, api, settings ) {

	var ScaleModel = happyForms.classes.models.Part.extend( {
		defaults: function () {
			return _.extend(
				{},
				settings.formParts.scale.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var ScaleView = happyForms.classes.views.Part.extend( {
		template: '#happyforms-customize-scale-template',

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply( this, arguments );

			this.listenTo( this.model, 'change:step', this.onStepChange );
			this.listenTo( this.model, 'change:min_label', this.onMinLabelChange );
			this.listenTo( this.model, 'change:max_label', this.onMaxLabelChange );
			this.listenTo( this.model, 'change:multiple', this.onMultipleChange );
		},

		events: function() {
			return _.extend({}, happyForms.classes.views.Part.prototype.events, {
				'change [data-bind=default_value]': 'onDefaultValueChange',
				'change [data-bind=min_value]': 'onMinValueChange',
				'change [data-bind=max_value]': 'onMaxValueChange',
				'change [data-bind=default_range_from]': 'onDefaultRangeFromChange',
				'change [data-bind=default_range_to]': 'onDefaultRangeToChange'
			});
		},

		onMinValueChange: function( e ) {
			var model = this.model;
			var value = $(e.target).val();

			model.set('min_value', value);

			if ( parseInt( value, 10 ) > parseInt( model.get( 'default_range_from' ), 10 ) ) {
				model.set( 'default_range_from', value );
			}

			$( '[data-bind=default_value]', this.$el ).val( model.get( 'default_value' ) );
			$( '[data-bind=default_range_from]', this.$el ).val( model.get( 'default_range_from' ) );

			this.refreshScalePart();
		},

		onMaxValueChange: function (e) {
			var model = this.model;
			var value = $(e.target).val();
			var intValue = parseInt(value, 10);

			if ( intValue < parseInt( model.get('default_value'), 10 ) ) {
				model.set('default_value', value);
			}

			if ( intValue < parseInt( model.get('default_range_to'), 10 ) ) {
				model.set('default_range_to', value );
			}

			$( '[data-bind=default_value]', this.$el ).val( model.get( 'default_value' ) );
			$( '[data-bind=default_range_to]', this.$el ).val( model.get( 'default_range_to' ) );

			this.refreshScalePart();
		},

		onDefaultValueChange: function( model, value ) {
			this.refreshScalePart();
		},

		onDefaultRangeFromChange: function( e ) {
			var value = $(e.target).val();
			var model = this.model;

			if ( parseInt( value, 10 ) < parseInt( model.get('min_value'), 10 ) ) {
				model.set('default_range_from', model.get('min_value'));
				$('[data-bind=default_range_from]', this.$el).val(model.get('min_value'));
			}
		},

		onDefaultRangeToChange: function( e ) {
			var value = $(e.target).val();
			var model = this.model;

			if (parseInt(value, 10) > parseInt(model.get('max_value'), 10)) {
				model.set('default_range_to', model.get('max_value'));
				$('[data-bind=default_range_to]', this.$el).val(model.get('max_value'));
			}
		},

		onStepChange: function( model, value ) {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onScaleStepChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		onMinLabelChange: function( model, value ) {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onScaleMinLabelChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		onMaxLabelChange: function ( model, value ) {
			var data = {
				id: this.model.get( 'id' ),
				callback: 'onScaleMaxLabelChange',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		onMultipleChange: function ( model, value ) {
			if ( 1 === value ) {
				this.$el.find('.scale-multiple-options').css({
					display: 'flex'
				});
				this.$el.find('.scale-single-options').hide();
			} else {
				this.$el.find('.scale-single-options').show();
				this.$el.find('.scale-multiple-options').hide();
			}

			this.refreshScalePart();
		},

		refreshScalePart: function() {
			var model = this.model;

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
		onScaleStepChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $input = this.$( 'input', $part );

			$input.attr( 'step', part.get( 'step' ) );
		},

		onScaleMinLabelChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $label = this.$( '.label-min', $part );

			$label.text( part.get( 'min_label' ) );
		},

		onScaleMaxLabelChange: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $label = this.$( '.label-max', $part );

			$label.text( part.get( 'max_label' ) );
		},
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function( func, attrs, options, BaseClass ) {
		if ( 'scale' === attrs.type ) {
			BaseClass = ScaleModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function ( func, options, BaseClass ) {
		if ( 'scale' === options.type ) {
			BaseClass = ScaleView;
		}

		return func( options, BaseClass );
	} );

	api.bind( 'ready', function () {
		api.previewer.bind( 'happyforms-part-render', function ( $el ) {
			if ( ! $el.is( '.happyforms-part--scale' ) ) {
				return;
			}

			$('input', $el).happyFormsScale();
		} );

		api.previewer.bind( 'happyforms-part-dom-updated', function ( $el ) {
			if ( ! $el.is( '.happyforms-part--scale' ) ) {
				return;
			}

			$( 'input', $el ).happyFormsScale();
		} );
	} );

} )( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
