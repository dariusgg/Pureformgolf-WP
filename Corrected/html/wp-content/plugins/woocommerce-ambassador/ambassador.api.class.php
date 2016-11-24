<?php
/**
 * Ambassador API class
 *
 * Uses the Ambassador API to record and retrieve commission data
 *
 * @version 1.0.0
 * @category Plugins
 * @package WordPress
 * @subpackage WooFramework
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - private $settings
 * - private $api_username
 * - private $api_key
 * - private $response_type;
 * - private $sandbox;
 * - private $default_campaign_uid;
 *
 * - Constructor()
 * - api_call()
 * - record_event()
 * - get_ambassador()
 * - get_ambassador_stats()
 * - get_all_ambassadors()
 * - deduct_balance()
 * - add_balance()
 * - get_shortcode_data()
 * - addthis()
 * - get_company_details()
 * - get_company_stats()
 */

if (!class_exists('Ambassador_API')) {

	class Ambassador_API {

		//API username and key are specified in plugin options
		private $settings = array();
		private $api_username = 'none';
		private $api_key = 'none';
		private $response_type;
		private $sandbox;
		private $default_campaign_uid;
		
		public function __construct() {

			//Get user settings
			$this->settings = get_option( 'woocommerce_ambassador_settings' );

			if(count($this->settings) > 0 && $this->settings['mbsy_enable'] == 'yes') {
		        $this->api_username = $this->settings['mbsy_apiusername'];
				$this->api_key = $this->settings['mbsy_apikey'];
				if($this->settings['mbsy_sandbox'] == 'yes') {
					$this->sandbox = 1;
				} else {
					$this->sandbox = 0;
				}
				$this->default_campaign_uid = $this->settings['mbsy_defaultcampaignid'];
				$this->response_type = 'json';
			} else {
				return false;
			}
	    }

		/**
		 * Ambassador API call
		 * 
		 * @access private
		 * @param string $module
		 * @param string $method
		 * @param array $data
		 * @return string $response
		 */
		protected function api_call( $module = 'event' , $method = 'record', $data = false ) {

			if($this->settings['mbsy_enable'] == 'yes') {

			    $data['sandbox'] = $this->sandbox;

			    $postdata = http_build_query($data);

			    $url = 'https://getambassador.com/api/v2/' . $this->api_username . '/' . $this->api_key . '/' . $this->response_type . '/' . $module . '/' . $method . '/';

			    $curl_handle = curl_init();
			    curl_setopt($curl_handle, CURLOPT_URL, $url);
			    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
			    curl_setopt($curl_handle, CURLOPT_POST, true);
			    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postdata);   
			    curl_setopt($curl_handle, CURLOPT_FAILONERROR, false); 
			    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);   
			    $response = curl_exec($curl_handle);
			    curl_close($curl_handle);   
			    
			    return $response;
			}

			return false;
		}

		/**
		 * Records an instance of a campaign referral event
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function record_event( $data = array() ) {
			if ( count( $data ) > 0 ) {
				//Check for shortcode cookie and retrieve data
				if ( isset( $_COOKIE['mbsy_shortcode'] ) && $_COOKIE['mbsy_shortcode'] ) {
					$data['short_code'] = $_COOKIE['mbsy_shortcode'];

					$json = $this->get_shortcode_data( array( 'short_code' => $data['short_code'], 'sandbox' => $this->sandbox ) );
					$res = json_decode( $json );

			        if ( isset( $res->response ) && $res->response->code == 200 ) {
						if ( isset( $res->response->data->shortcode ) ) {
			                foreach ( $res->response->data->shortcode as $k => $v ) {
			                	$shortcode_data[ $k ] = $v;
			                }

			                if ( $shortcode_data['campaign_uid'] ) {
								$data['campaign_uid'] = $shortcode_data['campaign_uid'];
							} else {
								if($_COOKIE['mbsy_campaign_id']) {
									$data['campaign_uid'] = $_COOKIE['mbsy_campaign_id'];
								} else {
									$data['campaign_uid'] = $this->default_campaign_uid;
								}
							}

							if ( $data['campaign_uid'] && $data['email'] ) {
								return $this->api_call( 'event' , 'record' , $data );
							}
			            }
					}
				}
			}

			return false;
		}

		/**
		 * Retrieves details about a given ambassador including their active share links
		 * Automatically creates the requested ambassador if they do not exist yet
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function get_ambassador( $data = array() ) {

			if( count($data) > 0 ) {

				if($data['email']) {
					return $this->api_call( 'ambassador', 'get' , $data);
				}

			}

			return false;
		}

		/**
		 * Retrieves statistics about a given ambassador including a summary and per-campaign report of their earned commissions, generated revenue,
		 * shares, share clicks and unique referrals as well as shares, share clicks, and unique referrals per social channel.
		 * Automatically creates the requested ambassador if they do not exist yet.
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function get_ambassador_stats( $data = array() ) {

			if( count($data) > 0 ) {

				if($data['email']) {
					return $this->api_call( 'ambassador', 'stats' , $data);
				}

			}

			return false;
		}

		/**
		 * Retrieves a list of up to 100 ambassadors meeting the provided thresholds
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function get_all_ambassadors( $data = array() ) {

			if( count($data) > 0 ) {

				return $this->api_call( 'ambassador', 'all' , $data);

			}

			return false;
		}

		/**
		 * Deducts from the running commission balance of an ambassador
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function deduct_balance( $data = array() ) {

			if( count($data) > 0 ) {

				if($data['email'] && $data['amount']) {
					return $this->api_call( 'balance', 'deduct' , $data);
				}

			}

			return false;
		}

		/**
		 * Adds to the running commission balance of an ambassador
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function add_balance( $data = array() ) {

			if( count($data) > 0 ) {

				if($data['email'] && $data['amount']) {
					return $this->api_call( 'balance', 'add' , $data);
				}

			}

			return false;
		}

		/**
		 * Retrieves the referring ambassador and campaign information tied to an mbsy shortcode
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function get_shortcode_data( $data = array() ) {

			if( count($data) > 0 ) {

				return $this->api_call( 'shortcode', 'get' , $data);

			}

			return false;
		}

		/**
		 * Retrieves an AddThis snippet for all active campaigns for one of your customers
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function addthis( $data = array() ) {

			if( count($data) > 0 ) {

				return $this->api_call( 'social', 'addthis' , $data);

			}

			return false;
		}

		/**
		 * Retrieves details about your company and your active campaigns
		 * 
		 * @access public
		 * @param array $data
		 * @return mixed
		 */
		public function get_company_details() {

			return $this->api_call( 'company', 'get' , false);

		}

		/**
		 * Retrieves high-level statistics about your company's referral program
		 * 
		 * @access public
		 * @return mixed
		 */
		public function get_company_stats() {
			
			return $this->api_call( 'company', 'stats' , false);

		}
	}
}












?>