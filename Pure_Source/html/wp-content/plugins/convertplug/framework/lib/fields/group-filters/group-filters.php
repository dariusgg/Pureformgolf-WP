<?php
// Add new input type "group_filters"
if ( function_exists('smile_add_input_type'))
{
	smile_add_input_type('group_filters' , 'group_filters_settings_field' );
}

/**
* Function to handle new input type "group_filters"
*
* @param $settings		- settings provided when using the input type "group_filters"
* @param $value			- holds the default / updated value
* @return string/html 	- html output generated by the function
*/
function group_filters_settings_field($name, $settings, $value)
{
	$input_name = $name;
	$type = isset($settings['type']) ? $settings['type'] : '';
	$class = isset($settings['class']) ? $settings['class'] : '';
	ob_start();
	?>
<select name="<?php echo esc_attr( $input_name ); ?>" id="smile_<?php echo esc_attr( $input_name ); ?>" class="select2-group_filters-dropdown form-control smile-input <?php echo esc_attr( 'smile-'.$type.' '.$input_name.' '.$type.' '.$class ); ?>" multiple="multiple" style="width:260px;">
	<optgroup label="<?php echo esc_attr( __( 'Pages' ) ); ?>">
	<?php
    $pages = get_pages();
	$val_arr = explode( ",", $value );
    foreach ( $pages as $page ) {
		$selected = ( in_array( "post-".$page->ID, $val_arr) ) ? 'selected="selected"' : '';
		$option = '<option value="post-' . $page->ID . '" ' . $selected . '>';
		$option .= $page->post_title;
		$option .= '</option>';
		echo $option;
    }
    ?>
    </optgroup>
	<optgroup label="<?php echo esc_attr( __( 'Posts' ) ); ?>">
    <?php
	$args = array( 'posts_per_page' => -1 );
	$myposts = get_posts( $args );
    foreach ( $myposts as $post ) {
		$selected = ( in_array( "post-".$post->ID, $val_arr) ) ? 'selected="selected"' : '';
		$option = '<option value="post-' . $post->ID . '" ' . $selected . '>';
		$option .= $post->post_title;
		$option .= '</option>';
		echo $option;
    }
	?>
    </optgroup>
    <?php
	$args = array(
	   'public'   => true,
	   '_builtin' => false
	);

	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types( $args, $output, $operator );

    foreach ( $post_types as $post_type ) {
        $type = $post_type;
        $args = array(
          'post_type' => $type,
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'ignore_sticky_posts'=> 1
        );

        $cp_query = null;
        $cp_query = new WP_Query( $args );
        if( $cp_query->have_posts() ) { ?>
			<optgroup label="<?php echo ucwords( $post_type ); ?>">
			<?php
            while ($cp_query->have_posts()) : $cp_query->the_post(); ?>
				<?php
                global $post;
                $val_arr = explode( ",", $value );
                $selected = ( in_array( "post-".$post->ID, $val_arr ) ) ? 'selected="selected"' : '';
                ?>
                <option <?php echo $selected; ?> value="post-<?php echo $post->ID; ?>"><?php the_title(); ?></option>
                <?php
            endwhile;
			?>
            </optgroup>
            <?php
        }
        wp_reset_query();  // Restore global post data stomped by the_post()
	}
	$args = array(
	   'public'   => true,
	   '_builtin' => false
	);

	$output = 'objects'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );

    foreach ( $taxonomies as $taxonomy ) {
        $terms = get_terms( $taxonomy->name, array(
			'orderby'    => 'count',
			'hide_empty' => 0,
		 ) );

		if( !empty( $terms ) ){
			?>
			<optgroup label="<?php echo ucwords( $taxonomy->label ); ?>">
			<?php
			foreach( $terms as $term ) { ?>
			<?php
				$val_arr = explode( ",", $value );
				$selected = ( in_array( "tax-".$term->term_id, $val_arr) ) ? 'selected="selected"' : '';
			?>
				<option <?php echo $selected; ?> value="tax-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
			<?php
			}
			?>
            </optgroup>
            <?php

		}
	}
	$args = array(
	   'public'   => true,
	   '_builtin' => true
	);

	$output = 'objects'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );

    foreach ( $taxonomies as $taxonomy ) {
        $terms = get_terms( $taxonomy->name, array(
			'orderby'    => 'count',
			'hide_empty' => 0,
		 ) );

		 if( !empty( $terms ) ){
			?>
			<optgroup label="<?php echo ucwords( $taxonomy->label ); ?>">
			<?php
			foreach( $terms as $term ) { ?>
			<?php
				$val_arr = explode( ",", $value );
				$selected = ( in_array( "tax-".$term->term_id, $val_arr) ) ? 'selected="selected"' : '';
			?>
				<option <?php echo $selected; ?> value="tax-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
			<?php
			}
			?>
			</optgroup>
			<?php
		}
	}

	// Special Pages
	$spacial_pages = array(
	'blog' 			=> 'Blog / Posts Page',
	'front_page' 	=> 'Front Page',
	'archive' 		=> 'Archive Page',
	'author' 		=> 'Author Page',
	'search' 		=> 'Search Page',
	'404' 			=> '404 Page',
	);
	?>
	<optgroup label="<?php echo __("Special Pages", "smile" ); ?>">
	<?php
	foreach ( $spacial_pages as $page => $title ) {
		$val_arr = explode( ",", $value );
		$selected = ( in_array( "special-".$page, $val_arr) ) ? 'selected="selected"' : '';
		?>
		<option <?php echo $selected; ?> value="special-<?php echo $page; ?>"><?php echo $title; ?></option>
		<?php
	}
	?>
	</optgroup>
</select>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('select.select2-group_filters-dropdown').select2({
		 placeholder: "Select pages / post / categories",
	});
});
</script>
    <?php
	return ob_get_clean();
}