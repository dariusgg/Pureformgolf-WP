<?php

/**
 * Copyright 2014 Media Temple, Inc. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

/**
 * Class GD_Reseller_System_Plugin_Sso_Xmlrpc
 * Extend WordPress XMLRPC capabilities with custom rpc functions that utilize
 * GoDaddy's sso token for validation instead of username/password.
 * @version 1.0
 * @author Media Temple, Inc.
 */
class GD_Reseller_System_Plugin_Sso_Xmlrpc {

    // priority 21 (after default filter)
    protected $_priority = 21;

    protected $_error;
    protected $_gdapi;
    protected $_text_domain;
    private $_num_args = 3;

    /**
      * Constructor
      * Add any actions / hooks
      * @return GD_Reseller_System_Plugin_Sso_Xmlrpc
    */
    public function __construct( $text_domain = 'gd-reseller-plugin', $priority = 21, $api = null ) {
        $this->_text_domain = $text_domain;
        $this->_priority = $priority;
        $this->_gdapi = $api;
        add_action( 'init', array( $this, 'init' ) );
    }

	/**
	 * Init filter
	 */
    public function init() {
        add_filter( 'xmlrpc_methods', array( $this, 'new_xmlrpc_methods' ) );
    }

	/**
	 * Add our new xmlrpc methods to the xmlrpc_methods filter
	 * @param array $methods
	 * @return array
	 */
    public function new_xmlrpc_methods( $methods ) {
        $methods['gdsso.getUsers'] = array( $this, 'gdsso_getUsers' );
        $methods['gdsso.getOptions'] = array( $this, 'gdsso_getOptions' );
        $methods['gdsso.getPosts'] = array( $this, 'gdsso_getPosts' );
        $methods['gdsso.getComments'] = array( $this, 'gdsso_getComments' );
        $methods['gdsso.activateTheme'] = array( $this, 'gdsso_activateTheme' );
        $methods['gdsso.themes'] = array( $this, 'gdsso_themes' );
        $methods['gdsso.siteData'] = array( $this, 'gdsso_siteData' );
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
	 * Set the auth filter priority
	 * @param int $priority
	 */
    public function set_sso_auth_filter_priority( $priority ) {
        $this->_priority = $priority;
    }

    /**
      * Checks if the method received the minimum number of arguments.
      *
      * @param string|array $args Sanitize single string or array of strings.
      * @param int $count Minimum number of arguments.
      * @return boolean if $args contains at least $count arguments.
    */
    protected function minimum_args( $args, $count ) {
        if ( count( $args ) < $count ) {
            $this->_error = new IXR_Error( 400, __( 'Insufficient arguments passed to this XML-RPC method.', $this->_text_domain ) );
            return false;
        }

        return true;
    }

	/**
	 * Return any error messages
	 * @return type
	 */
    protected function error() {
        return $this->_error;
    }

	/**
	 * Make a call, use SSO hash for authentication
	 * @global GD_System_Plugin_Logger $gd_system_logger
	 * @param string $hash
	 * @param callable $callback
	 * @param array $args (passed to callback)
	 * @return mixed
	 */
	protected function make_authenticated_call( $hash, $callback, $args=null ) {
		global $gd_system_logger;

            if ( is_null( $this->_gdapi ) ) {
                $msg = __( 'Your site does not support the GoDaddy API.', $this->_text_domain );
                $gd_system_logger->log( GD_SYSTEM_LOG_ERROR, $msg );
                wp_die( $msg, 403 );
            }
	
		// Validate sso hash
        $resp = $this->_gdapi->validate_sso_hash( $hash );
        if ( is_wp_error( $resp ) ) {
            $msg = __( 'Could not fetch response from api to validate SSO hash.', $this->_text_domain );
            $gd_system_logger->log( GD_SYSTEM_LOG_ERROR, $msg . ' Error [' . $resp->get_error_code() . ']: ' . $resp->get_error_message() );
            wp_die( $resp->get_error_message(), $resp->get_error_code() );
        }

        // Check for valid response from gd api 
        if ( !( is_string( $resp['body'] ) && ( 'true' === strtolower( $resp['body'] ) ) ) ) {
            $msg = __( 'Could not validate SSO hash.', $this->_text_domain );
            $gd_system_logger->log( GD_SYSTEM_LOG_ERROR, $msg );
            wp_die( $msg, 403 );
        }

        // post sso validation authenticate filter
        // This gets applied when the WordPress xmlrpc function gets called and
        // tries to auth with username/password.
        // Putting this in init() will cause log out issues
        add_filter( 'authenticate', array( $this, 'my_auth'), $this->_priority, $this->_num_args);

        // We have access. Construct internal xmlrpc request
        $resp = call_user_func_array( $callback, $args );

        // Don't need the filter anymore
        remove_filter( 'authenticate', array( $this, 'my_auth'), $this->_priority);

		// Done
		return $resp;
	}
	
	/**
	 * Override authentication (only called if there's a valid SSO token)
	 * @param type $user
	 * @param type $username
	 * @param type $password
	 * @return \WP_User
	 */
    public function my_auth( $user, $username = null, $password = null ) {
        if ( is_a( $user, 'WP_User' ) ) {
            return $user;
        }

        $user = get_users( array(
            'role'   => 'administrator',
            'number' => 1
        ) );
        if ( ! $user[0] instanceof WP_User ) {
            return;
        }
        return $user[0];
    }

	/**
	 * Get a list of users
	 * @global mixed $wp_xmlrpc_server
	 * @param array $args
	 * @return array
	 */
    public function gdsso_getUsers( $args ) {
        global $wp_xmlrpc_server;
        if ( ! $this->minimum_args( $args, 2 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $wp_xmlrpc_server->escape( $args );

        $blog_id = $args[0];
        $hash    = $args[1];
        $filter  = isset( $args[2] ) ? $args[2] : array();

		return $this->make_authenticated_call( $hash, array( $wp_xmlrpc_server, 'wp_getUsers' ), array( array( $blog_id, null, null, $filter ) ) );
    }

	/**
	 * Get a list of options
	 * @global mixed $wp_xmlrpc_server
	 * @param array $args
	 * @return array
	 */
    public function gdsso_getOptions( $args ) {
        global $wp_xmlrpc_server;
        if ( ! $this->minimum_args( $args, 2 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $wp_xmlrpc_server->escape( $args );
        $blog_id = $args[0];
        $hash    = $args[1];
        $options = isset( $args[2] ) ? $args[2] : array();

		return $this->make_authenticated_call( $hash, array( $wp_xmlrpc_server, 'wp_getOptions' ), array( array( $blog_id, null, null, $options ) ) );
    }

	/**
	 * Get a list of posts
	 * @global mixed $wp_xmlrpc_server
	 * @param array $args
	 * @return array
	 */
    public function gdsso_getPosts( $args ) {
        global $wp_xmlrpc_server;

        if ( ! $this->minimum_args( $args, 2 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $wp_xmlrpc_server->escape( $args );
        $blog_id = $args[0];
        $hash    = $args[1];
        $filter  = isset( $args[2] ) ? $args[2] : array();

		return $this->make_authenticated_call( $hash, array( $wp_xmlrpc_server, 'wp_getPosts' ), array( array( $blog_id, null, null, $filter ) ) );
    }

	/**
	 * Get a list of comments
	 * @global mixed $wp_xmlrpc_server
	 * @param array $args
	 * @return array
	 */
    public function gdsso_getComments( $args ) {
        global $wp_xmlrpc_server;

        if ( ! $this->minimum_args( $args, 2 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $wp_xmlrpc_server->escape( $args );
        $blog_id = $args[0];
        $hash    = $args[1];
        $filter  = isset( $args[2] ) ? $args[2] : array();

		return $this->make_authenticated_call( $hash, array( $wp_xmlrpc_server, 'wp_getComments' ), array( array( $blog_id, null, null, $filter ) ) );
    }

	/**
	 * Activate a theme (RPC entrypoint)
	 * @param array $args
	 * @return array
	 */
    public function gdsso_activateTheme( $args ) {

        if ( ! $this->minimum_args( $args, 2 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $hash  = $args[0];
        $theme = $args[1];

        return $this->make_authenticated_call( $hash, array( $this, 'activateTheme' ), array( $theme ) );
    }

	/**
	 * Activate a theme
	 * @param string $theme
	 * @return array
	 */
    public function activateTheme( $theme ) {

        if ( ! isset( $theme ) or empty( $theme ) )
            wp_die( __( 'no theme was specified', $this->_text_domain ), 400 );

        $theme_obj = wp_get_theme( $theme );

        if ( ! $theme_obj->exists() || ! $theme_obj->is_allowed() )
            wp_die( __( 'invalid theme specified', $this->_text_domain ), 400 );

        switch_theme( $theme_obj->get_stylesheet() );
        return array(
                'status'  => 1,
                'message' => 'Success',
        );
    }

	/**
	 * Get Themes (RPC entrypoint)
	 * @param array $args
	 * @return array
	 */
    public function gdsso_themes( $args ) {

        if ( ! $this->minimum_args( $args, 1 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $hash  = $args[0];
        return $this->make_authenticated_call( $hash, array( $this, 'get_themes' ), array() );
    }

	/**
	 * Get Themes
	 * @return array
	 */
    public function get_themes() {

        $theme_objs = wp_get_themes();
        $themes = array();
        foreach ( $theme_objs as $theme_obj ) {
            array_push( $themes, array(
                'name'           => $theme_obj->get('Name'),
                'text_domain'    => $theme_obj->get('TextDomain'),
                'stylesheet_dir' => $theme_obj->get_stylesheet(),
                'description'    => $theme_obj->get('Description'),
                'author'         => $theme_obj->get('Author'),
                'version'        => $theme_obj->get('Version'),
                'status'         => $theme_obj->get('Status'),
                'exists'         => $theme_obj->exists(),
                'allowed'        => $theme_obj->is_allowed(),
                'screenshot'     => $theme_obj->get_screenshot(),
            ));
        }
        return $themes;
    }

	/**
	 * Site Data (RPC entrypoint)
	 * @param array $args
	 * @return array
	 */
    public function gdsso_siteData( $args ) {

        if ( ! $this->minimum_args( $args, 1 ) )
            wp_die( __( 'Insufficient arguments passed to this XML-RPC method', $this->_text_domain ), 400 );

        $hash = $args[0];
        $comments_filter = isset( $args[1] ) ? $args[1] : array();
        $pages_filter    = isset( $args[2] ) ? $args[2] : array();
        $posts_filter    = isset( $args[3] ) ? $args[3] : array();
        $users_filter    = isset( $args[4] ) ? $args[4] : array();

        return $this->make_authenticated_call( $hash, array( $this, 'site_data' ), array( array( $comments_filter, $pages_filter, $posts_filter, $users_filter ) ) );
    }

	/**
	 * Site Data
	 * @return array
	 */
    public function site_data( $args = null ) {

        // Pull in args
        $comments_filter = isset( $args[0] ) ? $args[0] : array();
        $pages_filter    = isset( $args[1] ) ? $args[1] : array();
        $posts_filter    = isset( $args[2] ) ? $args[2] : array();
        $users_filter    = isset( $args[3] ) ? $args[3] : array();

        // Admin Stats
        $comments = get_comments( $comments_filter );
        $pages    = get_posts( $pages_filter );
        $posts    = get_posts( $posts_filter );
        $users    = get_users( $users_filter );

        // Current Theme object
        $theme_obj = wp_get_theme();

        return array(
            'title'                => get_bloginfo('name'),
            'version'              => get_bloginfo('version'),
            'theme'                => $theme_obj->get('Name'),
            'theme_stylesheet_dir' => $theme_obj->get_stylesheet(),
            'themes'               => $this->get_themes(),
            'comments'             => $comments,
            'pages'                => count( $pages ),
            'posts'                => count( $posts ),
            'users'                => count( $users ),
        );
    }
}
