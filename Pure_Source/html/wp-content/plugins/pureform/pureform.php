<?php 
/*
Plugin Name: Pure form golf plugin
Plugin URI: http://www.2swebsolutions.com/
Description: Custom Pure form.
Author: 2S Web Solutions Support
Version: 1.0.1
Author URI: http://www.2swebsolutions.com/
*/


 //Activation function
// [pureform foo="foo-value"]



function pureform_func( $atts ) {
	 ob_start();
     include('with_subscription.php');
     $output = ob_get_clean();
     ob_start();
     return $output;
	 

}
add_shortcode( 'pureform', 'pureform_func' );








function pureform_func_ws( $atts ) {
	 ob_start();
     include('without_subscription.php');
     $output = ob_get_clean();
     ob_start();
     return $output;
	 

}
add_shortcode( 'pureform_ws', 'pureform_func_ws' );
 ?>