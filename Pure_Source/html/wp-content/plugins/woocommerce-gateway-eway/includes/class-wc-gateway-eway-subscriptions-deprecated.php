<?php
/**
 * WC_Gateway_EWAY_Subscriptions_Deprecated class.
 *
 * @extends WC_Gateway_EWAY
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Gateway_EWAY_Subscriptions_Deprecated' ) ) {
	class WC_Gateway_EWAY_Subscriptions_Deprecated extends WC_Gateway_EWAY {

		function __construct() {

			parent::__construct();

			add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 3 );
			add_action( 'woocommerce_subscriptions_changed_failing_payment_method_' . $this->id, array( $this, 'change_failing_payment_method' ), 10, 2 );

			// display the current payment method used for a subscription in the "My Subscriptions" table
			add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'maybe_render_subscription_payment_method' ), 10, 3 );
		}

		/**
		 * Update the customer_id for a subscription after using eWAY to complete a payment to make up for
		 * an automatic renewal payment which previously failed.
		 *
		 * @param WC_Order $original_order The original order in which the subscription was purchased.
		 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
		 */
		public function change_failing_payment_method( $original_order, $renewal_order ) {

			$new_customer_id = get_post_meta( $renewal_order->id, '_eway_token_customer_id', true );

			update_post_meta( $original_order->id, '_eway_token_customer_id', $new_customer_id );

		}

		/**
		 * scheduled_subscription_payment function.
		 *
		 * @param $amount_to_charge float The amount to charge.
		 * @param $order WC_Order The WC_Order object of the order which the subscription was purchased in.
		 * @param $product_id int The ID of the subscription product for which this payment relates.
		 * @access public
		 * @return void
		 */
		function scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {

			$result = $this->process_subscription_payment( $order, $amount_to_charge );

			if ( is_wp_error( $result ) ) {
				$order->add_order_note( sprintf( __( 'eWAY subscription renewal failed - %s', 'wc-eway' ), $this->response_message_lookup( $result->get_error_message() ) ) );
				WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
			} else {
				WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );
			}

		}

		/**
		 * process_subscription_payment function.
		 *
		 * @access public
		 * @param mixed $order
		 * @param int $amount (default: 0)
		 * @return void
		 */
		function process_subscription_payment( $order = '', $amount = 0 ) {
			$eway_token_customer_id = get_post_meta( $order->id, '_eway_token_customer_id', true );

			if ( ! $eway_token_customer_id )
				return new WP_Error( 'eway_error', __( 'Token Customer ID not found', 'wc-eway' ) );

			// Charge the customer
			try {
				$amount_in_cents = $amount * 100;
				$result = json_decode( $this->get_api()->direct_payment( $order, $eway_token_customer_id, $amount_in_cents ) );
				switch ( $result->ResponseMessage ) {
					case 'A2000' :
					case 'A2008' :
					case 'A2010' :
					case 'A2011' :
					case 'A2016' :
						return true;
					break;
					default:
						if ( isset( $result->Errors ) && ! is_null( $result->Errors ) ) {
							return new WP_Error( 'eway_error', $this->response_message_lookup( $result->Errors ) );
						} else {
							return new WP_Error( 'eway_error', $this->response_message_lookup( $result->ResponseMessage ) );
						}
					break;
				}
			} catch ( Exception $e ) {
				return new WP_Error( 'eway_error', $e->getMessage() );
			}
		}

		/**
		 * Render the payment method used for a subscription in the "My Subscriptions" table
		 *
		 * @param string $payment_method_to_display the default payment method text to display
		 * @param array $subscription_details the subscription details
		 * @param WC_Order $order the order containing the subscription
		 * @return string the subscription payment method
		 */
		public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription_details, WC_Order $order ) {

			// bail for other payment methods
			if ( $this->id !== $order->recurring_payment_method || ! $order->customer_user )
				return $payment_method_to_display;

			$order_token_id = get_post_meta( $order->id, '_eway_token_customer_id', true );
			$eway_cards = get_user_meta( $order->customer_user, '_eway_token_cards', true );

			if ( $eway_cards && ! empty( $eway_cards ) ) {
				foreach ( $eway_cards as $card ) {
					if ( $card['id'] == $order_token_id ) {
						$payment_method_to_display = sprintf( __( 'Via card %s', 'wc-eway' ), $card['number'] );
						break;
					}
				}
			}

			return $payment_method_to_display;
		}

		protected function request_access_code( $order ) {

			// Check if order is for a subscription, if it is check for fee and charge that
			if ( class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order->id ) ) {

				$method = 'TokenPayment';

				if ( 0 == WC_Subscriptions_Order::get_total_initial_payment( $order ) ) {
					$method = 'CreateTokenCustomer';
				}

				$order_total = WC_Subscriptions_Order::get_total_initial_payment( $order ) * 100;

				$result = json_decode( $this->get_api()->request_access_code( $order, $method, 'Recurring', $order_total ) );

				if ( isset( $result->Errors ) && ! is_null( $result->Errors ) ) {
					throw new Exception( $this->response_message_lookup( $result->Errors ) );
				}

				return $result;

			} else {

				return parent::request_access_code( $order );

			}

		}

	}
}
