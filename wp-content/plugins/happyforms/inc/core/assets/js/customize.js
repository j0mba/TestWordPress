( function( $, _, Backbone, api, settings ) {

	var happyForms;
	var classes = {};
	classes.models = {};
	classes.collections = {};
	classes.views = {};
	classes.routers = {};

	classes.models.Form = Backbone.Model.extend( {
		idAttribute: 'ID',
		defaults: settings.form,

		getPreviewUrl: function() {
			var previewUrl =
				settings.baseUrl +
				'?post_type=' + this.get( 'post_type' ) +
				'&p=' + this.id +
				'&preview=true';

			return previewUrl;
		},

		isNew: function() {
			return '0' === this.id;
		},

		initialize: function( attrs, options ) {
			Backbone.Model.prototype.initialize.apply( this, arguments );

			this.attributes.parts = new classes.collections.Parts( this.get( 'parts' ), options );
		},

		toJSON: function() {
			var json = Backbone.Model.prototype.toJSON.apply( this, arguments );
			json.parts = json.parts.toJSON();

			return json;
		},

		save: function( options ) {
			var self = this;
			options = options || {};

			var request = wp.ajax.post( 'happyforms-update-form', _.extend( {
				'happyforms-nonce': api.settings.nonce.happyforms,
				happyforms: 1,
				form_id: this.id,
				wp_customize: 'on',
			}, { form: this.toJSON() } ) );

			request.done( function( response ) {
				self.set( response.data );

				if ( self.isNew() ) {
					happyForms.updateFormID( response.ID );
				}

				if ( happyForms.previewLoaded ) {
					api.previewer.refresh();
				}

				if ( options.success ) {
					options.success( response );
				}
			} );

			request.fail( function( response ) {
				// noop
			} );
		}
	} );

	classes.models.Part = Backbone.Model.extend( {
		initialize: function( attributes ) {
			Backbone.Model.prototype.initialize.apply( this, arguments );

			if ( ! this.id ) {
				var id = happyForms.utils.uniqueId( this.get( 'type' ) + '_', this.collection );
				this.set( 'id', id );
			}
		},

		fetchHtml: function( success ) {
			var data = {
				action: 'happyforms-form-part-add',
				'happyforms-nonce': api.settings.nonce.happyforms,
				happyforms: 1,
				wp_customize: 'on',
				form_id: happyForms.form.id,
				part: this.toJSON(),
			};

			var request = $.ajax( ajaxurl, {
				type: 'post',
				dataType: 'html',
				data: data
			} );

			happyForms.previewSend( 'happyforms-form-part-disable', {
				id: this.get( 'id' ),
			} );

			request.done( success );
		}
	} );

	classes.collections.Parts = Backbone.Collection.extend( {
		model: function( attrs, options ) {
			var model = PartFactory.model( attrs, options );
			return model;
		}
	} );

	var PartFactory = {
		model: function( attrs, options, BaseClass ) {
			BaseClass = BaseClass || classes.models.Part;
			return new BaseClass( attrs, options );
		},

		view: function( attrs, BaseClass ) {
			BaseClass = BaseClass || classes.views.Part;
			return new BaseClass( attrs );
		},
	};

	HappyForms = Backbone.Router.extend( {
		routes: {
			'build': 'build',
			'setup': 'setup',
			'style': 'style',
		},

		steps: [ 'build', 'setup', 'style' ],
		currentRoute: 'build',
		form: false,
		previewLoaded: false,
		buffer: [],

		initialize: function( options ) {
			Backbone.Router.prototype.initialize( this, arguments );

			this.listenTo( this, 'route', this.onRoute );
		},

		start: function( options ) {
			this.parts = new Backbone.Collection();
			this.parts.reset( _( settings.formParts ).values() );
			this.form = new classes.models.Form( settings.form, { silent: true } );
			this.actions = new classes.views.Actions( { model: this.form } ).render();
			this.sidebar = new classes.views.Sidebar( { model: this.form } ).render();

			Backbone.history.start();
			api.previewer.previewUrl( this.form.getPreviewUrl() );
		},

		flushBuffer: function() {
			if ( this.buffer.length > 0 ) {
				_.each( this.buffer, function( entry ) {
					api.previewer.send( entry.event, entry.data );
				} );

				this.buffer = [];
			}
		},

		previewSend: function( event, data ) {
			if ( happyForms.previewer.ready ) {
				api.previewer.send( event, data );
			} else {
				happyForms.buffer.push( {
					event: event,
					data: data
				} );
			}
		},

		onRoute: function( segment ) {
			this.sidebar.steps.disable();

			var previousStepIndex = this.steps.indexOf( this.currentRoute ) + 1;
			var stepIndex = this.steps.indexOf( segment ) + 1;
			var direction = previousStepIndex < stepIndex ? 1: -1;
			var stepProgress = Math.round( stepIndex / ( this.steps.length ) * 100 );
			var childView;

			switch( segment ) {
				case 'setup':
					childView = new classes.views.FormSetup( { model: this.form } );
					break;
				case 'build':
					childView = new classes.views.FormBuild( { model: this.form } );
					break;
				case 'style':
					childView = new classes.views.FormStyle( { model: this.form } );
					break;
			}

			this.currentRoute = segment;

			this.sidebar.doStep( {
				step: {
					slug: segment,
					index: stepIndex,
					progress: stepProgress,
					count: this.steps.length,
				},
				direction: direction,
				child: childView,
			} );
		},

		forward: function() {
			var nextStepIndex = this.steps.indexOf( this.currentRoute ) + 1;
			nextStepIndex = Math.min( nextStepIndex, this.steps.length - 1 );
			var nextStep = this.steps[nextStepIndex];

			this.navigate( nextStep, { trigger: true } );
		},

		back: function() {
			var previousStepIndex = this.steps.indexOf( this.currentRoute ) - 1;
			previousStepIndex = Math.max( previousStepIndex, 0 );

			var previousStep = this.steps[previousStepIndex];
			this.navigate( previousStep, { trigger: true } );
		},

		updateFormID: function( id ) {
			var url = window.location.href.replace( /form_id=[\d+]/, 'form_id=' + id );
			window.location.href = url;
		},

		setup: function() {
			// noop
		},

		build: function() {
			// noop
		},

		style: function() {
			// noop
		},

	} );

	classes.views.Base = Backbone.View.extend( {
		events: {
			'mouseover [data-pointer]': 'onHelpMouseOver',
			'mouseout [data-pointer]': 'onHelpMouseOut',
		},

		pointers: {},

		initialize: function() {
			if ( this.template ) {
				this.template = _.template( $( this.template ).text() );
			}

			this.listenTo( this, 'ready', this.ready );

			// Capture and mute link clicks to avoid
			// hijacking Backbone router and breaking
			// Customizer navigation.
			this.delegate( 'click', '.happyforms-stack-view a:not(.external)', this.muteLink );
		},

		ready: function() {
			// Noop
		},

		muteLink: function( e ) {
			e.preventDefault();
		},

		setupHelpPointers: function() {
			var $helpTriggers = $( '[data-pointer]', this.$el );
			var self = this;

			$helpTriggers.each( function() {
				var $trigger = $( this );
				var pointerId = $trigger.data( 'pointer' );
				var $target = $trigger.parents( '.customize-control' ).find( '[data-pointer-target]' );
				var $pointerTemplate = $( '#happyforms-pointer-' + pointerId );

				var $pointer = $target.pointer( {
					pointerClass: 'wp-pointer happyforms-help-pointer',
					content: $pointerTemplate.html(),
					position: {
						edge: 'left',
						align: 'center',
					},
					open: function( e, ui ) {
						ui.pointer.css( 'margin-left', '-1px' );
					},
					close: function( e, ui ) {
						ui.pointer.css( 'margin-left', '0' );
					},
					buttons: function() {},
				} );

				self.pointers[pointerId] = $pointer;
			} );
		},

		onHelpMouseOver: function( e ) {
			var $target = $( e.target );
			var pointerId = $target.data( 'pointer' );
			var $pointer = this.pointers[pointerId];

			if ( $pointer ) {
				$pointer.pointer( 'open' );
			}
		},

		onHelpMouseOut: function( e ) {
			var $target = $( e.target );
			var pointerId = $target.data( 'pointer' );
			var $pointer = this.pointers[pointerId];

			if ( $pointer ) {
				$pointer.pointer( 'close' );
			}
		},

		unbindEvents: function() {
			// Unbind any listenTo handlers
			this.stopListening();
			// Unbind any delegated DOM handlers
			this.undelegateEvents()
			// Unbind any direct view handlers
			this.off();
		},

		remove: function() {
			this.unbindEvents();
			Backbone.View.prototype.remove.apply( this, arguments );
		},
	} );

	classes.views.Actions = classes.views.Base.extend( {
		el: '#customize-header-actions',
		template: '#happyforms-customize-header-actions',

		events: {
			'click #happyforms-save-button': 'onSaveClick',
			'click #happyforms-close-link': 'onCloseClick',
			'onbeforeunload': 'onWindowClose',
		},

		initialize: function() {
			classes.views.Base.prototype.initialize.apply( this, arguments );

			this.listenTo( this.model, 'change', this.onFormChange );
			$( window ).bind( 'beforeunload', this.onWindowClose.bind( this ) );
		},

		render: function() {
			this.$el.html( this.template( {} ) );

			if ( this.model.isNew() ) {
				this.enableSave();
			}

			return this;
		},

		enableSave: function() {
			var $saveButton = $( '#happyforms-save-button', this.$el );

			$saveButton.removeAttr( 'disabled' ).text( $saveButton.data( 'text-default' ) );
		},

		disableSave: function() {
			$( '#happyforms-save-button', this.$el ).attr( 'disabled', 'disabled' );
		},

		isDirty: function() {
			return $( '#happyforms-save-button', this.$el ).is( ':enabled' );
		},

		onFormChange: function() {
			this.enableSave();
		},

		onCloseClick: function( e ) {
			if ( this.isDirty() ) {
				var message = $( e.currentTarget ).data( 'message' );

				if ( ! confirm( message ) ) {
					e.preventDefault();
					e.stopPropagation();

					return false;
				} else {
					$( window ).unbind( 'beforeunload' );
				}
			}
		},

		onWindowClose: function() {
			if ( this.isDirty() ) {
				return '';
			}
		},

		onSaveClick: function( e ) {
			e.preventDefault();

			var self = this;

			this.disableSave();

			this.model.save({
				success: function() {
					var $saveButton = $( '#happyforms-save-button', this.$el );

					$saveButton.text( $saveButton.data('text-saved') );
				}
			});
		},
	} );

	classes.views.Sidebar = classes.views.Base.extend( {
		el: '.wp-full-overlay-sidebar-content',

		steps: null,
		current: null,
		previous: null,

		render: function( options ) {
			this.$el.empty();
			this.steps = new classes.views.Steps( { model: this.model } );

			return this;
		},

		doStep: function( options ) {
			var child = options.child.render();
			this.$el.append( child.$el );
			child.trigger( 'ready' );

			if ( this.current ) {
				this.previous = this.current;
				this.current = child;

				this.current.$el.css( 'left', options.direction > 0 ? '300px': '-300px' );
				this.current.$el.animate( {
					left: '0px',
				}, 200 );

				this.previous.$el.animate( {
					left: options.direction > 0 ? '-300px': '300px',
				}, 200, $.proxy( this.onStepComplete, this, options ) );
			} else {
				this.current = child;
				this.current.$el.css( 'left', '0px' );
				this.steps.render( options );
			}
		},

		onStepComplete: function( options ) {
			this.previous.remove();
			$( '.wp-full-overlay-sidebar-content' ).scrollTop( 0 );
			this.steps.render( options );
		}
	} );

	classes.views.Steps = classes.views.Base.extend( {
		el: '#customize-footer-actions',
		template: '#happyforms-form-steps-template',

		events: {
			'click .happyforms-step-previous': 'onPreviousStepClick',
			'click .happyforms-step-next': 'onNextStepClick',
			'click .happyforms-step-save': 'onSaveStepClick',
		},

		render: function( options ) {
			var data = _.extend( {}, options, { form: this.model.toJSON() } );
			this.$el.html( this.template( data ) );
			this.$el.show();
		},

		enable: function() {
			$( 'button', this.$el ).removeAttr( 'disabled' );
		},

		disable: function() {
			$( 'button', this.$el ).attr( 'disabled', 'disabled' );
		},

		onNextStepClick: function( e ) {
			e.preventDefault();
			happyForms.forward();
		},

		onPreviousStepClick: function( e ) {
			e.preventDefault();
			happyForms.back();
		},

		onSaveStepClick: function( e ) {
			e.preventDefault();

			this.model.save( {
				success: function() {
					happyForms.actions.disableSave();
					window.location.href = $( '#happyforms-close-link' ).attr( 'href' );
				}
			} );
		},

	} );

	classes.views.FormBuild = classes.views.Base.extend( {
		template: '#happyforms-form-build-template',

		events: {
			'keyup #happyforms-form-name': 'onNameChange',
			'change #happyforms-form-name': 'onNameChange',
			'click .expand-collapse-all': 'onExpandCollapseAllClick',
			'global-attribute-set': 'onSetGlobalAttribute',
			'global-attribute-unset': 'onUnsetGlobalAttribute',
		},

		drawer: null,

		globalAttributes: {},

		initialize: function() {
			classes.views.Base.prototype.initialize.apply( this, arguments );

			this.partViews = new Backbone.Collection();

			this.listenTo( happyForms, 'part-add', this.onPartAdd );
			this.listenTo( happyForms, 'part-duplicate', this.onPartDuplicate );
			this.listenTo( this.model.get( 'parts' ), 'add', this.onPartModelAdd );
			this.listenTo( this.model.get( 'parts' ), 'remove', this.onPartModelRemove );
			this.listenTo( this.model.get( 'parts' ), 'change', this.onPartModelChange );
			this.listenTo( this.model.get( 'parts' ), 'reset', this.onPartModelsSorted );
			this.listenTo( this.model.get( 'parts' ), 'widget-toggle', this.onWidgetToggle );
			this.listenTo( this.partViews, 'add', this.onPartViewAdd );
			this.listenTo( this.partViews, 'remove', this.onPartViewRemove );
			this.listenTo( this.partViews, 'reset', this.onPartViewsSorted );
			this.listenTo( this.partViews, 'add remove reset', this.onPartViewsChanged );
			this.listenTo( this, 'sort-stop', this.onPartSortStop );
		},

		render: function() {
			this.setElement( this.template( this.model.toJSON() ) );
			return this;
		},

		onWidgetToggle: function() {
			var $expandCollapseButton = $( '.expand-collapse-all', this.$el );

			if ( 0 === $( '.happyforms-widget-expanded', this.$el ).length ) {
				$expandCollapseButton.text($expandCollapseButton.data('expand-text')).removeClass('collapse').addClass('expand');
			}

			if ( 0 === $( '.happyforms-widget:not(.happyforms-widget-expanded)', this.$el ).length ) {
				$expandCollapseButton.text($expandCollapseButton.data('collapse-text')).removeClass('expand').addClass('collapse');
			}
		},

		ready: function() {
			this.model.get( 'parts' ).each( function( partModel ) {
				this.addViewPart( partModel );
			}, this );

			$( '.happyforms-form-widgets', this.$el ).sortable( {
				handle: '.happyforms-part-widget-top',

				stop: function ( e, ui ) {
					this.trigger( 'sort-stop', e, ui );
				}.bind( this ),
			} );

			this.drawer = new classes.views.PartsDrawer();
			$( '.wp-full-overlay' ).append( this.drawer.render().$el );
			$( 'body' ).addClass( 'adding-happyforms-parts' );

			this.drawer.$el.animate( {
				'left': '0px',
			}, 200 );

			$( '#happyforms-form-name', this.$el ).focus();
		},

		onNameChange: function( e ) {
			e.preventDefault();

			var value = $( e.target ).val();
			this.model.set( 'post_title', value );
			happyForms.previewSend( 'happyforms-form-title-update', value );
		},

		onExpandCollapseAllClick: function(e) {
			e.preventDefault();

			var $button = $(e.target);

			this.partViews.each(function (model) {
				if ($button.hasClass('expand')) {
					model.get('view').trigger('widget-expand');
				} else {
					model.get('view').trigger('widget-collapse');
				}
			});

			if ($button.hasClass('expand')) {
				$button.text($button.data('collapse-text')).removeClass('expand').addClass('collapse');
			} else {
				$button.text($button.data('expand-text')).removeClass('collapse').addClass('expand');
			}
		},

		showExpandCollapseButton: function() {
			this.$el.find('.expand-collapse-all').show();
		},

		hideExpandCollapseButton: function () {
			this.$el.find('.expand-collapse-all').hide();
		},

		onPartAdd: function( type, options ) {
			var partModel = PartFactory.model(
				{ type: type },
				{ collection: this.model.get( 'parts' ) },
			);

			this.model.get( 'parts' ).add( partModel, options );
			this.model.trigger( 'change', this.model );

			partModel.fetchHtml( function( response ) {
				happyForms.previewSend( 'happyforms-form-part-add', response );
			} );
		},

		onPartDuplicate: function( part, options ) {
			var attrs = part.toJSON();
			delete attrs.id;
			attrs.label += ' (Copy)';

			var duplicate = PartFactory.model(
				attrs,
				{ collection: this.model.get( 'parts' ) },
			);

			this.model.get( 'parts' ).add( duplicate, options );
			this.model.trigger( 'change', this.model );

			duplicate.fetchHtml( function( response ) {
				happyForms.previewSend( 'happyforms-form-part-add', response );
			} );
		},

		onPartModelAdd: function( partModel, partsCollection, options ) {
			this.addViewPart( partModel, options );

			for ( var attribute in this.globalAttributes ) {
				if ( partModel.has( attribute ) ) {
					partModel.set( attribute, this.globalAttributes[attribute] );
				}
			}
		},

		onPartModelRemove: function( partModel ) {
			this.model.trigger( 'change', this.model );

			var partViewModel = this.partViews.find( function( viewModel ) {
				return viewModel.get( 'view' ).model.id === partModel.id;
			}, this );

			this.partViews.remove( partViewModel );

			happyForms.previewSend( 'happyforms-form-part-remove', partModel.id );
		},

		onPartModelChange: function( partModel ) {
			this.model.trigger( 'change' );
		},

		onPartModelsSorted: function() {
			this.partViews.reset( _.map( this.model.get( 'parts' ).pluck( 'id' ), function( id ) {
				return this.partViews.get( id );
			}, this ) );
			this.model.trigger( 'change' );

			var ids = this.model.get( 'parts' ).pluck( 'id' );
			happyForms.previewSend( 'happyforms-form-parts-sort', ids );
		},

		addViewPart: function( partModel, options ) {
			var settings = happyForms.parts.findWhere( { type: partModel.get( 'type' ) } );

			var partView = PartFactory.view( _.extend( {
				type: settings.get( 'type' ),
				model: partModel,
				settings: settings,
			}, options ) );

			var partViewModel = new Backbone.Model( {
				id: partModel.id,
				view: partView,
			} );

			this.partViews.add( partViewModel, options );
		},

		onPartViewAdd: function( viewModel, collection, options ) {
			var partView = viewModel.get( 'view' );
			$( '.happyforms-form-widgets', this.$el ).append( partView.render().$el );
			partView.trigger( 'ready' );

			if ( options.scrollto ) {
				this.$el.parent().animate( {
					scrollTop: partView.$el.position().top
				}, 400 );
			}

			this.showExpandCollapseButton();
		},

		onPartViewRemove: function( viewModel ) {
			var partView = viewModel.get( 'view' );
			partView.remove();

			if (!this.partViews.length) {
				this.hideExpandCollapseButton();
			}
		},

		onPartSortStop: function( e, ui ) {
			var $sortable = $( '.happyforms-form-widgets', this.$el );
			var ids = $sortable.sortable( 'toArray', { attribute: 'data-part-id' } );

			this.model.get( 'parts' ).reset( _.map( ids, function( id ) {
				return this.model.get( 'parts' ).get( id );
			}, this ) );
		},

		onPartViewsSorted: function( partViews ) {
			var $stage = $( '.happyforms-form-widgets', this.$el );

			partViews.forEach( function( partViewModel ) {
				var partView = partViewModel.get( 'view' );
				var $partViewEl = partView.$el;
				$partViewEl.detach();
				$stage.append( $partViewEl );
				partView.trigger( 'refresh' );
			}, this );
		},

		onPartViewsChanged: function( partViews ) {
			if ( this.partViews.length > 0 ) {
				this.$el.addClass( 'has-parts' );
			} else {
				this.$el.removeClass( 'has-parts' );
			}
		},

		onSetGlobalAttribute: function( e, data ) {
			this.partViews
				.filter( function( viewModel ) {
					return viewModel.id !== data.id
				} )
				.forEach( function( viewModel ) {
					var view = viewModel.get( 'view' );
					$( '[data-apply-to="' + data.attribute + '"]', view.$el ).prop( 'checked', false );
					$( '[data-bind="' + data.attribute + '"]', view.$el ).val( data.value );
					view.model.set( data.attribute, data.value );
				} );

			this.globalAttributes[data.attribute] = data.value;
		},

		onUnsetGlobalAttribute: function( e, data ) {
			this.partViews
				.filter( function( viewModel ) {
					return viewModel.id !== data.id
				} )
				.forEach( function( viewModel ) {
					var view = viewModel.get( 'view' );
					var previous = view.model.previous( data.attribute );
					$( '[data-bind="' + data.attribute + '"]', view.$el ).val( previous );
					view.model.set( data.attribute, previous );
				} );

			delete this.globalAttributes[data.attribute];
		},

		remove: function() {
			while ( partView = this.partViews.first() ) {
				this.partViews.remove( partView );
			};

			var drawer = this.drawer;
			this.drawer.$el.animate( {
				'left': '-300px',
			}, 200, function() {
				drawer.remove();
			} );

			$( 'body' ).removeClass( 'adding-happyforms-parts' );

			classes.views.Base.prototype.remove.apply( this, arguments );
		},
	} );

	classes.views.PartsDrawer = classes.views.Base.extend( {
		template: '#happyforms-form-parts-drawer-template',

		events: {
			'click .happyforms-parts-list-item': 'onListItemClick',
			'keyup #part-search': 'onPartSearch',
		},

		render: function() {
			this.setElement( this.template( { parts: happyForms.parts.toJSON() } ) );
			return this;
		},

		onListItemClick: function( e ) {
			var type = $( e.currentTarget ).data( 'part-type' );
			happyForms.trigger( 'part-add', type, { expand: true } );
		},

		onPartSearch: function( e ) {
			var search = $( e.target ).val().toLowerCase();
			var $partEls = $( '.happyforms-parts-list-item', this.$el );

			if ( '' === search ) {
				$partEls.show();
			}

			var results = happyForms.parts.filter( function( part ) {
				var label = part.get( 'label' ).toLowerCase();
				var description = part.get( 'description' ).toLowerCase();

				return label.indexOf( search ) >= 0 || description.indexOf( search ) >= 0;
			} );

			$partEls.hide();

			results.forEach( function( part ) {
				$( '.happyforms-parts-list-item[data-part-type="' + part.get( 'type' ) + '"]', this.$el ).show();
			} );

		}
	} );

	classes.views.Part = classes.views.Base.extend( {
		$: $,

		events: {
			'click .happyforms-widget-action': 'onWidgetToggle',
			'click .happyforms-form-part-close': 'onWidgetToggle',
			'click .happyforms-form-part-remove': 'onPartRemoveClick',
			'click .happyforms-form-part-duplicate': 'onPartDuplicateClick',
			'keyup [data-bind]': 'onInputChange',
			'change [data-bind]': 'onInputChange',
			'mouseover': 'onMouseOver',
			'mouseout': 'onMouseOut',
			'click .apply-all-check': 'applyOptionGlobally',
			'click .happyforms-form-part-advanced-settings': 'onAdvancedSettingsClick'
		},

		initialize: function( options ) {
			classes.views.Base.prototype.initialize.apply( this, arguments );
			this.settings = options.settings;

			// listen to changes in common settings
			this.listenTo( this.model, 'change:label', this.onPartLabelChange );
			this.listenTo( this.model, 'change:width', this.onPartWidthChange );
			this.listenTo( this.model, 'change:required', this.onRequiredCheckboxChange );
			this.listenTo( this.model, 'change:placeholder', this.onPlaceholderChange );
			this.listenTo( this.model, 'change:description', this.onDescriptionChange );
			this.listenTo( this.model, 'change:description_mode', this.onDescriptionModeChange );
			this.listenTo( this.model, 'change:label_placement', this.onLabelPlacementChange );
			this.listenTo( this.model, 'change:css_class', this.onCSSClassChange );
			this.listenTo( this.model, 'change:focus_reveal_description', this.onFocusRevealDescriptionChange );

			this.listenTo( this, 'widget-expand', this.expand );
			this.listenTo( this, 'widget-collapse', this.collapse );

			if ( options.expand ) {
				this.listenTo( this, 'ready', this.expandToggle );
			}
		},

		render: function() {
			this.setElement( this.template( {
				settings: this.settings.toJSON(),
				instance: this.model.toJSON(),
			} ) );

			return this;
		},

		/**
		 * Trigger a previewer event on mouse over.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onMouseOver: function() {
			var data = {
				id: this.model.id,
				callback: 'onPartMouseOverCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Trigger a previewer event on mouse out.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onMouseOut: function() {
			var data = {
				id: this.model.id,
				callback: 'onPartMouseOutCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Send changed label value to previewer.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onPartLabelChange: function() {
			var data = {
				id: this.model.id,
				callback: 'onPartLabelChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Send data about changed part width to previewer.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onPartWidthChange: function( model, value, options ) {
			var data = {
				id: this.model.id,
				callback: 'onPartWidthChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Trigger a previewer event on change of the "This is a required field" checkbox.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onRequiredCheckboxChange: function() {
			var model = this.model;

			var data = {
				id: this.model.id,
				callback: 'onRequiredCheckboxChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Slide toggle part view in the customize pane.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		expandToggle: function() {
			var $el = this.$el;
			var self = this;

			$( '.happyforms-widget-content', this.$el ).slideToggle( function() {
				$el.toggleClass('happyforms-widget-expanded');

				self.model.collection.trigger('widget-toggle');
			} );
		},

		/**
		 * Expand part view.
		 *
		 * @since 1.1.0.
		 *
		 * @return void
		 */
		expand: function() {
			var $el = this.$el;

			$el.find('.happyforms-widget-content').slideDown(function() {
				$el.addClass('happyforms-widget-expanded');
			});
		},

		/**
		 * Collapse part view.
		 *
		 * @since 1.1.0.
		 *
		 * @return void
		 */
		collapse: function() {
			var $el = this.$el;

			$el.find('.happyforms-widget-content').slideUp(function () {
				$el.removeClass('happyforms-widget-expanded');
			});
		},

		/**
		 * Call expandToggle method on toggle indicator click or 'Close' button click of the part view in Customize pane.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onWidgetToggle: function( e ) {
			e.preventDefault();
			this.expandToggle();
		},

		/**
		 * Remove part model from collection on "Delete" button click.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onPartRemoveClick: function( e ) {
			e.preventDefault();

			this.model.collection.remove( this.model );
		},

		onPartDuplicateClick: function( e ) {
			e.preventDefault();

			happyForms.trigger( 'part-duplicate', this.model, {
				expand: true,
				scrollto: true,
			} );
		},

		/**
		 * Update model with the changed data. Triggered on change event of inputs in the part view.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onInputChange: function( e ) {
			var $el = $( e.target );
			var value = $el.val();
			var attribute = $el.data( 'bind' );

			if ( 'label' === attribute ) {
				var $inWidgetTitle = this.$el.find('.in-widget-title');
				$inWidgetTitle.find('span').text(value);

				if ( value ) {
					$inWidgetTitle.show();
				} else {
					$inWidgetTitle.hide();
				}
			}

			if ( $el.is(':checkbox') ) {
				if ( $el.is(':checked') ) {
					value = 1;
				} else {
					value = 0;
				}
			}

			this.model.set( attribute, value );
		},

		/**
		 * Send changed placeholder value to previewer.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onPlaceholderChange: function() {
			var data = {
				id: this.model.id,
				callback: 'onPlaceholderChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Send changed description value to previewer.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onDescriptionChange: function( model, value ) {
			var data = {
				id: this.model.id,
				callback: 'onDescriptionChangeCallback',
			};

			if ( value ) {
				this.showDescriptionOptions();
			} else {
				model.set('tooltip_description', 0);
				this.hideDescriptionOptions();
			}

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Trigger a previewer event on tooltip description checkbox change.
		 *
		 * @since 1.1.0.
		 *
		 * @return void
		 */
		onDescriptionModeChange: function( model, value ) {
			var data = {
				id: model.id,
				callback: 'onDescriptionModeChangeCallback',
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		/**
		 * Send data about changed label placement value to previewer.
		 *
		 * @since 1.0.0.
		 *
		 * @return void
		 */
		onLabelPlacementChange: function( model, value, options ) {
			var $select = $( '[data-bind=label_placement]', this.$el );

			if ( $('option[value='+value+']', $select).length > 0 ) {
				$select.val( value );

				if ( ! options.skipGlobalReveal ) {
					var $globalWrap = this.$( '.label_placement-options', this.$el );
					// reset global checkbox
					this.$( 'input', $globalWrap ).prop( 'checked', false );
					// fade in the global checkbox wrapper
					$globalWrap.fadeIn();
				}

				if ( 'as_placeholder' === value ) {
					$( '.happyforms-placeholder-option', this.$el ).hide();
				} else {
					$( '.happyforms-placeholder-option', this.$el ).show();
				}

				model.fetchHtml( function( response ) {
					var data = {
						id: model.get( 'id' ),
						html: response,
					};

					happyForms.previewSend( 'happyforms-form-part-refresh', data );
				} );
			} else {
				model.set('label_placement', model.previous('label_placement'), { silent: true });
			}
		},

		applyOptionGlobally: function( e ) {
			var $input = $( e.target );
			var attribute = $input.attr( 'data-apply-to' );

			if ( $input.is( ':checked' ) ) {
				this.$el.trigger( 'global-attribute-set', {
					id: this.model.id,
					attribute: attribute,
					value: this.model.get( attribute ),
				} );
			} else {
				this.$el.trigger( 'global-attribute-unset', {
					id: this.model.id,
					attribute: attribute,
				} );
			}
		},

		onCSSClassChange: function( model, value, options ) {
			var data = {
				id: this.model.id,
				callback: 'onCSSClassChangeCallback',
				options: options,
			};

			happyForms.previewSend( 'happyforms-part-dom-update', data );
		},

		showDescriptionOptions: function() {
			this.$el.find('.happyforms-description-options').fadeIn();
		},

		hideDescriptionOptions: function() {
			var $descriptionOptionsWrap = this.$el.find('.happyforms-description-options');

			$descriptionOptionsWrap.fadeOut(200, function() {
				$descriptionOptionsWrap.find('input').prop('checked', false);
			});
		},

		onFocusRevealDescriptionChange: function( model, value ) {
			if ( 1 == value && 1 == model.get( 'tooltip_description' ) ) {
				$( '[data-bind=tooltip_description]', this.$el ).prop('checked', false ).trigger('change');
			}

			var data = {
				id: this.model.id,
				callback: 'onFocusRevealDescriptionCallback',
			};

			happyForms.previewSend('happyforms-part-dom-update', data);
		},

		onAdvancedSettingsClick: function( e ) {
			$( '.happyforms-part-advanced-settings-wrap', this.$el ).slideToggle( 300, function() {
				$( e.target ).toggleClass( 'opened' );
			} );
		}
	} );

	classes.views.FormSetup = classes.views.Base.extend( {
		template: '#happyforms-form-setup-template',

		events: _.extend( {}, classes.views.Base.prototype.events, {
			'keyup [data-attribute]': 'onInputChange',
			'change [data-attribute]': 'onInputChange',
		} ),

		pointers: {},

		editorIds: [
			'happyforms-confirmation-message',
			'happyforms-confirmation-email-content'
		],

		initialize: function() {
			classes.views.Base.prototype.initialize.apply( this, arguments );

			this.listenTo( this.model, 'change:receive_email_alerts', this.onReceiveEmailAlertsChange );
			this.listenTo( this.model, 'change:send_confirmation_email', this.onSendEmailConfirmationChange );
			this.listenTo( this.model, 'change:captcha', this.onChangeCaptcha );
			this.listenTo( this.model, 'change:captcha_site_key', this.onChangeCaptchaKey );
			this.listenTo( this.model, 'change:captcha_secret_key', this.onChangeCaptchaKey );
			this.listenTo( this.model, 'change:submit_button_label', this.onSubmitButtonLabelChange );
			this.listenTo( this.model, 'change:preview_before_submit', this.onPreviewBeforeSubmitChange );
			this.listenTo( this.model, 'change:unique_id', this.onUniqueIDChange );
		},

		render: function() {
			this.setElement( this.template( this.model.toJSON() ) );
			return this;
		},

		ready: function() {
			this.setupHelpPointers();

			var editorSettings = {
				tinymce: {
					toolbar1: 'bold,italic,bullist,numlist,link,hr',
					setup: this.onEditorInit.bind( this ),
					content_style: 'body { font-family: sans-serif; }'
				},
			};

			_.each( this.editorIds, function( editorId ) {
				var $textarea = $( '#' + editorId, this.$el );
				wp.editor.initialize( editorId, editorSettings );
			} );

			this.onChangeCaptcha();
		},

		onEditorInit: function( editor ) {
			var $textarea = $( '#' + editor.id, this.$el );
			var attribute = $textarea.data( 'attribute' );
			var self = this;

			editor.on( 'keyup change', function() {
				self.model.set( attribute, editor.getContent() );
			} );
		},

		onInputChange: function( e ) {
			e.preventDefault();

			var $el = $( e.target );
			var attribute = $el.data( 'attribute' );
			var value = $el.val();

			if ( $el.is( ':checkbox' ) ) {
				value = $el.is( ':checked' ) ? value: 0;
			}

			this.model.set( attribute, value );
		},

		onReceiveEmailAlertsChange: function( model, value ) {
			var $alertEmailSettings = $( '#happyforms-alert-email-settings', this.$el );

			if ( value ) {
				$alertEmailSettings.show();
			} else {
				$alertEmailSettings.hide();
			}
		},

		onSendEmailConfirmationChange: function( model, value ) {
			var $confirmationEmailSettings = $( '#happyforms-confirmation-email-settings', this.$el );

			if ( value ) {
				$confirmationEmailSettings.show();
			} else {
				$confirmationEmailSettings.hide();
			}
		},

		onChangeCaptcha: function() {
			var $captchaSettings = $( '#happyforms-captcha-settings', this.$el );

			if ( this.model.get( 'captcha' ) ) {
				$captchaSettings.show();
			} else {
				$captchaSettings.hide();
			}

			this.onChangeCaptchaKey();
		},

		onChangeCaptchaKey: function() {
			var data = {
				callback: 'onRecaptchaUpdate',
			};

			happyForms.previewSend( 'happyforms-form-recaptcha-update', data );
		},

		onSubmitButtonLabelChange: function( model, value ) {
			happyForms.previewSend( 'happyforms-submit-button-text-update', value );
		},

		onPreviewBeforeSubmitChange: function() {
			var $reviewButtonLabelSettings = $( '#happyforms-review-button-label-settings', this.$el );

			if ( this.model.get( 'preview_before_submit' ) ) {
				$reviewButtonLabelSettings.show();
			} else {
				$reviewButtonLabelSettings.hide();
			}
		},

		onUniqueIDChange: function() {
			var $uniqueIDSettings = $( '#happyforms-unique-id-settings', this.$el );

			if ( this.model.get( 'unique_id' ) ) {
				$uniqueIDSettings.show();
			} else {
				$uniqueIDSettings.hide();
			}
		},

		remove: function() {
			_.each( this.editorIds, function( editorId ) {
				wp.editor.remove( editorId );
			} );

			classes.views.Base.prototype.remove.apply( this, arguments );
		}
	} );

	classes.views.FormStyle = classes.views.Base.extend( {
		template: '#happyforms-form-style-template',

		events: _.extend( {}, classes.views.Base.prototype.events, {
			'click h3.accordion-section-title': 'onGroupClick',
			'click .customize-panel-back': 'onGroupBackClick',
			'change [data-target="form_class"] input': 'onFormClassChange',
			'change [data-target="form_class"] select': 'onFormClassChange',
			'change [data-target="form_class"] input[type="checkbox"]': 'onFormClassCheckboxChange',
			'change [data-target="css_var"] input[type=radio]': 'onButtonSetCssVarChange',
			'keyup [data-target="attribute"] input[type=text]': 'onAttributeChange',
			'keyup input[data-attribute="optional_part_label"]': 'onOptionalPartLabelChange',
		} ),

		pointers: {},

		initialize: function() {
			classes.views.Base.prototype.initialize.apply( this, arguments );

			this.styles = new Backbone.Collection();
		},

		render: function() {
			this.setElement( this.template( this.model.toJSON() ) );
			return this;
		},

		ready: function() {
			this.initColorPickers();
			this.initUISliders();
			this.initButtonSet();
			this.initFormWidthSlider();
			this.setupHelpPointers();
		},

		onFormClassChange: function( e ) {
			e.preventDefault();

			var $target = $( e.target );
			var attribute = $target.data( 'attribute' );
			var value = $target.val();

			happyForms.form.set( attribute, value );

			var data = {
				attribute: attribute,
				callback: 'onFormClassChangeCallback',
			};

			happyForms.previewSend( 'happyforms-form-class-update', data );
		},

		onFormClassCheckboxChange: function( e ) {
			e.preventDefault();

			var $target = $( e.target );
			var attribute = $target.data( 'attribute' );
			var value = $target.val();

			if ( $target.is(':checked') ) {
				happyForms.form.set( attribute, value );
			} else {
				happyForms.form.set( attribute, '' );
			}

			var data = {
				attribute: attribute,
				callback: 'onFormClassToggleCallback'
			};

			happyForms.previewSend( 'happyforms-form-class-update', data );
		},

		onButtonSetCssVarChange: function( e ) {
			e.preventDefault();

			var $target = $( e.target );
			var attribute = $target.data( 'attribute' );
			var variable = $target.parents( '.happyforms-buttonset-control' ).data( 'variable' );

			var value = $target.val();

			happyForms.form.set( attribute, value );

			var data = {
				variable: variable,
				value: value,
			};

			happyForms.previewSend( 'happyforms-css-variable-update', data );
		},

		onAttributeChange: function( e ) {
			e.preventDefault();

			var $target = $( e.target );
			var attribute = $target.data( 'attribute' );
			var value = $target.val();

			happyForms.form.set( attribute, value );
		},

		onOptionalPartLabelChange: function( e ) {
			var data = {
				callback: 'onOptionalPartLabelChangeCallback',
			};

			happyForms.previewSend( 'happyforms-form-dom-update', data );
		},

		onGroupClick: function( e ) {
			e.preventDefault();

			$( '.happyforms-style-controls-group', this.$el ).removeClass( 'open' );

			$( '.happyforms-divider-control', this.$el )
				.removeClass( 'active' )
				.addClass( 'inactive' );

			$( e.target ).parent().next().addClass( 'open' );
		},

		onGroupBackClick: function( e ) {
			e.preventDefault();

			$( '.happyforms-divider-control', this.$el )
				.removeClass( 'inactive' )
				.addClass( 'active' );

			var $section = $( e.target ).closest( '.happyforms-style-controls-group' );

			$section.addClass( 'closing' );

			setTimeout(function () {
				$section.removeClass('closing open');
			}, 200);
		},

		initButtonSet: function() {
			$('.happyforms-buttonset-container').buttonset();
		},

		initColorPickers: function() {
			var self = this;
			var $colorInputs = $( '.happyforms-color-input', this.$el );

			$colorInputs.each( function( index, el ) {
				var $control = $( el ).parents( '.customize-control' );
				var variable = $control.data( 'variable' );

				$( el ).wpColorPicker( {
					change: function( e, ui ) {
						var value = ui.color.toString();

						self.model.set( $( this ).attr( 'data-attribute' ), value );

						var data = {
							variable: variable,
							value: value,
						};

						happyForms.previewSend( 'happyforms-css-variable-update', data );
					}
				} );

				var $wpPickerContainer = $( el ).parent().parent();

				$wpPickerContainer.find( '.wp-picker-clear' ).on( 'click', function() {
					var attribute = $( el ).attr( 'data-attribute' );
					var value = $( el ).attr( 'data-default' );

					self.model.set( attribute, value );
				} );
			} );
		},

		initUISliders: function() {
			var self = this;
			var $container = this.$el.find( '.happyforms-range-control:not(.form-width-control)' );

			$container.each(function (el, index) {
				var $this = $(this);
				var variable = $this.data('variable');
				var $slider = $( '.happyforms-range-slider', $this );
				var $sliderInput = $( 'input', $this );
				var min = parseFloat( $sliderInput.attr( 'min' ) );
				var max = parseFloat( $sliderInput.attr( 'max' ) );
				var step = parseFloat( $sliderInput.attr( 'step' ) );
				var value = parseFloat( $sliderInput.val() );

				$sliderInput.on('keyup mouseup', function() {
					var $this = $(this);

					self.model.set( $sliderInput.attr('data-attribute'), $this.val() );

					var data = {
						variable: variable,
						value: $this.val() + '' + $this.parent().attr('data-unit'),
					};

					happyForms.previewSend('happyforms-css-variable-update', data);
				});

				$slider.slider( {
					value: value,
					min: min,
					max: max,
					step: step,

					stop: function( e, ui ) {
						$sliderInput.val(ui.value);
						self.model.set( $sliderInput.attr( 'data-attribute' ), ui.value );

						var data = {
							variable: variable,
							value: ui.value + '' + $sliderInput.parent().attr('data-unit'),
						};

						happyForms.previewSend('happyforms-css-variable-update', data);
					}
				} );
			} );
		},

		initFormWidthSlider: function(reInit) {
			var self = this;

			var $container = this.$el.find( '.happyforms-range-control.form-width-control' );
			var $slider = $( '.happyforms-range-slider', $container );
			var $input = $( 'input', $container );
			var $unitSwitch = $( '.happyforms-unit-switch', $container );

			var stringValue = this.model.get('form_width').toString();
			var numericValue = (stringValue) ? parseFloat(stringValue.replace(/px|%/gi, '')) : 100;
			var unit = $unitSwitch.val();

			if ( ! reInit ) {
				if ( -1 !== stringValue.indexOf('%') ) {
					unit = '%';
				} else if ( -1 !== stringValue.indexOf('px') ) {
					unit = 'px';
				} else {
					unit = '%';
				}

				$unitSwitch.val(unit);
			}

			var min = ('px' === unit) ? 360 : 0;
			var max = ('px' === unit) ? 1440 : 100;
			var step = ('px' === unit) ? 10 : 5;

			$input.attr('min', min);
			$input.attr('max', max);
			$input.attr('step', step);

			$unitSwitch.on('change', function () {
				self.initFormWidthSlider(true);
			});

			if ( reInit ) {
				numericValue = ('%' === unit) ? 100 : 900;

				self.updateFormWidth(numericValue, unit, $slider);
			}

			$input.val(numericValue);

			$input.on('keyup change mouseup', function () {
				var $this = $(this);

				self.updateFormWidth($this.val(), unit, $slider);
			});

			$slider.slider({
				value: numericValue,
				min: min,
				max: max,
				step: step,

				stop: function (e, ui) {
					$input.val(ui.value);

					self.updateFormWidth(ui.value, unit, $slider);
				}
			});
		},

		updateFormWidth: function( value, unit, $slider ) {
			$slider.slider('value', value);

			this.model.set('form_width', value + unit);

			var data = {
				variable: '--happyforms-form-width',
				value: value + unit,
			};

			happyForms.previewSend('happyforms-css-variable-update', data);
		},
	} );

	Previewer = {
		$: $,
		ready: false,

		getPartModel: function( id ) {
			return happyForms.form.get( 'parts' ).get( id );
		},

		getPartElement: function( html ) {
			return this.$( html );
		},

		bind: function() {
			this.ready = true;

			// Form title pencil
			api.previewer.bind(
				'happyforms-pencil-click-title',
				this.onPreviewPencilClickTitle.bind( this )
			);

			// Part pencils
			api.previewer.bind(
				'happyforms-pencil-click-part',
				this.onPreviewPencilClickPart.bind( this )
			);
		},

		/**
		 *
		 * Previewer callbacks for pencils
		 *
		 */
		onPreviewPencilClickPart: function( id ) {
			happyForms.navigate( 'build', { trigger: true } );

			var $partWidget = $( '[data-part-id="' + id + '"]' );

			if ( ! $partWidget.hasClass( 'happyforms-widget-expanded' ) ) {
				$partWidget.find( '.toggle-indicator' ).click();
			}

			$( 'input', $partWidget ).first().focus();
		},

		onPreviewPencilClickTitle: function( id ) {
			happyForms.navigate( 'build', { trigger: true } );

			$( 'input[name="post_title"]' ).focus();
		},

		onOptionalPartLabelChangeCallback: function( $form ) {
			var optionalLabel = happyForms.form.get( 'optional_part_label' );
			$( '.happyforms-optional', $form ).text( optionalLabel );
		},

		/**
		 *
		 * Previewer callbacks for live part DOM updates
		 *
		 */
		onPartMouseOverCallback: function( id, html ) {
			var $part = this.$( html );
			$part.addClass( 'highlighted' );
		},

		onPartMouseOutCallback: function( id, html ) {
			var $part = this.$( html );
			$part.removeClass( 'highlighted' );
		},

		onPartLabelChangeCallback: function( id, html ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var $label = this.$( '.happyforms-part__label span.label', $part ).first();

			$label.text( part.get( 'label' ) );
		},

		onRequiredCheckboxChangeCallback: function( id, html ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var required = part.get( 'required' );
			var optionalLabel = happyForms.form.get( 'optional_part_label' );

			if ( 0 === parseInt( required, 10 ) ) {
				$part.removeAttr( 'data-happyforms-required' );
				$( '.happyforms-optional', $part ).text( optionalLabel );
			} else {
				$part.attr( 'data-happyforms-required', '' );
			}
		},

		onPartWidthChangeCallback: function( id, html, options ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var width = part.get( 'width' );

			$part.removeClass( 'happyforms-part--width-half' );
			$part.removeClass( 'happyforms-part--width-full' );
			$part.removeClass( 'happyforms-part--width-third' );
			$part.removeClass( 'happyforms-part--width-auto' );
			$part.addClass( 'happyforms-part--width-' + width );
		},

		onPlaceholderChangeCallback: function( id, html ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );

			this.$( 'input', $part ).attr( 'placeholder', part.get( 'placeholder' ) );
		},

		onDescriptionChangeCallback: function( id, html ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var description = part.get('description');
			var $description = this.$( '.happyforms-part__description', $part );

			$description.text(description);
		},

		onDescriptionModeChangeCallback: function( id, html ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var $description = this.$( '.happyforms-tooltip + .happyforms-part__description', $part );
			var $tooltip = this.$( '.happyforms-part__tooltip', $part );

			switch( part.get( 'description_mode' ) ) {
				case 'focus-reveal':
					$tooltip.hide();
					$description.show();
					$part.addClass('happyforms-part--focus-reveal-description');
					break;
				case 'tooltip':
					$tooltip.show();
					$description.hide();
					$part.removeClass('happyforms-part--focus-reveal-description');
					break;
				case '':
				default:
					$tooltip.hide();
					$description.show();
					$part.removeClass('happyforms-part--focus-reveal-description');
					break;
			}
		},

		onLabelPlacementChangeCallback: function( id, html, options ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );

			$part.removeClass( 'happyforms-part--label-above' );
			$part.removeClass( 'happyforms-part--label-below' );
			$part.removeClass( 'happyforms-part--label-left' );
			$part.removeClass( 'happyforms-part--label-right' );
			$part.removeClass( 'happyforms-part--label-inside' );
			$part.addClass( 'happyforms-part--label-' + part.get( 'label_placement' ) );
		},

		onCSSClassChangeCallback: function( id, html, options ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var previousClass = part.previous( 'css_class' );
			var currentClass = part.get( 'css_class' );

			$part.removeClass( previousClass );
			$part.addClass( currentClass );
		},

		onSubPartAdded: function( id, partHTML, optionHTML ) {
			var partView = happyForms.sidebar.current.partViews.get( id ).get( 'view' );
			partView.onSubPartAdded( id, partHTML, optionHTML );
		},

		onFormClassChangeCallback: function( attribute, html, options ) {
			var $form = this.$( html );
			var previousClass = happyForms.form.previous( attribute );
			var currentClass = happyForms.form.get( attribute );

			$form.removeClass( previousClass );
			$form.addClass( currentClass );

			api.previewer.send( 'happyforms-form-class-updated' );
		},

		onFormClassToggleCallback: function( attribute, html, options ) {
			var $form = this.$( html );
			var previousClass = happyForms.form.previous( attribute );
			var currentClass = happyForms.form.get( attribute );

			$form.removeClass( previousClass );
			$form.addClass( currentClass );
		},

		onRecaptchaUpdate: function( $recaptcha, $ ) {
			var captcha = happyForms.form.get( 'captcha' );

			if ( captcha ) {
				var siteKey = happyForms.form.get( 'captcha_site_key' ) || 'null';
				$recaptcha.attr( 'data-sitekey', siteKey );
				$recaptcha.show();
				$recaptcha.happyFormPart( 'render' );
			} else {
				$recaptcha.hide();
				$recaptcha.happyFormPart( 'reset' );
			}
		},

		onFocusRevealDescriptionCallback: function( id, html, options ) {
			var part = happyForms.form.get( 'parts' ).get( id );
			var $part = this.$( html );
			var focusRevealDescription = part.get('focus_reveal_description');

			if ( 1 == focusRevealDescription ) {
				$part.addClass( 'happyforms-part--focus-reveal-description' );
			} else {
				$part.removeClass( 'happyforms-part--focus-reveal-description' );
			}
		}
	};

	happyForms = window.happyForms = new HappyForms();
	happyForms.classes = classes;
	happyForms.factory = PartFactory;
	happyForms.previewer = Previewer;

	happyForms.utils = {
		uniqueId: function( prefix, collection ) {
			if ( collection ) {
				var increments = collection
					.pluck( 'id' )
					.map( function( id ) {
						var numberId = id.match( /_(\d+)$/ );
						numberId = numberId !== null ? parseInt( numberId[1] ): 0;
						return numberId;
					} )
					.sort( function( a, b ) {
						return b - a;
					} );

				var increment = increments.length ? increments[0] + 1 : 1;

				return prefix + increment;
			}

			return _.uniqueId( prefix );
		}
	};

	api.bind( 'ready', function() {
		happyForms.start();

		api.previewer.bind( 'ready', function() {
			happyForms.flushBuffer();
			happyForms.previewer.bind();
		} );
	} );

} ) ( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
