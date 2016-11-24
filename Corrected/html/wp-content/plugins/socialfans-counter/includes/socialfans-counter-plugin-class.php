<?php

if ( !defined( 'DAY_IN_SECONDS' ) ) {
    define( 'DAY_IN_SECONDS' , ( 60 * 60 * 24 ) );
}

/**
 * SocialFans Counter Plugin Handler for all actions
 */
class SocialFans_Counter_Plugin {

    public function __construct() {

        // Load languages
        add_action('plugins_loaded', array($this, 'register_plugin_languages'), 0);

        // load count with ajax
        add_action('wp_ajax_sfcounter', array($this, 'register_plugin_ajax'));
        add_action('wp_ajax_nopriv_sfcounter', array($this, 'register_plugin_ajax'));
        add_action('wp_ajax_sscounter', array($this, 'register_sticky_ajax'));
        add_action('wp_ajax_nopriv_sscounter', array($this, 'register_sticky_ajax'));

        // admin menu
        add_action('admin_menu', array($this, 'register_admin_menu'));

        // save options
        add_action('admin_init', array($this, 'update_setting'));
        add_action('admin_init', array($this, 'update_sticky_setting'));
        add_action('admin_init', array($this, 'clear_debug_list'));

        // Scripts
        add_action('wp_enqueue_scripts', array($this, 'getPluginFonts'));
        add_action('wp_enqueue_scripts', array($this, 'register_front_assets'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));

        // shortcode
        add_shortcode('sfcounter', array($this, 'register_plugin_shortcodes'));
        add_shortcode('sscounter', array($this, 'register_plugin_sticky_shortcodes'));

        // default settings
        add_action('admin_init', array($this, 'register_install_plugin'));

        add_filter('init', array($this,'display_transient_update_plugins'));

        add_action('wp_footer', array($this, 'add_sticky_to_footer'));
    }

    function display_transient_update_plugins( $transient ) {

        require_once( dirname( __FILE__ ) . '/socialfans-counter-auto-update-class.php' );

        $sf_plugin_remote_path = 'http://labs.themeinity.com/plugins/updates/socialfans-counter.php';
        $sf_plugin_slug = 'socialfans-counter/socialfans-counter.php';

        new SocialFans_Counter_Auto_Update( SOCIALFANS_COUNTER_VERSION , $sf_plugin_remote_path , $sf_plugin_slug );
    }


    /**
     * add plugin links to admin menu
     */
    public function register_admin_menu() {

        $icon = SOCIALFANS_COUNTER_URL . 'assets/css/images/setting_tools.png';

        // check updates for current version
        $has_update = $this->checkUpdates( 'updates' , SOCIALFANS_COUNTER_CHECK_UPDATES_URL );

        $updates_text = '';

        // add new span for main menu button
        if ( $has_update ) {
            $updates_text = '<span class="awaiting-mod count-1"><span class="pending-count">' . __( 'New' , 'sfcounter' ) . '</span></span>';
        }

        // add menu pages
        add_menu_page( 'SFC' , 'Social Fans' . $updates_text , 'activate_plugins' , 'sfcounter-panel' , array( $this , 'register_admin_setting' ) , $icon );
        add_submenu_page( 'sfcounter-panel' , __( 'Setting' , 'sfcounter' ) , __( 'Setting' , 'sfcounter' ) , 'activate_plugins' , 'sfcounter-panel' , array( $this , 'register_admin_setting' ) );
        add_submenu_page( 'sfcounter-panel' , __( 'Stick Setting' , 'sfcounter' ) , __( 'Stick Setting' , 'sfcounter' ) , 'activate_plugins' , 'sscounter-panel' , array( $this , 'register_admin_sticky_setting' ) );
        add_submenu_page( 'sfcounter-panel' , __( 'Debug List' , 'sfcounter' ) , __( 'Debug List' , 'sfcounter' ) , 'activate_plugins' , 'sfcounter-debug' , array( $this , 'register_admin_debug_list' ) );
        add_submenu_page( 'sfcounter-panel' , __( 'Docs' , 'sfcounter' ) , __( 'Docs' , 'sfcounter' ) , 'activate_plugins' , 'sfcounter-docs' , array( $this , 'register_redirect_docs' ) );

        // add updates links
        if ( $has_update ) {
            add_submenu_page( 'sfcounter-panel' , __( 'New Update' , 'sfcounter' ) , __( 'New Update' , 'sfcounter' ) . $updates_text , 'activate_plugins' , 'sfcounter-updates' , array( $this , 'register_redirect_updates' ) );
        }
    }

    /**
     * check if current version has updates or not
     *
     * @return boolean
     */
    private function checkUpdates( $update_name , $update_url ) {

        $update_key = 'sfcounter_' . $update_name . '_status';

        // has updates
        $update_status = get_transient( $update_key );

        // check if status is true and time not expired
        if ( $update_status == '1' ) {
            return true;
        }

        if ( $update_status == '-1' ) {

            return false;
        }

        // set default update to failed
        set_transient( $update_key , '-1' , DAY_IN_SECONDS );

        // send request for updates url
        $request = wp_remote_get( $update_url );

        // get response ode from request
        $response_code = wp_remote_retrieve_response_code( $request );

        // check if request not OK return false
        if ( 200 == $response_code ) {

            // get body for current request
            $response = wp_remote_retrieve_body( $request );

            // convert body from json to assoc array
            $updates = @json_decode( $response , true );

            // be sure decode well done
            if ( !is_array( $updates ) || !isset( $updates['version'] ) ) {
                return false;
            }

            // compare between current version and request version
            if ( $updates['version'] > SOCIALFANS_COUNTER_VERSION ) {

                // update status
                set_transient( $update_key , '1' , DAY_IN_SECONDS );

                // return we have updates :)
                return true;
            }
        }
    }

    /**
     * redirect to updates url if action fired
     */
    public function register_redirect_updates() {

        echo '<script>window.location = "' . SOCIALFANS_COUNTER_UPDATES_URL . '"</script>';
        exit;
    }

    /**
     * redirect to new version url if action fired
     */
    public function register_redirect_new_version() {

        echo '<script>window.location = "' . SOCIALFANS_COUNTER_NEW_VERSION_URL . '"</script>';
        exit;
    }

    /**
     * loading admin assets
     */
    public function register_admin_assets() {

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'social-fans-admin-script' , SOCIALFANS_COUNTER_URL . 'assets/js/social-fans-admin.js' , false , SOCIALFANS_COUNTER_VERSION );
        wp_enqueue_script( 'social-fans-clipboard' , SOCIALFANS_COUNTER_URL . 'assets/js/jquery.clipboard.js' , false , SOCIALFANS_COUNTER_VERSION );

        wp_enqueue_style( 'social-fans-admin-style-ui' , SOCIALFANS_COUNTER_URL . 'assets/css/jquery-ui.css' , false , SOCIALFANS_COUNTER_VERSION );
    }

    public function register_front_assets() {

        wp_enqueue_script( 'jquery' );

        wp_register_style( 'socialfans-widget-style' , SOCIALFANS_COUNTER_URL . 'assets/css/socialfans-style.css' , false , SOCIALFANS_COUNTER_VERSION );
        wp_enqueue_script( 'socialfans-widget-script' , SOCIALFANS_COUNTER_URL . 'assets/js/socialfans-script.js' , false , SOCIALFANS_COUNTER_VERSION, true );

        wp_enqueue_style( 'socialfans-widget-style' );
        wp_enqueue_script( 'socialfans-widget-script' );

        wp_localize_script( 'socialfans-widget-script' , 'SfcounterObject' , array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }

    public function getPluginFonts() {

        $fonts_url = SOCIALFANS_COUNTER_URL . 'assets/font/';

        $fonts = "<style type=\"text/css\">\n";
        $fonts .= "@font-face {\n";
        $fonts .= "\tfont-family: 'socialfans';\n";
        $fonts .= sprintf("\tsrc: url('%s%s?v=%s');\n", $fonts_url, 'socialfans.eot', SOCIALFANS_COUNTER_VERSION);
        $fonts .= sprintf("\tsrc: url('%s%s?v=%s#iefix') format('embedded-opentype'),\n", $fonts_url, 'socialfans.eot', SOCIALFANS_COUNTER_VERSION);
        $fonts .= sprintf("\turl('%s%s?v=%s') format('woff'),\n", $fonts_url, 'socialfans.woff', SOCIALFANS_COUNTER_VERSION);
        $fonts .= sprintf("\turl('%s%s?v=%s') format('truetype'),\n", $fonts_url, 'socialfans.ttf', SOCIALFANS_COUNTER_VERSION);
        $fonts .= sprintf("\turl('%s%s?v=%s') format('svg');\n", $fonts_url, 'socialfans.svg', SOCIALFANS_COUNTER_VERSION);
        $fonts .= "\tfont-weight: normal;\n";
        $fonts .= "\tfont-style: normal;\n";
        $fonts .= "}\n";
        $fonts .= "</style>\n";

        echo $fonts;
    }

    /**
     * load text domain
     */
    public function register_plugin_languages() {

        load_plugin_textdomain( 'sfcounter' , false , 'socialfans-counter/languages/' );
    }

    /**
     * register shortcodes for plugin
     */
    public function register_plugin_shortcodes( $attrs ) {

        $defaults = array(
            'title' => 'Social Stats' ,
            'hide_numbers' => 0 ,
            'hide_title' => 0 ,
            'show_total' => 1 ,
            'box_width' => '' ,
            'is_lazy' => 1 ,
            'block_shadow' => 0 ,
            'block_divider' => 0 ,
            'block_margin' => 0 ,
            'block_radius' => 0 ,
            'columns' => 2 ,
            'effects' => 'sf-no-effect' ,
            'icon_color' => 'light' ,
            'bg_color' => 'colord' ,
            'hover_text_color' => 'light' ,
            'hover_text_bg_color' => 'colord' ,
            'show_diff' => 1 ,
            'show_diff_lt_zero' => 0 ,
            'diff_count_text_color' => '' ,
            'diff_count_bg_color' => '' ,
        );

        $attrs = shortcode_atts( $defaults , $attrs );

        SFCounter_Widget_Options::register_options( (array) $attrs );

        extract( $attrs );

        ob_start();
        include SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-shortcode-view.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    /**
     * register sticky shortcodes for plugin
     */
    public function register_plugin_sticky_shortcodes( $attrs ) {

        $defaults = array(
            'is_lazy' => 1 ,
            'show_numbers' => 1 ,
            'show_total' => 1 ,
            'block_shadow' => 0 ,
            'block_divider' => 0 ,
            'block_size' => 'medium' ,
            'block_margin' => 0 ,
            'block_radius' => 0 ,
            'position' => 'left' ,
            'icon_color' => 'light' ,
            'bg_color' => 'colord' ,
        );

        $attrs = shortcode_atts( $defaults , $attrs );

        SSCounter_Widget_Options::register_options( (array) $attrs );

        ob_start();
        include SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-sticky-shortcode-view.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * fire this method when plugin activated first time
     */
    public function register_install_plugin() {

        $current_option = get_option( 'sfcounter_plugin_version' );

        $index = 0;
        $defaults = array();
        $defaults['sfcounter'] = array();

        $defaults['sfcounter']['facebook']['id'] = '';
        $defaults['sfcounter']['facebook']['account_type'] = 'page';
        $defaults['sfcounter']['facebook']['access_token'] = '';
        $defaults['sfcounter']['facebook']['followers_count'] = '';
        $defaults['sfcounter']['facebook']['text'] = __( 'Fans' , 'sfcounter' );
        $defaults['sfcounter']['facebook']['hover_text'] = __( 'Like' , 'sfcounter' );
        $defaults['sfcounter']['facebook']['expire'] = 0;
        $defaults['sfcounter']['facebook']['enabled'] = 0;
        $defaults['sfcounter']['facebook']['order'] = $index++;

        $defaults['sfcounter']['twitter']['consumer_key'] = '';
        $defaults['sfcounter']['twitter']['consumer_secret'] = '';
        $defaults['sfcounter']['twitter']['access_token'] = '';
        $defaults['sfcounter']['twitter']['access_token_secret'] = '';
        $defaults['sfcounter']['twitter']['id'] = '';
        $defaults['sfcounter']['twitter']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['twitter']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['twitter']['expire'] = 0;
        $defaults['sfcounter']['twitter']['enabled'] = 0;
        $defaults['sfcounter']['twitter']['order'] = $index++;

        $defaults['sfcounter']['google']['id'] = '';
        $defaults['sfcounter']['google']['api_key'] = '';
        $defaults['sfcounter']['google']['text'] = __( 'Fans' , 'sfcounter' );
        $defaults['sfcounter']['google']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['google']['expire'] = 0;
        $defaults['sfcounter']['google']['enabled'] = 0;
        $defaults['sfcounter']['google']['order'] = $index++;

        $defaults['sfcounter']['pinterest']['id'] = '';
        $defaults['sfcounter']['pinterest']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['pinterest']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['pinterest']['expire'] = 0;
        $defaults['sfcounter']['pinterest']['enabled'] = 0;
        $defaults['sfcounter']['pinterest']['order'] = $index++;

        $defaults['sfcounter']['linkedin']['app_key'] = '';
        $defaults['sfcounter']['linkedin']['app_secret'] = '';
        $defaults['sfcounter']['linkedin']['id'] = '';
        $defaults['sfcounter']['linkedin']['account_type'] = 'profile';
        $defaults['sfcounter']['linkedin']['connections_count'] = '';
        $defaults['sfcounter']['linkedin']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['linkedin']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['linkedin']['expire'] = 0;
        $defaults['sfcounter']['linkedin']['enabled'] = 0;
        $defaults['sfcounter']['linkedin']['order'] = $index++;

        $defaults['sfcounter']['github']['id'] = '';
        $defaults['sfcounter']['github']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['github']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['github']['expire'] = 0;
        $defaults['sfcounter']['github']['enabled'] = 0;
        $defaults['sfcounter']['github']['order'] = $index++;

        $defaults['sfcounter']['vimeo']['id'] = '';
        $defaults['sfcounter']['vimeo']['account_type'] = 'channel';
        $defaults['sfcounter']['vimeo']['access_token'] = '';
        $defaults['sfcounter']['vimeo']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['vimeo']['hover_text'] = __( 'Subscribe' , 'sfcounter' );
        $defaults['sfcounter']['vimeo']['expire'] = 0;
        $defaults['sfcounter']['vimeo']['enabled'] = 0;
        $defaults['sfcounter']['vimeo']['order'] = $index++;

        $defaults['sfcounter']['dribbble']['id'] = '';
        $defaults['sfcounter']['dribbble']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['dribbble']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['dribbble']['expire'] = 0;
        $defaults['sfcounter']['dribbble']['enabled'] = 0;
        $defaults['sfcounter']['dribbble']['order'] = $index++;

        $defaults['sfcounter']['envato']['id'] = '';
        $defaults['sfcounter']['envato']['site'] = 'themeforest';
        $defaults['sfcounter']['envato']['ref'] = '';
        $defaults['sfcounter']['envato']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['envato']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['envato']['expire'] = 0;
        $defaults['sfcounter']['envato']['enabled'] = 0;
        $defaults['sfcounter']['envato']['order'] = $index++;

        $defaults['sfcounter']['soundcloud']['api_key'] = '';
        $defaults['sfcounter']['soundcloud']['id'] = '';
        $defaults['sfcounter']['soundcloud']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['soundcloud']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['soundcloud']['expire'] = 0;
        $defaults['sfcounter']['soundcloud']['enabled'] = 0;
        $defaults['sfcounter']['soundcloud']['order'] = $index++;

        $defaults['sfcounter']['behance']['api_key'] = '';
        $defaults['sfcounter']['behance']['id'] = '';
        $defaults['sfcounter']['behance']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['behance']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['behance']['expire'] = 0;
        $defaults['sfcounter']['behance']['enabled'] = 0;
        $defaults['sfcounter']['behance']['order'] = $index++;

        $defaults['sfcounter']['foursquare']['api_key'] = '';
        $defaults['sfcounter']['foursquare']['text'] = __( 'Friends' , 'sfcounter' );
        $defaults['sfcounter']['foursquare']['hover_text'] = __( 'Add' , 'sfcounter' );
        $defaults['sfcounter']['foursquare']['expire'] = 0;
        $defaults['sfcounter']['foursquare']['enabled'] = 0;
        $defaults['sfcounter']['foursquare']['order'] = $index++;

        $defaults['sfcounter']['forrst']['id'] = '';
        $defaults['sfcounter']['forrst']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['forrst']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['forrst']['expire'] = '';
        $defaults['sfcounter']['forrst']['enabled'] = 0;
        $defaults['sfcounter']['forrst']['order'] = $index++;

        $defaults['sfcounter']['mailchimp']['api_key'] = '';
        $defaults['sfcounter']['mailchimp']['id'] = '';
        $defaults['sfcounter']['mailchimp']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['mailchimp']['hover_text'] = __( 'Subscribe' , 'sfcounter' );
        $defaults['sfcounter']['mailchimp']['list_url'] = '';
        $defaults['sfcounter']['mailchimp']['expire'] = 0;
        $defaults['sfcounter']['mailchimp']['enabled'] = 0;
        $defaults['sfcounter']['mailchimp']['order'] = $index++;

        $defaults['sfcounter']['delicious']['id'] = '';
        $defaults['sfcounter']['delicious']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['delicious']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['delicious']['expire'] = 0;
        $defaults['sfcounter']['delicious']['enabled'] = 0;
        $defaults['sfcounter']['delicious']['order'] = $index++;

        $defaults['sfcounter']['instgram']['id'] = '';
        $defaults['sfcounter']['instgram']['username'] = '';
        $defaults['sfcounter']['instgram']['text'] = 'Followers';
        $defaults['sfcounter']['instgram']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['instgram']['expire'] = 0;
        $defaults['sfcounter']['instgram']['enabled'] = 0;
        $defaults['sfcounter']['instgram']['order'] = $index++;

        $defaults['sfcounter']['youtube']['key'] = '';
        $defaults['sfcounter']['youtube']['id'] = '';
        $defaults['sfcounter']['youtube']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['youtube']['hover_text'] = __( 'Subscribe' , 'sfcounter' );
        $defaults['sfcounter']['youtube']['account_type'] = 'user';
        $defaults['sfcounter']['youtube']['expire'] = 0;
        $defaults['sfcounter']['youtube']['enabled'] = 0;
        $defaults['sfcounter']['youtube']['order'] = $index++;

        $defaults['sfcounter']['vk']['id'] = '';
        $defaults['sfcounter']['vk']['account_type'] = 'user';
        $defaults['sfcounter']['vk']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['vk']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['vk']['expire'] = 0;
        $defaults['sfcounter']['vk']['enabled'] = 0;
        $defaults['sfcounter']['vk']['order'] = $index++;

        $defaults['sfcounter']['rss']['link'] = '';
        $defaults['sfcounter']['rss']['count'] = '';
        $defaults['sfcounter']['rss']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['rss']['hover_text'] = __( 'Subscribe' , 'sfcounter' );
        $defaults['sfcounter']['rss']['expire'] = 0;
        $defaults['sfcounter']['rss']['enabled'] = 0;
        $defaults['sfcounter']['rss']['order'] = $index++;

        $defaults['sfcounter']['vine']['email'] = '';
        $defaults['sfcounter']['vine']['password'] = '';
        $defaults['sfcounter']['vine']['username'] = '';
        $defaults['sfcounter']['vine']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['vine']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['vine']['expire'] = 0;
        $defaults['sfcounter']['vine']['enabled'] = 0;
        $defaults['sfcounter']['vine']['order'] = $index++;

        $defaults['sfcounter']['tumblr']['api_key'] = '';
        $defaults['sfcounter']['tumblr']['basename'] = '';
        $defaults['sfcounter']['tumblr']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['tumblr']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['tumblr']['expire'] = 0;
        $defaults['sfcounter']['tumblr']['enabled'] = 0;
        $defaults['sfcounter']['tumblr']['order'] = $index++;

        $defaults['sfcounter']['slideshare']['username'] = '';
        $defaults['sfcounter']['slideshare']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['slideshare']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['slideshare']['expire'] = 0;
        $defaults['sfcounter']['slideshare']['enabled'] = 0;
        $defaults['sfcounter']['slideshare']['order'] = $index++;

        $defaults['sfcounter']['500px']['api_key'] = '';
        $defaults['sfcounter']['500px']['api_secret'] = '';
        $defaults['sfcounter']['500px']['username'] = '';
        $defaults['sfcounter']['500px']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['500px']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['500px']['expire'] = 0;
        $defaults['sfcounter']['500px']['enabled'] = 0;
        $defaults['sfcounter']['500px']['order'] = $index++;

        $defaults['sfcounter']['flickr']['id'] = '';
        $defaults['sfcounter']['flickr']['count'] = '';
        $defaults['sfcounter']['flickr']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['flickr']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['flickr']['expire'] = 0;
        $defaults['sfcounter']['flickr']['enabled'] = 0;
        $defaults['sfcounter']['flickr']['order'] = $index++;

        $defaults['sfcounter']['wp_posts']['text'] = __( 'Posts' , 'sfcounter' );
        $defaults['sfcounter']['wp_posts']['expire'] = 0;
        $defaults['sfcounter']['wp_posts']['enabled'] = 0;
        $defaults['sfcounter']['wp_posts']['order'] = $index++;

        $defaults['sfcounter']['wp_comments']['text'] = __( 'Comments' , 'sfcounter' );
        $defaults['sfcounter']['wp_comments']['expire'] = 0;
        $defaults['sfcounter']['wp_comments']['enabled'] = 0;
        $defaults['sfcounter']['wp_comments']['order'] = $index++;

        $defaults['sfcounter']['wp_users']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['wp_users']['expire'] = 0;
        $defaults['sfcounter']['wp_users']['enabled'] = 0;
        $defaults['sfcounter']['wp_users']['order'] = $index++;

        $defaults['sfcounter']['audioboo']['id'] = '';
        $defaults['sfcounter']['audioboo']['text'] = __( 'Followers' , 'sfcounter' );
        $defaults['sfcounter']['audioboo']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['audioboo']['expire'] = 0;
        $defaults['sfcounter']['audioboo']['enabled'] = 0;
        $defaults['sfcounter']['audioboo']['order'] = $index++;

        $defaults['sfcounter']['steamcommunity']['id'] = '';
        $defaults['sfcounter']['steamcommunity']['text'] = __( 'Members' , 'sfcounter' );
        $defaults['sfcounter']['steamcommunity']['hover_text'] = __( 'Join' , 'sfcounter' );
        $defaults['sfcounter']['steamcommunity']['expire'] = 0;
        $defaults['sfcounter']['steamcommunity']['enabled'] = 0;
        $defaults['sfcounter']['steamcommunity']['order'] = $index++;

        $defaults['sfcounter']['weheartit']['id'] = '';
        $defaults['sfcounter']['weheartit']['text'] = __( 'Followrs' , 'sfcounter' );
        $defaults['sfcounter']['weheartit']['hover_text'] = __( 'Follow' , 'sfcounter' );
        $defaults['sfcounter']['weheartit']['expire'] = 0;
        $defaults['sfcounter']['weheartit']['enabled'] = 0;
        $defaults['sfcounter']['weheartit']['order'] = $index++;

        $defaults['sfcounter']['feedly']['url'] = '';
        $defaults['sfcounter']['feedly']['text'] = __( 'Subscribers' , 'sfcounter' );
        $defaults['sfcounter']['feedly']['hover_text'] = __( 'subscribe' , 'sfcounter' );
        $defaults['sfcounter']['feedly']['expire'] = 0;
        $defaults['sfcounter']['feedly']['enabled'] = 0;
        $defaults['sfcounter']['feedly']['order'] = $index++;

        $defaults['sfcounter']['total']['text'] = __( 'Fans Love us' , 'sfcounter' );

        $defaults['sfcounter']['setting']['expire'] = 60;
        $defaults['sfcounter']['setting']['format'] = 'l';

        $defaults['sfcounter']['shortcode']['title'] = __( 'Social Stats' );
        $defaults['sfcounter']['shortcode']['hide_title'] = '0';
        $defaults['sfcounter']['shortcode']['hide_numbers'] = '0';
        $defaults['sfcounter']['shortcode']['show_total'] = '1';
        $defaults['sfcounter']['shortcode']['is_lazy'] = 1;
        $defaults['sfcounter']['shortcode']['columns'] = '2';
        $defaults['sfcounter']['shortcode']['effects'] = 'sf-no-effect';
        $defaults['sfcounter']['shortcode']['icon_color'] = 'light';
        $defaults['sfcounter']['shortcode']['bg_color'] = 'colord';
        $defaults['sfcounter']['shortcode']['hover_text_color'] = 'light';
        $defaults['sfcounter']['shortcode']['hover_text_bg_color'] = 'colord';
        $defaults['sfcounter']['shortcode']['show_diff'] = 1;
        $defaults['sfcounter']['shortcode']['show_diff_lt_zero'] = 0;
        $defaults['sfcounter']['shortcode']['diff_count_text_color'] = '';
        $defaults['sfcounter']['shortcode']['diff_count_bg_color'] = '';


        if ( empty( $current_option ) ) {

            update_option( 'sfcounter_plugin_setting' , serialize( $defaults ) );
            update_option( 'sfcounter_plugin_version' , SOCIALFANS_COUNTER_VERSION );
        }

        $index = 0;
        $sticky_defaults = array();
        $sticky_defaults['sscounter'] = array();

        $sticky_defaults['sscounter']['facebook']['enabled'] = 0;
        $sticky_defaults['sscounter']['facebook']['order'] = $index++;

        $sticky_defaults['sscounter']['twitter']['enabled'] = 0;
        $sticky_defaults['sscounter']['twitter']['order'] = $index++;

        $sticky_defaults['sscounter']['google']['enabled'] = 0;
        $sticky_defaults['sscounter']['google']['order'] = $index++;

        $sticky_defaults['sscounter']['pinterest']['enabled'] = 0;
        $sticky_defaults['sscounter']['pinterest']['order'] = $index++;

        $sticky_defaults['sscounter']['linkedin']['enabled'] = 0;
        $sticky_defaults['sscounter']['linkedin']['order'] = $index++;

        $sticky_defaults['sscounter']['github']['enabled'] = 0;
        $sticky_defaults['sscounter']['github']['order'] = $index++;

        $sticky_defaults['sscounter']['vimeo']['enabled'] = 0;
        $sticky_defaults['sscounter']['vimeo']['order'] = $index++;

        $sticky_defaults['sscounter']['dribbble']['enabled'] = 0;
        $sticky_defaults['sscounter']['dribbble']['order'] = $index++;

        $sticky_defaults['sscounter']['envato']['enabled'] = 0;
        $sticky_defaults['sscounter']['envato']['order'] = $index++;

        $sticky_defaults['sscounter']['soundcloud']['enabled'] = 0;
        $sticky_defaults['sscounter']['soundcloud']['order'] = $index++;

        $sticky_defaults['sscounter']['behance']['enabled'] = 0;
        $sticky_defaults['sscounter']['behance']['order'] = $index++;

        $sticky_defaults['sscounter']['foursquare']['enabled'] = 0;
        $sticky_defaults['sscounter']['foursquare']['order'] = $index++;

        $sticky_defaults['sscounter']['forrst']['enabled'] = 0;
        $sticky_defaults['sscounter']['forrst']['order'] = $index++;

        $sticky_defaults['sscounter']['mailchimp']['enabled'] = 0;
        $sticky_defaults['sscounter']['mailchimp']['order'] = $index++;

        $sticky_defaults['sscounter']['delicious']['enabled'] = 0;
        $sticky_defaults['sscounter']['delicious']['order'] = $index++;

        $sticky_defaults['sscounter']['instgram']['enabled'] = 0;
        $sticky_defaults['sscounter']['instgram']['order'] = $index++;

        $sticky_defaults['sscounter']['youtube']['enabled'] = 0;
        $sticky_defaults['sscounter']['youtube']['order'] = $index++;

        $sticky_defaults['sscounter']['vk']['enabled'] = 0;
        $sticky_defaults['sscounter']['vk']['order'] = $index++;

        $sticky_defaults['sscounter']['rss']['enabled'] = 0;
        $sticky_defaults['sscounter']['rss']['order'] = $index++;

        $sticky_defaults['sscounter']['vine']['enabled'] = 0;
        $sticky_defaults['sscounter']['vine']['order'] = $index++;

        $sticky_defaults['sscounter']['tumblr']['enabled'] = 0;
        $sticky_defaults['sscounter']['tumblr']['order'] = $index++;

        $sticky_defaults['sscounter']['slideshare']['enabled'] = 0;
        $sticky_defaults['sscounter']['slideshare']['order'] = $index++;

        $sticky_defaults['sscounter']['500px']['enabled'] = 0;
        $sticky_defaults['sscounter']['500px']['order'] = $index++;

        $sticky_defaults['sscounter']['flickr']['enabled'] = 0;
        $sticky_defaults['sscounter']['flickr']['order'] = $index++;

        $sticky_defaults['sscounter']['wp_posts']['enabled'] = 0;
        $sticky_defaults['sscounter']['wp_posts']['order'] = $index++;

        $sticky_defaults['sscounter']['wp_comments']['enabled'] = 0;
        $sticky_defaults['sscounter']['wp_comments']['order'] = $index++;

        $sticky_defaults['sscounter']['wp_users']['enabled'] = 0;
        $sticky_defaults['sscounter']['wp_users']['order'] = $index++;

        $sticky_defaults['sscounter']['audioboo']['enabled'] = 0;
        $sticky_defaults['sscounter']['audioboo']['order'] = $index++;

        $sticky_defaults['sscounter']['steamcommunity']['enabled'] = 0;
        $sticky_defaults['sscounter']['steamcommunity']['order'] = $index++;

        $sticky_defaults['sscounter']['weheartit']['enabled'] = 0;
        $sticky_defaults['sscounter']['weheartit']['order'] = $index++;

        $sticky_defaults['sscounter']['feedly']['enabled'] = 0;
        $sticky_defaults['sscounter']['feedly']['order'] = $index++;

        $sticky_defaults['sscounter']['feedly']['enabled'] = 0;
        $sticky_defaults['sscounter']['feedly']['order'] = $index++;

        $sticky_defaults['sscounter']['status'] = 0;
        $sticky_defaults['sscounter']['enabled']['home'] = 0;
        $sticky_defaults['sscounter']['enabled']['archive'] = 0;
        $sticky_defaults['sscounter']['enabled']['post'] = 0;
        $sticky_defaults['sscounter']['enabled']['page'] = 0;
        $sticky_defaults['sscounter']['enabled']['search'] = 0;
        $sticky_defaults['sscounter']['enabled']['category'] = 0;
        $sticky_defaults['sscounter']['enabled']['404'] = 0;
        $sticky_defaults['sscounter']['enabled']['author'] = 0;

        $sticky_defaults['sscounter']['setting']['is_lazy'] = 1;
        $sticky_defaults['sscounter']['setting']['show_numbers'] = 1;
        $sticky_defaults['sscounter']['setting']['show_total'] = 1;
        $sticky_defaults['sscounter']['setting']['block_shadow'] = 0;
        $sticky_defaults['sscounter']['setting']['block_divider'] = 0;
        $sticky_defaults['sscounter']['setting']['position'] = 'left';
        $sticky_defaults['sscounter']['setting']['block_size'] = 'medium';
        $sticky_defaults['sscounter']['setting']['block_radius'] = 0;
        $sticky_defaults['sscounter']['setting']['block_margin'] = 0;
        $sticky_defaults['sscounter']['setting']['icon_color'] = 'light';
        $sticky_defaults['sscounter']['setting']['bg_color'] = 'colord';

        $sticky_defaults['sscounter']['shortcode']['is_lazy'] = 1;
        $sticky_defaults['sscounter']['shortcode']['show_numbers'] = 1;
        $sticky_defaults['sscounter']['shortcode']['show_total'] = 1;
        $sticky_defaults['sscounter']['shortcode']['block_shadow'] = 0;
        $sticky_defaults['sscounter']['shortcode']['block_divider'] = 0;
        $sticky_defaults['sscounter']['shortcode']['position'] = 'left';
        $sticky_defaults['sscounter']['shortcode']['block_size'] = 'medium';
        $sticky_defaults['sscounter']['shortcode']['block_radius'] = 0;
        $sticky_defaults['sscounter']['shortcode']['block_margin'] = 0;
        $sticky_defaults['sscounter']['shortcode']['icon_color'] = 'light';
        $sticky_defaults['sscounter']['shortcode']['bg_color'] = 'colord';

        if ( null == get_option('sfcounter_sticky_setting') ) {

            update_option( 'sfcounter_sticky_setting' , $defaults );
        }

        if ( !empty( $current_option ) && $current_option != SOCIALFANS_COUNTER_VERSION ) {

            update_option( 'sfcounter_plugin_version' , SOCIALFANS_COUNTER_VERSION );
        }
    }

    /**
     * show admin panel
     */
    public function register_admin_setting() {

        $panel = new SocialFans_Counter_Panel();
        $panel->view( SocialFans_Counter_Plugin::orderedSocials() , SocialFans_Counter_Plugin::cachePeriods() , SocialFans_Counter_Plugin::numbersFormat() );
    }

    /**
     * show admin sticky panel
     */
    public function register_admin_sticky_setting() {

        $panel = new SocialFans_Counter_Panel();
        $panel->viewSticky( SocialFans_Counter_Plugin::orderedStickySocials() );
    }

    /**
     * show admin debug list
     */
    public function register_admin_debug_list() {

        $panel = new SocialFans_Counter_Panel();
        $panel->viewDebug();
    }

    /**
     * redirect to plugin documentaion
     */
    public function register_redirect_docs() {

        echo '<script>window.location = "' . SOCIALFANS_COUNTER_DOCS_URL . '"</script>';
        exit;
    }

    /**
     *  set socials avaiable in the plugin
     * @return array
     */
    public static function availableSocials() {

        $socials = array();
        $socials[] = 'facebook';
        $socials[] = 'twitter';
        $socials[] = 'google';
        $socials[] = 'pinterest';
        $socials[] = 'linkedin';
        $socials[] = 'github';
        $socials[] = 'vimeo';
        $socials[] = 'dribbble';
        $socials[] = 'envato';
        $socials[] = 'soundcloud';
        $socials[] = 'behance';
        $socials[] = 'foursquare';
        $socials[] = 'forrst';
        $socials[] = 'mailchimp';
        $socials[] = 'delicious';
        $socials[] = 'instgram';
        $socials[] = 'youtube';
        $socials[] = 'vk';
        $socials[] = 'rss';
        $socials[] = 'vine';
        $socials[] = 'tumblr';
        $socials[] = 'slideshare';
        $socials[] = '500px';
        $socials[] = 'flickr';
        $socials[] = 'wp_posts';
        $socials[] = 'wp_comments';
        $socials[] = 'wp_users';
        $socials[] = 'audioboo';
        $socials[] = 'steamcommunity';
        $socials[] = 'weheartit';
        $socials[] = 'feedly';

        return $socials;
    }

    /**
     *  set cache periods
     * @return array
     */
    public static function cachePeriods() {

        $periods = array();
        $periods[0] = 'Use Default';
        $periods[15] = '15 Minutes';
        $periods[30] = '30 Minutes';
        $periods[45] = '45 minutes';
        $periods[60] = '01 Hour';
        $periods[120] = '03 Hours';
        $periods[600] = '06 Hours';
        $periods[540] = '09 Hours';
        $periods[720] = '12 Hours';
        $periods[900] = '15 Hours';
        $periods[1080] = '18 Hours';
        $periods[1260] = '21 Hours';
        $periods[1440] = '01 Day';
        $periods[4320] = '03 Days';
        $periods[7200] = '05 Days';
        $periods[14400] = '10 Days';
        $periods[21600] = '15 Days';
        $periods[28800] = '20 Days';
        $periods[36000] = '25 Days';
        $periods[43200] = '01 Month';

        return $periods;
    }

    /**
     *  set numbers format for stats
     * @return array
     */
    public static function numbersFormat() {

        $format = array();
        $format['nf'] = '1000, 10000'; #no format
        $format['d'] = '1.000, 10.000'; #format dot
        $format['c'] = '1,000, 10,000'; #format comma
        $format['s'] = '1 000, 10 000'; #format space
        $format['l'] = '1k, 10k, 100k, 1m'; #format with letters

        return $format;
    }

    /**
     * get socials ordered as user defined
     * @return array
     */
    public static function orderedSocials() {

        $ordered = array();

        foreach ( self::availableSocials() as $i => $social ) {

            $social_order = sfc_get_option( $social . '.order' , $i );

            if ( $social_order === '' ) {
                $social_order = $i;
            }

            $ordered[$social_order] = $social;

            $i++;
        }

        ksort( $ordered );

        return $ordered;
    }
    /**
     * get socials ordered as user defined
     * @return array
     */
    public static function orderedStickySocials() {

        $ordered = array();

        foreach ( self::availableSocials() as $i => $social ) {

            $social_order = ssc_get_option( $social . '.order' , $i );

            if ( $social_order === '' ) {
                $social_order = $i;
            }

            $ordered[$social_order] = $social;

            $i++;
        }

        ksort( $ordered );

        return $ordered;
    }

    /**
     * fire this method when user update setting
     */
    public function update_setting() {

        if ( isset( $_POST['sfcounter'] ) ) {

            $options = serialize( $_POST );

            update_option( 'sfcounter_plugin_setting' , $options );

            $this->clearCache();

            header( 'Location:' . admin_url() . 'admin.php?page=sfcounter-panel&update=1' );
            exit;
        }
    }

    /**
     * fire this method when user update setting
     */
    public function update_sticky_setting() {

        if ( isset( $_POST['sscounter'] ) ) {

            $options = $_POST;

            update_option( 'sfcounter_sticky_setting' , $options );

            $this->clearCache();

            header( 'Location:' . admin_url() . 'admin.php?page=sscounter-panel&update=1' );
            exit;
        }
    }

    /**
     * fire this method when user clear debug list
     */
    public function clear_debug_list() {

        if ( isset( $_POST['sfcounter-debug-clear'] ) ) {

            update_option( 'sfcounter_debug_list' , array() );

            header( 'Location:' . admin_url() . 'admin.php?page=sfcounter-debug' );
            exit;
        }
    }

    public function clearCache() {
        foreach ( self::availableSocials() as $social ) {

            $key = 'sfcounter_' . $social . '_expire';
            delete_option( $key );

        }
    }

    public function register_plugin_ajax($action = 'sfcounter') {

        $result = array();
        $result['status'] = 'success';

        $total = 0;

        $socials = SFCounter_Widget_Options::enabled_socials();

        if ($action == 'sscounter') {

            $socials = SFCounter_Widget_Options::enabledStickySocials();
        }

        foreach ( $socials as $social ) {

            $count = SFCounter_Widget_Options::fans_count( $social , false );
            $result['social'][$social]['count'] = $count;
            $result['social'][$social]['count_formated'] = SFCounter_Widget_Options::format_count( $count );

            $total += $count;
            $result['social']['total']['count'] = $total;
            $result['social']['total']['count_formated'] = SFCounter_Widget_Options::format_count( $total );

        }

        echo json_encode( $result );
        exit;
    }

    public function register_sticky_ajax() {

        $this->register_plugin_ajax('sscounter');
    }

    public function add_sticky_to_footer() {

        if ( false == ssc_get_option('status') ) return;

        if ( ( is_front_page() || is_home() ) && false == ssc_get_option( 'enabled.home' ) ) return;

        if ( ( is_archive() && !is_category() && !is_author() ) && false == ssc_get_option( 'enabled.archive' ) ) return;

        if ( is_single() && false == ssc_get_option( 'enabled.post' ) ) return;

        if ( is_page() && false == ssc_get_option( 'enabled.page' ) ) return;

        if ( is_search() && false == ssc_get_option( 'enabled.search' ) ) return;

        if ( is_category() && false == ssc_get_option( 'enabled.category' ) ) return;

        if ( is_404() && false == ssc_get_option( 'enabled.404' ) ) return;

        if ( is_author() && false == ssc_get_option( 'enabled.author' ) ) return;

        $shortcode = '[sscounter';
        $shortcode .= sprintf(' show_numbers="%s"', ssc_get_option( 'setting.show_numbers' ));
        $shortcode .= sprintf(' show_total="%s"', ssc_get_option( 'setting.show_total' ) );
        $shortcode .= sprintf(' block_shadow="%s"', ssc_get_option( 'setting.block_shadow' ) );
        $shortcode .= sprintf(' block_divider="%s"', ssc_get_option( 'setting.block_divider' ) );
        $shortcode .= sprintf(' block_size="%s"', ssc_get_option( 'setting.block_size' ) );
        $shortcode .= sprintf(' block_margin="%s"', ssc_get_option( 'setting.block_margin' ) );
        $shortcode .= sprintf(' block_radius="%s"', ssc_get_option( 'setting.block_radius' ) );
        $shortcode .= sprintf(' position="%s"', ssc_get_option( 'setting.position' ) );
        $shortcode .= sprintf(' icon_color="%s"', ssc_get_option( 'setting.icon_color' ) );
        $shortcode .= sprintf(' bg_color="%s"', ssc_get_option( 'setting.bg_color' ) );
        $shortcode .= sprintf(']');

        echo do_shortcode($shortcode);
    }

}
