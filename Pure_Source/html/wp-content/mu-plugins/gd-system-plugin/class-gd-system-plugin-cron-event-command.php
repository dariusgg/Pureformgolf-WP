<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class GD_System_Plugin_Cron_Event_Command extends Cron_Event_Command {

	/**
	 * Run all cron events that are due now.
	 *
	 * ## EXAMPLES
	 *
	 *     wp cron event wppaas run
	 */
	public function run( $args, $assoc_args ) {

		$events = static::get_cron_events_due_now();

		if ( ! is_wp_error( $events ) && $events ) {

			$hooks = wp_list_pluck( $events, 'hook' );

			WP_CLI::log( sprintf( 'Running %d cron event(s) due now: %s', count( $hooks ), implode( ', ', $hooks ) ) );

			WP_CLI::run_command( array_merge( [ 'cron', 'event', 'run' ], $hooks ), $assoc_args );

			return;

		}

		if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'quiet' ) ) {

			return;

		}

		WP_CLI::warning( 'No cron events are due.' );

	}

	/**
	 * List all cron events that are due now.
	 *
	 * ## EXAMPLES
	 *
	 *     wp cron event wppaas list
	 *
	 * @subcommand list
	 */
	public function list_( $args, $assoc_args ) {

		$events = static::get_cron_events_due_now();

		if ( ! is_wp_error( $events ) && $events ) {

			$hooks = wp_list_pluck( $events, 'hook' );

			WP_CLI::log( sprintf( 'There are %d cron event(s) due now: %s', count( $hooks ), implode( ', ', $hooks ) ) );

			return;

		}

		WP_CLI::warning( 'No cron events are due.' );

	}

	/**
	 * Mark a cron event to be skipped temporarily.
	 *
	 * ## OPTIONS
	 *
	 * <hook>
	 * : The hook to skip.
	 *
	 * [--time]
	 * : Amount of time to skip (in seconds). Default: 3600 (1 hour)
	 *
	 * ## EXAMPLES
	 *
	 *     wp cron event wppaas skip foo
	 */
	public function skip( $args, $assoc_args ) {

		$hook = $args[0];
		$time = empty( $assoc_args['time'] ) ? HOUR_IN_SECONDS : absint( $assoc_args['time'] );

		set_transient( $this->get_skipped_hook_transient_key( $hook ), $hook, $time );

		$this->reset( $args, [ 'quiet' => true ] );

		if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'quiet' ) ) {

			return;

		}

		WP_CLI::success( sprintf( "Skipping cron event '%s' for %d seconds.", $hook, $time ) );

	}

	/**
	 * Reset cron events to be due now.
	 *
	 * ## OPTIONS
	 *
	 * [<hook>...]
	 * : One or more hooks to reset.
	 *
	 * [--all]
	 * : Reset all cron event hooks.
	 *
	 * ## EXAMPLES
	 *
	 *     wp cron event wppaas reset foo
	 *     wp cron event wppaas reset foo bar baz
	 *     wp cron event wppaas reset --all
	 */
	public function reset( $args, $assoc_args ) {

		$all = WP_CLI\Utils\get_flag_value( $assoc_args, 'all' );

		if ( empty( $args ) && ! $all ) {

			WP_CLI::error( 'Please specify one or more cron events, or use --all.' );

		}

		$reset  = 0;
		$events = static::get_cron_events();

		if ( ! is_wp_error( $events ) && $events ) {

			foreach ( $events as $event ) {

				if ( in_array( $event->hook, $args ) || $all ) {

					wp_clear_scheduled_hook( $event->hook, $event->args );

					if ( $event->schedule ) {

						wp_schedule_event( time(), $event->schedule, $event->hook, $event->args );

					} else {

						wp_schedule_single_event( time(), $event->hook, $event->args );

					}

					$reset++;

				}

			}

		}

		if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'quiet' ) ) {

			return;

		}

		if ( $reset ) {

			WP_CLI::success( sprintf( 'Reset a total of %d cron event(s).', $reset ) );

			return;

		}

		WP_CLI::warning( 'No cron events could be reset.' );

	}

	/**
	 * Return cron events that are due now.
	 *
	 * @return array
	 */
	protected static function get_cron_events_due_now() {

		$events = static::get_cron_events();

		if ( is_wp_error( $events ) ) {

			return $events;

		}

		$output = [];

		foreach ( $events as $hash => $event ) {

			if (
				// Event is due now
				time() >= $event->time
				&&
				// Contains no spaces or special chars
				sanitize_file_name( $event->hook ) === $event->hook
			) {

				$output[ $hash ] = $event;

			}

		}

		return $output;

	}

	/**
	 * Return the transient key for a skipped hook
	 *
	 * If the hook provided does not yet have a transient
	 * key one will be generated.
	 *
	 * @param  string $hook
	 *
	 * @return string
	 */
	protected function get_skipped_hook_transient_key( $hook ) {

		global $gd_system_cron_event_temp_blacklist;

		$generated_key = sprintf( 'wppaas_skip_cron_%s', sanitize_key( wp_generate_password( 8, false, false ) ) );

		if ( ! $gd_system_cron_event_temp_blacklist ) {

			return $generated_key;

		}

		$existing_key = array_search( $hook, $gd_system_cron_event_temp_blacklist );

		return ( false !== $existing_key ) ? $existing_key : $generated_key;

	}

}

WP_CLI::add_command( 'cron event wppaas', 'GD_System_Plugin_Cron_Event_Command' );
