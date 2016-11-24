<?php include "_panel-css.php";?>
<div class="wrap">
    <h2><?php echo __( 'Sticky Setting' , 'sfcounter' ); ?></h2>
    <br />

    <?php if ( true === $is_update ) { ?>
        <div id="message" class="updated below-h2"><p><?php echo __( 'Options Updated' , 'sfcounter' ); ?> :)</p></div>
    <?php } ?>

    <form action="" method="post" id="SocialFansSocialForm">
        <div id="poststuff">
            <div id="post-body" class="columns-2">
                <div id="post-body-content" class="">
                    <div id="stickyfans-accordion">
                        <?php foreach ( $socials as $i => $social ) { ?>
                            <?php $input_prefix = 'sscounter[' . $social . ']'; ?>
                            <div>
                                <h3>
                                    <?php echo $this->getCheckboxElement( $input_prefix . '[enabled]' , ssc_get_option( $social . '.enabled' ) ); ?>
                                    <?php echo $this->getHiddenElement( $input_prefix . '[order]' , ssc_get_option( $social . '.order' , $i ) ); ?>
                                    <?php echo ucwords( strtolower( str_replace( '_' , ' ' , $social ) ) ); ?>
                                </h3>
                            </div><!-- end-row -->
                        <?php } ?>
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
                        <h3 class="hndle"><span><?php echo __( 'Sticky Settings' , 'sfcounter' ) ?></span></h3>
                        <div class="inside">
                            <p><?php echo __( 'Lazy Load:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[setting][is_lazy]' , ssc_get_option( 'setting.is_lazy' ) ); ?></p>
                            <p><?php echo __( 'Show Numbers:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[setting][show_numbers]' , ssc_get_option( 'setting.show_numbers' ) ); ?></p>
                            <p><?php echo __( 'Show Total:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[setting][show_total]' , ssc_get_option( 'setting.show_total' ) ); ?></p>
                            <p><?php echo __( 'Block Shadow:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[setting][block_shadow]' , ssc_get_option( 'setting.block_shadow' ) ); ?></p>
                            <p><?php echo __( 'Block Divider:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[setting][block_divider]' , ssc_get_option( 'setting.block_divider' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Position:' , 'sfcounter' ) , 'sscounter[setting][position]' , array ( 'left' => __( 'Left' ) , 'top' => __( 'Top' ) , 'right' => __( 'Right' ) , 'bottom' => __( 'Bottom' ) ) , ssc_get_option( 'setting.position' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Size:' , 'sfcounter' ) , 'sscounter[setting][block_size]' , array ( 'small' => __( 'Small' ) , 'medium' => __( 'Medium' ) , 'large' => __( 'Large' ) ) , ssc_get_option( 'setting.block_size' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Radius:' , 'sfcounter' ) , 'sscounter[setting][block_radius]' , array ( 0 => __( 'None' ) , 5 => __( '5px' ) , 10 => __( '10px' ) , 15 => __( '15px' ) , 20 => __( '20px' ) ) , ssc_get_option( 'setting.block_radius' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Margin:' , 'sfcounter' ) , 'sscounter[setting][block_margin]' , array ( 0 => __( 'None' ) , 1 => __( '1px' ) , 2 => __( '2px' ) , 3 => __( '3px' ) , 4 => __( '4px' ) , 5 => __( '5px' ) ) , ssc_get_option( 'setting.block_margin' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Icon Color:' , 'sfcounter' ) , 'sscounter[setting][icon_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) ) , ssc_get_option( 'setting.icon_color' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Background Color:' , 'sfcounter' ) , 'sscounter[setting][bg_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) , 'transparent' => __( 'Transparent' ) ) , ssc_get_option( 'setting.bg_color' ) ); ?></p>
                            <hr />
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[status]', ssc_get_option( 'status' ) ); ?> <?php echo __( 'Enable Sticky' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][home]', ssc_get_option( 'enabled.home' ) ); ?> <?php echo __( 'Display Sticky in HomePage' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][archive]', ssc_get_option( 'enabled.archive' ) ); ?> <?php echo __( 'Display Sticky in Archive' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][post]', ssc_get_option( 'enabled.post' ) ); ?> <?php echo __( 'Display Sticky in Single Post' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][page]', ssc_get_option( 'enabled.page' ) ); ?> <?php echo __( 'Display Sticky in Single Page' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][search]', ssc_get_option( 'enabled.search' ) ); ?> <?php echo __( 'Display Sticky in Search' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][category]', ssc_get_option( 'enabled.category' ) ); ?><?php echo __( 'Display Sticky in Category' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][404]', ssc_get_option( 'enabled.404' ) ); ?> <?php echo __( 'Display Sticky in 404' , 'sfcounter' ); ?></label></p>
                            <p><label><?php echo $this->getCheckboxElement( 'sscounter[enabled][author]', ssc_get_option( 'enabled.author' ) ); ?> <?php echo __( 'Display Sticky in Author' , 'sfcounter' ); ?></label></p>
                        </div><!-- End inside -->
                    </div><!-- End postbox -->
                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Sticky Shortcode Generator' , 'sfcounter' ) ?></span></h3>
                        <div class="inside sticky-shortcode-elements">
                            <p><?php echo __( 'Lazy Load:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[shortcode][is_lazy]' , ssc_get_option( 'shortcode.is_lazy' ) ); ?></p>
                            <p><?php echo __( 'Show Numbers:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[shortcode][show_numbers]' , ssc_get_option( 'shortcode.show_numbers' ) ); ?></p>
                            <p><?php echo __( 'Show Total:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[shortcode][show_total]' , ssc_get_option( 'shortcode.show_total' ) ); ?></p>
                            <p><?php echo __( 'Block Shadow:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[shortcode][block_shadow]' , ssc_get_option( 'shortcode.block_shadow' ) ); ?></p>
                            <p><?php echo __( 'Block Divider:' , 'sfcounter' ); ?> <?php echo $this->getCheckboxElement( 'sscounter[shortcode][block_divider]' , ssc_get_option( 'shortcode.block_divider' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Position:' , 'sfcounter' ) , 'sscounter[shortcode][position]' , array ( 'left' => __( 'Left' ) , 'top' => __( 'Top' ) , 'right' => __( 'Right' ) , 'bottom' => __( 'Bottom' ) ) , ssc_get_option( 'shortcode.position' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Size:' , 'sfcounter' ) , 'sscounter[shortcode][block_size]' , array ( 'small' => __( 'Small' ) , 'medium' => __( 'Medium' ) , 'large' => __( 'Large' ) ) , ssc_get_option( 'shortcode.block_soze' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Radius:' , 'sfcounter' ) , 'sscounter[shortcode][block_radius]' , array ( 0 => __( 'None' ) , 5 => __( '5px' ) , 10 => __( '10px' ) , 15 => __( '15px' ) , 20 => __( '20px' ) ) , ssc_get_option( 'shortcode.block_radius' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Block Margin:' , 'sfcounter' ) , 'sscounter[shortcode][block_margin]' , array ( 0 => __( 'None' ) , 1 => __( '1px' ) , 2 => __( '2px' ) , 3 => __( '3px' ) , 4 => __( '4px' ) , 5 => __( '5px' ) ) , ssc_get_option( 'shortcode.block_margin' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Icon Color:' , 'sfcounter' ) , 'sscounter[shortcode][icon_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) ) , ssc_get_option( 'shortcode.icon_color' ) ); ?></p>
                            <p><?php echo $this->getSelectElement( __( 'Background Color:' , 'sfcounter' ) , 'sscounter[shortcode][bg_color]' , array ( 'light' => __( 'Light' ) , 'dark' => __( 'Dark' ) , 'colord' => __( 'Colord' ) , 'transparent' => __( 'Transparent' ) ) , ssc_get_option( 'shortcode.bg_color' ) ); ?></p>
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