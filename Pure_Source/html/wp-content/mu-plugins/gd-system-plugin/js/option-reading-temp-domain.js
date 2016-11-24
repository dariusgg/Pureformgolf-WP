/* global jQuery, gd_system_option_reading_temp_domain_vars */

jQuery( document ).ready( function( $ ) {

	$( '#blog_public').prop( 'disabled', true );

	var $notice = $( '<div class="wppaas-inline-notice"></div>' );

	$notice.html( gd_system_option_reading_temp_domain_vars.blog_public_notice_text );

	$( '.option-site-visibility p.description' )
		.after( $notice )
		.after( '<div class="clear"></div>' )
		.hide();

} );
