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

            $do = new \WP_Fields\Utility\DatabaseOperations;

            $result = $do->tableCreate( $post_type, $position );

            if( $result === 'exists' ) {
                rest_ensure_response(['id' => $recordset_id, 'message' => 'Recordset created successfully, table already exists.']);
            }
            
            return rest_ensure_response(['id' => $recordset_id, 'message' => 'Recordset and empty table created successfully']);
        }

        return new \WP_Error('create_failed', 'Failed to create recordset', ['status' => 500]);
    }

    // Update an existing recordset
    public function update_recordset(\WP_REST_Request $request) {

        $id = $request->get_param('id');
        $position = intval($request->get_param('position'));

        if ( !$position ) {
            return new \WP_Error('invalid_data', 'Position is required', ['status' => 400]);
        }

        $recordset_db = new RecordsetModel();
        $updated = $recordset_db->update_recordset( $id, $position );

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

        // Delete the recordset from the database
        $recordset_db = new RecordsetModel();
        $recordset = $recordset_db->get_recordset($id);

        if (empty($recordset)) {
            return new \WP_Error('no_recordset', 'Recordset not found', ['status' => 404]);
        }

        // Call tableDrop to remove the associated table
        $post_type = $recordset->post_type;  // Assuming the 'post_type' is stored in the recordset
        $position = $recordset->position;    // Assuming the 'position' is stored in the recordset

        // Initialize the DatabaseOperations class to drop the table
        $do = new \WP_Fields\Utility\DatabaseOperations;
        $result = $do->tableDrop($post_type, $position);

        if ($result === 'not_exists') {
            return new \WP_Error('table_not_found', 'The associated table does not exist', ['status' => 404]);
        }

        // Proceed with deleting the recordset
        $deleted = $recordset_db->delete_recordset($id);

        if ($deleted) {
            return rest_ensure_response(['message' => 'Recordset and associated table deleted successfully']);
        }

        return new \WP_Error('delete_failed', 'Failed to delete recordset', ['status' => 500]);

    }

}
