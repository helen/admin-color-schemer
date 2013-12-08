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

	public function admin_menu() {
		$hook = add_management_page( 'Admin Color Schemer', 'Admin Colors', 'manage_options', 'admin-color-schemer', array( $this, 'admin_page' ) );
		add_action( 'load-' . $hook, array( $this, 'load' ) );
	}

	public function load() {
		if ( isset( $_GET['updated'] ) ) {
			add_action( 'admin_notices', array( $this, 'updated' ) );
		}
	}

	public function updated() {
		include( $this->base . '/templates/updated.php' );
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
		foreach ( array( 'base', 'highlight', 'notification', 'button' ) as $thing ) {
			$scheme->{$thing} = $_post[$thing];
		}
		$this->set_option( 'schemes', array( $scheme->id => $scheme->to_array() ) );
		wp_redirect( $this->admin_url() . '&updated=true' );
		exit;
	}
}
