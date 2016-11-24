<?php

/*
  Plugin Name: SocialFans Counter
  Plugin URI: http://labs.themeinity.com/plugins/socialfans-counter
  Description: Responsive Wordpress Plugin Displaying Number of Your Social Accounts Fans, Subscribes and Followers.
  Version: 4.2.1
  Plugin: socialfans-counter/socialfans-counter.php
  Author: Themeinity
  Author URI: http://codecanyon.net/user/themeinity
  Text Domain: sfcounter
 */


// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) )
  exit;

// Sets the plugin path and info
define( 'SOCIALFANS_COUNTER_PATH' , plugin_dir_path( __FILE__ ) );
define( 'SOCIALFANS_COUNTER_URL' , plugins_url( '' , __FILE__) . '/' );
define( 'SOCIALFANS_COUNTER_TITLE' , 'SocialFans Counter' );
define( 'SOCIALFANS_COUNTER_VERSION' , '4.2.1' );
define( 'SOCIALFANS_COUNTER_DOCS_URL' , 'http://labs.themeinity.com/plugins/docs/socialfans-counter' );
define( 'SOCIALFANS_COUNTER_SUPPORT_URL' , 'http://labs.themeinity.com/plugins/support/socialfans-counter' );
define( 'SOCIALFANS_COUNTER_CHECK_UPDATES_URL' , 'http://labs.themeinity.com/plugins/updates/socialfans-counter-update.json' );
define( 'SOCIALFANS_COUNTER_UPDATES_URL' , 'http://labs.themeinity.com/plugins/socialfans-counter/' );


// include plugin files
require_once SOCIALFANS_COUNTER_PATH . 'includes/socialfans-counter-auto-update-class.php';
require_once SOCIALFANS_COUNTER_PATH . 'includes/socialfans-counter-plugin-class.php';
require_once SOCIALFANS_COUNTER_PATH . 'includes/socialfans-counter-panel-class.php';
require_once SOCIALFANS_COUNTER_PATH . 'includes/socialfans-counter-functions.php';

// include widget and counter class
require_once SOCIALFANS_COUNTER_PATH . 'includes/socialfans-counter-class.php';
require_once SOCIALFANS_COUNTER_PATH . 'widgets/socialfans-counter-widget.php';

// execute plugin
new SocialFans_Counter_Plugin();

