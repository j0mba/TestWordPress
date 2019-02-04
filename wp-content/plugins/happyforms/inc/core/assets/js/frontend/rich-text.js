( function( $, editorSettings ) {

	HappyForms.parts = HappyForms.parts || {};

	window._wpLink = {};

	var defaultOnPostRender = function( editor, settings, e ) {
		e.target.$el.html( $( '#' + settings.icon ).html() );
	};

	var GetButton = function( editor, settings ) {
		var onPostRender = settings.onPostRender || defaultOnPostRender;

		var button = Object.assign( {}, settings, {
			onClick: settings.onClick.bind( this, editor ),
			onPostRender: onPostRender.bind( this, editor, settings ),
		} );

		return button;
	};

	var buttons = {
		hfbold: {
			icon: 'happyforms-rich-text-icon-bold',
			stateSelector: 'strong',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'bold' );
			},
		},
		hfitalic: {
			icon: 'happyforms-rich-text-icon-italic',
			stateSelector: 'em',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'italic' );
			},
		},
		hful: {
			icon: 'happyforms-rich-text-icon-ul',
			stateSelector: 'ul',

			onClick: function ( editor ) {
				editor.execCommand( 'InsertUnorderedList' );
			},
		},
		hfol: {
			icon: 'happyforms-rich-text-icon-ol',
			stateSelector: 'ol',

			onClick: function ( editor ) {
				editor.execCommand( 'InsertOrderedList' );
			},
		},
		hfquote: {
			icon: 'happyforms-rich-text-icon-quote',
			stateSelector: 'blockquote',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'blockquote' );
			},
		},
		hfcode: {
			icon: 'happyforms-rich-text-icon-code',
			stateSelector: 'pre',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'pre' );
			},
		},
		hfstrike: {
			icon: 'happyforms-rich-text-icon-strike',
			stateSelector: 'del',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'strikethrough' );
			},
		},
		hfunderline: {
			icon: 'happyforms-rich-text-icon-underline',

			onClick: function ( editor ) {
				editor.execCommand( 'mceToggleFormat', false, 'underline' );
			},

			onPostRender: function( editor, settings, e ) {
				defaultOnPostRender.apply( this, arguments );

				var self = e.target;

				editor.on( 'NodeChange', function( e ) {
					self.active( 'underline' === e.element.style.textDecoration );
				} );
			},
		},
		hfhr: {
			icon: 'happyforms-rich-text-icon-hr',

			onClick: function ( editor ) {
				editor.execCommand( 'InsertHorizontalRule' );
			},
		},
	};

	HappyForms.parts.rich_text = {
		init: function( options ) {
			this.$form = options.form;
			this.type = this.$el.data( 'happyforms-type' );
			this.$input = $( 'textarea', this.$el );
			this.editorId = this.$input.attr( 'id' );
			this.editor = null;

			this.$el.on( 'happyforms.attach', this.editorCreate.bind( this ) );
			this.$el.on( 'happyforms.detach', this.editorDestroy.bind( this ) );
			this.$el.on( 'happyforms.formclass', this.onFormClassChange.bind( this ) );
			this.$el.on( 'happyforms.cssvar', this.onCSSVarChange.bind( this ) );

			this.editorCreate();
		},

		editorCreate: function() {
			var self = this;

			wp.editor.initialize( this.editorId, {
				tinymce: {
					toolbar1: 'hfbold,hfitalic,hful,hfol,hfquote,link,unlink,hfcode,hfstrike,hfunderline,hfhr',
					statusbar: true,
					resize: true,
					plugins: 'wordpress,wplink,hr,paste',
					content_css: editorSettings.contentCSS,
					valid_elements: editorSettings.validElements,
					paste_as_text: true,

					setup: function( editor ) {
						var wpAddButton = editor.addButton;

						// Remove tooltips
						editor.addButton = function( id, options ) {
							switch ( id ) {
								case 'link':
								case 'unlink':
								case 'wp_link_apply':
								case 'wp_link_edit':
								case 'wp_link_remove':
									delete options.tooltip;
									break;
								default:
									break;
							}

							return wpAddButton.apply( this, arguments );
						}

						// Add custom buttons
						for ( var button in buttons ) {
							editor.addButton( button, GetButton( editor, buttons[button] ) );
						}

						// Custom popover toolbar icons,
						// class and border-color
						editor.on( 'preinit', function() {
							var _createToolbar = this.wp._createToolbar;

							this.wp._createToolbar = function( buttons, bottom ) {
								var index = buttons.indexOf( 'wp_link_advanced' );
								var isLinkToolbar = -1 < index;

								if ( isLinkToolbar ) {
									buttons.splice( index, 1 );
								}

								var toolbar = _createToolbar.apply( this, [ buttons, bottom ] );

								toolbar.$el.addClass( 'happyforms-editor-toolbar-link' );

								toolbar.on( 'show', function() {
									self.inheritCSSVars( editorSettings.cssVars, toolbar.$el[0] );
								} );

								$( 'i.dashicons-editor-break', toolbar.$el ).replaceWith(
									$( '#happyforms-rich-text-icon-return' ).html()
								);

								$( 'i.dashicons-edit', toolbar.$el ).replaceWith(
									$( '#happyforms-rich-text-icon-pencil' ).html()
								);

								$( 'i.dashicons-editor-unlink', toolbar.$el ).replaceWith(
									$( '#happyforms-rich-text-icon-unlink' ).html()
								);

								return toolbar;
							}
						} );

						// Inherit form class, fonts and css-vars
						editor.on( 'init', function() {
							self.editor = editor;

							self.inheritFormClass();
							self.inheritFonts();

							var target = self.editor.dom.doc.querySelector( 'body' );
							self.inheritCSSVars( editorSettings.cssVars, target );
						} );

						// Custom main toolbar icons
						editor.on( 'postrender', function() {
							var $el = $( editor.container );

							$( 'i.mce-i-link', $el ).replaceWith(
								$( '#happyforms-rich-text-icon-link' ).html()
							);

							$( 'i.mce-i-unlink', $el ).replaceWith(
								$( '#happyforms-rich-text-icon-unlink' ).html()
							);
						} );

						editor.on( 'change', self.onEditorChange.bind( self ) );
						editor.on( 'focus', self.onFocus.bind( self ) );
						editor.on( 'blur', self.onBlur.bind( self ) );
					},
				},
			} );
		},

		editorDestroy: function() {
			wp.editor.remove( this.editorId );
			self.editor = null;
		},

		onEditorChange: function() {
			this.triggerChange();
		},

		onFocus: function() {
			this.$el.addClass( 'focus' );
		},

		onBlur: function() {
			this.$el.removeClass( 'focus' );
		},

		inheritFormClass: function() {
			this.editor.dom.doc.documentElement.className = this.$form.attr( 'class' );
		},

		inheritCSSVars: function( vars, target ) {
			var styles = getComputedStyle( this.$form[0] );
			var self = this;

			vars.forEach( function( variable ) {
				var value = styles.getPropertyValue( variable );
				target.style.setProperty( variable, value );
			} );
		},

		inheritFonts: function() {
			var formStyle = getComputedStyle( this.$form[0] );
			var stylesheets = [].slice.call( document.styleSheets );
			var editorDocument = this.editor.dom.doc;

			// Append parent stylesheets to editor
			stylesheets
				.map( function( stylesheet ) {
					return stylesheet.ownerNode.cloneNode();
				} )
				.forEach( function( node ) {
					editorDocument.head.insertBefore( node, editorDocument.head.firstChild );
				} );

			// Apply font-family
			editorDocument.body.style.setProperty( 'font-family', formStyle.getPropertyValue( 'font-family' ) );
		},

		onFormClassChange: function( e, classes ) {
			if ( this.editor ) {
				this.editor.dom.doc.documentElement.className = classes;
			}
		},

		onCSSVarChange: function( e, variable ) {
			if ( this.editor ) {
				this.editor.dom.doc.querySelector( 'body' ).style.setProperty( variable.name, variable.value );

				$( '.happyforms-editor-toolbar-link' ).each( function() {
					this.style.setProperty( variable.name, variable.value );
				} );
			}
		},

		isFilled: function() {
			var content = wp.editor.getContent( this.editorId );
			return '' !== content;
		},

		serialize: function() {
			var serialized = [ {
				name: this.$input.attr( 'name' ),
				value: wp.editor.getContent( this.editorId ),
			} ];

			return serialized;
		},
	};

} )( jQuery, happyFormsRichTextSettings );
