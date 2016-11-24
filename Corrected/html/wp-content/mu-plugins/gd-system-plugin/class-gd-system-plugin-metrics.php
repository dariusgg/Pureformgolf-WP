<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class GD_System_Plugin_Metrics {

	/**
	 * Whether a log has occurred in the current process.
	 *
	 * @var bool
	 */
	private static $logged = false;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_login', function( $user_login, $user ) {

			if ( user_can( $user, 'activate_plugins' ) ) {

				$this->log( 'login' );

			}

		}, 10, 2 );

		/**
		 * We will ignore publish activity when:
		 *
		 * 1. WP Easy Mode is running.
		 * 2. WP-CLI is being used.
		 * 3. Not triggered by a logged in user.
		 */
		if ( gd_is_doing_wpem() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

			return;

		}

		// Themes
		add_action( 'switch_theme', function() { $this->log_user_action( 'publish' ); } );

		// Plugins
		add_action( 'activated_plugin',   function() { $this->log_user_action( 'publish' ); } );
		add_action( 'deactivated_plugin', function() { $this->log_user_action( 'publish' ); } );

		// Attachments
		add_action( 'add_attachment',    function() { $this->log_user_action( 'publish' ); } );
		add_action( 'edit_attachment',   function() { $this->log_user_action( 'publish' ); } );
		add_action( 'delete_attachment', function() { $this->log_user_action( 'publish' ); } );

		// Create/edit/schedule posts
		add_action( 'save_post', function( $post_id, $post ) {

			if ( in_array( $post->post_status, [ 'publish', 'future' ] ) ) {

				$this->log_user_action( 'publish' );

			}

		}, 10, 2 );

		// Trash posts
		add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {

			if ( in_array( $old_status, [ 'publish', 'future' ] ) && 'trash' === $new_status ) {

				$this->log_user_action( 'publish' );

			}

		}, 10, 3 );

		// Widgets
		add_action( 'updated_option', function( $option ) {

			if ( 0 === strpos( $option, 'widget_' ) ) {

				$this->log_user_action( 'publish' );

			}

		} );

	}

	/**
	 * Log an event if triggered by a logged in user.
	 *
	 * @param  string $e_id
	 *
	 * @return bool
	 */
	private function log_user_action( $e_id ) {

		return is_user_logged_in() ? $this->log( $e_id ) : false;

	}

	/**
	 * Log an event to the database and syslog.
	 *
	 * @param  string $e_id
	 *
	 * @return bool
	 */
	private function log( $e_id ) {

		if ( static::$logged || ! $e_id ) {

			return false;

		}

		$time = time();
		$e_id = sanitize_key( $e_id );

		update_option( "gd_system_last_{$e_id}", $time );

		if ( ! get_option( "gd_system_first_{$e_id}" ) ) {

			update_option( "gd_system_first_{$e_id}", $time );

		}

		if ( ! gd_asap_key() || ! gd_xid() ) {

			return false;

		}

		$data = [
			'asapkey' => gd_asap_key(),
			'xid'     => gd_xid(),
			'e_id'    => sprintf( 'hosting.wpaas.account.wpadmin.%s', $e_id ),
			'e_time'  => $this->e_time(),
		];

		if ( false === openlog( 'wpaas-event', LOG_NDELAY | LOG_PID, LOG_LOCAL1 ) ) {

			return false;

		}

		syslog( LOG_INFO, wp_json_encode( $data ) );

		closelog();

		return static::$logged = true;

	}

	/**
	 * Return the current time in ISO 8601 extended format.
	 *
	 * e.g. 2016-03-31T14:17:47.67Z
	 *
	 * @return string
	 */
	private function e_time() {

		$time     = microtime( true );
		$micro    = sprintf( '%06d', ( $time - floor( $time ) ) * 1000000 );
		$datetime = new DateTime( gmdate( 'Y-m-d H:i:s.' . $micro, $time ) );

		return sprintf(
			'%s%02dZ',
			$datetime->format( 'Y-m-d\TH:i:s.' ),
			floor( $datetime->format( 'u' ) / 10000 )
		);

	}

}
