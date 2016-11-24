<?php
/**
 * Plugin Name: GoDaddy Admin Color Scheme
 * Description: Adds a custom GoDaddy color scheme option for the WordPress Admin.
 * Version: 1.0.1
 * Author: GoDaddy
 */

namespace WPPaaS;

class GD_Admin_Color_Scheme {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	const VERSION = '1.0.1';

	/**
	 * Admin_Color_Scheme constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_scheme' ], 1 );

	}

	/**
	 * Register the admin color scheme
	 *
	 * @action admin_init
	 */
	public function register_scheme() {

		$suffix = is_rtl() ? '-rtl' : '';
		$suffix .= SCRIPT_DEBUG ? '' : '.min';

		$url = add_query_arg(
			[
				'ver' => static::VERSION,
			],
			plugins_url( "assets/colors{$suffix}.css", __FILE__ )
		);

		wp_admin_css_color(
			'godaddy',
			'GoDaddy',
			$url,
			[
				'#212121',
				'#77c043',
				'#008a32',
				'#f2812e',
			],
			[
				'base'    => '#000',
				'focus'   => '#008a32',
				'current' => '#fff',
			]
		);

	}

}

new GD_Admin_Color_Scheme;
