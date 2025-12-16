<?php
/**
 * Database Installer class
 *
 * @package SimpleContactManager\Database
 */

namespace SimpleContactManager\Database;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Installer
 * Handles database table creation and management
 */
class Installer {

    /**
     * Table name without prefix
     *
     * @var string
     */
    private $table_name = 'scm_submissions';

    /**
     * Get full table name with prefix
     *
     * @return string
     */
    public function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }

    /**
     * Create the submissions table
     */
    public function create_table() {
        global $wpdb;

        $table_name      = $this->get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            message TEXT NOT NULL,
            submission_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'unread',
            PRIMARY KEY (id),
            KEY email (email),
            KEY submission_date (submission_date),
            KEY status (status)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // Store database version
        update_option( 'scm_db_version', SCM_VERSION );
    }

    /**
     * Drop the submissions table
     */
    public function drop_table() {
        global $wpdb;

        $table_name = $this->get_table_name();
        $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }
}
