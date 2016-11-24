<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

/**
 * Class GD_System_Plugin_Hotfixes
 * Handle any hotfixes
 * @version 1.0
 * @author Kurt Payne <kpayne@godaddy.com>
 */
class GD_System_Plugin_Hotfixes {

	/**
	 * Constructor.
	 * Hook any needed actions/filters
	 */
	public function __construct() {

		// Enable sampling for WP Popular Posts, this makes it perform much better especially on high traffic sites
		add_filter( 'wpp_data_sampling', '__return_true' );

		// Clean up limit login attempts
		$flag = ( mt_rand( 0, 50 ) == 47 );

		if ( apply_filters( 'gd_system_clean_limit_login_attempts', $flag ) ) {

			add_action( 'muplugins_loaded', [ $this, 'clean_limit_login_attempts' ] );

		}

		// Prevent jetpack from validating siteurl and home option on staging
		if ( gd_is_staging_site() ) {

			add_filter( 'jetpack_has_identity_crisis', '__return_false' );

		}

		add_filter( 'option_jetpack_options', [ $this, 'remove_jetpack_nag' ] );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			$this->blacklist_cron_event_hooks();

		}

	}

	/**
	 * Clean up limit login attempts options.  On social sites, these can get to be
	 * huge arrays that turn into huge strings and break MySQL because of packet size
	 * limitations
	 * @return void
	 */
	public function clean_limit_login_attempts() {
		foreach( array( 'limit_login_retries_valid', 'limit_login_retries', 'limit_login_logged' ) as $opt ) {
			$val = get_option( $opt );
			if ( !empty( $val ) && is_array( $val ) && count( $val ) > 250 ) {
				uasort( $val, array( $this, '__sort' ) );
				$val = array_slice( $val, -200 );
				update_option( $opt, $val );
			}
		}
	}

	/**
	 * Sort function for limit login attempts options
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 */
	public function __sort( $a, $b ) {
		if ( is_array( $b ) ) {
			if ( count( $a ) == count( $b ) ) {
				return 0;
			} else {
				return ( count( $a ) < count( $b ) ) ? - 1 : 1;
			}
		} else {
			if ( $a == $b ) {
				return 0;
			} else {
				return ( $a < $b ) ? -1 : 1;
			}
		}
	}

	/**
	 * Hide the updates screen nag from Jetpack
	 *
	 * @param array $value
	 *
	 * @return array
	 */
	public function remove_jetpack_nag( $value ) {

		if ( $value && empty( $value['hide_jitm']['manage'] ) || 'hide' !== $value['hide_jitm']['manage'] ) {

			$value['hide_jitm']['manage'] = 'hide';

		}

		return $value;

	}

	/**
	 * Blacklist cron event hooks
	 *
	 * Note: Should only run when using WP-CLI
	 */
	private function blacklist_cron_event_hooks() {

		$blacklist = [
			'wp_version_check',
		];

		global $wpdb, $gd_system_cron_event_temp_blacklist;

		// Get temporary blacklist transients
		$transients = (array) $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '_transient_wppaas_skip_cron_%';" );

		// Scrub array of key prefixes and expired transients
		foreach ( $transients as $key => $transient ) {

			$transients[ $key ]->option_name = preg_filter( '/^_transient_/', '', $transient->option_name );

			if ( false === get_transient( $transient->option_name ) ) {

				unset( $transients[ $key ] );

			}

		}

		// Store in global, used by 'reset' subcommand
		$gd_system_cron_event_temp_blacklist = array_combine(
			wp_list_pluck( $transients, 'option_name' ),
			wp_list_pluck( $transients, 'option_value' )
		);

		// Merge temporary blacklist into core blacklist
		$blacklist = array_merge( $blacklist, array_values( $gd_system_cron_event_temp_blacklist ) );

		// Remove blacklisted events from the crons array
		add_filter( 'option_cron', function( $crons ) use ( $blacklist ) {

			if ( ! $crons ) {

				return $crons;

			}

			foreach ( (array) $crons as $timestamp => $events ) {

				foreach ( (array) $events as $hook => $event ) {

					if ( in_array( $hook, $blacklist ) ) {

						unset( $crons[ $timestamp ][ $hook ] );

					}

				}

				if ( ! $events ) {

					unset( $crons[ $timestamp ] );

				}

			}

			return (array) $crons;

		}, 999 );

	}

}
