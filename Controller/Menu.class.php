<?php
namespace Octopus\Controller;

class Menu extends Base {

	public $nav_menus = array();

	public function init() {
	}

	function register_menus() {
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
		$args = apply_filters( 'octopus_nav_menu_args', $args );

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

