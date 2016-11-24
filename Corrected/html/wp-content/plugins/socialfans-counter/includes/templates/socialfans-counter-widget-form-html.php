<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title' , 'sfcounter' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" value="<?php echo $instance['title']; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'hide_title' ); ?>"><?php echo __( 'Hide Title' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'hide_title' ); ?>" id="<?php echo $this->get_field_id( 'hide_title' ); ?>" value="1" <?php if ( 1 == $instance['hide_title'] ) { echo ' checked="checked"'; } ?> />
  <span style="font-weight: 700; font-size: 0.9em"><em></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'hide_numbers' ); ?>"><?php echo __( 'Hide Numbers' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'hide_numbers' ); ?>" id="<?php echo $this->get_field_id( 'hide_numbers' ); ?>" value="1" <?php if ( 1 == $instance['hide_numbers'] ) { echo ' checked="checked"'; } ?> />
  <span style="font-weight: 700; font-size: 0.9em"><br /><em><?php echo __( 'show all enabled socials without numbers' , 'sfcounter' ); ?></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'show_total' ); ?>"><?php echo __( 'Show Total' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'show_total' ); ?>" id="<?php echo $this->get_field_id( 'show_total' ); ?>" value="1" <?php if ( 1 == $instance['show_total'] ) { echo ' checked="checked"'; } ?> />
  <span style="font-weight: 700; font-size: 0.9em"><br /><em><?php echo __( 'Show sum social accounts fans' , 'sfcounter' ); ?></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'box_width' ); ?>"><?php echo __( 'Box Width' , 'sfcounter' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'box_width' ); ?>" id="<?php echo $this->get_field_id( 'box_width' ); ?>" value="<?php echo $instance['box_width'];?>" size="5" /> px
  <span style="font-weight: 700; font-size: 0.9em"><br /><em><?php echo __( 'force box with custom width' , 'sfcounter' ); ?></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'is_lazy' ); ?>"><?php echo __( 'Lazy Load' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'is_lazy' ); ?>" id="<?php echo $this->get_field_id( 'is_lazy' ); ?>" value="1" <?php if ( 1 == $instance['is_lazy'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'block_shadow' ); ?>"><?php echo __( 'Block Shadow' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'block_shadow' ); ?>" id="<?php echo $this->get_field_id( 'block_shadow' ); ?>" value="1" <?php if ( 1 == $instance['block_shadow'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'block_divider' ); ?>"><?php echo __( 'Block Divider' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'block_divider' ); ?>" id="<?php echo $this->get_field_id( 'block_divider' ); ?>" value="1" <?php if ( 1 == $instance['block_divider'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'block_radius' ); ?>"><?php echo __( 'Block Radius' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'block_radius' ); ?>" id="<?php echo $this->get_field_id( 'block_radius' ); ?>" class="widefat">
    <option value="0" <?php if ( $instance['block_radius'] == 0 ) { echo 'selected="selected"'; } ?>><?php echo __( 'None' , 'sfcounter' ); ?></option>
    <option value="5" <?php if ( $instance['block_radius'] == 5 ) { echo 'selected="selected"'; } ?>>5px</option>
    <option value="10" <?php if ( $instance['block_radius'] == 10 ) { echo 'selected="selected"'; } ?>>10px</option>
    <option value="15" <?php if ( $instance['block_radius'] == 15 ) { echo 'selected="selected"'; } ?>>15px</option>
    <option value="20" <?php if ( $instance['block_radius'] == 20 ) { echo 'selected="selected"'; } ?>>20px</option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'block_margin' ); ?>"><?php echo __( 'Block Margin' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'block_margin' ); ?>" id="<?php echo $this->get_field_id( 'block_margin' ); ?>" class="widefat">
    <option value="0" <?php if ( $instance['block_margin'] == 0 ) { echo 'selected="selected"'; } ?>><?php echo __( 'None' , 'sfcounter' ); ?></option>
    <option value="1" <?php if ( $instance['block_margin'] == 1 ) { echo 'selected="selected"'; } ?>>1px</option>
    <option value="2" <?php if ( $instance['block_margin'] == 2 ) { echo 'selected="selected"'; } ?>>2px</option>
    <option value="3" <?php if ( $instance['block_margin'] == 3 ) { echo 'selected="selected"'; } ?>>3px</option>
    <option value="4" <?php if ( $instance['block_margin'] == 4 ) { echo 'selected="selected"'; } ?>>4px</option>
    <option value="5" <?php if ( $instance['block_margin'] == 5 ) { echo 'selected="selected"'; } ?>>5px</option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php echo __( 'Columns' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'columns' ); ?>" id="<?php echo $this->get_field_id( 'columns' ); ?>" class="widefat">
    <option value="1" <?php if ( $instance['columns'] == 1 ) { echo 'selected="selected"'; } ?>>1 <?php echo __( 'Column' , 'sfcounter' ); ?></option>
    <option value="2" <?php if ( $instance['columns'] == 2 ) { echo 'selected="selected"'; } ?>>2 <?php echo __( 'Columns' , 'sfcounter' ); ?></option>
    <option value="3" <?php if ( $instance['columns'] == 3 ) { echo 'selected="selected"'; } ?>>3 <?php echo __( 'Columns' , 'sfcounter' ); ?></option>
    <option value="4" <?php if ( $instance['columns'] == 4 ) { echo 'selected="selected"'; } ?>>4 <?php echo __( 'Columns' , 'sfcounter' ); ?></option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'effects' ); ?>"><?php echo __( 'Effects' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'effects' ); ?>" id="<?php echo $this->get_field_id( 'effects' ); ?>" class="widefat">
    <option value="sf-no-effect" <?php if ( $instance['effects'] == 'sf-no-effect' ) { echo 'selected="selected"'; } ?>><?php echo __( 'No Effect (No Hover Text)' , 'sfcounter' ); ?></option>
    <option value="sf-view-first" <?php if ( $instance['effects'] == 'sf-view-first' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Effect' , 'sfcounter' ); ?> 1</option>
    <option value="sf-view-two" <?php if ( $instance['effects'] == 'sf-view-two' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Effect' , 'sfcounter' ); ?> 2</option>
    <option value="sf-view-three" <?php if ( $instance['effects'] == 'sf-view-three' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Effect' , 'sfcounter' ); ?> 3</option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'icon_color' ); ?>"><?php echo __( 'Icon / Text Color' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'icon_color' ); ?>" id="<?php echo $this->get_field_id( 'icon_color' ); ?>" class="widefat">
    <option value="light" <?php if ( $instance['icon_color'] == 'light' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Light' , 'sfcounter' ); ?></option>
    <option value="dark" <?php if ( $instance['icon_color'] == 'dark' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Dark' , 'sfcounter' ); ?></option>
    <option value="colord" <?php if ( $instance['icon_color'] == 'colord' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Colord' , 'sfcounter' ); ?></option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'bg_color' ); ?>"><?php echo __( 'Background Color' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'bg_color' ); ?>" id="<?php echo $this->get_field_id( 'bg_color' ); ?>" class="widefat">
    <option value="light" <?php if ( $instance['bg_color'] == 'light' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Light' , 'sfcounter' ); ?></option>
    <option value="dark" <?php if ( $instance['bg_color'] == 'dark' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Dark' , 'sfcounter' ); ?></option>
    <option value="colord" <?php if ( $instance['bg_color'] == 'colord' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Colord' , 'sfcounter' ); ?></option>
    <option value="transparent" <?php if ( $instance['bg_color'] == 'transparent' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Transparent' , 'sfcounter' ); ?></option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'hover_text_color' ); ?>"><?php echo __( 'Hover Text Color' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'hover_text_color' ); ?>" id="<?php echo $this->get_field_id( 'hover_text_color' ); ?>" class="widefat">
    <option value="light" <?php if ( $instance['hover_text_color'] == 'light' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Light' , 'sfcounter' ); ?></option>
    <option value="dark" <?php if ( $instance['hover_text_color'] == 'dark' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Dark' , 'sfcounter' ); ?></option>
    <option value="colord" <?php if ( $instance['hover_text_color'] == 'colord' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Colord' , 'sfcounter' ); ?></option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'hover_text_bg_color' ); ?>"><?php echo __( 'Hover Text Background Color' , 'sfcounter' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'hover_text_bg_color' ); ?>" id="<?php echo $this->get_field_id( 'hover_text_bg_color' ); ?>" class="widefat">
    <option value="light" <?php if ( $instance['hover_text_bg_color'] == 'light' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Light' , 'sfcounter' ); ?></option>
    <option value="dark" <?php if ( $instance['hover_text_bg_color'] == 'dark' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Dark' , 'sfcounter' ); ?></option>
    <option value="colord" <?php if ( $instance['hover_text_bg_color'] == 'colord' ) { echo 'selected="selected"'; } ?>><?php echo __( 'Colord' , 'sfcounter' ); ?></option>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'show_diff' ); ?>"><?php echo __( 'Lastweek diffrence' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'show_diff' ); ?>" id="<?php echo $this->get_field_id( 'show_diff' ); ?>" value="1" <?php if ( 1 == $instance['show_diff'] ) { echo ' checked="checked"'; } ?> />
  <span style="font-weight: 700; font-size: 0.9em"><br /><em><?php echo __( 'show last week diffrence count' , 'sfcounter' ); ?></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'show_diff_lt_zero' ); ?>"><?php echo __( 'Show diffrence less than zero' , 'sfcounter' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'show_diff_lt_zero' ); ?>" id="<?php echo $this->get_field_id( 'show_diff_lt_zero' ); ?>" value="1" <?php if ( 1 == $instance['show_diff_lt_zero'] ) { echo ' checked="checked"'; } ?> />
  <span style="font-weight: 700; font-size: 0.9em"><br /><em><?php echo __( 'show last week diffrence count if less than zero' , 'sfcounter' ); ?></em></span>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'diff_count_text_color' ); ?>"><?php echo __( 'Diff text color' , 'sfcounter' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'diff_count_text_color' ); ?>" id="<?php echo $this->get_field_id( 'diff_count_text_color' ); ?>" class="widefat sf-color-picker" value="<?php echo $instance['diff_count_text_color']; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'diff_count_bg_color' ); ?>"><?php echo __( 'Diff Background color' , 'sfcounter' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'diff_count_bg_color' ); ?>" id="<?php echo $this->get_field_id( 'diff_count_bg_color' ); ?>" class="widefat sf-color-picker" value="<?php echo $instance['diff_count_bg_color']; ?>" />
</p>
