<?php

class HappyForms_Core {

	/**
	 * The parameter key used to connotate
	 * HappyForms Customize screen sessions.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $customize_mode = 'happyforms';

	/**
	 * The form shortcode name.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $shortcode = 'happyforms';

	/**
	 * URL of plugin landing page.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $landing_page_url = 'https://www.happyforms.me';

	/**
	 * List of forms found on current page.
	 *
	 * @since 1.3
	 *
	 * @var array
	 */
	private $current_forms = array();

	/**
	 * Whether or not frontend styles were loaded.
	 */
	private $frontend_styles = false;

	/**
	 * Action: initialize admin and frontend logic.
	 *
	 * @since 1.0
	 *
	 * @hooked action plugins_loaded
	 *
	 * @return void
	 */
	public function initialize_plugin() {
		require_once( happyforms_get_include_folder() . '/core/helpers/helper-misc.php' );
		require_once( happyforms_get_include_folder() . '/core/helpers/helper-styles.php' );

		if ( is_admin() ) {
			require_once( happyforms_get_include_folder() . '/core/classes/class-admin-notices.php' );
		}

		require_once( happyforms_get_include_folder() . '/core/classes/class-tracking.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-form-controller.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-message-controller.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-email-message.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-form-part-library.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-form-styles.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-session.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-happyforms-widget.php' );
		require_once( happyforms_get_include_folder() . '/core/helpers/helper-form-templates.php' );
		require_once( happyforms_get_include_folder() . '/core/classes/class-migrations.php' );
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Gutenberg block
		if ( happyforms_is_gutenberg() ) {
			require_once( happyforms_get_include_folder() . '/core/classes/class-block.php' );
		}

		// Admin hooks
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 80 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'current_screen', array( $this, 'admin_screens' ) );
		add_filter( 'media_buttons_context', array( $this, 'insert_editor_buttons' ) );
		add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );

		// Widget
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		// Common hooks
		add_shortcode( $this->shortcode, array( $this, 'handle_shortcode' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ), 0 );
		add_action( 'admin_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ), 0 );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_shortcode_template' ) );

		// Preview scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_preview' ) );
		add_action( 'wp_footer', array( $this, 'enqueue_scripts_preview' ) );
	}

	/**
	 * Action: initialize Customize screen logic.
	 *
	 * @since 1.0
	 *
	 * @hooked action customize_loaded_components
	 *
	 * @param array $components Array of standard Customize components.
	 *
	 * @return array
	 */
	public function initialize_customize_screen( $components ) {
		/*
		 * See "Resetting the Customizer to a Blank Slate"
		 * https://make.xwp.co/2016/09/11/resetting-the-customizer-to-a-blank-slate/
		 * https://github.com/xwp/wp-customizer-blank-slate
		 */

		// Initialize our customize screen if we're in HappyForms mode.
		if ( ! $this->is_customize_mode() ) {
			return $components;
		}

		require_once( happyforms_get_include_folder() . '/core/classes/class-wp-customize-form-manager.php' );
		require_once( happyforms_get_include_folder() . '/core/helpers/helper-validation.php' );
		require_once( happyforms_get_include_folder() . '/core/helpers/helper-customize-templates.php' );

		$this->customize = new HappyForms_WP_Customize_Form_Manager();

		// Short-circuit widgets, nav-menus, etc from loading.
		return array();
	}

	/**
	 * Action: register admin menus.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		$form_controller = happyforms_get_form_controller();
		$message_controller = happyforms_get_message_controller();

		add_menu_page(
			__( 'HappyForms Index', 'happyforms' ),
			__( 'HappyForms', 'happyforms' ),
			$form_controller->capability,
			'happyforms',
			array( $this, 'happyforms_page_index' ),
			'dashicons-format-status',
			50
		);

		add_submenu_page(
			'happyforms',
			__( 'All Forms', 'happyforms' ),
			__( 'All Forms', 'happyforms' ),
			$form_controller->capability,
			'/edit.php?post_type=happyform'
		);

		add_submenu_page(
			'happyforms',
			__( 'Add New', 'happyforms' ),
			__( 'Add New', 'happyforms' ),
			$form_controller->capability,
			happyforms_get_form_edit_link( 0 )
		);

		add_submenu_page(
			'happyforms',
			__( 'Responses', 'happyforms' ),
			__( 'Responses', 'happyforms' ) . happyforms_unread_messages_badge(),
			$message_controller->capability,
			'/edit.php?post_type=happyforms-message'
		);

		$tracking = happyforms_get_tracking();
		$status = $tracking->get_status();

		if ( 4 > $status['status'] ) {
			add_submenu_page(
				'happyforms',
				__( 'Welcome', 'happyforms' ),
				__( 'Welcome', 'happyforms' ),
				'manage_options',
				'happyforms-welcome',
				array( happyforms_get_tracking(), 'settings_page' )
			);
		}

		// Remove first duplicate submenu link
		remove_submenu_page( 'happyforms', 'happyforms' );
	}

	/**
	 * Action: enqueue scripts and styles for the admin.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_enqueue_scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style(
			'happyforms-admin',
			happyforms_get_plugin_url() . 'inc/core/assets/css/admin.css',
			array(), HAPPYFORMS_VERSION
		);

		wp_enqueue_script(
			'happyforms-admin',
			happyforms_get_plugin_url() . 'inc/core/assets/js/admin/dashboard.js',
			array(), HAPPYFORMS_VERSION, true
		);
	}

	/**
	 * Action: include custom admin screens
	 * for the Form and Message post types.
	 *
	 * @since 1.0
	 *
	 * @hooked action current_screen
	 *
	 * @return void
	 */
	public function admin_screens() {
		global $pagenow;

		$form_post_type = happyforms_get_form_controller()->post_type;
		$message_post_type = happyforms_get_message_controller()->post_type;
		$current_post_type = get_current_screen()->post_type;

		if ( in_array( $pagenow, array( 'edit.php', 'post.php' ) )
			&& in_array( $current_post_type, array( $form_post_type, $message_post_type ) ) ) {

			switch( $current_post_type ) {
				case $form_post_type:
					require_once( happyforms_get_include_folder() . '/core/classes/class-form-admin.php' );
					break;
				case $message_post_type:
					require_once( happyforms_get_include_folder() . '/core/classes/class-message-admin.php' );
					break;
			}
		}
	}

	/**
	 * Get basic info about the form being currently edited.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_form_data_array() {
		$forms = happyforms_get_form_controller()->get();
		$form_data = array();

		foreach ( $forms as $form ) {
			array_push( $form_data, array( 'id' => $form['ID'], 'title' => $form['post_title'] ) );
		}

		return $form_data;
	}

	/**
	 * Action: add a HappyForms menu to the admin bar.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_bar_menu
	 *
	 * @return void
	 */
	public function admin_bar_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_node( array(
			'id'     => 'new-content-happyforms',
			'parent' => 'new-content',
			'title'  => __( 'HappyForm', 'happyforms' ),
			'href'   => admin_url( happyforms_get_form_edit_link( 0 ) ),
			'meta'   => array(
				'target' => '_self',
				'title'  => __( 'New HappyForm', 'happyforms' ),
			),
		) );
	}

	/**
	 * Return whether or not we're running
	 * the Customize screen in HappyForms mode.
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	private function is_customize_mode() {
		return (
			isset( $_REQUEST['happyforms'] )
			&& ! empty( $_REQUEST['happyforms'] )
			&& isset( $_REQUEST['form_id'] )
		);
	}

	/**
	 * Filter: register the form dropdown button
	 * for he post content editor toolbar.
	 *
	 * @since 1.0
	 *
	 * @hooked filter mce_buttons
	 *
	 * @param array $buttons The currently registered buttons.
	 *
	 * @return array
	 */
	public function tinymce_register_button( $buttons ) {
		$buttons[] = 'happyforms_form_picker';

		return $buttons;
	}

	/**
	 * Enqueue a form to load assets for.
	 *
	 * @since 1.0
	 *
	 * @param array $form The form to enqueue.
	 *
	 * @return void
	 */
	public function enqueue_form( $form ) {
		$this->current_forms[$form['ID']] = $form;
	}

	/**
	 * Render the HappyForms shortcode.
	 *
	 * @since 1.0
	 *
	 * @param array $attrs The shortcode attributes.
	 *
	 * @return string
	 */
	public function handle_shortcode( $attrs ) {
		$form_id = intval( $attrs['id'] );
		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( $form_id );

		if ( empty( $form ) ) {
			return '';
		}

		$output = $form_controller->render( $form, true );
		$this->enqueue_form( $form );

		return $output;
	}

	/**
	 * Action: output scripts and styles for the forms
	 * embedded into the current post.
	 *
	 * @since 1.0
	 *
	 * @hooked action wp_head
	 *
	 * @return void
	 */
	public function wp_head() {
		?>
		<!-- HappyForms global container -->
		<script type="text/javascript">HappyForms = {};</script>
		<!-- End of HappyForms global container -->
		<?php
	}

	public function admin_head() {
		global $pagenow;

		if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
			return;
		}

		$forms = happyforms_get_form_controller()->get();
		$form_titles = wp_list_pluck( $forms, 'post_title', 'ID' );
		?>
		<script type="text/javascript">
		HappyForms = {};
		HappyForms.admin = {
			edit_link: '<?php echo admin_url( happyforms_get_form_edit_link( 'ID', 'URL' ) ); ?>',
			forms: <?php echo json_encode( $form_titles ); ?>,
		}
		</script>
        <?php
	}

	/**
	 * Filter: Add HappyForms button markup to a markup above content editor, next to
	 * Add Media button.
	 *
	 * @since 1.1.0.
	 *
	 * @hooked filter media_buttons_context
	 *
	 * @param string $context HTML markup.
	 *
	 * @return void
	 */
	public function insert_editor_buttons( $context ) {
        global $pagenow;

        if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
            return $context;
        }

        $button_html = '<a href="#" class="button happyforms-editor-button" data-title="' . __( 'Insert HappyForm', 'happyforms' ) . '"><span class="dashicons dashicons-format-status"></span><span>'. __( 'Add HappyForms', 'happyforms' ) .'</span></a>';

		add_action( 'admin_footer', array( $this, 'output_happyforms_modal' ) );

        return $context . ' ' . $button_html;
	}

	public function mce_external_plugins( $plugins ) {
		$plugins['happyforms_shortcode'] = happyforms_get_plugin_url() . 'inc/core/assets/js/admin/shortcode.js';

		return $plugins;
	}

	/**
	 * Render HappyForms dialog in the footer of edit post / page screen. Also
	 * prints a script block for adding shortcode to visual editor.
	 *
	 * @since 1.3.0.
	 *
	 * @hooked action admin_footer
	 *
	 * @return void
	 */
	public function output_happyforms_modal() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		require_once( happyforms_get_include_folder() . '/core/templates/admin-form-modal.php' );
	}

	public function print_frontend_styles() {
		$output = apply_filters( 'happyforms_enqueue_style', true );

		if ( ! $output ) {
			return;
		}

		$url = happyforms_get_plugin_url() . 'inc/core/assets/css/frontend.css?' . HAPPYFORMS_VERSION;
		?>
		<link rel="stylesheet" property="stylesheet" href="<?php echo $url; ?>" />
		<?php
	}

	/**
	 * Action: enqueue scripts and styles
	 * for the frontend part of the plugin.
	 *
	 * @since 1.0
	 *
	 * @hooked action wp_print_footer_scripts
	 *
	 * @return void
	 */
	public function wp_print_footer_scripts() {
		if ( happyforms_is_preview() ) {
			$form_controller = happyforms_get_form_controller();
			$form = $form_controller->get( get_the_ID() );
			$this->current_forms[] = $form;
		}

		// Return early if no current forms
		// are being displayed.
		if ( empty( $this->current_forms ) ) {
			return;
		}

		if ( is_admin() && happyforms_is_gutenberg() ) {
			return;
		}

		wp_register_script(
			'happyforms-select',
			happyforms_get_plugin_url() . 'inc/core/assets/js/lib/happyforms-select.js',
			array( 'jquery' ), HAPPYFORMS_VERSION, true
		);

		$dependencies = apply_filters(
			'happyforms_frontend_dependencies',
			array( 'jquery' ), $this->current_forms
		);

		wp_enqueue_script(
			'happyforms-frontend',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend.js',
			$dependencies, HAPPYFORMS_VERSION, true
		);

		/**
		 * Output form-specific scripts and styles.
		 *
		 * @since 1.1
		 *
		 * @param array $forms Array of forms found in page.
		 *
		 * @return void
		 */
		do_action( 'happyforms_footer', $this->current_forms );
	}

	public function print_shortcode_template() {
		require_once( happyforms_get_include_folder() . '/core/templates/admin-shortcode.php' );
	}

	public function enqueue_styles_preview() {
		if ( ! happyforms_is_preview() ) {
			return;
		}

		wp_enqueue_style(
			'happyforms-preview',
			happyforms_get_plugin_url() . 'inc/core/assets/css/preview.css',
			array(), HAPPYFORMS_VERSION
		);
	}

	/**
	 * Action: enqueue HappyForms styles and scripts
	 * for the Customizer preview part.
	 *
	 * @since  1.3
	 *
	 * @hooked action customize_preview_init
	 *
	 * @return void
	 */
	public function enqueue_scripts_preview() {
		if ( ! happyforms_is_preview() ) {
			return;
		}

		$preview_deps = apply_filters(
			'happyforms_preview_dependencies',
			array( 'customize-preview' )
		);

		wp_enqueue_script(
			'happyforms-preview',
			happyforms_get_plugin_url() . 'inc/core/assets/js/preview.js',
			$preview_deps, HAPPYFORMS_VERSION, true
		);

		require_once( happyforms_get_include_folder() . '/core/templates/preview-form-pencil.php' );
	}

	/**
	 * Action: register the HappyForms widget.
	 *
	 * @since 1.0
	 *
	 * @hooked action widgets_init
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( 'HappyForms_Widget' );
	}

}
