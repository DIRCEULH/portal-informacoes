<?php
/***
 * The function for the child theme
 */

/**
 * Tell WordPress to customize the page title.
 */
function darktt_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'darktt' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'darktt_wp_title', 10, 2 );

/**
 * Tell WordPress to add our scripts to the head.
 */
function darktt_theme_scripts() {
	wp_enqueue_style('darktt-style', get_stylesheet_uri(), false, 0.1);

	wp_enqueue_script( 'darktt-menu', get_stylesheet_directory_uri() . '/js/menu.js', array('jquery'), false, 0.1);
}
add_action('wp_enqueue_scripts', 'darktt_theme_scripts');