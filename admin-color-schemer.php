<?php
/*
Plugin Name: Admin Color Schemer
Plugin URI: http://wordpress.org/plugins/admin-color-schemer/
Description: Create your own admin color schemes, right in the WordPress admin.
Version: 1.0
Author: WordPress Core Team
Author URI: http://wordpress.org/
Text Domain: admin-color-schemer
Domain Path: /languages
*/

/*
Copyright 2013 Helen Hou-Sandí, Mark Jaquith

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

defined( 'WPINC' ) or die;

// Pull in the plugin classes and initialize
include( dirname( __FILE__ ) . '/classes/color-scheme.php' );
include( dirname( __FILE__ ) . '/classes/plugin.php' );
Admin_Color_Schemer_Plugin::get_instance();

// Pull in the version checker and initialize
include( dirname( __FILE__ ) . '/classes/version-check.php' );
Admin_Color_Schemer_Version_Check::get_instance();
