<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Plugin {
	private static $instance;
	private $base;

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
	}

	public function admin_menu() {
		add_management_page( 'Admin Color Schemer', 'Admin Colors', 'manage_options', 'admin-color-schemer', array( $this, 'admin_page' ) );
	}

	public function admin_page() {
		include( $this->base . '/templates/admin-page.php' );
	}
}
