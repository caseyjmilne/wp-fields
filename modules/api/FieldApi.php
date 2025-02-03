<?php

namespace WP_Fields\Api;

use WP_Fields\Api\FieldModel; // Include the database operations class.

class FieldApi {

    public function __construct() {
        // Register the routes when the REST API is initialized
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    // Register all the custom REST API routes
    public function register_routes() {
        register_rest_route('wp-fields/v1', '/fields', [
            'methods' => 'GET',
            'callback' => [$this, 'get_fields'],
        ]);

        register_rest_route('wp-fields/v1', '/fields', [
            'methods' => 'POST',
            'callback' => [$this, 'create_field'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('wp-fields/v1', '/fields/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_single_field'],
        ]);

        register_rest_route('wp-fields/v1', '/fields/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_field'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('wp-fields/v1', '/fields/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_field'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
    }

    // Get all fields (optionally filtered by recordset_id)
    public function get_fields(\WP_REST_Request $request) {
        $recordset_id = $request->get_param('recordset_id');
        $field_db = new FieldModel();
        $fields = $field_db->get_fields($recordset_id);

        return rest_ensure_response($fields);
    }

    // Get a single field by ID
    public function get_single_field(\WP_REST_Request $request) {
        $id = $request->get_param('id');
        $field_db = new FieldModel();
        $field = $field_db->get_field($id);

        if (empty($field)) {
            return new \WP_Error('no_field', 'Field not found', ['status' => 404]);
        }

        return rest_ensure_response($field);
    }

    // Create a new field
    public function create_field(\WP_REST_Request $request) {
        $recordset_id = intval($request->get_param('recordset_id'));
        $name         = sanitize_text_field($request->get_param('name'));
        $type         = sanitize_text_field($request->get_param('type'));
        $position     = intval($request->get_param('position'));

        if (empty($recordset_id) || empty($name) || empty($type)) {
            return new \WP_Error('invalid_data', 'Recordset ID, name, and type are required', ['status' => 400]);
        }

        $field_db = new FieldModel();
        $field_id = $field_db->create_field($recordset_id, $name, $type, $position);

        if ($field_id) {
            return rest_ensure_response(['id' => $field_id, 'message' => 'Field created successfully']);
        }

        return new \WP_Error('create_failed', 'Failed to create field', ['status' => 500]);
    }

    // Update an existing field
    public function update_field(\WP_REST_Request $request) {
        $id       = intval($request->get_param('id'));
        $name     = sanitize_text_field($request->get_param('name'));
        $type     = sanitize_text_field($request->get_param('type'));
        $position = intval($request->get_param('position'));

        if (empty($id) || empty($name) || empty($type)) {
            return new \WP_Error('invalid_data', 'ID, name, and type are required', ['status' => 400]);
        }

        $field_db = new FieldModel();
        $updated = $field_db->update_field($id, $name, $type, $position);

        if ($updated) {
            return rest_ensure_response(['message' => 'Field updated successfully']);
        }

        return new \WP_Error('update_failed', 'Failed to update field', ['status' => 500]);
    }

    // Delete a field
    public function delete_field(\WP_REST_Request $request) {
        $id = intval($request->get_param('id'));

        if (!$id) {
            return new \WP_Error('invalid_id', 'ID is required', ['status' => 400]);
        }

        $field_db = new FieldModel();
        $deleted = $field_db->delete_field($id);

        if ($deleted) {
            return rest_ensure_response(['message' => 'Field deleted successfully']);
        }

        return new \WP_Error('delete_failed', 'Failed to delete field', ['status' => 500]);
    }
}
