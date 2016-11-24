<?php
namespace Octopus\Controller;
class Base {

	private static $_instances = array();

	public $attributes = array();

	public static $_wpdb;

	public static $init_loading = 1;

	final private function __construct() {
	}

	/**
	 * @return Cache|CustomPostType|CustomTaxonomy|Development|Image|Menu|Page|Post|Script|Sidebar|Single|Taxonomy|Theme
	 */
	final public static function get_instance() {
		$class = get_called_class();
		if ( ! isset( self::$_instances[ $class ] ) ) {
			self::$_instances[ $class ] = new $class();
			if ( method_exists( self::$_instances[ $class ], 'init' ) ) {
				self::$_instances[ $class ]->init();
			}
		}

		return self::$_instances[ $class ];
	}

	protected function init() {
		global $wpdb;
		self::$_wpdb = $wpdb;
	}

	final private function __clone() {
	}

	protected function add_attribute( $name, $value, $is_array = true ) {
		if ( $is_array ) {
			$this->attributes[ $name ] = ( isset( $this->attributes[ $name ] ) ? $this->attributes[ $name ] : array() );
			$this->attributes[ $name ] = array_merge( $this->attributes[ $name ], $value );
		} else {
			$this->attributes[ $name ] = $value;
		}
	}

	protected function get_attribute( $name ) {
		if ( isset( $this->attributes[ $name ] ) ) {
			return $this->attributes[ $name ];
		} else {
			return null;
		}
	}
}
