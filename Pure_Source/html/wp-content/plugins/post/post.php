<?php
/*
  Plugin Name: Po.st
  Plugin URI: http://www.po.st/
  Description: Po.st makes your site social by letting your users share posts and pages with others. Po.st supports several social networks, email and languages. Check the README file for configuration options and our support site at <a href="http://support.po.st/">http://support.po.st/</a> for other inquiries.
  Author: Po.st
  Version: 1.4.3
  Author URI: http://www.po.st/
 */

include_once( 'post-services.php' );

load_plugin_textdomain( 'po.st' );

add_filter( 'the_content', 'post_add_widget_content' );
add_action( 'wp_head', 'post_add_js_init' );
add_action( 'admin_menu', 'post_menu_items' );
add_action( 'wp_ajax_post_ajax_preview', 'post_ajax_preview' );
add_action( 'init', 'post_options_form_save', 9999 );
add_action( 'admin_notices', 'post_warning' );
add_action( 'admin_init', 'post_add_meta_box' );
add_action( 'save_post', 'post_meta_box_save' );
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );

$showOn = array(
	'list'  => __( 'Lists of posts', 'po.st' ),
	'posts' => __( 'Single posts', 'po.st' ),
	'pages' => __( 'Pages', 'po.st' ),
);

function enqueue_styles() {
	wp_register_style( 'post-plugin', plugins_url( '/post-plugin.css', __FILE__ ), array(), '1', 'all' );
	wp_enqueue_style( 'post-plugin' );
}

function post_menu_items() {
	if ( ak_can_update_options() ) {
		$page = add_options_page( __( 'Po.st Options', 'po.st' ), __( 'Po.st', 'po.st' ), 'manage_options', basename( __FILE__ ), 'post_options_form' );
		add_action( 'admin_print_scripts-' . $page, 'post_admin_scripts' );
		add_action( 'admin_print_styles-' . $page, 'post_admin_styles' );
	}
}

function post_admin_scripts() {
	wp_enqueue_script( 'thickbox', null, array( 'jquery' ) );
	wp_register_script( 'post-sortable-script', plugins_url( '/js/jquery-sortable.js', __FILE__ ) );
	wp_enqueue_script( 'post-sortable-script' );
	wp_register_script( 'post-constructor-script', plugins_url( '/js/post-constructor.js', __FILE__ ) );
	wp_enqueue_script( 'post-constructor-script' );
}

function post_admin_styles() {
	wp_enqueue_style( 'thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css' );
}

function post_warning() {
	$p_key = trim( get_option( 'post_p_key', '' ) );
	if ( empty( $p_key ) ) {
		echo "<div class='error fade' id='pubkeyerror'><p>" . sprintf( __( 'You must <a href="%1$s">enter your Po.st publisher\'s Key</a> for it to work.' ), get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=post.php' ) . "</p></div>";
	}
}

function post_add_meta_box() {
	add_meta_box( 'post_pinterest_meta', '"Po.st" Pinterest Settings', 'post_pinterest_meta_box_content', 'page', 'advanced', 'high' );
	add_meta_box( 'post_pinterest_meta', '"Po.st" Pinterest Settings', 'post_pinterest_meta_box_content', 'post', 'advanced', 'high' );
}

function post_pinterest_meta_box_content( $post ) {
	$pinterestImgUrl = get_post_meta( $post->ID, 'pinterest_url', true );

	include dirname( __FILE__ ) . '/tpl/pinterest_meta.tpl.php';
}

function post_meta_box_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( isset( $_POST[ 'post_type' ] ) && ( 'post' == $_POST[ 'post_type' ] || 'page' == $_POST[ 'post_type' ] ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST[ 'pinterest_url' ] ) ) {
				update_post_meta( $post_id, 'pinterest_url', $_POST[ 'pinterest_url' ] );
			} else {
				delete_post_meta( $post_id, 'pinterest_url' );
			}
		}
	}

	return $post_id;
}

function post_options_form_save() {
	$plugin_location = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );

	if ( $_POST && isset( $_POST[ 'post_action' ] ) ) {

		$options = get_data_from_post();
		@session_start();

		if ( check_admin_referer( 'update-po.st-settings' ) ) {
			if ( $_POST[ 'post_action' ] == 'save' ) {
				update_option( 'post_p_key', trim( $options[ 'post_p_key' ] ) );
				update_option( 'post_display_pages', $options[ 'post_display_pages' ] );
				update_option( 'post_display_position_horizontal', $options[ 'post_display_position_horizontal' ] );
				update_option( 'post_display_position_vertical', $options[ 'post_display_position_vertical' ] );
				update_option( 'post_design_type', $options[ 'post_design_type' ] );
				update_option( 'post_design_custom_code_on', $options[ 'post_design_custom_code_on' ] );
				update_option( 'post_design_orientation', $options[ 'post_design_orientation' ] );
				update_option( 'post_design_icons', $options[ 'post_design_icons' ] );
				update_option( 'post_design_buttons', $options[ 'post_design_buttons' ] );
				update_option( 'post_design_custom_code', $options[ 'post_design_custom_code' ] );
				update_option( 'post_display_custom_position_horizontal', $options[ 'post_display_custom_position_horizontal' ] );
				update_option( 'post_design_totaltype', $options[ 'post_design_totaltype' ] );

				wp_redirect( get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=post.php&updated=true' );
				exit;
			}
		}
	}
}

function post_options_form() {
	@session_start();

	$plugin_location = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );

	$p_key                       = get_option( 'post_p_key', '' );
	$display_pages               = get_option( 'post_display_pages', 'list,posts' );
	$display_position_horizontal = get_option( 'post_display_position_horizontal', 'above' );
	$display_position_vertical   = get_option( 'post_display_position_vertical', 'left' );
	$design_type                 = get_option( 'post_design_type', 'icons-small' );
	$design_custom_code_on       = get_option( 'post_design_custom_code_on', '0' );
	$design_totaltype            = get_option( 'post_design_totaltype', 'icons' );
	$design_orientation          = get_option( 'post_design_orientation', 'horizontal' );
	$design_icons                = get_option( 'post_design_icons', 'facebook:0,twitter:0,email:0,stumbleupon:0,post:0' );
	$design_buttons              = get_option( 'post_design_buttons', 'facebook:0,twitter:0,linkedin:0,googleplus:0,post:0' );
	$default_design_custom_code  = '<div class="pw-widget pw-size-small" pw:url="[PAGEURL]" pw:title="[PAGETITLE]">
	<a class="pw-button-facebook"></a>
	<a class="pw-button-twitter"></a>
	<a class="pw-button-email"></a>
	<a class="pw-button-stumbleupon"></a>
	<a class="pw-button-post"></a>
</div>';

	$display_custom_position_horizontal = get_option( 'post_display_custom_position_horizontal', 'above' );
	$design_custom_code                 = get_option( 'post_design_custom_code', $default_design_custom_code );

	$design_icons = explode( ',', $design_icons );
	$temp         = array();
	foreach ( $design_icons as $servI ) {
		list( $serv, $counter ) = explode( ':', $servI );
		$temp[ $serv ] = $counter;
	}
	$design_icons = $temp;

	$design_buttons = explode( ',', $design_buttons );
	$temp           = array();
	foreach ( $design_buttons as $servI ) {
		list( $serv, $counter ) = explode( ':', $servI );
		$temp[ $serv ] = $counter;
	}
	ksort( $temp );
	$design_buttons = $temp;

	if ( empty( $display_pages ) ) {
		$display_pages = array();
	} else {
		$display_pages = explode( ',', $display_pages );
	}

	$current_type = explode( '-', $design_type );
	$current_type = $current_type[ 0 ];

	$_SESSION[ '_token' ] = $_token = base64_encode( openssl_random_pseudo_bytes( 32 ) );

	include dirname( __FILE__ ) . '/tpl/form.tpl.php';
}

function post_add_js_init() {
	$p_key   = get_option( 'post_p_key', '' );
	$options = null;
	if ( isset( $_GET[ 'preview' ] ) && $_GET[ 'preview' ] ) {
		$options = get_transient( 'post_settings' );
		if ( $options ) {
			$p_key = $options[ 'post_p_key' ];
		}
	}
	print "<script type=\"text/javascript\">
(function ()
{
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = ('https:' == document.location.protocol ? 'https://s' : 'http://i') + '.po.st/share/script/post-widget.js#publisherKey={$p_key}&retina=true';
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
})();
</script>";
}

function post_add_widget_content( $content ) {
	static $verticalAdded;

	$options = null;
	if ( isset( $_GET[ 'preview' ] ) && $_GET[ 'preview' ] ) {
		$options = get_transient( 'post_settings' );
	}
	if ( empty( $options ) ) {
		$display_pages                      = get_option( 'post_display_pages', 'pages,posts' );
		$display_position_horizontal        = get_option( 'post_display_position_horizontal', 'above' );
		$display_position_vertical          = get_option( 'post_display_position_vertical', 'left' );
		$design_orientation                 = get_option( 'post_design_orientation', 'horizontal' );
		$design_custom_code_on              = get_option( 'post_design_custom_code_on', '0' );
		$display_custom_position_horizontal = get_option( 'post_display_custom_position_horizontal', 'above' );
	} else {
		$display_pages                      = $options[ 'post_display_pages' ];
		$display_position_horizontal        = $options[ 'post_display_position_horizontal' ];
		$display_position_vertical          = $options[ 'post_display_position_vertical' ];
		$design_orientation                 = $options[ 'post_design_orientation' ];
		$design_custom_code_on              = $options[ 'post_design_custom_code_on' ];
		$display_custom_position_horizontal = $options[ 'post_display_custom_position_horizontal' ];
	}

	$add_widget = false;
	foreach ( explode( ',', $display_pages ) as $page ) {
		switch ( $page ) {
			case 'list':
				if ( ! is_singular() ) {
					$add_widget = true;
				}
				break;
			case 'pages':
				if ( is_page() && is_singular() ) {
					$add_widget = true;
				}
				break;
			case 'posts':
				if ( is_singular() && get_post_type() == 'post' ) {
					$add_widget = true;
				}
				break;
		}
	}

	if ( $add_widget ) {
		if ( $design_custom_code_on ) {
			if ( count( $display_custom_position_horizontal ) > 1 ) {
				$content = post_make_widget( get_permalink(), get_the_title(), $options ) . $content . post_make_widget( get_permalink(), get_the_title(), $options );
			} else if ( $display_custom_position_horizontal[ 0 ] == 'above' ) {
				$content = post_make_widget( get_permalink(), get_the_title(), $options ) . $content;
			} else if ( $display_custom_position_horizontal[ 0 ] == 'below' ) {
				$content .= post_make_widget( get_permalink(), get_the_title(), $options );
			}
		} else {

			if ( $design_orientation == 'horizontal' ) {

				if ( count( $display_position_horizontal ) > 1 ) {

					$content = post_make_widget( get_permalink(), get_the_title(), $options ) . $content . post_make_widget( get_permalink(), get_the_title(), $options );
				} else if ( $display_position_horizontal[ 0 ] == 'above' ) {

					$content = post_make_widget( get_permalink(), get_the_title(), $options ) . $content;
				} else if ( $display_position_horizontal[ 0 ] == 'below' ) {

					$content .= post_make_widget( get_permalink(), get_the_title(), $options );
				}
			} else {
				if ( ! isset( $verticalAdded ) ) {
					$verticalAdded = true;
					add_action( 'wp_footer', 'post_add_float_widget' );
				}
			}
		}
	}

	return $content;
}

function post_add_float_widget() {
	$options = null;
	if ( isset( $_GET[ 'preview' ] ) && $_GET[ 'preview' ] ) {
		$options = get_transient( 'post_settings' );
	}
	echo post_make_widget( '', '', $options );
}

function post_make_widget( $url = '', $title = '', $options = null ) {

	global $displayTypes, $avServices, $orientationType, $positionType, $post;

	$meta            = get_post_meta( $post->ID, 'pinterest_url' );
	$pinterest_url   = array_shift( $meta );
	$plugin_location = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );

	if ( empty( $options ) ) {
		$p_key                       = get_option( 'post_p_key' );
		$design_type                 = get_option( 'post_design_type', 'icon-small' );
		$design_orientation          = get_option( 'post_design_orientation', 'horizontal' );
		$display_position_horizontal = get_option( 'post_display_position_horizontal', 'above' );
		$display_position_vertical   = get_option( 'post_display_position_vertical', 'left' );
		$design_icons                = get_option( 'post_design_icons', 'googleplus:0,facebook:0,twitter:0,post:0' );
		$design_buttons              = get_option( 'post_design_buttons', 'googleplus:0,facebook:0,twitter:0,post:0' );
		$design_custom_code          = get_option( 'post_design_custom_code' );
		$design_custom_code_on       = get_option( 'post_design_custom_code_on' );
		$design_totaltype            = get_option( 'post_design_totaltype' );
	} else {
		$p_key                       = $options[ 'post_p_key' ];
		$design_type                 = $options[ 'post_design_type' ];
		$design_orientation          = $options[ 'post_design_orientation' ];
		$display_position_horizontal = $options[ 'post_display_position_horizontal' ];
		$display_position_vertical   = $options[ 'post_display_position_vertical' ];
		$design_icons                = $options[ 'post_design_icons' ];
		$design_buttons              = $options[ 'post_design_buttons' ];
		$design_custom_code          = $options[ 'post_design_custom_code' ];
		$design_custom_code_on       = $options[ 'post_design_custom_code_on' ];
		$design_totaltype            = $options[ 'post_design_totaltype' ];
	}

	$out   = "";
	$extra = '';
	if ( $url ) {
		$extra .= "pw:url=\"{$url}\" ";
	}
	if ( $title ) {
		$extra .= "pw:title=\"{$title}\" ";
	}
	if ( $design_custom_code_on ) {
		$out = stripslashes( $design_custom_code );
	} else {
		$services        = array();
		$postCounter     = '';
		$current_type    = explode( '-', $design_type );
		$current_type    = $current_type[ 0 ];
		$design_services = ${'design_' . $current_type};

		foreach ( explode( ',', $design_services ) as $servI ) {
			list( $serv, $counter ) = explode( ':', $servI );
			$services[ $serv ] = array(
				'counter' => $counter
			);
			if ( $serv == 'pinterest' && ! is_singular() && $design_orientation == 'vertical' ) {
				continue;
			}
			if ( isset( $avServices[ $serv ] ) && isset( $avServices[ $serv ][ 'extra' ] ) ) {
				if ( isset( $avServices[ $serv ][ 'extra' ][ 'global' ] ) ) {
					$extra .= implode( ' ', $avServices[ $serv ][ 'extra' ][ 'global' ] ) . ' ';
				}
				if ( isset( $avServices[ $serv ][ 'extra' ][ 'local' ] ) ) {
					$services[ $serv ][ 'extra' ] = implode( ' ', $avServices[ $serv ][ 'extra' ][ 'local' ] );
				}
			}
		}

		if ( $current_type == 'icons' ) {
			if ( isset( $services[ 'post' ] ) && ( $services[ 'post' ][ 'counter' ] == 1 ) ) {
				$postCounter = ' pw-counter-show ';
			}
		}
		if ( $design_orientation == 'vertical' ) {
			$positionClass = $positionType[ $design_orientation ][ ${"display_position_$design_orientation"} ][ 'class' ];
		} else {
			$positionClass = $positionType[ $design_orientation ][ ${"display_position_$design_orientation"}[ 0 ] ][ 'class' ];
		}

		if ( $design_orientation == 'vertical' ) {
			$out .= "<div class='{$positionClass}' style='position:fixed; margin-top:-9999px'>";
		}

		$out .= "<div pw:image='[IMAGEURL]' class='pw-widget " . $postCounter . $displayTypes[ $design_type ][ 'class' ] . ' ' . $orientationType[ $design_orientation ][ 'class' ] . "' {$extra}>\n";
		$type = $displayTypes[ $design_type ][ 'type' ];
		foreach ( $services as $serv => $data ) {
			$counterStr = '';
			if ( $data[ 'counter' ] ) {
				$counterStr = ' pw-counter ';
			}
			if ( isset( $avServices[ $serv ][ $type ] ) ) {
				$out .= "\t<a class='" . $avServices[ $serv ][ $type ][ 'class' ] . $counterStr . "' " . $data[ 'extra' ] . "></a>\n";
			}
		}
		$out .= "</div>";
		if ( $design_orientation == 'vertical' ) {
			$out .= "</div>";
			$out .= "<script type=\"text/javascript\" src='" . $plugin_location . "js/post.vert.js'></script>";
		}
	}
	$out = str_replace( '[IMAGEURL]', $pinterest_url, $out );
	$out = str_replace( '[PAGEURL]', $url, $out );
	$out = str_replace( '[PAGETITLE]', $title, $out );

	return $out;
}

function post_ajax_preview() {

	if ( $_POST && isset( $_POST[ 'post_action' ] ) && $_POST[ 'post_action' ] == 'preview' ) {

		$options = get_data_from_post();

		if ( false !== get_transient( 'post_settings' ) ) {
			delete_transient( 'post_settings' );
		}
		$eh = set_transient( 'post_settings', $options, 120 );

		die();
	}
}

function get_data_from_post() {
	global $displayTypes, $orientationType;

	$options                  = array();
	$options[ 'post_p_key' ]  = isset( $_POST[ 'p_key' ] ) ? $_POST[ 'p_key' ] : '';

	$options[ 'post_display_pages' ] = isset( $_POST[ 'show_on' ] ) ? $_POST[ 'show_on' ] : array();
	$options[ 'post_display_pages' ] = implode( ',', array_keys( $options[ 'post_display_pages' ] ) );

	$options[ 'post_display_position_horizontal' ] = isset( $_POST[ 'display_position_horizontal' ] ) ? $_POST[ 'display_position_horizontal' ] : 'above';
	$options[ 'post_display_position_vertical' ]   = isset( $_POST[ 'display_position_vertical' ] ) ? $_POST[ 'display_position_vertical' ] : 'left';

	$options[ 'post_design_type' ] = isset( $_POST[ 'design_type' ] ) ? $_POST[ 'design_type' ] : 'icon-small';
	if ( ! isset( $displayTypes[ $options[ 'post_design_type' ] ] ) ) {
		$options[ 'post_design_type' ] = 'icon-small';
	}

	$options[ 'post_design_orientation' ] = isset( $_POST[ 'design_orientation' ] ) ? $_POST[ 'design_orientation' ] : 'horizontal';
	if ( ! isset( $orientationType[ $options[ 'post_design_orientation' ] ] ) ) {
		$options[ 'post_design_orientation' ] = 'horizontal';
	}

	$post_design_icons = isset( $_POST[ 'icons' ] ) ? $_POST[ 'icons' ] : array();
	$temp              = array();
	foreach ( $post_design_icons as $serv => $counter ) {
		$temp[ ] = $serv . ':' . $counter;
	}
	$options[ 'post_design_icons' ] = implode( ',', $temp );

	$post_design_buttons = isset( $_POST[ 'buttons' ] ) ? $_POST[ 'buttons' ] : array();
	$temp                = array();
	foreach ( $post_design_buttons as $serv => $counter ) {
		$temp[ ] = $serv . ':' . $counter;
	}

	$options[ 'post_design_buttons' ] = implode( ',', $temp );

	$options[ 'post_design_custom_code' ]                 = isset( $_POST[ 'design_custom_code' ] ) ? $_POST[ 'design_custom_code' ] : '';
	$options[ 'post_display_custom_position_horizontal' ] = isset( $_POST[ 'display_custom_position_horizontal' ] ) ? $_POST[ 'display_custom_position_horizontal' ] : 'above';
	$options[ 'post_design_custom_code_on' ]              = isset( $_POST[ 'design_custom_code_on' ] ) ? $_POST[ 'design_custom_code_on' ] : 0;
	$options[ 'post_design_totaltype' ]                   = isset( $_POST[ 'design_totaltype' ] ) ? $_POST[ 'design_totaltype' ] : 'icons';

	return $options;
}

if ( ! function_exists( 'ak_can_update_options' ) ) {
	function ak_can_update_options() {
		if ( function_exists( 'current_user_can' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}
		} else {
			global $user_level;
			get_currentuserinfo();
			if ( $user_level >= 8 ) {
				return true;
			}
		}

		return false;
	}
}
