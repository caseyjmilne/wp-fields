<?php

namespace WP_Fields\Metabox;

use WP_Fields\Utility\LoadFields;  // Import the LoadFields utility class.

class FieldsMetabox {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // Hook into the post edit screen
        add_action('add_meta_boxes', [$this, 'add_field_metabox']);
        add_action('save_post', [$this, 'save_fields'], 10, 2);
    }

    /**
     * Add the custom metabox to the post edit screen.
     */
    public function add_field_metabox() {
        // Get the current post type
        $post_type = get_post_type();

        // Check if the post type has fields using the LoadFields utility
        $fields = $this->get_fields_by_post_type($post_type);

        if ($fields) {
            add_meta_box(
                'wp_fields_metabox', // ID
                'Field Data', // Title
                [$this, 'render_metabox'], // Callback to render the metabox
                $post_type, // Post type
                'normal', // Context
                'high', // Priority
                ['fields' => $fields] // Pass fields as context
            );
        }
    }

    /**
     * Get fields for a given post type using the LoadFields utility.
     */
    private function get_fields_by_post_type($post_type) {
        // Use the LoadFields utility to fetch fields for the given post type
        $load_fields = new LoadFields();
        return $load_fields->get_fields_by_post_type($post_type);
    }

    /**
     * Render the fields inside the metabox as text input fields.
     */
    public function render_metabox($post, $metabox) {

        $fields = $metabox['args']['fields']; // Retrieve the fields

        echo '<pre>';
        var_dump($fields);
        echo '</pre>';

        echo '<table class="form-table">';
        foreach ($fields as $field) {
            // Retrieve the current value of the field
            $field_value = get_post_meta($post->ID, '_wp_field_' . $field['field_name'], true);

            echo '<tr>';
            echo '<th><label for="wp_field_' . esc_attr($field['id']) . '">' . esc_html($field['name']) . '</label></th>';
            echo '<td><input type="text" id="wp_field_' . esc_attr($field['field_name']) . '" name="wp_field_' . esc_attr($field['field_name']) . '" value="' . esc_attr($field_value) . '" class="field-text-input"></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Save the fields when the post is saved.
     */
    public function save_fields($post_id, $post) {
        // Verify if this is an auto-save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check if it's the correct post type
        if ('post' !== $post->post_type) {
            return $post_id;
        }

        // Loop through each field and save its value
        $fields = $this->get_fields_by_post_type($post->post_type);

        foreach ($fields as $field) {
            $field_value = isset($_POST['wp_field_' . $field['field_name']]) ? sanitize_text_field($_POST['wp_field_' . $field['field_name']]) : '';
            update_post_meta($post_id, '_wp_field_' . $field['field_name'], $field_value);
        }

        return $post_id;
    }
}
