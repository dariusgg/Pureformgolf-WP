<?php

/**
 * Copyright 2014 Media Temple, Inc. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

/**
 * Class MT_System_Plugin_Google_Analytics_Xmlrpc
 * Extend WordPress XMLRPC capabilities with custom rpc functions that utilize
 * GoDaddy's sso token for validation instead of username/password.
 * @version 1.0
 * @author Media Temple, Inc.
 */
class MT_System_Plugin_Google_Analytics_Xmlrpc extends GD_Reseller_System_Plugin_Sso_Xmlrpc {

    protected $_gdapi;
    protected $_text_domain;

    /**
      * Constructor
      * Add any actions / hooks
      * @return MT_System_Plugin_Google_Analytics_Xmlrpc
    */
    public function __construct( $text_domain = 'gd-reseller-plugin', $api = null ) {
        $this->_text_domain = $text_domain;
        $this->_gdapi = $api;
        add_action( 'init', array( $this, 'init' ) );
//        if ( !( is_admin() OR is_feed() OR is_robots() OR is_trackback() ) ) {
        if ( !is_admin() ) {
            // We are viewing the theme
            add_action( 'wp_head', array( $this, 'add_ga_tracking_snippet_to_head' ), PHP_INT_MAX );
            add_action( 'wp_footer', array( $this, 'add_ga_tracking_snippet_to_footer' ), PHP_INT_MAX );
        } else {
            // We are likely viewing the WordPress Administration Panel. Do nothing.
        }

        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name = 'mtsp'; // Plugin Folder

    }

    /**
     * Init filter
     */
    public function init() {
        add_filter( 'xmlrpc_methods', array( $this, 'new_xmlrpc_methods' ) );

	// If test-head query arg exists hook into wp_head
	if ( isset( $_GET['test-head'] ) )
	    add_action( 'wp_head', array( $this, 'test_head' ), PHP_INT_MAX );
 
	// If test-footer query arg exists hook into wp_footer
	if ( isset( $_GET['test-footer'] ) )
	    add_action( 'wp_footer', array( $this, 'test_footer' ), PHP_INT_MAX );
    }

    /**
     * Echo a string (that we can search for later) into the head of the document.
     * This should end up appearing directly before </head>
     * @param nothing
     * @return nothing
     */
    public function test_head() {
        echo '<!--wp_head-->';
    }
 
    /**
     * Echo a string (that we can search for later) into the footer of the document.
     * This should end up appearing directly before </body>
     * @param nothing
     * @return nothing
     */
    public function test_footer() {
        echo '<!--wp_footer-->';
    }
 
    /**
     * Add our new xmlrpc methods to the xmlrpc_methods filter
     * @param array $methods
     * @return array
     */
    public function new_xmlrpc_methods( $methods ) {
        $methods['gdsso.gaInstallHeader'] = array( $this, 'gdsso_gaInstallHeader' );
        $methods['gdsso.gaInstallFooter'] = array( $this, 'gdsso_gaInstallFooter' );
        return $methods;   
    }

    /**
     * Set the API (for validating SSO tokens)
     * @param GD_System_Plugin_API $api
     */
    public function set_api( $api ) {
        $this->_gdapi = $api;
    }

    /**
     * Echoes the tracking snippet (located in the option table) into the <head>
     * section just before the closing </head>
     * @param nothing
     * @return nothing
     */
    public function add_ga_tracking_snippet_to_head()
    {
        if ( get_option( $this->plugin->name . '_web_property_id_head' ) !== false ) {
            $web_property_id = get_option( $this->plugin->name . '_web_property_id_head' );
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $web_property_id ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php
        }
    }

    /**
     * Echoes the tracking snippet (located in the option table) into the footer
     * section just before the closing </body>
     * @param nothing
     * @return nothing
     */
    public function add_ga_tracking_snippet_to_footer()
    {
        if ( get_option( $this->plugin->name . '_web_property_id_footer' ) !== false ) {
            $web_property_id = get_option( $this->plugin->name . '_web_property_id_footer' );
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $web_property_id ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php
        }
    }

    /**
     * Flush object cache and all php processes
     * @param nothing
     * @return nothing
     */
    public function _flush_cache()
    {
        // Flush object cache
        wp_cache_flush();

        // Trigger a flush on all php processes
        // USUALLY this is handled in the object cache completely
        // but if this is invoked from a CLI context and there
        // is no APC loaded, then this will still send
        // the signal out to all the child procs
        update_option( 'gd_system_last_cache_flush', time() );
    }

    /**
     * Entry point for gaInstallHeader() function
     * @global mixed $wp_xmlrpc_server
     * @param array $args - (ua_id, sso token)
     * @return array ('status' and 'message' keys)
     */
    public function gdsso_gaInstallHeader( $args ) {
        global $wp_xmlrpc_server;

        if ( ! $this->minimum_args( $args, 2 ) )
            return $this->error();

        $wp_xmlrpc_server->escape( $args );
        $ua_id   = $args[0];
        $hash    = $args[1];

        return $this->make_authenticated_call( $hash, array( $this, 'gaInstallHeader' ), array( array( $ua_id ) ) );
    }

    /**
     * Add the tracking snippet (located in the option table) into the <head>
     * section just before the closing </head>
     * @global mixed $wp_xmlrpc_server
     * @param array $args - (ua_id)
     * @return array ('status' and 'message' keys)
     */
    public function gaInstallHeader( $args = null ) {

        if ( ! $this->minimum_args( $args, 1 ) )
            return $this->error();

        $ua_id = $args[0];

        $status = 1;
        $msg = 'Success';
        if ( function_exists( 'wp_head' ) ) {
            if ( has_action( 'wp_head' ) ) {
                $option_name = $this->plugin->name . '_web_property_id_head';
                if ( get_option( $option_name ) !== false ) {
                    // The option already exists, so we just update it.
                    update_option( $option_name, $ua_id );
                } else {
                    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                    $deprecated = null;
                    $autoload = 'no';
                    if ( add_option( $option_name, $ua_id, $deprecated, $autoload ) ) {
                    } else {
                        $status = 0;
                        $msg = 'WordPress experienced a problem adding option ' . $option_name;
                        echo '<br><span class="mtss-fatal">FATAL: WordPress problem adding option ' . $option_name . '. Please contact MT Support.</span><br>';
                    }
                }

                // Flush object cache to prevent cached UA-IDs in the live html page source
                $this->_flush_cache();

                // Delete the web property id footer option so we don't have both head and footer
                delete_option( $this->plugin->name . '_web_property_id_footer' );

            }
            else {
                $status = 0;
                $msg = 'Your WordPress site does not support the wp_head action.';
            }
        }
        else {
            $status = 0;
            $msg = 'Your WordPress installation does not contain the wp_head function.';
        }

        return array(
            'status'  => $status,
            'message' => $msg,
        );
    }

    /**
     * Entry point for gaInstallFooter() function
     * @global mixed $wp_xmlrpc_server
     * @param array $args - (ua_id, sso token)
     * @return array ('status' and 'message' keys)
     */
    public function gdsso_gaInstallFooter( $args ) {
        global $wp_xmlrpc_server;

        if ( ! $this->minimum_args( $args, 2 ) )
            return $this->error();

        $wp_xmlrpc_server->escape( $args );
        $ua_id   = $args[0];
        $hash    = $args[1];

        return $this->make_authenticated_call( $hash, array( $this, 'gaInstallFooter' ), array( array( $ua_id ) ) );
    }

    /**
     * Add the tracking snippet (located in the option table) into the <body>
     * section just before the closing </body>
     * @global mixed $wp_xmlrpc_server
     * @param array $args - (ua_id)
     * @return array ('status' and 'message' keys)
     */
    public function gaInstallFooter( $args = null ) {

        if ( ! $this->minimum_args( $args, 1 ) )
            return $this->error();

        $ua_id = $args[0];
        $status = 1;
        $msg = 'Success';
        if ( function_exists( 'wp_footer' ) ) {
            if ( has_action( 'wp_footer' ) ) {
                $option_name = $this->plugin->name . '_web_property_id_footer';
                if ( get_option( $option_name ) !== false ) {
                    // The option already exists, so we just update it.
                    update_option( $option_name, $ua_id );
                } else {
                    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                    $deprecated = null;
                    $autoload = 'no';
                    if ( add_option( $option_name, $ua_id, $deprecated, $autoload ) ) {
                    } else {
                        $status = 0;
                        $msg = 'WordPress experienced a problem adding option ' . $option_name;
                        echo '<br><span class="mtss-fatal">FATAL: WordPress problem adding option ' . $option_name . '. Please contact MT Support.</span><br>';
                    }
                }

                // Flush object cache to prevent cached UA-IDs in the live html page source
                $this->_flush_cache();

                // Delete the web property id head option so we don't have both head and footer
                delete_option( $this->plugin->name . '_web_property_id_head' );

            }
            else {
                $status = 0;
                $msg = 'Your WordPress site does not support the wp_footer action.';
            }
        }
        else {
            $status = 0;
            $msg = 'Your WordPress installation does not contain the wp_footer function.';
        }

        return array(
            'status'  => $status,
            'message' => $msg,
        );
    }
}
