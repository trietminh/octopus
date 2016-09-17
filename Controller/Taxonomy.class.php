<?php
namespace Octopus\Controller;
class Taxonomy extends Base {

	const CLASS_NAME = '';
	const NAME = '';
	const PLURAL_NAME = '';

	public function init() {
	}

	public static function get_top_terms( $args = array(), $cache = true ) {
		if ( ! empty( static::CLASS_NAME ) ) {
			$args = wp_parse_args( $args, array(
				'taxonomy'   => static::CLASS_NAME,
				'permalink'  => true,
				'parent'     => 0,
				'orderby'    => 'id',
				'order'      => 'ASC',
				'hide_empty' => false,
			) );

			$terms = get_terms( $args );

			if ( ! empty( $terms ) && $args['permalink'] ) {
				foreach ( $terms as $k => $item ) {
					$item->permalink = get_term_link( $item );
					$terms[ $k ]     = $item;
				}
			}

			return $terms;
		}

		return false;
	}

	public static function list_terms_to_string( $terms ) {
		if ( ! empty( $terms ) ) {
			$list = array();
			foreach ( $terms as $term ) {
				if ( ! empty( $term->permalink ) ) {
					$list[] = sprintf( '<a href="%s" title="%s">%s</a>', $term->permalink, esc_attr( $term->name ), $term->name );
				} else {
					$list[] = sprintf( '%s', $term->name );
				}
			}

			return implode( ', ', $list );
		}

		return '';
	}

	public static function get_terms( $args = array(), $cache = true ) {
		if ( ! empty( static::CLASS_NAME ) ) {
			$args = wp_parse_args( $args, array(
				'taxonomy'   => static::CLASS_NAME,
				'permalink'  => true,
				'orderby'    => 'id',
				'order'      => 'ASC',
				'hide_empty' => false,

			) );

			$terms = get_terms( $args );

			if ( ! empty( $terms ) && $args['permalink'] ) {
				foreach ( $terms as $k => $item ) {
					$item->permalink = get_term_link( $item );
					$terms[ $k ]     = $item;
				}
			}

			return $terms;
		}

		return false;
	}

}   // EOC
