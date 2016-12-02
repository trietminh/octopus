<?php
namespace Octopus\Model;

abstract class Taxonomy extends Base {

	const CLASS_NAME = 'taxonomy';

	protected function init() {
	}

	/**
	 * @param $object_or_id
	 * @param array $args
	 *
	 * @return Taxonomy|bool|null|string
	 */
	public static function get_instance( $object_or_id, $args = array() ) {
		if ( empty( $object_or_id ) ) {
			return false;
		}

		$class = get_called_class();

		if ( $object_or_id instanceof $class ) {    // is this class
			$_object = $object_or_id;
		} elseif ( $object_or_id instanceof \WP_Term && ! empty( $object_or_id->term_id ) ) {
			$_object = new $class( $object_or_id->term_id, $args );
		} else {
			$object_or_id = absint( $object_or_id );
			$_object      = new $class( $object_or_id, $args );
		}

		if ( ! $_object ) {
			return null;
		}

		if ( $_object instanceof $class && method_exists( $_object, 'init' ) ) {
			$_object->init();
		}

		return $_object;
	}

	protected function __construct( $object_or_id, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'output'    => OBJECT,
			'filter'    => 'raw',
			'permalink' => true
		) );

		if ( $object_or_id && ! empty( static::CLASS_NAME ) ) {

			$_term = get_term( $object_or_id, static::CLASS_NAME, $args['output'], $args['filter'] );
			if ( $_term ) {
				$vars = get_object_vars( $_term );
				foreach ( $vars as $var => $value ) {
					$this->$var = $value;
				}

				// Get HTML Title
				$this->esc_name = esc_attr( $this->name );

				// Add Permalink
				if ( $args['permalink'] ) {
					if ( ! empty( $_term->permalink ) ) {
						$this->permalink = $_term->permalink;
					} else {
						$this->permalink = get_term_link( $_term );
					}
				}

			}
		}

		// parent
		parent::__construct( $object_or_id );
	}

	public function is_root() {
		return empty( $this->parent );
	}

	public function get_parent() {
		if ( ! empty( $this->parent ) ) {
			return static::get_instance( $this->parent );
		}

		return false;
	}


}