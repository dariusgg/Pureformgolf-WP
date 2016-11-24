<?php

#alias of get_option() but custom for our plugin
if (!function_exists('sfc_get_option')) {

    function sfc_get_option($option, $default = '') {

        $db_options = get_option('sfcounter_plugin_setting');

        $options = unserialize($db_options);

        if (strstr($option, '.')) {

            $args = explode('.', $option);

            if (isset($options['sfcounter'][$args[0]]) && isset($options['sfcounter'][$args[0]][$args[1]])) {
                return trim($options['sfcounter'][$args[0]][$args[1]]);
            }
        } else {

            if (isset($options['sfcounter'][$option])) {
                return trim($options['sfcounter'][$option]);
            }
        }

        return $default;
    }
}

#alias of get_option() but custom for our plugin
if (!function_exists('ssc_get_option')) {

    function ssc_get_option($option, $default = '') {

        $db_options = get_option('sfcounter_sticky_setting');

        $options = $db_options;

        if (strstr($option, '.')) {

            $args = explode('.', $option);

            if (isset($options['sscounter'][$args[0]]) && isset($options['sscounter'][$args[0]][$args[1]])) {
                return trim($options['sscounter'][$args[0]][$args[1]]);
            }
        } else {

            if (isset($options['sscounter'][$option])) {
                return trim($options['sscounter'][$option]);
            }
        }

        return $default;
    }
}

if (!function_exists('sfc_debug_add')) {

    function sfc_debug_add( $social, $error_msg, $solution ) {

        $error = array($social, $error_msg, $solution, time());

        $errors = sfc_debug_list();

        array_unshift( $errors , $error );

        update_option('sfcounter_debug_list', $errors);
    }
}

if (!function_exists('sfc_debug_list')) {

    function sfc_debug_list() {

        return (array) get_option('sfcounter_debug_list');
    }
}

if (!class_exists('SFCounter_Widget_Options')) {

    class SFCounter_Widget_Options {

        private static $options = array();

        public static function register_options($options) {

            self::$options = $options;
        }

        public static function show_title() {

            if (false == self::get_option('hide_title')) {

                return true;
            }
        }

        public static function widget_columns() {

            return intval(self::get_option('columns'));
        }

        public static function column_class() {

            $columns = self::widget_columns();

            if ($columns == 1) {

                return 'sf-col-md-12 sf-col-one';
            }

            if ($columns > 0) {

                $css_cols = (12 / $columns);

                return 'sf-col-lg-'.$css_cols.' sf-col-md-'.$css_cols.' sf-col-sm-'.$css_cols.' sf-col-xs-'.$css_cols;
            }
        }

        public static function effect_class($social) {

            $effects = self::get_option('effects');

            if (in_array($social, array('wp_posts', 'wp_users', 'wp_comments'))) {
                $effects = 'sf-no-effect';
            }

            return $effects;
        }

        public static function show_numbers() {

            if (false == self::get_option('hide_numbers')) {

                return true;
            }
        }

        public static function show_diff() {

            if (false != self::get_option('show_diff')) {

                return true;
            }
        }

        public static function show_diff_lt_zero() {

            if (false != self::get_option('show_diff_lt_zero')) {

                return true;
            }
        }

        public static function diff_count_text_color() {

            $color = self::get_option('diff_count_text_color');
            if (!empty($color)) {
                return 'color: '.$color.' !important;';
            }
        }

        public static function diff_count_bg_color() {

            $color = self::get_option('diff_count_bg_color');

            if (!empty($color)) {
                return 'background-color: '.$color.' !important;';
            }
        }

        public static function css_bg_class($social) {

            $bg_color = self::get_option('bg_color');

            if ($bg_color == 'light') {

                return 'sf-dark-bg';
            }

            if ($bg_color == 'dark') {

                return 'sf-light-bg';
            }

            if ($bg_color == 'colord') {

                return 'sf-bg-'.$social;
            }

            if ($bg_color == 'transparent') {

                return 'sf-transparent';
            }
        }

        public static function css_text_color_class() {

            if (self::get_option('icon_color') == 'light') {

                return 'sf-dark-color';
            }

            if (self::get_option('icon_color') == 'dark') {

                return 'sf-light-color';
            }
        }

        public static function css_icon_image_class($social) {

            $icon = '';

            switch ($social) {
                case 'wp_posts':
                    $icon = '-sf-icon-wp-posts';
                    break;

                case 'wp_comments':
                    $icon = '-sf-icon-wp-comments';
                    break;

                case 'wp_users':
                    $icon = '-sf-icon-wp-users';
                    break;

                case 'weheartit':
                    $icon = '-sf-icon-weheartit-1';
                    break;

                default:
                    $icon = '-sf-icon-'.$social;
                    break;
            }
            return $icon;
        }

        public static function css_icon_color_class($social) {

            if (self::get_option('icon_color') == 'colord') {

                return 'sf-c-'.$social;
            }
        }

        public static function css_sp_class($social) {

            if (self::get_option('icon_color') == 'light') {

                return 'sf-dark-bg';
            }

            if (self::get_option('icon_color') == 'dark') {

                return 'sf-light-bg';
            }

            if (self::get_option('icon_color') == 'colord') {

                return 'sf-bg-'.$social;
            }
        }

        public static function css_hover_text_bg_color_class($social) {

            $hover_text_color = self::get_option('hover_text_bg_color');

            if ($hover_text_color == 'light') {

                return 'sf-dark-bg';
            }

            if ($hover_text_color == 'dark') {

                return 'sf-light-bg';
            }

            if ($hover_text_color == 'colord') {

                return 'sf-bg-'.$social;
            }
        }

        public static function css_hover_text_color_class($social) {

            $hover_text_color = self::get_option('hover_text_color');

            if ($hover_text_color == 'light') {

                return 'sf-dark-color';
            }

            if ($hover_text_color == 'dark') {

                return 'sf-light-color';
            }

            if ($hover_text_color == 'colord') {

                return 'sf-c-'.$social;
            }
        }

        public static function social_url($social) {

            switch ($social) {
                case 'facebook':
                    return 'http://www.facebook.com/'.sfc_get_option($social.'.id');
                    break;
                case 'twitter':
                    return 'http://www.twitter.com/'.sfc_get_option($social.'.id');
                    break;
                case 'google':
                    return 'http://plus.google.com/'.sfc_get_option($social.'.id');
                    break;
                case 'pinterest':
                    return 'http://www.pinterest.com/'.sfc_get_option($social.'.id');
                    break;
                case 'linkedin':
                    if (sfc_get_option($social.'.account_type', 'company') == 'company') {
                        return 'http://www.linkedin.com/company/'.sfc_get_option($social.'.id');
                    } elseif (sfc_get_option($social.'.account_type', 'company') == 'profile') {
                        return 'http://www.linkedin.com/profile/view?id='.sfc_get_option($social.'.id');
                    } else {
                        return '#'; // groups removed
                    }
                    break;
                case 'github':
                    return 'http://github.com/'.sfc_get_option($social.'.id');
                    break;
                case 'vimeo':
                    if (sfc_get_option($social.'.account_type', 'channel') == 'user') {
                        {
                            $vimeo_id = trim(sfc_get_option($social.'.id'));

                            if (preg_match('/^[0-9]+$/', $vimeo_id)) {
                                return 'http://vimeo.com/user'.$vimeo_id;
                            } else {
                                return 'http://vimeo.com/'.$vimeo_id;
                            }
                        }
                    } else {
                        return 'http://vimeo.com/channels/'.sfc_get_option($social.'.id');
                    }
                    break;
                case 'dribbble':
                    return 'http://dribbble.com/'.sfc_get_option($social.'.id');
                    break;
                case 'soundcloud':
                    return 'https://soundcloud.com/'.sfc_get_option($social.'.id');
                    break;
                case 'behance':
                    return 'http://www.behance.net/'.sfc_get_option($social.'.id');
                    break;
                case 'foursquare':
                    if (intval(sfc_get_option($social.'.id')) && intval(sfc_get_option($social.'.id')) == sfc_get_option($social.'.id')) {
                        return 'https://foursquare.com/user/'.sfc_get_option($social.'.id');
                    } else {
                        return 'https://foursquare.com/'.sfc_get_option($social.'.id');
                    }
                    break;
                case 'forrst':
                    return 'http://forrst.com/people/'.sfc_get_option($social.'.id');
                    break;
                case 'mailchimp':
                    return sfc_get_option($social.'.list_link');
                    break;
                case 'delicious':
                    return 'https://delicious.com/'.sfc_get_option($social.'.id');
                    break;
                case 'instgram':
                    return 'http://instagram.com/'.sfc_get_option($social.'.username');
                    break;
                case 'youtube':
                    if ( sfc_get_option($social.'.account_type') == 'channel' && sfc_get_option($social.'.custom_channel_url')) {

                        return 'http://www.youtube.com/c/'.sfc_get_option($social.'.custom_channel_url');
                    }
                    return 'http://www.youtube.com/'.sfc_get_option($social.'.account_type').'/'.sfc_get_option($social.'.id');
                case 'envato':
                    $ref = '';
                    if (sfc_get_option($social.'.ref')) {
                        $ref = '?ref='.sfc_get_option($social.'.ref');
                    }
                    return 'http://www.'.sfc_get_option($social.'.site').'.net/user/'.sfc_get_option($social.'.id').$ref;
                    break;
                case 'vk':
                    if (is_numeric(sfc_get_option($social.'.id'))) {
                        return 'http://www.vk.com/id' . sfc_get_option($social . '.id');
                    } else {
                        return 'http://www.vk.com/' . sfc_get_option($social . '.id');
                    }
                    break;
                case 'rss':
                    return sfc_get_option($social.'.link');
                    break;
                case 'vine':
                    return 'https://vine.co/'.sfc_get_option($social.'.username');
                    break;
                case 'tumblr':
                    $basename2arr = explode('.', sfc_get_option($social.'.basename'));
                    if ($basename2arr == 'www') {
                        return 'http://'.sfc_get_option($social.'.basename');
                    } else {
                        return 'http://www.tumblr.com/follow/'.@$basename2arr[0];
                    }
                    break;
                case 'slideshare':
                    return 'http://www.slideshare.net/'.sfc_get_option($social.'.username');
                    break;
                case '500px':
                    return 'http://500px.com/'.sfc_get_option($social.'.username');
                    break;
                case 'flickr':
                    return 'https://www.flickr.com/photos/'.sfc_get_option($social.'.id');
                    break;
                case 'wp_posts':
                case 'wp_users':
                case 'wp_comments':
                    return 'javascript:void(0);';
                    break;
                case 'audioboo':
                    return 'https://audioboo.fm/users/'.sfc_get_option($social.'.id');
                    break;
                case 'steamcommunity':
                    return 'http://steamcommunity.com/groups/'.sfc_get_option($social.'.id');
                    break;
                case 'weheartit':
                    return 'http://weheartit.com/'.sfc_get_option($social.'.id');
                    break;
                case 'feedly':
                    return 'http://feedly.com/i/subscription/feed'.urlencode('/'.sfc_get_option($social.'.url'));
                    break;
            }
        }

        public static function fans_count($social, $format = true) {

            if (self::is_cached($social)) {
                if ($format) {
                    return self::format_count(self::get_cached_count($social));
                } else {
                    return self::get_cached_count($social);
                }
            }

            switch ($social) {
                case 'twitter':
                    $count = SocialFans_Counter::twitter();
                    break;
                case 'facebook':
                    $count = SocialFans_Counter::facebook();
                    break;
                case 'google':
                    $count = SocialFans_Counter::googleplus();
                    break;
                case 'pinterest':
                    $count = SocialFans_Counter::pinterest();
                    break;
                case 'linkedin':
                    $count = SocialFans_Counter::linkedin();
                    break;
                case 'vimeo':
                    $count = SocialFans_Counter::vimeo();
                    break;
                case 'github':
                    $count = SocialFans_Counter::github();
                    break;
                case 'dribbble':
                    $count = SocialFans_Counter::dribbble();
                    break;
                case 'envato':
                    $count = SocialFans_Counter::envato();
                    break;
                case 'soundcloud':
                    $count = SocialFans_Counter::soundcloud();
                    break;
                case 'behance':
                    $count = SocialFans_Counter::behance();
                    break;
                case 'foursquare':
                    $count = SocialFans_Counter::foursquare();
                    break;
                case 'forrst':
                    $count = SocialFans_Counter::forrst();
                    break;
                case 'mailchimp':
                    $count = SocialFans_Counter::mailchimp();
                    break;
                case 'delicious':
                    $count = SocialFans_Counter::delicious();
                    break;
                case 'instgram':
                    $count = SocialFans_Counter::instgram();
                    break;
                case 'youtube':
                    $count = SocialFans_Counter::youtube();
                    break;
                case 'vk':
                    $count = SocialFans_Counter::vk();
                    break;
                case 'rss':
                    $count = SocialFans_Counter::rss();
                    break;
                case 'vine':
                    $count = SocialFans_Counter::vine();
                    break;
                case 'tumblr':
                    $count = SocialFans_Counter::tumblr();
                    break;
                case 'slideshare':
                    $count = SocialFans_Counter::slideshare();
                    break;
                case '500px':
                    $count = SocialFans_Counter::c500Px();
                    break;
                case 'flickr':
                    $count = SocialFans_Counter::flickr();
                    break;
                case 'wp_posts':
                    $count = SocialFans_Counter::wpposts();
                    break;
                case 'wp_comments':
                    $count = SocialFans_Counter::wpcomments();
                    break;
                case 'wp_users':
                    $count = SocialFans_Counter::wpusers();
                    break;
                case 'audioboo':
                    $count = SocialFans_Counter::audioboo();
                    break;
                case 'steamcommunity':
                    $count = SocialFans_Counter::steamcommunity();
                    break;
                case 'weheartit':
                    $count = SocialFans_Counter::weheartit();
                    break;
                case 'feedly':
                    $count = SocialFans_Counter::feedly();
                    break;
                default:
                    $count = 0;
                    break;
            }

            if (empty($count)) {
                $count = self::get_cached_count($social);
            }

            self::cache_count($social, $count);

            if ($format) {
                return self::format_count($count);
            } else {
                return $count;
            }
        }

        public static function total_fans() {

            $total = 0;

            foreach (self::enabled_socials() as $social) {

                $count = self::get_cached_count($social);
                if (intval($count) > 0) {
                    $total += $count;
                }
            }

            return self::format_count($total);
        }

        public static function fans_text($social) {

            return sfc_get_option($social.'.text');
        }

        public static function fans_hover_text($social) {

            return sfc_get_option($social.'.hover_text');
        }

        public static function enabled_socials() {

            $socials = SocialFans_Counter_Plugin::orderedSocials();

            $result = array();

            foreach ($socials as $social) {

                if (sfc_get_option($social.'.enabled')) {

                    if (self::is_valid_account($social)) {

                        $result[] = $social;
                    }
                }
            }

            return $result;
        }

        public static function enabledStickySocials() {

            $socials = SocialFans_Counter_Plugin::orderedStickySocials();

            $result = array();

            foreach ($socials as $social) {

                if (ssc_get_option($social.'.enabled')) {

                    if (self::is_valid_account($social)) {

                        $result[] = $social;
                    }
                }
            }

            return $result;
        }

        public static function is_valid_account($social) {

            switch ($social) {

                case 'mailchimp':
                    return sfc_get_option($social.'.list_id');
                    break;
                case 'rss':
                    return sfc_get_option($social.'.link');
                    break;
                case 'feedly':
                    return sfc_get_option($social.'.url');
                    break;
                case 'vine':
                case 'slideshare':
                case '500px':
                    return sfc_get_option($social.'.username');
                    break;
                case 'tumblr':
                    return sfc_get_option($social.'.basename');
                    break;
                case 'wp_posts':
                case 'wp_comments':
                case 'wp_users':
                    return true;
                    break;
                default :
                    return sfc_get_option($social.'.id');
                    break;
            }
        }

        public static function show_total() {

            if (false != self::get_option('show_total')) {

                return true;
            }
        }

        public static function css_total_class() {

            $socials = count(self::enabled_socials());
            $columns = self::widget_columns();

            $rows = floor(($socials / $columns));
            $decimal = ($rows + 1) - ($socials / $columns);

            if ($decimal == 0) {
                $css_cols = 12;
            } else {
                $css_cols = ($decimal * 12);
            }

            return 'sf-block sf-view sf-col-lg-'.$css_cols.' sf-col-md-'.$css_cols.' sf-col-sm-'.$css_cols.' sf-col-xs-'.$css_cols;
        }

        public static function format_count($count) {

            $format = sfc_get_option('setting.format');

            switch ($format) {
                case 'nf':
                    $result = number_format(self::prevent_format_count($count), 0, '', '');
                    break;
                case 'd':
                    $result = number_format(self::prevent_format_count($count), 0, '', '.');
                    break;
                case 'c':
                    $result = number_format(self::prevent_format_count($count), 0, '', ',');
                    break;
                case 's':
                    $result = number_format(self::prevent_format_count($count), 0, '', ' ');
                    break;
                case 'l':
                    $result = self::format_count_to_letter(self::prevent_format_count($count));
                    break;
                default:
                    $result = $count;
                    break;
            }

            return $result;
        }

        private static function prevent_format_count($count) {

            if (strpos(strtolower($count), 'k')) {

                $count = (intval($count) * 1000);
            }

            if (strpos(strtolower($count), 'm')) {

                $count = (intval($count) * 1000);
            }

            return $count;
        }

        private static function format_count_to_letter($count) {

            $count = intval($count);

            if ($count < 1000) {
                return $count;
            }

            if ($count < 1000000) {
                return number_format(($count / 1000), 1).'k';
            }

            return number_format(($count / 1000000), 1).'m';
        }

        private static function get_option($option) {

            if (isset(self::$options[$option])) {

                return self::$options[$option];
            }
        }

        private static function is_cached($social) {

            $expire_time = get_option('sfcounter_'.$social.'_expire');
            $now = time();

            $is_alive = ($expire_time > $now);

            if (true == $is_alive) {
                return true;
            }

            return false;
        }

        private static function get_cached_count($social) {

            return get_option('sfcounter_'.$social.'_count');
        }

        private static function cache_count($social, $count) {

            $social_expire = sfc_get_option($social.'.expire');

            $expire_time = $social_expire;

            if (empty($social_expire)) {
                $expire_time = sfc_get_option('setting.expire');
            }

            update_option('sfcounter_'.$social.'_count', $count);
            update_option('sfcounter_'.$social.'_expire', (time() + ($expire_time * 60)));
        }

        public static function box_width() {

            if (self::get_option('box_width') > 0) {
                return "width: ".self::get_option('box_width').'px !important;';
            }
        }

        public static function calc_lastweek_count($social) {

            $last_week_count = get_option('sfcounter_'.$social.'_last_week_count');
            $current_count = self::get_cached_count($social);

            return ($current_count - $last_week_count);
        }

        public static function get_social_diff($social) {

            // last week date in db
            $last_week_day = get_option('sfcounter_last_week_date');

            // last week real
            $last_week_real = (time() - (7 * 24 * 60 * 60));

            $social_last_week_count = get_option('sfcounter_'.$social.'_last_week_count');

            // check if week done set new counters
            if (($last_week_real > $last_week_day) || empty($social_last_week_count)) {
                update_option('sfcounter_last_week_date', time());
                update_option('sfcounter_'.$social.'_last_week_count', self::get_cached_count($social));
            }

            return self::calc_lastweek_count($social);
        }

        public static function lazy_load() {

            return (self::get_option('is_lazy') == 1);
        }

        public static function block_shadow_class() {

            return (self::get_option('block_shadow') == 1) ? 'sf-shadow' : '';
        }

        public static function block_divider_class() {

            return (self::get_option('block_divider') == 1) ? 'sf-divider' : '';
        }

        public static function block_margin_class() {

            switch (self::get_option('block_margin')) {
                case 1:
                    return 'sf-m1';
                case 2:
                    return 'sf-m2';
                case 3:
                    return 'sf-m3';
                case 4:
                    return 'sf-m4';
                case 5:
                    return 'sf-m5';
                default:
                    return 'sf-m0';
            }
        }

        public static function block_radius_class() {

            switch (self::get_option('block_radius')) {
                case 5:
                    return 'sf-brdrdus5';
                case 10:
                    return 'sf-brdrdus10';
                case 15:
                    return 'sf-brdrdus15';
                case 20:
                    return 'sf-brdrdus20';
                default:
                    return 'sf-brdrdus0';
            }
        }
    }
}

if (!class_exists('SSCounter_Widget_Options')) {

    class SSCounter_Widget_Options {

        private static $options = array();

        public static function register_options($options) {

            self::$options = $options;
        }

        private static function get_option($option) {

                if (isset(self::$options[$option])) {

                    return self::$options[$option];
                }
            }

        public static function block_shadow_class() {

            return (self::get_option('block_shadow') == 1) ? 'sf-shadow' : '';
        }

        public static function block_divider_class() {

            return (self::get_option('block_divider') == 1) ? 'sf-divider' : '';
        }

        public static function block_margin_class() {

            switch (self::get_option('block_margin')) {
                case 1:
                    return 'sf-m1';
                case 2:
                    return 'sf-m2';
                case 3:
                    return 'sf-m3';
                case 4:
                    return 'sf-m4';
                case 5:
                    return 'sf-m5';
                default:
                    return 'sf-m0';
            }
        }

        public static function block_radius_class() {

            switch (self::get_option('block_radius')) {
                case 5:
                    return 'sf-brdrdus5';
                case 10:
                    return 'sf-brdrdus10';
                case 15:
                    return 'sf-brdrdus15';
                case 20:
                    return 'sf-brdrdus20';
                default:
                    return 'sf-brdrdus0';
            }
        }

        public static function block_position_class() {

            switch (self::get_option('position')) {
                case 'top':
                    return 'sf-top';
                case 'right':
                    return 'sf-right';
                case 'bottom':
                    return 'sf-bottom';
                default:
                    return 'sf-left';
            }
        }

        public static function block_size_class() {

            switch (self::get_option('block_size')) {

                case 'small':
                    return 'sf-size-1';
                case 'large':
                    return 'sf-size-3';
                default:
                    return 'sf-size-2';
            }
        }

        public static function show_numbers() {

            return self::get_option('show_numbers');
        }

        public static function is_lazy() {

            return self::get_option('is_lazy');
        }

        public static function show_total() {

            return self::get_option('show_total');
        }

        public static function css_text_color_class() {

            if (self::get_option('icon_color') == 'light') {

                return 'sf-dark-color';
            }

            if (self::get_option('icon_color') == 'dark') {

                return 'sf-light-color';
            }
        }

        public static function css_icon_image_class($social) {

            $icon = '';

            switch ($social) {
                case 'wp_posts':
                    $icon = '-sf-icon-wp-posts';
                    break;

                case 'wp_comments':
                    $icon = '-sf-icon-wp-comments';
                    break;

                case 'wp_users':
                    $icon = '-sf-icon-wp-users';
                    break;

                case 'weheartit':
                    $icon = '-sf-icon-weheartit-1';
                    break;

                default:
                    $icon = '-sf-icon-'.$social;
                    break;
            }
            return $icon;
        }

        public static function css_icon_color_class($social) {

            if (self::get_option('icon_color') == 'colord') {

                return 'sf-c-'.$social;
            }
        }

        public static function css_bg_class($social) {

            $bg_color = self::get_option('bg_color');

            if ($bg_color == 'light') {

                return 'sf-dark-bg';
            }

            if ($bg_color == 'dark') {

                return 'sf-light-bg';
            }

            if ($bg_color == 'colord') {

                return 'sf-bg-'.$social;
            }

            if ($bg_color == 'transparent') {

                return 'sf-transparent';
            }
        }

        public static function css_sp_class($social) {

            if (self::get_option('icon_color') == 'light') {

                return 'sf-dark-bg';
            }

            if (self::get_option('icon_color') == 'dark') {

                return 'sf-light-bg';
            }

            if (self::get_option('icon_color') == 'colord') {

                return 'sf-bg-'.$social;
            }
        }
    }
}
