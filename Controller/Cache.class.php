<?php
namespace Octopus\Controller;

// @todo test this class
class Cache extends Base {

	protected static $cache_time;
	protected static $cache_group;
	protected static $cache_prefix;

	public function init() {
		self::$cache_time   = apply_filters( 'otp_cache_time', 1 * 60 * 60 ); // 1
		self::$cache_group  = apply_filters( 'otp_cache_group', 'otp_cache' );
		self::$cache_prefix = apply_filters( 'otp_cache_prefix', 'otp_' );


		var_dump('inittttttttttttttttttttt cache');
	}

	public static function transient_set( $key, $data, $expiration = 0 ) {
		$key = self::sanitize_cache_key( $key );

		return set_transient( $key, $data, $expiration );
	}

	public static function transient_get( $key ) {
		$key = self::sanitize_cache_key( $key );

		return get_transient( $key );
	}

	public static function transient_delete( $key ) {
		$key = self::sanitize_cache_key( $key );

		return delete_transient( $key );
	}

	public static function wp_set( $key, $data, $group = '', $expiration = '' ) {
		$key = self::sanitize_cache_key( $key );
		if ( empty( $group ) ) {
			$group = self::$cache_group;
		}
		if ( ! isset( $expiration ) ) {
			$expiration = self::$cache_time;
		}

		return wp_cache_set( $key, $data, $group, $expiration );
	}

	public static function wp_add( $key, $data, $group = '', $expiration = '' ) {
		$key = self::sanitize_cache_key( $key );
		if ( empty( $group ) ) {
			$group = self::$cache_group;
		}
		if ( ! isset( $expiration ) ) {
			$expiration = self::$cache_time;
		}

		return wp_cache_add( $key, $data, $group, $expiration );
	}

	public static function wp_get( $key, $group = '', $force = false ) {
		$key = self::sanitize_cache_key( $key );
		if ( empty( $group ) ) {
			$group = self::$cache_group;
		}

		return wp_cache_get( $key, $group, $force );
	}

	public static function wp_delete( $key, $group ) {
		$key = self::sanitize_cache_key( $key );
		if ( empty( $group ) ) {
			$group = self::$cache_group;
		}

		return wp_cache_delete( $key, $group );
	}

	public static function wp_flush() {
		return wp_cache_flush();
	}

	public static function sanitize_cache_key( $key, $char = 6 ) {
		if ( ! is_string( $key ) || strlen( $key ) > $char ) { // check condition to sanitize

			if ( is_array( $key ) ) {
				$key = serialize( $key );
			} else {
				$key = (string) $key;
			}

			return self::$cache_prefix . substr( md5( $key ), 0, $char );
		}

		return self::$cache_prefix . $key;
	}

}   // EOC

