<?php
/*
Plugin Name: Kings Tab Slider
Plugin URI: http://wordpress.org/plugins/kings-tab-slider
Description: This will add a tab slider in your wordpress site.
Author: Saif Bin-Alam
Version: 1.0
Author URI: http://kingsitservice.com/saif
*/


function kings_tab_slider_latest_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'kings_tab_slider_latest_jquery');



function kings_tab_slider_plugin_main_files() {
    wp_enqueue_script( 'kings-tab-slider-js1', plugins_url( '/js/jquery.easing.min.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_script( 'kings-tab-slider-js2', plugins_url( '/js/jquery.touchSwipe.min.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_script( 'kings-tab-slider-js3', plugins_url( '/js/jquery.liquid-slider.min.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_style( 'kings-tab-slider-css1', plugins_url( '/css/animate.min.css', __FILE__ ));
    wp_enqueue_style( 'kings-tab-slider-css2', plugins_url( '/css/liquid-slider.css', __FILE__ ));
}

add_action('init','kings_tab_slider_plugin_main_files');



add_action( 'init', 'slider_custom_post' );
function slider_custom_post() {

	register_post_type( 'tabslider-items',
		array(
			'labels' => array(
				'name' => __( 'Tabs' ),
				'singular_name' => __( 'Tab' ),
				'add_new_item' => __( 'Add New Tab' )
			),
			'public' => true,
                            'menu_position' => 14,
			'supports' => array( 'title', 'editor', 'custom-fields'),
			'has_archive' => true,
			'rewrite' => array('slug' => 'tab-slider-item'),
		)
	);
		

}



function slider_taxonomy() {
	register_taxonomy(
		'tabslider_cat',  
		'tabslider-items',                  
		array(
			'hierarchical'          => true,
			'label'                         => 'Tab Category',  
			'query_var'             => true,
			'show_admin_column'			=> true,
			'rewrite'                       => array(
				'slug'                  => 'tab-category', 
				'with_front'    => true 
				)
			)
	);
}
add_action( 'init', 'slider_taxonomy');   


function kings_tab_slider_shortcode($atts){
	extract( shortcode_atts( array(
		'category' => '',
		'style' => '',
		'count' => '5',
		'id' => ''
	), $atts, 'projects' ) );
	
    $q = new WP_Query(
        array('posts_per_page' => $count, 'post_type' => 'tabslider-items', 'tabslider_cat' => $category)
        );		
		
		
	$list = '<div id="main-slider'.$id.'" class="liquid-slider">';
	$html = '<script>
			jQuery(document).ready(function(){
				jQuery("#main-slider'.$id.'").liquidSlider();
			  jQuery("#main-slider'.$id.'").addClass("'.$style.'");
			  jQuery("#main-slider'.$id.'").siblings(".ls-nav").addClass("'.$style.'");
			});</script>
			<style type="text/css">
				#main-slider'.$id.'.two {background:#e74c3c!important;}
				#main-slider'.$id.'.three {background:#f2f2f2!important;}
				#main-slider'.$id.'.four {background:#2ecc71!important;}
				#main-slider'.$id.'.five {background:#9b59b6!important;}
				#main-slider'.$id.'-wrapper {margin-bottom:20px;}
			</style>';
	echo $html;
	while($q->have_posts()) : $q->the_post();
		$idd = get_the_ID();
		
		$list .= '
			<div>
			  <h2 class="title">'.get_the_title().'</h2>
			  <p>'.get_the_content().'</p>
			</div>
		
		';        
	endwhile;
	$list.= '</div>';
	wp_reset_query();
	return $list;
}
add_shortcode('tab-slider', 'kings_tab_slider_shortcode');	












?>