<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Class GD_System_Plugin_SSL
 *
 * @version 1.0
 */
final class GD_System_Plugin_Pointers {

	/**
	 * Array of pointers
	 *
	 * @var array
	 */
	private $pointers = [];

	/**
	 * Constructor
	 */
	public function __construct( ) {

		add_action( 'init',                  [ $this, 'register_pointer' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

	}

	/**
	 * Register all pointers on init for i18n
	 */
	public function register_pointer() {

		$this->pointers[] = [
			'id'               => 'wpaas_admin_bar_buttons',
			'target'           => '#wp-admin-bar-wppass .ab-icon',
			'cap'              => 'activate_plugins',
			'site_created_max' => '2016-01-14',
			'options'          => [
				'content'  => wp_kses_post(
					sprintf(
						'<h3>%s</h3><p>%s</p>',
						__( 'Good news!', 'gd_system' ),
						__( 'You can now access <strong>Flush Cache</strong> and other links directly from the admin bar using your desktop or mobile device.', 'gd_system' )
					)
				),
				'position' => [
					'edge'  => 'top',
					'align' => 'left',
				],
			],
		];

	}


	/**
	 * Enqueue script for pointers
	 */
	public function admin_enqueue_scripts() {

		if ( ! $this->pointers ) {

			return;

		}

		$pointers = [];

		foreach ( $this->pointers as $pointer ) {

			if ( $this->is_viewable( $pointer ) ) {

				$pointers[] = $pointer;

			}

		}

		if ( ! $pointers ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wp-pointer' );

		wp_enqueue_script( 'wp-pointer' );

		wp_enqueue_script(
			'wpaas-pointers',
			GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/js/pointers{$suffix}.js",
			[ 'jquery', 'wp-pointer' ],
			'0.0.1',
			true
		);

		wp_localize_script( 'wpaas-pointers', 'wpaas_pointers', $pointers );

	}

	/**
	 * Check if a pointer is viewable
	 *
	 * @param  array $pointer
	 *
	 * @return bool
	 */
	private function is_viewable( $pointer ) {

		// Checking screen
		$should_appear_on_screen = true;

		if ( isset( $pointer['screen'] ) && $pointer['screen'] !== get_current_screen()->id ) {

			$should_appear_on_screen = false;

		}

		// Checking cap
		$user_can_see = current_user_can( ! empty( $pointer['cap'] ) ? $pointer['cap'] : 'read' );

		// Checking date
		$is_before_site_created_max = ! empty( $pointer['site_created_max'] ) ? ( gd_site_created_date() <= strtotime( $pointer['site_created_max'] ) ) : true;

		return (
			$user_can_see
			&& ! $this->is_dismissed( $pointer['id'] )
			&& $should_appear_on_screen
			&& $is_before_site_created_max
		);

	}

	/**
	 * Check if a pointer has been dismissed by the current user
	 *
	 * @param  string $pointer_id
	 *
	 * @return bool
	 */
	private function is_dismissed( $pointer_id ) {

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		return in_array( $pointer_id, $dismissed );

	}

}
