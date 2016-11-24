<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class GD_System_Plugin_Dashboard_Widgets {

	/**
	 * User cap to view custom widgets
	 *
	 * @var string
	 */
	private $cap = 'publish_posts';

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( gd_is_mt() || gd_is_reseller() || ! gd_has_used_wpem() ) {

			return;

		}

		add_action( 'wp_dashboard_setup', array( $this, 'init' ) );

	}

	/**
	 * Register custom widgets
	 *
	 * @action wp_dashboard_setup
	 */
	public function init() {

		if ( ! current_user_can( $this->cap ) ) {

			return;

		}

		/**
		 * Filter to allow custom dashboard widgets to be toggled on/off
		 *
		 * @var bool
		 */
		$show = (bool) apply_filters( 'gd_system_show_dashboard_widgets', true );

		if ( ! $show ) {

			return;

		}

		wp_add_dashboard_widget(
			'gd_system_dashboard_godaddy_garage',
			esc_html_x( 'GoDaddy Garage', 'The name of our company blog found at https://garage.godaddy.com', 'gd_system' ),
			array( $this, 'widget_godaddy_garage' )
		);

	}

	/**
	 * Widget: GoDaddy Garage
	 */
	public function widget_godaddy_garage() {

		$garage_rss = $this->get_feed_items( 'https://garage.godaddy.com/webpro/wordpress/feed/' );

		$item = ! empty( $garage_rss[0] ) ? $garage_rss[0] : null;

		if ( is_a( $item, 'SimplePie_Item' ) ) : ?>

			<div class="rss-widget">

				<ul>
					<li>
						<a href="<?php echo esc_url( $item->get_link() ) ?>" target="_blank" class="rsswidget"><?php echo esc_html( $item->get_title() ) ?></a>
						<span class="rss-date"><?php echo esc_html( $item->get_date( get_option( 'date_format' ) ) ) ?></span>
						<div class="rssSummary"><?php echo wp_trim_words( $item->get_description(), 25, ' [&hellip;]' ) ?></div>
					</li>
				</ul>

			</div>

			<?php unset( $garage_rss[0] ) ?>

		<?php endif; ?>

		<?php if ( ! empty( $garage_rss ) ) : ?>

			<div class="rss-widget">

				<ul>
				<?php foreach ( $garage_rss as $item ) : ?>
					<?php if ( is_a( $item, 'SimplePie_Item' ) ) : ?>
						<li>
							<a href="<?php echo esc_url( $item->get_link() ) ?>" target="_blank" class="rsswidget"><?php echo esc_html( $item->get_title() ) ?></a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
				</ul>

			</div>

		<?php endif;

	}

	/**
	 * Return items from a given RSS feed
	 *
	 * @param  string $url
	 * @param  int    $limit (optional)
	 * @param  int    $offset (optional)
	 *
	 * @return array
	 */
	private function get_feed_items( $url, $limit = 5, $offset = 0 ) {

		if ( ! function_exists( 'fetch_feed' ) ) {

			require_once ABSPATH . WPINC . '/feed.php';

		}

		$rss = fetch_feed( $url );

		if ( is_wp_error( $rss ) ) {

			return array();

		}

		$limit = $rss->get_item_quantity( absint( $limit ) );

		if ( 0 === $limit ) {

			return array();

		}

		$items = $rss->get_items( absint( $offset ), $limit );

		foreach ( $items as $item ) {

			$output[] = array(
				'title'     => '',
				'permalink' => $item->get_link(),
				'excerpt'   => '',
			);

		}

		return $items;

	}

}
