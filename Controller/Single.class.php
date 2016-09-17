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

}   // EOC
