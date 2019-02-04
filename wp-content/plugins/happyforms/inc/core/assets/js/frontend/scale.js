( function( $ ) {
	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.scale = {
		init: function() {
			this.type = this.$el.data( 'happyforms-type' );

			if ( document.readyState != 'loading' ) {
				if ( typeof multirange !== undefined ) {
					multirange.init();
				}
			}

			if ( $( 'input', this.$el ).length > 1 ) {
				this.initRangeSlider();
			} else {
				this.initSingleSlider();
			}
		},

		initSingleSlider: function() {
			this.$input = $( 'input', this.$el );
			this.$output = $( 'output span', this.$el );

			this.$input.on( 'input change', this.refreshSingleOutput.bind(this) );
			this.$input.on( 'input change', this.updateTrackerColor.bind(this) );

			this.$input.trigger('input');
		},

		initRangeSlider: function() {
			this.$input = $( 'input:first', this.$el );
			this.$output = $( 'output:first span', this.$el );
			this.$ghostInput = $( 'input:last', this.$el );
			this.$ghostOutput = $( 'output:last span', this.$el );

			this.$ghostInput.on( 'input change', this.refreshMultiOutput.bind(this) );
			this.$input.on( 'input change', this.refreshMultiOutput.bind(this) );

			this.$input.trigger( 'input' );
		},

		refreshSingleOutput: function( e ) {
			var inputVal = this.$input.val();
			var outputPosition = this.getOutputPosition();

			this.$output.css( 'left', outputPosition + '%' ).text( inputVal );
		},

		refreshMultiOutput: function( e ) {
			var inputVal = this.$input.val().split(',');
			var outputPosition = this.getOutputPosition();

			this.$output.css( 'left', outputPosition['original'] + '%' ).text( inputVal[0] );
			this.$ghostOutput.css( 'left', outputPosition['ghost'] + '%' ).text( inputVal[1] );
		},

		getOutputPosition: function() {
			var value = this.$input.val();
			var min = this.$input.attr( 'min' ) ? this.$input.attr( 'min' ) : 0;
			var max = this.$input.attr( 'max' ) ? this.$input.attr( 'max' ) : 100;
			var data;

			if ( -1 !== value.indexOf( ',' ) ) {
				value = value.split( ',' );

				var originalOutputPosition = 100 * ( ( parseInt( value[0] ) - min ) / ( max - min ) );
				var ghostOutputPosition = 100 * ( ( parseInt(value[1] ) - min ) / ( max - min ) );

				originalOutputPosition = originalOutputPosition - originalOutputPosition * 0.025;
				ghostOutputPosition = ghostOutputPosition - ghostOutputPosition * 0.025;

				data = {
					'original': originalOutputPosition,
					'ghost': ghostOutputPosition
				};
			} else {
				var outputPosition = 100 * ( ( parseInt( value ) - min ) / ( max - min ) );
				var coefficient = 0.02;

				if ( outputPosition > 50 ) {
					coefficient = 0.025;
				}

				outputPosition -= outputPosition * coefficient;

				data = outputPosition;
			}

			return data;
   		},

		updateTrackerColor: function() {
			var value = this.$input.val();
			var min = this.$input.attr( 'min' ) ? this.$input.attr( 'min' ) : 0;
			var max = this.$input.attr( 'max' ) ? this.$input.attr( 'max' ) : 100;

			var percentValue = 100 * ( ( value - min ) / ( max - min ) );

			this.$input.css( {
				'background': 'linear-gradient(to right, var(--happyforms-color-part-value) ' + percentValue + '%, var(--happyforms-color-part-border) ' + percentValue + '%, var(--happyforms-color-part-border) 100%)'
			} );
		}
	};
} )( jQuery );
