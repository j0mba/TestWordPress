( function( $ ) {
	// Dismissible notices
	$( '.happyforms-notice:not(.one-time)' ).on( 'click', '.notice-dismiss', function( e ) {
		e.preventDefault();

		var $target = $( e.target );
		var $parent = $target.parents( '.notice' ).first();
		var id = $parent.attr( 'id' ).replace( 'happyforms-notice-', '' );
		var nonce = $parent.data( 'nonce' );

		$.post( ajaxurl, {
				action: 'happyforms_hide_notice',
				nid: id,
				nonce: nonce
			}
		);
	} );

	// Messages edit screen
	$( 'form#post' ).on( 'click', '#happyforms-message-status-toggle', function( e ) {
		e.preventDefault();

		var $target = $( e.target );
		var $form = $( 'form#post' );
		var $field = $form.find( '[name="message-status"]' );

		if ( 1 == $field.val() ) {
			$field.val( 0 );
		} else {
			$field.val( 1 );
		}

		$form.submit();
	} );

	function getCurrentEditor() {
		var editor,
			hasTinymce = typeof tinymce !== 'undefined',
			hasQuicktags = typeof QTags !== 'undefined';

		if ( ! wpActiveEditor ) {
			if ( hasTinymce && tinymce.activeEditor ) {
				editor = tinymce.activeEditor;
				wpActiveEditor = editor.id;
			} else if ( ! hasQuicktags ) {
				return false;
			}
		} else if ( hasTinymce ) {
			editor = tinymce.get( wpActiveEditor );
		}

		return editor;
	}

	// Shortcode popup insert button
	$( '.happyforms-dialog__button' ).on( 'click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var selectedForm = $( '#happyforms-dialog-select' ).val();
		if ( ! selectedForm ) {
			return false;
		}

		var shortcode = '[happyforms id="' + selectedForm + '" /]';
		window.parent.send_to_editor( shortcode );
		$( '#happyforms-modal' ).dialog( 'close' );
		$( '#happyforms-dialog-select' ).val( '' );

		if ( editor = getCurrentEditor() ) {
			editor.focus();
		}
	} );

	// Shortcode popup
	$( document ).on( 'click', '.happyforms-editor-button', function( e ) {
		var title = $( e.currentTarget ).attr( 'data-title' );

		$('#happyforms-modal').dialog( {
			title: title,
			dialogClass: 'happyforms-dialog wp-dialog',
			draggable: false,
			width: 'auto',
			modal: true,
			resizable: false,
			closeOnEscape: true,
			position: {
				my: 'center',
				at: 'center',
				of: $(window)
			}
		} );
	} );
} )( jQuery );
