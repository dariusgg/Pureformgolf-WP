<?php

add_action( 'widgets_init' , create_function( '' , 'return register_widget( "SocialFans_Counter_Widget" );' ) );

class SocialFans_Counter_Widget extends WP_Widget {

  public function __construct() {

    $options = array( 'description' => __( 'Show your social accounts stats' , 'sfcounter' ) );

    parent::__construct( false , __( 'SocialFans Counter: Stats' , 'sfcounter' ) , $options );

  }

  public function form( $instance ) {

    $defaults = array(
        'title' => 'Social Stats' ,
        'hide_numbers' => 0 ,
        'hide_title' => 1 ,
        'show_total' => 1 ,
        'box_width' => '' ,
        'is_lazy' => 1 ,
        'block_shadow' => 0 ,
        'block_divider' => 0 ,
        'block_margin' => 0 ,
        'block_radius' => 0 ,
        'columns' => 3 ,
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

    $instance = wp_parse_args( ( array ) $instance , $defaults );

    require SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-widget-form-html.php';

  }

  public function update( $new_instance , $old_instance ) {

    $instance = $old_instance;

    $instance['title']                 = trim( $new_instance['title'] );

    $instance['box_width']             = intval( $new_instance['box_width'] );
    $instance['columns']               = intval( $new_instance['columns'] );
    $instance['effects']               = $new_instance['effects'];

    $instance['hide_numbers']          = $new_instance['hide_numbers'];
    $instance['hide_title']            = $new_instance['hide_title'];
    $instance['show_total']            = $new_instance['show_total'];

    $instance['icon_color']            = $new_instance['icon_color'];
    $instance['bg_color']              = $new_instance['bg_color'];
    $instance['hover_text_color']      = $new_instance['hover_text_color'];
    $instance['hover_text_bg_color']   = $new_instance['hover_text_bg_color'];

    $instance['show_diff']             = $new_instance['show_diff'];
    $instance['show_diff_lt_zero']     = $new_instance['show_diff_lt_zero'];
    $instance['diff_count_text_color'] = $new_instance['diff_count_text_color'];
    $instance['diff_count_bg_color']   = $new_instance['diff_count_bg_color'];

    $instance['is_lazy']               = $new_instance['is_lazy'];
    $instance['block_shadow']          = $new_instance['block_shadow'];
    $instance['block_divider']         = $new_instance['block_divider'];
    $instance['block_margin']          = $new_instance['block_margin'];
    $instance['block_radius']          = $new_instance['block_radius'];

    return $instance;

  }

  public function widget( $args , $instance ) {

    extract( $args );

    $before_widget = $args['before_widget'];
    $before_title  = $args['before_title'];
    $after_title   = $args['after_title'];
    $after_widget  = $args['after_widget'];
    $title         = $instance['title'];
    // register current widget options
    SFCounter_Widget_Options::register_options( ( array ) $instance );
    
    include SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-widget-view-html.php';

  }

}