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

        $app_url = plugin_dir_url(__FILE__) . 'app/build';

        // Enqueue React app JS
        wp_enqueue_script(
            'wp-fields-admin-app',
            $app_url . '/static/js/main.js',
            ['wp-element'], // React dependency
            WP_FIELDS_VERSION,
            true
        );

        // Enqueue React app CSS
        wp_enqueue_style(
            'wp-fields-admin-app',
            $app_url . '/static/css/main.css',
            [],
            WP_FIELDS_VERSION
        );
    }
}
