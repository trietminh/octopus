<?php

namespace Octopus\Settings\Sidebar;

use Octopus\Base;

class SidebarService extends Base {

	function register_sidebar( $args ) {
		add_action( 'widgets_init', function () use ( $args ) {
			register_sidebar( $args );
		} );
	}

}   // EOC

