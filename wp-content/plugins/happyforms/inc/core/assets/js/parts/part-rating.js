( function ( $, _, Backbone, api, settings ) {

	var RatingModel = happyForms.classes.models.Part.extend({
		defaults: function () {
			return _.extend(
				{},
				settings.formParts.rating.defaults,
				_.result( happyForms.classes.models.Part.prototype, 'defaults' ),
			);
		},
	} );

	var RatingView = happyForms.classes.views.Part.extend( {
		template: '#happyforms-customize-rating-template',

		initialize: function() {
			happyForms.classes.views.Part.prototype.initialize.apply(this, arguments);

			this.listenTo( this.model, 'change:rating_type', this.onRatingTypeChange );
			this.listenTo( this.model, 'change:rating_visuals', this.onRatingVisualsChange );
		},

		events: _.extend( {}, happyForms.classes.views.Part.prototype.events, {
			'keyup input.rating-label': 'updateRatingLabels',
			'change input.rating-label': 'updateRatingLabels',
		} ),

		onRatingTypeChange: function( model, value ) {
			var $ratingVisualsDropdown = $( 'select[data-bind=rating_visuals]', this.$el );
			var newValue = $( 'option.' + value + '-default', $ratingVisualsDropdown ).attr( 'value' );

			$( '[data-allowed-for]', $ratingVisualsDropdown ).attr('disabled', 'disabled');
			$( '[data-allowed-for*=' + value + ']', $ratingVisualsDropdown ).removeAttr('disabled');

			if ( model.get( 'rating_visuals' ) !== newValue ) {
				$ratingVisualsDropdown.val( newValue ).trigger('change');
			} else {
				this.refreshRatingPart();
			}

			this.toggleRatingLabels();
		},

		onRatingVisualsChange: function() {
			this.toggleRatingLabels();
			this.refreshRatingPart();
		},

		toggleRatingLabels: function() {
			var ratingType = this.model.get( 'rating_type' );
			var ratingVisuals = this.model.get( 'rating_visuals' );
			var $scaleRatingLabels = $( '.happyforms-rating-labels-scale', this.$el );
			var $yesnoRatingLabels = $( '.happyforms-rating-labels-yesno', this.$el );

			if ( 'smileys' === ratingVisuals || 'thumbs' === ratingVisuals ) {
				switch( ratingType ) {
					case 'scale':
						$yesnoRatingLabels.hide();
						$scaleRatingLabels.show();
						break;
					case 'yesno':
						$scaleRatingLabels.hide();
						$yesnoRatingLabels.show();
						break;
				}
			} else {
				$scaleRatingLabels.hide();
				$yesnoRatingLabels.hide();
			}
		},

		refreshRatingPart: function () {
			var model = this.model;

			model.fetchHtml(function (response) {
				var data = {
					id: model.get('id'),
					html: response
				};

				happyForms.previewSend('happyforms-form-part-refresh', data);
			});
		},

		updateRatingLabels: function( e ) {
			var $input = $( e.target );
			var attribute = $input.data( 'attribute' );
			var labels = this.model.get( attribute );

			labels[$input.data('index')] = $input.val();

			this.model.set( attribute, labels ).trigger('change');

			var data = {
				id: this.model.id,
				callback: 'onRatingLabelUpdate',
				options: {
					attribute: attribute,
					index: $input.data('index')
				}
			};

			happyForms.previewSend('happyforms-part-dom-update', data );
		}
	} );

	happyForms.previewer = _.extend( happyForms.previewer, {
		onRatingLabelUpdate: function( id, html, options ) {
			var part = this.getPartModel( id );
			var $part = this.getPartElement( html );
			var $partWrap = $( '.happyforms-part__el', $part );

			$( 'label:eq('+ options.index + ') .happyforms-rating__item-label', $partWrap ).text( part.get( options.attribute )[options.index] );
		}
	} );

	happyForms.factory.model = _.wrap( happyForms.factory.model, function ( func, attrs, options, BaseClass ) {
		if ( 'rating' === attrs.type ) {
			BaseClass = RatingModel;
		}

		return func( attrs, options, BaseClass );
	} );

	happyForms.factory.view = _.wrap( happyForms.factory.view, function ( func, options, BaseClass ) {
		if ( 'rating' === options.type ) {
			BaseClass = RatingView;
		}

		return func( options, BaseClass );
	} );

} )( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
