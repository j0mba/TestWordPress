( function( $ ) {

	HappyForms.parts = HappyForms.parts || {};

	HappyForms.parts.recaptcha = {
		init: function() {
			this.type = this.$el.data( 'happyforms-type' );
			this.$input = $( '> div', this.$el );
			this.html = this.$el.html();
			this.checked = false;
			this.ready = false;
			this.widgetID = false;

			this.render();
		},

		onReady: function() {
			this.ready = true;
			this.render();
		},

		render: function() {
			if ( ! this.ready ) {
				grecaptcha.ready( this.onReady.bind( this ) );
				return;
			}

			var id = this.$input.attr( 'id' );
			var siteKey = this.$el.attr( 'data-sitekey' );
			var self = this;

			if ( ! siteKey ) {
				return;
			}

			if ( false !== this.widgetID ) {
				this.reset();
			}

			this.widgetID = grecaptcha.render( id, {
				sitekey: siteKey,

				callback: function( response ) {
					self.checked = true;

					self.triggerChange( {
						recaptcha: true,
						response: response
					} );
				},
			} );
		},

		reset: function() {
			this.$el.html( this.html );
			this.widgetID = false;
		},

		isFilled: function() {
			return this.checked;
		},

		isValid: function() {
			return this.checked;
		},

		serialize: function() {
			var serialized = [ {
				name: 'g-recaptcha-response',
				value: $( '[name=g-recaptcha-response]', this.$el ).val(),
			} ];

			return serialized;
		},
	};

} )( jQuery );
