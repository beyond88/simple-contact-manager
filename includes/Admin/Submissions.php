<?php
/**
 * Admin Submissions class
 *
 * @package SimpleContactManager\Admin
 */

namespace SimpleContactManager\Admin;

use SimpleContactManager\Database\Repository;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Submissions
 * Handles admin AJAX actions for submissions
 */
class Submissions {

    /**
     * Repository instance
     *
     * @var Repository
     */
    private $repository;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new Repository();

        // AJAX handlers
        add_action( 'wp_ajax_scm_delete_submission', array( $this, 'ajax_delete_submission' ) );
        add_action( 'wp_ajax_scm_mark_as_read', array( $this, 'ajax_mark_as_read' ) );
    }

    /**
     * AJAX: Delete submission
     */
    public function ajax_delete_submission() {
        // Verify nonce
        if ( ! check_ajax_referer( 'scm_delete_submission', 'nonce', false ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'simple-contact-manager' ),
            ) );
        }

        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'simple-contact-manager' ),
            ) );
        }

        // Get submission ID
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid submission ID.', 'simple-contact-manager' ),
            ) );
        }

        // Delete submission
        $result = $this->repository->delete( $id );

        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( 'Submission deleted successfully.', 'simple-contact-manager' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to delete submission.', 'simple-contact-manager' ),
            ) );
        }
    }

    /**
     * AJAX: Mark submission as read
     */
    public function ajax_mark_as_read() {
        // Verify nonce
        if ( ! check_ajax_referer( 'scm_delete_submission', 'nonce', false ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'simple-contact-manager' ),
            ) );
        }

        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'simple-contact-manager' ),
            ) );
        }

        // Get submission ID
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid submission ID.', 'simple-contact-manager' ),
            ) );
        }

        // Update status
        $result = $this->repository->update_status( $id, 'read' );

        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( 'Submission marked as read.', 'simple-contact-manager' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to update submission.', 'simple-contact-manager' ),
            ) );
        }
    }
}
