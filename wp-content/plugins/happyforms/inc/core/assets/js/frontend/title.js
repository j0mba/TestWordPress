( function( $ ) {

	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.title = {
		init: function( options ) {
			this.type = this.$el.data( 'happyforms-type' );

			this.$input = $( '[data-serialize]', this.$el );
			var $visualInput = $( 'input[type="text"]', this.$el );
			var $select = $( '.happyforms-custom-select-dropdown', this.$el );

			$visualInput.happyFormsSelect( {
				$input: this.$input,
				$select: $select,
				searchable: false,
			});

			this.$input.on( 'blur', this.onBlur.bind(this) );
		},
	};

} )( jQuery );