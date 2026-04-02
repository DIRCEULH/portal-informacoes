<?php
/**
 * Plugin Name: Alert Notice Boxes
 * Plugin URI: http://www.madadim.co.il
 * Description: Create Alert Notice Box wherever you want
 * Version: 1.2.5.2
 * Author: Yehi Co
 * Author URI: http://www.madadim.co.il
 * License: GPL2
 * Text Domain: alert-notice-boxes


Alert Notice Boxes is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Alert Notice Boxes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Alert Notice Boxes. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

define( 'ANB__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANB__PLUGIN_URL', plugin_basename(__FILE__) );

global $anb_version;
$anb_version = '1.2.5.1'; // version changed from 1.2.4 to 1.2.5

function alert_notice_boxes_install() {
    global $wpdb;
    global $anb_version;
    $table_name = $wpdb->prefix . 'alert_notice_boxes'; // do not forget about tables prefix
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = $wpdb->prepare( "CREATE TABLE " . $table_name . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		post_ID int(11) NOT NULL,
		title TEXT NULL,
		content TEXT NULL,
		display_in TEXT NULL,
		style VARCHAR(100) NULL,
		delay int(11) DEFAULT '2000',
		show_time int(11) DEFAULT '8000',
		enabled VARCHAR(100) NULL,
		PRIMARY KEY  (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;" );
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

    add_option('anb_version', $anb_version);

    $installed_ver = get_option('anb_version');
    if ($installed_ver != $anb_version) {
	$sql = $wpdb->prepare( "CREATE TABLE " . $table_name . " (
	id int(11) NOT NULL AUTO_INCREMENT,
	post_ID int(11) NOT NULL,
	title TEXT NULL,
	content TEXT NULL,
	display_in TEXT NULL,
	style VARCHAR(100) NULL,
	delay int(11) DEFAULT '2000',
	show_time int(11) DEFAULT '8000',
	enabled VARCHAR(100) NULL,
	PRIMARY KEY  (id)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;" );
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

        update_option('anb_version', $anb_version);
    }
    
}

register_activation_hook(__FILE__, 'alert_notice_boxes_install');

function anb_update_db_check() {
    global $anb_version;
    if (get_site_option('anb_version') != $anb_version) {
        alert_notice_boxes_install();
    }
}

add_action('plugins_loaded', 'anb_update_db_check');




/*-------------------class start------------------*/

class YCanb {

function __construct() {

	add_action( 'admin_init', array( $this, 'anb_capabilities' ) );
	add_action( 'init', array( $this, 'register_anb' ) );
	add_action( 'admin_menu', array($this, 'anb_admin_menu') );
	add_action( 'add_meta_boxes', array($this, 'anb_create_meta_boxes') );
	add_action( 'plugins_loaded', array($this, 'anb_load_textdomain') );
	add_action( 'save_post', array($this, 'save_post_type_values') );
	add_action( 'before_delete_post', array($this, 'delete_post_row') );
	add_action( 'wp_footer', array( $this, 'register_alert_notice_boxes' ) );
	add_action( 'wp_enqueue_scripts', array( $this, 'add_anb_scripts' ) );
	add_action( 'wp_enqueue_scripts', array( $this, 'add_anb_styles' ) );
	add_action( 'manage_pages_custom_column' , array( $this, 'anb_custom_columns' ), 10, 2 );
	add_filter( 'manage_anb_posts_columns' , array( $this, 'anb_columns' ) );
	add_action( 'add_meta_boxes', array( $this, 'add_individual_control_meta_box' ) );
	add_action( 'save_post', array( $this, 'save_individual_control_meta_box' ), 10, 3);
	// filter
	add_filter( 'post_updated_messages', array($this, 'anb_update_messages') );
	// register
	register_deactivation_hook( ANB__PLUGIN_URL, array( $this, 'anb_deactivation') );

}

function anb_capabilities() {
	
	$role = get_role( 'administrator' );
	$role->add_cap( 'delete_anbs', true );
	$role->add_cap( 'delete_others_anbs', true );
	$role->add_cap( 'delete_private_anbs', true );
	$role->add_cap( 'delete_published_anbs', true );
	$role->add_cap( 'edit_anbs', true );
	$role->add_cap( 'edit_others_anbs', true );
	$role->add_cap( 'edit_private_anbs', true );
	$role->add_cap( 'edit_published_anbs', true );
	$role->add_cap( 'publish_anbs', true );
	$role->add_cap( 'read_private_anbs', true );
}

function anb_deactivation() {

	$role = get_role( 'administrator' );
	$role->remove_cap( 'delete_anbs');
	$role->remove_cap( 'delete_others_anbs');
	$role->remove_cap( 'delete_private_anbs');
	$role->remove_cap( 'delete_published_anbs');
	$role->remove_cap( 'edit_anbs');
	$role->remove_cap( 'edit_others_anbs');
	$role->remove_cap( 'edit_private_anbs');
	$role->remove_cap( 'edit_published_anbs');
	$role->remove_cap( 'publish_anbs');
	$role->remove_cap( 'read_private_anbs');
}
    
function register_anb() {
    register_post_type( 'anb', array(
        'labels' => array(
		'name'               => __( 'Alert Notice', 'alert-notice-boxes' ),
		'singular_name'      => _x( 'Alert Notice', 'post type singular name', 'alert-notice-boxes' ),
		'menu_name'          => _x( 'Alert Notice', 'admin menu', 'alert-notice-boxes' ),
		'name_admin_bar'     => _x( 'Alert Notice', 'add new on admin bar', 'alert-notice-boxes' ),
		'add_new'            => _x( 'Add New', 'Post Type', 'alert-notice-boxes' ),
		'add_new_item'       => __( 'Add New', 'alert-notice-boxes' ),
		'new_item'           => __( 'New Alert Notice Box', 'alert-notice-boxes' ),
		'edit_item'          => __( 'Edit Alert Notice Box', 'alert-notice-boxes' ),
		'view_item'          => __( 'View Alert Notice Box', 'alert-notice-boxes' ),
		'all_items'          => __( 'Alert Notice', 'alert-notice-boxes' ),
		'search_items'       => __( 'Search', 'alert-notice-boxes' ),
		'parent_item_colon'  => __( 'Parent Alert Notice Box:', 'alert-notice-boxes' ),
		'not_found'          => __( 'No Alert Notice Box found.', 'alert-notice-boxes' ),
		'not_found_in_trash' => __( 'No Alert Notice Box found in Trash.', 'alert-notice-boxes' ),
		),
		
		// Frontend // Admin
		'supports'              => array( 'title', 'editor' ),
		'hierarchical'          => true,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => 100,
		'menu_icon'             => 'dashicons-megaphone',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'anb',
		'map_meta_cap'          => true
    ) );    
}

function anb_item_meta_box() {
      
	global $wpdb;
	$post = get_post();
	$table_name = $wpdb->prefix . 'alert_notice_boxes';
	$alert_notice_box = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_ID = %d", $post->ID) );
	
	$post_type_menu_name = $alert_notice_box->menu_name;
	$post_id = $alert_notice_box->post_ID;

 ?>
	<form id="formanb" method="POST">
	<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	    <input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(ANB__PLUGIN_URL.$post->ID); ?>" />
	    <tbody>
	    <tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="menu_icon"><?php _e('On / Off', 'alert-notice-boxes')?></label>
	            <p><?php _e('If the check box is enabled the message will be active by your chosen settings', 'alert-notice-boxes')?></p>
	        </th>
	        <td>
	        <?php
			if ( $alert_notice_box->post_ID == $post->ID ) {
				$enabled_value = $alert_notice_box->enabled;
				$check_enabled_value  = strpos($enabled_value, 'enabled');
				if ($check_enabled_value !== false) {
					?>
					<input name="enabled" type="checkbox" value="enabled" checked><label for="enabled"><?php _e('Enabled', 'alert-notice-boxes')?></label>
					<?php
				} else {
					?>
					<input name="enabled" type="checkbox" value="enabled"><label for="enabled"><?php _e('Enabled', 'alert-notice-boxes')?></label>
					<?php
                } ?>
			<?php } else { ?>
				<input name="enabled" type="checkbox" value="enabled" checked><label for="enabled"><?php _e('Enabled', 'alert-notice-boxes')?></label>
			<?php } ?>
			
	        </td>
	    </tr>
	    <tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="style"><?php _e('Style', 'alert-notice-boxes')?></label>
	            <p><?php _e('Choose the alart box style', 'alert-notice-boxes')?></p>
	        </th>
	        <td>
	        	<select name="style">
		                <?php
		                    $style = $alert_notice_box->style;
		                    $option_values = array(success, info, warning, danger);
		
		                    foreach($option_values as $key => $value) 
		                    {
		                        if($value == $style)
		                        {
		                            ?>
		                                <option selected><?php echo $value; ?></option>
		                            <?php    
		                        }
		                        else
		                        {
		                            ?>
		                                <option><?php echo $value; ?></option>
		                            <?php
		                        }
		                    }
		                ?>
            		</select>
	        </td>
	    </tr>
		<tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="delay"><?php _e('Delay', 'alert-notice-boxes')?></label>
	            <p><?php _e('The time it takes the notice to appear in milliseconds', 'alert-notice-boxes')?></p>
	        </th>
	        <td>
				<?php
				if ( $alert_notice_box->post_ID == $post->ID ) {
					$delay_value = $alert_notice_box->delay
					?>
					<input name="delay" type="text" value="<?php echo $delay_value; ?>">
					<?php
				} else {
					?>
					<input name="delay" type="text" value="2000">
					<?php
				} ?>
		    </td>
	    </tr>
		<tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="show_time"><?php _e('Show Time', 'alert-notice-boxes')?></label>
	            <p><?php _e('The duration of the notice will appear in milliseconds', 'alert-notice-boxes')?></p>
	        </th>
			<td>
				<?php
				if ( $alert_notice_box->post_ID == $post->ID ) {
					$show_time_value = $alert_notice_box->show_time
					?>
					<input name="show_time" type="text" value="<?php echo $show_time_value; ?>">
					<?php
				} else {
					?>
					<input name="show_time" type="text" value="8000">
					<?php
				} ?>
		    </td>
	    </tr>
	    <tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="display_in"><?php _e('Published in', 'alert-notice-boxes')?></label>
	            <p><?php _e('Choose where you want to display the alert', 'alert-notice-boxes')?></p>
	        </th>
	        <td>
		<?php
			$post_types = get_post_types( array( 'public' => true ) );
			$display_in_value = $alert_notice_box->display_in;
			foreach ( $post_types as $post_type ) {
				$check_post_type = strpos($display_in_value, $post_type);
				if ($check_post_type !== false) {
					?>
					<input name="<?php echo esc_attr( $post_type ); ?>" type="checkbox" id="anb_<?php echo esc_attr( $post_type ); ?>" value="<?php echo esc_attr( $post_type ); ?>" checked><label for="post_types_<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( ucfirst( $post_type ) ); ?></label><br>
					<?php
				} else {
					?>
					<input name="<?php echo esc_attr( $post_type ); ?>" type="checkbox" id="anb_<?php echo esc_attr( $post_type ); ?>" value="<?php echo esc_attr( $post_type ); ?>"><label for="post_types_<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( ucfirst( $post_type ) ); ?></label><br>
					<?php
				}
		                
		                
				
			}
		?>
	        </td>
	    </tr>
	    </tbody>
	</table>
	</form>
	
<?php

}

function save_post_type_values() {

    if( get_post_type() == 'anb' ) {
	global $wpdb;
	$post = get_post();
	$table_name = $wpdb->prefix . 'alert_notice_boxes'; // do not forget about tables prefix
	$alert_notice_box = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_ID = %d", $post->ID) );
	$post_id = $alert_notice_box->post_ID;
	$post_types = get_post_types( array( 'public' => true ) );
	
		foreach ( $post_types as $post_type ) {
			$post_type_name_atr = sanitize_text_field( $_POST[$post_type] );
			$check_post_type = strpos($post_type_name_atr, $post_type);
			if ($check_post_type !== false) {
				$alert_notice_box_post_types .= sanitize_text_field( $_POST[$post_type] . ' ,  ' );
			}
		}
	
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], ANB__PLUGIN_URL.$post->ID)) { return $post_id; }
	
	if ( $alert_notice_box->post_ID == $post->ID ) {
		$alert_notice_box_id = $alert_notice_box->id;
		$alert_notice_box_title = sanitize_text_field( $_POST['post_title'] );
		$alert_notice_box_content = $_POST['content'];
		$alert_notice_box_display_in = $alert_notice_box_post_types;
		$alert_notice_box_style = sanitize_text_field( $_POST['style'] );
		$alert_notice_box_delay = sanitize_text_field( $_POST['delay'] );
		$alert_notice_box_show_time = sanitize_text_field( $_POST['show_time'] );
		$alert_notice_box_enabled = sanitize_text_field( $_POST['enabled'] );
	
		$wpdb->prepare( $wpdb->update ( $table_name, array(
		        'title' => $alert_notice_box_title,
		        'content' => $alert_notice_box_content,
		        'display_in' => $alert_notice_box_display_in,
		        'style' => $alert_notice_box_style,
				'delay' => $alert_notice_box_delay,
				'show_time' => $alert_notice_box_show_time,
		        'enabled' => $alert_notice_box_enabled
		        
		), array('id'=>$alert_notice_box_id)));
	
	} else {
	
		$alert_notice_box_title = sanitize_text_field( $_POST['post_title'] );
		$alert_notice_box_content = $_POST['content'];
		$alert_notice_box_display_in = $alert_notice_box_post_types;
		$alert_notice_box_style = sanitize_text_field( $_POST['style'] );
		$alert_notice_box_delay = sanitize_text_field( $_POST['delay'] );
		$alert_notice_box_show_time = sanitize_text_field( $_POST['show_time'] );
		$alert_notice_box_enabled = sanitize_text_field( $_POST['enabled'] );
		$wpdb->prepare( $wpdb->insert ( $table_name, array(
		        'post_ID' => $post->ID,
		        'title' => $post->post_title,
		        'content' => $alert_notice_box_content,
		        'display_in' => $alert_notice_box_display_in,
		        'style' => $alert_notice_box_style,
				'delay' => $alert_notice_box_delay,
				'show_time' => $alert_notice_box_show_time,
		        'enabled' => $alert_notice_box_enabled
		        
		)));
	}

    }
}

function delete_post_row($delete_row){

    if( get_post_type() == 'anb' ) {

	global $wpdb;
	$post = get_post();
	$table_name = $wpdb->prefix . 'alert_notice_boxes';
	
	$delete_row = "DELETE FROM $table_name WHERE post_ID= $post->ID";
	$wpdb->query($delete_row);
    }
}

function anb_create_meta_boxes() {
    add_meta_box("alert-notice-boxes-item-meta-box", __( 'Alert Notice Box settings', 'alert-notice-boxes' ), array($this, 'anb_item_meta_box'), "anb", "normal", "core", null);
}

public function anb_admin_menu() {
    add_utility_page( __( 'Alert Notice', 'alert-notice-boxes' ), __( 'Alert Notice', 'alert-notice-boxes' ), 'edit_anbs', 'edit.php?post_type=anb', '', 'dashicons-welcome-add-page' );
    add_submenu_page( 'edit.php?post_type=anb', __( 'Add ons', 'alert-notice-boxes' ), __( 'Add ons', 'alert-notice-boxes' ), 'edit_anbs', 'edit.php?post_type=anb_add_ons' );
}


function anb_load_textdomain() {
    load_plugin_textdomain( 'alert-notice-boxes', false, plugin_basename( ANB__PLUGIN_DIR . 'languages' ) );
}

function anb_columns($columns) {
	
	unset(
		$columns['date'],
		$columns['comments']
	);
	$new_columns = array(
		'enabled' => __('Enabled', 'alert-notice-boxes'),
		'display_in' => __('Published in', 'alert-notice-boxes'),
		'delay' => __('Delay', 'alert-notice-boxes'),
		'show_time' => __('Show Time', 'alert-notice-boxes'),
	);
    return array_merge($columns, $new_columns);
}
 
function anb_custom_columns( $column, $post_id ) {
    global $wpdb;
	$post = get_post();
	$table_name = $wpdb->prefix . 'alert_notice_boxes';
	$alert_notice_box = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_ID = %d", $post->ID) );
    switch ( $column ) {

    case 'enabled' :
	$alert_notice_enabled = $alert_notice_box->enabled;
	
        echo $alert_notice_enabled;
        break;

    case 'display_in' :
	$alert_notice_display_in = $alert_notice_box->display_in;
	
        echo $alert_notice_display_in;
        break;
    
	case 'delay' :
	$alert_notice_delay = $alert_notice_box->delay;
	
        echo $alert_notice_delay;
        break;
		
	case 'show_time' :
	$alert_notice_show_time = $alert_notice_box->show_time;
	
        echo $alert_notice_show_time;
        break;
	}
}

function add_anb_scripts() {
    // Register the script like this for a plugin:
    wp_register_script( 'anb-js', plugins_url( '/js/anb.js', ANB__PLUGIN_URL ) );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'anb-js' );
}

function add_anb_styles() {
    // Register the style like this for a plugin:
    wp_register_style( 'anb-style', plugins_url( '/css/anb.css', ANB__PLUGIN_URL ), array(), '', 'all' );
 
    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'anb-style' );
}

function anb_update_messages( $messages ) {

		global $post, $post_ID;

		$messages['anb' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Alert Notice Box updated.', 'alert-notice-boxes' ),
			2 => __( 'Alert Notice Box updated.', 'alert-notice-boxes' ),
			3 => __( 'Alert Notice Box deleted.', 'alert-notice-boxes' ),
			4 => __( 'Alert Notice Box updated.', 'alert-notice-boxes' ),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __( 'Alert Notice Box restored to revision from %s', 'alert-notice-boxes' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Alert Notice Box published.', 'alert-notice-boxes' ),
			7 => __( 'Alert Notice Box saved.', 'alert-notice-boxes' ),
			8 => __( 'Alert Notice Box submitted.', 'alert-notice-boxes' ),
			9 => __( 'Alert Notice Box scheduled for.', 'alert-notice-boxes' ),
			10 => __( 'Alert Notice Box draft updated.', 'alert-notice-boxes' ),
		);

		return $messages;

}



function register_alert_notice_boxes() {
	
 	global $wpdb;
	$post = get_post();
	$table_name = $wpdb->prefix . 'alert_notice_boxes'; // do not forget about tables prefix
	$result = $wpdb->get_results( "SELECT * FROM $table_name");
	
	$get_page_id = get_the_ID();
	$post_meta_name = 'disable_all_alerts_page_' . $get_page_id;
	$meta_box_checkbox_value = $_POST[$post_meta_name];
	$get_post_meta_name = get_post_meta($get_page_id, $post_meta_name, true);

	?>
	
    <div id="anb-container">
		<div id="anb-wrapper"></div>
	</div>
	<script>
		window.onload = function() {
			
			var alert_wrapper = document.getElementById("anb-wrapper");
			
			setTimeout(alertfunc, 2000);
			function alertfunc() {

				<?php
					$i = '1';
					foreach ($result as $alert_notice_values) {
						$alert_notice_id = $alert_notice_values->id;
						$alert_notice_enabled = $alert_notice_values->enabled;
						$alert_notice_content_html = $alert_notice_values->content;
						$alert_notice_content = str_replace(array("\r", "\n"), '', $alert_notice_content_html);
						$alert_notice_display_in = $alert_notice_values->display_in;
						$alert_notice_style = $alert_notice_values->style;
						$alert_notice_delay = $alert_notice_values->delay;
						$alert_notice_show_time = $alert_notice_values->show_time;
						$post_type_name = get_post_type( get_the_ID() );
						$check_post = strpos($alert_notice_display_in, 'post');
						$check_page = strpos($alert_notice_display_in, 'page');
						$check_post_type = strpos($alert_notice_display_in, $post_type_name);
						$show_alert_notice = 'no';
						$check_post_true = 'no';
						$alert_id = $alert_notice_values->id;
						$alert_title = $alert_notice_values->title;
						$alert_post_meta_name = 'display_alert_' . $alert_id . '_page_' . $get_page_id;
						$get_alert_post_meta_name = get_post_meta($get_page_id, $alert_post_meta_name, true);
						$post_id = $alert_notice_values->post_ID;
						
						if ($check_post !== false && get_post_type( get_the_ID() ) == 'post') {
							if ($get_post_meta_name == $get_page_id) {
								$show_alert_notice = 'no';
							} else {
								$show_alert_notice = 'yes';
							}
						} else if ($check_page !== false && get_post_type( get_the_ID() ) == 'page') {
							if ($get_post_meta_name == $get_page_id) {
								$show_alert_notice = 'no';
							} else {
								$show_alert_notice = 'yes';
							}
						} else if ($check_post_type !== false) {
							if ($get_post_meta_name == $get_page_id) {
								$show_alert_notice = 'no';
							} else {
								$check_post_true = $post_type_name;
							}
						}
						if( $alert_notice_enabled == 'enabled' && get_post_status ( $post_id ) == 'publish' ) {
							if( $show_alert_notice == 'yes' ||  get_post_type( get_the_ID() ) == $check_post_true || $get_alert_post_meta_name == $alert_notice_id ) {
								?>
								setTimeout(delaybox<?php echo $i; ?>, <?php echo $alert_notice_delay; ?>);
								function delaybox<?php echo $i; ?>() {
									var message<?php echo $alert_notice_id; ?> = '<?php echo $alert_notice_content; ?>';
									var status<?php echo $alert_notice_id; ?> = '<?php echo $alert_notice_style; ?>';
									createAlert(message<?php echo $alert_notice_id; ?>, status<?php echo $alert_notice_id; ?>, <?php echo $alert_notice_show_time; ?>);
								}
							<?php
							$i+=1;
							}
						}
					}
				?>
			}
		};
	</script>

	<?php

}

function individual_control_meta_box() {

	$screen = get_current_screen();
	$get_page_id = get_the_ID();
	$post_meta_name = 'disable_all_alerts_page_' . $get_page_id;
	$meta_box_checkbox_value = $_POST[$post_meta_name];
	$get_post_meta_name = get_post_meta($get_page_id, $post_meta_name, true);
	global $wpdb;
	$table_name = $wpdb->prefix . 'alert_notice_boxes'; // do not forget about tables prefix
	$result = $wpdb->get_results( "SELECT * FROM $table_name");

	?>
	<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	    <tbody>
	    <tr class="form-field">
	        <td>
				<input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(ANB__PLUGIN_URL.$get_page_id); ?>" />
				<input name="<?php echo $post_meta_name; ?>" type="checkbox" value="<?php echo $get_page_id; ?>" <?php  if(esc_attr( $get_post_meta_name ) == $get_page_id ) {echo 'checked="checked"';} ?> ><label for="disable_all_alerts_page"><strong><?php _e( 'Turn off all alerts for this page', 'alert-notice-boxes' ) ?></strong></label>
	        </td>
	    </tr>
		<tr class="form-field">
			<td>
				<strong><?php _e( 'Display this page these alerts', 'alert-notice-boxes' ) ?></strong><br>
				<?php
                foreach ($result as $alert_notice_values) {
                    $alert_id = $alert_notice_values->id;
					$alert_title = $alert_notice_values->title;
					$alert_post_meta_name = 'display_alert_' . $alert_id . '_page_' . $get_page_id;
					$get_alert_post_meta_name = get_post_meta($get_page_id, $alert_post_meta_name, true);
                    ?>
                    <input name="display_alert_<?php echo $alert_id; ?>_page_<?php echo $get_page_id; ?>" type="checkbox" value="<?php echo $alert_id; ?>" <?php  if(esc_attr( $get_alert_post_meta_name ) == $alert_id ) {echo 'checked="checked"';} ?> ><label for="hide_menu_checkbox"><?php echo $alert_title; ?></label></br>
                    <?php
                }
				?>
			</td>
	    </tr>
	    </tbody>
	</table>
	<?php

}

function add_individual_control_meta_box()
{
    if( get_post_type() != 'anb' ) {
		add_meta_box("individual_control_meta_box_id", __( 'Alert settings for this page', 'alert-notice-boxes' ), array($this, 'individual_control_meta_box'), $screen->id, "side", "high", null);
	}
}

function save_individual_control_meta_box($post_id, $post, $update) {
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'alert_notice_boxes'; // do not forget about tables prefix
	$result = $wpdb->get_results( "SELECT * FROM $table_name");
	
	
	$get_page_id = get_the_ID();
	$post_meta_name = 'disable_all_alerts_page_' . $get_page_id;
	$meta_box_checkbox_value = $_POST[$post_meta_name];
	$get_post_meta_name = get_post_meta($get_page_id, $post_meta_name, true);
	
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], ANB__PLUGIN_URL.$get_page_id)) { return $get_page_id; }
	
	if(isset($_POST[$post_meta_name]) != "") {
		update_post_meta( $get_page_id, $post_meta_name, $meta_box_checkbox_value );
	} else {
		if ($get_post_meta_name != '') {
			update_post_meta( $get_page_id, $post_meta_name, '' );
		} else {
			// Do nothing
		}
	}
	
	foreach ($result as $alert_notice_values) {
		$alert_id = $alert_notice_values->id;
		$alert_title = $alert_notice_values->title;
		$alert_post_meta_name = 'display_alert_' . $alert_id . '_page_' . $get_page_id;
		$get_alert_post_meta_name = get_post_meta($get_page_id, $alert_post_meta_name, true);
		
		if(isset($_POST[$alert_post_meta_name]) != "") {
			update_post_meta( $get_page_id, $alert_post_meta_name, $alert_id );
		} else {
			if ($get_alert_post_meta_name != '') {
				update_post_meta( $get_page_id, $alert_post_meta_name, '' );
			} else {
				// Do nothing
			}
		}
	}
}

     
}
 
$YCanb = new YCanb;