<?php
namespace Octopus\Controller;

class Sidebar extends Base {

	public $sidebars = array();
	public $widgets = array();

	public function init() {
	}

	function register_sidebars() {
		add_action( 'widgets_init', function () {
			if ( ! empty( $this->sidebars ) ) {
				foreach ( $this->sidebars as $k => $sidebar ) {
					$id                                = register_sidebar( $sidebar );
					$this->sidebars[ $k ]['global_id'] = $id;
				}
			}
		} );
	}

	function register_widgets() {
		add_action( 'widgets_init', function () {
			if ( ! empty( $this->widgets ) ) {
				foreach ( $this->widgets as $k => $widget ) {
					$widget_file = Theme::locate_file( otp_get_setting( 'slug' ) . '/Widgets/' . $widget . '.php', false );
					if ( $widget_file ) {
						require_once( $widget_file );
					}
					if ( class_exists( $widget ) ) {
						register_widget( $widget );
					}
				}
			}
		} );
	}

}   // EOC

