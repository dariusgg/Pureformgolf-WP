<?php
/*
Plugin Name: (mt) MediaTemple System Plugin
Text Domain: mt-system-plugin
Description: System Plugin
Author: Media Temple
Version: 0.4
Plugin URI: http://www.mediatemple.net
Author URI: http://www.mediatemple.net
Copyright 2014 (mt) Media Temple, Inc. All Rights Reserved.
*/

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) ) {
    die( 'Forbidden' );
}

global $wp_filter;

/* some plugin defines */
define('MTSP_URL',		plugins_url().'/mt-system-plugin/');
define('MTSP_VERSION',		'0.3');
// System plugin dir
define( 'MT_SYSTEM_PLUGIN_DIR', trailingslashit ( realpath( dirname( __FILE__ ) ) ) . 'classes/' );

if ( is_ssl() ) {
    define( 'MT_SYSTEM_PLUGIN_URL', trailingslashit( str_ireplace( 'http://', 'https://', MTSP_URL ) ) );
} else {
    define( 'MT_SYSTEM_PLUGIN_URL', trailingslashit( MTSP_URL ) );
}

spl_autoload_register( 'mt_system_plugin_autoload' );

global $gd_api;

$sso_xmlrpc = new GD_Reseller_System_Plugin_Sso_Xmlrpc( 'mt-system-plugin' );
$sso_xmlrpc->set_sso_auth_filter_priority( 21 );
$sso_xmlrpc->set_api( $gd_api );

$ga_xmlrpc = new MT_System_Plugin_Google_Analytics_Xmlrpc( 'mt-system-plugin' );
$ga_xmlrpc->set_api( $gd_api );

$migration_cleanup = new MT_User_Migration_Cleanup( 'mt-system-plugin' );
$migration_cleanup->set_api( $gd_api );

 /**
  * Auto loader for this plugin
  * @param string $className
  */
function mt_system_plugin_autoload( $className ) {
    if ( 0 === stripos( $className, 'GD_Reseller_System_' ) ) {
        $filename = trailingslashit( MT_SYSTEM_PLUGIN_DIR ) . 'class-' . str_replace( '_', '-', strtolower( $className ) ) . '.php';
        require_once( $filename );
    }
    else if ( 0 === stripos( $className, 'MT_System_Plugin_' ) ) {
        $filename = trailingslashit( MT_SYSTEM_PLUGIN_DIR ) . 'class-' . str_replace( '_', '-', strtolower( $className ) ) . '.php';
        require_once( $filename );
    }
    else if ( 0 === stripos( $className, 'MT_User_Migration' ) ) {
        $filename = trailingslashit( MT_SYSTEM_PLUGIN_DIR ) . 'class-' . str_replace( '_', '-', strtolower( $className ) ) . '.php';
        require_once( $filename );
    }
}

/* What to do when the plugin is activated? */
function activate_mtsp() {
}
register_activation_hook(__FILE__, 'activate_mtsp');

/* What to do when the plugin is deactivated? */
function deactivate_mtsp() {
    delete_option('mtsp_web_property_id_head');
    delete_option('mtsp_web_property_id_footer');
}
register_deactivation_hook(__FILE__, 'deactivate_mtsp');
