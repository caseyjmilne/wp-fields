<?php 

namespace WP_Fields\Utility;

use WP_Fields\Api\FieldModel;

class DatabaseOperations {

    // Method to create a table
    public function tableCreate( $post_type, $position ) {
        global $wpdb;
        $table_name = $wpdb->prefix . "field_" . sanitize_key($post_type) . "_" . $position;

        // Check if the table already exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

        if ($table_exists) {
            return 'exists';
        }

        // Create the table for the recordset (empty for now)
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Include WordPress database upgrade function
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    // Method to drop a table
    public function tableDrop( $post_type, $position ) {
        global $wpdb;
        $table_name = $wpdb->prefix . "field_" . sanitize_key($post_type) . "_" . $position;

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

        if (!$table_exists) {
            return 'not_exists'; // Table does not exist
        }

        // Drop the table
        $sql = "DROP TABLE IF EXISTS {$table_name}";
        $wpdb->query($sql);

        return 'dropped';
    }

    function tableRefresh($recordset_id, $table_name) {
        
        global $wpdb;
    
        // Check if the table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
        if (!$table_exists) {
            return 'not_exists'; // Table does not exist
        }
    
        // Get defined fields (new structure)
        $field_model = new FieldModel();
        $fields = $field_model->get_fields($recordset_id); // Assuming this returns an array of stdClass objects with ->name and ->position
    
        // Fetch existing columns
        $existing_columns = $wpdb->get_col("SHOW COLUMNS FROM `$table_name`");
    
        // Prepare desired field names
        $new_columns = array_map(function($field) {
            return $field->name;
        }, $fields);
    
        // Identify columns to add and drop
        $columns_to_add = array_diff($new_columns, $existing_columns);
        $columns_to_drop = array_diff($existing_columns, $new_columns);
    
        // SQL Queries
        $sql = '';
    
        // Add new columns
        foreach ($columns_to_add as $column) {
            $sql .= "ALTER TABLE `$table_name` ADD COLUMN `$column` VARCHAR(255);\n";
        }
    
        // Drop obsolete columns
        foreach ($columns_to_drop as $column) {
            $sql .= "ALTER TABLE `$table_name` DROP COLUMN `$column`;\n";
        }
    
        // Reorder columns (MySQL-specific using MODIFY)
        usort($fields, function($a, $b) {
            return $a->position - $b->position;
        });
    
        $prev_column = null;
        foreach ($fields as $field) {
            if (in_array($field->name, $existing_columns) || in_array($field->name, $columns_to_add)) {
                $after_clause = $prev_column ? "AFTER `$prev_column`" : "FIRST";
                $sql .= "ALTER TABLE `$table_name` MODIFY COLUMN `$field->name` VARCHAR(255) $after_clause;\n";
                $prev_column = $field->name;
            }
        }
    
        // Execute SQL
        if ($sql) {
            $wpdb->query('START TRANSACTION');
            try {
                $queries = explode(";\n", trim($sql));
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $wpdb->query($query);
                    }
                }
                $wpdb->query('COMMIT');
            } catch (Exception $e) {
                $wpdb->query('ROLLBACK');
                return 'error: ' . $e->getMessage();
            }
        }
    
        return 'refreshed';
    }    

}
