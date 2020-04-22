<?php

namespace Octopus\Settings\Script;

use Octopus\Base;

class ScriptService extends Base {

	protected $enqueued_scripts = [];
	protected $enqueued_styles = [];

	function set_version( $version ) {
		$this->version = (string) $version;

		return $this;
	}

	function add_script( $handle, $src, $deps = array(), $ver = '', $in_footer = true ) {

		$this->enqueued_scripts[ $handle ] = [
			'handle'    => $handle,
			'src'       => $src,
			'deps'      => $deps,
			'ver'       => $ver,
			'in_footer' => $in_footer
		];

		return $this;
	}

	function add_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {

		$this->enqueued_styles[ $handle ] = [
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media
		];

		return $this;
	}

	function enqueue_all_scripts() {
		if ( ! empty( $this->enqueued_scripts ) ) {
			add_action( 'init', function () {
				if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {
					foreach ( $this->enqueued_scripts as $script ) {
						wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
					}
				}
			}, 50 );
		}

		return $this;
	}

	function enqueue_all_styles() {

		if ( ! empty( $this->enqueued_styles ) ) {
			add_action( 'wp_enqueue_scripts', function () {
				foreach ( $this->enqueued_styles as $style ) {
					wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );
				}
			}, 50 );
		}

		return $this;
	}

	function enqueue_all() {
		$this->enqueue_all_scripts();
		$this->enqueue_all_styles();

		return $this;
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

