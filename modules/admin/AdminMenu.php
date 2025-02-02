<?php 

namespace WP_Fields\Admin;

use WP_Fields\Utility\PostTypeFetch; // Import the PostTypeFetch class

class AdminMenu {

    private $postTypeFetch;

    public function __construct() {
        // Initialize the PostTypeFetch class
        $this->postTypeFetch = new PostTypeFetch();

        add_action('admin_menu', function() {
            add_menu_page(
                'Fields',               // Page title
                'Fields',               // Menu title
                'manage_options',       // Capability
                'wp-fields',            // Menu slug
                [$this, 'render_react_app'], // Callback function
                'dashicons-screenoptions',   // Icon
                20                        // Position
            );
        });
    }

    public function render_react_app() {
        echo '<div id="wp-fields-app"></div>';

        // Test output for the PostTypeFetch utility class
        echo '<h2>Test: Post Type Dropdown</h2>';
        $this->postTypeFetch->render();
    }
}
