<?php

namespace WP_Fields\Utility;

class LoadFields {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get all fields for a given post type
     *
     * @param string $post_type The post type to find fields for.
     * @return array List of fields related to the post type.
     */
    public function get_fields_by_post_type($post_type) {
        $recordsets_table = $this->wpdb->prefix . 'field_recordsets';
        $fields_table = $this->wpdb->prefix . 'field_fields';

        $query = $this->wpdb->prepare("
            SELECT 
                f.id AS field_id,
                f.name AS field_name,
                f.type AS field_type,
                f.position AS field_position,
                r.id AS recordset_id,
                r.post_type
            FROM 
                $recordsets_table AS r
            INNER JOIN 
                $fields_table AS f ON r.id = f.recordset_id
            WHERE 
                r.post_type = %s
            ORDER BY 
                f.position ASC, f.id ASC
        ", $post_type);

        $results = $this->wpdb->get_results($query, ARRAY_A);

        return $results;
    }
}
