<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * GoDaddy admin page with tabs
 *
 * @version 1.1.0
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 */
class GD_System_Plugin_Admin_Page {

	/**
	 * User cap required to view page
	 *
	 * @var string
	 */
	private $cap = 'activate_plugins';

	/**
	 * Page slug
	 *
	 * @var string
	 */
	private $slug = 'godaddy';

	/**
	 * Menu position
	 *
	 * Use a long tail decimal to reduce chance
	 * the of conflicts with other menu items.
	 *
	 * @var string
	 */
	private $position = '2.000001';

	/**
	 * Array of registered tabs
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Current tab slug
	 *
	 * @var string
	 */
	private $tab = 'help';

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		if ( gd_is_mt() || gd_is_reseller() ) {

			return;

		}

		/**
		 * Filter the admin menu position
		 *
		 * Note: By default we will use a long decimal string to reduce the
		 * chance of position conflicts with other menu items, see Codex.
		 *
		 * @return string
		 */
		$this->position = (string) apply_filters( 'gd_system_page_menu_position', $this->position );

		add_action( 'init',             array( $this, 'init' ) );
		add_action( 'admin_menu',       array( $this, 'register_menu_page' ) );
		add_action( 'admin_bar_menu',   array( $this, 'admin_bar_menu' ), 110 );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

	}

	/**
	 * Register tabs
	 *
	 * @action init
	 */
	public function init() {

		$this->tabs = array(
			'help'    => __( 'FAQ &amp; Support', 'gd_system' ),
			'hire'    => __( 'Hire a Pro', 'gd_system' ),
			'plugins' => __( 'Plugin Partners', 'gd_system' ),
		);

		/**
		 * Only display the `Hire A Pro` tab to customers that:
		 *
		 * 1. Have completed WPEM
		 * 2. Speak English
		 * 3. Are located in the United States
		 */
		if ( ! gd_has_used_wpem() || ! gd_is_english() || 'US' !== gd_wpem_country_code() ) {

			unset( $this->tabs['hire'] );

		}

		$tab = filter_input( INPUT_GET, 'tab' );

		$this->tab = ! empty( $tab ) ? sanitize_key( $tab ) : $this->tab;

	}

	/**
	 * Enqueue styles needed for the admin bar
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_scripts() {

		if ( ! is_user_logged_in() ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'gd_system_admin_css', GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/css/admin$suffix.css", array(), '0.0.3' );

	}

	/**
	 * Enqueue admin styles
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'gd_system_admin_css', GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/css/admin{$suffix}.css", array(), '0.0.3' );

		if ( sprintf( 'toplevel_page_%s', $this->slug ) !== $hook ) {

			return;

		}

		if ( 'help' === $this->tab ) {

			wp_enqueue_script(
				'gd_system_iframeresizer',
				GD_SYSTEM_PLUGIN_URL . 'gd-system-plugin/js/iframeResizer.min.js',
				[],
				'3.5.1',
				false
			);

			wp_enqueue_script(
				'gd_system_iframeresizer_ie8_polyfils',
				GD_SYSTEM_PLUGIN_URL . 'gd-system-plugin/js/iframeResizer.ie8.polyfils.min.js',
				[],
				'3.5.1',
				false
			);

			wp_script_add_data( 'gd_system_iframeresizer_ie8_polyfils', 'conditional', 'lte IE 8' );

		}

		if ( 'plugins' === $this->tab ) {

			wp_enqueue_style( 'thickbox' );

			wp_enqueue_style( 'plugin-install' );

			wp_enqueue_script( 'plugin-install' );

		}

	}

	/**
	 * Register menu page
	 *
	 * @action admin_menu
	 */
	public function register_menu_page() {

		global $submenu;

		add_menu_page(
			__( 'GoDaddy', 'gd_system' ),
			__( 'GoDaddy', 'gd_system' ),
			$this->cap,
			$this->slug,
			[ $this, 'render_menu_page' ],
			'div',
			$this->position
		);

		foreach ( $this->tabs as $slug => $label ) {

			$parent_slug = $this->slug;

			$permalink = add_query_arg(
				[
					'page' => $this->slug,
					'tab'  => $slug,
				],
				self_admin_url( 'admin.php' )
			);

			$submenu[ $this->slug ][] = [ $label, $this->cap, $permalink ];

			$closure = function( $submenu_file, $parent_file ) use ( $parent_slug, $slug, $permalink, &$closure ) {

				if ( $parent_file === $parent_slug ) {

					if ( $slug === filter_input( INPUT_GET, 'tab' ) ) {

						$submenu_file = $permalink;

					}

					// No need to continue applying the filter once we found our parent
					remove_filter( 'submenu_file', $closure );
				}

				return $submenu_file;

			};

			add_filter( 'submenu_file', $closure, 10, 2 );

		}

	}

	/**
	 * Add direct link to GoDaddy tabs in menu
	 *
	 * @param $admin_bar
	 */
	public function admin_bar_menu( $admin_bar ) {

		// Return early if user doesn't have cap or we are in CLI mode
		if ( ! current_user_can( $this->cap ) || ! class_exists( 'GD_System_Plugin_Admin_Menu' ) ) {

			return;

		}

		foreach ( $this->tabs as $slug => $label ) {

			$admin_bar->add_menu( [
				'parent' => GD_System_Plugin_Admin_Menu::ADMIN_MENU_SLUG,
				'id'     => GD_System_Plugin_Admin_Menu::ADMIN_MENU_SLUG . '-' . $slug,
				'title'  =>  $label,
				'href'   => add_query_arg(
					[
						'page' => $this->slug,
						'tab'  => $slug,
					],
					self_admin_url( 'admin.php' )
				),
			] );

		}

	}

	/**
	 * Modify admin body classes
	 *
	 * @action admin_body_class
	 *
	 * @param  string $classes
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {

		$classes = array_map( 'trim', explode( ' ', $classes ) );

		if ( 'plugins' === $this->tab ) {

			$classes[] = 'plugin-install-php';

		}

		return implode( ' ', $classes );

	}

	/**
	 * Render menu page
	 */
	public function render_menu_page() {

		?>
		<div class="wrap <?php printf( '%s-tab-%s', esc_attr( $this->slug ), esc_attr( $this->tab ) ) ?>">

			<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>

			<?php if ( ! empty( $this->tabs ) ) : ?>

				<h2 class="nav-tab-wrapper">

					<?php foreach ( $this->tabs as $name => $label ) : ?>

						<a href="<?php echo esc_url( add_query_arg( array( 'tab' => $name ) ) ) ?>" class="nav-tab<?php if ( $this->tab === $name ) : ?> nav-tab-active<?php endif; ?>"><?php echo esc_html( $label ) ?></a>

					<?php endforeach; ?>

				</h2>

			<?php endif;

			if ( array_key_exists( $this->tab, $this->tabs ) && method_exists( $this, "render_menu_page_{$this->tab}" ) ) {

				call_user_func( array( $this, "render_menu_page_{$this->tab}" ) );

			}

		?>
		</div>
		<?php

	}

	public function render_menu_page_help() {

		$language = get_option( 'WPLANG', 'www' );

		$parts = explode( '_', $language );

		$subdomain = ! empty( $parts[1] ) ? strtolower( $parts[1] ) : strtolower( $language );

		// Overrides
		switch ( $subdomain ) {

			case '';

				$subdomain = 'www'; // Default

				break;

			case 'uk':

				$subdomain = 'ua'; // Ukrainian (Українська)

				break;

			case 'el':

				$subdomain = 'gr'; // Greek (Ελληνικά)

				break;

		}

		?>
		<iframe src="<?php echo esc_url( "https://{$subdomain}.godaddy.com/help/managed-wordpress-1000021" ) ?>" frameborder="0" scrolling="no"></iframe>

		<script type="text/javascript">
			iFrameResize( {
				bodyBackground: 'transparent',
				checkOrigin: false,
				heightCalculationMethod: 'taggedElement'
			} );
		</script>
		<?php

	}

	/**
	 * Hire tab content
	 *
	 * Note: The $version var value should be incremented
	 * each time new changes are introduced to this page
	 * for tracking purposes.
	 */
	public function render_menu_page_hire() {

		// Content version number for campaign tracking
		$version = '002';

		$pro_connect_url = add_query_arg(
			array(
				'utm_source'   => 'managed_wordpress',
				'utm_medium'   => 'referral',
				'utm_campaign' => sprintf( 'gdplug-hirepro-%s', $version ),
			),
			'https://www.godaddy.com/pro-connect'
		);

		$pws_schedule_call_url = add_query_arg(
			array(), // @TODO
			'https://www.godaddy.com/websites/web-design/contact-us'
		);

		$pws_learn_more_url = add_query_arg(
			array(), // @TODO
			'https://www.godaddy.com/websites/web-design'
		);

		?>
		<div class="dashboard-widgets-wrap">

			<div id="dashboard-widgets" class="metabox-holder">

				<div id="normal-sortables" class="meta-box-sortables ui-sortable">

					<div id="dashboard_pro_connect" class="postbox">

						<h2 class="hndle ui-sortable-handle"><span><?php _e( 'Find a Freelancer', 'gd_system' ) ?></span></h2>

						<div class="inside">

							<div class="featured-image">

								<img src="<?php echo GD_SYSTEM_PLUGIN_URL . 'gd-system-plugin/images/godaddy-pro-connect.png' ?>">

							</div>

							<p><?php _e( 'Need a Pro to build your website?', 'gd_system' ) ?></p>

							<p><?php _e( "We'll put you in touch with the best freelancer to help build your WordPress site according to your requirements and budget. It's free to try.", 'gd_system' ) ?></p>

							<p class="submit">

								<a href="<?php echo esc_url( $pro_connect_url ) ?>" target="_blank" class="button button-primary"><?php _e( 'Get an Estimate', 'gd_system' ) ?> <span class="dashicons dashicons-external"></span></a>

							</p>

							<div class="clear"></div>

						</div>

					</div>

					<div id="dashboard_pws" class="postbox">

						<h2 class="hndle ui-sortable-handle"><span><?php _e( 'Hire a Professional', 'gd_system' ) ?></span></h2>

						<div class="inside">

							<div class="featured-image">

								<img src="<?php echo GD_SYSTEM_PLUGIN_URL . 'gd-system-plugin/images/godaddy-pws.png' ?>">

							</div>

							<p><?php _e( 'Let us build it for you!', 'gd_system' ) ?></p>

							<p><?php _e( 'Get your business online fast with a professionally-designed WordPress website custom tailored just for you.', 'gd_system' ) ?></p>

							<p class="submit">

								<a href="<?php echo esc_url( $pws_schedule_call_url ) ?>" target="_blank" class="button button-primary"><?php _e( 'Schedule a Call', 'gd_system' ) ?> <span class="dashicons dashicons-external"></span></a>

								<a href="<?php echo esc_url( $pws_learn_more_url ) ?>" target="_blank" class="button button-secondary"><?php _e( 'Learn More', 'gd_system' ) ?> <span class="dashicons dashicons-external"></span></a>

							</p>

							<div class="clear"></div>

						</div>

					</div>

				</div>

			</div>

		</div>
		<?php

	}

	public function render_menu_page_plugins() {

		$plugins = (array) $this->get_plugins();

		?>
		<div id="welcome-panel" class="welcome-panel">

			<div class="welcome-panel-content">

				<h2><?php _e( 'Meet the plugins that meet our high standards.', 'gd_system' ) ?></h2>

				<p class="about-description"><?php _e( "We've partnered with the world’s top WordPress plugin authors to provide a list of plugins that work well with GoDaddy WordPress hosting.", 'gd_system' ) ?></p>

			</div>

		</div>

		<div id="plugin-filter">

			<div class="wp-list-table widefat plugin-install">

				<h2 class="screen-reader-text"><?php _e( 'Plugins list' ) ?></h2>

				<div id="the-list">

					<?php if ( ! $plugins ) : ?>

						<div class="error">

							<p><?php _e( 'Whoops! There was a problem fetching the list of plugins, please try reloading this page.', 'gd_system' ) ?></p>

						</div>

					<?php endif; ?>

					<?php foreach ( $plugins as $plugin ) :

						if ( ! function_exists( 'install_plugin_install_status' ) ) {

							require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

						}

						$status = install_plugin_install_status( $plugin );

						$install_status = ! empty( $status['status'] ) ? $status['status'] : 'install';

						$install_url = ! empty( $status['url'] ) ? $status['url'] : null;

						$install_file = ! empty( $status['file'] ) ? $status['file'] : null;

						$more_details_link = add_query_arg(
							array(
								'tab'       => 'plugin-information',
								'plugin'    => urlencode( $plugin['slug'] ),
								'TB_iframe' => 'true',
								'width'     => 600,
								'height'    => 550,
							),
							self_admin_url( 'plugin-install.php' )
						);

						?>

						<div class="plugin-card plugin-card-<?php echo esc_attr( $plugin['slug'] ) ?>">

							<div class="plugin-card-top">

								<div class="name column-name">

									<h3>

										<?php if ( $plugin['plugins_api'] ) : ?>

											<a href="<?php echo esc_url( $more_details_link ) ?>" class="thickbox" aria-label="<?php esc_attr_e( sprintf( __( 'More information about %s' ), $plugin['name'] ) ) ?>" data-title="<?php echo esc_attr( $plugin['name'] ) ?>">

										<?php endif; ?>

												<?php echo esc_html( $plugin['name'] ) ?>

												<img src="<?php echo esc_url( $plugin['icon'] ) ?>" class="plugin-icon" alt="">

										<?php if ( $plugin['plugins_api'] ) : ?>

											</a>

										<?php endif; ?>

									</h3>

								</div>

								<div class="action-links">

									<ul class="plugin-action-buttons">

										<?php if ( $plugin['plugins_api'] && 'install' === $install_status && $install_url ) : ?>

											<li><a class="install-now button" href="<?php echo esc_url( $install_url ) ?>" data-slug="<?php echo esc_attr( $plugin['slug'] ) ?>" data-name="<?php echo esc_attr( $plugin['name'] ) ?>" aria-label="<?php esc_attr_e( sprintf( __( 'Install %s now' ), $plugin['name'] ) ) ?>"><?php _e( 'Install Now' ) ?></a></li>

										<?php elseif ( $plugin['plugins_api'] && 'update_available' === $install_status && $install_url ) : ?>

											<li><a class="update-now button" href="<?php echo esc_url( $install_url ) ?>" data-plugin="<?php echo esc_attr( $install_file ) ?>" data-slug="<?php echo esc_attr( $plugin['slug'] ) ?>" data-name="<?php echo esc_attr( $plugin['name'] ) ?>" aria-label="<?php esc_attr_e( sprintf( __( 'Update %s now' ), $plugin['name'] ) ) ?>"><?php _e( 'Update Now' ) ?></a></li>

										<?php elseif ( false !== strpos( $install_status, '_installed' ) ) : ?>

											<li><span class="button button-disabled" title="<?php esc_attr_e( 'This plugin is already installed and is up to date' ) ?>"><?php _ex( 'Installed', 'plugin' ) ?></span></li>

										<?php endif; ?>

										<?php if ( ! $plugin['plugins_api'] ) : ?>

											<li><a href="<?php echo esc_url( $plugin['homepage'] ) ?>" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'Learn More', 'gd_system' ) ?></a></li>

										<?php endif; ?>

									</ul>

								</div>

								<div class="desc column-description">

									<p><?php echo esc_html( $plugin['short_description'] ) ?></p>

									<p class="authors">

										<cite><?php printf( __( 'By %s' ), wp_kses_post( $plugin['author'] ) ) ?></cite>

									</p>

								</div>

							</div>

						</div>

					<?php endforeach; ?>

				</div>

			</div>

		</div>
		<?php

	}

	/**
	 * Get plugin data
	 *
	 * @return array
	 */
	private function get_plugins() {

		$transient = 'gd_ppp_data';

		if ( false === ( $plugins = get_transient( $transient ) ) ) {

			$plugins = $this->fetch_plugins();

			if ( ! $plugins ) {

				return array();

			}

			foreach ( $plugins as $slug => $data ) {

				if ( $data && empty( $data['plugins_api'] ) ) {

					$plugins[ $slug ]['plugins_api'] = false;

					$plugins[ $slug ]['slug'] = $slug;

					// CDN cache is indefinite, ignores query vars
					$plugins[ $slug ]['icon'] = sprintf( '//cdn.rawgit.com/godaddy/wp-plugin-partners/master/%s', $data['icon'] );

					$plugins[ $slug ]['last_updated'] = ! empty( $data['last_updated'] ) ? strtotime( $data['last_updated'] ) : null;

					continue;

				}

				if ( ! function_exists( 'plugins_api' ) ) {

					require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

				}

				$_data = (array) plugins_api(
					'plugin_information',
					array(
						'slug'   => $slug,
						'fields' => array(
							'active_installs'   => true,
							'added'             => false,
							'author_profile'    => false,
							'compatibility'     => false,
							'donate_link'       => false,
							'downloaded'        => true,
							'download_link'     => false,
							'icons'             => true,
							'sections'          => false,
							'short_description' => true,
							'ratings'           => false,
							'tags'              => false,
						),
					)
				);

				$_data['plugins_api'] = true;

				$_data['icon'] = array_shift( $_data['icons'] );

				unset(
					$_data['author_profile'],
					$_data['contributors'],
					$_data['icons'],
					$_data['num_ratings'],
					$_data['rating']
				);

				$_data['last_updated'] = strtotime( $_data['last_updated'] );

				$_data = array_merge( $_data, $data ); // Allow overrides

				$plugins[ $slug ] = $_data;

			}

			if ( ! $plugins ) {

				return array();

			}

			shuffle( $plugins );

			set_transient( $transient, $plugins, 12 * HOUR_IN_SECONDS ); // Twice daily

		}

		return $plugins;

	}

	/**
	 * Fetch plugin data
	 *
	 * @return array|bool
	 */
	private function fetch_plugins() {

		$response = wp_remote_get( sprintf( 'https://raw.githubusercontent.com/godaddy/wp-plugin-partners/master/manifest.json?ver=%d', time() ) );

		if ( ! $response || is_wp_error( $response ) ) {

			return false;

		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		return ( ! $data ) ? false : (array) $data;

	}

}
