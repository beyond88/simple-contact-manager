<?php
/**
 * Plugin Name: Simple Contact Manager
 * Plugin URI: https://thebitcraft.com/plugins/simple-contact-manager
 * Description: A simple contact form manager that stores submissions in a custom database table.
 * Version: 1.0.0
 * Author: TheBitCraft
 * Author URI: https://thebitcraft.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-contact-manager
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'SCM_VERSION', '1.0.0' );
define( 'SCM_PLUGIN_FILE', __FILE__ );
define( 'SCM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Composer autoloader
if ( file_exists( SCM_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once SCM_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Main plugin class
 */
final class Simple_Contact_Manager {

    /**
     * Plugin instance
     *
     * @var Simple_Contact_Manager
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Simple_Contact_Manager
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation hook
        register_activation_hook( SCM_PLUGIN_FILE, array( $this, 'activate' ) );
        
        // Deactivation hook
        register_deactivation_hook( SCM_PLUGIN_FILE, array( $this, 'deactivate' ) );

        // Initialize plugin after plugins loaded
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        $database = new SimpleContactManager\Database\Installer();
        $database->create_table();

        // Set activation flag
        update_option( 'scm_activated', true );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain( 'simple-contact-manager', false, dirname( SCM_PLUGIN_BASENAME ) . '/languages' );

        // Initialize admin
        if ( is_admin() ) {
            new SimpleContactManager\Admin\Menu();
            new SimpleContactManager\Admin\Submissions();
        }

        // Initialize frontend
        new SimpleContactManager\Frontend\Shortcode();
        new SimpleContactManager\Frontend\Assets();
    }
}

/**
 * Initialize the plugin
 *
 * @return Simple_Contact_Manager
 */
function simple_contact_manager() {
    return Simple_Contact_Manager::get_instance();
}

// Start the plugin
simple_contact_manager();
