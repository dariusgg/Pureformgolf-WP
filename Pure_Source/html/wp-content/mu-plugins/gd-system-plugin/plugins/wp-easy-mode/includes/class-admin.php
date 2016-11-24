<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Admin {

	use Data;

	/**
	 * Holds the image api instance
	 *
	 * @var object
	 */
	private $image_api;

	/**
	 * Holds the Log instance
	 *
	 * @var object
	 */
	private $log;

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->cap   = 'manage_options';
		$this->steps = [];

		add_action( 'init', [ $this, 'load' ] );

	}

	/**
	 * Return an array of steps
	 *
	 * @return array
	 */
	public function get_steps() {

		$steps = (array) $this->steps;

		if ( ! $steps ) {

			return [];

		}

		return $steps;

	}

	/**
	 * Load admin area
	 *
	 * @action init
	 */
	public function load() {

		if ( ! current_user_can( $this->cap ) ) {

			return;

		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			return;

		}

		$this->log       = new Log;
		$this->image_api = new Image_API;

		$this->register_steps();

		$this->maybe_force_redirect();

		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_action( 'admin_init', [ $this, 'submit' ] );
		add_action( 'admin_init', [ $this, 'screen' ] );

		add_filter( 'option_blogname', function( $value ) {

			return wpem_get_step_field( 'blogname', 'settings', $value );

		} );

		add_filter( 'option_blogdescription', function( $value ) {

			return wpem_get_step_field( 'blogdescription', 'settings', $value );

		} );

	}

	/**
	 * Determine if we are viewing the wizard
	 *
	 * @return bool
	 */
	public function is_wizard() {

		return ( current_user_can( $this->cap ) && wpem()->page_slug === filter_input( INPUT_GET, 'page' ) );

	}

	/**
	 * Register the steps used by the wizard
	 */
	private function register_steps() {

		// Some steps depend on the image api
		$this->steps = [
			new Step_Start( $this->log ),
			new Step_Settings( $this->log, $this->image_api ),
			new Step_Contact( $this->log ),
			new Step_Theme( $this->log, $this->image_api ),
		];

		foreach ( $this->steps as $i => $step ) {

			$step->position = $i + 1;

			$step->url = add_query_arg(
				[
					'step' => $step->name,
				],
				wpem_get_wizard_url()
			);

		}

		$this->last_viewed = $this->get_step_by( 'name', get_option( 'wpem_last_viewed', 'start' ) );

	}

	/**
	 * Force the wizard to be completed
	 */
	private function maybe_force_redirect() {

		if ( ! $this->is_wizard() ) {

			wp_safe_redirect( $this->last_viewed->url );

			exit;

		}

		$current_step = wpem_get_current_step();

		if ( $current_step->position <= $this->last_viewed->position ) {

			return;

		}

		$steps = array_slice( $this->get_steps(), $this->last_viewed->position - 1 );

		foreach ( $steps as $step ) {

			if ( $step->position === $current_step->position ) {

				break;

			}

			if ( ! $step->can_skip ) {

				wp_safe_redirect( $step->url );

				exit;

			}

		}

	}

	/**
	 * Register admin menu and assets
	 *
	 * @action admin_menu
	 */
	public function menu() {

		add_dashboard_page(
			_x( 'WP Easy Mode', 'Main plugin title', 'wp-easy-mode' ),
			_x( 'Easy Mode', 'Menu title', 'wp-easy-mode' ),
			$this->cap,
			wpem()->page_slug,
			[ $this, 'screen' ]
		);

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'font-awesome',
			wpem()->assets_url . 'css/font-awesome.min.css',
			[],
			'4.5.0'
		);

		wp_register_style(
			'wpem-fullscreen',
			wpem()->assets_url . "css/fullscreen{$suffix}.css",
			[ 'dashicons', 'buttons', 'install' ],
			wpem()->version
		);

		wp_register_script(
			'jquery-blockui',
			wpem()->assets_url . 'js/jquery.blockui.min.js',
			[ 'jquery' ],
			'2.70.0'
		);

		wp_register_script(
			'wpem',
			wpem()->assets_url . "js/common{$suffix}.js",
			[ 'jquery' ],
			wpem()->version
		);

		wp_register_script(
			'wpem-contact',
			wpem()->assets_url . "js/contact{$suffix}.js",
			[ 'wpem' ],
			wpem()->version
		);

		wp_register_script(
			'wpem-theme',
			wpem()->assets_url . "js/theme{$suffix}.js",
			[ 'wpem', 'wp-pointer', 'wpem-pointers' ],
			wpem()->version
		);

		wp_localize_script(
			'wpem',
			'wpem_vars',
			[
				'step' => wpem_get_current_step()->name,
				'i18n' => [
					'exit_confirm' => esc_attr__( 'Are you sure you want to exit and configure WordPress on your own?', 'wp-easy-mode' ),
				],
			]
		);

		/**
		 * Filter the list of themes to display
		 *
		 * @var array
		 */
		$themes = (array) apply_filters(
			'wpem_themes',
			[
				'twentysixteen',
				'twentyfifteen',
				'twentyfourteen',
			]
		);

		wp_localize_script(
			'wpem-theme',
			'wpem_theme_vars',
			[
				'themes'       => array_map( 'esc_js', array_values( array_unique( $themes ) ) ),
				'i18n'         => [
					'expand'   => esc_attr__( 'Expand Sidebar', 'wp-easy-mode' ),
					'collapse' => esc_attr__( 'Collapse Sidebar', 'wp-easy-mode' ),
				],
				'preview_url'  => static::demo_site_url(
					[
						'blogname'        => get_option( 'blogname' ),
						'blogdescription' => get_option( 'blogdescription' ),
						'email'           => wpem_get_contact_info( 'email' ),
						'phone'           => wpem_get_contact_info( 'phone' ),
						'fax'             => wpem_get_contact_info( 'fax' ),
						'address'         => wpem_get_contact_info( 'address' ),
						'social'          => implode( ',', wpem_get_social_profiles() ),
					],
					false
				),
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'customizer_url'       => wpem_get_customizer_url(
					[
						'return' => self_admin_url(),
						'wpem'   => 1,
					]
				),
			]
		);

	}

	/**
	 * Return a URL for the demo API
	 *
	 * @param  array $args (optional)
	 * @param  bool  $hide_empty_args (optional)
	 *
	 * @return string
	 */
	public static function demo_site_url( $args = [], $hide_empty_args = true ) {

		$defaults = [
			'site_type'     => wpem_get_site_type(),
			'site_industry' => wpem_get_site_industry(),
			'lang'          => get_locale(),
		];

		$args = array_merge( $defaults, $args );
		$args = ( $hide_empty_args ) ? array_filter( $args ) : $args;

		return add_query_arg(
			array_map( 'urlencode', $args ),
			esc_url_raw( wpem()->api_url )
		);

	}

	/**
	 * Listen for POST requests and process them
	 *
	 * @action admin_init
	 */
	public function submit() {

		$nonce = filter_input( INPUT_POST, 'wpem_step_nonce' );

		$name = filter_input( INPUT_POST, 'wpem_step_name' );

		if ( false === wp_verify_nonce( $nonce, sprintf( 'wpem_step_nonce-%s-%d', $name, get_current_user_id() ) ) ) {

			return;

		}

		$step = $this->get_step_by( 'name', $name );

		if ( ! $step ) {

			return;

		}

		$took = filter_input( INPUT_POST, 'wpem_step_took' );

		if ( $took ) {

			$this->log->add_step_time( $took );

		}

		$step->callback();

		$next_step = wpem_get_next_step();

		if ( $next_step ) {

			update_option( 'wpem_last_viewed', $next_step->name );

			wp_safe_redirect( $next_step->url );

			exit;

		}

		new Done( $this->log );

	}

	/**
	 * Register admin menu screen
	 *
	 * @action admin_init
	 */
	public function screen() {

		$template = wpem()->base_dir . 'templates/fullscreen.php';

		if ( is_readable( $template ) ) {

			require_once $template;

			exit;

		}

	}

	/**
	 * Get a step by name or actual position
	 *
	 * @param  string $field
	 * @param  mixed  $value
	 *
	 * @return object
	 */
	public function get_step_by( $field, $value ) {

		$steps = (array) $this->steps;

		if ( empty( $steps ) || empty( $value ) ) {

			return;

		}

		if ( 'name' === $field ) {

			foreach ( $steps as $step ) {

				if ( $step->name !== $value ) {

					continue;

				}

				return $step;

			}

		}

		if ( 'position' === $field && is_numeric( $value ) ) {

			foreach ( $steps as $step ) {

				if ( $step->position !== $value ) {

					continue;

				}

				return $step;

			}

		}

	}

}
