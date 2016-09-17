<?php
namespace Octopus\Controller;
class Script extends Base {

	protected $enqueued_scripts;
	protected $enqueued_styles;

	public function init() {
	}

	function enqueue_scripts() {
		if ( ! empty( $this->enqueued_scripts ) ) {
			add_action( 'init', function () {
				if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {
					$current_theme = wp_get_theme();
					//var_dump( $this->enqueued_scripts );
					foreach ( $this->enqueued_scripts as $script ) {
						$deps      = isset( $script['deps'] ) ? $script['deps'] : array( 'jquery' );
						$in_footer = ( isset( $script['in_footer'] ) && $script['in_footer'] == false ) ? false : true;
						wp_enqueue_script( $script['name'], $script['src'], $deps, $current_theme->get( 'Version' ), $in_footer );
					}
				}
			}, 50 );    // action
		}
	}

	function enqueue_styles() {
		if ( ! empty( $this->enqueued_styles ) ) {
			add_action( 'wp_enqueue_scripts', function () {
				$current_theme = wp_get_theme();
				foreach ( $this->enqueued_styles as $style ) {
					$deps   = isset( $style['deps'] ) ? $style['deps'] : array();
					$screen = ( $style['screen'] ) ? $style['screen'] : 'all';
					wp_enqueue_style( $style['name'], $style['src'], $deps, $current_theme->get( 'Version' ), $screen );
				}
			}, 50 );    // action
		}
	}

}   // EOC
