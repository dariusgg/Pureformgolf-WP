<?php
/**
 * Plugin Name: WP Easy Mode
 * Description: Helping users launch their new WordPress site in just a few clicks.
 * Version: 2.0.3
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Text Domain: wp-easy-mode
 * Domain Path: /languages
 */

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/includes/autoload.php';

/**
 * WP Easy Mode
 *
 * Helping users launch their new WordPress site in just a few clicks.
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 * @author Jonathan Bardo <jbardo@godaddy.com>
 */
final class Plugin {

	use Singleton, Data;

	/**
	 * Hold screen id for plugin
	 */
	const SCREEN_ID = 'wpem';

	/**
	 * Admin object
	 *
	 * @var Admin
	 */
	public $admin;

	/**
	 * Class constructor
	 */
	private function __construct() {

		$this->version    = '2.0.3';
		$this->basename   = plugin_basename( __FILE__ );
		$this->base_dir   = plugin_dir_path( __FILE__ );
		$this->assets_url = plugin_dir_url( __FILE__ ) . 'assets/';
		$this->page_slug  = 'wpem';
		$this->api_url    = 'http://demo.wpeasymode.com/';

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			$composer_autoloader = __DIR__ . '/vendor/autoload.php';

			if ( file_exists( $composer_autoloader ) ) {

				// This is for enabling codeception
				require_once $composer_autoloader;

			}

			\WP_CLI::add_command( 'easy-mode', sprintf( '\%s\CLI', __NAMESPACE__ ) );

			return;

		}

		if ( ! is_admin() ) {

			return;

		}

		if ( ! $this->is_fresh_wp() ) {

			if ( ! $this->is_done() ) {

				add_filter( 'wpem_deactivate_plugins_on_quit', '__return_false' );

				wpem_quit();

			}

			return;

		}

		add_action( 'plugins_loaded', [ $this, 'i18n' ] );

		// Always allow external HTTP requests to our API
		add_filter( 'http_request_host_is_external', function( $allow, $host, $url ) {

			$api_url = parse_url( $this->api_url );

			return ( $api_url['host'] === $host ) ? true : $allow;

		}, 10, 3 );

		// Enqueue customizer if we are on this screen
		add_action( 'load-customize.php', function() {

			if ( filter_input( INPUT_GET, static::SCREEN_ID ) ) {

				new Customizer;

			}

		} );

		if ( $this->is_done() ) {

			$this->self_destruct();

			$this->deactivate();

			add_action( 'init', [ $this, 'maybe_redirect' ] );

			return;

		}

		define( 'WPEM_DOING_STEPS', true );

		$this->admin = new Admin;

	}

	/**
	 * Is this a fresh WordPress install?
	 *
	 * @return bool
	 */
	private function is_fresh_wp() {

		$log      = new Log;
		$is_fresh = $log->is_fresh_wp;

		if ( ! isset( $is_fresh ) ) {

			$is_fresh = $this->check_is_fresh_wp();

			$log->add( 'is_fresh_wp', $is_fresh );

		}

		return $is_fresh;

	}

	/**
	 * Check the WordPress database for freshness
	 *
	 * @return bool
	 */
	private function check_is_fresh_wp() {

		global $wpdb;

		$highest_post_id = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} ORDER BY ID DESC LIMIT 0,1" );
		$highest_user_id = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->users} ORDER BY ID DESC LIMIT 0,1" );
		$is_fresh        = ( $highest_post_id <= 2 && 1 === $highest_user_id );

		return (bool) apply_filters( 'wpem_check_is_fresh_wp', $is_fresh );

	}

	/**
	 * Has the wizard already been done?
	 *
	 * @return bool
	 */
	public function is_done() {

		$status = get_option( 'wpem_done' );

		return ! empty( $status );

	}

	/**
	 * Is WPEM running as a standalone plugin?
	 *
	 * @return bool
	 */
	public function is_standalone_plugin() {

		if ( ! function_exists( 'is_plugin_active' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		}

		return is_plugin_active( $this->basename );

	}

	/**
	 * Redirect away from the wizard screen
	 *
	 * @action init
	 */
	public function maybe_redirect() {

		if ( static::SCREEN_ID !== filter_input( INPUT_GET, 'page' ) ) {

			return;

		}

		wp_safe_redirect( self_admin_url() );

		exit;

	}

	/**
	 * Load languages
	 *
	 * @action plugins_loaded
	 */
	public function i18n() {

		load_plugin_textdomain( 'wp-easy-mode', false, dirname( $this->basename ) . '/languages' );

	}

	/**
	 * Self-destruct the plugin
	 */
	public function self_destruct() {

		if ( ! $this->is_standalone_plugin() ) {

			return;

		}

		/**
		 * Filter to self-destruct when done
		 *
		 * @var bool
		 */
		if ( ! (bool) apply_filters( 'wpem_self_destruct', true ) || defined( 'WPEM_DOING_TESTS' ) && WPEM_DOING_TESTS ) {

			return;

		}

		if ( ! class_exists( 'WP_Filesystem' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';

		}

		WP_Filesystem();

		global $wp_filesystem;

		$wp_filesystem->rmdir( $this->base_dir, true );

	}

	/**
	 * Deactivate the plugin silently
	 */
	public function deactivate() {

		if ( ! $this->is_standalone_plugin() ) {

			return;

		}

		/**
		 * Filter to deactivate when done
		 *
		 * @var bool
		 */
		if ( ! (bool) apply_filters( 'wpem_deactivate', true ) || defined( 'WPEM_DOING_TESTS' ) && WPEM_DOING_TESTS ) {

			return;

		}

		deactivate_plugins( $this->basename, true );

	}

}

wpem();
