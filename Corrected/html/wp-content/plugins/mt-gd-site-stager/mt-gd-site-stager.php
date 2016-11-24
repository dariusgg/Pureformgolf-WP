<?php
/*
Plugin Name: Site Stager
Description: Wordpress staging and development made easy. Bundled with the Media Temple Premium Wordpress product to enable developers to stage/sync Wordpress sites.
Author: Media Temple
Version: 0.2
Plugin URI: http://www.mediatemple.net
Author URI: http://www.mediatemple.net
Copyright 2014 (mt) Media Temple, Inc. All Rights Reserved.
*/

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) ) {
    die( 'Forbidden' );
}

global $https_server;
$mt_server = 'ac.mediatemple.net';
$https_port = '443';
$https_server = 'https://' . $mt_server . ':' . $https_port;

require_once('mt-gd-site-stager-functions.inc.php');

/* some plugin defines */
define('WPSC_URL',		plugins_url().'/mt-gd-site-stager/');
define('WPSC_TEMP_URL',		WPSC_URL.'temp/');
define('WPSC_TEMP_DIR',		dirname(__FILE__).'/'.'temp'.'/');
define('WPSC_VERSION',		'0.2');
define('ERROR_DIV',			'<div class="error">');

/* What to do when the plugin is activated? */
register_activation_hook( __FILE__, 'wp_mtss_install' );

/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'wp_mtss_remove' );

function wp_mtss_install() {
    $dom = explode('.',$_SERVER['SERVER_NAME']);
    while ( sizeof($dom) > 2 ) {
        array_shift($dom);
        array_shift($dom);
        array_shift($dom);
        $dom = implode('.', $dom);
        if ( ($dom === 'myftpupload.com') or ($dom === 'hostedresource.net') ) {
            add_action('wp_before_admin_bar_render', 'mtss_staging_notice_menu');
        }
    }
}

function wp_mtss_remove() {
    $option_name = 'staging_sites';
    delete_option($option_name);
}

function wp_mtss_admin_init() {
    wp_register_script( 'mtssjs', WPSC_URL.'mtssjs.js', array('jquery') );
    wp_register_style( 'mtsscss', WPSC_URL.'mtss-admin.css' );
}
add_action( 'admin_init', 'wp_mtss_admin_init' );

function wp_mtss_admin_menu() {
    $mtssmainpage  = add_menu_page('Site Stager', 'Site Stager', 'manage_options', 'mtss', 'wp_main_page', WPSC_URL.'mt-logo-16.png');
    $mtssstagepage = add_submenu_page('mtss', 'Stage', 'Stage', 'manage_options', 'mtss_stage', 'mt_stage_page');
    $mtsssitespage = add_submenu_page('mtss','Sites', 'Sites', 'manage_options', 'mtss_sites', 'mt_sites_page');
    add_action('load-' . $mtssmainpage, 'wp_mtss_admin_scripts');
    add_action('load-' . $mtssstagepage, 'wp_mtss_admin_scripts');
    add_action('load-' . $mtsssitespage, 'wp_mtss_admin_scripts');
}
add_action('admin_menu', 'wp_mtss_admin_menu');

function stage_site() {
    check_ajax_referer( 'site-stager' );
    $step = 'CREATE_STAGE_SITE';
    $parent_domain = $_POST['site'];
    $parent_db = DB_NAME;
    $response = mt_api_stage_site( $parent_domain, $parent_db, array('timeout' => 15) );
    if ( response_ok( $response ) ) {
        if ( !empty( $response['data']->domain ) ) {
            $domain = $response['data']->domain;
            $option_name = 'staging_sites';
            if ( get_option( $option_name ) !== false ) {
                // The option already exists, so we just update it.
                $tmp = get_option( $option_name );
                array_push($tmp, $response['data']);
                update_option( $option_name, $tmp );
            } else {
                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'no';
                if (add_option( $option_name, $response['data'], $deprecated, $autoload ) ) {
                } else {
                    echo '<br><span class="mtss-fatal">FATAL: Wordpress problem adding option ' . $option_name . '. Please contact MT Support.</span><br>';
                }
            }
            echo '<br><span class="mtss-info">INFO: Created Staging Site <a href="http://' . $domain . '/" target="_blank" >http://' . $domain . '</a></span><br>';
        } else {
            echo '<br><span class="mtss-warning">WARNING: No staging domain was returned. Click Sites submenu to see if it was created.</span><br>';
        }
    } elseif ( response_http_error( $response ) ) {
        if ( !empty( $response['data']->message ) ) {
            $msg = sanitize_msg( $response['data']->message ); 
            echo '<br><span class="mtss-error">ERROR: ' . $msg . '</span><br>';
        } else {
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': http error but no status message. Click Sites submenu to see if it was created.</span><br>';
        }
    } elseif ( response_curl_error( $response ) ) {
        $msg = $response['curlerr'];
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': CURL_ERR: ' . $msg . '. Click Sites submenu to see if it was created.</span><br>';
    } else {
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': (unknown error). Please contact MT Support.</span><br>';
    }
    die();
}
add_action( 'wp_ajax_stage', 'stage_site' );

function show_stage_form( $site_type ) {
    $site_name = parse_url( home_url(), PHP_URL_HOST );
    $tag = preg_replace('/\./', '_', $site_name);
    $stage_id = 'stage_' . $tag;
    $nonce_tag = 'site-stager';
    $nonce = wp_create_nonce( $nonce_tag );
?>
<script  type='text/javascript'>
var count = 0;

// When the document loads do everything inside here ...
jQuery(document).ready(function(){
        jQuery('#<?php echo $stage_id; ?>').click(function() { //start function when Stage button is clicked
                jQuery.ajax({
                        type: "post",url: "admin-ajax.php",data: { action: 'stage', site: escape( jQuery( '#site' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
                        beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery("div#loading").fadeIn('fast');
                        },
                        success: function(html){ //so, if data is retrieved, store it in html
                                jQuery("div#loading").fadeOut('slow');
                                jQuery("div#formstatus").html( html ); //show the html inside formstatus div
                        }
                }); //close jQuery.ajax
                return false;
        })
})
</script>
<style type='text/css'>
#loading { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
</style>
<div class="wrap">
<form action='' method='POST' id='helloworld4form'>
<?php
if ($site_type === 'live') {
    $tag = 'Live Site: ';
    $name = 'site';
    $id = $name;
} elseif ($site_type === 'stage') {
    $tag = 'Stage Site: ';
    $name = 'site';
    $id = $name;
}
?><p><b><?php echo $tag; ?></b><input type='text' class='readonly' name='<?php echo $name; ?>' id='<?php echo $id; ?>' value='<?php echo $site_name; ?>' readonly />
<input type='submit' class='button' name='action' id='<?php echo $stage_id; ?>' value='Stage' />
</p>
</form>
<div id='loading'>PROCESSING</div>
<p><div id='formstatus'></div></p>
</div>
<?php
}

function mt_stage_page() {
?>
<div class="mtss-wrapper">
<a href="http://mediatemple.net" target="_blank"><img src="<?php echo WPSC_URL; ?>mt.png" align="left" /></a>
<div class="mtss-title">Media Temple Site Stager</div><br>
<div class="mtss-version">v <?php echo WPSC_VERSION; ?></div><br>
<?php
    show_stage_form('live');
?>
</div>
<?php
}

function show_sites_to_sync() {
    $step = 'LIST_STAGING_SITES';
    $nonce_tag = preg_replace('/\./', '_', $site_name);
    $nonce_tag = 'site-stager';
    $nonce = wp_create_nonce( $nonce_tag );
    $option_name = 'staging_sites';
    $domain_name = parse_url( home_url(), PHP_URL_HOST );
    $db_name = DB_NAME;
    $response = mt_api_list_related_accounts( $domain_name, $db_name, array('timeout' => 15) );
    $b_got_list = 0;
    if ( response_ok( $response ) ) {
        if ( !empty( $response['data']->staging ) && is_array( $response['data']->staging ) ) {
            $b_got_list = 1;
            $stage_array = $response['data']->staging;
            if ( get_option( $option_name ) !== false ) {
                update_option( $option_name, $stage_array );
            } else {
                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'no';
                if (add_option( $option_name, $stage_array, $deprecated, $autoload ) ) {
                } else {
                    echo '<br><span class="mtss-fatal">FATAL: Problem adding option ' . $option_name . '. Please contact MT Support.</span><br>';
                }
            }
        } else {
            echo '<p><span class="mtss-info">INFO: There are no staging sites to list</span><br>';
        }
    } elseif ( response_http_error( $response ) ) {
        if ( !empty( $response['data']->message ) ) {
            $msg = sanitize_msg( $response['data']->message );
            echo '<br><span class="mtss-error">ERROR: ' . $msg . '</span><br>';
        } else {
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': http error but no status message.</span><br>';
        }
    } elseif ( response_curl_error( $response ) ) {
        $msg = $response['curlerr'];
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': CURL_ERR: ' . $msg . '</span><br>';
    } else {
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': (unknown error). Please contact MT Support.</span><br>';
    }

    if ( $b_got_list && ( $staging_sites = get_option($option_name) ) !== false ) {
        $staging_site_count = count($staging_sites);
        if ( $staging_site_count > 0 ) {
?>
<script type='text/javascript'>
var count = <?php echo count($staging_sites); ?>

// When the document loads do everything inside here ...
jQuery(document).ready(function(){
<?php
            $cnt = 0;
            foreach ($staging_sites as $site_name) {
                $cnt++;
                $site_prefix = array_shift(explode('.', $site_name->domain));
                $stage_id = 'deploy_' . $site_prefix . $cnt;
                $delete_id = 'delete_' . $site_prefix . $cnt;
                $loading_id = 'loading_' . $site_prefix . $cnt;
?>
    jQuery('#<?php echo $stage_id; ?>').click(function() { //start function when Sync button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'sync', site: escape( jQuery( '#site_<?php echo $stage_id; ?>' ).val() ), dbname: escape( jQuery( '#dbname_<?php echo $stage_id; ?>' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#<?php echo $loading_id; ?>').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#<?php echo $loading_id; ?>').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
            }
        }); //close jQuery.ajax
        return false;
    })
    jQuery('#<?php echo $delete_id; ?>').click(function() { //start function when Delete button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'delete_staging', site: escape( jQuery( '#site_<?php echo $stage_id; ?>' ).val() ), dbname: escape( jQuery( '#dbname_<?php echo $stage_id; ?>' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#<?php echo $loading_id; ?>').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#<?php echo $loading_id; ?>').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
                                jQuery('div#<?php echo $stage_id; ?>').remove(); //remove the DOM div element for this site
                                count--;
                                if (count == 0) { // if all sites removed, then remove Delete All button
                                    jQuery('#delete_all').remove();
                                    alert('All staging sites deleted');
                                }
            }
        }); //close jQuery.ajax
        return false;
    })
<?php
            }
?>
    jQuery('#delete_all_id').click(function() { //start function when Delete All button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'delete_all_staging', _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#loading').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#loading').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
<?php
            $cnt = 0;
            foreach ($staging_sites as $site_name) {
                $cnt++;
                $site_prefix = array_shift(explode('.', $site_name->domain));
                $stage_id = 'deploy_' . $site_prefix . $cnt;
?>
                                jQuery('div#<?php echo $stage_id; ?>').remove(); // remove individual site DOM div element
<?php
            }
?>
                                jQuery('#delete_all').remove(); // remove Delete All button
                                alert('All staging sites deleted');
            }
        }); //close jQuery.ajax
        return false;
    })
})
</script>
<style type='text/css'>
#loading { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
<?php
            $cnt = 0;
            foreach ($staging_sites as $site_name) {
                $cnt++;
                $site_prefix = array_shift(explode('.', $site_name->domain));
                $loading_id = 'loading_' . $site_prefix . $cnt;
?>
#<?php echo $loading_id; ?> { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
<?php
            }
?>
</style>
<div class="wrap">
<?php
            $cnt = 0;
            foreach ($staging_sites as $site_name) {
                $cnt++;
                $site_prefix = array_shift(explode('.', $site_name->domain));
                $stage_id = 'deploy_' . $site_prefix . $cnt;
                $delete_id = 'delete_' . $site_prefix . $cnt;
                $loading_id = 'loading_' . $site_prefix . $cnt;
?>
<div id='<?php echo $stage_id; ?>'>
<form action='' method='POST' id='helloworld4form'>
<p><b>Staging Site:</b> <input type='text' name='site' id='site_<?php echo $stage_id; ?>' value='<?php echo $site_name->domain; ?>' size=28 readonly />
<b>Staging DB Name:</b> <input type='text' name='dbname' id='dbname_<?php echo $stage_id; ?>' value='<?php echo $site_name->db_name; ?>' size=15 readonly />
<input type='submit' class='button' name='action' id='<?php echo $stage_id; ?>' value='Sync' />
<input type='submit' class='button' name='action' id='<?php echo $delete_id; ?>' value='Delete' />
</p></form>
<div id='<?php echo $loading_id; ?>'>PROCESSING</div>
</div>
<?php
            }
?>
<div id='delete_all'>
<p><form action='' method='POST' id='delete_all_form'>
<input type='submit' class='button' name='action' id='delete_all_id' value='Delete All' />
</form></p>
<div id='loading'>DELETING ALL SITES</div>
</div>
<p><div id='formstatus'></div></p>
</div> 
<?php
        } // end of staging_site_count
    } // end of get_option check
} // end function

function show_sites() {
    $nonce_tag = preg_replace('/\./', '_', $site_name);
    $nonce_tag = 'site-stager';
    $nonce = wp_create_nonce( $nonce_tag );
    $staging_option_name = 'staging_sites';
    if ( ( $staging_sites = get_option($staging_option_name) ) !== false ) {
        $staging_site_count = count($staging_sites);
        $total_site_count = $staging_site_count;
    }
    $template_option_name = 'template_sites';
    if ( ( $template_sites = get_option($template_option_name) ) !== false ) {
        $template_site_count += count($template_sites);
        $total_site_count += $template_site_count;
    }
    if ( $total_site_count > 0 ) {
?>
<script type='text/javascript'>
var count = <?php echo $total_site_count; ?>

// When the document loads do everything inside here ...
jQuery(document).ready(function(){
<?php
    if ( $staging_site_count > 0 ) {
        $cnt = 0;
        foreach ($staging_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $stage_id = 'deploy_' . $site_prefix . $cnt;
            $delete_id = 'delete_' . $site_prefix . $cnt;
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
    jQuery('#<?php echo $stage_id; ?>').click(function() { //start function when Sync button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'sync', site: escape( jQuery( '#site_<?php echo $stage_id; ?>' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#<?php echo $loading_id; ?>').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#<?php echo $loading_id; ?>').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
            }
        }); //close jQuery.ajax
        return false;
    })
    jQuery('#<?php echo $delete_id; ?>').click(function() { //start function when Delete button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'delete_staging', site: escape( jQuery( '#site_<?php echo $stage_id; ?>' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#<?php echo $loading_id; ?>').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#<?php echo $loading_id; ?>').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
                                jQuery('div#<?php echo $stage_id; ?>').remove(); //remove the DOM div element for this site
                                count--;
                                if (count == 0) { // if all sites removed, then remove Delete All button
                                    jQuery('#delete_all').remove();
                                    alert('All staging/template sites deleted');
                                }
            }
        }); //close jQuery.ajax
        return false;
    })
<?php
        }
    }
?>
<?php
    if ( $template_site_count > 0 ) {
        $cnt = 0;
        foreach ($template_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $template_id = 'template_' . $site_prefix . $cnt;
            $delete_id = 'delete_' . $site_prefix . $cnt;
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
    jQuery('#<?php echo $delete_id; ?>').click(function() { //start function when Delete button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'delete_template', site: escape( jQuery( '#site_<?php echo $template_id; ?>' ).val() ), _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#<?php echo $loading_id; ?>').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#<?php echo $loading_id; ?>').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
                                jQuery('div#<?php echo $template_id; ?>').remove(); //remove the DOM div element for this site
                                count--;
                                if (count == 0) { // if all sites removed, then remove Delete All button
                                    jQuery('#delete_all').remove();
                                    alert('All staging/template sites deleted');
                                }
            }
        }); //close jQuery.ajax
        return false;
    })
<?php
        }
    }
?>
    jQuery('#delete_all_id').click(function() { //start function when Delete All button is clicked
        jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'delete_all', _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {  //fadeIn loading just when link is clicked
                                jQuery('div#loading').fadeIn('fast');
            },
            success: function(html){ //so, if data is retrieved, store it in html
                                jQuery('div#loading').fadeOut('slow');
                                jQuery('div#formstatus').html( html ); //show the html inside formstatus div
<?php
    if ( $staging_site_count > 0 ) {
        $cnt = 0;
        foreach ($staging_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $stage_id = 'deploy_' . $site_prefix . $cnt;
?>
                                jQuery('div#<?php echo $stage_id; ?>').remove(); // remove individual site DOM div element
<?php
        }
    }
?>
<?php
    if ( $template_site_count > 0 ) {
        $cnt = 0;
        foreach ($template_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $template_id = 'template_' . $site_prefix . $cnt;
?>
                                jQuery('div#<?php echo $template_id; ?>').remove(); // remove individual site DOM div element
<?php
        }
    }
?>
                                jQuery('#delete_all').remove(); // remove Delete All button
                                alert('All staging/template sites deleted');
            }
        }); //close jQuery.ajax
        return false;
    })
})
</script>
<style type='text/css'>
#loading { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
<?php
    if ( $staging_site_count > 0 ) {
        $cnt = 0;
        foreach ($staging_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
#<?php echo $loading_id; ?> { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
<?php
        }
    }
?>
<?php
    if ( $template_site_count > 0 ) {
        $cnt = 0;
        foreach ($template_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
#<?php echo $loading_id; ?> { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
<?php
        }
    }
?>
<?php
    if ( ( $staging_site_count > 0 ) ||
         ( $template_site_count > 0 ) ) {
?>
<?php
    }
?>
</style>
<div class="wrap">
<?php
    if ( $staging_site_count > 0 ) {
?>
<h2>Staging Sites</h2>
<?php
        $cnt = 0;
        foreach ($staging_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $stage_id = 'deploy_' . $site_prefix . $cnt;
            $delete_id = 'delete_' . $site_prefix . $cnt;
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
<div id='<?php echo $stage_id; ?>'>
<p><form action='' method='POST' id='helloworld4form'>
Staging Site: <input type='text' name='site' id='site_<?php echo $stage_id; ?>' value='<?php echo $site_name; ?>' readonly />
<input type='submit' class='button' name='action' id='<?php echo $stage_id; ?>' value='Sync' />
<input type='submit' class='button' name='action' id='<?php echo $delete_id; ?>' value='Delete' />
</form></p>
<div id='<?php echo $loading_id; ?>'>PROCESSING</div>
</div>
<?php
        }
    }
?>
<?php
    if ( $template_site_count > 0 ) {
?>
<h2>Template Sites</h2>
<?php
        $cnt = 0;
        foreach ($template_sites as $site_name) {
            $cnt++;
            $site_prefix = array_shift(explode('.', $site_name));
            $template_id = 'template_' . $site_prefix . $cnt;
            $delete_id = 'delete_' . $site_prefix . $cnt;
            $loading_id = 'loading_' . $site_prefix . $cnt;
?>
<div id='<?php echo $template_id; ?>'>
<p><form action='' method='POST' id='helloworld4form'>
Template Site: <input type='text' name='site' id='site_<?php echo $template_id; ?>' value='<?php echo $site_name; ?>' readonly />
<input type='submit' class='button' name='action' id='<?php echo $delete_id; ?>' value='Delete' />
</form></p>
<div id='<?php echo $loading_id; ?>'>PROCESSING</div>
</div>
<?php
        }
    }
?>
<div id='delete_all'>
<p><form action='' method='POST' id='delete_all_form'>
<input type='submit' class='button' name='action' id='delete_all_id' value='Delete All' />
</form></p>
<div id='loading'>DELETING ALL SITES</div>
</div>
<p><div id='formstatus'></div></p>
</div>
<?php
    } // end of total_site_count check
} // end function

function sync_site() {
    check_ajax_referer( 'site-stager' );
    $step = 'SYNC_STAGING_TO_LIVE';
    $response = mt_api_sync_site( $_POST['site'], $_POST['dbname'], array('timeout' => 15) );
    if ( response_ok( $response ) ) {
        if ( $response['data'] == 1 ) {
            echo '<span class="mtss-info">INFO: Site ' . $_POST['site'] . ' Synced!</span><br>';
        } else {
            echo '<span class="mtss-error">ERROR: Sync failed for site ' . $_POST['site'] . '</span><br>';
        }
    } elseif ( response_http_error( $response ) ) {
        if ( !empty( $response['data']->message ) ) {
            $msg = sanitize_msg( $response['data']->message );
            echo '<br><span class="mtss-error">ERROR: ' . $msg . '</span><br>';
        } else {
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': http error but no status message.</span><br>';
        }
    } elseif ( response_curl_error( $response ) ) {
        $msg = $response['curlerr'];
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': CURL_ERR: ' . $msg . '.</span><br>';
    } else {
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': (unknown error). Please contact MT Support.</span><br>';
    }
    die();
}
add_action( 'wp_ajax_sync', 'sync_site' );

function mt_sites_page() {
?>
<div class="mtss-wrapper">
<a href="http://mediatemple.net" target="_blank"><img src="<?php echo WPSC_URL; ?>mt.png" align="left" /></a>
<div class="mtss-title">Media Temple Site Stager</div><br>
<div class="mtss-version">v <?php echo WPSC_VERSION; ?></div><br>
<?php
    show_sites_to_sync();
?>
</div>
<?php
}

function delete_staging_site() {
    check_ajax_referer( 'site-stager' );
    $step = 'DELETE_STAGING_SITE';
    if ( !empty( $_POST['dbname'] ) ) {
        $response = mt_api_delete_site( $_POST['site'], $_POST['dbname'], array('timeout' => 15) );
        //var_dump($response);
        if ( response_ok( $response ) ) {
            if ( !empty( $response['data']->status ) ) {
                if ( $response['data']->status === 'ok' ) {
                    // Delete staging site option from wp_options table
                    $option_name = 'staging_sites';
                    if ( get_option( $option_name ) !== false ) {
                        // The option already exists, so we just update it.
                        $tmp = get_option( $option_name );
                        if ( ( $sitekey = array_search($_POST['site'], $tmp ) ) !== false ) {
                            unset($tmp[$sitekey]);
                        }
                        update_option( $option_name, $tmp );
                    }
                    echo '<br><span class="mtss-info">INFO: Staging site ' . $_POST['site'] . ' deleted</span><br>';
                } else {
                    echo '<br><span class="mtss-error">ERROR: Problem deleting staging site. Please contact MT Support.</span><br>';
                }
            } else {
                echo '<br><span class="mtss-error">ERROR: Problem deleting staging site. Please contact MT Support.</span><br>';
            }
        } elseif ( response_http_error( $response ) ) {
            if ( !empty( $response['data']->message ) ) {
                $msg = sanitize_msg( $response['data']->message );
                echo '<br><span class="mtss-error">ERROR: ' . $msg . '</span><br>';
            } else {
                echo '<br><span class="mtss-error">ERROR: ' . $step . ': Could not delete staging site (http error but no status message). Please contact MT Support.</span><br>';
            }
        } elseif ( response_curl_error( $response ) ) {
            $msg = $response['curlerr'];
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': CURL_ERR: ' . $msg . '. Please contact MT Support.</span><br>';
        } else {
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': (unknown error). Please contact MT Support.</span><br>';
        }
    } else {
        echo '<br><span class="mtss-warning">WARNING: ' . $step . ': Could not delete staging site (no database name). Site is probably still in setup phase. Try again in a few minutes.</span><br>';
    }

    die();
}
add_action( 'wp_ajax_delete_staging', 'delete_staging_site' );

function delete_all_staging_sites() {
    check_ajax_referer( 'site-stager' );
    $step = 'DELETE_ALL_STAGING_SITES';
    $parent_domain = parse_url( home_url(), PHP_URL_HOST );
    $parent_db = DB_NAME;
    $response = mt_api_delete_all_staging_sites( $parent_domain, $parent_db, array('timeout' => 15) );
    //var_dump($response);
    if ( response_ok( $response ) ) {
        if ( !empty( $response['data']->status ) ) {
            if ( $response['data']->status === 'ok' ) {
                $option_name = 'staging_sites';
                if ( get_option( $option_name ) !== false ) {
                    // The option exists, so we just delete it.
                    if ( delete_option( $option_name ) !== false ) {
                        echo '<br><span class="mtss-info">INFO: All staging sites deleted</span><br>';
                    } else {
                        echo '<br><span class="mtss-error">ERROR: Problem deleting all staging sites</span><br>';
                    }
                }
            } else {
                echo '<br><span class="mtss-error">ERROR: Problem deleting all staging sites. Please contact MT Support.</span><br>';
            }
        } else {
            echo '<br><span class="mtss-error">ERROR: No ok status returned. Please contact MT Support.</span><br>';
        }
    } elseif ( response_http_error( $response ) ) {
        if ( !empty( $response['data']->message ) ) {
            $msg = sanitize_msg( $response['data']->message );
            echo '<br><span class="mtss-error">ERROR: ' . $msg . '</span><br>';
        } else {
            echo '<br><span class="mtss-error">ERROR: ' . $step . ': http error but no status message.</span><br>';
        }
    } elseif ( response_curl_error( $response ) ) {
        $msg = $response['curlerr'];
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': CURL_ERR: ' . $msg . '. Click Sites submenu to see if it was created.</span><br>';
    } else {
        echo '<br><span class="mtss-error">ERROR: ' . $step . ': (unknown error). Please contact MT Support.</span><br>';
    }
    die();
}
add_action( 'wp_ajax_delete_all_staging', 'delete_all_staging_sites' );

function wp_mtss_admin_scripts() {
    add_action('admin_enqueue_scripts', 'enqueue_wpmtss_js');
}

function enqueue_wpmtss_js() {
    wp_enqueue_script( 'mtssjs' );
    wp_enqueue_style( 'mtsscss' );
}

/**
* Add staging site notification to toolbar
*/
function mtss_staging_notice_menu() {
    global $wp_admin_bar;
    $wp_admin_bar->add_node(array(
        'id' => 'mt-gd-site-stager',
        'title' => __('This is your Staging Site!'),
        'href' => 'http://mediatemple.net'
    ));
}


// #####################################################################################
// ################                  MAIN PAGE                 #########################
// #####################################################################################
function wp_main_page() {
?>
<div class="mtss-wrapper">
<a href="http://mediatemple.net" target="_blank"><img src="<?php echo WPSC_URL; ?>mt.png" align="left" /></a>
<div class="mtss-title">Media Temple Site Stager</div><br>
<div class="mtss-version">v <?php echo WPSC_VERSION; ?></div><br>
<div class="info">
<h3>Welcome!</h3>
<p>
This plugin is being offered as a way for site developers to create copies<br>
of their live site, make changes, and then merge the changes back to their<br>
live site, essentially creating a pseudo development environment in which<br>
for them to work. Happy developing! We welcome your feedback!<br>
<p>
<h3>Usage</h3><p>
Under the Site Stager menu on the left...
<p>
Step 1. Click the Stage submenu to create a staging account. The site in the<br>
text box is your live site
<p>
Step 2. Click Sites submenu to see all of your staging accounts. This is where<br>
you can perform sync and delete actions. On the Sites page, clicking the Sync<br>
button will sync the staging site back to the live site. The Delete button is<br>
self-explanatory.<br>
<p>
<h3>Support</h3>
<p>
This plugin is in it's initial phase. Please contact <a href="http://mediatemple.net/help" target="_blank">Media Temple Support</a> with<br>
any error messages so that we can further assist.<br>
<p>
<h3>FAQ</h3>
<p>
<b>What is a stage site?</b><br>
A stage site is essentially a copy of your live site. You can make development<br>
changes on the stage site, and when you're ready to "go live," sync your<br>
changes back to the live site
<p>
<b>What are the wordpress login credentials of my new staging site?</b><br>
same as your live site
<p>
<b>When I click 'Sites', why is my Staging DB Name empty?</b><br>
Your site is in setup phase, and the db name is not available yet. Simply wait<br>
about 10 seconds and refresh the page
<p>
See the <a href="http://mediatemple.net/blog/news/welcome-to-the-future-of-managed-wordpress-hosting/" target="_blank">Media Temple Wordpress Blog</a> for more info about the product.
<p>
</div>
</div>
<?php
}
?>
