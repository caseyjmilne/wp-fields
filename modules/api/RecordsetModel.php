<?php 

namespace WP_Fields\Api;

class RecordsetModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'field_recordsets'; // Table name with prefix
    }

    // Create a new recordset
    public function create_recordset($post_type, $position) {
        global $wpdb;

        // Insert a new recordset into the database
        $result = $wpdb->insert(
            $this->table_name,
            [
                'post_type' => sanitize_text_field($post_type),
                'position'  => intval($position),
                'created'   => current_time('mysql'),
                'updated'   => current_time('mysql'),
            ]
        );

        return $result ? $wpdb->insert_id : false; // Return the ID of the new recordset
    }

    // Get all recordsets
    public function get_all_recordsets($args = []) {
        global $wpdb;

        // Default arguments (you can modify these as needed)
        $defaults = [
            'post_type' => '', // Filter by post type if provided
            'order'     => 'ASC', // Default order
        ];
        $args = wp_parse_args($args, $defaults);

        // Build the query
        $query = "SELECT * FROM $this->table_name";
        if (!empty($args['post_type'])) {
            $query .= $wpdb->prepare(" WHERE post_type = %s", $args['post_type']);
        }
        $query .= " ORDER BY position {$args['order']}";

        return $wpdb->get_results($query); // Return all recordsets
    }

    // Get a specific recordset by ID
    public function get_recordset($id) {
        global $wpdb;

        // Get the recordset by ID
        $query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id);
        return $wpdb->get_row($query); // Return the recordset
    }

    // Update an existing recordset
    public function update_recordset( $id, $position ) {

        global $wpdb;

        // Update the recordset in the database
        $result = $wpdb->update(
            $this->table_name,
            [
                'position'  => intval($position),
                'updated'   => current_time('mysql'),
            ],
            ['id' => intval($id)]
        );

        return $result !== false;

    }

    // Delete a recordset
    public function delete_recordset($id) {
        global $wpdb;

        // Delete the recordset by ID
        $result = $wpdb->delete($this->table_name, ['id' => intval($id)]);

        return $result !== false; // Return whether the deletion was successful
    }
}
