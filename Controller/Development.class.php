<?php
namespace Octopus\Controller;

use Octopus\Main;

class Development extends Base {

	public function init() {
	}

	function change_attachment_base_url( $replace_url = '' ) {
		$replace_url = esc_url( $replace_url );
		if ( ! empty( $replace_url ) ) {
			$this->add_attribute( 'development_replace_url', $replace_url, false );

			add_filter( 'wp_get_attachment_url', function ( $url, $post_id ) {
				$replace_url = $this->get_attribute( 'development_replace_url' );
				$current_url = get_home_url();

				if ( ! empty( $replace_url ) ) {
					$new_url = str_replace( $current_url, $replace_url, $url );

					return $new_url;
				}

				return $url;
			}, 10, 2 );

			add_filter( 'wp_calculate_image_srcset', function ( $sources ) {
				$replace_url = $this->get_attribute( 'development_replace_url' );
				$current_url = get_home_url();

				if ( ! empty( $sources ) ) {
					foreach ( $sources as $k => $item ) {
						$item['url']   = str_replace( $current_url, $replace_url, $item['url'] );
						$sources[ $k ] = $item;
					}
				}

				return $sources;
			}, 10, 1 );

		}
	}

}   // EOC