<?php
namespace Octopus\Model;

use Octopus\Controller;

abstract class Single extends Base {

	const CLASS_NAME = 'single';
	public $default_fields_names = array();

	protected function init() {
	}

	/**
	 * @param $object_or_id
	 * @param bool $field_names
	 * @param array $post_args
	 *
	 * @return Single|bool|null|string
	 */
	public static function get_instance( $object_or_id, $field_names = false, $post_args = array() ) {
		if ( empty( $object_or_id ) ) {
			return false;
		}

		$class = get_called_class();

		if ( $object_or_id instanceof $class ) {    // is this class
			$_object = $object_or_id;
		} else {
			$_object = new $class( $object_or_id->ID, $field_names, $post_args );
		}

		if ( ! $_object ) {
			return null;
		}

		if ( $_object instanceof $class && method_exists( $_object, 'init' ) ) {
			$_object->init();
		}

		return $_object;
	}

	protected function __construct( $object_or_id, $field_names = false, $post_args = array() ) {

		$post_args = wp_parse_args( $post_args, array(
			'output'         => OBJECT,
			'filter'         => 'raw',
			'permalink'      => true,
			'image'          => true,
			'default_image'  => false,
			'adjacent'       => false,
			'number_excerpt' => apply_filters( 'otp_number_excerpt', 30 )
		) );

		if ( $object_or_id ) {
			$_post = get_post( $object_or_id, $post_args['output'], $post_args['filter'] );
			if ( $_post ) {
				$vars = get_object_vars( $_post );
				foreach ( $vars as $var => $value ) {
					$this->$var = $value;
				}

				// Get Fields
				$this->field_names = ( isset( $this->field_names ) ? $this->field_names : array() );
				$this->field_names = ( $field_names ? array_merge( $this->field_names, $field_names ) : $this->field_names );
				$this->field_names = array_merge( $this->field_names, $this->default_fields_names );

				if ( isset( $this->field_names ) ) {
					$this->fields = $this->get_fields( $this->field_names );
				}

				// Apply Filters
				$this->display_content = apply_filters( 'the_content', $this->post_content );

				$this->display_excerpt = $this->get_excerpt( $this->post_excerpt, $this->post_content, $post_args['number_excerpt'] );

				// Get HTML Title
				$this->esc_title = esc_attr( $this->post_title );

				// Add Permalink
				if ( $post_args['permalink'] ) {
					$this->permalink = get_permalink( $_post );
				}

				// Get Featured Image
				if ( $post_args['image'] ) {
					$this->obj_image = new Image( $this->ID, false, ( isset( $this->image_size ) ) ? $this->image_size : false );

				}

				// Get adjacent posts
				if ( $post_args['adjacent'] ) {
					$this->adjacent = $this->get_adjacent_links( $this );
				}

			}
		}

		// parent
		parent::__construct( $object_or_id );
	}

	/**
	 * Get fields for stuff singles
	 *
	 * @param $field_names
	 *
	 * @return array|bool
	 */
	public function get_fields( $field_names ) {
		$fields = array();
		foreach ( $field_names as $field_name ) {
			if ( function_exists( 'get_field' ) ) {
				$fields[ $field_name ] = get_field( $field_name, $this->ID );
			} else {
				$fields[ $field_name ] = get_post_meta( $this->ID, $field_name );
				if ( 1 == count( $fields[ $field_name ] ) ) {
					$fields[ $field_name ] = reset( $fields[ $field_name ] );
				}
				$fields[ $field_name ] = maybe_unserialize( $fields[ $field_name ] );
			}
		}

		return $fields;
	}

	/**
	 * Trim excerpt
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function get_excerpt( $post_excerpt, $post_content, $num = 55 ) {
		if ( $post_excerpt && $post_excerpt !== "" ) {
			return wp_trim_words( $post_excerpt, $num );
		}
		$post_excerpt = wp_trim_words( $post_content, $num );

		return strip_shortcodes( wp_strip_all_tags( $post_excerpt ) );
	}

	/**
	 * Get a short 15 word excerpt
	 *
	 * @param $post_excerpt
	 * @param int $length
	 *
	 * @return string
	 */
	public function get_short_excpert( $post_excerpt, $length = 15 ) {
		return wp_trim_words( $post_excerpt, $length );
	}


	/**
	 * Get next and previous posts
	 *
	 * @param $queried_post
	 * @param bool $previous_post
	 * @param bool $next_post
	 *
	 * @return \stdClass
	 */
	public function get_adjacent_links( $queried_post, $previous_post = false, $next_post = false ) {

		global $post;
		$_query_post = $post;
		$post        = $queried_post;

		/**
		 * When in PHP mode, show the next and previous post link, inside
		 */
		$adjacent_posts = new \stdClass();

		$previous_post = ( ! $previous_post ) ? ( ( $previous_post === false ) ? false : get_previous_post() ) : $previous_post;
		$next_post     = ( ! $next_post ) ? ( ( $next_post === false ) ? false : get_next_post() ) : $next_post;


		$adjacent_posts->prev = get_permalink( $previous_post->ID );
		$adjacent_posts->next = get_permalink( $next_post->ID );

		$post = $_query_post;

		return $adjacent_posts;
	}

	public function get_image_tag( $size_name = '', $alt = '', $attr = array() ) {
		$html = "";
		if ( ! empty( $this->obj_image ) && $this->obj_image instanceof Image ) {

			if ( ! empty( $size_name ) && ! empty( $this->obj_image->sizes->{$size_name} ) ) {
				$url = $this->obj_image->sizes->{$size_name};
			} elseif ( ! empty( $this->obj_image->url ) ) {
				$url = $this->obj_image->url;
			} else {
				$url = '';
			}

			if ( ! empty( $url ) ) {
				$alt = empty( $alt ) ?
					"alt='{$this->obj_image->metadata->alt}'" : "alt='$alt'";

				$others = '';
				if ( ! empty( $attr ) ) {
					foreach ( $attr as $k => $val ) {
						$val = str_replace( '%url%', $url, $val );
						$others .= " $k='$val'";
					}
				}

				$html = "<img src='{$url}' $alt $others />";

			}
		}

		return $html;
	}

	public function get_image_tag_place( $size_name = '', $alt = '', $attr = array() ) {
		$img_tag = $this->get_image_tag( $size_name, $alt, $attr );
		if ( empty( $img_tag ) ) {
			$img_size = Controller\Image::get_image_size( $size_name );
			if ( ! empty( $img_size['size_string'] ) ) {
				$url     = 'http://placehold.it/' . $img_size['size_string'] . '?text=No+image';
				$img_tag = "<img src='$url' alt='no-image' />";
			}
		}

		return $img_tag;
	}

	public function get_terms( $taxonomy = '', $args = array(), $query = array() ) {
		if ( empty( $this->ID ) ) {
			return false;
		}


		$query = wp_parse_args( $query, array(
			'single'    => true,
			'permalink' => true
		) );

		$terms = wp_get_post_terms( $this->ID, $taxonomy, $args );

		if ( ! empty( $terms ) ) {

			foreach ( $terms as $term ) {
				if ( $query['permalink'] ) {
					$term->permalink = get_term_link( $term );
				}
				if ( $query['single'] ) {
					return $term;
				}
			}
		}


		return $terms;
	}
}