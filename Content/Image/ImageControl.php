<?php

namespace Octopus\Content\Image;

use Octopus\Base;

class ImageControl extends Base {
	/**
	 * Thumbnail size, the key is: post-thumbnail
	 *
	 * @example array('width' => 200, 'height' => 200, 'crop' => true)
	 * @var array
	 */
	public $post_thumbnail_size = array();

	/**
	 * Image sizes
	 *
	 * @example array(
	 *              'avatar' => array( 'width' => 150, 'height' => 150, 'crop' => true ),
	 *          )
	 * @var array
	 */
	public $image_sizes = array();

	public function init() {
	}

	function improve_thumbnail_upscale() {
		add_filter( 'image_resize_dimensions', function ( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ) {
			if ( ! $crop ) {
				return null;
			} // let the wordpress default function handle this

			// $aspect_ratio = $orig_w / $orig_h;
			$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

			$crop_w = round( $new_w / $size_ratio );
			$crop_h = round( $new_h / $size_ratio );

			$s_x = floor( ( $orig_w - $crop_w ) / 2 );
			$s_y = floor( ( $orig_h - $crop_h ) / 2 );

			return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
		}, 10, 6 );

		return $this;
	}

	function set_post_thumbnail_size( $size_array = array() ) {
		if ( ! empty( $size_array ) ) {
			$this->post_thumbnail_size = $size_array;
			add_action( 'after_setup_theme', function () {
				$size = wp_parse_args( $this->post_thumbnail_size, array(
					'width'  => 0,
					'height' => 0,
					'crop'   => true
				) );

				if ( ! empty( $size['width'] ) && ! empty( $size['height'] ) ) {
					set_post_thumbnail_size( $size['width'], $size['height'], $size['crop'] );
				}

			} );
		}

		return $this;
	}

	function add_image_sizes( $size_array = array() ) {
		if ( ! empty( $size_array ) ) {
			$this->image_sizes = $size_array;
			add_action( 'after_setup_theme', function () {
				$sizes = (array) $this->image_sizes;
				if ( ! empty( $sizes ) ) {
					foreach ( $sizes as $key => $row ) {
						if ( ! empty( $row['width'] ) && ! empty( $row['height'] ) ) {
							$row['crop'] = ( $row['crop'] ) ? true : false;

							add_image_size( $key, $row['width'], $row['height'], $row['crop'] );
						}
						// for checking sizes
						//global $_wp_additional_image_sizes;
						//var_dump( $_wp_additional_image_sizes );
					}
				}
			} );
		}

		return $this;
	}

	static function get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width']       = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height']      = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']        = (bool) get_option( "{$_size}_crop" );
				$sizes[ $_size ]['size_string'] = $sizes[ $_size ]['width'] . 'x' . $sizes[ $_size ]['height'];
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'       => $_wp_additional_image_sizes[ $_size ]['width'],
					'height'      => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'        => $_wp_additional_image_sizes[ $_size ]['crop'],
					'size_string' => $_wp_additional_image_sizes[ $_size ]['width'] . 'x' . $_wp_additional_image_sizes[ $_size ]['height'],
				);
			}
		}

		return $sizes;
	}

	static function get_image_size( $size ) {
		$sizes = self::get_all_image_sizes();

		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		}

		return false;
	}
}