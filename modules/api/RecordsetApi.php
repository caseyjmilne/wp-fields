<?php

namespace WP_Fields\Api;

use WP_Fields\Api\RecordsetModel; // Include the database operations class.

class RecordsetApi {

    public function __construct() {
        // Register the routes when the REST API is initialized
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    // Register all the custom REST API routes
    public function register_routes() {
        register_rest_route('wp-fields/v1', '/recordsets', [
            'methods' => 'GET',
            'callback' => [$this, 'get_recordsets'],
        ]);

        register_rest_route('wp-fields/v1', '/recordsets', [
            'methods' => 'POST',
            'callback' => [$this, 'create_recordset'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('wp-fields/v1', '/recordsets/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_single_recordset'],
        ]);

        register_rest_route('wp-fields/v1', '/recordsets/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_recordset'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('wp-fields/v1', '/recordsets/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_recordset'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
    }

    // Get all recordsets
    public function get_recordsets(\WP_REST_Request $request) {
        $recordset_db = new RecordsetModel();
        $args = $request->get_params();
        $recordsets = $recordset_db->get_all_recordsets($args);

        return rest_ensure_response($recordsets);
    }

    // Get a single recordset by ID
    public function get_single_recordset(\WP_REST_Request $request) {
        $id = $request->get_param('id');
        $recordset_db = new RecordsetModel();
        $recordset = $recordset_db->get_recordset($id);

        if (empty($recordset)) {
            return new \WP_Error('no_recordset', 'Recordset not found', ['status' => 404]);
        }

        return rest_ensure_response($recordset);
    }

    // Create a new recordset
    public function create_recordset(\WP_REST_Request $request) {
        $post_type = sanitize_text_field($request->get_param('post_type'));
        $position = intval($request->get_param('position'));
        
        if (empty($post_type) || !isset($position)) {
            return new \WP_Error('invalid_data', 'Post type and position are required', ['status' => 400]);
        }
        
        // Create a new recordset entry in the database
        $recordset_db = new RecordsetModel();
        $recordset_id = $recordset_db->create_recordset($post_type, $position);

        // If the recordset creation is successful, create the table
        if ($recordset_id) {
            // Generate the table name based on the naming convention
            global $wpdb;
            $table_name = $wpdb->prefix . "field_" . sanitize_key($post_type) . "_" . $position;

            // Check if the table already exists
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

            if ($table_exists) {
                return rest_ensure_response(['id' => $recordset_id, 'message' => 'Recordset created successfully, table already exists.']);
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

            return rest_ensure_response(['id' => $recordset_id, 'message' => 'Recordset and empty table created successfully']);
        }

        return new \WP_Error('create_failed', 'Failed to create recordset', ['status' => 500]);
    }

    // Update an existing recordset
    public function update_recordset(\WP_REST_Request $request) {
        $id = $request->get_param('id');
        $post_type = sanitize_text_field($request->get_param('post_type'));
        $position = intval($request->get_param('position'));

        if (!$id || !$post_type || !$position) {
            return new \WP_Error('invalid_data', 'ID, post type, and position are required', ['status' => 400]);
        }

        $recordset_db = new RecordsetModel();
        $updated = $recordset_db->update_recordset($id, $post_type, $position);

        if ($updated) {
            return rest_ensure_response(['message' => 'Recordset updated successfully']);
        }

        return new \WP_Error('update_failed', 'Failed to update recordset', ['status' => 500]);
    }

    // Delete a recordset
    public function delete_recordset(\WP_REST_Request $request) {
        $id = $request->get_param('id');

        if (!$id) {
            return new \WP_Error('invalid_id', 'ID is required', ['status' => 400]);
        }

        $recordset_db = new RecordsetModel();
        $deleted = $recordset_db->delete_recordset($id);

        if ($deleted) {
            return rest_ensure_response(['message' => 'Recordset deleted successfully']);
        }

        return new \WP_Error('delete_failed', 'Failed to delete recordset', ['status' => 500]);
    }
}
