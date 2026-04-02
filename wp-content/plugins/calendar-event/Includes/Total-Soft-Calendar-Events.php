<?php
	if(!current_user_can('manage_options'))
	{
		die('Access Denied');
	}
	require_once(dirname(__FILE__) . '/Total-Soft-Calendar-Install.php');
	global $wpdb;

	wp_enqueue_media();
	wp_enqueue_script( 'custom-header' );
	add_filter( 'upload_size_limit', 'PBP_increase_upload' );
	function PBP_increase_upload(  )
	{
	 	return 20480000; // 20MB
	}

	$table_name3 = $wpdb->prefix . "totalsoft_cal_events";
	$table_name4 = $wpdb->prefix . "totalsoft_cal_types";
	$table_name6 = $wpdb->prefix . "totalsoft_cal_events_p2";

	$TotalSoftCal_EvName=sanitize_text_field($_POST['TotalSoftCal_EvName']);
	$TotalSoftCal_EvCal=sanitize_text_field($_POST['TotalSoftCal_EvCal']);
	$TotalSoftCal_EvStartDate=sanitize_text_field($_POST['TotalSoftCal_EvStartDate']);
	$TotalSoftCal_EvEndDate=sanitize_text_field($_POST['TotalSoftCal_EvEndDate']);
	$TotalSoftCal_EvURL=sanitize_text_field($_POST['TotalSoftCal_EvURL']);
	$TotalSoftCal_EvURLNewTab=sanitize_text_field($_POST['TotalSoftCal_EvURLNewTab']);
	$TotalSoftCal_EvStartTime=sanitize_text_field($_POST['TotalSoftCal_EvStartTime']);
	$TotalSoftCal_EvEndTime=sanitize_text_field($_POST['TotalSoftCal_EvEndTime']);
	$TotalSoftCal_EvColor=sanitize_text_field($_POST['TotalSoftCal_EvColor']);
	$TotalSoftCal_EvDesc=sanitize_text_field($_POST['TotalSoftCal_EvDesc']);
	$TotalSoftCal_EvImg=sanitize_text_field($_POST['TotalSoftCalendar_URL_Image_2']);
	$TotalSoftCal_EvVid_Src=sanitize_text_field($_POST['TotalSoftCalendar_URL_Video_2']);
	$TotalSoftCal_EvVid_Iframe=sanitize_text_field($_POST['TotalSoftCalendar_URL_Video_1']);

	$TotalSoftCal_EvDesc=str_replace('\"',')*^*(', $TotalSoftCal_EvDesc);
	$TotalSoftCal_EvDesc=str_replace("\'",")*&*(", $TotalSoftCal_EvDesc);

	if(isset($_POST['Total_Soft_Cal_SaveEv']))
	{
		$wpdb->query($wpdb->prepare("INSERT INTO $table_name3 (id, TotalSoftCal_EvName, TotalSoftCal_EvCal, TotalSoftCal_EvStartDate, TotalSoftCal_EvEndDate, TotalSoftCal_EvURL, TotalSoftCal_EvURLNewTab, TotalSoftCal_EvStartTime, TotalSoftCal_EvEndTime, TotalSoftCal_EvColor) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s)", '', $TotalSoftCal_EvName, $TotalSoftCal_EvCal, $TotalSoftCal_EvStartDate, $TotalSoftCal_EvEndDate, $TotalSoftCal_EvURL, $TotalSoftCal_EvURLNewTab, $TotalSoftCal_EvStartTime, $TotalSoftCal_EvEndTime, $TotalSoftCal_EvColor));

		$TotalSoftCalEvent=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name3 WHERE id>%d order by id desc limit 1",0));

		$wpdb->query($wpdb->prepare("INSERT INTO $table_name6 (id, TotalSoftCal_EvDesc, TotalSoftCal_EvImg, TotalSoftCal_EvVid_Src, TotalSoftCal_EvVid_Iframe, TotalSoftCal_EvCal) VALUES (%d, %s, %s, %s, %s, %s)", '', $TotalSoftCal_EvDesc, $TotalSoftCal_EvImg, $TotalSoftCal_EvVid_Src, $TotalSoftCal_EvVid_Iframe, $TotalSoftCalEvent[0]->id));
	}
	else if(isset($_POST['Total_Soft_Cal_UpdateEv']))
	{
		$Total_SoftCal_EvUpdate=sanitize_text_field($_POST['Total_SoftCal_EvUpdate']);
		$wpdb->query($wpdb->prepare("UPDATE $table_name3 set TotalSoftCal_EvName=%s, TotalSoftCal_EvCal=%s, TotalSoftCal_EvStartDate=%s, TotalSoftCal_EvEndDate=%s, TotalSoftCal_EvURL=%s, TotalSoftCal_EvURLNewTab=%s, TotalSoftCal_EvStartTime=%s, TotalSoftCal_EvEndTime=%s, TotalSoftCal_EvColor=%s WHERE id=%d", $TotalSoftCal_EvName, $TotalSoftCal_EvCal, $TotalSoftCal_EvStartDate, $TotalSoftCal_EvEndDate, $TotalSoftCal_EvURL, $TotalSoftCal_EvURLNewTab, $TotalSoftCal_EvStartTime, $TotalSoftCal_EvEndTime, $TotalSoftCal_EvColor, $Total_SoftCal_EvUpdate));
		$wpdb->query($wpdb->prepare("UPDATE $table_name6 set TotalSoftCal_EvDesc=%s, TotalSoftCal_EvImg=%s, TotalSoftCal_EvVid_Src=%s, TotalSoftCal_EvVid_Iframe=%s WHERE TotalSoftCal_EvCal=%s", $TotalSoftCal_EvDesc, $TotalSoftCal_EvImg, $TotalSoftCal_EvVid_Src, $TotalSoftCal_EvVid_Iframe, $Total_SoftCal_EvUpdate));
	}

	$TotalSoftCalCount=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name4 WHERE id>%d",0));
	$TotalSoftEvCount=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name3 WHERE id>%d order by id",0));
?>
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('../CSS/totalsoft.css',__FILE__);?>">
<form method="POST" enctype="multipart/form-data">
	<div class="Total_Soft_Cal_AMD">
		<div class="Total_Soft_Cal_AMD1"></div>
		<input type="text" style="display:none;" name="Total_SoftCal_EvUpdate" id="Total_SoftCal_EvUpdate">
		<div class="Total_Soft_Cal_AMD2">
			<i class="Total_Soft_Help totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Click for Creating New Event', 'Total-Soft-Calendar' );?>"></i>
			<input type="button" name="" value="<?php echo __( 'Create Event', 'Total-Soft-Calendar' );?>" class="Total_Soft_Cal_AMD2_But" onclick="Total_Soft_CalEv_AMD2_But1()">
		</div>
	</div>
	<table class="Total_Soft_AMEvTable">
		<tr class="Total_Soft_Titles">
			<td colspan="4"><?php echo __( 'Add New Event', 'Total-Soft-Calendar' );?></td>
		</tr>
		<tr>
			<td><?php echo __( 'Event Title', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'You can give a name for event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="text" name="TotalSoftCal_EvName" id="TotalSoftCal_EvName" class="Total_Soft_Select" required placeholder=" * <?php echo __( 'Required', 'Total-Soft-Calendar' );?>"></td>
			<td><?php echo __( 'Calendar Name', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Choose that version of calendar themes, in which you want to see the Events.', 'Total-Soft-Calendar' );?>"></i></td>
			<td>
				<select class="Total_Soft_Select" name="TotalSoftCal_EvCal" id="TotalSoftCal_EvCal">
					<?php for($i=0;$i<count($TotalSoftCalCount);$i++){?>
						<option value="<?php echo $TotalSoftCalCount[$i]->id;?>"><?php echo $TotalSoftCalCount[$i]->TotalSoftCal_Name;?></option>
					<?php }?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo __( 'Start Date', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Select the start of the event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="date" class="Total_Soft_Select" name="TotalSoftCal_EvStartDate" id="TotalSoftCal_EvStartDate" placeholder="<?php echo __( 'yyyy-mm-dd', 'Total-Soft-Calendar' );?>"></td>
			<td><?php echo __( 'End Date', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Select the finish time of the event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="date" class="Total_Soft_Select" name="TotalSoftCal_EvEndDate" id="TotalSoftCal_EvEndDate" placeholder="<?php echo __( 'yyyy-mm-dd', 'Total-Soft-Calendar' );?>"></td>
		</tr>
		<tr>
			<td><?php echo __( 'URL', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Set external URL in the calendar, which should be included in the event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="text" name="TotalSoftCal_EvURL" id="TotalSoftCal_EvURL" class="Total_Soft_Select" placeholder=" * <?php echo __( 'Optional', 'Total-Soft-Calendar' );?>"></td>
			<td><?php echo __( 'Open In New Tab', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Choose, by clicking on the link should open in new tab or not.', 'Total-Soft-Calendar' );?>"></i></td>
			<td>
				<select class="Total_Soft_Select" name="TotalSoftCal_EvURLNewTab" id="TotalSoftCal_EvURLNewTab">
					<option value="_blank"><?php echo __( 'Open In New Tab', 'Total-Soft-Calendar' );?></option>
					<option value="none"><?php echo __( 'Open In Same Tab', 'Total-Soft-Calendar' );?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo __( 'Start Time', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Select the event start time.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="time" name="TotalSoftCal_EvStartTime" id="TotalSoftCal_EvStartTime" placeholder="<?php echo __( 'hh:mm', 'Total-Soft-Calendar' );?>"></td>
			<td><?php echo __( 'End Time', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Select the event end time.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="time" name="TotalSoftCal_EvEndTime" id="TotalSoftCal_EvEndTime" placeholder="<?php echo __( 'hh:mm', 'Total-Soft-Calendar' );?>"></td>
		</tr>
		<tr>
			<td><?php echo __( 'Event Color', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'Select that color, which you want to see for your event, which shows in the calendar.', 'Total-Soft-Calendar' );?>"></i></td>
			<td><input type="text" name="TotalSoftCal_EvColor" id="TotalSoftCal_EvColor" class="Total_Soft_Color" value="#ffffff"></td>
			<td colspan="2"><?php echo __( 'Event Color option is only for Event Calendar Type.', 'Total-Soft-Calendar' );?></td>
		</tr>
		<tr>
			<td><?php echo __( 'Event Description', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'You can give a description for event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td>
				<textarea rows="4" style="width: 90%" id="TotalSoftCal_EvDesc" name="TotalSoftCal_EvDesc"></textarea>
			</td>
			<td colspan="2"><?php echo __( 'Event Description option is only for Simple Calendar Type.', 'Total-Soft-Calendar' );?></td>
		</tr>
		<tr>
			<td><?php echo __( 'Event Image/Video', 'Total-Soft-Calendar' );?> <i class="Total_Soft_Help1 totalsoft totalsoft-question-circle-o" title="<?php echo __( 'You can give Image or Video for event.', 'Total-Soft-Calendar' );?>"></i></td>
			<td>
				<div id="wp-content-media-buttons" class="wp-media-buttons" >													
					<a href="#" class="button insert-media add_media" style="border:1px solid #009491; color:#009491; background-color:#f4f4f4" data-editor="TotalSoftCalendar_URL_1" title="Add Media" id="TotalSoftCalendar_URL" onclick="TotalSoftCalendar_URL_Clicked()">
						<span class="wp-media-buttons-icon"></span>Add Media
					</a>
				</div>
				<input type="text" style="display:none;" id="TotalSoftCalendar_URL_1">					
			</td>
			
			<td colspan="2"><?php echo __( 'Event Image or Video option is only for Flexible Calendar Type.', 'Total-Soft-Calendar' );?></td>
		</tr>
		<tr>
			<td><input type="text" id="TotalSoftCalendar_URL_Video_2" name="TotalSoftCalendar_URL_Video_2" class="Total_Soft_Select" style="display:none"></td>
			<td>
				<input type="text" id="TotalSoftCalendar_URL_Video_1" name="TotalSoftCalendar_URL_Video_1" readonly class="Total_Soft_Select">
			</td>
			<td><input type="text" id="TotalSoftCalendar_URL_Image_2" name="TotalSoftCalendar_URL_Image_2" class="Total_Soft_Select" style="display:none"></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3"></td>
			<td style="text-align:center;"><input type="submit" value="<?php echo __( 'Update', 'Total-Soft-Calendar' );?>" class="Total_Soft_Cal_Update_Ev Total_Soft_Cal_AMEvT_But" name="Total_Soft_Cal_UpdateEv"><input type="submit" value="<?php echo __( 'Save', 'Total-Soft-Calendar' );?>" class="Total_Soft_Cal_Save_Ev Total_Soft_Cal_AMEvT_But" name="Total_Soft_Cal_SaveEv"> <input type="button" value="<?php echo __( 'Cancel', 'Total-Soft-Calendar' );?>" class="Total_Soft_Cal_AMEvT_But" onclick='TotalSoft_Reload()'></td>
		</tr>
	</table>

	<table class="Total_Soft_AMMTable">
		<tr class="Total_Soft_AMMTableFR">
			<td><?php echo __( 'No', 'Total-Soft-Calendar' );?></td>
			<td><?php echo __( 'Event Title', 'Total-Soft-Calendar' );?></td>
			<td><?php echo __( 'Calendar Name', 'Total-Soft-Calendar' );?></td>
			<td><?php echo __( 'Actions', 'Total-Soft-Calendar' );?></td>
		</tr>
	</table>

	<table class="Total_Soft_AMOTable">
	 	<?php for($i=0;$i<count($TotalSoftEvCount);$i++){
	 		$TotalSoft_Cal_Name=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name4 WHERE id=%d", $TotalSoftEvCount[$i]->TotalSoftCal_EvCal));
	 		?> 
	 		<tr>
				<td><?php echo $i+1;?></td>
				<td><?php echo $TotalSoftEvCount[$i]->TotalSoftCal_EvName;?></td>
				<td><?php echo $TotalSoft_Cal_Name[0]->TotalSoftCal_Name;?></td>
				<td onclick="TotalSoftCal_EditEv(<?php echo $TotalSoftEvCount[$i]->id;?>)"><i class="Total_Soft_icon totalsoft totalsoft-pencil"></i></td>
				<td onclick="TotalSoftCal_DelEv(<?php echo $TotalSoftEvCount[$i]->id;?>)"><i class="Total_Soft_icon totalsoft totalsoft-trash"></i></td>
			</tr>
	 	<?php }?>
	</table>
</form>