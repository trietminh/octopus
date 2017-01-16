<?php

function otp_get_setting( $name, $allow_filter = true ) {
	// vars
	$r = null;

	// load from bpp if available
	if ( isset( Octopus\Main::$settings[ $name ] ) ) {
		$r = Octopus\Main::$settings[ $name ];
	}

	// filter for 3rd party customization
	if ( $allow_filter ) {
		$r = apply_filters( "octopus/settings/{$name}", $r );
	}

	// return
	return $r;
}


function otp_get_current_url() {

	// vars
	$home = home_url();
	$url  = home_url( $_SERVER['REQUEST_URI'] );

	// explode url (4th bit is the sub folder)
	$bits = explode( '/', $home, 4 );

	// handle sub folder
	if ( ! empty( $bits[3] ) ) {

		$find   = '/' . $bits[3];
		$pos    = strpos( $url, $find );
		$length = strlen( $find );

		if ( $pos !== false ) {
			$url = substr_replace( $url, '', $pos, $length );
		}
	}

	// return
	return $url;
}


function otp_get_fields( $field_key, $post_id = false, $format_value = true ) {
	if ( function_exists( 'get_field' ) ) {
		$result = get_field( $field_key, $post_id, $format_value );

		return $result;
	} else {
		return '';
	}
}

function otp_sanitize_paragraph( $paragraph ) {
	$paragraph = stripslashes_deep( $paragraph );
	$paragraph = balanceTags( wp_kses_post( $paragraph ), true );

	return $paragraph;
}

function otp_sanitize_textarea( $paragraph ) {
	$paragraph = stripslashes_deep( $paragraph );

	return $paragraph;
}

function otp_sanitize_text( $text ) {
	$text = stripslashes_deep( $text );

	return sanitize_text_field( $text );
}

function otp_escape_paragraph( $paragraph ) {
	return balanceTags( wp_kses_post( $paragraph ), true );
}

function otp_escape_textarea( $paragraph ) {
	return esc_textarea( $paragraph );
}

function otp_is_array_full_value( $array ) {
	foreach ( $array as $key => $value ) {
		if ( empty( $value ) ) {
			return false;
		}
	}

	return true;
}

function otp_get_youtube_id( $url ) {
	preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches );

	return $matches[1];
}
