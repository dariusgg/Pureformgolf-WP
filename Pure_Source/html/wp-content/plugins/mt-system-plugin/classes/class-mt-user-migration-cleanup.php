<?php

/**
 * Copyright 2014 Media Temple, Inc. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

/**
 * Class MT_User_Migration_Cleanup
 * Clean up system user automatically generated when user migrates from GRID to WPAAS
 * @version 1.0
 * @author Media Temple, Inc.
 */
class MT_User_Migration_Cleanup extends GD_Reseller_System_Plugin_Sso_Xmlrpc {

    protected $_gdapi;

    /**
     * Constructor
     * Add init action to the wp init
     * @return MT_User_Migration_Cleanup
     */
    public function __construct( $text_domain = 'gd-reseller-plugin', $api = null ) {
        $this->_text_domain = $text_domain;
        $this->_gdapi = $api;
        add_action( 'init', array( $this, 'init' ));
    }

    /**
     * Init Filter
     */
    public function init() {
        add_filter( 'xmlrpc_methods', array( $this, 'new_xmlrpc_methods' ) );
    }

    /**
     * Add new xmlrpc method to the xmlrpc_methods filter
     * @param array $methods
     * @return array
     */
    public function new_xmlrpc_methods( $methods ) {
        $methods['gdsso.delete_user'] = array( $this, 'gdsso_delete_user' );
        return $methods;   
    }

    /**
     * Delete mtusermigration user
     * @global GD_System_Plugin_Logger $gd_system_logger
     * @param username
     * @return response array
     */
    public function cleanup( $username ) {
        global $gd_system_logger;

        $id = username_exists( $username );
        if ( $id ) {
            wp_delete_user( $id );

            $msg = __( 'Migration user deleted successfully', $this->_text_domain );
            $gd_system_logger->log( GD_SYSTEM_LOG_ERROR, $msg );
            return array(
                'status'   => 1,
                'message' => $msg
            );
        } else {
            $msg = __( 'Migration user does not exist', $this->_text_domain );
            $gd_system_logger->log( GD_SYSTEM_LOG_ERROR, $msg );
            return array(
                'status'   => 0,
                'message' => $msg
            );
        }
    }

    /**
     * xmlrpc method to delete migration user
     * @global mixed $wp_xmlrpc_server
     * @param array $args
     * @return array
     */
    public function gdsso_delete_user( $args ) {

        global $wp_xmlrpc_server;

        if ( ! $this->minimum_args( $args, 3 ) )
            return $this->error();

        $wp_xmlrpc_server->escape( $args );
        $hash       = $args[0];
        $username   = $args[1];

        return $this->make_authenticated_call( $hash, array($this, 'cleanup'), array( $username ) );
    }
}
