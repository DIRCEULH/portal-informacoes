=== WP Accordion with Categories ===
Contributors: wponlinesupport, anoopranawat 
Tags:  Accordion, accordions, accordions with categories, accordions jquery, wordpress accordions , accordions short-code, accordions , jQuery accordions, Responsive accordions, shortcodes
Requires at least: 3.1
Tested up to: 4.7
Author URI: http://wponlinesupport.com
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A quick, easy way to add an responsive Accordions grid for WordPress. By this plugin you can display unlimited accordion with category at same page via short-code.

== Description ==
By this plugin you can display unlimited accordion with category at same page via short-code. You can change accordion background color, font color & font-size by using the shortcode parameters of this plugin.

View [DEMO](http://demo.wponlinesupport.com/accordion-demo/) for more details


= Complete Shrtcode is =
<code>[accordion  limit="10"  category="category_ID"  grid="2"
category_name="sports"  single_open="false"
transition_speed="300" background_color="#000"
font_color="#fff" heading_font_size="20"]</code>

= Shortcode Parameters are =

* **limit** : [accordion limit="10"] (ie Limit the number items to be display. By default value is limit="-1" ie all)
* **category** : [accordion category="category_ID"] (ie Display by category)
* **category_name** : [accordion category_name="category name"] (ie Display category name)
* **grid** : [accordion grid="2"] (ie Display in grid view.)
* **single_open** : [accordion single_open="true"] (ie Display One item when click to open. By default value is "true". Values are "true" and "false")
* **transition_speed** : [accordion transition_speed="300"] (ie transition speed when user click to open item )
* **background_color** : [accordion background_color="#000"] (ie Set background color of item )
* **font_color** : [accordion font_color="#fff"] (ie Set font color of  item )
* **heading_font_size** : [accordion heading_font_size="20"] (ie Set font size for heading)


Here is the example :
<code>
[accordion category="category_ID" category_name="News"]
[accordion category="category_ID" category_name="Sports"]
</code>

To use this WP Accordion with Categories  plugin just create a new page and add this short code 
<code>[accordion]</code> 
OR
If you want to display Accordion by category then use this short code 
<code>[accordion category="category_ID"]</code> 
Where catergory id you can find under "Accordion--> Accordion category"


= Features include: =
* WP accordion with category <code>[accordion category="category_ID"]</code>
* Just create a page and add short code <code>[accordion]</code>
* Fully responsive and mobile ready.
* Create different theme with shortcode parameter on the same page.
* Custom font color and size for accordions header.
* WP Title Editor for Accordion Content.
* Accordion with animation
* Added parameters
* Add thumb image
* Easy to configure accordion page
* Smooth Accordion effect
* CSS and JS file for custmization
* Search Engine Friendly URLs

== Installation ==

1. Upload the 'wp-accordion-with-categories' folder to the '/wp-content/plugins/' directory.
2. Activate the wp-accordion-with-categories list plugin through the 'Plugins' menu in WordPress.
3. Add a new page and add this short code <code>[accordion]</code>


== Screenshots ==

1. All View
2. category shortcode
3. Designs


== Changelog ==

= 1.1 =
* Fixed some css issues.

= 1.0.1 =
* Fixed some css issues.
* Resolved some notices.
* Resolved css and js issues with multiple accordion shortcode.

= 1.0 =
* Initial release Pro v


== Upgrade Notice ==

= 1.1 =
* Fixed some css issues.

= 1.0.1 =
* Fixed some css issues.
* Resolved some notices.
* Resolved css and js issues with multiple accordion shortcode.

= 1.0 =
* Initial release Pro v