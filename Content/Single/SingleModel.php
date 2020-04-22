<?php

namespace Octopus\Content\Single;

use Octopus\Content\Image\ImageModel;

abstract class SingleModel {

	const CLASS_NAME = '';
	public $required_fields = array();

	public function init() {

	}

	/**
	 * Get instance of the Model
	 *
	 * @param $object_id_slug mixed Can be Object, ID or slug
	 * @param  bool  $field_names
	 * @param  array  $post_args
	 *
	 * @return static|bool|null|string
	 */
	public static function get( $object_id_slug, $field_names = false, $post_args = array() ) {

		$post_args = wp_parse_args( $post_args, array(
			'output'         => OBJECT,
			'filter'         => 'raw',
			'permalink'      => true,
			'image'          => true,
			'default_image'  => false,
			'adjacent'       => false,
			'number_excerpt' => apply_filters( 'octp_number_excerpt', 30 )
		) );

		if ( empty( $object_id_slug ) ) {
			return false;
		}

		$class = get_called_class();

		if ( $object_id_slug instanceof $class ) {
			$_object = $object_id_slug;
		} elseif ( $object_id_slug instanceof \WP_Post && ! empty( $object_id_slug->ID ) ) {
			$_post = get_post( $object_id_slug, $post_args['output'], $post_args['filter'] );

			if ( ! empty( $_post ) && static::CLASS_NAME == $_post->post_type ) {
				$_object = new $class( $_post, $field_names, $post_args );
			}
		} elseif ( is_string( $object_id_slug ) && ! is_numeric( $object_id_slug ) ) {
			$args = array(
				'post_type'      => static::CLASS_NAME,
				'post_status'    => 'publish',
				'posts_per_page' => 1
			);

			$object_id_slug = sanitize_title( $object_id_slug );

			if ( 'page' == $class ) {
				$args['pagename'] = $object_id_slug;
			} else {
				$args['name'] = $object_id_slug;

			}
			$_posts = get_posts( $args );

			if ( ! empty( $_posts ) ) {
				$_post   = reset( $_posts );
				$_object = new $class( $_post, $field_names, $post_args );
			}
		} else {
			$object_id_slug = absint( $object_id_slug );
			$_post          = get_post( $object_id_slug, $post_args['output'], $post_args['filter'] );
			if ( ! empty( $_post ) && static::CLASS_NAME == $_post->post_type ) {
				$_object = new $class( $_post, $field_names, $post_args );
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

	protected function __construct( \WP_Post $_post, $custom_fields = false, $post_args = array() ) {

		if ( $_post && $_post instanceof \WP_Post && ! empty( static::CLASS_NAME ) ) {
			$vars = get_object_vars( $_post );
			foreach ( $vars as $var => $value ) {
				$this->$var = $value;
			}

			// Get Fields
			$this->field_names = ( isset( $this->field_names ) ? $this->field_names : array() );
			$this->field_names = ( $custom_fields ? array_merge( $this->field_names,
				$custom_fields ) : $this->field_names );
			$this->field_names = array_merge( $this->field_names, $this->required_fields );

			if ( isset( $this->field_names ) ) {
				$this->fields = $this->get_fields( $this->field_names );
			}

			// Apply Filters
			$this->display_content = apply_filters( 'the_content', $this->post_content );

			$this->display_excerpt = $this->get_excerpt( $this->post_excerpt, $this->post_content,
				$post_args['number_excerpt'] );

			// Get HTML Title
			$this->esc_title = esc_attr( $this->post_title );

			// Add Permalink
			if ( $post_args['permalink'] ) {
				$this->permalink  = get_permalink( $_post );
				$this->link_title = $this->get_title_with_link( $this->permalink );
			} else {
				$this->link_title = $this->get_title_with_link();
			}

			// Get Featured Image
			if ( $post_args['image'] ) {
				$this->featured_image = new ImageModel( $this->ID, false,
					( isset( $this->image_size ) ) ? $this->image_size : false );
			}

			// Get adjacent posts
			if ( $post_args['adjacent'] ) {
				$this->adjacents = $this->get_adjacent_links( $this );
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
	 * @param  string
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
	 * @param  int  $length
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
	 * @param  bool  $previous_post
	 * @param  bool  $next_post
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

	public function get_image_tag( $size_name = '', $alt = '', $attr = array(), $default_url = '' ) {
		$html = "";
		if ( ! empty( $this->featured_image->url ) && $this->featured_image instanceof ImageModel ) {

			if ( ! empty( $size_name ) && ! empty( $this->featured_image->sizes->{$size_name} ) ) {
				$url = $this->featured_image->sizes->{$size_name};
			} elseif ( ! empty( $this->featured_image->url ) ) {
				$url = $this->featured_image->url;
			} else {
				$url = '';
			}

			$alt = empty( $alt ) ?
				"alt='{$this->featured_image->metadata->alt}'" : "alt='$alt'";

		} elseif ( ! empty( $default_url ) ) {
			$url = $default_url;

			$alt = "alt='$alt'";
		}

		if ( ! empty( $url ) ) {
			$others = '';
			if ( ! empty( $attr ) ) {
				foreach ( $attr as $k => $val ) {
					$others .= " $k='$val'";
				}
			}

			$html = "<img src='{$url}' $alt $others />";
		}

		return $html;
	}

	// @todo get image with place holder
	/*public function get_image_tag_place( $size_name = '', $alt = '', $attr = array() ) {
		$img_tag = $this->get_image_tag( $size_name, $alt, $attr );
		if ( empty( $img_tag ) ) {
			$img_size = Controller\Image::get_image_size( $size_name );
			if ( ! empty( $img_size['size_string'] ) ) {
				$url     = 'http://placehold.it/' . $img_size['size_string'] . '?text=No+image';
				$img_tag = "<img src='$url' alt='no-image' />";
			}
		}

		return $img_tag;
	}*/

	// @todo apply TaxonomyModel here
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

	public function get_publish_date( $date_format = '' ) {
		if ( empty( $this->ID ) ) {
			return '';
		}

		if ( empty( $date_format ) ) {
			$date_format = get_option( 'date_format' );
		}
		$date = new \DateTime( $this->post_date );

		return $date->format( $date_format );
	}

	public function get_title_with_link( $permalink = '#' ) {
		$link = sprintf( '<a href="%s" title="%s">%s</a>',
			$permalink,
			$this->esc_title,
			$this->post_title
		);

		return $link;
	}
}