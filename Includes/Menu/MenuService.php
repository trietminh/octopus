<?php

namespace Octopus\Includes\Menu;

use Octopus\Base;

class MenuService extends Base {

	public $nav_menus = array();

	function init() {
		// add class to <li>
		add_filter( 'nav_menu_css_class', array( $this, 'add_li_class' ), 1, 3 );

		// add class to <a>
		add_filter( 'nav_menu_link_attributes', array( $this, 'add_link_class' ), 1, 3 );
	}

	/**
	 * @param  array  $locations  An associative array of menu location slugs (key) and descriptions (according value)*
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

		return $this;
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

	function change_sub_menu_class( $parent_class = '', $submenu_class = '', $menu_locations = [] ) {
		add_filter( 'wp_nav_menu', function ( $menu, $args ) use ( $parent_class, $submenu_class, $menu_locations ) {

			if ( empty( $menu_locations ) || in_array( $args->menu, $menu_locations ) ) {

				if ( ! empty( $parent_class ) ) {
					$menu = preg_replace( '/menu-item-has-children/', $parent_class, $menu );
				}
				if ( ! empty( $submenu_class ) ) {
					$menu = preg_replace( '/ class="sub-menu"/', ' class="' . $submenu_class . '"', $menu );
				}
			}

			return $menu;
		}, 10, 2 );

		return $this;
	}

	function add_li_class( $classes, $item, $args ) {
		if ( isset( $args->li_class ) ) {
			$classes[] = $args->li_class;
		}

		return $classes;
	}

	function add_link_class( $atts, $item, $args ) {
		if ( isset( $args->link_class ) ) {
			$atts['class'] = $args->link_class;
		}

		return $atts;
	}

}   // EOC

