<?php

namespace Octopus\Content\Comment;

use Octopus\Base;

class CommentService extends Base {

	function init() {
	}

	public function disable() {
		add_action( 'admin_init', function () {
			$post_types = get_post_types();
			foreach ( $post_types as $post_type ) {
				if ( post_type_supports( $post_type, 'comments' ) ) {
					remove_post_type_support( $post_type, 'comments' );
					remove_post_type_support( $post_type, 'trackbacks' );
				}
			}
		} );

		add_filter( 'comments_open', function () {
			return false;
		}, 20, 2 );

		add_filter( 'pings_open', function () {
			return false;
		}, 20, 2 );

		add_filter( 'comments_array', function ( $comments ) {
			$comments = array();

			return $comments;
		}, 10, 2 );

		add_action( 'admin_menu', function () {
			remove_menu_page( 'edit-comments.php' );
			if ( ! current_user_can( 'manage_options' ) && ! defined( 'DOING_AJAX' ) ) {
				remove_menu_page( 'tools.php' );
				remove_menu_page( 'index.php' );
			}
		} );

		add_action( 'admin_init', function () {
			global $pagenow;
			if ( $pagenow === 'edit-comments.php' ) {
				wp_redirect( admin_url() );
				exit;
			}
		} );

		add_action( 'admin_init', function () {
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		} );

		add_action( 'init', function () {
			if ( is_admin_bar_showing() ) {
				remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
			}
		} );

		add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'comments' );
		}, 999 );

		// disable feed
		add_action( 'do_feed_rss2', function ( $is_comment_feed ) {
			if ( $is_comment_feed ) {
				wp_redirect( '/' );
			}
		}, 1 );
		add_action( 'do_feed_atom_comments', function ( $is_comment_feed ) {
			if ( $is_comment_feed ) {
				wp_redirect( '/' );
			}
		}, 1 );

	}


}