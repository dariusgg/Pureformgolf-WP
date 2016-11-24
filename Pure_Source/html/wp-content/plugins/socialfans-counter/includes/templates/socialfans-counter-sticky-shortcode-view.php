<?php $block_shadow = SSCounter_Widget_Options::block_shadow_class(); ?>
<?php $block_divider = SSCounter_Widget_Options::block_divider_class(); ?>
<?php $block_margin = SSCounter_Widget_Options::block_margin_class(); ?>
<?php $block_radius = SSCounter_Widget_Options::block_radius_class(); ?>
<?php $block_position = SSCounter_Widget_Options::block_position_class(); ?>
<?php

$class = "";
$class .= " " . $block_shadow;
$class .= " " . $block_divider;
$class .= " " . $block_margin;
$class .= " " . $block_radius;
$class .= " " . $block_position;
$class .= " " . ( (SSCounter_Widget_Options::is_lazy() ) ? 'ss-widget-lazy' : '');
?>
<div class="sf-floats-mode-holder <?php echo $class;?>">
    <ul>
        <?php include 'socialfans-counter-sticky-view-html.php'; ?>
    </ul>
</div><!-- End Widget Holder -->
