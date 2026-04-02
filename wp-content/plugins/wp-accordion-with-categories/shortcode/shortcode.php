<?php /* 
 * Add [accordion limit="-1"] shortcode
 *
 */
function wpawc_accordion_shortcode_pro( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"limit" => '',
		"category" => '',		
		"grid"     => '',
		"category_name" => '',
		"single_open"   => '',
		"transition_speed" => '',
		"background_color" => '',
		"font_color" => '',		
		"heading_font_size" => '', 
	), $atts));

	// Some defaults
	$customclass = '';
	$faqgrid = '';

	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	// Define limit
	if( $category ) { 
		$cat = $category;
		$term = get_term( $cat, "accordion_cat" );
		$customclass = !empty($term) ? $term->slug : '';
	} else {
		$cat = '';
	}
	
	
	if( $background_color ) { 
		$backgroundColor = $background_color; 
	} else {
		$backgroundColor = '#f4f4f4';
	}
	
	if( $font_color ) { 
		$fontColor = $font_color; 
	} else {
		$fontColor = '#444444';
	}
	
	
	
	if( $heading_font_size ) { 
		$headingFontsize = $heading_font_size; 
	} else {
		$headingFontsize = '20';
	}
	
	if( $grid ) {		
		if($grid == '2')
		{ 
			$faqgrid = '6' ;
			} elseif($grid == '3')
			{
				$faqgrid = '4' ;		
		
			} else {
		$faqgrid = '';
		}
	}
	

	
	if( $category_name ) { 
		$faqcatname = $category_name; 
	} else {
		$faqcatname = '';
	}
	
	if( $single_open != ''  ) { 
		$faqsingleOpen = $single_open; 
	} else {
		$faqsingleOpen = 'true';
	}
	
	if( $transition_speed != '' ) { 
		$faqtransitionSpeed = $transition_speed; 
	} else {
		$faqtransitionSpeed = '300';
	}
	
	ob_start();
	$unique 		= wpawc_get_unique();
	$post_type 		= 'accordion_post';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
				 
        $args = array ( 
            'post_type'      => $post_type, 
            'orderby'        => $orderby, 
            'order'          => $order,
            'posts_per_page' => $posts_per_page
            );
	if($cat != ""){
            	$args['tax_query'] = array( array( 'taxonomy' => 'accordion_cat', 'field' => 'term_id', 'terms' => $cat) );
            }        
      $query = new WP_Query($args);

	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	// Displays Custom post info
	if( $post_count > 0) :
	if ( !empty($background_color) || !empty($border_color) || !empty($font_color) )
	{
	?>
	<style>
	.wpawc-accordion-<?php echo $unique; ?> .accordion-main{ background:<?php echo $backgroundColor; ?> ; border:1px solid <?php echo $backgroundColor; ?> ; }
	.wpawc-accordion-<?php echo $unique; ?> .accordion-title h4{color:<?php echo $fontColor ; ?>;  font-size:<?php echo $headingFontsize ; ?>px !important; }
	</style>
	<?php } ?>
	<div class="wpawc-accordion-<?php echo $unique; ?> wpawc_accordion <?php echo ($customclass != "" ? $customclass : ""); ?> wp-medium-<?php echo $faqgrid; ?> wpcolumns" data-accordion-group>	
	<?php if($faqcatname != '') { ?>
	<h3><?php echo $faqcatname; ?> </h3>	
	<?php	}
		// Loop 
		while ($query->have_posts()) : $query->the_post();
		?>			  
      <div data-accordion class="accordion-main">
        <div data-control class="accordion-title"><h4> <?php the_title(); ?></h4></div>
        <div data-content>
         <?php
                  if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { 
				  
                    the_post_thumbnail('thumbnail'); 
                  }
                  ?>
				  
          
        <div class="accordion-content"><?php the_content(); ?></div>
      
        </div>
      </div>
	  
		<?php
		$i++;
		endwhile; ?>
		</div>
<?php	endif;
	// Reset query to prevent conflicts
	wp_reset_query();
	?>
	    <script type="text/javascript">
      jQuery(document).ready(function() {
        jQuery('.wpawc-accordion-<?php echo $unique; ?> [data-accordion]').accordionfaq({
		 singleOpen: <?php echo $faqsingleOpen; ?>,
		 transitionEasing: 'ease',
          transitionSpeed: <?php echo $faqtransitionSpeed; ?>
		});

        
      });
    </script>
	<?php
	return ob_get_clean();
}

add_shortcode("accordion", "wpawc_accordion_shortcode_pro");