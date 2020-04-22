<?php

namespace Octopus;

class Main {

	static $settings;

	private function __construct() {
	}

	private function __clone() {
	}

	private function __wakeup() {
	}

	public static function install_plugin() {
		// self::runInstall();
		update_option( 'octopus_framework_activated', 1 );
	}

	public static function uninstall_plugin() {
		update_option( 'octopus_framework_activated', 0 );
	}

	public static function install_actions() {
		register_activation_hook( OCTOPUS_FW_FCPATH, 'Octopus::install_plugin' );
		register_deactivation_hook( OCTOPUS_FW_FCPATH, 'Octopus::uninstall_plugin' );

		// add actions
		add_action( 'init', 'Octopus\Main::init', 10 );
		add_action( 'plugins_loaded', 'Octopus\Main::plugins_loaded' );

		// add filter
	}


	public static function init() {
		// Initial functions here
	}

	public static function plugins_loaded() {
		// set settings
		self::$settings = array(
			'slug'            => basename( dirname( __FILE__ ) ),
			'plugin_path'     => OCTOPUS_FW_PATH,
			'plugin_fcpath'   => OCTOPUS_FW_FCPATH,
			'plugin_basename' => OCTOPUS_FW_BASENAME
		);

		// load text
		load_plugin_textdomain( 'octopus_fw', false, self::$settings['plugin_path'] . 'languages/' );

		// load libs
		spl_autoload_register( 'Octopus\Main::autoload_class' );
		spl_autoload_register( 'Octopus\Main::autoload_theme_class' );
		spl_autoload_register( 'Octopus\Main::autoload_child_theme_class' );
	}

	public static function autoload_class( $class_name ) {
		// project-specific namespace prefix
		$prefix = 'Octopus\\';

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class_name, $len );

		$file = self::$settings['plugin_path'] . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	public static function autoload_theme_class( $class_name ) {

		// project-specific namespace prefix
		$prefix    = 'Octopus\\';
		$theme_dir = trailingslashit( get_template_directory() );

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}
		$relative_class = substr( $class_name, $len );
		$file           = $theme_dir . self::$settings['slug'] . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	public static function autoload_child_theme_class( $class_name ) {

		// project-specific namespace prefix
		$prefix    = 'Octopus\\';
		$theme_dir = trailingslashit( get_stylesheet_directory() );

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}
		$relative_class = substr( $class_name, $len );
		$file           = $theme_dir . self::$settings['slug'] . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';
		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	public static function class2filename( $name ) {
		//Lower case everything
		$name = strtolower( $name );
		//Make alphanumeric (removes all other characters)
		$name = preg_replace( "/[^a-z0-9\s-._]/", "", $name );
		//Clean up multiple dashes or whitespaces
		$name = preg_replace( "/[\s-]+/", " ", $name );
		//Convert whitespaces and underscore to dash
		$name = preg_replace( "/[\s_]/", "-", $name );

		$name = str_replace( 'octopus-', '', $name );

		$name = $name . '.php';

		return $name;
	}

}   // endclass