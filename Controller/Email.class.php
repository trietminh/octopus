<?php
namespace Octopus\Controller;
class Email extends Base {

	static public function send( $args = '' ) {

		$args = wp_parse_args( $args,
			array(
				'from'     => '',
				'sender'   => '',
				'reply-to' => '',
				'to'       => array(),
				'cc'       => array(),
				'bcc'      => array(),
				'subject'  => '',
				'message'  => ''
			) );

		$args['to'] = (array) $args['to'];
		$mail_to    = array();
		foreach ( $args['to'] as $k => $item ) {
			if ( is_email( $item ) ) {
				$mail_to[] = $item;
			}
		}
		if ( empty( $mail_to ) ) {
			return false;
		}

		if ( empty( $args['from'] ) ) {
			$args['from'] = get_option( 'admin_email' );
		}

		if ( empty( $args['sender'] ) ) {
			$args['sender'] = get_bloginfo( 'title' );
		}

		if ( empty( $args['reply-to'] ) ) {
			$args['reply-to'] = $args['sender'];
		}


		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n";
		$headers .= "From: {$args['sender']} <{$args['from']}>" . "\r\n" .
		            "Reply-To: {$args['sender']} <{$args['from']}>" . "\r\n";

		$args['cc'] = (array) $args['cc'];
		foreach ( $args['cc'] as $k => $item ) {
			if ( is_email( $item ) ) {
				$headers .= "CC: <$item>" . "\r\n";
			}
		}

		$args['bcc'] = (array) $args['bcc'];
		foreach ( $args['bcc'] as $k => $item ) {
			if ( is_email( $item ) ) {
				$headers .= "BCC: <$item>" . "\r\n";
			}
		}

		$message = apply_filters( 'otp_mail_content', $args['message'] );

		/*var_dump( $mail_to, $args['subject'], $message, $headers );
		return 0;*/

		return wp_mail( $mail_to, $args['subject'], $message, $headers );
	}

}   // EOC
