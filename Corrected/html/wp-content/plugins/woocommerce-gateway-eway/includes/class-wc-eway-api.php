<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_EWAY_API' ) ) {
	class WC_EWAY_API {

		const PRODUCTION_ENDPOINT = 'https://api.ewaypayments.com';

		const TEST_ENDPOINT = 'https://api.sandbox.ewaypayments.com';

		public $endpoint;

		public $api_key;

		public $api_password;

		public $debug_mode;

		public function __construct( $api_key, $api_password, $environment, $debug_mode ) {
			$this->api_key = $api_key;
			$this->api_password = $api_password;
			$this->endpoint = ( 'production' === $environment ) ? WC_EWAY_API::PRODUCTION_ENDPOINT : WC_EWAY_API::TEST_ENDPOINT;
			$this->debug_mode = $debug_mode;
		}

		private function perform_request( $endpoint, $json ) {
			$args = array(
				'timeout'     => apply_filters( 'wc_eway_api_timeout', 45 ), // default to 45 seconds
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'accept'       	=> 'application/json',
					'content-type' 	=> 'application/json',
					'authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->api_password )
				),
				'body'        => $json,
				'cookies'     => array(),
				'user-agent'  => "PHP " . PHP_VERSION . '/WooCommerce ' . get_option( 'woocommerce_db_version' )
			);

			$this->debug_message( json_decode( $json ) );

			$response = wp_remote_post( $this->endpoint . $endpoint, $args );

			$this->debug_message( $response );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( $response['response']['code'] <> 200 ) {
				throw new Exception( $response['response']['message'] );
			}

			$this->debug_message( json_decode( $response['body'] ) );

			return $response['body'];
		}

		private function perform_get_request( $endpoint ) {
			$args = array(
				'timeout'     => apply_filters( 'wc_eway_api_timeout', 45 ), // default to 45 seconds
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->api_password )
				),
				'cookies'     => array(),
				'user-agent'  => "PHP " . PHP_VERSION . '/WooCommerce ' . get_option( 'woocommerce_db_version' )
			);

			$response = wp_remote_get( $this->endpoint . $endpoint, $args );

			$this->debug_message( $response );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( $response['response']['code'] <> 200 ) {
				throw new Exception( $response['response']['message'] );
			}

			$this->debug_message( json_decode( $response['body'] ) );

			return $response['body'];
		}

		/**
		 * Request an access code for use in an eWAY Transparent Redirect payment
		 * See: https://eway.io/api-v3/#transparent-redirect
		 *
		 * @param WC_Order $order
		 * @param string   $method       The "Method" parameter, see: https://eway.io/api-v3/#payment-methods
		 * @param string   $trx_type     The "TransactionType" parameter, see: https://eway.io/api-v3/#transaction-types
		 * @param mixed    $order_total  The amount to charge for this transaction
		 *
		 * @return mixed     JSON response from /CreateAccessCode.json on success
		 * @throws Exception Thrown on failure
		 */
		public function request_access_code( $order, $method = 'ProcessPayment', $trx_type = 'Purchase', $order_total = null ) {
			$customer_ip = get_post_meta( $order->id, '_customer_ip_address', true );

			// If an order total isn't provided (in the case of a subscription), grab it from the Order itself
			if ( is_null( $order_total ) ) {
				$order_total = $order->get_total() * 100.00;
			}

			// set up request object
			$request = array(
				'Method' => $method,
				'TransactionType' => $trx_type,
				'RedirectUrl' => str_replace( 'https:', 'http:', add_query_arg( array( 'wc-api' => 'WC_Gateway_EWAY', 'order_id' => $order->id, 'order_key' => $order->order_key, 'sig_key' => md5( $order->order_key . 'WOO' . $order->id ) ), home_url( '/' ) ) ),
				'IPAddress' => $customer_ip,
				'DeviceID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'Payment' => array(
					'TotalAmount' => $order_total,
					'CurrencyCode' => get_woocommerce_currency(),
					'InvoiceDescription' => apply_filters( 'woocommerce_eway_description', '', $order ),
					'InvoiceNumber' => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference' => $order->id,
				),
				'Customer' => array(
					'FirstName' => $order->billing_first_name,
					'LastName' => $order->billing_last_name,
					'CompanyName' => substr( $order->billing_company, 0, 50 ),
					'Street1' => $order->billing_address_1,
					'Street2' => $order->billing_address_2,
					'City' => $order->billing_city,
					'State' => $order->billing_state,
					'PostalCode' => $order->billing_postcode,
					'Country' => strtolower( $order->billing_country ),
					'Email' => $order->billing_email,
					'Phone' => $order->billing_phone
				),
			);

			// Add customer ID if logged in
			if ( is_user_logged_in() ) {
				$request['Options'][] = array( 'customerID' => get_current_user_id() );
			}
			return $this->perform_request( '/CreateAccessCode.json', json_encode( $request ) );
		}

		public function get_access_code_result( $access_code ) {
			$request = array(
				'AccessCode' => $access_code
			);

			return $this->perform_request( '/GetAccessCodeResult.json', json_encode( $request ) );
		}

		public function direct_payment( $order, $token_customer_id, $amount = 0 ) {
			// Check for 0 value order
			if ( 0 == $amount ) {
				$return_object = array(
					'Payment' => array(
						'InvoiceReference' => $order->id
					),
					'ResponseMessage' => 'A2000',
					'TransactionID' => ''
				);
				return json_encode( $return_object );
			}
			$request = array(
				'DeviceID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'TransactionType' => 'Recurring',
    			'Method' => 'TokenPayment',
				'Customer' => array(
					'TokenCustomerID' => $token_customer_id,
				),
				'Payment' => array(
					'TotalAmount' => $amount,
					'CurrencyCode' => get_woocommerce_currency(),
					'InvoiceDescription' => apply_filters( 'woocommerce_eway_description', '', $order ),
					'InvoiceNumber' => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference' => $order->id,
				),
    			'Options' => array(
					array( 'OrderID' => $order->id ),
					array( 'OrderKey' => $order->order_key ),
					array( 'SigKey' => md5( $order->order_key . 'WOO' . $order->id ) ),
				),
			);
			return $this->perform_request( '/DirectPayment.json', json_encode( $request ) );
		}

		public function direct_refund( $order, $transaction_id, $amount = 0, $reason = '' ) {
			$request = array(
				'DeviceID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'Refund' => array(
					'TotalAmount' => $amount,
					'TransactionID' => $transaction_id,
					'InvoiceNumber' => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference' => $order->id,
					'InvoiceDescription' => $reason,
				),
			);
			return $this->perform_request( '/DirectRefund.json', json_encode( $request ) );
		}

		public function debug_message( $message ) {
			if ( 'on' == $this->debug_mode ) {
				if ( is_array( $message ) || is_object( $message ) ) {
					$message = print_r( $message, true );
				}

				error_log( $message );

				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( $message );
				}
			}
		}

		public function lookup_customer( $token_customer_id ) {
			return $this->perform_get_request( '/Customer/' . $token_customer_id );
		}
	}
}
