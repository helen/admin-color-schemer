<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Version_Check {
	public static $instance;

	public function __construct() {
		self::$instance = $this;
		add_action( 'init', array( $this, 'init' ) );
	}

	public static function get_instance() {
		return self::$instance;
	}

	public function passes() {
		return version_compare( get_bloginfo( 'version' ), '3.8-RC1', '>=' );
	}

	public function init() {
		if ( ! $this->passes() ) {
  		if ( current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			}
		}
	}

	public function admin_init() {
		deactivate_plugins( plugin_basename( dirname( dirname( __FILE__ ) ) . '/admin-color-schemer.php' ) );
	}

	public function admin_notices() {
		echo '<div class="updated error"><p>' . __('<strong>Admin Color Schemer</strong> requires WordPress 3.8 or higher, and has thus been <strong>deactivated</strong>. Please update your install and then try again!', 'admin-color-schemer' ) . '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}
