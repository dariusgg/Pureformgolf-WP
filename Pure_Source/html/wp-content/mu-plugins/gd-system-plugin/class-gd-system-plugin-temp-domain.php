<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Class GD_System_Plugin_Temp_Domain
 *
 * Varnish actually disallow robots from indexing the site but there isn't a clear indication for
 * the end user. This class takes care of the latter.
 *
 * @version 1.1.0
 *
 * @author Jonathan Bardo <jbardo@godaddy.com>
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 */
final class GD_System_Plugin_Temp_Domain {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_filter( 'option_blog_public',            [ $this, 'option_blog_public' ], 9999 );
		add_filter( 'pre_update_option_blog_public', [ $this, 'pre_update_option_blog_public' ], 9999, 2 );
		add_action( 'admin_enqueue_scripts',         [ $this, 'admin_enqueue_scripts' ] );

	}

	/**
	 * Always disallow indexing on temp domains
	 *
	 * @filter option_blog_public
	 *
	 * @return string
	 */
	public function option_blog_public() {

		return '0';

	}

	/**
	 * Prevent updating the value on temp domains
	 *
	 * @filter pre_update_option_blog_public
	 *
	 * @param  string $new_value
	 * @param  string $old_value
	 *
	 * @return string
	 */
	public function pre_update_option_blog_public( $new_value, $old_value ) {

		return $old_value;

	}

	/**
	 * Enqueue small JS to disable blog_public checkbox on admin
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {

		if ( 'options-reading.php' !== $hook ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'gd_system_option_reading_temp_domain_js',
			GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/js/option-reading-temp-domain{$suffix}.js",
			[ 'jquery' ]
		);

		if ( gd_is_staging_site() ) {

			$notice = sprintf(
				__( '%s This is your staging site and it cannot be indexed by search engines.', 'gd_system' ),
				sprintf( '<strong>%s</strong>', __( 'Note:', 'gd_system' ) )
			);

		} else {

			$notice = sprintf(
				__( '%s Your site is using a temporary domain that cannot be indexed by search engines.', 'gd_system' ),
				sprintf( '<strong>%s</strong>', __( 'Note:', 'gd_system' ) )
			);

		}

		wp_localize_script(
			'gd_system_option_reading_temp_domain_js',
			'gd_system_option_reading_temp_domain_vars',
			[
				'blog_public_notice_text' => esc_js( $notice ),
			]
		);

	}

}
