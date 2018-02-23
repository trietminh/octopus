<?php

namespace Octopus\Controller;

use Octopus\Main;
use Octopus\Model;

class Single extends Base {

	const CLASS_NAME = '';
	const NAME = '';
	const PLURAL_NAME = '';

	public function init() {
	}

	public static function get_posts( $args = array(), $field_names = array(), $post_args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {
			$args['post_type'] = static::CLASS_NAME;

			$posts = get_posts( $args );
			if ( ! empty( $posts ) ) {
				$result = array();
				foreach ( $posts as $item ) {
					$class_name = "Octopus\\Model\\" . ucfirst( strtolower( static::CLASS_NAME ) );
					if ( Main::is_custom_class_file_exist( $class_name ) ) {
						$result[] = $class_name::get_instance( $item, $field_names, $post_args );
					} else {
						$result[] = Model\Post::get_instance( $item, $field_names, $post_args );
					}
				}
			} else {
				$result = false;
			}

			return $result;
		}

		return false;
	}

	public static function query_posts( $args = array(), $field_names = array(), $post_args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {
			$args['post_type'] = static::CLASS_NAME;

			$posts = query_posts( $args );
			if ( ! empty( $posts ) ) {
				$result = array();
				foreach ( $posts as $item ) {
					$class_name = "Octopus\\Model\\" . ucfirst( strtolower( static::CLASS_NAME ) );
					if ( Main::is_custom_class_file_exist( $class_name ) ) {
						$result[] = $class_name::get_instance( $item, $field_names, $post_args );
					} else {
						$result[] = Model\Post::get_instance( $item, $field_names, $post_args );
					}
				}
			} else {
				$result = false;
			}

			return $result;
		}

		return false;
	}

	public static function wp_query_posts( $args = array(), $field_names = array(), $post_args = array(), $cache = false ) {
		if ( ! empty( static::CLASS_NAME ) && post_type_exists( static::CLASS_NAME ) ) {
			$args['post_type'] = static::CLASS_NAME;

			$wp_posts = new \WP_Query( $args );
			if ( ! empty( $wp_posts->posts ) ) {
				$result = [
					'posts'    => [],
					'wp_query' => $wp_posts,
					'info'     => [
						'post_count'    => $wp_posts->post_count,
						'current_post'  => $wp_posts->current_post,
						'found_posts'   => $wp_posts->found_posts,
						'max_num_pages' => $wp_posts->max_num_pages,
					]
				];
				foreach ( $wp_posts->posts as $item ) {
					$class_name = "Octopus\\Model\\" . ucfirst( strtolower( static::CLASS_NAME ) );
					if ( Main::is_custom_class_file_exist( $class_name ) ) {
						$result['posts'][] = $class_name::get_instance( $item, $field_names, $post_args );
					} else {
						$result['posts'][] = Model\Post::get_instance( $item, $field_names, $post_args );
					}
				}
			} else {
				$result = false;
			}

			return $result;
		}

		return false;
	}

	public static function get_permalink( $post ) {
		$permalink = '';
		if ( ! empty( $post ) && ! empty( $post->post_type ) && ! empty( $post->ID ) && ! empty( $post->post_name ) ) {
			$post->filter = 'sample';
			$permalink    = get_permalink( $post );
		}

		return $permalink;
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
