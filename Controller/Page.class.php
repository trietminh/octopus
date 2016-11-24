<?php
namespace Octopus\Controller;

class Page extends Single {

	const CLASS_NAME = 'page';

	public function init() {

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
			$page = Cache::wp_get( $args );

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
			$page->permalink = self::get_permalink( $page );

			if ( $cache && $page !== false ) {
				Cache::wp_set( $args, $page );
			}

			return $page;
		}

		return false;
	}


}   // EOC