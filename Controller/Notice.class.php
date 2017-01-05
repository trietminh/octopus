<?php
namespace Octopus\Controller;
class Notice extends Base {

	protected $wp_error;

	protected $cookie_name;
	protected $cookie_expired;
	protected $cookie_domain;
	protected $cookie_raw_notice;

	public function init() {
		$this->wp_error       = new \WP_Error();
		$this->cookie_name    = apply_filters( 'otp_cookie_notice_name', 'otp_notice' );
		$this->cookie_expired = apply_filters( 'otp_cookie_notice_expire', time() + 60 * 30 );
		$this->cookie_domain  = $_SERVER['HTTP_HOST'];

		$this->cookie_raw_notice = $this->get_raw_cookie();
	}

	function get_raw_cookie() {
		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {

			$result = $_COOKIE[ $this->cookie_name ];
			$this->delete_cookie();

			return $result;
		}

		return '';
	}

	function get_cookie( $code = '' ) {
		if ( ! empty( $this->cookie_raw_notice ) ) {
			$notices = $this->cookie_raw_notice;
			if ( ! empty( $notices ) ) {
				$notices = unserialize( base64_decode( $notices ) );
				if ( empty( $code ) ) {
					return $notices;
				} else {
					return ! empty( $notices[ $code ] ) ? $notices[ $code ] : array();
				}
			}
		}

		return array();
	}

	function set_cookie( $code, $message ) {
		if ( empty( $code ) || empty( $message ) ) {
			return false;
		}

		$notices = (array) $this->get_cookie();

		if ( empty( $notices[ $code ] ) ) {
			$notices[ $code ] = array( $message );
		} else {
			$notices[ $code ][] = $message;
		}

		return setcookie( $this->cookie_name, base64_encode( serialize( $notices ) ), $this->cookie_expired, '/', $this->cookie_domain );
	}

	function delete_cookie() {
		return setcookie( $this->cookie_name, '', - 1, '/', $this->cookie_domain );
	}

	function add_flash_notice( $code, $message ) {
		$this->set_cookie( $code, $message );
	}

	function get_flash_notice_messages( $code = '' ) {
		$messages = $this->get_cookie( $code );

		return $messages;
	}

	function add_notice( $code, $message ) {
		$this->wp_error->add( $code, $message );
	}

	function remove_notice( $code ) {
		$this->wp_error->remove( $code );
	}

	function get_notice_messages( $code = '' ) {
		return $this->wp_error->get_error_messages( $code );
	}

	function get_count_notice_messages( $code = '' ) {
		return count( $this->wp_error->get_error_messages( $code ) );
	}

}   // EOC
