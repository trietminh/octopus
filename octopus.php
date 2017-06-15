<?php
/*
Plugin Name: Octopus Framework
Plugin URI: http://webviet.org/
Description: Octopus Framework - The framework for Octopus theme.
Author: Webviet
Version: 1.0.1
Author URI: http://webviet.org/
Text Domain: octopus
Domain Path: /languages/
License: GPL v3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OCTOPUS_VERSION', '1.0.0' );
define( 'OCTOPUS_BASENAME', function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) :
	basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

global $wp_plugin_paths;
foreach ( $wp_plugin_paths as $dir => $realdir ) {
	if ( strpos( __FILE__, $realdir ) === 0 ) {
		define( 'OCTOPUS_FCPATH', $dir . '/' . basename( __FILE__ ) );
		define( 'OCTOPUS_PATH', trailingslashit( $dir ) );
		break;
	}
}
if ( ! defined( 'OCTOPUS_FCPATH' ) ) {
	/** @noinspection PhpConstantReassignmentInspection */
	define( 'OCTOPUS_FCPATH', __FILE__ );
	/** @noinspection PhpConstantReassignmentInspection */
	define( 'OCTOPUS_PATH', trailingslashit( dirname( OCTOPUS_FCPATH ) ) );
}

require_once( 'helpers.php' );
require_once( 'Main.class.php' );
Octopus\Main::install_actions();