<?php
/**
 * Admin Menu class
 *
 * @package SimpleContactManager\Admin
 */

namespace SimpleContactManager\Admin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Menu
 * Handles admin menu registration
 */
class Menu {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Register admin menu
     */
    public function register_menu() {
        add_menu_page(
            __( 'Contact Manager', 'simple-contact-manager' ),
            __( 'Contact Manager', 'simple-contact-manager' ),
            'manage_options',
            'simple-contact-manager',
            array( $this, 'render_page' ),
            'dashicons-email-alt',
            30
        );

        add_submenu_page(
            'simple-contact-manager',
            __( 'All Submissions', 'simple-contact-manager' ),
            __( 'All Submissions', 'simple-contact-manager' ),
            'manage_options',
            'simple-contact-manager',
            array( $this, 'render_page' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_simple-contact-manager' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'scm-admin-style',
            SCM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SCM_VERSION
        );

        wp_enqueue_script(
            'scm-admin-script',
            SCM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            SCM_VERSION,
            true
        );

        wp_localize_script(
            'scm-admin-script',
            'scmAdmin',
            array(
                'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
                'deleteNonce'  => wp_create_nonce( 'scm_delete_submission' ),
                'confirmDelete' => __( 'Are you sure you want to delete this submission?', 'simple-contact-manager' ),
            )
        );
    }

    /**
     * Render admin page
     */
    public function render_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        include SCM_PLUGIN_DIR . 'includes/Admin/views/submissions.php';
    }
}
