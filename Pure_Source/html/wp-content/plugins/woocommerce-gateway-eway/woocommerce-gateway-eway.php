<?php
/*
	Plugin Name: WooCommerce eWAY Payment Gateway
	Description: WooCommerce eWAY Rapid 3.1 payment gateway integration supporting all countries. Support for WooCommerce Subscriptions.
	Plugin URI: http://woothemes.com/products/eway/
	Author: WooThemes
	Author URI: http://woothemes.com/
	Version: 3.1.9
	Text Domain: wc-eway
	Domain Path: /languages

	Copyright: © 2014 Gerhard Potgieter.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '2c497769d98d025e0d340cd0b5ea5da1', '18604' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

add_action( 'plugins_loaded', 'woocommerce_eway_init', 0 );

function woocommerce_eway_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	load_plugin_textdomain( 'wc-eway', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	define( 'WC_EWAY_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

	require_once 'includes/class-wc-gateway-eway.php';
	include_once 'includes/class-wc-gateway-eway-saved-cards.php';

	// Load subscriptions class if active
	if ( class_exists( 'WC_Subscriptions_Order' ) ) {
		if ( ! function_exists( 'wcs_create_renewal_order' ) ) { // Subscriptions < 2.0
			require_once 'includes/class-wc-gateway-eway-subscriptions-deprecated.php';
		} else {
			require_once 'includes/class-wc-gateway-eway-subscriptions.php';
		}
	}

	// Add classes to WC Payment Methods
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_eway_add_gateway' );
}

function woocommerce_eway_add_gateway( $available_gateways ) {
	if ( class_exists( 'WC_Subscriptions_Order' ) ) {
		if ( ! function_exists( 'wcs_create_renewal_order' ) ) { // Subscriptions < 2.0
			$available_gateways[] = 'WC_Gateway_EWAY_Subscriptions_Deprecated';
		} else {
			$available_gateways[] = 'WC_Gateway_EWAY_Subscriptions';
		}
	} else {
		$available_gateways[] = 'WC_Gateway_EWAY';
	}
	return $available_gateways;
}
