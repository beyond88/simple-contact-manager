<?php
/**
 * Frontend Assets class
 *
 * @package SimpleContactManager\Frontend
 */

namespace SimpleContactManager\Frontend;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Assets
 * Handles frontend asset enqueuing
 */
class Assets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        // Only enqueue if shortcode is present
        global $post;

        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'simple_contact_form' ) ) {
            wp_enqueue_style(
                'scm-frontend-style',
                SCM_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                SCM_VERSION
            );

            wp_enqueue_script(
                'scm-frontend-script',
                SCM_PLUGIN_URL . 'assets/js/frontend.js',
                array( 'jquery' ),
                SCM_VERSION,
                true
            );
        }
    }
}
