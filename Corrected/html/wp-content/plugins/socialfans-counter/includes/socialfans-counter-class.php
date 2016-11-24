<?php

if (!class_exists('OAuthServer')) {
    require_once SOCIALFANS_COUNTER_PATH.'includes/OAuth/OAuth.php';
}

if (!class_exists('TwitterOAuthn')) {
    require_once SOCIALFANS_COUNTER_PATH.'includes/twitter/twitteroauth.php';
}

if (!class_exists('Mailchimp')) {
    require_once SOCIALFANS_COUNTER_PATH.'includes/MailChimp/Mailchimp.php';
}

if (!class_exists('VineApp')) {
    require_once SOCIALFANS_COUNTER_PATH.'includes/vine/Vine.php';
}

if (!class_exists('TumblrClient')) {
    require_once SOCIALFANS_COUNTER_PATH.'includes/Tumblr/Tumblr.php';
}

class SocialFans_Counter {

    public static function twitter() {

        $consumer_key = sfc_get_option('twitter.consumer_key');
        $consumer_secret = sfc_get_option('twitter.consumer_secret');
        $access_token = sfc_get_option('twitter.access_token');
        $access_token_secret = sfc_get_option('twitter.access_token_secret');
        $id = sfc_get_option('twitter.id');

        if (empty($consumer_key) || empty($consumer_secret) || empty($access_token) || empty($access_token_secret) || empty($id)) {

            sfc_debug_add('Twitter', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));

            return 0;
        }

        $api = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        $response = $api->get('users/lookup', array('screen_name' => trim($id)));

        if (isset($response->errors)) {

            $error = isset($response->errors[0]) ? (array) $response->errors[0] : array('message' => json_encode((array) $response->errors));
            sfc_debug_add('Twitter', sprintf('Api error: %s', $error['message']), __('Contact support team', 'sfcounter'));

            return null;
        }

        if (isset($response[0]) && is_array($response[0])) {

            return $response[0]['followers_count'];
        }

        if (isset($response[0]->followers_count)) {

            return $response[0]->followers_count;
        }

        sfc_debug_add('Twitter', 'Api error: Response empty', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function facebook() {

        $id = sfc_get_option('facebook.id');
        $account_type = sfc_get_option('facebook.account_type', 'page');

        if (($account_type == 'page' && empty($id)) || ($account_type == 'followers' && (empty($id)))) {

            sfc_debug_add('Facebook', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        if ($account_type == 'followers') {

            return self::facebook_followers();
        } else {

            return self::facebook_page();
        }
    }

    private static function facebook_page() {

        $request = wp_remote_get('https://graph.facebook.com/'.sfc_get_option('facebook.id').'?fields=likes&access_token='.sfc_get_option('facebook.access_token'));

        if (false == $request) {

            sfc_debug_add('Facebook', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['likes'])) {
            return $response['likes'];
        }

        sfc_debug_add('Facebook', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    private static function facebook_followers() {

        $api_followers_count = get_option('sfcounter_facebook_count');// saved from api [because facebook stopped subscribers count version 3.3 ]
        $followers_count = sfc_get_option('facebook.followers_count');

        if (!$followers_count) {
            return $api_followers_count;
        }

        return $followers_count;
    }

    public static function googleplus() {

        $id = sfc_get_option('google.id');
        if (empty($id)) {

            sfc_debug_add('Google+', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('https://plus.google.com/'.urlencode($id).'/posts?hl=en');

        if (false == $request) {

            sfc_debug_add('Google+', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response = @wp_remote_retrieve_body($request);

        preg_match('/<span class="BOfSxb">([0-9., ]+)<\/span>/s', $response, $matches);

        if (!is_array($matches)) {

            sfc_debug_add('Google+', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
            return 0;
        }
        return str_replace(array('.', ' ', ','), '', $matches[1]);
    }

    public static function pinterest() {

        $id = sfc_get_option('pinterest.id');

        if (empty($id)) {

            sfc_debug_add('Pinterest', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('https://www.pinterest.com/'.$id);

        if (false == $request) {

            sfc_debug_add('Pinterest', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        @preg_match(' <meta property="pinterestapp:followers" name="pinterestapp:followers" content="(\d+)" data-app>', @wp_remote_retrieve_body($request), $matches);

        if (count($matches > 0) && isset($matches[1])) {
            return $matches[1];
        }

        sfc_debug_add('Pinterest', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function linkedin() {

        $id = sfc_get_option('linkedin.id');
        $account_type = sfc_get_option('linkedin.account_type', 'company');
        $token = sfc_get_option('linkedin.token');
        $connections_count = sfc_get_option('linkedin.connections_count');

        if (empty($id) || (empty($token) && empty($connections_count))) {

            sfc_debug_add('Linkedin', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $args = array(
            'headers' => array('Authorization' => sprintf('Bearer %s', $token))
        );

        if ($account_type == 'company') {

            $response = wp_remote_get(sprintf('https://api.linkedin.com/v1/companies/%s/num-followers?format=json', $id), $args);

            if (is_wp_error($response) || !$response) {

                sfc_debug_add('Linkedin', sprintf('Api Error: %s', json_encode($response)), __('Contact support team', 'sfcounter'));
                return null;
            }

            $count = intval(wp_remote_retrieve_body($response));

            if ($count == 0) {

                sfc_debug_add('Linkedin', sprintf('Api Error: %s', wp_remote_retrieve_body($response)), __('Contact support team', 'sfcounter'));
            }
            return $count;
        } elseif ($account_type == 'profile') {

            // get count from api [because linkedin limit connections to 500 version 3.3 ]
            if (!$connections_count) {

                $response = wp_remote_get('https://api.linkedin.com/v1/people/~:(num-connections)?format=json', $args);

                if (is_wp_error($response)) {

                    sfc_debug_add('Linkedin', sprintf('Api Error: %s', wp_remote_retrieve_body($response)), __('Contact support team', 'sfcounter'));
                    return 0;
                }

                $result = json_decode(wp_remote_retrieve_body($response), true);

                if (!$result || !isset($result['numConnections'])) {

                    sfc_debug_add('Linkedin', sprintf('Api Error: %s', json_encode($result)), __('Contact support team', 'sfcounter'));
                    return 0;
                }

                return $result['numConnections'];
            }

            return $connections_count;
        } else {

            sfc_debug_add('Linkedin', 'Account type not supported', __('Make sure Linkedin account type is selected', 'sfcounter'));
            return null;
        }
    }

    public static function github() {

        $id = sfc_get_option('github.id');

        if (empty($id)) {

            sfc_debug_add('Github', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('https://api.github.com/users/'.$id);

        if (false == $request) {

            sfc_debug_add('Github', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = @wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['followers'])) {
            return $response['followers'];
        }

        sfc_debug_add('Github', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function vimeo() {

        $id = sfc_get_option('vimeo.id');
        $account_type = sfc_get_option('vimeo.account_type', 'channel');
        $access_token = sfc_get_option('vimeo.access_token');

        if (($account_type == 'channel' && empty($id)) || ($account_type == 'user' && (empty($id) || empty($access_token)))) {

            sfc_debug_add('Vimeo', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        if ($account_type == 'user') {
            return self::vimeo_user();
        } else {
            return self::vimeo_channel();
        }
    }

    private static function vimeo_channel() {

        $request = wp_remote_get('http://vimeo.com/api/v2/channel/'.sfc_get_option('vimeo.id').'/info.json');

        if (false == $request) {

            sfc_debug_add('Vimeo', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['total_subscribers'])) {

            return $response['total_subscribers'];
        }

        sfc_debug_add('Vimeo', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    private static function vimeo_user() {

        $request = wp_remote_get('https://api.vimeo.com/users/'.sfc_get_option('vimeo.id').'/followers?access_token='.sfc_get_option('vimeo.access_token'));

        if (false == $request) {

            sfc_debug_add('Vimeo', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['total'])) {
            return $response['total'];
        }

        sfc_debug_add('Vimeo', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function dribbble() {

        $id = sfc_get_option('dribbble.id');
        $access_token = sfc_get_option('dribbble.access_token');

        if (empty($id) || empty($access_token)) {

            sfc_debug_add('Dribbble', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get(sprintf('https://api.dribbble.com/v1/users/%s?access_token=%s', $id, $access_token));

        if (false == $request) {

            sfc_debug_add('Dribbble', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['followers_count'])) {

            return $response['followers_count'];
        }

        sfc_debug_add('Dribbble', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function envato() {

        $id = sfc_get_option('envato.id');

        if (empty($id)) {

            sfc_debug_add('Envato', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('http://marketplace.envato.com/api/edge/user:'.$id.'.json');

        if (false == $request) {

            sfc_debug_add('Envato', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['user']) && isset($response['user']['followers'])) {

            return $response['user']['followers'];
        }

        sfc_debug_add('Envato', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function soundcloud() {

        $id = sfc_get_option('soundcloud.id');
        $api_key = sfc_get_option('soundcloud.api_key');

        if (empty($id) || empty($api_key)) {

            sfc_debug_add('Soundcloud', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('http://api.soundcloud.com/users/'.$id.'.json?client_id='.$api_key);

        if (false == $request) {

            sfc_debug_add('Envato', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['followers_count'])) {
            return $response['followers_count'];
        }

        sfc_debug_add('Soundcloud', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function behance() {

        $id = sfc_get_option('behance.id');
        $api_key = sfc_get_option('behance.api_key');

        if (empty($id) || empty($api_key)) {

            sfc_debug_add('Behance', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: en\r\n".
                    "Cookie: foo=bar\r\n".
                    "User-Agent:Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)\r\n"
            )
        );

        $context = stream_context_create($opts);

        $request = @wp_remote_get('http://www.behance.net/v2/users/'.$id.'/?api_key='.$api_key);

        if (false == $request) {

            sfc_debug_add('Behance', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['user']) && isset($response['user']['stats']) && isset($response['user']['stats']['followers'])) {

            return $response['user']['stats']['followers'];
        }

        sfc_debug_add('Behance', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function delicious() {

        $id = sfc_get_option('delicious.id');

        if (empty($id)) {

            sfc_debug_add('Delicious', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('http://feeds.delicious.com/v2/json/userinfo/'.$id);

        if (false == $request) {

            sfc_debug_add('Delicious', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['2']) && isset($response['2']['n'])) {

            return $response['2']['n'];
        }

        sfc_debug_add('Delicious', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function instgram() {

        $username = sfc_get_option('instgram.username');
        $api_key = sfc_get_option('instgram.api_key');

        if (empty($username) || empty($api_key)) {

            sfc_debug_add('Instagram', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('https://api.instagram.com/v1/users/self/?access_token='.$api_key);

        if (false == $request) {

            sfc_debug_add('Instagram', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['data']) && isset($response['data']['counts']) && isset($response['data']['counts']['followed_by'])) {

            return $response['data']['counts']['followed_by'];
        }

        sfc_debug_add('Instagram', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function youtube() {

        $id = sfc_get_option('youtube.id');
        $key = sfc_get_option('youtube.key');
        $type = 'forUsername'; // default username

        if (empty($id) || empty($key)) {

            sfc_debug_add('Youtube', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        if (sfc_get_option('youtube.account_type') == 'channel') {
            $type = 'id';
        } // channel id

        $request = @wp_remote_get(sprintf('https://www.googleapis.com/youtube/v3/channels?part=statistics&%s=%s&key=%s', $type, $id, $key));

        if (false == $request || is_wp_error($request)) {

            sfc_debug_add('Youtube', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (!$response || !isset($response['items']) || !isset($response['items'][0]) ||
            !isset($response['items'][0]['statistics']) || !isset($response['items'][0]['statistics']['subscriberCount'])
        ) {

            sfc_debug_add('Youtube', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
            return null;
        }

        return intval($response['items'][0]['statistics']['subscriberCount']);
    }

    public static function foursquare() {

        $id = sfc_get_option('foursquare.id');
        $api_key = sfc_get_option('foursquare.api_key');

        if (empty($id) || empty($api_key)) {
            return 0;
        }

        $request = @wp_remote_get('https://api.foursquare.com/v2/users/self?oauth_token='.$api_key.'&v='.date('Ymd'));

        if (false == $request) {

            sfc_debug_add('FourSquare', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['response']) && isset($response['response']['user']) && isset($response['response']['user']['friends']['count'])) {
            return $response['response']['user']['friends']['count'];
        }

        sfc_debug_add('FourSquare', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function forrst() {

        $id = sfc_get_option('forrst.id');

        if (empty($id)) {

            sfc_debug_add('Forrst', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('http://forrst.com/api/v2/users/info?username='.$id);

        if (false == $request) {

            sfc_debug_add('Forrst', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return null;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['resp']) && isset($response['resp']['followers'])) {

            return $response['resp']['followers'];
        }

        sfc_debug_add('Forrst', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function mailchimp() {

        $id = sfc_get_option('mailchimp.list_id');
        $api_key = sfc_get_option('mailchimp.api_key');

        if (empty($id) || empty($api_key)) {

            sfc_debug_add('Mailchimp', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        try {
            $api = new Mailchimp($api_key);
            $response = $api->lists->members($id);

            if (isset($response['total'])) {
                return $response['total'];
            }
        } catch (Mailchimp_List_DoesNotExist $e) {

            sfc_debug_add('Mailchimp', sprintf('Api Error: %s', $e->getMessage()), __('Contact support team', 'sfcounter'));
            return null;
        }
    }

    public static function vk() {

        $id = sfc_get_option('vk.id');
        $account_type = sfc_get_option('vk.account_type', 'user');

        if ( empty($id) ) {

            sfc_debug_add('VK', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        if ( $account_type == 'group' ) {

            $request = @wp_remote_get(sprintf('https://api.vk.com/method/groups.getById?group_id=%s&fields=members_count', $id));
        } else {

            $request = @wp_remote_post('https://api.vk.com/method/users.getFollowers', array('body' => array('count' => '0', 'user_id' => $id)));
        }

        if (false == $request) {

            sfc_debug_add('VK', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if ($account_type == 'group') {

            if (isset($response['response']) && isset($response['response'][0]) && isset($response['response'][0]['members_count'])) {
                return $response['response'][0]['members_count'];
            }
        } else {

            if (isset($response['response']) && isset($response['response']['count'])) {
                return $response['response']['count'];
            }
        }

        sfc_debug_add('VK', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function rss() {

        $account_type = sfc_get_option('rss.account_type', 'manual');
        $json_file = sfc_get_option('rss.json_file');
        $url = sfc_get_option('rss.link');

        if (($account_type == 'feedpress' && (empty($json_file) || empty($url)))) {

            sfc_debug_add('RSS', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        if ($account_type == 'feedpress') {
            return self::rss_feedpress();
        }

        if ($account_type == 'manual') {
            return self::rss_manual();
        }

        sfc_debug_add('RSS', 'Type not supported', __('Contact support team', 'sfcounter'));
        return null;
    }

    private static function rss_feedpress() {

        $json_file = sfc_get_option('rss.json_file');

        $request = wp_remote_get($json_file);

        if (!$request) {

            sfc_debug_add('RSS', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (is_array($response) && isset($response['subscribers'])) {

            return $response['subscribers'];
        }

        sfc_debug_add('RSS', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    private static function rss_manual() {

        return sfc_get_option('rss.count');
    }

    public static function vine() {

        $email = trim(sfc_get_option('vine.email'));
        $password = trim(sfc_get_option('vine.password'));

        if (empty($email) || empty($password)) {

            sfc_debug_add('Vine', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $v = new VineApp($email, $password);
        $user = $v->userinfo();

        if (!$user) {

            sfc_debug_add('Vine', 'Api Error: User not found', __('Contact support team', 'sfcounter'));
            return 0;
        }

        return $user['data']['followerCount'];
    }

    public static function tumblr() {

        $api_key = trim(sfc_get_option('tumblr.api_key'));
        $api_secret = trim(sfc_get_option('tumblr.api_secret'));
        $access_token = trim(sfc_get_option('tumblr.access_token'));
        $access_token_secret = trim(sfc_get_option('tumblr.access_token_secret'));

        $basename = trim(sfc_get_option('tumblr.basename'));

        if (empty($api_key) || empty($api_secret) || empty($access_token) || empty($access_token_secret) || empty($basename)) {

            sfc_debug_add('Tumblr', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $tumblr = new Tumblr($api_key, $api_secret, $access_token, $access_token_secret);
        $response = $tumblr->followers($basename);

        if (!$response || !is_object($response)) {

            sfc_debug_add('Tumblr', 'Api Error: Requets Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = json_encode($response);

        if (isset($response->response) && isset($response->response->total_users)) {
            return $response->response->total_users;
        }

        sfc_debug_add('Tumblr', sprintf('Api Error: %s', $response_body), __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function slideshare() {

        $username = trim(sfc_get_option('slideshare.username'));

        if (empty($username)) {

            sfc_debug_add('Slideshare', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('http://www.slideshare.net/'.$username.'/followers');

        if (false == $request) {

            sfc_debug_add('Slideshare', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response = @wp_remote_retrieve_body($request);

        if (!$response) {

            sfc_debug_add('Slideshare', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
            return 0;
        }

        @preg_match('/([0-9]+)( Followers| Follower)/s', $response, $matches);

        if (is_array($matches) && isset($matches[1])) {

            return $matches[1];
        }

        sfc_debug_add('Slideshare', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function c500Px() {

        $api_key = trim(sfc_get_option('500px.api_key'));
        $api_secret = trim(sfc_get_option('500px.api_secret'));
        $username = trim(sfc_get_option('500px.username'));

        if (empty($api_key) || empty($api_secret) || empty($username)) {

            sfc_debug_add('500px', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = @wp_remote_get('https://api.500px.com/v1/users/search?term='.$username.'&consumer_key='.$api_key);

        if (false == $request) {

            sfc_debug_add('500px', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (!is_array($response) || !isset($response['total_items']) || $response['total_items'] == 0) {
            return 0;
        }

        foreach ($response['users'] as $user) {
            if ($user['username'] == $username) {
                return $user['followers_count'];
            }
        }

        sfc_debug_add('500px', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function flickr() {

        return sfc_get_option('flickr.count');
    }

    public static function wpposts() {

        return wp_count_posts()->publish;
    }

    public static function wpcomments() {

        return wp_count_comments()->approved;
    }

    public static function wpusers() {

        $result = count_users();
        return $result['total_users'];
    }

    public static function audioboo() {

        $id = sfc_get_option('audioboo.id');

        if (empty($id)) {

            sfc_debug_add('AudioBoo', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = wp_remote_get('http://api.audioboo.fm/users/'.$id.'/followers');

        if (false == $request) {

            sfc_debug_add('AudioBoo', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (isset($response['body']) && isset($response['body']['totals'])) {
            return $response['body']['totals']['count'];
        }

        sfc_debug_add('AudioBoo', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function steamcommunity() {

        $id = sfc_get_option('steamcommunity.id');

        if (empty($id)) {

            sfc_debug_add('Steam Community', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = wp_remote_get('http://steamcommunity.com/groups/'.$id);

        if (false == $request) {

            sfc_debug_add('Steam Community', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        preg_match('/<span class="count ">([0-9.,]+)<\/span>/s', wp_remote_retrieve_body($request), $matches);

        if (is_array($matches) && count($matches) > 0) {

            return str_replace(array('.', ' ', ','), '', $matches[1]);
        }

        sfc_debug_add('Steam Community', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function weheartit() {

        $id = sfc_get_option('weheartit.id');

        if (empty($id)) {

            sfc_debug_add('We Heart It', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = wp_remote_request('http://weheartit.com/'.$id.'/fans');

        if (false == $request) {

            sfc_debug_add('We Heart It', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        preg_match('/<h3>([0-9]+) (Follower|Followers)<\/h3>/s', wp_remote_retrieve_body($request), $matches);

        if (is_array($matches) && count($matches) > 0) {
            return $matches[1];
        }

        sfc_debug_add('We Heart It', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }

    public static function feedly() {

        $url = sfc_get_option('feedly.url');

        if (empty($url)) {

            sfc_debug_add('Feedly', 'Missing Setting', __('Make sure all required inputs filled with right data', 'sfcounter'));
            return 0;
        }

        $request = wp_remote_request('http://cloud.feedly.com/v3/feeds/feed'.urlencode('/'.$url));

        if (false == $request) {

            sfc_debug_add('Feedly', 'Api Error: Request Failed', __('Contact support team', 'sfcounter'));
            return 0;
        }

        $response_body = wp_remote_retrieve_body($request);
        $response = json_decode($response_body, true);

        if (is_array($response) && isset($response['subscribers'])) {

            return $response['subscribers'];
        }

        sfc_debug_add('Feedly', 'Api Error: Empty Response', __('Contact support team', 'sfcounter'));
        return null;
    }
}
