<?php

namespace Octopus\Settings\Cache;

use Octopus\Base;

class CacheService extends Base {

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

		return wp_cache_set( $key, $data, $group, $expiration );
	}

	public static function wp_add( $key, $data, $group = '', $expiration = '' ) {
		$key = self::sanitize_cache_key( $key );

		return wp_cache_add( $key, $data, $group, $expiration );
	}

	public static function wp_get( $key, $group = '', $force = false ) {
		$key = self::sanitize_cache_key( $key );

		return wp_cache_get( $key, $group, $force );
	}

	public static function wp_delete( $key, $group ) {
		$key = self::sanitize_cache_key( $key );

		return wp_cache_delete( $key, $group );
	}

	public static function wp_flush() {
		return wp_cache_flush();
	}

	public static function sanitize_cache_key( $key, $char = 6 ) {
		$cache_prefix = apply_filters( 'octp_cache_prefix', 'octp_' );
		if ( ! is_string( $key ) || strlen( $key ) > $char ) { // check condition to sanitize

			if ( is_array( $key ) ) {
				$key = serialize( $key );
			} else {
				$key = (string) $key;
			}

			return $cache_prefix . substr( md5( $key ), 0, $char );
		}

		return $cache_prefix . $key;
	}

}   // EOC