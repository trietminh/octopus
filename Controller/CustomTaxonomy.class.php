<?php
namespace Octopus\Controller;

class CustomTaxonomy extends Taxonomy {

	protected $labels = array();
	protected $settings = array();
	protected $object_type = array();

	public function init() {
	}

	protected function register_taxonomy() {
		if ( ! empty( $this::CLASS_NAME ) && ! taxonomy_exists( $this::CLASS_NAME ) ) {

			if ( ! isset( $this->labels ) ) {
				$this->labels = array();
			}
			if ( ! isset( $this->settings ) ) {
				$this->settings = array();
			}

			$this->labels = array_merge( array(
				'name'              => sprintf( '%s', $this::PLURAL_NAME ),
				'singular_name'     => sprintf( '%s', $this::NAME ),
				'search_items'      => sprintf( 'Search %s', $this::NAME ),
				'all_items'         => sprintf( 'All %s', $this::PLURAL_NAME ),
				'parent_item'       => sprintf( 'Parent %s:', $this::NAME ),
				'parent_item_colon' => sprintf( 'Parent %s:', $this::NAME ),
				'edit_item'         => sprintf( 'Edit %s', $this::NAME ),
				'update_item'       => sprintf( 'Update %s', $this::NAME ),
				'add_new_item'      => sprintf( 'Add New %s', $this::NAME ),
				'new_item_name'     => sprintf( 'New %s Name', $this::NAME ),
				'menu_name'         => sprintf( '%s', $this::PLURAL_NAME ),
			), $this->labels );

			$this->settings = array_merge( array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $this::CLASS_NAME ),
			), $this->settings );

			$this->settings['labels'] = $this->labels;

			register_taxonomy( $this::CLASS_NAME, $this->object_type, $this->settings );
		}
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_settings() {
		return $this->settings;
	}

}   // EOC

