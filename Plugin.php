<?php

/*
 * Plugin Name: WP Fields
 * Description: WP Fields is a fields system for WordPress that enables you to create structured data.
 * Author: Casey Milne
 * Author URL: https://caseymilne.com 
 * Version: 0.0.1
 */

namespace WP_Fields;

define( 'WP_FIELDS_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_FIELDS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_FIELDS_VERSION', '0.0.1' );

class Plugin {

    public function __construct() {

        // Require PHP classes.
        require_once( WP_FIELDS_PATH . 'modules/admin/AdminMenu.php' );
        require_once( WP_FIELDS_PATH . 'modules/admin/AdminScripts.php' );
        require_once( WP_FIELDS_PATH . 'modules/activation/Activate.php' );
        require_once( WP_FIELDS_PATH . 'modules/utility/PostTypeFetch.php' );

        // Initiate classes that have constructor initiation hooks.
        new \WP_Fields\Admin\AdminMenu();
        new \WP_Fields\Admin\AdminScripts();
    }

    // This method will be called on plugin activation
    public static function activate() {

        $activation = new \WP_Fields\Activation\Activate();
        $activation->create_table();
        
    }

}

// Register the activation hook
register_activation_hook( __FILE__, [ 'WP_Fields\Plugin', 'activate' ] );

new Plugin();
