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

	public static function _parse_job_orderby( $orderby, $allowed_keys = array() ) {

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

	public static function count_jobs( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'employer' => 0,
			'status'   => array( 'publish', 'pending' )
		) );

		$where_string = "";

		if ( ! empty( $args['employer'] ) ) {
			$employer_id = intval( $args['employer'] );
			$where_string .= " AND `employer_id`={$employer_id}";
		}

		if ( ! empty( $args['status'] ) ) {
			if ( is_string( $args['status'] ) ) {
				$str_status = esc_sql( $args['status'] );
				$where_string .= " AND `status`='$str_status' ";
			} elseif ( is_array( $args['status'] ) ) {
				$str_status = implode( "','", $args['status'] );
				$where_string .= " AND `status` IN ('" . $str_status . "') ";
			}
		}

		$sql = " SELECT COUNT(ID)
                     FROM " . $this->tables['jobs'] . "
                     WHERE 1=1
                     " . $where_string;

		return (int) $this->wpdb->get_var( $sql );
	}


}   // EOC

