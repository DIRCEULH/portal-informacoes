<?php
	
	class Total_Soft_Cal extends WP_Widget
	{
		function __construct()
 	  	{
 			$params=array('name'=>'Total Soft Calendar','description'=>__( 'This is the widget of Total Soft Calendar plugin', 'Total-Soft-Calendar' ));
			parent::__construct('Total_Soft_Cal','',$params);
 	  	}
 	  	function form($instance)
 		{
 			$defaults = array('Total_Soft_Cal'=>'');
		    $instance = wp_parse_args((array)$instance, $defaults);

		   	$Calendar = $instance['Total_Soft_Cal'];
		   	?>
		   	<div>			  
			   	<p>
			   		Calendar Title:
			   		<select name="<?php echo $this->get_field_name('Total_Soft_Cal'); ?>" class="widefat">
				   		<?php
				   			global $wpdb;

							$table_name4 = $wpdb->prefix . "totalsoft_cal_types";
							$Total_Soft_Cal=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name4 WHERE id > %d", 0));
				   			
				   			foreach ($Total_Soft_Cal as $Total_Soft_Cal1)
				   			{
				   				?> <option value="<?php echo $Total_Soft_Cal1->id; ?>"> <?php echo $Total_Soft_Cal1->TotalSoftCal_Name; ?> </option> <?php 
				   			}
				   		?>
			   		</select>
			   	</p>
		   	</div>
		   	<?php
 		}
 		function widget($args,$instance)
 		{
 			extract($args);
 		 	$Total_Soft_Cal = empty($instance['Total_Soft_Cal']) ? '' : $instance['Total_Soft_Cal'];
 		 	global $wpdb;

			$table_name  = $wpdb->prefix . "totalsoft_fonts";
			$table_name1 = $wpdb->prefix . "totalsoft_cal_1";
			$table_name2 = $wpdb->prefix . "totalsoft_cal_ids";
			$table_name3 = $wpdb->prefix . "totalsoft_cal_events";
			$table_name4 = $wpdb->prefix . "totalsoft_cal_types";
			$table_name5 = $wpdb->prefix . "totalsoft_cal_2";
			$table_name6 = $wpdb->prefix . "totalsoft_cal_events_p2";
			$table_name7 = $wpdb->prefix . "totalsoft_cal_3";

			$TotalSoftCal_Type=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name4 WHERE id = %d", $Total_Soft_Cal));

			if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Event Calendar')
			{
				$TotalSoftCal_Par=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name1 WHERE TotalSoftCal_ID = %d", $TotalSoftCal_Type[0]->id));
			}
			else if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Simple Calendar')
			{
				$TotalSoftCal_Par=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name5 WHERE TotalSoftCal_ID = %d", $TotalSoftCal_Type[0]->id));
			}
			else if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Flexible Calendar')
			{
				$TotalSoftCal_Par=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name7 WHERE TotalSoftCal_ID = %d", $TotalSoftCal_Type[0]->id));
			}

			$Total_Soft_CalEvents=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name3 WHERE TotalSoftCal_EvCal=%s order by id",$Total_Soft_Cal));

			$Total_Soft_CalEvents_Date = [];
			$Total_Soft_CalEvents_Desc = [];
			$Total_Soft_CalEvents_Date_Real = [];
			$Total_Soft_CalEvents_Desc_Real = [];

			for($i=0;$i<count($Total_Soft_CalEvents);$i++){ 
				array_push($Total_Soft_CalEvents_Date, $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate);

				if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab=='none')
				{
					$Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab='';
				} 
				$Total_Soft_CalEventDesc=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name6 WHERE TotalSoftCal_EvCal=%s order by id",$Total_Soft_CalEvents[$i]->id));
				$TotalSoftcalEvent = '';
				
				if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_I_Pos == '1') // Media Before Title
				{
					if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Iframe != '')
					{
						$TotalSoftcalEvent .= "<div style='position: relative; width: 99%; margin: 5px auto; text-align: center;'>";
						if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src == '')
						{
							$TotalSoftcalEvent .= "<img src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvImg . "' class='TotalSoftcalEvent_Media'>";
						}
						else
						{
							$TotalSoftcalEvent .= "<div class='TotalSoftcalEvent_Mediadiv'><iframe src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src . "' class='TotalSoftcalEvent_Mediaiframe' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>";
						}
						$TotalSoftcalEvent .= "</div>";
					}
				}

				if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL != '')
				{
					if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Pos == '1') // Link Before Title
					{
						$TotalSoftcalEvent .= "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkBl' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a>";
						$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . "</p>";
					}
					else if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Pos == '2') // Link After Title
					{
						$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . "</p>";
						$TotalSoftcalEvent .= "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkBl' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a>";
					}
					else if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Pos == '3') // Link After Title Text
					{
						$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkMar' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a></p>";
					}
					else
					{
						$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . "</p>";
					}
				}
				else
				{
					$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . "</p>";
				}	

				if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate == $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate)
				{
					$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate;

					if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime != '')
					{
						if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime != '' &&  $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime != $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime)
						{
							$TotalSoftcalEvent .= "<span style='margin-left: 10px;'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime . ' - ' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime . "</span>";
						}
						else
						{
							$TotalSoftcalEvent .= "<span style='margin-left: 10px;'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime . "</span>";
						}
					}

					$TotalSoftcalEvent .= "</p>";
				}
				else
				{
					$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Title'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate . ' / ' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate;

					if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime != '')
					{
						if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime != '' &&  $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime != $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime)
						{
							$TotalSoftcalEvent .= "<span style='margin-left: 10px;'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime . ' - ' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime . "</span>";
						}
						else
						{
							$TotalSoftcalEvent .= "<span style='margin-left: 10px;'>" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime . "</span>";
						}
					}

					$TotalSoftcalEvent .= "</p>";
				}					

				if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_I_Pos == '2') // Media After Title
				{
					if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Iframe != '')
					{								
						$TotalSoftcalEvent .= "<div style='position: relative; width: 99%; margin: 5px auto; text-align: center;'>";
						if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src == '')
						{
							$TotalSoftcalEvent .= "<img src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvImg . "' class='TotalSoftcalEvent_Media'>";
						}
						else
						{
							$TotalSoftcalEvent .= "<div class='TotalSoftcalEvent_Mediadiv'><iframe src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src . "' class='TotalSoftcalEvent_Mediaiframe' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>";
						}
						$TotalSoftcalEvent .= "</div>";
					}
				}

				if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL != '')
				{	
					if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc != '')
					{							
						if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Pos == '4') // Link After Description
						{
							$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Desc'>" . str_replace(')*^*(', '"', str_replace(")*&*(", "'", $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc)) . "</p>";
							$TotalSoftcalEvent .= "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkBl' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a>";
						}
						else if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Pos == '5') // Link After Description Text
						{
							$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Desc'>" . str_replace(')*^*(', '"', str_replace(")*&*(", "'", $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc)) . "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkMar' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a></p>";
						}
						else
						{
							$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Desc'>" . str_replace(')*^*(', '"', str_replace(")*&*(", "'", $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc)) . "</p>";
						}
					}
					else
					{
						$TotalSoftcalEvent .= "<a class='TotalSoftcalEvent_Link TotalSoftcalEvent_LinkBl' href='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . "' target='" . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab . "'>" . $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_Text . "</a>";
					}
				}
				else
				{
					if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc != '')
					{
						$TotalSoftcalEvent .= "<p class='TotalSoftcalEvent_Desc'>" . str_replace(')*^*(', '"', str_replace(")*&*(", "'", $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc)) . "</p>";
					}
				}

				if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_I_Pos == '3') // Media After Description
				{
					if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Iframe != '')
					{								
						$TotalSoftcalEvent .= "<div style='position: relative; width: 99%; margin: 10px auto; text-align: center;'>";
						if($Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src == '')
						{
							$TotalSoftcalEvent .= "<img src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvImg . "' class='TotalSoftcalEvent_Media'>";
						}
						else
						{
							$TotalSoftcalEvent .= "<div class='TotalSoftcalEvent_Mediadiv'><iframe src='" . $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvVid_Src . "' class='TotalSoftcalEvent_Mediaiframe' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>";
						}
						$TotalSoftcalEvent .= "</div>";
					}
				}

				$TotalSoftcalEvent .= "<div class='TotalSoftcalEvent_LAE'></div>";
				array_push($Total_Soft_CalEvents_Desc, $TotalSoftcalEvent);
			}
			for($i=0;$i<count($Total_Soft_CalEvents_Date);$i++)
			{
				if($Total_Soft_CalEvents_Date[$i] != '' || $Total_Soft_CalEvents_Date[$i] != null)
				{
					for($j=$i; $j<count($Total_Soft_CalEvents_Date);$j++)
					{
						if($Total_Soft_CalEvents_Date[$i] === $Total_Soft_CalEvents_Date[$j+1])
						{
							$Total_Soft_CalEvents_Date[$j+1] = '';
							$Total_Soft_CalEvents_Desc[$i] = $Total_Soft_CalEvents_Desc[$i] . $Total_Soft_CalEvents_Desc[$j+1];
							$Total_Soft_CalEvents_Desc[$j+1] = '';
						}
					}	
				}	
			}
			for($i=0;$i<count($Total_Soft_CalEvents_Date);$i++)
			{
				if($Total_Soft_CalEvents_Date[$i] != '')
				{
					array_push($Total_Soft_CalEvents_Date_Real, $Total_Soft_CalEvents_Date[$i]);
					array_push($Total_Soft_CalEvents_Desc_Real, $Total_Soft_CalEvents_Desc[$i]);
				}
			}
 		 	echo $before_widget;
 		 	if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Event Calendar'){ ?>
 		 		<style type="text/css">
					.monthly {
						box-shadow: 0px 0px 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_BSCol;?>;
						border:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_BW;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_BStyle;?> <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_BCol;?>;
					}
					.desc {
						max-width: 250px;
						text-align: left;
						font-size:14px;
						padding-top:30px;
						line-height: 1.4em;
					}
					.resize {
						background: #222;
						display: inline-block;
						padding: 6px 15px;
						border-radius: 22px;
						font-size: 13px;
					}
					@media (max-height: 700px) {
						.sticky {
							position: relative;
						}
					}
					@media (max-width: 600px) {
						.resize {
							display: none;
						}
					}
					/* Contains title & nav */
					.monthly-header {
						position: relative;
						text-align:center;
						padding:10px;
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HBgCol;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HCol;?>;
						box-sizing: border-box;
					}
					.monthly-header-title {
						font-size:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HFS;?>px;
						font-family:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HFF;?>;
					}
					.monthly-day-title-wrap {
						display:table;
						table-layout:fixed;
						width:100%;
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_WBgCol;?>;
						color:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_WCol;?>;
						border-bottom: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_LAW;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_LAWS;?> <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_LAWC;?>;
					}
					.monthly-day-title-wrap div {
						font-size:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_WFS;?>px;
						font-family:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_WFF;?>;
					}
					/* Calendar Days */
					.monthly-day, .monthly-day-blank {
						box-shadow: 0 0 0 <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_GW;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_GrCol;?>;
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_DBgCol;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_DCol;?>;
					}
					/* Days that are part of previous or next month */
					.monthly-day-blank {
						background:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_BgCol;?>;
					}
					.monthly-day-event > .monthly-day-number {
						font-size:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_DFS;?>px;
						<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_NumPos;?>: 2px;
					}
					.monthly-today .monthly-day-number {
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_TCol;?>;
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_TNBgCol;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_TFS;?>px;
						<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_NumPos;?>: 2px;
					}
					.monthly-today{
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_TBgCol;?>;
					}
					/* Increase font & spacing over larger size */
					@media (min-width: 400px) {
						.monthly-day-number {
							top: 5px;
							<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_NumPos;?>: 5px;
							font-size: 13px;
						}
					}
					.TotalSoftRefresh
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_RefIcSize;?>px;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_RefIcCol;?>;
					}
					.TotalSoftArrow
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_ArrowSize;?>px;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_ArrowCol;?>;
					}
					.monthly-day:hover
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HovBgCol;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal_HovCol;?>;
					}
				</style>
				<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('../CSS/totalsoft.css',__FILE__);?>">
				<div class="page">
					<input type="text" style="display:none;" id="TotalSoftCal_ArrowLeft" value="<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_ArrowLeft;?>">
					<input type="text" style="display:none;" id="TotalSoftCal_ArrowRight" value="<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_ArrowRight;?>">
					<input type="text" style="display:none;" id="totalsoftcal_<?php echo $Total_Soft_Cal;?>_1" value="<?php echo $Total_Soft_Cal;?>">
					<div style="width:100%; max-width:<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_MW;?>px; display:inline-block;">
						<div class="monthly" id="totalsoftcal_<?php echo $Total_Soft_Cal;?>"></div>
					</div>
				</div>
				<!-- JS ======================================================= -->
				<script type="text/javascript">
					(function($) {
						$.fn.extend({
							monthly: function(options) {
								// These are overridden by options declared in footer
								var defaults = {
									weekStart: 'Mon',
									mode: '',
									xmlUrl: '',
									target: '',
									eventList: true,
									maxWidth: false,
									setWidth: false,
									startHidden: false,
									showTrigger: '',
									stylePast: false,
									disablePast: false
								}

								var options = $.extend(defaults, options),
									that = this,
									uniqueId = $(this).attr('id'),
									d = new Date(),
									currentMonth = d.getMonth() + 1,
									currentYear = d.getFullYear(),
									currentDay = d.getDate(),
									monthNames = options.monthNames || ['<?php echo __( 'Jan', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Feb', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Mar', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Apr', 'Total-Soft-Calendar' );?>', '<?php echo __( 'May', 'Total-Soft-Calendar' );?>', '<?php echo __( 'June', 'Total-Soft-Calendar' );?>', '<?php echo __( 'July', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Aug', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Sep', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Oct', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Nov', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Dec', 'Total-Soft-Calendar' );?>'],
									dayNames = options.dayNames || ['<?php echo __( 'Sun', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Mon', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Tue', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Wed', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Thu', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Fri', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Sat', 'Total-Soft-Calendar' );?>'];
							if (options.maxWidth != false){
								$('#'+uniqueId).css('maxWidth',options.maxWidth);
							}
							if (options.setWidth != false){
								$('#'+uniqueId).css('width',options.setWidth);
							}
							if (options.startHidden == true){
								$('#'+uniqueId).addClass('monthly-pop').css({
									'position' : 'absolute',
									'display' : 'none'
								});
								$(document).on('focus', ''+options.showTrigger+'', function (e) {
									$('#'+uniqueId).show();
									e.preventDefault();
								});
								$(document).on('click', ''+options.showTrigger+', .monthly-pop', function (e) {
									e.stopPropagation();
									e.preventDefault();
								});
								$(document).on('click', function (e) {
									$('#'+uniqueId).hide();
								});
							}
							if (options.weekStart == 'Sun') {
								$('#' + uniqueId).append('<div class="monthly-day-title-wrap"><div>'+dayNames[0]+'</div><div>'+dayNames[1]+'</div><div>'+dayNames[2]+'</div><div>'+dayNames[3]+'</div><div>'+dayNames[4]+'</div><div>'+dayNames[5]+'</div><div>'+dayNames[6]+'</div></div><div class="monthly-day-wrap"></div>');
							} else{
								$('#' + uniqueId).append('<div class="monthly-day-title-wrap"><div>'+dayNames[1]+'</div><div>'+dayNames[2]+'</div><div>'+dayNames[3]+'</div><div>'+dayNames[4]+'</div><div>'+dayNames[5]+'</div><div>'+dayNames[6]+'</div><div>'+dayNames[0]+'</div></div><div class="monthly-day-wrap"></div>');
							}
							var TotalSoftCal_ArrowLeft=jQuery('#TotalSoftCal_ArrowLeft').val();
							var TotalSoftCal_ArrowRight=jQuery('#TotalSoftCal_ArrowRight').val();
							$('#' + uniqueId).prepend('<div class="monthly-header"><div class="monthly-header-title"></div><a href="#" class="monthly-prev"><i class="TotalSoftArrow '+TotalSoftCal_ArrowLeft+'"></i></a><a href="#" class="monthly-next"><i class="TotalSoftArrow '+TotalSoftCal_ArrowRight+'"></i></a></div>').append('<div class="monthly-event-list"></div>');
							function daysInMonth(m, y){
								return m===2?y&3||!(y%25)&&y&15?28:29:30+(m+(m>>3)&1);
							}
							function setMonthly(m, y){
								$('#' + uniqueId).data('setMonth', m).data('setYear', y);
								var dayQty = daysInMonth(m, y),
									mZeroed = m -1,
									firstDay = new Date(y, mZeroed, 1, 0, 0, 0, 0).getDay();
								$('#' + uniqueId + ' .monthly-day, #' + uniqueId + ' .monthly-day-blank').remove();
								$('#'+uniqueId+' .monthly-event-list').empty();
								$('#'+uniqueId+' .monthly-day-wrap').empty();
								if (options.mode == 'event') {
									for(var i = 0; i < dayQty; i++) {
										var day = i + 1; 
										var dayNamenum = new Date(y, mZeroed, day, 0, 0, 0, 0).getDay()
										$('#' + uniqueId + ' .monthly-day-wrap').append('<a href="#" class="m-d monthly-day monthly-day-event" data-number="'+day+'"><div class="monthly-day-number">'+day+'</div><div class="monthly-indicator-wrap"></div></a>');
										$('#' + uniqueId + ' .monthly-event-list').append('<div class="monthly-list-item" id="'+uniqueId+'day'+day+'" data-number="'+day+'"><div class="monthly-event-list-date">'+dayNames[dayNamenum]+'<br>'+day+'</div></div>');
									}
								} else {
									for(var i = 0; i < dayQty; i++) {
										var day = i + 1;
										if(((day < currentDay && m === currentMonth) || y < currentYear || (m < currentMonth && y == currentYear)) && options.stylePast == true){
												$('#' + uniqueId + ' .monthly-day-wrap').append('<a href="#" class="m-d monthly-day monthly-day-pick monthly-past-day" data-number="'+day+'"><div class="monthly-day-number">'+day+'</div><div class="monthly-indicator-wrap"></div></a>');
										} else {
											$('#' + uniqueId + ' .monthly-day-wrap').append('<a href="#" class="m-d monthly-day monthly-day-pick" data-number="'+day+'"><div class="monthly-day-number">'+day+'</div><div class="monthly-indicator-wrap"></div></a>');
										}
									}
								}
								var setMonth = $('#' + uniqueId).data('setMonth'),
									setYear = $('#' + uniqueId).data('setYear');
								if (setMonth == currentMonth && setYear == currentYear) {
									$('#' + uniqueId + ' *[data-number="'+currentDay+'"]').addClass('monthly-today');
								}
								if (setMonth == currentMonth && setYear == currentYear) {
									$('#' + uniqueId + ' .monthly-header-title').html(monthNames[m - 1] +' '+ y);
								} else {
									$('#' + uniqueId + ' .monthly-header-title').html(monthNames[m - 1] +' '+ y +'<a href="#" class="monthly-reset" title="<?php echo __( 'Back To This Month', 'Total-Soft-Calendar' );?>"><i class="TotalSoftRefresh totalsoft totalsoft-refresh"></i></a> ');
								}
								if(options.weekStart == 'Sun' && firstDay != 7) {
									for(var i = 0; i < firstDay; i++) {
										$('#' + uniqueId + ' .monthly-day-wrap').prepend('<div class="m-d monthly-day-blank"><div class="monthly-day-number"></div></div>');
									}
								} else if (options.weekStart == 'Mon' && firstDay == 0) {
									for(var i = 0; i < 6; i++) {
										$('#' + uniqueId + ' .monthly-day-wrap').prepend('<div class="m-d monthly-day-blank" ><div class="monthly-day-number"></div></div>');
									}
								} else if (options.weekStart == 'Mon' && firstDay != 1) {
									for(var i = 0; i < (firstDay - 1); i++) {
										$('#' + uniqueId + ' .monthly-day-wrap').prepend('<div class="m-d monthly-day-blank" ><div class="monthly-day-number"></div></div>');
									}
								}
								var numdays = $('#' + uniqueId + ' .monthly-day').length,
									numempty = $('#' + uniqueId + ' .monthly-day-blank').length,
									totaldays = numdays + numempty,
									roundup = Math.ceil(totaldays/7) * 7,
									daysdiff = roundup - totaldays;
								if(totaldays % 7 != 0) {
									for(var i = 0; i < daysdiff; i++) {
										$('#' + uniqueId + ' .monthly-day-wrap').append('<div class="m-d monthly-day-blank"><div class="monthly-day-number"></div></div>');
									}
								}
								if (options.mode == 'event') {
									$.get(''+options.xmlUrl+'', function(d){
										<?php for($i=0;$i<count($Total_Soft_CalEvents);$i++){
											$TotalSoftCal_EvStartDate=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate);
											if($TotalSoftCal_EvStartDate[1][0]==0)
											{
												$TotalSoftCal_EvStartDate[1]=$TotalSoftCal_EvStartDate[1][1];
											}
											if($TotalSoftCal_EvStartDate[2][0]==0)
											{
												$TotalSoftCal_EvStartDate[2]=$TotalSoftCal_EvStartDate[2][1];
											}
											$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate=implode('-',$TotalSoftCal_EvStartDate);

											$TotalSoftCal_EvEndDate=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate);
											if($TotalSoftCal_EvEndDate[1][0]==0)
											{
												$TotalSoftCal_EvEndDate[1]=$TotalSoftCal_EvEndDate[1][1];
											}
											if($TotalSoftCal_EvEndDate[2][0]==0)
											{
												$TotalSoftCal_EvEndDate[2]=$TotalSoftCal_EvEndDate[2][1];
											}
											$Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate=implode('-',$TotalSoftCal_EvEndDate);
											if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab=='none')
											{
												$Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab='';
											} 
											$TotalSoftEventData=$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvColor . 'TSCEv' . $Total_Soft_CalEvents[$i]->id . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime . 'TSCEv' . $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab;
											?>
												var CalData="<?php echo $TotalSoftEventData;?>".split('TSCEv');
												var fullstartDate = CalData[0],
												startArr = fullstartDate.split("-"),
												startYear = startArr[0],
												startMonth = parseInt(startArr[1], 10),
												startDay = parseInt(startArr[2], 10),
												fullendDate = CalData[1],
												endArr = fullendDate.split("-"),
												endYear = endArr[0],
												endMonth = parseInt(endArr[1], 10),
												endDay = parseInt(endArr[2], 10),
												eventURL = CalData[2],
												eventTitle = CalData[3],
												eventColor = CalData[4],
												eventId = CalData[5],
												startTime = CalData[6],
												startSplit = startTime.split(":");
												endTime = CalData[7],
												endSplit = endTime.split(":");
												eventLink = '',
												startPeriod = 'AM',
												endPeriod = 'AM';
												if(parseInt(startSplit[0]) >= 12) {
													if(parseInt(startSplit[0]) >= 22)
													{
														var startTime = (startSplit[0] - 12)+':'+startSplit[1]+'';
													}
													else
													{
														var startTime = '0'+(startSplit[0] - 12)+':'+startSplit[1]+'';
													}
													var startPeriod = 'PM'
												}
												if(parseInt(startTime) == 0) {
													var startTime = '12:'+startSplit[1]+'';
												}
												if(parseInt(endSplit[0]) >= 12) {
													if(parseInt(endSplit[0]) >= 22)
													{
														var endTime = (endSplit[0] - 12)+':'+endSplit[1]+'';
													}
													else
													{
														var endTime = '0'+(endSplit[0] - 12)+':'+endSplit[1]+'';
													}
													var endPeriod = 'PM'
												}
												if(parseInt(endTime) == 0) {
													var endTime = '12:'+endSplit[1]+'';
												}
												if (eventURL){
													var eventLink = 'href="'+eventURL+'"';
												}
												function multidaylist(){
													var timeHtml = '';
													if (startTime){
														var startTimehtml = '<div><div class="monthly-list-time-start">'+startTime+' '+startPeriod+'</div>';
														var endTimehtml = '';
														if (endTime){
															var endTimehtml = '<div class="monthly-list-time-end">'+endTime+' '+endPeriod+'</div>';
														}
														var timeHtml = startTimehtml + endTimehtml + '</div>';
													}
													$('#'+uniqueId+' .monthly-list-item[data-number="'+i+'"]').addClass('item-has-event').append('<a href="'+eventURL+'" target="'+CalData[8]+'" class="listed-event"  data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+' '+timeHtml+'</a>');
												}
												if (!fullendDate && startMonth == setMonth && startYear == setYear) {
													$('#'+uniqueId+' *[data-number="'+startDay+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator"  data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+'</div>');
													var timeHtml = '';
													if (startTime){
														var startTimehtml = '<div><div class="monthly-list-time-start">'+startTime+' '+startPeriod+'</div>';
														var endTimehtml = '';
														if (endTime){
															var endTimehtml = '<div class="monthly-list-time-end">'+endTime+' '+endPeriod+'</div>';
														}
														var timeHtml = startTimehtml + endTimehtml + '</div>';
													}
													$('#'+uniqueId+' .monthly-list-item[data-number="'+startDay+'"]').addClass('item-has-event').append('<a href="'+eventURL+'" target="'+CalData[8]+'" class="listed-event"  data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+' '+timeHtml+'</a>');
												} else if (startMonth == setMonth && startYear == setYear && endMonth == setMonth && endYear == setYear){
													for(var i = parseInt(startDay); i <= parseInt(endDay); i++) {
														if (i == parseInt(startDay)) {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+'</div>');
														} else {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'"></div>');
														}
														multidaylist();
													}
												} else if ((endMonth == setMonth && endYear == setYear) && ((startMonth < setMonth && startYear == setYear) || (startYear < setYear))) {
													for(var i = 0; i <= parseInt(endDay); i++) {
														if (i==1){
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+'</div>');
														} else {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'"></div>');
														}
														multidaylist();
													}
												} else if ((startMonth == setMonth && startYear == setYear) && ((endMonth > setMonth && endYear == setYear) || (endYear > setYear))){
													for(var i = parseInt(startDay); i <= dayQty; i++) {
														if (i == parseInt(startDay)) {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+'</div>');
														} else {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'"></div>');
														}
														multidaylist();
													}
												} else if (((startMonth < setMonth && startYear == setYear) || (startYear < setYear)) && ((endMonth > setMonth && endYear == setYear) || (endYear > setYear))){
													for(var i = 0; i <= dayQty; i++) {
														if (i == 1){
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'">'+eventTitle+'</div>');
														} else {
															$('#'+uniqueId+' *[data-number="'+i+'"] .monthly-indicator-wrap').append('<div class="monthly-event-indicator" data-eventid="'+ eventId +'" style="background:'+eventColor+'" title="'+eventTitle+'"></div>');
														}
														multidaylist();
													}
												}
										<?php } ?>
									}).fail(function() {
										console.error('Error Data...');
									});
								}
								var divs = $("#"+uniqueId+" .m-d");
								for(var i = 0; i < divs.length; i+=7) {
								  divs.slice(i, i+7).wrapAll("<div class='monthly-week'></div>");
								}
							}
							setMonthly(currentMonth, currentYear);
							function viewToggleButton(){
								if($('#'+uniqueId+' .monthly-event-list').is(":visible")) {
									$('#'+uniqueId+' .monthly-cal').remove();
									$('#'+uniqueId+' .monthly-header-title').prepend('<a href="#" class="monthly-cal" title="<?php echo __( 'Back To Month View', 'Total-Soft-Calendar' );?>"><div></div></a>');
								}
							}
							$(document.body).on('click', '#'+uniqueId+' .monthly-next', function (e) {
								var setMonth = $('#' + uniqueId).data('setMonth'),
									setYear = $('#' + uniqueId).data('setYear');
								if (setMonth == 12) {
									var newMonth = 1,
										newYear = setYear + 1;
									setMonthly(newMonth, newYear);
								} else {
									var newMonth = setMonth + 1,
										newYear = setYear;
									setMonthly(newMonth, newYear);
								}
								viewToggleButton();
								e.preventDefault();
							});
							$(document.body).on('click', '#'+uniqueId+' .monthly-prev', function (e) {
								var setMonth = $('#' + uniqueId).data('setMonth'),
									setYear = $('#' + uniqueId).data('setYear');
								if (setMonth == 1) {
									var newMonth = 12,
										newYear = setYear - 1;
									setMonthly(newMonth, newYear);
								} else {
									var newMonth = setMonth - 1,
										newYear = setYear;
									setMonthly(newMonth, newYear);
								}
								viewToggleButton();
								e.preventDefault();
							});
							$(document.body).on('click', '#'+uniqueId+' .monthly-reset', function (e) {
								setMonthly(currentMonth, currentYear);
								viewToggleButton();
								e.preventDefault();
								e.stopPropagation();
							});
							$(document.body).on('click', '#'+uniqueId+' .monthly-cal', function (e) {
								$(this).remove();
									$('#' + uniqueId+' .monthly-event-list').css('transform','scale(0)').delay('800').hide();
								e.preventDefault();
							});
							$(document.body).on('click', '#'+uniqueId+' a.monthly-day', function (e) {
								if(options.mode == 'event' && options.eventList == true) {
									var whichDay = $(this).data('number');
									$('#' + uniqueId+' .monthly-event-list').show();
									$('#' + uniqueId+' .monthly-event-list').css('transform');
									$('#' + uniqueId+' .monthly-event-list').css('transform','scale(1)');
									$('#' + uniqueId+' .monthly-list-item[data-number="'+whichDay+'"]').show();
									var myElement = document.getElementById(uniqueId+'day'+whichDay);
									var topPos = myElement.offsetTop;
									//document.getElementByClassname('scrolling_div').scrollTop = topPos;
									$('#'+uniqueId+' .monthly-event-list').scrollTop(topPos);
									viewToggleButton();
								} 
								e.preventDefault();
							});
							$(document.body).on('click', '#'+uniqueId+' .listed-event', function (e) {
								var href = $(this).attr('href');
								if(!href) {
									e.preventDefault();
								}
							});
							}
						});
					})(jQuery);
					jQuery(window).load( function() {
						jQuery('#totalsoftcal_<?php echo $Total_Soft_Cal;?>').monthly({
							mode: 'event',
							weekStart: '<?php echo $TotalSoftCal_Par[0]->TotalSoftCal_WDStart;?>',
						});
					});   
				</script>
	 		<?php } else if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Simple Calendar'){ ?>
	 			<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('../CSS/totalsoft.css',__FILE__);?>">
			    <link rel="stylesheet" href="<?php echo plugins_url('../CSS/jquery.e-calendar.css',__FILE__);?>"/>
			    <style type="text/css">
			    	.TotalSoftSimpleCalendar {
					    max-width: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_W;?>px;
					    height: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_H;?>px;
					    border: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BW;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BS;?> <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BC;?>;
					    <?php if($TotalSoftCal_Par[0]->TotalSoftCal2_BxShShow=='Yes'){ ?>
						    <?php if($TotalSoftCal_Par[0]->TotalSoftCal2_BxShType=='1'){ ?>
								-webkit-box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
								-moz-box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
								box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
						    <?php }else{ ?> <?php ?>
						    	-webkit-box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
								-moz-box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
								box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_BxShC;?>;
						    <?php }?>
						<?php }?>
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_DBgC;?>;
					}
					.c-grid-title 
					{
    					background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_MBgC;?>;
					}
					.c-month
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_MC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_MFS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_MFF;?>;
					}
					.c-week-day 
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_WBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_WC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_WFS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_WFF;?>;
						border-bottom: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_LAW_W;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_LAW_S;?> <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_LAW_C;?>;
					}
					.c-day
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_DBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_DC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_DFS;?>px;
					}
					.c-today 
					{
					    background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_TdBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_TdC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_TdFS;?>px;
					}
					.c-event
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_EdBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_EdC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_EdFS;?>px;
					}
					.c-event-over 
					{
					    background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_HBgC;?>;
					    color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_HC;?>;
					}
					.c-previous, .c-next
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_ArrFS;?>px;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_ArrC;?>;
					}
					.c-day-previous-month, .c-day-next-month
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_OmBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_OmC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_OmFS;?>px;
					}
					.c-event-title 
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_HBgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_HC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_HFS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_HFF;?>;
					}
					.c-event-body
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_BBgC;?>;
					}
					.c-event-item > .title, .c-event-item > .title a 
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_TC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_TFS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_TFF;?>;
					}
					.c-event-item > .description 
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_DC;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_DFS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_DFF;?>;
					}	
					.monthly-list-item:after{
					    content:"<?php echo __( 'No Events', 'Total-Soft-Calendar' );?>";
					    padding:4px 10px;
					    display:block;
					    margin-bottom:5px;
					}				
			    </style>
			    <div id="calendar"></div>
			    <script type="text/javascript">
			    	jQuery(document).ready(function () {
					    jQuery('#calendar').eCalendar({
							weekDays: ['<?php echo __( 'Sun', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Mon', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Tue', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Wed', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Thu', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Fri', 'Total-Soft-Calendar' );?>', '<?php echo __( 'Sat', 'Total-Soft-Calendar' );?>'],
					        months: ['<?php echo __( 'January', 'Total-Soft-Calendar' );?>', '<?php echo __( 'February', 'Total-Soft-Calendar' );?>', '<?php echo __( 'March', 'Total-Soft-Calendar' );?>', '<?php echo __( 'April', 'Total-Soft-Calendar' );?>', '<?php echo __( 'May', 'Total-Soft-Calendar' );?>', '<?php echo __( 'June', 'Total-Soft-Calendar' );?>', '<?php echo __( 'July', 'Total-Soft-Calendar' );?>', '<?php echo __( 'August', 'Total-Soft-Calendar' );?>', '<?php echo __( 'September', 'Total-Soft-Calendar' );?>', '<?php echo __( 'October', 'Total-Soft-Calendar' );?>', '<?php echo __( 'November', 'Total-Soft-Calendar' );?>', '<?php echo __( 'December', 'Total-Soft-Calendar' );?>'],
					        textArrows: {previous: '<i class="totalsoft totalsoft-<?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_ArrType;?>-left"></i>', next: '<i class="totalsoft totalsoft-<?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_ArrType;?>-right"></i>'},
					        eventTitle: '<?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_Ev_HText;?>',
					        url: '',
					        events: [
					        	<?php for($i=0;$i<count($Total_Soft_CalEvents);$i++){
									$TotalSoftCal_EvStartDate=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate);
									if($TotalSoftCal_EvStartDate[1][0]==0)
									{
										$TotalSoftCal_EvStartDate[1]=$TotalSoftCal_EvStartDate[1][1];
									}
									if($TotalSoftCal_EvStartDate[2][0]==0)
									{
										$TotalSoftCal_EvStartDate[2]=$TotalSoftCal_EvStartDate[2][1];
									}
									$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate=implode('-',$TotalSoftCal_EvStartDate);

									$TotalSoftCal_EvEndDate=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate);
									if($TotalSoftCal_EvEndDate[1][0]==0)
									{
										$TotalSoftCal_EvEndDate[1]=$TotalSoftCal_EvEndDate[1][1];
									}
									if($TotalSoftCal_EvEndDate[2][0]==0)
									{
										$TotalSoftCal_EvEndDate[2]=$TotalSoftCal_EvEndDate[2][1];
									}
									$Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate=implode('-',$TotalSoftCal_EvEndDate);
									if($Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab=='none')
									{
										$Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab='';
									} 
									$Total_Soft_CalEventDesc=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name6 WHERE TotalSoftCal_EvCal=%s order by id",$Total_Soft_CalEvents[$i]->id));
									
									$TotalSoftCal_EvStartDateSplit=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartDate);
									$TotalSoftCal_EvStartTimeSplit=explode(':',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvStartTime);
									$TotalSoftCal_EvEndDateSplit=explode('-',$Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndDate);

									?>
					            	{title: '<?php echo $Total_Soft_CalEvents[$i]->TotalSoftCal_EvName;?>', description: "<?php if($Total_Soft_CalEventDesc){ echo str_replace(')*^*(', '"', str_replace(")*&*(", "'", $Total_Soft_CalEventDesc[0]->TotalSoftCal_EvDesc));}?>", datetime: new Date(<?php echo $TotalSoftCal_EvStartDateSplit[0];?>, <?php echo $TotalSoftCal_EvStartDateSplit[1]-1;?>, <?php echo $TotalSoftCal_EvStartDateSplit[2];?>, <?php echo $TotalSoftCal_EvStartTimeSplit[0];?>, <?php echo $TotalSoftCal_EvStartTimeSplit[1];?>), endtime: '<?php echo $Total_Soft_CalEvents[$i]->TotalSoftCal_EvEndTime?>', eventurl: "<?php echo $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURL?>", eventnewtab: "<?php echo $Total_Soft_CalEvents[$i]->TotalSoftCal_EvURLNewTab?>", enddateyear: "<?php echo $TotalSoftCal_EvEndDateSplit[0];?>", enddatemonth: "<?php echo $TotalSoftCal_EvEndDateSplit[1]-1;?>", enddateday: "<?php echo $TotalSoftCal_EvEndDateSplit[2];?>"},
								<?php }?>
					        ],
					        firstDayOfWeek: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal2_WDStart;?>
					    });
					});
			    </script>
			<?php } else if($TotalSoftCal_Type[0]->TotalSoftCal_Type=='Flexible Calendar'){ ?>
	 			<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('../CSS/totalsoft.css',__FILE__);?>">
				<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('../CSS/calendar.css',__FILE__);?>" />
				<style type="text/css">
					.container > header, .main
					{
						max-width: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_MW;?>px;
						<?php if($TotalSoftCal_Par[0]->TotalSoftCal3_BoxShShow=='Yes'){ ?>
						    <?php if($TotalSoftCal_Par[0]->TotalSoftCal3_BoxShType=='1'){ ?>
								-webkit-box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
								-moz-box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
								box-shadow: 0 30px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px -18px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
						    <?php }else{ ?> <?php ?>
						    	-webkit-box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
								-moz-box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
								box-shadow: 0px 0px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxSh;?>px <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BoxShC;?>;
						    <?php }?>
						<?php }?>
					}
					.fc-calendar .fc-row > div:empty 
					{
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BgC;?>;
					}
					.fc-calendar .fc-row > div:empty:hover
					{
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BgC;?>;
					}
					.fc-calendar .fc-row 
					{
						border-bottom: 1px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_GrC;?>;
					}
					.fc-calendar .fc-row > div 
					{
						border-right: 1px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_GrC;?>;
					}
					.fc-calendar .fc-body 
					{
						border: 1px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_BBC;?>;
					}
					.custom-header 
					{
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_BgC;?>;
						border-top: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_BTW;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_BTC;?>;
						border-bottom: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_LAH_W;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_LAH_C;?>;
					}
					.custom-header h3
					{
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_FF;?>;
					}
					.custom-header .custom-month
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_MFS;?>px;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_MC;?>;
					}
					.custom-header .custom-year
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_YFS;?>px;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_H_YC;?>;
					}
					.custom-header nav i
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Arr_S;?>px;
					}
					.custom-header nav i:before
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Arr_C;?>;
					}
					.custom-header nav i:hover:before 
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Arr_HC;?>;
					}
					.fc-calendar .fc-head {
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_WD_BgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_WD_C;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_WD_FS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_WD_FF;?>;
					}
					.fc-calendar .fc-row > div 
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_D_BgC;?>;
					}
					.fc-calendar .fc-row > div > span.fc-date
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_D_C;?>;
					}
					.fc-calendar .fc-row > div.fc-today 
					{
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_TD_BgC;?>;
					}
					.fc-calendar .fc-row > div.fc-today > span.fc-date 
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_TD_C;?>;
					}
					.fc-calendar .fc-row > div:hover
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_HD_BgC;?>;
					}
					.fc-calendar .fc-row > div:hover span.fc-date
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_HD_C;?>;
					}
					.fc-calendar .fc-row > div.fc-content:after
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_ED_C;?>;
					}
					.fc-calendar .fc-row > div.fc-content:hover:after
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_ED_HC;?>;
					}
					.custom-content-reveal h4 
					{
						border-top: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_BTW;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_BTC;?>;
						background: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_BgC;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_C;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_FS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_FF;?>;
						border-bottom: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_LAH_W;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_LAH_C;?>;
					}
					.custom-content-reveal i.custom-content-close
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_C_C;?>;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_C_FS;?>px;
					}
					.custom-content-reveal i.custom-content-close:hover
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_C_HC;?>;
					}
					.custom-content-reveal 
					{
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_B_BgC;?>;
						border: 1px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_B_BC;?>;
						border-top: none;
						overflow: overlay;
					}
					.TotalSoftcalEvent_Title
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_T_FS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_T_FF;?> !important;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_T_C;?> !important;
						background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_T_BgC;?> !important;
						text-align: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_T_TA;?> !important;
						padding: 5px 10px;
						margin: 10px 0;
					}
					.TotalSoftcalEvent_Link
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_C;?> !important;
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_FS;?>px !important;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_FF;?> !important;
						border: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_BW;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_BC;?> !important;
						border-radius: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_BR;?>px !important;
						padding: 5px 10px;
					}
					.TotalSoftcalEvent_LinkMar
					{
						margin: 0px 10px;
					}
					.TotalSoftcalEvent_Link:hover
					{
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_L_HC;?> !important;
						text-decoration: none;
					}
					.TotalSoftcalEvent_Media
					{
						width: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_I_W;?>%;
						height: auto;
					}
					.TotalSoftcalEvent_Mediadiv
					{
						width: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_I_W;?>%;
						position: relative;
						display: inline-block;
					}
					.TotalSoftcalEvent_Mediadiv:after
					{
						padding-top: 56.25% !important;
						/* 16:9 ratio */
						display: block;
						content: '';
					}
					.TotalSoftcalEvent_Mediaiframe
					{
						width: 100% !important;
						height: 100% !important;
						left: 0;
						position: absolute;
					}
					.TotalSoftcalEvent_Desc
					{
						font-size: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_D_FS;?>px;
						font-family: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_D_FF;?>;
						color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_D_C;?> !important;
						text-align: justify;
						padding: 10px 30px 10px 10px;
						line-height: 1 !important;
					}
					.TotalSoftcalEvent_LAE
					{
						width: 85%;
						position: relative;
						margin: 10px auto;
						border-top: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_LAE_W;?>px solid <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_LAE_C;?>;
					}
					.custom-content-reveal::-webkit-scrollbar 
					{
					    width: 10px;
					}					 
					.custom-content-reveal::-webkit-scrollbar-track 
					{
					    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
					}
					.custom-content-reveal::-webkit-scrollbar-thumb 
					{
					  	background-color: <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_BTC;?>;
					  	outline: 1px solid slategrey;
					}
				</style>
				<script src="<?php echo plugins_url('../JS/modernizr.custom.63321.js',__FILE__);?>"></script>
				<div class="container">	
					<section class="main">
						<div class="custom-calendar-wrap">
							<div id="custom-inner" class="custom-inner">
								<div class="custom-header clearfix">
									<nav>
										<i id="custom-prev" class="totalsoft totalsoft-<?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Arr_Type;?>-left "></i>
										<i id="custom-next" class="totalsoft totalsoft-<?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Arr_Type;?>-right"></i>
									</nav>
									<?php if($TotalSoftCal_Par[0]->TotalSoftCal3_H_Format=='1'){ ?>
										<h3 id="custom-year" class="custom-year"></h3>
										<h3 id="custom-month" class="custom-month"></h3>
									<?php }else if($TotalSoftCal_Par[0]->TotalSoftCal3_H_Format=='2'){ ?>
										<h3 id="custom-month" class="custom-month"></h3>
										<h3 id="custom-year" class="custom-year"></h3>
									<?php }else if($TotalSoftCal_Par[0]->TotalSoftCal3_H_Format=='3'){ ?>
										<h3>
											<span id="custom-year" class="custom-year"></span>
											<span id="custom-month" class="custom-month"></span>
										</h3>
									<?php }else{ ?>
										<h3>
											<span id="custom-month" class="custom-month"></span>
											<span id="custom-year" class="custom-year"></span>
										</h3>
									<?php }?>
									
								</div>
								<div id="calendar" class="fc-calendar-container"></div>
							</div>
						</div>
					</section>
				</div><!-- /container -->
				<script type="text/javascript" src="<?php echo plugins_url('../JS/jquery.calendario.js',__FILE__);?>"></script>
				
				<script type="text/javascript">
					var codropsEvents = {
						<?php for($i=0;$i<count($Total_Soft_CalEvents_Date_Real);$i++){ ?>
							'<?php echo $Total_Soft_CalEvents_Date_Real[$i];?>' : "<?php echo $Total_Soft_CalEvents_Desc_Real[$i];?>",			            	
						<?php }?>
					};
				</script>
				<script type="text/javascript">	
					jQuery(function() {			
						var transEndEventNames = {
								'WebkitTransition' : 'webkitTransitionEnd',
								'MozTransition' : 'transitionend',
								'OTransition' : 'oTransitionEnd',
								'msTransition' : 'MSTransitionEnd',
								'transition' : 'transitionend'
							},
							transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
							$wrapper = jQuery( '#custom-inner' ),
							$calendar = jQuery( '#calendar' ),
							cal = $calendar.calendario( {
								onDayClick : function( $el, $contentEl, dateProperties ) {
									if( $contentEl.length > 0 ) {
										showEvents( $contentEl, dateProperties );
									}
								},
								caldata : codropsEvents,
								displayWeekAbbr : true,
								displayMonthAbbr : false,
								startIn : <?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_WDStart;?>,
							} ),
							$month = jQuery( '#custom-month' ).html( cal.getMonthName() ),
							$year = jQuery( '#custom-year' ).html( cal.getYear() );
						jQuery( '#custom-next' ).on( 'click', function() {
							cal.gotoNextMonth( updateMonthYear );
						} );
						jQuery( '#custom-prev' ).on( 'click', function() {
							cal.gotoPreviousMonth( updateMonthYear );
						} );
						function updateMonthYear() {				
							$month.html( cal.getMonthName() );
							$year.html( cal.getYear() );
						}
						// just an example..
						function showEvents( $contentEl, dateProperties ) {
							hideEvents();
							<?php if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_Format == '1'){ ?> 
								var $events = jQuery( '<div id="custom-content-reveal" class="custom-content-reveal"><h4>' + dateProperties.monthname + ' ' + dateProperties.day + ', ' + dateProperties.year + '</h4></div>' ),
							<?php } else if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_Format == '2'){ ?>
								var $events = jQuery( '<div id="custom-content-reveal" class="custom-content-reveal"><h4>' + dateProperties.year + ' ' + dateProperties.monthname  + ' ' + dateProperties.day + '</h4></div>' ),
							<?php } else if($TotalSoftCal_Par[0]->TotalSoftCal3_Ev_Format == '3'){ ?>
								var $events = jQuery( '<div id="custom-content-reveal" class="custom-content-reveal"><h4>' + dateProperties.day + ' ' + dateProperties.monthname + ' ' + dateProperties.year + '</h4></div>' ),
							<?php }?>							
								$close = jQuery( '<i class="custom-content-close totalsoft totalsoft-<?php echo $TotalSoftCal_Par[0]->TotalSoftCal3_Ev_C_Type;?>"></i>' ).on( 'click', hideEvents );
							$events.append( $contentEl.html() , $close ).insertAfter( $wrapper );
							setTimeout( function() {
								$events.css( 'top', '0%' );
							}, 25 );
						}
						function hideEvents() {
							var $events = jQuery( '#custom-content-reveal' );
							if( $events.length > 0 ) {
								$events.css( 'top', '100%' );
								Modernizr.csstransitions ? $events.on( transEndEventName, function() { jQuery( this ).remove(); } ) : $events.remove();
							}
						}
					});
				</script>
	 		<?php } 
 		 	echo $after_widget;
 		}
	}
?>