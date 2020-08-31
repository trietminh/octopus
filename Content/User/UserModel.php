<?php

namespace Octopus\Content\User;

class UserModel {
	public $required_fields = array();

	/**
	 * Get instance of the Model
	 *
	 * @param $object_id_login mixed Can be Object, ID or slug
	 * @param  bool  $field_names
	 * @param  array  $post_args
	 *
	 * @return static|bool|null|string
	 */
	public static function get( $object_id_login, $field_names = false, $args = array() ) {

		$args = wp_parse_args( $args, array() );

		if ( empty( $object_id_login ) ) {
			return false;
		}

		$class = get_called_class();

		if ( $object_id_login instanceof $class ) {
			$_object = $object_id_login;
		} elseif ( $object_id_login instanceof \WP_User && ! empty( $object_id_login->ID ) ) {
			$_object = new $class( $object_id_login, $field_names, $args );
		} elseif ( is_string( $object_id_login ) && ! is_numeric( $object_id_login ) ) {
			$object_id_login = sanitize_text_field( $object_id_login );

			$_user = get_user_by( 'login', $object_id_login );

			if ( ! empty( $_user ) ) {
				$_object = new $class( $_user, $field_names, $args );
			}
		} else {
			$object_id_login = absint( $object_id_login );
			$_user           = get_userdata( $object_id_login );
			if ( ! empty( $_user ) ) {
				$_object = new $class( $_user, $field_names, $args );
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

	protected function __construct( \WP_User $_user, $custom_fields = false, $args = array() ) {
		if ( $_user && $_user instanceof \WP_User ) {
			$vars = get_object_vars( $_user );
			foreach ( $vars as $var => $value ) {
				$this->$var = $value;
			}

			$this->wp_user = $_user;

//			var_dump( $this->wp_user );

			// Get Fields
			$this->field_names = ( isset( $this->field_names ) ? $this->field_names : array() );
			$this->field_names = ( $custom_fields ? array_merge( $this->field_names,
				$custom_fields ) : $this->field_names );
			$this->field_names = array_merge( $this->field_names, $this->required_fields );

			if ( isset( $this->field_names ) ) {
				$this->fields = $this->get_fields( $this->field_names );
			}
		}
	}

	/**
	 * Get fields for stuff singles. Support for ACF plugin.
	 *
	 * @param $field_names
	 *
	 * @return array|bool
	 */
	public function get_fields( $field_names ) {
		$fields = array();
		foreach ( $field_names as $field_name ) {
			if ( function_exists( 'get_field' ) ) {
				$fields[ $field_name ] = get_field( $field_name, 'user_' . $this->ID );
			} else {
				$fields[ $field_name ] = get_user_meta( $this->ID, $field_name );
				if ( 1 == count( $fields[ $field_name ] ) ) {
					$fields[ $field_name ] = reset( $fields[ $field_name ] );
				}
				$fields[ $field_name ] = maybe_unserialize( $fields[ $field_name ] );
			}
		}

		return $fields;
	}

}