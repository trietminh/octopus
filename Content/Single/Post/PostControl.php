<?php

namespace Octopus\Content\Single\Post;

use Octopus\Content\Single\SingleControl;

class PostControl extends SingleControl {
	const CLASS_NAME = 'post';

	function init() {
	}

	/**
	 * Disable default post type
	 *
	 * @return $this
	 */
	public function disable() {
		// remove admin menu
		add_action( 'admin_menu', function () {
			remove_menu_page( 'edit.php' );
		} );

		add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'new-post' );
		}, 999 );

		add_action( 'wp_dashboard_setup', function () {
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		}, 999 );


		return $this;
	}


}