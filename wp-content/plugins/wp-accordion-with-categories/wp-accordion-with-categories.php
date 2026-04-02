<?php
/*
Plugin Name: WP Accordion with Categories
Plugin URL: http://wponlinesupport.com
Description: A quick, easy way to add an responsive Accordions grid for WordPress. By this plugin you can display unlimited accordion with category at same page via short-code.
Version: 1.1
Author: WP Online Support
Author URI: http://wponlinesupport.com
Contributors: WP Online Support
*/

if( !defined( 'WPAWC_VERSION' ) ) {
    define( 'WPAWC_VERSION', '1.1' ); // Version of plugin
}

function wpawc_accordion_setup_post_types() {
	$wpawc_accordion_labels =  apply_filters( 'wpawc_accordion_labels', array(
		'name'                => 'Accordions',
		'singular_name'       => 'Accordions',
		'add_new'             => __('Add New', 'accordion_post'),
		'add_new_item'        => __('Add New Accordion', 'accordion_post'),
		'edit_item'           => __('Edit Accordions', 'accordion_post'),
		'new_item'            => __('New Accordion', 'accordion_post'),
		'all_items'           => __('All Accordions', 'accordion_post'),
		'view_item'           => __('View Accordions', 'accordion_post'),
		'search_items'        => __('Search Accordions', 'accordion_post'),
		'not_found'           => __('No Accordions found', 'accordion_post'),
		'not_found_in_trash'  => __('No Accordions found in Trash', 'accordion_post'),
		'parent_item_colon'   => '',
		'menu_name'           => __('Accordions', 'accordion_post'),
		'exclude_from_search' => true
	) );
	$wpawc_accordion_args = array(
		'labels' 			=> $wpawc_accordion_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'query_var' 		=> true,
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'hierarchical' 		=> false,
		'menu_icon'   => 'dashicons-list-view',
		'supports' => array('title','editor','thumbnail','excerpt'),
		'taxonomies' => array('post_tag')
	);
	register_post_type( 'accordion_post', apply_filters( 'wpawc_accordion_post_type_args', $wpawc_accordion_args ) );

}
add_action('init', 'wpawc_accordion_setup_post_types');

/* Register Taxonomy */
add_action( 'init', 'wpawc_accordion_taxonomies');
function wpawc_accordion_taxonomies() {
    $labels = array(
        'name'              => _x( 'Category', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Category' ),
        'all_items'         => __( 'All Category' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Accordion Category' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'faq_cat' ),
    );

    register_taxonomy( 'accordion_cat', array( 'accordion_post' ), $args );
}


require_once( 'shortcode/shortcode.php' );

add_action( 'wp_enqueue_scripts','style_css_script_wpawc_accordion' );
function style_css_script_wpawc_accordion() {
    wp_enqueue_style( 'wpawcaccordioncss',  plugin_dir_url( __FILE__ ). 'shortcode/css/jquery.accordion.css', array(), WPAWC_VERSION );
    wp_enqueue_script( 'wpawcaccordionjs', plugin_dir_url( __FILE__ ) . 'shortcode/js/jquery.accordion.js', array( 'jquery' ), WPAWC_VERSION );	
}

// Manage Category Shortcode Columns
add_filter("manage_accordion_cat_custom_column", 'wpawc_accordion_cat_columns', 10, 3);
add_filter("manage_edit-accordion_cat_columns", 'wpawc_accordion_cat_manage_columns'); 
 
function wpawc_accordion_cat_manage_columns($theme_columns) {
    $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name'),
            'accordion_category_shortcode' => __( 'Accordion Category Shortcode', 'accordion_post' ),
            'slug' => __('Slug'),
            'posts' => __('Posts')
        );
    return $new_columns;

}

function wpawc_accordion_cat_columns($out, $column_name, $theme_id) {
    $theme = get_term($theme_id, 'faq_cat');
    switch ($column_name) {
        
        case 'title':
            echo get_the_title();
        break;

        case 'accordion_category_shortcode':             
             echo '[accordion category="' . $theme_id. '"]';
        break;
 
        default:
            break;
    }
    return $out;    
}

/**
 * Function to get unique value number
 * 
 * @package WP Accordion with Categories
 * @since 1.0.1
 */
function wpawc_get_unique() {
  static $unique = 0;
  $unique++;

  return $unique;
}