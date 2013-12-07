<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Plugin {
	private static $instance;

	protected function __construct() {
		self::$instance = $this;
		add_action( 'init', array( $this, 'init' ) );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			new self;
		}
		return self::$instance;
	}

	public function init() {
		load_plugin_textdomain( 'admin-color-schemer', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
		// More hooks here
	}
}
