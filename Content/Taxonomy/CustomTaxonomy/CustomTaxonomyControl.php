<?php

namespace Octopus\Content\Taxonomy\CustomTaxonomy;

use Octopus\Content\Taxonomy\TaxonomyControl;

class CustomTaxonomyControl extends TaxonomyControl {

	protected $labels = array();
	protected $settings = array();
	protected $object_type = array();

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

	public function set_object( $object_type = array() ) {
		$this->object_type = (array) $object_type;

		return $this;
	}

	public function register() {
		if ( ! empty( $this::CLASS_NAME ) && ! taxonomy_exists( $this::CLASS_NAME ) ) {

			if ( ! isset( $this->labels ) ) {
				$this->labels = array();
			}
			if ( ! isset( $this->settings ) ) {
				$this->settings = array();
			}

			$this->labels = array_merge( array(
				'name'              => sprintf( __( '%s', 'octopus_fw' ), $this::$display_name ),
				'singular_name'     => sprintf( __( '%s', 'octopus_fw' ), $this::$display_singular_name ),
				'search_items'      => sprintf( __( 'Search %s', 'octopus_fw' ), $this::$display_singular_name ),
				'all_items'         => sprintf( __( 'All %s', 'octopus_fw' ), $this::$display_name ),
				'parent_item'       => sprintf( __( 'Parent %s:', 'octopus_fw' ), $this::$display_singular_name ),
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'octopus_fw' ), $this::$display_singular_name ),
				'edit_item'         => sprintf( __( 'Edit %s', 'octopus_fw' ), $this::$display_singular_name ),
				'update_item'       => sprintf( __( 'Update %s', 'octopus_fw' ), $this::$display_singular_name ),
				'add_new_item'      => sprintf( __( 'Add New %s', 'octopus_fw' ), $this::$display_singular_name ),
				'new_item_name'     => sprintf( __( 'New %s Name', 'octopus_fw' ), $this::$display_singular_name ),
				'menu_name'         => sprintf( __( '%s', 'octopus_fw' ), $this::$display_name ),
			), $this->labels );

			$this->settings = array_merge( array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $this::CLASS_NAME ),
			), $this->settings );

			$this->settings['labels'] = $this->labels;

			add_action( 'init', function () {
				register_taxonomy( $this::CLASS_NAME, $this->object_type, $this->settings );
			} );
		}
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_settings() {
		return $this->settings;
	}
}