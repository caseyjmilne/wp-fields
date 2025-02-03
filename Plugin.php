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
        require_once( WP_FIELDS_PATH . 'modules/utility/LoadFields.php' );
        require_once( WP_FIELDS_PATH . 'modules/api/RecordsetApi.php' );
        require_once( WP_FIELDS_PATH . 'modules/api/RecordsetModel.php' );
        require_once( WP_FIELDS_PATH . 'modules/api/FieldApi.php' );
        require_once( WP_FIELDS_PATH . 'modules/api/FieldModel.php' );
        require_once( WP_FIELDS_PATH . 'modules/metabox/FieldsMetabox.php' );

        // Initiate classes that have constructor initiation hooks.
        new \WP_Fields\Admin\AdminMenu();
        new \WP_Fields\Admin\AdminScripts();
        new \WP_Fields\Api\RecordsetApi();
        new \WP_Fields\Api\FieldApi();
        new \WP_Fields\Metabox\FieldsMetabox();

    }

    // This method will be called on plugin activation
    public static function activate() {

        $activation = new \WP_Fields\Activation\Activate();
        $activation->run();
        
    }

}

// Register the activation hook
register_activation_hook( __FILE__, [ 'WP_Fields\Plugin', 'activate' ] );

new Plugin();
