<?php

namespace Octopus\Content\Single;

use Octopus\Base;

abstract class SingleService extends Base {

	const CLASS_NAME = '';

	protected static $display_name = '';
	protected static $display_singular_name = '';

	public function init() {
	}

	public static function get_class_name() {
		return static::CLASS_NAME;
	}

	public static function get_posts( $args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {

			$args['post_type'] = static::CLASS_NAME;

			$posts = get_posts( $args );

			return $posts;
		}

		return false;
	}

	public static function query_posts( $args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {
			$args['post_type'] = static::CLASS_NAME;

			$posts = query_posts( $args );

			return $posts;
		}

		return false;
	}

	public static function wp_query_posts( $args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {
			$args['post_type'] = static::CLASS_NAME;

			$wp_posts = new \WP_Query( $args );
			if ( ! empty( $wp_posts->posts ) ) {
				$result = [
					'posts'    => $wp_posts->posts,
					'wp_query' => $wp_posts,
					'info'     => [
						'post_count'    => $wp_posts->post_count,
						'current_post'  => $wp_posts->current_post,
						'found_posts'   => $wp_posts->found_posts,
						'max_num_pages' => $wp_posts->max_num_pages,
					]
				];
			} else {
				$result = false;
			}

			return $result;
		}

		return false;
	}

	public static function get_post_type_object() {
		return get_post_type_object( static::CLASS_NAME );
	}

	public static function get_rewrite_slug() {
		$post_type_object = static::get_post_type_object();
		if ( ! empty( $post_type_object->rewrite['slug'] ) ) {
			return $post_type_object->rewrite['slug'];
		}

		return false;
	}

}   // EOC
