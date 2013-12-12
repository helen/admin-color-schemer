<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Plugin {
	private static $instance;
	private $base;
	const OPTION = 'admin-color-schemer';
	const NONCE = 'admin-color-schemer_save';

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

		// Hooks
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_post_admin-color-schemer-save', array( $this, 'save' ) );

		// Temporary creation of a default scheme
		if ( is_admin() ) {
			if ( ! $this->get_color_scheme() ) {
				// Create one
				$scheme = new Admin_Color_Schemer_Scheme( array(
					'id' => 1,
					'name' => __( 'Test', 'admin-color-schemer' ),
					'base' => '#25282b',
					'highlight' => '#363b3f',
					'notification' => '#69a8bb',
					'button' => '#e14d43',
				) );
				$this->set_option( 'schemes', array( $scheme->id => $scheme->to_array() ) );
			}
		}
	}

	public function admin_init() {
		$schemes = $this->get_option( 'schemes', array() );

		foreach ( $schemes as $scheme ) {
			wp_admin_css_color(
				'admin_color_schemer_' . $scheme['id'],
				$scheme['name'],
				esc_url( $scheme['uri'] ),
				array( $scheme['base'], $scheme['icon'], $scheme['highlight'], $scheme['notification'] ),
				array( 'base' => $scheme['icon'], 'focus' => $scheme['icon_focus'], 'current' => $scheme['icon_current'] )
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
		}

		add_action ( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function updated() {
		include( $this->base . '/templates/updated.php' );
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
		// ignoring $id right now during development
		$schemes = $this->get_option( 'schemes', array() );
		$scheme = array_shift( $schemes );
		if ( $scheme ) {
			return new Admin_Color_Schemer_Scheme( $scheme );
		} else {
			return false;
		}
	}

	public function admin_url() {
		return admin_url( 'tools.php?page=admin-color-schemer' );
	}

	public function save() {
		current_user_can( 'manage_options' ) || die;
		check_admin_referer( self::NONCE );
		$_post = stripslashes_deep( $_POST );
		$scheme = $this->get_color_scheme();

		// @todo: which, if any, of these are required?
		foreach ( array( 'base', 'highlight', 'notification', 'icon' ) as $thing ) {
			if ( isset( $_post[ $thing ] ) ) {
				$scheme->{$thing} = $_post[ $thing ];
			}
		}

		// okay, let's see about getting credentials
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

		// @todo: save into another subdirectory for multiple scheme handling
		$scss_file = $upload_dir . '/scheme.scss';

		// @todo: error handling if this can't be made - needs to be differentiated from already there
		$wp_filesystem->mkdir( $upload_dir );

		// pull in core's default colors.css and scss files if they're not there already
		$core_scss = array( '_admin.scss', '_mixins.scss', '_variables.scss' );
		$admin_dir = ABSPATH . '/wp-admin/css/';

		foreach ( $core_scss as $file ) {
			if ( ! file_exists( $upload_dir . "/{$file}" ) ) {
				if ( ! $wp_filesystem->put_contents( $upload_dir . "/{$file}", $wp_filesystem->get_contents( $admin_dir . 'colors/' . $file, FS_CHMOD_FILE) ) ) {
					// @todo: error that the scheme couldn't be written and redirect
					exit( "Could not copy the core file {$file}." );
				}
			}
		}

		if ( ! file_exists( $upload_dir . "/colors.css" ) ) {
			if ( ! $wp_filesystem->put_contents( $upload_dir . "/colors.css", $wp_filesystem->get_contents( $admin_dir . 'colors.css', FS_CHMOD_FILE) ) ) {
				// @todo: error that the scheme couldn't be written and redirect
				exit( "Could not copy the core file colors.css." );
			}
		}

		$scss = '';

		foreach( array( 'base', 'icon', 'highlight', 'notification' ) as $key ) {
			if ( '' !== $scheme->$key ) {
				$scss .= "\${$key}-color: {$scheme->$key};\n";
			}
		}

		$scss .= "\n\$form-checked: {$scheme->base};";

		$scss .= "\n\n@import 'colors.css';\n@import '_admin.scss';\n";

		// write the custom.scss file
		if ( ! $wp_filesystem->put_contents( $scss_file, $scss, FS_CHMOD_FILE) ) {
			// @todo: error that the scheme couldn't be written and redirect
			exit( 'Could not write custom SCSS file.' );
		}

		// Compile and write!
		require_once( $this->base . '/lib/phpsass/SassParser.php' );
		$sass = new SassParser();
		$css = $sass->toCss( $scss_file );

		$css_file = $upload_dir . '/scheme.css';

		if ( ! $wp_filesystem->put_contents( $css_file, $css, FS_CHMOD_FILE) ) {
			// @todo: error that the compiled scheme couldn't be written and redirect
			exit( 'Could not write compiled CSS file.' );
		}

		// add the URI of the sheet to the settings array
		$scheme->uri = $upload_url . '/scheme.css';

		$this->set_option( 'schemes', array( $scheme->id => $scheme->to_array() ) );
		wp_redirect( $this->admin_url() . '&updated=true' );
		exit;
	}
}
