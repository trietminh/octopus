<?php
namespace Octopus\Controller;

class Db extends Base {

	public static function _parse_order_clause( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'DESC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}

	public static function _parse_orderby_clause( $orderby, $allowed_keys = array() ) {

		if ( $orderby == 'rand' ) {
			$orderby_clause = 'RAND()';
		} else {
			if ( ! empty( $allowed_keys ) ) {
				if ( ! in_array( $orderby, $allowed_keys, true ) ) {
					return false;
				} else {
					$orderby_clause = $orderby;
				}
			} else {
				$orderby_clause = sanitize_sql_orderby( $orderby );
			}
		}

		return $orderby_clause;
	}

	public static function insert( $table, $data ) {
		global $wpdb;
		if ( $wpdb->insert( $table, $data ) ) {
			return $wpdb->insert_id;
		} else {
			return 0;
		}
	}

	public static function is_exist( $table, $id ) {
		global $wpdb;
		$id     = intval( $id );
		$result = $wpdb->get_var( "SELECT ID FROM $table WHERE ID = $id " );

		return ! empty( $result );
	}

	public static function delete( $table, $id ) {
		global $wpdb;
		$id = intval( $id );

		return $wpdb->query( "DELETE FROM $table WHERE ID = $id " );
	}

	public static function update( $table, $id, $data ) {
		global $wpdb;
		$id = intval( $id );

		return $wpdb->update( $table, $data, array( 'ID' => $id ) );
	}

	public static function strictly_update( $table, $id, $data ) {
		$id = intval( $id );
		if ( self::is_exist( $table, $id ) ) {
			self::delete( $table, $id );
		}

		return self::insert( $table, $data );
	}

	public static function count( $table ) {
		global $wpdb;
		$sql = " SELECT COUNT(ID)
                     FROM $table ";

		return (int) $wpdb->get_var( $sql );
	}


}   // EOC