<?php $is_lazy = SSCounter_Widget_Options::is_lazy(); //   ?>
<?php $show_numbers = SSCounter_Widget_Options::show_numbers(); //   ?>
<?php $size_class = SSCounter_Widget_Options::block_size_class(); //   ?>
<?php $css_text_color_class = SSCounter_Widget_Options::css_text_color_class(); // ?>

<?php $show_total = SSCounter_Widget_Options::show_total(); //   ?>
<?php $fans_total_text = SFCounter_Widget_Options::fans_text( 'total' ); //    ?>
<?php $total_css_bg_class = SSCounter_Widget_Options::css_bg_class( 'total' ); // social background color css class   ?>
<?php $total_css_text_color_class = SSCounter_Widget_Options::css_text_color_class(); //   ?>
<?php $fans_count = (($is_lazy) ? '...' : 0); ?>

<?php foreach ( SFCounter_Widget_Options::enabledStickySocials() as $social ) { ?>

    <?php $css_bg_class         = SSCounter_Widget_Options::css_bg_class( $social ); ?>
    <?php $css_icon_color_class = SSCounter_Widget_Options::css_icon_color_class( $social ); ?>
    <?php $css_icon_image_class = SSCounter_Widget_Options::css_icon_image_class( $social ); ?>
    <?php $css_sp_class         = SSCounter_Widget_Options::css_sp_class( $social ); // ?>
    <?php $fans_text            = SFCounter_Widget_Options::fans_text( $social ); // ?>
    <?php $social_url           = SFCounter_Widget_Options::social_url( $social ); ?>
    <?php
        if( $show_numbers && !$is_lazy ){
            $fans_count = SFCounter_Widget_Options::fans_count( $social );
        } ?>
    <li>
        <div class="sf-block sf-view <?php echo $size_class;?>" data-social="<?php echo $social;?>">
            <div class="sf-front <?php echo $css_bg_class;?>">
                <a class="<?php echo $css_text_color_class; ?>"  href="<?php echo $social_url; ?>" target="_blank" rel="nofollow">
                    <i class="<?php echo $css_icon_image_class; ?> <?php echo $css_icon_color_class; ?>"></i>
                    <?php if ( $show_numbers ) { // show numbers      ?>
                        <div class="sf-spe <?php echo $css_sp_class; ?>"></div>
                        <span class="sf-social-count <?php echo $css_text_color_class; ?>"><?php echo $fans_count; ?></span>
                        <div class="clearfix"></div>
                        <small class="<?php echo $css_text_color_class; ?>"><?php _e( $fans_text , 'sfcounter' ); ?></small>
                    <?php } // end show numbers    ?>
                </a>
            </div>
        </div>
    </li>
<?php } ?>

  <?php if ( count( SFCounter_Widget_Options::enabledStickySocials() ) > 0 && $show_total && $show_numbers ) { ?>

    <?php $fans_total = ( ($is_lazy) ? '...' : SFCounter_Widget_Options::total_fans() ); ?>

    <li>
        <div class="sf-block sf-view <?php echo $size_class;?>" data-social="total">
            <div class="sf-front <?php echo $total_css_bg_class;?>">
                <div class="sf-love <?php echo $total_css_text_color_class; ?>">
                    <i class="-sf-icon-heart"></i>
                    <div class="sf-spe "></div>
                    <span class="sf-social-count <?php echo $total_css_text_color_class; ?>"><?php echo $fans_total; ?></span>
                    <div class="clearfix"></div>
                    <small class="<?php echo $total_css_text_color_class; ?>"><?php _e( $fans_total_text , 'sfcounter' ); ?></small>
                </div>
            </div>
        </div>
    </li>
<?php } ?>