<?php

namespace Octopus\Content\Taxonomy;

abstract class TaxonomyModel {

	const CLASS_NAME = '';
	public $required_fields = array();

	protected function init() {
	}

	/**
	 * @param $object_id_slug
	 * @param array $args
	 *
	 * @return static|null
	 */
	public static function get( $object_id_slug, $field_names = false, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'output'    => OBJECT,
			'filter'    => 'raw',
			'permalink' => true
		) );

		if ( empty( $object_id_slug ) ) {
			return null;
		}

		$class = get_called_class();

		if ( $object_id_slug instanceof $class ) {    // is this class
			$_object = $object_id_slug;
		} elseif ( $object_id_slug instanceof \WP_Term && ! empty( $object_id_slug->term_id ) ) {
			$_term = get_term( $object_id_slug, static::CLASS_NAME, $args['output'], $args['filter'] );
			if ( ! empty( $_term ) && ! is_wp_error( $_term ) ) {
				$_object = new $class( $_term, $field_names, $args );
			}
		} else {
			if ( is_string( $object_id_slug ) && ! is_numeric( $object_id_slug ) ) {
				$object_id_slug = sanitize_title( $object_id_slug );
				$_term          = get_term_by( 'slug', $object_id_slug, static::CLASS_NAME, $args['output'], $args['filter'] );
			} else {
				$object_id_slug = absint( $object_id_slug );
				$_term          = get_term( $object_id_slug, static::CLASS_NAME, $args['output'], $args['filter'] );
			}

			if ( ! empty( $_term ) && ! is_wp_error( $_term ) ) {
				$_object = new $class( $_term, $field_names, $args );
			}

		}

		if ( empty( $_object ) ) {
			return null;
		}

		if ( $_object instanceof $class && method_exists( $_object, 'init' ) ) {
			$_object->init();
		}

		return $_object;
	}

	protected function __construct( \WP_Term $_term, $custom_fields = false, $args = array() ) {

		if ( $_term && $_term instanceof \WP_Term && ! empty( static::CLASS_NAME ) ) {
			$vars = get_object_vars( $_term );
			foreach ( $vars as $var => $value ) {
				$this->$var = $value;
			}

			// Get Fields
			$this->field_names = ( isset( $this->field_names ) ? $this->field_names : array() );
			$this->field_names = ( $custom_fields ? array_merge( $this->field_names, $custom_fields ) : $this->field_names );
			$this->field_names = array_merge( $this->field_names, $this->required_fields );

			if ( isset( $this->field_names ) ) {
				$this->fields = $this->get_fields( $this->field_names );
			}

			// Get more attributes
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

	/**
	 * Get fields for stuff terms. Support for ACF plugin.
	 *
	 * @param $field_names
	 *
	 * @return array|bool
	 */
	public function get_fields( $field_names ) {
		$fields = array();
		foreach ( $field_names as $field_name ) {
			if ( function_exists( 'get_field' ) ) {
				$fields[ $field_name ] = get_field( $field_name, $this );
			} else {
				$fields[ $field_name ] = get_term_meta( $this->term_id, $field_name );
				if ( 1 == count( $fields[ $field_name ] ) ) {
					$fields[ $field_name ] = reset( $fields[ $field_name ] );
				}
				$fields[ $field_name ] = maybe_unserialize( $fields[ $field_name ] );
			}
		}

		return $fields;
	}

	public function is_root() {
		return empty( $this->parent );
	}

	public function get_parent() {
		if ( ! empty( $this->parent ) ) {
			return static::get( $this->parent );
		}

		return false;
	}

}