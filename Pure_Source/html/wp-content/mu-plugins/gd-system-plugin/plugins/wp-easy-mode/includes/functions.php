<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Return the current step
 *
 * @return object
 */
function wpem_get_current_step() {

	if ( ! \WPEM\wpem()->admin->is_wizard() ) {

		return;

	}

	$step = wpem_get_step_by( 'name', filter_input( INPUT_GET, 'step' ) );

	return ! empty( $step ) ? $step : wpem_get_step_by( 'position', 1 ); // Default to first step

}

/**
 * Return the next step
 *
 * @return object
 */
function wpem_get_next_step() {

	return wpem_get_step_by( 'position', wpem_get_current_step()->position + 1 );

}

/**
 * Get a step by name or actual position
 *
 * @param  string $field
 * @param  mixed  $value
 *
 * @return object
 */
function wpem_get_step_by( $field, $value ) {

	return \WPEM\wpem()->admin->get_step_by( $field, $value );

}

/**
 * Return a step field value from the log
 *
 * @param  string $field
 * @param  string $step (optional)
 * @param  mixed  $default (optional)
 *
 * @return mixed
 */
function wpem_get_step_field( $field, $step = null, $default = false ) {

	$step = ! empty( $step ) ? $step : wpem_get_current_step()->name;

	$log = new \WPEM\Log;

	return ! empty( $log->steps[ $step ]['fields'][ $field ] ) ? $log->steps[ $step ]['fields'][ $field ] : $default;

}

/**
 * Return the URL for the setup wizard
 *
 * @return string
 */
function wpem_get_wizard_url() {

	$url = add_query_arg(
		[
			'page' => \WPEM\wpem()->page_slug,
		],
		self_admin_url()
	);

	return $url;

}

/**
 * Return the customizer version of a given URL
 *
 * @param  array $args (optional)
 *
 * @return string
 */
function wpem_get_customizer_url( $args = [] ) {

	$url = self_admin_url( 'customize.php' );

	if ( ! $args || ! is_array( $args ) ) {

		return $url;

	}

	return add_query_arg( array_map( 'urlencode', $args ), $url );

}

/**
 * Return the site type
 *
 * @param  string $default
 *
 * @return string
 */
function wpem_get_site_type( $default = 'standard' ) {

	return (string) get_option( 'wpem_site_type', $default );

}

/**
 * Return the site industry
 *
 * @param  string $default
 *
 * @return string
 */
function wpem_get_site_industry( $default = '' ) {

	return (string) get_option( 'wpem_site_industry', $default );

}


function wpem_get_site_industry_slugs_to( $sub_array_key = 'label' ) {

	if ( ! in_array( $sub_array_key, [ 'label', 'api_cat' ] ) ) {

		throw new Exception( 'Argument must either be "label" or "api_cat"' );

	}

	$list = [
		'business' => [
			'label'   => __( 'Business / Finance / Law', 'wp-easy-mode' ),
			'api_cat' => 'professional',
		],
		'design' => [
			'label'   => __( 'Design / Art / Portfolio', 'wp-easy-mode' ),
			'api_cat' => 'graphicdesign',
		],
		'education' => [
			'label'   => __( 'Education', 'wp-easy-mode' ),
			'api_cat' => 'education',
		],
		'health' => [
			'label'   => __( 'Health / Beauty', 'wp-easy-mode' ),
			'api_cat' => 'health',
		],
		'construction' => [
			'label'   => __( 'Home Services / Construction', 'wp-easy-mode' ),
			'api_cat' => 'constructionservices',
		],
		'entertainment' => [
			'label'   => __( 'Music / Movies / Entertainment', 'wp-easy-mode' ),
			'api_cat' => 'massmedia',
		],
		'non-profit'  => [
			'label'   => __( 'Non-profit / Causes / Religious', 'wp-easy-mode' ),
			'api_cat' => 'charitableorganizations',
		],
		'other' => [
			'label'   => __( 'Other', 'wp-easy-mode' ),
			'api_cat' => 'generic',
		],
		'pets' => [
			'label'   => __( 'Pets / Animals', 'wp-easy-mode' ),
			'api_cat' => 'pets',
		],
		'real-estate' => [
			'label'   => __( 'Real Estate', 'wp-easy-mode' ),
			'api_cat' => 'realestate',
		],
		'restaurant' => [
			'label'   => __( 'Restaurant / Food', 'wp-easy-mode' ),
			'api_cat' => 'restaurants',
		],
		'sports' => [
			'label'   => __( 'Sports / Recreation', 'wp-easy-mode' ),
			'api_cat' => 'active',
		],
		'transportation' => [
			'label'   => __( 'Transportation / Automotive', 'wp-easy-mode' ),
			'api_cat' => 'auto',
		],
		'travel'  => [
			'label'   => __( 'Travel / Hospitality / Leisure', 'wp-easy-mode' ),
			'api_cat' => 'hotelstravel',
		],
		'wedding' => [
			'label'   => __( 'Wedding', 'wp-easy-mode' ),
			'api_cat' => 'weddingphotographers',
		],
	];

	array_walk( $list, function( &$value ) use ( $sub_array_key ) {

		$value = $value[ $sub_array_key ];

	} );

	if ( 'label' == $sub_array_key ) {

		$list = [ '' => __( '- Select an industry -', 'wp-easy-mode' ) ] + $list;

	}

	return $list;

}

/**
 * Return site contact information
 *
 * @param  string $key
 * @param  mixed  $default (optional)
 *
 * @return mixed
 */
function wpem_get_contact_info( $key, $default = false ) {

	$array = (array) get_option( 'wpem_contact_info', [] );

	return isset( $array[ $key ] ) ? $array[ $key ] : $default;

}

/**
 * Return a social network URL
 *
 * @param  string $key
 * @param  mixed  $default (optional)
 *
 * @return mixed
 */
function wpem_get_social_profile_url( $key, $default = false ) {

	$array = (array) get_option( 'wpem_social_profiles', [] );

	return isset( $array[ $key ] ) ? $array[ $key ] : $default;

}

/**
 * Return an array of social profile names
 *
 * @return array
 */
function wpem_get_social_profiles() {

	return array_keys( (array) get_option( 'wpem_social_profiles', [] ) );

}

/**
 * Mark the wizard as started
 */
function wpem_mark_as_started() {

	update_option( 'wpem_started', 1 );

	update_option( 'wpem_done', 0 );

}

/**
 * Mark the wizard as done
 */
function wpem_mark_as_done() {

	delete_option( 'wpem_last_viewed' );

	update_option( 'wpem_done', 1 );

	\WPEM\wpem()->self_destruct();

	\WPEM\wpem()->deactivate();

}

/**
 * Quit the wizard
 */
function wpem_quit() {

	update_option( 'wpem_opt_out', 1 );

	wpem_mark_as_done();

	if ( ! function_exists( 'get_plugins' ) ) {

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

	}

	/**
	 * Filter plugins to be deactivated on quit
	 *
	 * @var array
	 */
	$plugins = apply_filters( 'wpem_deactivate_plugins_on_quit', array_keys( get_plugins() ) );

	if ( is_array( $plugins ) && ( ! defined( 'WPEM_DOING_TESTS' ) || ! WPEM_DOING_TESTS ) ) {

		deactivate_plugins( $plugins );

	}

	if ( function_exists( 'wp_safe_redirect' ) ) {

		wp_safe_redirect( self_admin_url() );

		exit;

	}

}

/**
 * Round a float and preserve trailing zeros
 *
 * @param  float $value
 * @param  int   $precision (optional)
 *
 * @return float
 */
function wpem_round( $value, $precision = 3 ) {

	$precision = absint( $precision );

	return sprintf( "%.{$precision}f", round( $value, $precision ) );

}

/**
 * Sanitize a phone number
 *
 * @param  string $value
 *
 * @return string
 */
function wpem_sanitize_phone( $value ) {

	return preg_replace( '/[^.+\-\(\) 0-9]/', '', $value );

}
