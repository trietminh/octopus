<?php

namespace Octopus;

class Helper {

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

	public static function setcookie( $name, $value, $expire = 0, $secure = false, $httponly = false ) {
		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure,
				apply_filters( 'otp_cookie_httponly', $httponly, $name, $value, $expire, $secure ) );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}",
				E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}

	public static function get_template( $template_name, $args = array(), $template_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'otp_get_template', $located, $template_name, $args, $template_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );

			return;
		}

		include $located;
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string  $type  admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	public static function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}

		return false;
	}

	public static function site_is_https() {
		return false !== strstr( get_option( 'home' ), 'https:' );
	}

	public static function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}

	public static function format_number( $number, $dec = 0, $dec_point = ',', $sep = '.' ) {
		if ( ! empty( $number ) ) {
			$number = floatval( $number );

			return number_format( $number, $dec, $dec_point, $sep );
		}

		return 0;
	}

	public static function trim_zeros_price( $price, $decimal_separator = ',' ) {
		return preg_replace( '/' . preg_quote( $decimal_separator, '/' ) . '0++$/', '', $price );
	}

	public static function price( $price, $args = array() ) {
		$args = apply_filters(
			'otp_price_args',
			wp_parse_args(
				$args,
				array(
					'currency'           => 'VND',
					'decimal_separator'  => ',',
					'thousand_separator' => '.',
					'decimals'           => 0,
					'price_format'       => '%2$s&nbsp;%1$s',
					'no_tags'            => true
				)
			)
		);

		$unformatted_price = $price;
		$negative          = $price < 0;
		$price             = apply_filters( 'raw_otp_price', floatval( $negative ? $price * - 1 : $price ) );
		$price             = apply_filters( 'formatted_otp_price',
			number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price,
			$args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

		if ( apply_filters( 'otp_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
			$price = self::trim_zeros_price( $price );
		}

		if ( $args['no_tags'] ) {
			$formatted_price = ( $negative ? '-' : '' ) .
			                   sprintf( $args['price_format'], $args['currency'], $price );

			$return = $formatted_price;
		} else {
			$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'],
					'<span class="otp-Price-currencySymbol">' . $args['currency'] . '</span>',
					$price );

			$return = '<span class="otp-Price-amount amount">' . $formatted_price . '</span>';
		}

		/**
		 * Filters the string of price markup.
		 *
		 * @param  string  $return  Price HTML markup.
		 * @param  string  $price  Formatted price.
		 * @param  array  $args  Pass on the args.
		 * @param  float  $unformatted_price  Price as float to allow plugins custom formatting.
		 */
		return apply_filters( 'otp_price', $return, $price, $args, $unformatted_price );
	}

	static public function old( $field, $echo = true ) {
		$result = isset( $_POST[ $field ] ) ? htmlspecialchars( stripslashes_deep( $_POST[ $field ] ) ) : '';

		if ( $echo ) {
			echo $result;
		}

		return $result;
	}

	static public function selected( $field, $current = true, $type = 'selected', $default = false, $echo = true ) {
		if ( isset( $_POST[ $field ] ) ) {
			$selected = $_POST[ $field ];
			if ( (string) $selected === (string) $current ) {
				$result = " $type='$type'";
			} else {
				$result = '';
			}
		} else {
			if ( $default ) {
				$result = " $type='$type'";
			} else {
				$result = '';
			}
		}

		if ( $echo ) {
			echo $result;
		}

		return $result;
	}

	/**
	 * Join a list of string to string using separator
	 *
	 * @param  string  $separator
	 * @param  mixed  ...$strings
	 *
	 * @return string
	 */
	static public function str_join( $separator = ',', ...$strings ) {
		$arr = [];
		foreach ( $strings as $string ) {
			if ( isset( $string ) && '' !== $string ) {
				$arr[] = $string;
			}
		}

		return ! empty( $arr ) ? implode( $separator, $arr ) : '';
	}

}