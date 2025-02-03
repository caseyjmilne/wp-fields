<?php

namespace WP_Fields\Admin;

class AdminScripts {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts($hook) {
        // Only load scripts on the Fields admin page
        if ($hook !== 'toplevel_page_wp-fields') {
            return;
        }

        // Define the build folder path
        $app_path = WP_FIELDS_PATH . 'modules/admin/app/build/';

        // Use glob to find the correct JS file (main.js or whatever the name is after build)
        $js_files = glob($app_path . 'static/js/*.js');
        $css_files = glob($app_path . 'static/css/*.css');

        // Ensure we found the files, otherwise exit
        if (empty($js_files) || empty($css_files)) {
            return; // Files are missing, you might want to log or handle this
        }

        // Get the latest JS and CSS file (the first one in the array)
        $js_file = array_pop($js_files); // Last element of the array
        $css_file = array_pop($css_files); // Last element of the array

        // Enqueue React app JS
        wp_enqueue_script(
            'wp-fields-admin-app',
            plugin_dir_url(__FILE__) . 'app/build/static/js/' . basename($js_file),
            ['wp-element'], // React dependency
            WP_FIELDS_VERSION,
            true
        );

        // Localize script to pass nonce
        wp_localize_script('wp-fields-admin-app', 'wpApiSettings', array(
            'nonce' => wp_create_nonce('wp_rest') // This generates the nonce for REST API
        ));

        // Enqueue React app CSS
        wp_enqueue_style(
            'wp-fields-admin-app',
            plugin_dir_url(__FILE__) . 'app/build/static/css/' . basename($css_file),
            [],
            WP_FIELDS_VERSION
        );

    }
}
