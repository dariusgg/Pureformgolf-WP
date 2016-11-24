<?php include "_panel-css.php";?>
<div class="wrap">
  <h2><?php echo SOCIALFANS_COUNTER_TITLE . ' ' . __( 'Setting' , 'sfcounter' ); ?></h2>
  <br />

  <?php if ( true === $is_update ) { ?>
    <div id="message" class="updated below-h2"><p><?php echo __( 'Options Updated' , 'sfcounter' ); ?> :)</p></div>
  <?php } ?>

  <form action="" method="post" id="SocialFansSocialForm">
    <div id="poststuff">
      <div id="post-body" class="columns-2">
        <div id="post-body-content" class="">
          <div id="socialfans-accordion">
            <?php foreach ( $socials as $i => $social ) { ?>
              <?php $input_prefix = 'sfcounter[' . $social . ']'; ?>
              <div>
                <h3>
                  <?php echo $this->getCheckboxElement( $input_prefix . '[enabled]' , sfc_get_option( $social . '.enabled' ) ); ?>
                  <?php echo $this->getHiddenElement( $input_prefix . '[order]' , sfc_get_option( $social . '.order' , $i ) ); ?>
                  <?php echo ucwords( strtolower( str_replace( '_' , ' ' , $social ) ) ); ?></h3>
                <div>

                  <?php if ( $social == 'facebook' ) { // Facebook Options?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Facebook Page ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getSelectElement( 'Fans Type' , $input_prefix . '[account_type]' , array ( 'page' => 'Page' , 'followers' => 'Followers' ) , sfc_get_option( $social . '.account_type' , 'page' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[access_token]' , sfc_get_option( $social . '.access_token' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Followers Count' , $input_prefix . '[followers_count]' , sfc_get_option( $social . '.followers_count' ) ); ?></div>
                  <?php } // End Facebook Options?>

                  <?php if ( $social == 'twitter' ) { // Twitter Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Key' , $input_prefix . '[consumer_key]' , sfc_get_option( $social . '.consumer_key' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Secret' , $input_prefix . '[consumer_secret]' , sfc_get_option( $social . '.consumer_secret' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[access_token]' , sfc_get_option( $social . '.access_token' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token Secret' , $input_prefix . '[access_token_secret]' , sfc_get_option( $social . '.access_token_secret' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Twitter ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End Twitter Options ?>

                  <?php if ( $social == 'google' ) { // Google+ Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Google+ Page ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End Google+ Options ?>

                  <?php if ( $social == 'pinterest' ) { // pinterest Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Pinterest ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End pinterest Options ?>

                  <?php if ( $social == 'linkedin' ) { // linkedin Options ?>
                      <div class="form-field"><?php echo $this->getSelectElement( 'Account Type' , $input_prefix . '[account_type]' , array ( 'profile' => 'Profile' , 'company' => 'Company' ) , sfc_get_option( $social . '.account_type' , 'company' ) ); ?></div>
                      <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[token]' , sfc_get_option( $social . '.token' ) ); ?></div>
                      <div class="form-field"><?php echo $this->getTextElement( 'Linkedin ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                      <div class="form-field"><?php echo $this->getTextElement( 'Connections Count' , $input_prefix . '[connections_count]' , sfc_get_option( $social . '.connections_count' ) ); ?></div>
                  <?php }// End linkedin Options ?>

                  <?php if ( $social == 'github' ) { // github Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Github ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End github Options ?>

                  <?php if ( $social == 'vimeo' ) { // vimeo Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Vimeo ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getSelectElement( 'Account Type' , $input_prefix . '[account_type]' , array ( 'channel' => 'Channel' , 'user' => 'User' ) , sfc_get_option( $social . '.account_type' , 'channel' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[access_token]' , sfc_get_option( $social . '.access_token' ) ); ?></div>
                  <?php }// End vimeo Options ?>

                  <?php if ( $social == 'dribbble' ) { // dribbble Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Dribbble ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                      <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[access_token]' , sfc_get_option( $social . '.access_token' ) ); ?></div>
                  <?php }// End dribbble Options ?>

                  <?php if ( $social == 'envato' ) { // envato Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Envato ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getSelectElement( 'Site' , $input_prefix . '[site]' , $envato_sites , sfc_get_option( $social . '.site' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Ref' , $input_prefix . '[ref]' , sfc_get_option( $social . '.ref' ) ); ?></div>
                  <?php }// End envato Options ?>

                  <?php if ( $social == 'soundcloud' ) { // soundcloud Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'SoundCloud ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Api Key' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                  <?php }// End soundcloud Options ?>

                  <?php if ( $social == 'behance' ) { // behance Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Behance ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Api Key' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                  <?php }// End behance Options ?>

                  <?php if ( $social == 'foursquare' ) { // foursquare Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Foursquare ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                  <?php }// End foursquare Options ?>

                  <?php if ( $social == 'forrst' ) { // forrst Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Forrst ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End forrst Options ?>

                  <?php if ( $social == 'mailchimp' ) { // mailchimp Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Api Key' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'List ID' , $input_prefix . '[list_id]' , sfc_get_option( $social . '.list_id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'List Link' , $input_prefix . '[list_link]' , sfc_get_option( $social . '.list_link' ) ); ?></div>
                  <?php }// End mailchimp Options ?>

                  <?php if ( $social == 'delicious' ) { // delicious Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Delicious ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End delicious Options ?>

                  <?php if ( $social == 'instgram' ) { // instgram Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Instgram ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Instgram Username' , $input_prefix . '[username]' , sfc_get_option( $social . '.username' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Access Token' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                  <?php }// End instgram Options ?>

                  <?php if ( $social == 'youtube' ) { // youtube Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'API Key' , $input_prefix . '[key]' , sfc_get_option( $social . '.key' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Youtube ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getSelectElement( 'Account Type' , $input_prefix . '[account_type]' , array ( 'channel' => 'Channel' , 'user' => 'User' ) , sfc_get_option( $social . '.account_type' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Custom Channel Url' , $input_prefix . '[custom_channel_url]' , sfc_get_option( $social . '.custom_channel_url' ) ); ?></div>
                  <?php }// End youtube Options ?>

                  <?php if ( $social == 'vk' ) { // vk Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Vk ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                      <div class="form-field"><?php echo $this->getSelectElement( 'Account Type' , $input_prefix . '[account_type]' , array ( 'user' => 'User', 'group' => 'Page' ) , sfc_get_option( $social . '.account_type', 'user' ) ); ?></div>
                  <?php }// End vk Options ?>

                  <?php if ( $social == 'rss' ) { // rss Options ?>
                    <div class="form-field"><?php echo $this->getSelectElement( 'Account Type' , $input_prefix . '[account_type]' , array ( 'manual' => 'Manual' , 'feedpress' => 'Feedpress' ) , sfc_get_option( $social . '.account_type' , 'manual' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Rss Link' , $input_prefix . '[link]' , sfc_get_option( $social . '.link' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Rss Count' , $input_prefix . '[count]' , sfc_get_option( $social . '.count' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Json File' , $input_prefix . '[json_file]' , sfc_get_option( $social . '.json_file' ) ); ?></div>
                  <?php }// End rss Options ?>

                  <?php if ( $social == 'vine' ) { // vine Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Vine Username' , $input_prefix . '[username]' , sfc_get_option( $social . '.username' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Vine Email' , $input_prefix . '[email]' , sfc_get_option( $social . '.email' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Vine Password' , $input_prefix . '[password]' , sfc_get_option( $social . '.password' ) , '' , 'password' ); ?></div>
                  <?php }// End vine Options ?>

                  <?php if ( $social == 'tumblr' ) { // tumblr Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Key' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Secret' , $input_prefix . '[api_secret]' , sfc_get_option( $social . '.api_secret' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Token' , $input_prefix . '[access_token]' , sfc_get_option( $social . '.access_token' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Token Secret' , $input_prefix . '[access_token_secret]' , sfc_get_option( $social . '.access_token_secret' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Blog Basename' , $input_prefix . '[basename]' , sfc_get_option( $social . '.basename' ) ); ?></div>
                  <?php }// End tumblr Options ?>

                  <?php if ( $social == 'slideshare' ) { // slideshare Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Username' , $input_prefix . '[username]' , sfc_get_option( $social . '.username' ) ); ?></div>
                  <?php }// End slideshare Options ?>

                  <?php if ( $social == '500px' ) { // 500px Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Key' , $input_prefix . '[api_key]' , sfc_get_option( $social . '.api_key' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Consumer Secret' , $input_prefix . '[api_secret]' , sfc_get_option( $social . '.api_secret' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Username' , $input_prefix . '[username]' , sfc_get_option( $social . '.username' ) ); ?></div>
                  <?php }// End 500px Options ?>

                  <?php if ( $social == 'flickr' ) { // flickr Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Flickr ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                    <div class="form-field"><?php echo $this->getTextElement( 'Flickr Count' , $input_prefix . '[count]' , sfc_get_option( $social . '.count' ) ); ?></div>
                  <?php }// End flickr Options ?>

                  <?php if ( $social == 'audioboo' ) { // audioboo Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Audioboo ID' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End audioboo Options ?>

                  <?php if ( $social == 'steamcommunity' ) { // steamcommunity Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Group base name' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End steamcommunity Options ?>

                  <?php if ( $social == 'weheartit' ) { // weheartit Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'Username' , $input_prefix . '[id]' , sfc_get_option( $social . '.id' ) ); ?></div>
                  <?php }// End weheartit Options ?>
                    
                  <?php if ( $social == 'feedly' ) { // feedly Options ?>
                    <div class="form-field"><?php echo $this->getTextElement( 'url' , $input_prefix . '[url]' , sfc_get_option( $social . '.url' ) ); ?></div>
                  <?php }// End feedly Options ?>

                  <div class="form-field"><?php echo $this->getTextElement( __( 'Fans Text' , 'sfcounter' ) , $input_prefix . '[text]' , sfc_get_option( $social . '.text' ) ); ?></div>
                  <?php if ( !in_array( $social , array ( 'wp_posts' , 'wp_comments' , 'wp_users' ) ) ) { ?>
                    <div class="form-field"><?php echo $this->getTextElement( __( 'Hover Text' , 'sfcounter' ) , $input_prefix . '[hover_text]' , sfc_get_option( $social . '.hover_text' ) ); ?></div>
                  <?php } ?>
                  <?php if ( $social != 'rss' ) { ?>
                    <div class="form-field"><?php echo $this->getSelectElement( __( 'Cache Period' , 'sfcounter' ) , $input_prefix . '[expire]' , $cache_periods , sfc_get_option( $social . '.expire' ) ); ?></div>
                  <?php } ?>
                  <div class="sf_social_doc_row"><?php echo $this->getSocialDocs( $social ); ?></div>
                  <br class="clear" />
                </div><!-- end-options-div -->
              </div><!-- end-row -->
            <?php } ?>
            <div>
              <h3><?php echo __( 'Total' , 'sfcounter' ); ?></h3>
              <div>
                <div class="form-field"><?php echo $this->getTextElement( __( 'Fans Total Text' , 'sfcounter' ) , 'sfcounter[total][text]' , sfc_get_option( 'total.text' ) ); ?></div>
                <br class="clear" />
              </div><!-- end-options-div -->
            </div>
          </div><!-- End socialfans-accordion -->
          <br />
          <input type="submit" value="submit" class="button-primary" />
        </div><!-- End post-body-content -->
        <div id="postbox-container-1" class="postbox-container">
          <div class="postbox">
            <h3 class="hndle"><span><?php echo __( 'Useful Links' , 'sfcounter' ) ?></span></h3>
            <div class="inside">
              <ul>
                <li><strong><a href="<?php echo SOCIALFANS_COUNTER_SUPPORT_URL; ?>" style="text-decoration: none;" target="_blank"><?php echo __( 'Have a Question?' , 'sfcounter' ); ?></a></strong></li>
                <li><strong><a href="<?php echo SOCIALFANS_COUNTER_DOCS_URL; ?>" target="_blank" style="text-decoration: none;"><?php echo SOCIALFANS_COUNTER_TITLE; ?> <?php echo __( 'Documentation' , 'sfcounter' ); ?></a></strong></li>
                <li><a href="http://codecanyon.net/downloads" style="text-decoration: none;" target="_blank"><?php echo __( 'Rate please!' , 'sfcounter' ); ?></a></li>
              </ul>
            </div><!-- End inside -->
          </div><!-- End postbox -->
          <div class="postbox">
            <h3 class="hndle"><span><?php echo __( 'Settings' , 'sfcounter' ) ?></span></h3>
            <div class="inside">
              <?php unset( $cache_periods[0] ); ?>
              <p><?php echo $this->getSelectElement( __( 'Default Cache Period:' , 'sfcounter' ) , 'sfcounter[setting][expire]' , $cache_periods , sfc_get_option( 'setting.expire' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Numbers Format:' , 'sfcounter' ) , 'sfcounter[setting][format]' , $numbers_format , sfc_get_option( 'setting.format' ) ); ?></p>
            </div><!-- End inside -->
          </div><!-- End postbox -->
          <div class="postbox">
            <h3 class="hndle"><span><?php echo __( 'Shortcode Generator' , 'sfcounter' ) ?></span></h3>
            <div class="inside shortcode-elements">
              <p><?php echo $this->getTextElement( __( 'Title:' , 'sfcounter' ) , 'sfcounter[shortcode][title]' , sfc_get_option( 'shortcode.title' ), '', 'text' , '200px' ); ?></p>
              <p><?php echo __( 'Hide Title:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][hide_title]' , sfc_get_option( 'shortcode.hide_title' ) ); ?></p>
              <p><?php echo __( 'Hide Numbers:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][hide_numbers]' , sfc_get_option( 'shortcode.hide_numbers' ) ); ?></p>
              <p><?php echo __( 'Show Total:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][show_total]' , sfc_get_option( 'shortcode.show_total' ) ); ?></p>
              <p><?php echo $this->getTextElement( __( 'Box Width:' , 'sfcounter' ) , 'sfcounter[shortcode][box_width]' , sfc_get_option( 'shortcode.box_width' ), '', 'text' , '50px' ); ?>px</p>
              <p><?php echo __( 'Lazy Load:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][is_lazy]' , sfc_get_option( 'shortcode.is_lazy' ) ); ?></p>
              <p><?php echo __( 'Block Shadow:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][block_shadow]' , sfc_get_option( 'shortcode.block_shadow' ) ); ?></p>
              <p><?php echo __( 'Block Divider:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][block_divider]' , sfc_get_option( 'shortcode.block_divider' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Block Radius:' , 'sfcounter' ) , 'sfcounter[shortcode][block_radius]' , array ( 0 => __( 'None' ) , 5 => __( '5px' ) , 10 => __( '10px' ) , 15 => __( '15px' ) , 20 => __( '20px' ) ) , sfc_get_option( 'shortcode.block_radius' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Block Margin:' , 'sfcounter' ) , 'sfcounter[shortcode][block_margin]' , array ( 0 => __( 'None' ) , 1 => __( '1px' ) , 2 => __( '2px' ) , 3 => __( '3px' ) , 4 => __( '4px' ) , 5 => __( '5px' ) ) , sfc_get_option( 'shortcode.block_margin' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Columns:' , 'sfcounter' ) , 'sfcounter[shortcode][columns]' , array ( 1 => __( '1 Column' ) , 2 => __( '2 Columns' ) , 3 => __( '3 Columns' ) , 4 => __( '4 Columns' ) ) , sfc_get_option( 'shortcode.columns' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Effects:' , 'sfcounter' ) , 'sfcounter[shortcode][effects]' , array ( 'sf-no-effect' => __( 'No Effect (No Hover Text)' ) , 'sf-view-first' => __( 'Effect 1' ) , 'sf-view-two' => __( 'Effect 2' ) , 'sf-view-three' => __( 'Effect 3' ) ) , sfc_get_option( 'shortcode.effects' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Icon Color:' , 'sfcounter' ) , 'sfcounter[shortcode][icon_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) ) , sfc_get_option( 'shortcode.icon_color' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Background Color:' , 'sfcounter' ) , 'sfcounter[shortcode][bg_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) , 'transparent' => __( 'Transparent' ) ) , sfc_get_option( 'shortcode.bg_color' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Hover Text Color:' , 'sfcounter' ) , 'sfcounter[shortcode][hover_text_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) ) , sfc_get_option( 'shortcode.hover_text_color' ) ); ?></p>
              <p><?php echo $this->getSelectElement( __( 'Hover Text Background Color:' , 'sfcounter' ) , 'sfcounter[shortcode][hover_text_bg_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) ) , sfc_get_option( 'shortcode.hover_text_bg_color' ) ); ?></p>
              <p><?php echo __( 'Lastweek diffrence:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][show_diff]' , sfc_get_option( 'shortcode.show_diff' ) ); ?></p>
              <p><?php echo __( 'Show diffrence less than zero:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sfcounter[shortcode][show_diff_lt_zero]' , sfc_get_option( 'shortcode.show_diff_lt_zero' ) ); ?></p>
              <p><?php echo $this->getTextElement( __( 'Diff text color:' , 'sfcounter' ) , 'sfcounter[shortcode][diff_count_text_color]' , sfc_get_option( 'shortcode.diff_count_text_color' ), '', 'text' , '100px' , 'sf-color-picker' ); ?></p>
              <p><?php echo $this->getTextElement( __( 'Diff Background color:' , 'sfcounter' ) , 'sfcounter[shortcode][diff_count_bg_color]' , sfc_get_option( 'shortcode.diff_count_bg_color' ), '', 'text' , '100px' , 'sf-color-picker' ); ?></p>
              <textarea readonly="true" id="shortcode-result" style="width: 100%" rows="10"></textarea>
              <button class="button-primary" id="sfcounter_shortcode_copy" type="button"><?php echo __( 'Copy' , 'sfcounter' );?></button>
            </div><!-- End inside -->
          </div><!-- End postbox -->
        </div><!-- End postbox-container-1 -->
        <br class="clear" />
      </div><!-- End post-body -->
    </div><!-- End poststuff -->
  </form><!-- End Form -->
</div><!-- End Wrap -->
