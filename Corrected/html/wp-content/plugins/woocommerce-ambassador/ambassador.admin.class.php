<?php
/**
 * Ambassador admin functions
 *
 * Handles Ambassador settings
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
 * - var $plugin_id
 * - var $settings
 * - var $form_fields
 * - var $errors
 * - var $sanitized_fields
 * - var $id
 * - var $method_title
 * - var $method_description
 
 * - Constructor()
 * - init_form_fields()
 */

if (!class_exists('Ambassador_Admin')) {

	class Ambassador_Admin extends WC_Integration {

		var $plugin_id = 'woocommerce_';
		var $settings = array();
		var $form_fields = array();
		var $errors = array();
		var $sanitized_fields = array();
		var $id;
		var $method_title;
		var $method_description;

		public function __construct() {
	        $this->id					= 'ambassador';
	        $this->method_title     	= __( 'Ambassador', 'woocommerce' );
	        $this->method_description	= __( 'Ambassador is a social affiliates program that turns your customers into ambassadors for your product.', 'woocommerce' );
			
			// Load the form fields.
			$this->init_form_fields();
			
			// Load the settings.
			$this->init_settings();

			// Define user set variables
			$this->mbsy_enable		 			= $this->settings['mbsy_enable'];
			$this->mbsy_apiusername 			= $this->settings['mbsy_apiusername'];
			$this->mbsy_apikey 					= $this->settings['mbsy_apikey'];
			$this->mbsy_sandbox 				= $this->settings['mbsy_sandbox'];
			$this->mbsy_defaultcampaignid 		= $this->settings['mbsy_defaultcampaignid'];
			$this->mbsy_create_affiliate 		= $this->settings['mbsy_create_affiliate'];
			$this->mbsy_show_addthis 			= $this->settings['mbsy_show_addthis'];
			$this->mbsy_only_items 				= $this->settings['mbsy_only_items'];
			$this->mbsy_twitter_name 			= $this->settings['mbsy_twitter_name'];
			$this->mbsy_share_message 			= $this->settings['mbsy_share_message'];
			
			// Actions
			add_action( 'woocommerce_update_options_integration_ambassador', array( &$this, 'process_admin_options') );
	    }

	    /**
	     * Initialise Settings Form Fields
	     */
	    function init_form_fields() {
	    
	    	$this->form_fields = array(
	    		'mbsy_enable' => array(  
					'title' 			=> __('Enable/Disable', 'woocommerce'),
					'label' 			=> __('Enable Ambassador integration', 'woocommerce'),
					'type' 				=> 'checkbox',
					'checkboxgroup'		=> 'enable',
					'default' 			=> get_option('woocommerce_mbsy_enable') ? get_option('woocommerce_mbsy_enable') : 'no'  // Backwards compat
				),
				'mbsy_sandbox' => array(  
					'title' 			=> __('Run in sandbox mode (for testing)', 'woocommerce'),
					'label' 			=> __('Enable this to run your Ambassador affiliate program in sandbox mode - this is needed for testing, but must be disabled when going live.', 'woocommerce'),
					'type' 				=> 'checkbox',
					'checkboxgroup'		=> 'sandbox',
					'default' 			=> get_option('woocommerce_mbsy_sandbox') ? get_option('woocommerce_mbsy_sandbox') : 'no'  // Backwards compat
				),
	    		'mbsy_apiusername' => array(  
					'title' 			=> __('Ambasador API Username', 'woocommerce'),
					'description' 		=> __('Log into your Ambassador account to find your API username.', 'woocommerce'),
					'type' 				=> 'text',
			    	'default' 			=> '' // Backwards compat
				),
				'mbsy_apikey' => array(  
					'title' 			=> __('Ambasador API Key', 'woocommerce'),
					'description' 		=> __('Log into your Ambassador account to find your API key.', 'woocommerce'),
					'type' 				=> 'text',
			    	'default' 			=> '' // Backwards compat
				),
				'mbsy_defaultcampaignid' => array(  
					'title' 			=> __('Default campaign ID', 'woocommerce'),
					'description' 		=> __('This will be the default campaign that will be used for commissions (can be overridden by referrer link).', 'woocommerce'),
					'type' 				=> 'text',
			    	'default' 			=> '' // Backwards compat
				),
				'mbsy_create_affiliate' => array(  
					'title' 			=> __('Create new affiliate on checkout', 'woocommerce'),
					'label' 			=> __('Enable this to create a new affiliate account for each of your customers when they complete a sale.', 'woocommerce'),
					'type' 				=> 'checkbox',
					'checkboxgroup'		=> 'createaffiliate',
					'default' 			=> get_option('woocommerce_mbsy_create_affiliate') ? get_option('woocommerce_mbsy_create_affiliate') : 'no'  // Backwards compat
				),
				'mbsy_show_addthis' => array(  
					'title' 			=> __('Display sharing buttons on checkout', 'woocommerce'),
					'label' 			=> __('Enable this to display sharing buttons for existing affiliates (or newly created affiliates if enabled) on checkout.', 'woocommerce'),
					'type' 				=> 'checkbox',
					'checkboxgroup'		=> 'showaddthis',
					'default' 			=> get_option('woocommerce_mbsy_show_addthis') ? get_option('woocommerce_mbsy_show_addthis') : 'no'  // Backwards compat
				),
				'mbsy_only_items' => array(  
					'title' 			=> __('Only give commission based on actual product prices', 'woocommerce'),
					'label' 			=> __('Enabling this will make sure that affiliates will only receive commissions on the price of the actual products - it will remove the shipping and tax totals from the amount sent to Ambassador.', 'woocommerce'),
					'type' 				=> 'checkbox',
					'checkboxgroup'		=> 'onlyitems',
					'default' 			=> get_option('woocommerce_mbsy_only_items') ? get_option('woocommerce_mbsy_only_items') : 'no'  // Backwards compat
				),
				'mbsy_twitter_name' => array(  
					'title' 			=> __('Your Twitter username', 'woocommerce'),
					'description' 			=> __('Enter your Twitter username (without the \'@\') that will be included in the customer\'s tweet.', 'woocommerce'),
					'type' 				=> 'text',
					'default' 			=> ''  // Backwards compat
				),
				'mbsy_share_message' => array(  
					'title' 			=> __('Social sharing message', 'woocommerce'),
					'description' 			=> __('Enter the message that will be included in the customer\'s tweet (can be changed by the customer).', 'woocommerce'),
					'type' 				=> 'text',
					'default' 			=> ''  // Backwards compat
				),
			);
			
	    } // End init_form_fields()	    
	}
}

?>