<?php $widget_columns = SFCounter_Widget_Options::widget_columns(); // widget columns count   ?>
<?php $column_class = SFCounter_Widget_Options::column_class(); // widget columns css class   ?>
<?php $show_numbers = SFCounter_Widget_Options::show_numbers(); //   ?>
<?php $show_total = SFCounter_Widget_Options::show_total(); //   ?>
<?php $show_diff = SFCounter_Widget_Options::show_diff(); //   ?>
<?php $show_diff_lt_zero = SFCounter_Widget_Options::show_diff_lt_zero(); //   ?>
<?php $fans_total_text = SFCounter_Widget_Options::fans_text( 'total' ); //    ?>
<?php $fans_total = 0; //    ?>
<?php $total_css_bg_class = SFCounter_Widget_Options::css_bg_class( 'total' ); // social background color css class   ?>
<?php $total_css_text_color_class = SFCounter_Widget_Options::css_text_color_class( 'total' ); //   ?>
<?php $diff_count_text_color = SFCounter_Widget_Options::diff_count_text_color(); ?>
<?php $diff_count_bg_color = SFCounter_Widget_Options::diff_count_bg_color(); ?>
<?php $lazy_load = SFCounter_Widget_Options::lazy_load(); ?>
<?php
$lazy_css_class = '';
$fans_count = '...';
$diff_count = 0;
$show_diff_lt_zero = false;

if ( $lazy_load ) $lazy_css_class = "sf-widget-lazy";
?>


<div class="<?php echo $lazy_css_class; ?>" data-hide_numbers="<?php echo (!$show_numbers) ? 1 : 0; ?>" data-show_total="<?php echo ($show_total) ? 1 : 0; ?>">
  <?php foreach ( SFCounter_Widget_Options::enabled_socials() as $social ) { ?>

    <?php $effect_class = SFCounter_Widget_Options::effect_class( $social ); // current widget effect css class ?>
    <?php $css_bg_class = SFCounter_Widget_Options::css_bg_class( $social ); // social background color css class ?>
    <?php $css_text_color_class = SFCounter_Widget_Options::css_text_color_class( $social ); // ?>
    <?php $css_icon_image_class = SFCounter_Widget_Options::css_icon_image_class( $social ); // ?>
    <?php $css_icon_color_class = SFCounter_Widget_Options::css_icon_color_class( $social ); // ?>
    <?php $css_sp_class = SFCounter_Widget_Options::css_sp_class( $social ); // ?>
    <?php $css_hover_bg_color_class = SFCounter_Widget_Options::css_hover_text_bg_color_class( $social ); // ?>
    <?php $css_hover_text_color_class = SFCounter_Widget_Options::css_hover_text_color_class( $social ); // ?>
    <?php $social_url = SFCounter_Widget_Options::social_url( $social ); // ?>
    <?php $fans_text = SFCounter_Widget_Options::fans_text( $social ); // ?>
    <?php $fans_hover_text = SFCounter_Widget_Options::fans_hover_text( $social ); //  ?>
    <?php if ( $show_numbers && !$lazy_load ) { $fans_count = SFCounter_Widget_Options::fans_count( $social ); } ?>
    <?php if ( $show_numbers && $show_diff ) { $diff_count = SFCounter_Widget_Options::get_social_diff( $social ); }  ?>
  <div
	  class="sf-block sf-view <?php echo $effect_class . ' ' . $column_class; ?>"
	  data-social="<?php echo $social;?>">
        <?php if ( $widget_columns > 1 ) { // more than 1 column   ?>
          <div class="sf-front <?php echo $css_bg_class; ?>">
            <?php if ( $show_numbers && $show_diff ) { ?>
              <?php if ( ($diff_count > 0 ) || ($diff_count < 0 && $show_diff_lt_zero ) ) { ?>
                <div class="weekly-added" style="<?php echo $diff_count_text_color; ?> <?php echo $diff_count_bg_color; ?>">
                  <i><?php if ( $diff_count > 0 ) {  echo '&#9650'; } else { echo '&#9660'; } ?></i>
                  <span><?php echo $diff_count; ?></span>
                </div>
              <?php } ?>
            <?php } ?>
            <a class="<?php echo $css_text_color_class; ?>"  href="<?php if ( $effect_class == 'sf-no-effect' ) {echo $social_url;} else {echo 'javascript:void(0);';} ?>" target="_blank" rel="nofollow">
              <i class="<?php echo $css_icon_image_class; ?> <?php echo $css_icon_color_class; ?>"></i>
              <?php if ( $show_numbers ) { // show numbers      ?>
                <div class="sf-spe <?php echo $css_sp_class; ?>"></div>
                <span class="sf-social-count <?php echo $css_text_color_class; ?>"><?php echo $fans_count; ?></span>
                <div class="clearfix"></div>
                <small class="<?php echo $css_text_color_class; ?>"><?php _e( $fans_text , 'sfcounter' ); ?></small>
              <?php } // end show numbers    ?>
            </a>
          </div>
          <div class="sf-back sf-mask">
            <a href="<?php echo $social_url; ?>" class="sf-join btn btn-xs <?php echo $css_hover_bg_color_class; ?> <?php echo $css_hover_text_color_class; ?>" target="_blank" rel="nofollow"><?php _e( $fans_hover_text , 'sfcounter' ); ?></a>
          </div>
        <?php } else { ?>
          <div class="<?php echo $css_bg_class . ' ' . $effect_class; ?> sf-col-exception">
            <div class="sf-col-one-icon pull-left">
              <a class="<?php echo $css_text_color_class; ?>" href="<?php if ( $effect_class == 'sf-no-effect' ) { echo $social_url; } else { echo 'javascript:void(0);'; } ?>" target="_blank" rel="nofollow">
                <i class="<?php echo $css_icon_image_class; ?> <?php echo $css_icon_color_class; ?>"></i>
              </a>
              <?php if ( $show_numbers && $show_diff ) { ?>
                <?php if ( ($diff_count > 0 ) || ($diff_count < 0 && $show_diff_lt_zero ) ) { ?>
                  <div class="weekly-added weekly-added-onecolumn" style="<?php echo $diff_count_text_color; ?> <?php echo $diff_count_bg_color; ?>">
                    <i><?php if ( $diff_count > 0 ) { echo '&#9650'; } else { echo '&#9660'; } ?></i><span><?php echo $diff_count; ?></span>
                  </div>
                <?php } ?>
              <?php } ?>
            </div><!-- End sf-col-one-icon -->
            <div class="sf-front pull-right">
              <?php if ( $show_numbers ) { ?>
                  <a class="<?php echo $css_text_color_class; ?>" href="<?php echo $social_url; ?>" target="_blank" rel="nofollow">
                    <span class="sf-social-count <?php echo $css_text_color_class; ?>"><?php echo $fans_count; ?></span>
                    <div class="clearfix"></div>
                    <small class="<?php echo $css_text_color_class; ?>"><?php _e( $fans_text , 'sfcounter' ); ?></small>
                  </a>
              <?php } ?>
            </div><!-- End sf-front -->
            <div class="sf-back sf-mask pull-right">
              <a href="<?php echo $social_url; ?>" class="sf-join btn <?php echo $css_hover_bg_color_class; ?> <?php echo $css_hover_text_color_class; ?> btn-xs" target="_blank"><?php _e( $fans_hover_text , 'sfcounter' ); ?></a>
            </div><!-- End sf-back -->
          </div><!-- End sf-col-exception -->
        <?php } ?>
    </div><!-- End sf-block -->
<?php } ?>

  <?php if ( $show_numbers && $show_numbers ) {
    $fans_total = SFCounter_Widget_Options::total_fans();
    $css_total = SFCounter_Widget_Options::css_total_class();
  } ?>

  <?php if ( count( SFCounter_Widget_Options::enabled_socials() ) > 0 && $show_total && $show_numbers ) { ?>
    <div class="<?php echo $css_total; ?>"  data-social="total">
      <div class="sf-front <?php echo $total_css_bg_class; ?>">
        <div class="sf-love <?php echo $total_css_text_color_class; ?>">
          <i class="-sf-icon-heart"></i>
          <div class="sf-spe "></div>
          <span class="sf-social-count <?php echo $total_css_text_color_class; ?>"><?php echo ($lazy_load) ? $fans_count : $fans_total; ?></span>
          <div class="clearfix"></div>
          <small class="<?php echo $total_css_text_color_class; ?>"><?php _e( $fans_total_text , 'sfcounter' ); ?></small>
        </div>
      </div>
    </div>
<?php } ?>
    <div style="clear: both;"></div>
</div>