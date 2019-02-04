( function( $ ) {

	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.table = {
		isFilled: function() {
			var $rows = $( 'tbody tr', this.$el );

			var $filledRows = $rows.filter( function() {
				var $row = $( this );
				var $input = $( 'input:checked', $row );

				return $input.length > 0;
			} );

			return $rows.length === $filledRows.length;
		},
	};

} )( jQuery );
