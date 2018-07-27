<?php

namespace Octopus\Content\Single\CustomPostType;

use Octopus\Content\Single\SingleControl;

abstract class CustomPostTypeControl extends SingleControl {

	protected $labels = array();
	protected $settings = array();
	protected $taxonomies = array();

	public function init() {
	}

	public function __get( $name ) {
		return $this->{$name};
	}

	public function set_attr( $labels = array(), $settings = array() ) {
		$this->labels   = (array) $labels;
		$this->settings = (array) $settings;

		return $this;
	}

	public function set_taxonomy( $taxonomies = array() ) {
		$this->taxonomies = (array) $taxonomies;

		return $this;
	}

	public function register() {

		if ( ! empty( $this::CLASS_NAME ) && ! post_type_exists( $this::CLASS_NAME ) ) {

			if ( ! isset( $this->labels ) ) {
				$this->labels = array();
			}
			if ( ! isset( $this->settings ) ) {
				$this->settings = array();
			}

			$this->labels = array_merge( array(
				'name'               => sprintf( __( '%s', 'octopus_fw' ), $this::$display_name ),
				'singular_name'      => sprintf( __( '%s', 'octopus_fw' ), $this::$display_singular_name ),
				'menu_name'          => sprintf( __( '%s', 'octopus_fw' ), $this::$display_name ),
				'name_admin_bar'     => sprintf( __( '%s', 'octopus_fw' ), $this::$display_singular_name ),
				'add_new'            => sprintf( __( 'Add new %s', 'octopus_fw' ), $this::$display_singular_name ),
				'add_new_item'       => sprintf( __( 'Add new %s', 'octopus_fw' ), $this::$display_singular_name ),
				'new_item'           => sprintf( __( 'New %s', 'octopus_fw' ), $this::$display_singular_name ),
				'edit_item'          => sprintf( __( 'Edit %s', 'octopus_fw' ), $this::$display_singular_name ),
				'view_item'          => sprintf( __( 'View %s', 'octopus_fw' ), $this::$display_singular_name ),
				'all_items'          => sprintf( __( 'All %s', 'octopus_fw' ), $this::$display_name ),
				'search_items'       => sprintf( __( 'Search %s', 'octopus_fw' ), $this::$display_singular_name ),
				'parent_item_colon'  => sprintf( __( 'Parent %s:', 'octopus_fw' ), $this::$display_singular_name ),
				'not_found'          => sprintf( __( 'No %s found:', 'octopus_fw' ), $this::$display_singular_name ),
				'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'octopus_fw' ), $this::$display_singular_name ),
			), $this->labels );

			$this->settings = array_merge( array(
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $this::CLASS_NAME ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			), $this->settings );

			if ( ! empty( $this->taxonomies ) ) {
				$this->settings['taxonomies'] = $this->taxonomies;
			}

			$this->settings['labels'] = $this->labels;

			add_action( 'init', function () {
				register_post_type( $this::CLASS_NAME, $this->settings );
			} );
		}

		return $this;
	}

}