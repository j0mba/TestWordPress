( function( $, _, Backbone, api ) {

	HappyForms.parts = HappyForms.parts || {};

	var handlers = {};
	var $pencil, $form, $submit, $recaptcha;

	handlers.getPart = function( id ) {
		var $part = $( '[data-happyforms-id="' + id + '"]' );

		return $part;
	}

	handlers.formTitleUpdate = function( title ) {
		$( '.happyforms-form__title' ).text( title );
	}

	handlers.formDomUpdate = function( e ) {
		var context = parent.happyForms.previewer;
		var callback = context[e.callback];
		var options = e.options || {}

		if ( callback ) {
			callback.call( context, $form, options, $ );
		}
	}

	handlers.formPartAdd = function( html ) {
		$part = $( html );

		if ( $recaptcha.length ) {
			$recaptcha.before( $part );
		} else {
			$submit.before( $part );
		}

		$part.prepend( $( $pencil ) );
		HappyForms.wrapPart( $part, $form );
	}

	handlers.formPartRefresh = function( e ) {
		var $part = handlers.getPart( e.id );
		var $next = $part.next();
		var $refreshedPart = $( e.html );

		$part.trigger( 'happyforms.detach' );
		$part.remove();
		$next.before( $refreshedPart );
		$refreshedPart.prepend( $( $pencil ) );
		HappyForms.wrapPart( $refreshedPart, $form );
		$refreshedPart.trigger( 'happyforms.attach' );
	}

	handlers.formPartDisable = function( e ) {
		var $part = handlers.getPart( e.id );

		$part.addClass( 'unloading' );
	}

	handlers.formPartRemove = function( id ) {
		var $part = handlers.getPart( id );
		$part.trigger( 'happyforms.detach' );
		$part.remove();
	}

	handlers.partDomUpdate = function( e ) {
		var $part = handlers.getPart( e.id );
		var context = parent.happyForms.previewer;
		var callback = context[e.callback];
		var options = e.options || {}

		if ( callback ) {
			callback.call( context, e.id, $part, options, $ );
		}
	}

	handlers.formPartsSort = function( ids ) {
		var $parts = $.map( ids, function( id ) {
			var $part = handlers.getPart( id );

			$part.trigger( 'happyforms.detach' );
			$part.detach();

			return $part;
		} );

		$.each( $parts, function( i, $part ) {
			if ( $recaptcha.length ) {
				$recaptcha.before( $part );
			} else {
				$submit.before( $part );
			}

			$part.trigger( 'happyforms.attach' );
		} );
	}

	handlers.subPartAdded = function( e ) {
		var $part = handlers.getPart( e.id );
		var callback = parent.happyForms.onSubPartAdded;
		var options = {};

		callback.call( parent.happyForms, e.id, $part, e.html, options );
	}

	handlers.cssVariableUpdate = function( e ) {
		var formID = parent.happyForms.form.get( 'ID' );
		var $parts = $( '[data-happyforms-type]', $form );
		var variable = {
			name: e.variable,
			value: e.value,
		};

		document.querySelector( '#happyforms-' + formID + ' form' ).style.setProperty( e.variable, e.value );

		$.each( $parts, function( i, part ) {
			$( part ).trigger( 'happyforms.cssvar', variable );
		} );
	}

	handlers.formClassUpdate = function( e ) {
		var context = parent.happyForms.previewer;
		var callback = context[e.callback];
		var options = e.options || {}
		var $parts = $( '[data-happyforms-type]', $form );

		if ( callback ) {
			callback.call( context, e.attribute, $form, options );
		}
	}

	handlers.formClassUpdated = function( e ) {
		var $parts = $( '[data-happyforms-type]', $form );

		$.each( $parts, function( i, part ) {
			$( part ).trigger( 'happyforms.formclass', $form.attr( 'class' ) );
		} );
	}

	handlers.pencilPartClick = function( e ) {
		e.preventDefault();

		var id = $( e.target ).parents( '.happyforms-part' ).data( 'happyforms-id' );
		api.preview.send( 'happyforms-pencil-click-part', id );
	}

	handlers.pencilTitleClick = function( e ) {
		e.preventDefault();

		api.preview.send( 'happyforms-pencil-click-title' );
	}

	handlers.submitButtonTextUpdate = function ( text ) {
		$( '.happyforms-button--submit' ).attr( 'value', text );
	}

	handlers.recaptchaUpdate = function( e ) {
		var context = parent.happyForms.previewer;
		var callback = context[e.callback];
		var options = e.options || {}

		if ( callback ) {
			callback.call( context, $recaptcha, $ );
		}
	}

	handlers.silenceEvent = function( e ) {
		e.preventDefault();
	}

	handlers.init = function() {
		// Populate pointers
		$pencil = $( '#happyforms-pencil-template' ).html();
		$form = $( '.happyforms-form form' );
		$submit = $( '.happyforms-part.happyforms-part--submit', $form );
		$recaptcha = $( '.happyforms-part.happyforms-part--recaptcha', $form );

		// Append pencils to existing elements
		$( '.happyforms-block-editable' ).prepend( $( $pencil ) );

		// Remove unpreviewable
		$( '.happyforms-form form' ).removeClass( 'customize-unpreviewable' );
		$( '.notice a' ).removeClass( 'customize-unpreviewable' );
	}

	handlers.bind = function() {
		// Bind preview handlers
		api.preview.bind( 'happyforms-form-title-update', handlers.formTitleUpdate );
		api.preview.bind( 'happyforms-form-dom-update', handlers.formDomUpdate );
		api.preview.bind( 'happyforms-form-part-add', handlers.formPartAdd );
		api.preview.bind( 'happyforms-form-part-remove', handlers.formPartRemove );
		api.preview.bind( 'happyforms-form-parts-sort', handlers.formPartsSort );
		api.preview.bind( 'happyforms-form-part-refresh', handlers.formPartRefresh );
		api.preview.bind( 'happyforms-form-part-disable', handlers.formPartDisable );
		api.preview.bind( 'happyforms-part-dom-update', handlers.partDomUpdate );
		api.preview.bind( 'happyforms-css-variable-update', handlers.cssVariableUpdate );
		api.preview.bind( 'happyforms-form-class-update', handlers.formClassUpdate );
		api.preview.bind( 'happyforms-form-class-updated', handlers.formClassUpdated );
		api.preview.bind( 'happyforms-submit-button-text-update', handlers.submitButtonTextUpdate );
		api.preview.bind( 'happyforms-form-recaptcha-update', handlers.recaptchaUpdate )

		// Bind DOM handlers
		$( document.body ).on(
			'click',
			'.happyforms-block-editable--title .customize-partial-edit-shortcut',
			handlers.pencilTitleClick
		);

		$( document.body ).on(
			'click',
			'.happyforms-block-editable--part .customize-partial-edit-shortcut',
			handlers.pencilPartClick
		);

		$( '.happyforms-ask-link' ).on( 'click', function() {
			window.open( $( this ).attr( 'href' ) );
		} );

		// Silence unwanted events
		$( document.body ).on( 'click', 'button', handlers.silenceEvent );
		$( document.body ).on( 'click', 'input[type=submit]', handlers.silenceEvent );
		$( '.happyforms-form' ).on( 'submit', handlers.silenceEvent );
	}

	$( document ).ready( function() {
		handlers.init();
		handlers.bind();
		api.preview.send( 'happyforms-preview-ready' );
	} );

} )( jQuery, _, Backbone, wp.customize );