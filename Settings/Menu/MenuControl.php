<?php

namespace Octopus\Settings\Menu;

use Octopus\Base;

class MenuControl extends Base {

	public $nav_menus = array();

	/**
	 * @param array $locations An associative array of menu location slugs (key) and descriptions (according value)*
	 *
	 * @return $this
	 */
	function set_nav_menus( $locations ) {
		$this->nav_menus = $locations;

		return $this;
	}

	function add_nav_menu( $location, $description ) {
		if ( isset( $this->nav_menus ) ) {
			$this->nav_menus = array_merge( $this->nav_menus, [ $location => $description ] );
		} else {
			$this->set_nav_menus( [ $location => $description ] );
		}

		return $this;
	}

	function register() {
		add_action( 'after_setup_theme', function () {
			if ( ! empty( $this->nav_menus ) ) {
				register_nav_menus( $this->nav_menus );
			}
		} );
	}

	static function show_menu( $args = array() ) {
		$defaults = array(
			'menu'            => '',
			'container'       => false,
			'container_class' => '',
			'container_id'    => '',
			'menu_class'      => 'menu',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => false,
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'depth'           => 0,
			'walker'          => '',
			'theme_location'  => ''
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'octp_nav_menu_args', $args );

		if ( $args['echo'] ) {
			wp_nav_menu( $args );
		} else {
			return wp_nav_menu( $args );
		}
	}

	static function get_menu_by_location( $location ) {
		if ( empty( $location ) ) {
			return false;
		}

		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		$menu_obj = get_term( $locations[ $location ], 'nav_menu' );

		return $menu_obj;
	}

}   // EOC

