<?php

namespace Octopus\Content\Single\Page;

use Octopus\Content\Single\SingleControl;
use Octopus\Settings\Cache\CacheControl;
use Octopus\Helpers;

class PageControl extends SingleControl {
	const CLASS_NAME = 'page';

	function init() {
	}

	/**
	 * Get first page by template file name
	 *
	 * @param string $name Name of template file (ex: template-products.php)
	 * @param bool $cache
	 *
	 * @return bool|mixed
	 */
	public static function get_page_by_template( $name, $cache = true ) {
		$args = array(
			'meta_key'     => '_wp_page_template',
			'meta_value'   => $name,
			'hierarchical' => 0
		);

		if ( $cache ) {
			$page = CacheControl::wp_get( $args );

			if ( false === $page ) {
				$pages = get_pages( $args );
			} else {
				$pages = array( $page );
			}
		} else {
			$pages = get_pages( $args );
		}


		if ( ! empty( $pages ) ) {
			$page            = reset( $pages );
			$page->permalink = Helpers::get_post_permalink( $page );

			if ( $cache && $page !== false ) {
				CacheControl::wp_set( $args, $page );
			}

			return $page;
		}

		return false;
	}
}