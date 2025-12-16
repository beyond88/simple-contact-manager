<?php
/**
 * Submissions Repository class
 *
 * @package SimpleContactManager\Database
 */

namespace SimpleContactManager\Database;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Repository
 * Handles CRUD operations for submissions
 */
class Repository {

    /**
     * Get table name
     *
     * @return string
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'scm_submissions';
    }

    /**
     * Insert a new submission
     *
     * @param array $data Submission data.
     * @return int|false The number of rows inserted, or false on error.
     */
    public function insert( array $data ) {
        global $wpdb;

        $defaults = array(
            'name'            => '',
            'email'           => '',
            'phone'           => '',
            'message'         => '',
            'submission_date' => current_time( 'mysql' ),
            'ip_address'      => $this->get_client_ip(),
            'status'          => 'unread',
        );

        $data = wp_parse_args( $data, $defaults );

        $result = $wpdb->insert(
            $this->get_table_name(),
            array(
                'name'            => sanitize_text_field( $data['name'] ),
                'email'           => sanitize_email( $data['email'] ),
                'phone'           => sanitize_text_field( $data['phone'] ),
                'message'         => sanitize_textarea_field( $data['message'] ),
                'submission_date' => $data['submission_date'],
                'ip_address'      => sanitize_text_field( $data['ip_address'] ),
                'status'          => sanitize_text_field( $data['status'] ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Get all submissions
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_all( array $args = array() ) {
        global $wpdb;

        $defaults = array(
            'orderby' => 'submission_date',
            'order'   => 'DESC',
            'limit'   => 20,
            'offset'  => 0,
            'status'  => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $table_name = $this->get_table_name();

        // Build query
        $sql = "SELECT * FROM {$table_name}";

        // Add status filter
        if ( ! empty( $args['status'] ) ) {
            $sql .= $wpdb->prepare( ' WHERE status = %s', $args['status'] );
        }

        // Add ordering
        $allowed_orderby = array( 'id', 'name', 'email', 'submission_date', 'status' );
        $orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'submission_date';
        $order           = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
        $sql            .= " ORDER BY {$orderby} {$order}";

        // Add limit and offset
        $sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $args['limit'], $args['offset'] );

        return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    }

    /**
     * Get single submission by ID
     *
     * @param int $id Submission ID.
     * @return object|null
     */
    public function get_by_id( int $id ) {
        global $wpdb;

        $table_name = $this->get_table_name();

        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        );
    }

    /**
     * Delete submission by ID
     *
     * @param int $id Submission ID.
     * @return bool
     */
    public function delete( int $id ) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->get_table_name(),
            array( 'id' => $id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Update submission status
     *
     * @param int    $id     Submission ID.
     * @param string $status New status.
     * @return bool
     */
    public function update_status( int $id, string $status ) {
        global $wpdb;

        $result = $wpdb->update(
            $this->get_table_name(),
            array( 'status' => sanitize_text_field( $status ) ),
            array( 'id' => $id ),
            array( '%s' ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Get total count of submissions
     *
     * @param string $status Optional status filter.
     * @return int
     */
    public function get_count( string $status = '' ) {
        global $wpdb;

        $table_name = $this->get_table_name();

        if ( ! empty( $status ) ) {
            return (int) $wpdb->get_var(
                $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE status = %s", $status ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            );
        }

        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    private function get_client_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return $ip;
    }
}
