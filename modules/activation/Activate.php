<?php

namespace WP_Fields\Activation;

class Activate {

    // Constructor to register the activation hook
    public function __construct() {
        // Register the activation hook
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
    }

    // This method is called on plugin activation
    public function activate() {
        // Call the function to create the table
        $this->create_table();
    }

    // Method to create the table
    public function create_table() {
        global $wpdb;

        // Set the table name with WordPress prefix
        $table_name = $wpdb->prefix . 'field_recordsets';

        // SQL to create the table
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_type VARCHAR(255) NOT NULL,
            position INT NOT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Include the necessary WordPress function to handle DB queries
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        // Execute the query to create the table
        dbDelta( $sql );
    }
}
