<?php
namespace Octopus\Model;

abstract class Base {

	static public $wpdb;

	protected function __construct( $post_id_or_object ) {
		$this->set_wpdb();
	}

	public function set_wpdb() {
		if ( empty( self::$wpdb ) ) {
			global $wpdb;
			self::$wpdb = $wpdb;
		}
	}
}