<?php
namespace Octopus\Controller;
class Cart2 extends Base {

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

	public static function get_object_key( $object_id ) {
		$object_id = sanitize_key( $object_id );
		$cart      = self::get_cart();
		if ( ! empty( $cart ) ) {
			foreach ( $cart as $k => $row ) {
				if ( $object_id == $row['id'] ) {
					return $k;
				}
			}
		}

		return false;
	}

	public static function add_to_cart( $object_id, $quantity = 1 ) {

		$object_id = sanitize_key( $object_id );
		if ( empty( $object_id ) ) {
			return false;
		}
		$quantity = absint( $quantity );
		$quantity = ( $quantity <= 0 ) ? 1 : $quantity;

		if ( ! isset( $_SESSION['otp_cart'] ) ) {
			$_SESSION['otp_cart'] = array();
		}

		$object_key = self::get_object_key( $object_id );

		if ( $object_key !== false ) {  // the product has item
			$_SESSION['otp_cart'][ $object_key ]['quantity'] += $quantity;
		} else {
			array_push( $_SESSION['otp_cart'], array( 'id' => $object_id, 'quantity' => $quantity ) );
		}
		static::validate_cart();

		return true;
	}

	public static function validate_cart() {
		if ( isset( $_SESSION['otp_cart'] ) ) {
			foreach ( $_SESSION['otp_cart'] as $k => $item ) {
				if ( $item['quantity'] <= 0 ) {
					unset( $_SESSION['otp_cart'][ $k ] );
				}
			}

			if ( empty( $_SESSION['otp_cart'] ) ) {
				unset( $_SESSION['otp_cart'] );
			} else {
				$_SESSION['otp_cart'] = array_values( $_SESSION['otp_cart'] );
			}
		}
	}

	public static function destroy_cart() {
		if ( isset( $_SESSION['otp_cart'] ) ) {
			unset( $_SESSION['otp_cart'] );

			return true;
		}

		return false;
	}

	public static function modify_item( $object_id, $quantity ) {
		if ( empty( $_SESSION['otp_cart'] ) ) {
			return false;
		}

		$object_key = self::get_object_key( $object_id );

		if ( $object_key !== false ) {  // the product has item
			$quantity = absint( $quantity );
			$quantity = ( $quantity <= 0 ) ? 1 : $quantity;

			$_SESSION['otp_cart'][ $object_key ]['quantity'] = $quantity;
			static::validate_cart();

			return true;
		}

		return false;
	}

	public static function remove_item( $object_id ) {
		if ( empty( $_SESSION['otp_cart'] ) ) {
			return false;
		}

		$object_key = self::get_object_key( $object_id );

		if ( $object_key !== false ) {  // the product has item
			unset( $_SESSION['otp_cart'][ $object_key ] );
			static::validate_cart();

			return true;
		}

		return false;
	}

}   // EOC
