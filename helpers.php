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

/**
 * @todo move this function to Page controller
 *
 * @param $name
 *
 * @return bool|mixed
 */
function otp_get_page_by_template( $name ) {
	$pages = get_pages( array(
		'meta_key'     => '_wp_page_template',
		'meta_value'   => $name,
		'hierarchical' => 0
	) );
	if ( ! empty( $pages ) ) {
		$page            = reset( $pages );
		$page->permalink = sb_get_permalink( $page );

		return $page;
	} else {
		return false;
	}
}

