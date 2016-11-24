<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
  exit();

global $wpdb;

$options_table = $wpdb->prefix . "options";
$wpdb->query( "DELETE FROM  $options_table WHERE option_name LIKE '%sfcounter%'" );
