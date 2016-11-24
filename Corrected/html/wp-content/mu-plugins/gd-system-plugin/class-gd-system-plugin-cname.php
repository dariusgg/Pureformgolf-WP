<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Class GD_System_Plugin_CName
 *
 * Urge the user not to stay on the temporary cname and to switch to
 * a permanent domain.
 *
 * @version 1.1.0
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 * @author Jonathan Bardo <jbardo@godaddy.com>
 */
final class GD_System_Plugin_CName {

	/**
	 * Transient key for domain changed
	 *
	 * @var string
	 */
	const TRANSIENT_KEY = 'gd_system_domain_changed';

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'init' ) );

	}

	/**
	 * Initialize the script
	 *
	 * @action admin_init
	 */
	public function init() {

		if ( ! current_user_can( 'activate_plugins' ) ) {

			return;

		}

		// Show a notice to the user if we're on a temporary domain
		if ( gd_is_temp_domain() || ! $this->user_changed_domain() ) {

			add_action( 'admin_notices', array( $this, 'show_notice'), -PHP_INT_MAX );

		}

	}

	/**
	 * Check the API
	 *
	 * See if the user has changed their domain,
	 * but it isn't reflected here yet because we're
	 * waiting on the DNS TTL to take effect.
	 *
	 * @return bool
	 */
	private function user_changed_domain() {

		if ( false !== ( $transient = get_site_transient( static::TRANSIENT_KEY ) ) ) {

			return ( 'Y' === $transient );

		}

		// Check if the domain has been changed in the DB but DNS hasn't propagated yet
		global $gd_api, $gd_system_logger;

		$response = $gd_api->domain_changed( $_SERVER['HTTP_HOST'] );

		if ( is_wp_error( $response ) ) {

			$gd_system_logger->log( GD_SYSTEM_LOG_ERROR, 'Could not fetch response from API to check if domain was changed. Error [' . $response->get_error_code() . ']: ' . $response->get_error_message() );

		} else {

			$json = json_decode( $response['body'], true );

			if ( null === $json ) {

				$gd_system_logger->log( GD_SYSTEM_LOG_ERROR, 'Could not decode domain changed API response.' );

			}

		}

		$domain_changed = ! empty( $json['domainChanged'] ) ? 'Y' : 'N';

		$cname_timeout = isset( $conf['cname_timeout'] ) ? absint( $conf['cname_timeout'] ) : 300;

		set_site_transient( static::TRANSIENT_KEY, $domain_changed, $cname_timeout );

		return ( 'Y' === $domain_changed );

	}

	/**
	 * Show a message prompting the customer to update change domain
	 * to not use a temporary CNAME.
	 *
	 * @action admin_notices
	 */
	public function show_notice() {

		global $gd_system_config;

		if ( empty( $gd_system_config ) || $gd_system_config->missing_gd_config ) {

			return;

		}

		$config = $gd_system_config->get_config();

		if ( empty( $config['cname_link'] ) ) {

			return;

		}

		$url = str_replace( '%domain%', $_SERVER['HTTP_HOST'], $config['cname_link'] );

		$url = str_replace( '%pl_id%', defined( 'GD_RESELLER' ) ? GD_RESELLER : '', $url );

		$message = sprintf(
			__( '<strong>Note:</strong> You\'re using the temporary domain <strong>%s</strong>. <a href="%s" target="_blank">Change domain</a>', 'gd_system' ),
			$_SERVER['HTTP_HOST'],
			esc_attr( $url )
		);

		?>
		<div class="updated error">

			<p><?php echo wp_kses_post( $message ) ?></p>

		</div>
		<?php

	}

}
