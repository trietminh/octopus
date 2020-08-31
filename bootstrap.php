<?php
/*
Plugin Name: Octopus Framework
Plugin URI: http://webviet.org/
Description: Octopus Framework - The framework for Octopus theme.
Author: Webviet
Version: 2.2
Author URI: http://webviet.org/
Text Domain: octopus_fw
Domain Path: /languages/
License: GPL v3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OCTOPUS_FW_VERSION', '2.2' );
define( 'OCTOPUS_FW_BASENAME', function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) :
	basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . basename( __FILE__ ) );

global $wp_plugin_paths;
foreach ( $wp_plugin_paths as $dir => $realdir ) {
	if ( strpos( __FILE__, $realdir ) === 0 ) {
		define( 'OCTOPUS_FW_FCPATH', $dir . DIRECTORY_SEPARATOR . basename( __FILE__ ) );
		define( 'OCTOPUS_FW_PATH', rtrim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR );

		break;
	}
}
if ( ! defined( 'OCTOPUS_FW_FCPATH' ) ) {
	/** @noinspection PhpConstantReassignmentInspection */
	define( 'OCTOPUS_FW_FCPATH', __FILE__ );
	/** @noinspection PhpConstantReassignmentInspection */
	define( 'OCTOPUS_FW_PATH', rtrim( dirname( OCTOPUS_FW_FCPATH ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR );
}


require_once( 'Main.php' );
Octopus\Main::install_actions();