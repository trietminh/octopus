<?php

namespace Octopus\Content\Taxonomy;

use Octopus\Base;
use Octopus\Includes\Cache\CacheService;

abstract class TaxonomyService extends Base {
	const CLASS_NAME = '';
	protected static $display_name;
	protected static $display_singular_name;

	public function init() {
	}

	public static function get_class_name() {
		return static::CLASS_NAME;
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

			if ( $cache ) {
				$terms = CacheService::wp_get( $args );
				if ( false === $terms ) {
					$terms = get_terms( $args );
				}
			} else {
				$terms = get_terms( $args );
			}

			if ( ! empty( $terms ) && $args['permalink'] ) {
				foreach ( $terms as $k => $item ) {
					$item->permalink = get_term_link( $item );
					$terms[ $k ]     = $item;
				}
			}

			if ( $cache && $terms !== false ) {
				CacheService::wp_set( $args, $terms );
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

			if ( $cache ) {
				$terms = CacheService::wp_get( $args );
				if ( false === $terms ) {
					$terms = get_terms( $args );
				}
			} else {
				$terms = get_terms( $args );
			}

			if ( ! empty( $terms ) && $args['permalink'] ) {
				foreach ( $terms as $k => $item ) {
					$item->permalink = get_term_link( $item );
					$terms[ $k ]     = $item;
				}
			}

			if ( $cache && $terms !== false ) {
				CacheService::wp_set( $args, $terms );
			}

			return $terms;
		}

		return false;
	}
}