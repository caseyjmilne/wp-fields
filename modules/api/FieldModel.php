<?php

namespace WP_Fields\Api;

class FieldModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'field_fields';
    }

    // Create a new field
    public function create_field($recordset_id, $name, $type, $position) {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            [
                'recordset_id' => intval($recordset_id),
                'name'         => sanitize_text_field($name),
                'type'         => sanitize_text_field($type),
                'position'     => intval($position),
                'created'      => current_time('mysql'),
                'updated'      => current_time('mysql')
            ],
            [
                '%d', '%s', '%s', '%d', '%s', '%s'
            ]
        );

        if ($result) {
            return $wpdb->insert_id; // Return the new field ID
        }

        return false;
    }

    // Get all fields (optionally filter by recordset_id)
    public function get_fields($recordset_id = null) {
        
        global $wpdb;

        if ($recordset_id) {
            $query = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE recordset_id = %d ORDER BY position ASC", $recordset_id);
            return $wpdb->get_results($query);
        }

        return $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY recordset_id, position ASC");
    
    }

    // Get a single field by ID
    public function get_field($id) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id);
        return $wpdb->get_row($query, ARRAY_A);
    }

    // Update an existing field
    public function update_field($id, $name, $type, $position) {
        global $wpdb;

        $result = $wpdb->update(
            $this->table_name,
            [
                'name'     => sanitize_text_field($name),
                'type'     => sanitize_text_field($type),
                'position' => intval($position),
                'updated'  => current_time('mysql')
            ],
            ['id' => intval($id)],
            ['%s', '%s', '%d', '%s'],
            ['%d']
        );

        return $result !== false;
    }

    // Delete a field
    public function delete_field($id) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            ['id' => intval($id)],
            ['%d']
        );

        return $result !== false;
    }
}
