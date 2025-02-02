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

        require_once( WP_FIELDS_PATH . 'modules/admin/AdminMenu.php' );
        new \WP_Fields\Admin\AdminMenu();

    }

}

new Plugin();