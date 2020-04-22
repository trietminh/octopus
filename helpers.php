<?php

namespace Octopus;

class Helpers {

	public static function get_post_permalink( $post ) {
		$permalink = '';
		if ( ! empty( $post ) && ! empty( $post->post_type ) && ! empty( $post->ID ) && ! empty( $post->post_name ) ) {
			$post->filter = 'sample';
			$permalink    = get_permalink( $post );
		}

		return $permalink;
	}

	public static function locate_theme_file( $file, $is_url = true ) {

		$theme_url        = get_template_directory_uri();
		$theme_path       = get_template_directory();
		$child_theme_url  = get_stylesheet_directory_uri();
		$child_theme_path = get_stylesheet_directory();


		if ( empty( $file ) ) {
			return false;
		}

		if ( $file[0] == '/' || $file[0] == '\\' ) {
			$file = substr( $file, 1 );
		}

		$file_paths = array(
			$child_theme_path . '/' . $file,
			$theme_path . DIRECTORY_SEPARATOR . $file
		);
		$file_urls  = array(
			$child_theme_url . '/' . $file,
			$theme_url . '/' . $file
		);

		foreach ( $file_paths as $k => $path ) {
			if ( file_exists( $path ) ) {
				if ( $is_url ) {
					return $file_urls[ $k ];
				} else {
					return $path;
				}
			}
		}

		return false;
	}

}