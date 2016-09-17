<?php
namespace Octopus\Controller;

class CustomPostType extends Single {

	protected $labels = array();
	protected $settings = array();
	protected $taxonomies = array();

	public function init() {
	}

	protected function register_post_type() {

		if ( ! empty( $this::CLASS_NAME ) && ! post_type_exists( $this::CLASS_NAME ) ) {

			if ( ! isset( $this->labels ) ) {
				$this->labels = array();
			}
			if ( ! isset( $this->settings ) ) {
				$this->settings = array();
			}

			$this->labels = array_merge( array(
				'name'               => sprintf( '%s', $this::PLURAL_NAME ),
				'singular_name'      => sprintf( '%s', $this::NAME ),
				'menu_name'          => sprintf( '%s', $this::PLURAL_NAME ),
				'name_admin_bar'     => sprintf( '%s', $this::NAME ),
				'add_new'            => sprintf( 'Add New %s', $this::NAME ),
				'add_new_item'       => sprintf( 'Add New %s', $this::NAME ),
				'new_item'           => sprintf( 'New %s', $this::NAME ),
				'edit_item'          => sprintf( 'Edit %s', $this::NAME ),
				'view_item'          => sprintf( 'View %s', $this::NAME ),
				'all_items'          => sprintf( 'All %s', $this::PLURAL_NAME ),
				'search_items'       => sprintf( 'Search %s', $this::NAME ),
				'parent_item_colon'  => sprintf( 'Parent %s:', $this::NAME ),
				'not_found'          => sprintf( 'No %s Found:', $this::NAME ),
				'not_found_in_trash' => sprintf( 'No %s found in Trash.', $this::NAME ),
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
			register_post_type( $this::CLASS_NAME, $this->settings );

		}
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_settings() {
		return $this->settings;
	}

}   // EOC

