<?php
namespace Octopus\Controller;
class Cart extends Base {

	public static $session_name = 'otp_cart';

	public function init() {
		// start session
		if ( ! session_id() ) {
			session_start();
		}
	}

	public static function get_cart() {
		if ( isset( $_SESSION['otp_cart'] ) && ! empty( $_SESSION['otp_cart'] ) ) {
			return $_SESSION['otp_cart'];
		}

		return array();
	}

	public static function add_to_cart( $object_id, $quantity = 1 ) {

		$object_id = absint( $object_id );
		if ( $object_id <= 0 ) {
			return false;
		}
		$quantity = absint( $quantity );
		$quantity = ( $quantity <= 0 ) ? 1 : $quantity;

		if ( ! isset( $_SESSION['otp_cart'] ) ) {
			$_SESSION['otp_cart'] = array();
		}

		if ( array_key_exists( $object_id, $_SESSION['otp_cart'] ) ) {  // the product has item
			$_SESSION['otp_cart'][ $object_id ] += $quantity;
		} else {
			$_SESSION['otp_cart'][ $object_id ] = $quantity;
		}
		static::validate_cart();

		return true;
	}

	public static function validate_cart() {
		if ( isset( $_SESSION['otp_cart'] ) ) {
			foreach ( $_SESSION['otp_cart'] as $k => $quantity ) {
				if ( $quantity <= 0 ) {
					unset( $_SESSION['otp_cart'][ $k ] );
				}
			}

			if ( empty( $_SESSION['otp_cart'] ) ) {
				unset( $_SESSION['otp_cart'] );
			}
		}
	}

	public static function destroy_cart() {
		if ( isset( $_SESSION['otp_cart'] ) ) {
			unset( $_SESSION['otp_cart'] );
		}
	}

	public static function modify_item( $object_id, $quantity ) {
		if ( empty( $_SESSION['otp_cart'] ) ) {
			return false;
		}

		if ( array_key_exists( $object_id, $_SESSION['otp_cart'] ) ) {  // the product has item
			$quantity = absint( $quantity );
			$quantity = ( $quantity <= 0 ) ? 1 : $quantity;

			$_SESSION['otp_cart'][ $object_id ] = $quantity;
			static::validate_cart();

			return true;
		}

		return false;
	}

	public static function remove_item( $object_id ) {
		if ( empty( $_SESSION['otp_cart'] ) ) {
			return false;
		}

		if ( array_key_exists( $object_id, $_SESSION['otp_cart'] ) ) {  // the product has item
			unset( $_SESSION['otp_cart'][ $object_id ] );
			static::validate_cart();

			return true;
		}

		return false;
	}

}   // EOC
