<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Plugin {
	private static $instance;
	private $base;
	const OPTION = 'admin-color-schemer';
	const NONCE = 'admin-color-schemer_save';

	private $colors;

	protected function __construct() {
		self::$instance = $this;
		$this->base = dirname( dirname( __FILE__ ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			new self;
		}
		return self::$instance;
	}

	public function init() {
		// Initialize translations
		load_plugin_textdomain( 'admin-color-schemer', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );

		// Set up color arrays - need translations
		$this->colors['basic'] = array(
			'base_color' => __( 'Base', 'admin-color-schemer' ),
			'icon_color' => __( 'Icon', 'admin-color-schemer' ),
			'highlight_color' => __( 'Highlight', 'admin-color-schemer' ),
			'notification_color' => __( 'Notification', 'admin-color-schemer' ),
		);

		$this->colors['advanced'] = array(
			'button_color' => __( 'Button', 'admin-color-schemer' ),
			'text_color' => __( 'Text (over Base)', 'admin-color-schemer' ),
			'body_background' => __( 'Body background', 'admin-color-schemer' ),
			'link' => __( 'Link', 'admin-color-schemer' ),
			'link_focus' => __( 'Link interaction', 'admin-color-schemer' ),
			'form_checked' => __( 'Checked form controls', 'admin-color-schemer' ),
			'menu_background' => __( 'Menu background', 'admin-color-schemer' ),
			'menu_text' => __( 'Menu text', 'admin-color-schemer' ),
			'menu_icon' => __( 'Menu icon', 'admin-color-schemer' ),
			'menu_highlight_background' => __( 'Menu highlight background', 'admin-color-schemer' ),
			'menu_highlight_text' => __( 'Menu highlight text', 'admin-color-schemer' ),
			'menu_highlight_icon' => __( 'Menu highlight icon', 'admin-color-schemer' ),
			'menu_current_background' => __( 'Menu current background', 'admin-color-schemer' ),
			'menu_current_text' => __( 'Menu current text', 'admin-color-schemer' ),
			'menu_current_icon' => __( 'Menu current icon', 'admin-color-schemer' ),
			'menu_submenu_background' => __( 'Submenu background', 'admin-color-schemer' ),
			'menu_submenu_text' => __( 'Submenu text', 'admin-color-schemer' ),
			'menu_submenu_background_alt' => __( 'Submenu alt background', 'admin-color-schemer' ),
			'menu_submenu_focus_text' => __( 'Submenu text interaction', 'admin-color-schemer' ),
			'menu_submenu_current_text' => __( 'Submenu current text', 'admin-color-schemer' ),
			'menu_bubble_background' => __( 'Bubble background', 'admin-color-schemer' ),
			'menu_bubble_text' => __( 'Bubble text', 'admin-color-schemer' ),
			'menu_bubble_current_background' => __( 'Bubble current background', 'admin-color-schemer' ),
			'menu_bubble_current_text' => __( 'Bubble current text', 'admin-color-schemer' ),
			'menu_collapse_text' => __( 'Menu collapse text', 'admin-color-schemer' ),
			'menu_collapse_icon' => __( 'Menu collapse icon', 'admin-color-schemer' ),
			'menu_collapse_focus_text' => __( 'Menu collapse text interaction', 'admin-color-schemer' ),
			'menu_collapse_focus_icon' => __( 'Menu collapse icon interaction', 'admin-color-schemer' ),
			'adminbar_avatar_frame' => __( 'Toolbar avatar frame', 'admin-color-schemer' ),
			'adminbar_input_background' => __( 'Toolbar input background', 'admin-color-schemer' ),
		);

		// Hooks
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_post_admin-color-schemer-save', array( $this, 'save' ) );
		add_action( 'wp_ajax_admin-color-schemer-save', array( $this, 'save' ) );
	}

	public function admin_init() {
		$schemes = $this->get_option( 'schemes', array() );

		foreach ( $schemes as $scheme ) {
			wp_admin_css_color(
				$scheme['slug'],
				$scheme['name'],
				esc_url( $scheme['uri'] ),
				array( $scheme['base_color'], $scheme['icon_color'], $scheme['highlight_color'], $scheme['notification_color'] ),
				array( 'base' => $scheme['icon_color'], 'focus' => $scheme['icon_focus'], 'current' => $scheme['icon_current'] )
			);
		}
	}

	public function admin_menu() {
		$hook = add_management_page( 'Admin Color Schemer', 'Admin Colors', 'manage_options', 'admin-color-schemer', array( $this, 'admin_page' ) );
		add_action( 'load-' . $hook, array( $this, 'load' ) );
	}

	public function load() {
		if ( isset( $_GET['updated'] ) ) {
			add_action( 'admin_notices', array( $this, 'updated' ) );
		} elseif ( isset( $_GET['empty_scheme'] ) ) {
			add_action( 'admin_notices', array( $this, 'empty_scheme' ) );
		}

		add_action ( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function updated() {
		include( $this->base . '/templates/updated.php' );
	}

	public function empty_scheme() {
		include( $this->base . '/templates/empty-scheme.php' );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'admin-color-schemer', plugins_url( '/js/admin-color-schemer.js', dirname( __FILE__ ) ), array( 'wp-color-picker' ), false, true );
		wp_enqueue_style( 'admin-color-schemer', plugins_url( '/css/admin-color-schemer.css', dirname( __FILE__ ) ), array( 'wp-color-picker' ) );
	}

	public function admin_page() {
		$scheme = $this->get_color_scheme();
		include( $this->base . '/templates/admin-page.php' );
	}

	public function get_option( $key, $default = null ) {
		$option = get_option( self::OPTION );
		if ( ! is_array( $option ) || ! isset( $option[$key] ) ) {
			return $default;
		} else {
			return $option[$key];
		}
	}

	public function set_option( $key, $value ) {
		$option = get_option( self::OPTION );
		is_array( $option ) || $option = array();
		$option[$key] = $value;
		update_option( self::OPTION, $option );
	}

	protected function get_color_scheme( $id = null ) {
		$scheme = null;

		// special handling for preview
		if ( 'preview' === $id ) {
			$preview_defaults = array(
				'id' => 'preview',
				'name' => 'preview',
			);

			$scheme = $this->get_option( 'preview', $preview_defaults );
		} else {
			// otherwise ignoring $id right now during development
			$schemes = $this->get_option( 'schemes', array() );
			$scheme = array_shift( $schemes );
		}

		if ( $scheme ) {
			return new Admin_Color_Schemer_Scheme( $scheme );
		} else {
			return new Admin_Color_Schemer_Scheme();
		}
	}

	public function get_colors( $set = null ) {
		if ( 'basic' === $set ) {
			return $this->colors['basic'];
		} elseif ( 'advanced' === $set ) {
			return $this->colors['advanced'];
		} elseif( 'keys' === $set ) {
			// special handling for dashes to underscores, because PHP
			$keys = array_keys( $this->get_colors() );
			$scss_keys = array();
			foreach ( $keys as $key ) {
				$scss_keys[] = str_replace( '-', '_', $key );
			}

			// the naming of keys is kind of backward here
			return array_combine( $keys, $scss_keys );
		} else {
			return array_merge( $this->colors['basic'], $this->colors['advanced'] );
		}
	}

	public function admin_url() {
		return admin_url( 'tools.php?page=admin-color-schemer' );
	}

	public function save() {
		current_user_can( 'manage_options' ) || die;
		check_admin_referer( self::NONCE );
		$_post = stripslashes_deep( $_POST );
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $doing_ajax ) {
			$scheme = $this->get_color_scheme( 'preview' );
		} else {
			$scheme = $this->get_color_scheme();
		}

		$colors = $this->get_colors( 'keys' );

		// @todo: which, if any, of these are required?
		foreach ( $colors as $key => $scss_key ) {
			// really, these are always set, but always check, too!
			if ( isset( $_post[ $key ] ) ) {
				$scheme->{$key} = $_post[ $key ];
			}
		}

		$scss = '';

		foreach( $colors as $key => $scss_key ) {
			if ( ! empty( $scheme->{$key} ) ) {
				$scss .= "\${$scss_key}: {$scheme->$key};\n";
			}
		}

		if ( empty( $scss ) ) {
			// bail if this gets emptied out
			if ( $doing_ajax ) {
				$response = array(
					'errors' => true,
					'message' => __( 'Please make more selections to preview the color scheme.', 'admin-color-schemer' ),
				);

				echo json_encode( $response );
				die();
			}

			// reset color scheme object
			$scheme = $this->get_color_scheme();
			wp_redirect( $this->admin_url() . '&empty_scheme=true' );
			exit;
		}

		$scss .= "\n\n@import 'colors.css';\n@import '_admin.scss';\n";

		// okay, let's see about getting credentials
		// @todo: what to do about preview
		if ( false === ( $creds = request_filesystem_credentials( $this->admin_url() ) ) ) {
			return true;
		}

		// now we have some credentials, try to get the wp_filesystem running
		if ( ! WP_Filesystem( $creds ) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials( $this->admin_url(), '', true );
			return true;
		}

		global $wp_filesystem;

		$wp_upload_dir = wp_upload_dir();
		$upload_dir = $wp_upload_dir['basedir'] . '/admin-color-schemer';
		$upload_url = $wp_upload_dir['baseurl'] . '/admin-color-schemer';

		// @todo: error handling if this can't be made - needs to be differentiated from already there
		$wp_filesystem->mkdir( $upload_dir );

		if ( $doing_ajax ) {
			$scss_file = $upload_dir . '/preview.scss';
			$css_file = $upload_dir . '/preview.css';
			// use a modified query arg to avoid caching problems
			// @todo: chances are we'll need to do this for the saved scheme as well.
			$uri = $upload_url . '/preview.css?m=' . microtime();
		} else {
			// @todo: save into another subdirectory for multiple scheme handling
			$scss_file = $upload_dir . '/scheme.scss';
			$css_file = $upload_dir . '/scheme.css';
			$uri = $upload_url . '/scheme.css';
		}

		$this->maybe_copy_core_files( $upload_dir );

		// write the custom.scss file
		if ( ! $wp_filesystem->put_contents( $scss_file, $scss, FS_CHMOD_FILE) ) {
			if ( $doing_ajax ) {
				$response = array(
					'errors' => true,
					'message' => __( 'Could not write custom SCSS file.', 'admin-color-schemer' ),
				);

				echo json_encode( $response );
				die();
			}

			// @todo: error that the scheme couldn't be written and redirect
			exit( 'Could not write custom SCSS file.' );
		}

		// Compile and write!
		require_once( $this->base . '/lib/phpsass/SassParser.php' );
		$sass = new SassParser();
		$css = $sass->toCss( $scss_file );

		if ( ! $wp_filesystem->put_contents( $css_file, $css, FS_CHMOD_FILE) ) {
			if ( $doing_ajax ) {
				$response = array(
					'errors' => true,
					'message' => __( 'Could not write compiled CSS file.', 'admin-color-schemer' ),
				);

				echo json_encode( $response );
				die();
			}

			// @todo: error that the compiled scheme couldn't be written and redirect
			exit( 'Could not write compiled CSS file.' );
		}

		// add the URI of the sheet to the settings array
		$scheme->uri = $uri;

		if ( $doing_ajax ) {
			$response = array(
				'uri' => $scheme->uri,
				'message' => __( 'Previewing. Be sure to save if you like the result.', 'admin-color-schemer' ),
			);

			echo json_encode( $response );
			die();
		}

		$this->set_option( 'schemes', array( $scheme->id => $scheme->to_array() ) );

		// switch to the scheme
		update_user_meta( get_current_user_id(), 'admin_color', $scheme->slug );

		wp_redirect( $this->admin_url() . '&updated=true' );
		exit;
	}

	public function maybe_copy_core_files( $upload_dir ) {
		global $wp_filesystem;

		// pull in core's default colors.css and scss files if they're not there already
		$core_scss = array( '_admin.scss', '_mixins.scss', '_variables.scss' );
		$admin_dir = ABSPATH . '/wp-admin/css/';

		foreach ( $core_scss as $file ) {
			if ( ! file_exists( $upload_dir . "/{$file}" ) ) {
				if ( ! $wp_filesystem->put_contents( $upload_dir . "/{$file}", $wp_filesystem->get_contents( $admin_dir . 'colors/' . $file, FS_CHMOD_FILE) ) ) {
					if ( $doing_ajax ) {
						$response = array(
							'errors' => true,
							'message' => __( 'Could not copy a core file.', 'admin-color-schemer' ),
						);

						echo json_encode( $response );
						die();
					}

					// @todo: error that the scheme couldn't be written and redirect
					exit( "Could not copy the core file {$file}." );
				}
			}
		}

		if ( ! file_exists( $upload_dir . "/colors.css" ) ) {
			if ( ! $wp_filesystem->put_contents( $upload_dir . "/colors.css", $wp_filesystem->get_contents( $admin_dir . 'colors.css', FS_CHMOD_FILE) ) ) {
				if ( $doing_ajax ) {
					$response = array(
						'errors' => true,
						'message' => __( 'Could not copy a core file.', 'admin-color-schemer' ),
					);

					echo json_encode( $response );
					die();
				}

				// @todo: error that the scheme couldn't be written and redirect
				exit( "Could not copy the core file colors.css." );
			}
		}
	}
}
