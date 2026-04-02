<?php
	add_action( 'wp_ajax_TotalSoftCal_Edit', 'TotalSoftCal_Edit_Callback' );
	add_action( 'wp_ajax_nopriv_TotalSoftCal_Edit', 'TotalSoftCal_Edit_Callback' );

	function TotalSoftCal_Edit_Callback()
	{
		$Total_Soft_Cal_ID = sanitize_text_field($_POST['foobar']);
		global $wpdb;
		$table_name1 = $wpdb->prefix . "totalsoft_cal_1";
		$table_name4 = $wpdb->prefix . "totalsoft_cal_types";
		$table_name5 = $wpdb->prefix . "totalsoft_cal_2";
		$table_name7 = $wpdb->prefix . "totalsoft_cal_3";

		$Total_Soft_Cal_Types=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name4 WHERE id=%d", $Total_Soft_Cal_ID));

		if($Total_Soft_Cal_Types[0]->TotalSoftCal_Type=='Event Calendar')
		{
			$Total_Soft_Cal=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name1 WHERE TotalSoftCal_ID=%s",$Total_Soft_Cal_ID));
		}
		else if($Total_Soft_Cal_Types[0]->TotalSoftCal_Type=='Simple Calendar')
		{
			$Total_Soft_Cal=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name5 WHERE TotalSoftCal_ID=%s",$Total_Soft_Cal_ID));
		}
		else if($Total_Soft_Cal_Types[0]->TotalSoftCal_Type=='Flexible Calendar')
		{
			$Total_Soft_Cal=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name7 WHERE TotalSoftCal_ID=%s",$Total_Soft_Cal_ID));
		}
		print_r($Total_Soft_Cal);
		die();
	}

	add_action( 'wp_ajax_TotalSoftCal_DelEv', 'TotalSoftCal_DelEv_Callback' );
	add_action( 'wp_ajax_nopriv_TotalSoftCal_DelEv', 'TotalSoftCal_DelEv_Callback' );

	function TotalSoftCal_DelEv_Callback()
	{
		$Total_Soft_CalEv_ID = sanitize_text_field($_POST['foobar']);
		global $wpdb;
		$table_name3 = $wpdb->prefix . "totalsoft_cal_events";
		$table_name6 = $wpdb->prefix . "totalsoft_cal_events_p2";		
		$wpdb->query($wpdb->prepare("DELETE FROM $table_name3 WHERE id=%d", $Total_Soft_CalEv_ID));
		$wpdb->query($wpdb->prepare("DELETE FROM $table_name6 WHERE TotalSoftCal_EvCal=%s", $Total_Soft_CalEv_ID));
		die();
	}

	add_action( 'wp_ajax_TotalSoftCal_EditEv', 'TotalSoftCal_EditEv_Callback' );
	add_action( 'wp_ajax_nopriv_TotalSoftCal_EditEv', 'TotalSoftCal_EditEv_Callback' );

	function TotalSoftCal_EditEv_Callback()
	{
		$Total_Soft_CalEv_ID = sanitize_text_field($_POST['foobar']);
		global $wpdb;
		$table_name3 = $wpdb->prefix . "totalsoft_cal_events";
		$Total_Soft_Cal_Ev=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name3 WHERE id=%d",$Total_Soft_CalEv_ID));
		print_r($Total_Soft_Cal_Ev);
		die();
	}

	add_action( 'wp_ajax_TotalSoftCal_EditEv_Desc', 'TotalSoftCal_EditEv_Desc_Callback' );
	add_action( 'wp_ajax_nopriv_TotalSoftCal_EditEv_Desc', 'TotalSoftCal_EditEv_Desc_Callback' );

	function TotalSoftCal_EditEv_Desc_Callback()
	{
		$Total_Soft_CalEv_ID = sanitize_text_field($_POST['foobar']);
		global $wpdb;
		$table_name6 = $wpdb->prefix . "totalsoft_cal_events_p2";		
		$Total_Soft_Cal_Ev=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name6 WHERE TotalSoftCal_EvCal=%s",$Total_Soft_CalEv_ID));
		if($Total_Soft_Cal_Ev)
		{
			print_r($Total_Soft_Cal_Ev);
		}
		else
		{
			echo 'none';
		}
		die();
	}

	add_action( 'wp_ajax_TSoftCal_Vimeo_Video_Image', 'TSoftCal_Vimeo_Video_Image_Callback' );
	add_action( 'wp_ajax_nopriv_TSoftCal_Vimeo_Video_Image', 'TSoftCal_Vimeo_Video_Image_Callback' );

	function TSoftCal_Vimeo_Video_Image_Callback()
	{
		$GET_Cal_Video_Video_Src = sanitize_text_field($_POST['foobar']);

		$TSoft_Cal_Image_Src=explode('video/',$GET_Cal_Video_Video_Src);
		$TSoft_Cal_Image_Src_Real=unserialize(file_get_contents("http://vimeo.com/api/v2/video/$TSoft_Cal_Image_Src[1].php"));
		$TSoft_Cal_Image_Src_Real=$TSoft_Cal_Image_Src_Real[0]['thumbnail_large'];

		echo $TSoft_Cal_Image_Src_Real;
		die();
	}
?>