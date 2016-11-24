<?php
/*
Plugin Name: System Plugin
Description:
Author:
Plugin URI:
Author URI:
Version:
Text Domain: gd_system
Domain Path: /gd-system-plugin/lang
*/

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( ! defined( 'ABSPATH' ) ) {

	die( 'Forbidden' );

}

if ( defined( 'PHPUNIT_PLUGIN_TEST' ) && PHPUNIT_PLUGIN_TEST ) {

	return;

}

// System plugin dir
define( 'GD_SYSTEM_PLUGIN_DIR', trailingslashit ( realpath( dirname( __FILE__ ) ) ) . 'gd-system-plugin/' );

// GD system url
// @codeCoverageIgnoreStart
if ( is_ssl() ) {
	define( 'GD_SYSTEM_PLUGIN_URL', trailingslashit( str_ireplace( 'http://', 'https://', WPMU_PLUGIN_URL ) ) );
} else {
	define( 'GD_SYSTEM_PLUGIN_URL', trailingslashit( WPMU_PLUGIN_URL ) );
}
// @codeCoverageIgnoreEnd

// Load the language
load_muplugin_textdomain( 'gd_system', 'gd-system-plugin/lang' );

// Register the autoloader
spl_autoload_register( 'gd_system_autoload' );

// Load the config lib
$gd_system_config = new GD_System_Plugin_Config();

// Set the WP101 key
$__conf = $gd_system_config->get_config();
if ( is_array( $__conf ) && array_key_exists( 'wp101_key', $__conf ) && is_string( $__conf['wp101_key'] ) ) {
	define( 'GD_WP101_API_KEY', $__conf['wp101_key'] );
}

// WP-CLI does not override this particular superglobal, so we will do it here
// This will prevent CLI notices from being thrown during WP_Async_Task
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$_SERVER['SERVER_ADDR'] = '127.0.0.1';
}

// Stop 404 loops on images
$gd_system_404s = new GD_System_Plugin_404();

// Load the logging lib
$gd_system_logger = new GD_System_Plugin_Logger();

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {

	// Load the admin-menu helper
	$gd_admin_menu = new GD_System_Plugin_Admin_Menu();

	// Load the custom dashboard widgets
	$gd_dashboard_widgets = new GD_System_Plugin_Dashboard_Widgets();

}

// Load the admin page
$gd_admin_page = new GD_System_Plugin_Admin_Page();

// Load the purge helper
$GLOBALS['gd_cache_purge'] = new GD_System_Plugin_Cache_Purge();

// Load the message helper
$gd_messages = new GD_System_Plugin_Messages();

// Handle commands sent from Go Daddy to this WordPress site
$gd_command_controller = new GD_System_Plugin_Command_Controller();

// Prevent blacklisted plugins from being installed
$gd_system_blacklist = new GD_System_Plugin_Blacklist();

// Required to run on `init` in order to check for WPEM
add_action( 'init', function() {

	// Check some important metrics
	$gd_system_metrics = new GD_System_Plugin_Metrics();

} );

// Handle communication with GD system API
$gd_api = new GD_System_Plugin_API();

// Auto upgrade all the things
$gd_auto_upgrades = new GD_System_Plugin_Auto_Upgrades();

// Support any HTML rewrites necessary
$gd_html_rewrite = new GD_System_Plugin_Output_Rewrite();

// Support multiple domains during a domain swing
$gd_domain_changer = new GD_System_Plugin_Domain_Changer();

// Support SSL integration
$gd_ssl = new GD_System_Plugin_SSL();

// Urge users not to use a temporary CNAME (except on staging sites)
if ( ! gd_is_staging_site() && gd_is_temp_domain() ) {

	$gd_cname = new GD_System_Plugin_CName();

}

// Custom CLI command for cron events
if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'Cron_Event_Command' ) ) {

	$gd_cli_cron = new GD_System_Plugin_Cron_Event_Command();

}

// Load any hotfixes
$gd_hotfixes = new GD_System_Plugin_Hotfixes();

// Pointers
$gd_pointers = new GD_System_Plugin_Pointers();

if ( gd_is_temp_domain() ) {

	$gd_temp_domain = new GD_System_Plugin_Temp_Domain();

}

// Array of bundled plugins to load
$bundled_plugins = array(
	'limit-login-attempts/limit-login-attempts.php' => true,
	'wp-easy-mode/wp-easy-mode.php' => gd_is_wpem_enabled(),
	'gd-admin-color-scheme/gd-admin-color-scheme.php' => ( ! gd_is_reseller() && ! gd_is_mt() ),
);

// Load bundled plugins
foreach ( $bundled_plugins as $plugin_basename => $enabled ) {

	if ( $enabled ) {

		gd_system_maybe_load_plugin( $plugin_basename );

	}

}

// Ensure that batcache's cache group labeled as persistent.  This ensures that
// when we purge a URL, it actually happens.
if ( isset( $batcache ) ) {
	$batcache->configure_groups();
}

/**
 * Filter to return our own "die" function (instead of _default_wp_die_handler)
 * @return string
 */
function gd_system_die_handler() {
	return 'gd_system_die' ;
}

// @codeCoverageIgnoreStart
/**
 * Die, but remove your filter first.  This abstraction is necessary for compatibility
 * with both wordpress AND the unit tests.  This is ignored by code coverage because
 * it's entirely unreachable without very dark magic.
 * @return void
 */
function gd_system_die() {
	die();
}

/**
 * Autoload any GD System Plugin classes
 * Code coverage doesn't reach here, even though this code *has* to be executed.
 * @param string $className
 */
function gd_system_autoload( $className ) {
	if ( 0 === stripos( $className, 'GD_System_' ) || 'WP_Async_Task' == $className ) {
		$filename = trailingslashit( GD_SYSTEM_PLUGIN_DIR ) . 'class-' . str_replace( '_', '-', strtolower( $className ) ) . '.php';
		require_once( $filename );
	}
}

/**
 * Maybe load a bundled system plugin
 *
 * @param  string $plugin_basename
 *
 * @return bool
 */
function gd_system_maybe_load_plugin( $plugin_basename ) {

	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Do nothing if the plugin is already active
	if ( in_array( $plugin_basename, $active_plugins ) ) {

		return false;

	}

	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;
	$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : null;

	/**
	 * Do not load on certain conditions
	 *
	 * We're on the pre-activation plugin sandbox or the plugin
	 * is currently activating and someone isn't trying to bypass
	 * the login page with fake request vars.
	 */
	if (
		false === stripos( $_SERVER['PHP_SELF'], 'wp-login.php' )
		&&
		in_array( $action, array( 'error_scrape', 'activate' ) )
		&&
		$plugin === $plugin_basename
	) {

		return false;

	}

	$path = GD_SYSTEM_PLUGIN_DIR . sprintf( 'plugins/%s', $plugin_basename );

	if ( ! is_readable( $path ) ) {

		return false;

	}

	add_filter( 'load_textdomain_mofile', 'gd_system_load_textdomain_mofile', 10, 2 );

	require_once $path;

	return true;

}

/**
 * Fix textdomain paths on bundled mu-plugins
 *
 * @param  string $mofile
 * @param  string $domain
 *
 * @return string
 */
function gd_system_load_textdomain_mofile( $mofile, $domain ) {

	$path = GD_SYSTEM_PLUGIN_DIR . sprintf( 'plugins/%1$s/languages/%1$s-%2$s.mo', $domain, get_locale() );

	return file_exists( $path ) ? $path : $mofile;

}

// Remove sidekick from loading in the customizer
add_action( 'admin_enqueue_scripts', function() {

	global $sidekick, $wp_customize;

	if ( isset( $sidekick, $wp_customize ) ) {

		remove_action( 'admin_enqueue_scripts', array( $sidekick, 'enqueue_required' ) );
		remove_action( 'customize_controls_enqueue_scripts', array( $sidekick, 'enqueue_required' ), 1000 );

	}

}, 9 );

/**
 * Exclusion of jetpack from auto updates
 * @param string $update
 * @param string $item
 * @return string
 */
function gd_system_auto_update_specific_plugins ( $update, $item ) {
    // Array of plugin slugs to always auto-update
    $plugins = array(
        'jetpack'
    );
    if ( in_array( $item->slug, $plugins ) ) {
        return false; // Never update plugins in this array
    } else {
        return $update; // Else, use the normal API response to decide whether to update or not
    }
}
add_filter( 'auto_update_plugin', 'gd_system_auto_update_specific_plugins', 10, 2 );

/**
 * Localization hack
 * @param string $mofile
 * @param string $domain
 * @return string
 */
function gd_system_localization_fallback( $mofile, $domain ) {
	if ( file_exists( $mofile ) && is_readable( $mofile ) ) {
		return $mofile;
	}
	$_mofile = preg_replace('/[_-][a-zA-Z]{2}\.mo$/', '.mo', $mofile);
	if ( file_exists( $_mofile ) && is_readable( $_mofile ) ) {
		return $_mofile;
	}
	return $mofile;
}
add_filter( 'load_textdomain_mofile', 'gd_system_localization_fallback', 0, 3 );

/**
 * Wrapper for header() function that still allows unit tests to pass
 * @param string $header
 */
function gd_system_header( $header ) {
	if ( PHP_SAPI != 'cli' && !headers_sent() ) {
		header( $header );
	}
	do_action( 'gd_system_sent_header', $header );
}
// @codeCoverageIgnoreEnd

/**
 * Add setting field for Web Pro opt-in
 */
add_action( 'wpem_step_settings_after_content', function () {

	/**
	 * Only display the Pro checkbox to customers that:
	 *
	 * 1. Are not resellers
	 * 2. Are not MT
	 * 3. Speak English
	 * 4. Are located in the United States
	 */
	if ( gd_is_reseller() || gd_is_mt() || ! gd_is_english() || 'US' !== gd_wpem_country_code() ) {

		return;

	}

	?>
	<p>
		<input type="checkbox" id="wpem_pro_opt_in" name="wpem_pro_opt_in" value="1"> <label for="wpem_pro_opt_in"><?php _e( 'I would be interested in hiring a professional to help me with my WordPress site.', 'gd_system' ) ?></label>
	</p>
	<?php

} );

/**
 * Save setting for Web Pro opt-in
 */
add_filter( 'wpem_step_settings_options', function( $options ) {

	if ( ! gd_is_reseller() && ! gd_is_mt() && gd_is_english() && 'US' === gd_wpem_country_code() ) {

		$options[] = array(
			'name'      => 'wpem_pro_opt_in',
			'sanitizer' => 'absint',
			'required'  => false,
		);

	}

	return $options;

} );

/**
 * Return the site created date
 *
 * @param string $format
 *
 * @return int|string
 */
function gd_site_created_date( $format = 'U' ) {

	// Use when this constant was introduced as default (Tue, 22 Dec 2015 00:00:00 GMT)
	$time = defined( 'GD_SITE_CREATED' ) ? (int) GD_SITE_CREATED : 1450742400;

	return ( 'U' === $format ) ? $time : gmdate( $format, $time );

}

/**
 * Check if we are on a staging site
 *
 * @return bool
 */
function gd_is_staging_site() {

	return ( defined( 'GD_STAGING_SITE' ) && GD_STAGING_SITE );

}

/**
 * Check if we are on a temporary domain
 *
 * @return bool
 */
function gd_is_temp_domain() {

	if ( gd_is_staging_site() ) {

		return true;

	}

	global $gd_system_config;

	if ( empty( $gd_system_config ) || $gd_system_config->missing_gd_config ) {

		return false;

	}

	$config = $gd_system_config->get_config();

	if ( ! isset( $config['cname_domains'] ) || ! is_array( $config['cname_domains'] ) ) {

		return false;

	}

	foreach( $config['cname_domains'] as $domain ) {

		if ( 0 === strcasecmp( substr( $_SERVER['HTTP_HOST'], 0 - strlen( $domain ) ), $domain ) ) {

			return true;

		}

	}

	return false;

}

/**
 * Check if we are on MediaTemple
 *
 * @return bool
 */
function gd_is_mt() {

	return ( defined( 'GD_RESELLER' ) && 495469 === GD_RESELLER );

}

/**
 * Check if this is a Reseller account
 *
 * @return bool
 */
function gd_is_reseller() {

	return ( defined( 'GD_RESELLER' ) && 1 !== GD_RESELLER && ! gd_is_mt() );

}

/**
 * Check if this site's language is English
 *
 * @return bool
 */
function gd_is_english() {

	return ( 'en' === substr( get_locale(), 0, 2 ) );

}

/**
 * Check if this site has WPEM enabled
 *
 * @return bool
 */
function gd_is_wpem_enabled() {

	return ( defined( 'GD_EASY_MODE' ) && GD_EASY_MODE );

}

/**
 * Check if this site has used WPEM (not opted-out)
 *
 * @return bool
 */
function gd_has_used_wpem() {

	return ( gd_is_wpem_enabled() && get_option( 'wpem_done' ) && ! get_option( 'wpem_opt_out' ) );

}

/**
 * Check if this site is doing WPEM steps
 *
 * @return bool
 */
function gd_is_doing_wpem() {

	return ( gd_is_wpem_enabled() && defined( 'WPEM_DOING_STEPS' ) && WPEM_DOING_STEPS );

}

/**
 * Return the country code determined during WPEM
 *
 * @return string|null
 */
function gd_wpem_country_code() {

	$wpem_log = json_decode( get_option( 'wpem_log' ) );

	return ! empty( $wpem_log->geodata->country_code ) ? $wpem_log->geodata->country_code : null;

}

/**
 * Return the ASAP key.
 *
 * @return string|null
 */
function gd_asap_key() {

	return ( defined( 'GD_ASAP_KEY' ) && GD_ASAP_KEY ) ? GD_ASAP_KEY : null;

}

/**
 * Return the XID.
 *
 * @return int|null
 */
function gd_xid() {

	$xid = (int) basename( dirname( ABSPATH ) );

	return ( $xid > 1000000 ) ? $xid : null;

}
