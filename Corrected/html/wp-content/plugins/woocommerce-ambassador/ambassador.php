<?php
/**
Plugin Name: WooCommerce Ambassador Integration
Plugin URI: http://www.woothemes.com/products/ambassador-affiliate-program-integration/
Description: Ambassador affiliate system integration for WooCommerce powered websites.
Version: 1.1.4
Author: WooThemes
Author URI: http://www.woothemes.com
Requires at least: 3.3
Tested up to: 3.8

	Copyright: © 2009-2011 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'f4b2ceed351f8ece4ba0e9a9b2b92090', '18705' );

if (is_woocommerce_active()) {

	/**
	 * Add settings link
	 **/
	if ( ! function_exists( 'add_ambassador_settings_link' ) ) {
		function add_ambassador_settings_link( $links ) {
			$settings_link = '<a href="admin.php?page=woocommerce&tab=integration&section=ambassador">Settings</a>';
	  		array_unshift( $links, $settings_link );
	  		return $links;
		}

		$plugin = plugin_basename( __FILE__ );
		add_filter( 'plugin_action_links_' . $plugin, 'add_ambassador_settings_link' );
	}

	require_once('ambassador.api.class.php');

	global $mbsy;
	global $mbsy_settings;

	$mbsy = new Ambassador_API();
	$mbsy_settings = get_option( 'woocommerce_ambassador_settings' );

	/**
	 * Add integration to WooCommerce
	 **/
	if(!function_exists('add_ambassador_integration')) {
		function add_ambassador_integration( $integrations ) {
			require_once('ambassador.admin.class.php');
			$integrations[] = 'Ambassador_Admin';
			return $integrations;
		}
		add_filter( 'woocommerce_integrations', 'add_ambassador_integration' );
	}

	/**
	 * Create referring cookie
	 **/
	if(!function_exists('ambassador_create_cookie')) {
		function ambassador_create_cookie() {
			global $mbsy_settings;

			if( ! headers_sent() ) {

				if($mbsy_settings['mbsy_enable'] == 'yes') {

					if ( isset( $_GET['mbsy'] ) && $_GET['mbsy'] ) {

			    		$expire = time() + 60 * 60 * 24 * 30;

			    		setcookie( 'mbsy_shortcode' , $_GET['mbsy'] , $expire , '/' );

			    		if ( isset( $_GET['campaignid'] ) && $_GET['campaignid'] ) {
				    		setcookie( 'mbsy_campaign_id' , $_GET['campaignid'] , $expire , '/' );
				    	}

			    		return true;
			    	}
			    }

			}

	    	return false;

		}
		add_action( 'init', 'ambassador_create_cookie', 10 );
	}

	/**
	 * Record sale event
	 **/
	if(!function_exists('ambassador_record_event')) {
		function ambassador_record_event( $order_id ) {
			global $mbsy, $mbsy_settings, $woocommerce;

			if($mbsy_settings['mbsy_enable'] == 'yes') {

				$order = new WC_Order( $order_id );

				if($mbsy_settings['mbsy_create_affiliate'] == 'yes') {
					$create_affiliate = 1;
				} else {
					$create_affiliate = 0;
				}

				if($order) {

					// Fetch the order total
					$order_total = (int) $order->get_total();

					if( isset($mbsy_settings['mbsy_only_items']) && $mbsy_settings['mbsy_only_items'] == 'yes') {

						// Fetch the shipping costs and tax amount
						if( version_compare( $woocommerce->version, '2.1-beta-1', ">=" ) ) {
							$order_shipping = (int) $order->get_total_shipping();
						} else {
							$order_shipping = (int) $order->get_shipping();
						}
						$order_tax = (int) $order->get_shipping_tax();

						// Subtract shipping and tax from order total
						$order_total = $order_total - $order_shipping - $order_tax;

					}

					$data = array(
						'email' => $order->billing_email,
						'revenue' => $order_total,
						'transaction_uid' => $order->id,
						'ip_address' => $_SERVER['SERVER_ADDR'],
						'uid' => $order->user_id,
						'first_name' => $order->billing_first_name,
						'last_name' => $order->billing_last_name,
						'email_new_ambassador' => 1,
						'deactivate_new_ambassador' => 0,
						'auto_create' => $create_affiliate
					);

					return $mbsy->record_event($data);
				}
			}

			return false;
		}
		add_action( 'woocommerce_thankyou', 'ambassador_record_event' );
	}

	/**
	 * Display AddThis snippet on checkout
	 **/
	if(!function_exists('ambassador_addthis')) {
		function ambassador_addthis( $order_id ) {
			global $mbsy;
			global $mbsy_settings;

			if($mbsy_settings['mbsy_enable'] == 'yes') {

				if($mbsy_settings['mbsy_show_addthis'] == 'yes') {

					$order = new WC_Order( $order_id );

					if($mbsy_settings['mbsy_create_affiliate'] == 'yes') {
						$create_affiliate = 1;
					} else {
						$create_affiliate = 0;
					}

					if($order) {
						$data = array(
							'email' => $order->billing_email,
							'uid' => $order->user_id,
							'first_name' => $order->billing_first_name,
							'last_name' => $order->billing_last_name,
							'message' => 'I am an ambassador',
							'email_new_ambassador' => 1,
							'deactivate_new_ambassador' => 0,
							'auto_create' => $create_affiliate
						);

						$json = $mbsy->addthis($data);
						$res = json_decode($json);

						$share_url = false;
						if($res->response->code == 200) {
							if(isset($res->response->data->ambassador->addthis)) {
				                foreach($res->response->data->ambassador->addthis as $data) {
				                	$share_url = $data->url;
				                }
				            }
						}

						if($share_url) {
							if(strlen($mbsy_settings['mbsy_twitter_name']) > 0) {
								$tweet_name = 'data-via="'.$mbsy_settings['mbsy_twitter_name'].'"';
							} else {
								$tweet_name = '';
							}

							if(strlen($mbsy_settings['mbsy_share_message']) > 0) {
								$share_msg = 'data-text="'.$mbsy_settings['mbsy_share_message'].'"';
							} else {
								$share_msg = '';
							}
							?>
							<div class="fix"></div>
							<div class="wc_ambassador_sharing_links">
								<p>
									Use this link to tell your friends about our store and earn commission from sales: <a href="<?php echo $share_url; ?>"><?php echo $share_url; ?></a><br/>
									You can simply share the link from here:
								</p>
								<div class="wc_ambassador_fbshare">
									<a name="fb_share" type="button" share_url="<?php echo $share_url; ?>"></a>
									<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
								</div>
								<div class="wc_ambassador_tweet">
									<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $share_url; ?>" <?php echo $share_msg; ?> <?php echo $tweet_name; ?> data-count="none">Tweet</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>
							</div>
							<?php
						}

						return true;
					}
				}
			}

			return false;
		}
		add_action( 'woocommerce_thankyou', 'ambassador_addthis' );
	}

}