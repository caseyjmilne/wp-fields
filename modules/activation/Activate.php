<?php

namespace WP_Fields\Activation;

class Activate {

    // Constructor to register the activation hook
    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'activate']);
    }

    // This method is called on plugin activation
    public function activate() {
        $this->create_table_recordsets();
        $this->create_table_fields();
    }

    // Create the recordsets table
    public function create_table_recordsets() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'field_recordsets';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_type VARCHAR(255) NOT NULL,
            position INT NOT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Create the fields table
    public function create_table_fields() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'field_fields';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            recordset_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            position INT NOT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY recordset_id (recordset_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
