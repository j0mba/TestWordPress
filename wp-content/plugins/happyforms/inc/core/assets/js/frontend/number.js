( function( $ ) {

	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.number = {
		init: function() {
			this.type = this.$el.data( 'happyforms-type' );

			var masked = this.$el.attr( 'data-mask' );

			this.$input = $( 'input', this.$el );
			this.cleaveInstances = [];

			this.$input.on( 'keyup', this.triggerChange.bind( this ) );
			this.$input.on( 'change', this.triggerChange.bind( this ) );
			this.$input.on( 'blur', this.onBlur.bind( this ) );

			if ( masked ) {
				var numeralDecimalMark = this.$el.attr( 'data-decimal-mark' ) || '.';
				var delimiter = this.$el.attr( 'data-thousands-delimiter' ) || ',';
				var prefix = this.$el.attr( 'data-prefix' ) || '';
				var self = this;

				this.$input.each( function() {
					var $input = $( this );

					var cleave = new Cleave( $input, {
						numeral: true,
						numeralDecimalMark: numeralDecimalMark,
						delimiter: delimiter,
						prefix: prefix
					} );

					self.cleaveInstances.push( cleave );
				} );
			}

			this.onBlur();
		},

		reinit: function() {
			$.each( this.cleaveInstances, function( i, instance ) {
				var input = instance.element;
				var rawValue = instance.getRawValue();

				instance.destroy();

				input.value = rawValue;
			} );

			this.init();
		},
	};

} )( jQuery );