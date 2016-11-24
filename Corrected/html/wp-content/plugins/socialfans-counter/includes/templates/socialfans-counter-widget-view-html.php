<?php $show_title = SFCounter_Widget_Options::show_title(); ?>
<?php $box_width = SFCounter_Widget_Options::box_width(); // widget columns count       ?>
<?php $lazy_load = SFCounter_Widget_Options::lazy_load(); ?>
<?php $block_shadow = SFCounter_Widget_Options::block_shadow_class(); ?>
<?php $block_divider = SFCounter_Widget_Options::block_divider_class(); ?>
<?php $block_margin = SFCounter_Widget_Options::block_margin_class(); ?>
<?php $block_radius = SFCounter_Widget_Options::block_radius_class(); ?>
<?php
$data = '';

if ( $lazy_load ) {
    $data .= 'data-is_lazy="1" ';
    $data .= 'data-animate_numbers="1" ';
}

$class = $block_radius;
$class .= " " . $block_margin;
$class .= " " . $block_shadow;
$class .= " " . $block_divider;
?>
<?php if ( $show_title ) { ?>
  <?php echo $before_widget; ?>
  <?php echo $before_title; ?>
  <?php echo $title; ?>
    <?php echo $after_title; ?>
  		<div class="sf-widget-holder <?php echo $class;?>" style="<?php echo $box_width; ?>" <?php echo $data; ?>>
    <?php } else { ?>
    	<div class="sf-widget-holder <?php echo $class;?>" style="<?php echo $box_width; ?>" <?php echo $data; ?>>
    <?php } ?>
    <?php include 'socialfans-counter-view-html.php'; ?>
  <?php if ( $show_title ) { ?>
    </div><!-- End Widget Holder -->
    <?php echo $after_widget; ?>
<?php } else { ?>
  </div><!-- End Widget Holder -->
<?php } ?>
        
