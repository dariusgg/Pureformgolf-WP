<?php

namespace WPEM;

use \WP_CLI as WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class CLI extends \WP_CLI_Command {

	/**
	 * Reset the WordPress site to factory defaults.
	 *
	 * This command is most commonly used for testing purposes
	 * to undo everything that WP Easy Mode set up during the
	 * on-boarding process (content, themes & plugins).
	 *
	 * Note: Requires WP_DEBUG to be set to TRUE.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * [--lang]
	 * : Reset language to default
	 *
	 * ## EXAMPLES
	 *
	 *     wp easy-mode reset [--yes]
	 */
	public function reset( $args, $assoc_args ) {

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {

			WP_CLI::error( 'WP_DEBUG must be enabled to reset WP Easy Mode!' );

		}

		WP_CLI::confirm( 'Are you sure you want to reset your WordPress site? This cannot be undone.', $assoc_args );

		global $wpdb;

		/**
		 * Plugins
		 */

		foreach ( get_option( 'wpem_plugins', [] ) as $basename ) {

			$plugin = dirname( $basename );

			WP_CLI::line( "Deleting plugin: {$plugin} ..." );

			WP_CLI::launch_self( 'plugin uninstall', [ $plugin ], [ 'deactivate' => true ], false );

		}

		WP_CLI::line( 'Dropping custom database tables ...' );

		$mysql = $wpdb->get_results(
			"SELECT GROUP_CONCAT( table_name ) AS query FROM information_schema.tables
				WHERE ( `table_name` LIKE '{$wpdb->prefix}nf3_%' )
					OR ( `table_name` LIKE '{$wpdb->prefix}nf_%' )
					OR ( `table_name` LIKE '{$wpdb->prefix}ninja_forms_%' )
					OR ( `table_name` LIKE '{$wpdb->prefix}woocommerce_%' );"
		);

		if ( isset( $mysql[0]->query ) ) {

			$tables = implode( ',', array_unique( explode( ',', $mysql[0]->query ) ) );

			$wpdb->query( "DROP TABLE IF EXISTS {$tables};" );

		}

		$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE ( `option_name` LIKE 'nf_%' ) OR ( `option_name` LIKE '%ninja_forms%' ) OR ( `option_name` LIKE '%woocommerce%' );" );
		$wpdb->query( "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` LIKE '%woocommerce%';" );

		/**
		 * Themes
		 */

		WP_CLI::line( sprintf( 'Activating default theme: %s ...', WP_DEFAULT_THEME ) );

		WP_CLI::launch_self( 'theme install ' . WP_DEFAULT_THEME . ' --activate', [], [], false );

		if ( $theme = get_option( 'wpem_theme' ) ) {

			WP_CLI::line( "Deleting theme: {$theme} ..." );

			WP_CLI::launch_self( 'theme delete', [ $theme ], [], false );

		}

		if ( $parent_theme = get_option( 'wpem_parent_theme' ) ) {

			WP_CLI::line( "Deleting parent theme: {$parent_theme} ..." );

			WP_CLI::launch_self( 'theme delete', [ $parent_theme ], [], false );

		}

		/**
		 * Users
		 */

		WP_CLI::line( 'Removing all users except main admin ...' );

		$wpdb->query( "DELETE FROM `{$wpdb->users}` WHERE `ID` > 1" );

		/**
		 * Settings
		 */

		WP_CLI::line( 'Restoring default settings ...' );

		$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE ( `option_name` LIKE 'wpem_%' ) OR ( `option_name` LIKE '%_transient_%' ) OR ( `option_name` LIKE 'theme_mods_%' );" );

		update_option( 'blogname', 'A WordPress Site' );
		update_option( 'blogdescription', 'Just another WordPress site' );

		$wpdb->query( "DELETE FROM `{$wpdb->usermeta}` WHERE ( `meta_key` = 'sk_ignore_notice' ) OR ( `meta_key` = 'dismissed_wp_pointers' AND `meta_value` LIKE '%wpem_%' );" );

		WP_CLI::line( 'Deleting all sidebar widgets ...' );

		update_option( 'sidebars_widgets', [ 'wp_inactive_widgets' => [] ] );

		/**
		 * Uploads
		 */

		WP_CLI::line( 'Deleting all uploads ...' );

		WP_Filesystem();

		global $wp_filesystem;

		$uploads = wp_upload_dir();

		foreach ( glob( $uploads['basedir'] . '/*' ) as $file ) {

			$wp_filesystem->delete( $file, true );

		}

		/**
		 * Site content
		 */

		WP_CLI::line( 'Resetting site content ...' );

		$wpdb->query( "TRUNCATE TABLE `{$wpdb->comments}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->commentmeta}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->links}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->posts}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->postmeta}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->terms}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->term_taxonomy}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->term_relationships}`" );
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->termmeta}`" );

		/**
		 * Success
		 */

		WP_CLI::success( 'DONE!' );

	}

}
