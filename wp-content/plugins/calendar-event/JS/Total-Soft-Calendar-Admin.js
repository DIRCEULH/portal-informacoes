function TotalSoftCal_Edit(Total_Soft_Cal_ID)
{
	var ajaxurl = object.ajaxurl;
	var data = {
	action: 'TotalSoftCal_Edit', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
	foobar: Total_Soft_Cal_ID, // translates into $_POST['foobar'] in PHP
	};
	jQuery.post(ajaxurl, data, function(response) {
		var b=Array();
		var a=response.split('=>');
		for(var i=3;i<a.length;i++)
		{ b[b.length]=a[i].split('[')[0].trim(); }
		b[b.length-1]=b[b.length-1].split(')')[0];
		jQuery('.Total_Soft_Cal_AMD2').hide(500);
		jQuery('.Total_Soft_AMMTable').hide(500);
		jQuery('.Total_Soft_AMOTable').hide(500);
		jQuery('.Total_Soft_Cal_Save').hide(500);
		jQuery('.Total_Soft_Cal_Update').show(500);
		jQuery('.Total_Soft_Cal_ID').html('[Total_Soft_Cal id="'+Total_Soft_Cal_ID+'"]');
		jQuery('.Total_Soft_Cal_TID').html('&lt;?php echo do_shortcode("[Total_Soft_Cal id='+'"'+Total_Soft_Cal_ID+'"'+']");?&gt');
		jQuery('#TotalSoftCal_Name').val(b[1]);	
		jQuery('#TotalSoftCal_Type').val(b[2]);	
		jQuery('#TotalSoftCal_Type').hide();
		setTimeout(function(){
			jQuery('#Total_SoftCal_Update').val(Total_Soft_Cal_ID);
			jQuery('.Total_Soft_AMSetTable_Main').show(500);
			if(b[2]=='Event Calendar')
			{
				jQuery('#TotalSoftCal_BgCol').val(b[3]); 
				jQuery('#TotalSoftCal_GrCol').val(b[4]); 
				jQuery('#TotalSoftCal_GW').val(b[5]); 
				jQuery('#TotalSoftCal_GW_Output').val(b[5]+'px'); 
				jQuery('#TotalSoftCal_BW').val(b[6]); 
				jQuery('#TotalSoftCal_BW_Output').val(b[6]+'px'); 
				jQuery('#TotalSoftCal_BStyle').val(b[7]); 
				jQuery('#TotalSoftCal_BCol').val(b[8]); 
				jQuery('#TotalSoftCal_BSCol').val(b[9]); 
				jQuery('#TotalSoftCal_MW').val(b[10]); 
				jQuery('#TotalSoftCal_MW_Output').val(b[10]+'px'); 
				jQuery('#TotalSoftCal_HBgCol').val(b[11]); 
				jQuery('#TotalSoftCal_HCol').val(b[12]); 
				jQuery('#TotalSoftCal_HFS').val(b[13]); 
				jQuery('#TotalSoftCal_HFS_Output').val(b[13]+'px');	
				jQuery('#TotalSoftCal_HFF').val(b[14]);	
				jQuery('#TotalSoftCal_WBgCol').val(b[15]); 
				jQuery('#TotalSoftCal_WCol').val(b[16]);	
				jQuery('#TotalSoftCal_WFS').val(b[17]);	
				jQuery('#TotalSoftCal_WFS_Output').val(b[17]+'px');	
				jQuery('#TotalSoftCal_WFF').val(b[18]);	
				jQuery('#TotalSoftCal_LAW').val(b[19]);	
				jQuery('#TotalSoftCal_LAW_Output').val(b[19]+'px');	
				jQuery('#TotalSoftCal_LAWS').val(b[20]); 
				jQuery('#TotalSoftCal_LAWC').val(b[21]); 
				jQuery('#TotalSoftCal_DBgCol').val(b[22]); 
				jQuery('#TotalSoftCal_DCol').val(b[23]); 
				jQuery('#TotalSoftCal_DFS').val(b[24]); 
				jQuery('#TotalSoftCal_DFS_Output').val(b[24]+'px'); 
				jQuery('#TotalSoftCal_TBgCol').val(b[25]); 
				jQuery('#TotalSoftCal_TCol').val(b[26]); 
				jQuery('#TotalSoftCal_TFS').val(b[27]); 
				jQuery('#TotalSoftCal_TFS_Output').val(b[27]+'px'); 
				jQuery('#TotalSoftCal_TNBgCol').val(b[28]); 
				jQuery('#TotalSoftCal_HovBgCol').val(b[29]); 
				jQuery('#TotalSoftCal_HovCol').val(b[30]); 
				jQuery('#TotalSoftCal_NumPos').val(b[31]); 
				jQuery('#TotalSoftCal_WDStart').val(b[32]); 
				jQuery('#TotalSoftCal_RefIcCol').val(b[33]); 
				jQuery('#TotalSoftCal_RefIcSize').val(b[34]); 
				jQuery('#TotalSoftCal_RefIcSize_Output').val(b[34]+'px'); 
				jQuery('#TotalSoftCal_ArrowType').val(b[35]); 
				jQuery('#TotalSoftCal_ArrowCol').val(b[38]); 
				jQuery('#TotalSoftCal_ArrowSize').val(b[39]); 
				jQuery('#TotalSoftCal_ArrowSize_Output').val(b[39]+'px');

				jQuery('.Total_Soft_AMSetTable_1').show(500);
			}
			else if(b[2]=='Simple Calendar')
			{
				jQuery('#TotalSoftCal2_WDStart').val(b[3]);
				jQuery('#TotalSoftCal2_BW').val(b[4]);
				jQuery('#TotalSoftCal2_BW_Output').val(b[4]+'px');
				jQuery('#TotalSoftCal2_BS').val(b[5]);
				jQuery('#TotalSoftCal2_BC').val(b[6]);
				jQuery('#TotalSoftCal2_W').val(b[7]);
				jQuery('#TotalSoftCal2_W_Output').val(b[7]+'px');
				jQuery('#TotalSoftCal2_H').val(b[8]);
				jQuery('#TotalSoftCal2_H_Output').val(b[8]+'px');
				jQuery('#TotalSoftCal2_BxShShow').val(b[9]);
				jQuery('#TotalSoftCal2_BxShType').val(b[10]);
				jQuery('#TotalSoftCal2_BxSh').val(b[11]);
				jQuery('#TotalSoftCal2_BxSh_Output').val(b[11]+'px');
				jQuery('#TotalSoftCal2_BxShC').val(b[12]);
				jQuery('#TotalSoftCal2_MBgC').val(b[13]);
				jQuery('#TotalSoftCal2_MC').val(b[14]);
				jQuery('#TotalSoftCal2_MFS').val(b[15]);
				jQuery('#TotalSoftCal2_MFS_Output').val(b[15]+'px');
				jQuery('#TotalSoftCal2_MFF').val(b[16]);
				jQuery('#TotalSoftCal2_WBgC').val(b[17]);
				jQuery('#TotalSoftCal2_WC').val(b[18]);
				jQuery('#TotalSoftCal2_WFS').val(b[19]);
				jQuery('#TotalSoftCal2_WFS_Output').val(b[19]+'px');
				jQuery('#TotalSoftCal2_WFF').val(b[20]);
				jQuery('#TotalSoftCal2_LAW_W').val(b[21]);
				jQuery('#TotalSoftCal2_LAW_W_Output').val(b[21]+'px');
				jQuery('#TotalSoftCal2_LAW_S').val(b[22]);
				jQuery('#TotalSoftCal2_LAW_C').val(b[23]);
				jQuery('#TotalSoftCal2_DBgC').val(b[24]);
				jQuery('#TotalSoftCal2_DC').val(b[25]);
				jQuery('#TotalSoftCal2_DFS').val(b[26]);
				jQuery('#TotalSoftCal2_DFS_Output').val(b[26]+'px');
				jQuery('#TotalSoftCal2_TdBgC').val(b[27]);
				jQuery('#TotalSoftCal2_TdC').val(b[28]);
				jQuery('#TotalSoftCal2_TdFS').val(b[29]);
				jQuery('#TotalSoftCal2_TdFS_Output').val(b[29]+'px');
				jQuery('#TotalSoftCal2_EdBgC').val(b[30]);
				jQuery('#TotalSoftCal2_EdC').val(b[31]);
				jQuery('#TotalSoftCal2_EdFS').val(b[32]);
				jQuery('#TotalSoftCal2_EdFS_Output').val(b[32]+'px');
				jQuery('#TotalSoftCal2_HBgC').val(b[33]);
				jQuery('#TotalSoftCal2_HC').val(b[34]);
				jQuery('#TotalSoftCal2_ArrType').val(b[35]);
				jQuery('#TotalSoftCal2_ArrFS').val(b[36]);
				jQuery('#TotalSoftCal2_ArrFS_Output').val(b[36]+'px');
				jQuery('#TotalSoftCal2_ArrC').val(b[37]);
				jQuery('#TotalSoftCal2_OmBgC').val(b[38]);
				jQuery('#TotalSoftCal2_OmC').val(b[39]);
				jQuery('#TotalSoftCal2_OmFS').val(b[40]);
				jQuery('#TotalSoftCal2_OmFS_Output').val(b[40]+'px');
				jQuery('#TotalSoftCal2_Ev_HBgC').val(b[41]);
				jQuery('#TotalSoftCal2_Ev_HC').val(b[42]);
				jQuery('#TotalSoftCal2_Ev_HFS').val(b[43]);
				jQuery('#TotalSoftCal2_Ev_HFS_Output').val(b[43]+'px');
				jQuery('#TotalSoftCal2_Ev_HFF').val(b[44]);
				jQuery('#TotalSoftCal2_Ev_HText').val(b[45]);
				jQuery('#TotalSoftCal2_Ev_BBgC').val(b[46]);
				jQuery('#TotalSoftCal2_Ev_TC').val(b[47]);
				jQuery('#TotalSoftCal2_Ev_TFF').val(b[48]);
				jQuery('#TotalSoftCal2_Ev_TFS').val(b[49]);
				jQuery('#TotalSoftCal2_Ev_TFS_Output').val(b[49]+'px');
				jQuery('#TotalSoftCal2_Ev_DC').val(b[50]);
				jQuery('#TotalSoftCal2_Ev_DFF').val(b[51]);
				jQuery('#TotalSoftCal2_Ev_DFS').val(parseInt(b[52]));
				jQuery('#TotalSoftCal2_Ev_DFS_Output').val(parseInt(b[52])+'px');

				jQuery('.Total_Soft_AMSetTable_2').show(500);
			}
			else if(b[2]=='Flexible Calendar')
			{
				jQuery('#TotalSoftCal3_MW').val(b[3]);
				jQuery('#TotalSoftCal3_MW_Output').val(b[3]+'px');
				jQuery('#TotalSoftCal3_WDStart').val(b[4]);
				jQuery('#TotalSoftCal3_BgC').val(b[5]);
				jQuery('#TotalSoftCal3_GrC').val(b[6]);
				jQuery('#TotalSoftCal3_BBC').val(b[7]);
				jQuery('#TotalSoftCal3_BoxShShow').val(b[8]);
				jQuery('#TotalSoftCal3_BoxShType').val(b[9]);
				jQuery('#TotalSoftCal3_BoxSh').val(b[10]);
				jQuery('#TotalSoftCal3_BoxSh_Output').val(b[10]+'px');
				jQuery('#TotalSoftCal3_BoxShC').val(b[11]);
				jQuery('#TotalSoftCal3_H_BgC').val(b[12]);
				jQuery('#TotalSoftCal3_H_BTW').val(b[13]);
				jQuery('#TotalSoftCal3_H_BTW_Output').val(b[13]+'px');
				jQuery('#TotalSoftCal3_H_BTC').val(b[14]);
				jQuery('#TotalSoftCal3_H_FF').val(b[15]);
				jQuery('#TotalSoftCal3_H_MFS').val(b[16]);
				jQuery('#TotalSoftCal3_H_MFS_Output').val(b[16]+'px');
				jQuery('#TotalSoftCal3_H_MC').val(b[17]);
				jQuery('#TotalSoftCal3_H_YFS').val(b[18]);
				jQuery('#TotalSoftCal3_H_YFS_Output').val(b[18]+'px');
				jQuery('#TotalSoftCal3_H_YC').val(b[19]);
				jQuery('#TotalSoftCal3_H_Format').val(b[20]);
				jQuery('#TotalSoftCal3_Arr_Type').val(b[21]);
				jQuery('#TotalSoftCal3_Arr_C').val(b[22]);
				jQuery('#TotalSoftCal3_Arr_S').val(b[23]);
				jQuery('#TotalSoftCal3_Arr_S_Output').val(b[23]+'px');
				jQuery('#TotalSoftCal3_Arr_HC').val(b[24]);
				jQuery('#TotalSoftCal3_LAH_W').val(b[25]);
				jQuery('#TotalSoftCal3_LAH_W_Output').val(b[25]+'px');
				jQuery('#TotalSoftCal3_LAH_C').val(b[26]);
				jQuery('#TotalSoftCal3_WD_BgC').val(b[27]);
				jQuery('#TotalSoftCal3_WD_C').val(b[28]);
				jQuery('#TotalSoftCal3_WD_FS').val(b[29]);
				jQuery('#TotalSoftCal3_WD_FS_Output').val(b[29]+'px');
				jQuery('#TotalSoftCal3_WD_FF').val(b[30]);
				jQuery('#TotalSoftCal3_D_BgC').val(b[31]);
				jQuery('#TotalSoftCal3_D_C').val(b[32]);
				jQuery('#TotalSoftCal3_TD_BgC').val(b[33]);
				jQuery('#TotalSoftCal3_TD_C').val(b[34]);
				jQuery('#TotalSoftCal3_HD_BgC').val(b[35]);
				jQuery('#TotalSoftCal3_HD_C').val(b[36]);
				jQuery('#TotalSoftCal3_ED_C').val(b[37]);
				jQuery('#TotalSoftCal3_ED_HC').val(b[38]);
				jQuery('#TotalSoftCal3_Ev_Format').val(b[39]);
				jQuery('#TotalSoftCal3_Ev_BTW').val(b[40]);
				jQuery('#TotalSoftCal3_Ev_BTW_Output').val(b[40]+'px');
				jQuery('#TotalSoftCal3_Ev_BTC').val(b[41]);
				jQuery('#TotalSoftCal3_Ev_BgC').val(b[42]);
				jQuery('#TotalSoftCal3_Ev_C').val(b[43]);
				jQuery('#TotalSoftCal3_Ev_FS').val(b[44]);
				jQuery('#TotalSoftCal3_Ev_FS_Output').val(b[44]+'px');
				jQuery('#TotalSoftCal3_Ev_FF').val(b[45]);
				jQuery('#TotalSoftCal3_Ev_C_Type').val(b[46]);
				jQuery('#TotalSoftCal3_Ev_C_C').val(b[47]);
				jQuery('#TotalSoftCal3_Ev_C_HC').val(b[48]);
				jQuery('#TotalSoftCal3_Ev_C_FS').val(b[49]);
				jQuery('#TotalSoftCal3_Ev_C_FS_Output').val(b[49]+'px');
				jQuery('#TotalSoftCal3_Ev_LAH_W').val(b[50]);
				jQuery('#TotalSoftCal3_Ev_LAH_W_Output').val(b[50]+'px');
				jQuery('#TotalSoftCal3_Ev_LAH_C').val(b[51]);
				jQuery('#TotalSoftCal3_Ev_B_BgC').val(b[52]);
				jQuery('#TotalSoftCal3_Ev_B_BC').val(b[53]);
				jQuery('#TotalSoftCal3_Ev_T_FS').val(b[54]);
				jQuery('#TotalSoftCal3_Ev_T_FS_Output').val(b[54]+'px');
				jQuery('#TotalSoftCal3_Ev_T_FF').val(b[55]);
				jQuery('#TotalSoftCal3_Ev_T_BgC').val(b[56]);
				jQuery('#TotalSoftCal3_Ev_T_C').val(b[57]);
				jQuery('#TotalSoftCal3_Ev_T_TA').val(b[58]);
				jQuery('#TotalSoftCal3_Ev_D_FS').val(b[59]);
				jQuery('#TotalSoftCal3_Ev_D_FS_Output').val(b[59]+'px');
				jQuery('#TotalSoftCal3_Ev_D_FF').val(b[60]);
				jQuery('#TotalSoftCal3_Ev_D_C').val(b[61]);
				jQuery('#TotalSoftCal3_Ev_I_W').val(b[62]);
				jQuery('#TotalSoftCal3_Ev_I_W_Output').val(b[62]+'%');
				jQuery('#TotalSoftCal3_Ev_I_Pos').val(b[63]);
				jQuery('#TotalSoftCal3_Ev_L_C').val(b[64]);
				jQuery('#TotalSoftCal3_Ev_L_HC').val(b[65]);
				jQuery('#TotalSoftCal3_Ev_L_Pos').val(b[66]);
				jQuery('#TotalSoftCal3_Ev_L_Text').val(b[67]);
				jQuery('#TotalSoftCal3_Ev_LAE_W').val(b[68]);
				jQuery('#TotalSoftCal3_Ev_LAE_W_Output').val(b[68]+'px');
				jQuery('#TotalSoftCal3_Ev_LAE_C').val(b[69]);
				jQuery('#TotalSoftCal3_Ev_L_FS').val(b[70]);
				jQuery('#TotalSoftCal3_Ev_L_FS_Output').val(b[70]+'px');
				jQuery('#TotalSoftCal3_Ev_L_FF').val(b[71]);
				jQuery('#TotalSoftCal3_Ev_L_BW').val(b[72]);
				jQuery('#TotalSoftCal3_Ev_L_BW_Output').val(b[72]+'px');
				jQuery('#TotalSoftCal3_Ev_L_BC').val(b[73]);
				jQuery('#TotalSoftCal3_Ev_L_BR').val(parseInt(b[74]));
				jQuery('#TotalSoftCal3_Ev_L_BR_Output').val(parseInt(b[74])+'px');

				jQuery('.Total_Soft_AMSetTable_3').show(500);
			}
			jQuery('.Total_Soft_Cal_AMD3').show(500);
			jQuery('.Total_Soft_AMShortTable').show(500);
			jQuery('.Total_Soft_Color').wpColorPicker();
		},500)
	})
}
function TotalSoft_Reload()
{
	location.reload();
}
function TotalSoftCal_EditEv(Total_Soft_CalEv_ID)
{
	var ajaxurl = object.ajaxurl;
	var data = {
	action: 'TotalSoftCal_EditEv', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
	foobar: Total_Soft_CalEv_ID, // translates into $_POST['foobar'] in PHP
	};
	jQuery.post(ajaxurl, data, function(response) {

		var b=Array();
		var a=response.split('=>');
		for(var i=3;i<a.length;i++)
		{ b[b.length]=a[i].split('[')[0].trim(); }
		b[b.length-1]=b[b.length-1].split(')')[0];
		jQuery('.Total_Soft_Cal_Save_Ev').hide(500);
		jQuery('.Total_Soft_Cal_Update_Ev').show(500);

		jQuery('#Total_SoftCal_EvUpdate').val(Total_Soft_CalEv_ID);
		jQuery('#TotalSoftCal_EvName').val(b[0]);			
		jQuery('#TotalSoftCal_EvCal').val(b[1]);			
		jQuery('#TotalSoftCal_EvStartDate').val(b[2]);			
		jQuery('#TotalSoftCal_EvEndDate').val(b[3]);			
		jQuery('#TotalSoftCal_EvURL').val(b[4]);			
		jQuery('#TotalSoftCal_EvURLNewTab').val(b[5]);			
		jQuery('#TotalSoftCal_EvStartTime').val(b[6]);			
		jQuery('#TotalSoftCal_EvEndTime').val(b[7]);			
		jQuery('#TotalSoftCal_EvColor').val(b[8]);		
		setTimeout(function(){
			jQuery('.Total_Soft_AMEvTable').fadeIn(500);
			jQuery('.Total_Soft_Color').wpColorPicker();
		},500)	
	})

	var ajaxurl = object.ajaxurl;
	var data = {
	action: 'TotalSoftCal_EditEv_Desc', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
	foobar: Total_Soft_CalEv_ID, // translates into $_POST['foobar'] in PHP
	};
	jQuery.post(ajaxurl, data, function(response) {
		if(response!='none')
		{
			var b=Array();
			var a=response.split('=>');
			for(var i=3;i<a.length;i++)
			{ b[b.length]=a[i].split('[')[0].trim(); }
			b[b.length-1]=b[b.length-1].split(')')[0];

			b[0]=b[0].replace(')*^*(', '"');
			b[0]=b[0].replace(")*&*(", "'");
			jQuery('#TotalSoftCal_EvDesc').val(b[0]);
			jQuery('#TotalSoftCalendar_URL_Image_2').val(b[1]);
			jQuery('#TotalSoftCalendar_URL_Video_2').val(b[2]);
			jQuery('#TotalSoftCalendar_URL_Video_1').val(b[3]);
		}
	})
}
function TotalSoftCal_DelEv(Total_Soft_CalEv_ID)
{
	var ajaxurl = object.ajaxurl;
	var data = {
	action: 'TotalSoftCal_DelEv', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
	foobar: Total_Soft_CalEv_ID, // translates into $_POST['foobar'] in PHP
	};
	jQuery.post(ajaxurl, data, function(response) {
		location.reload();
	})
}
function Total_Soft_CalEv_AMD2_But1()
{
	jQuery('.Total_Soft_AMEvTable').fadeIn(500);
	jQuery('.Total_Soft_Color').wpColorPicker();
}
function TotalSoftCalendar_URL_Clicked()
{
	var nIntervId = setInterval(function(){
		var code = jQuery('#TotalSoftCalendar_URL_1').val();		

		if(code.indexOf('https://www.youtube.com/')>0)
		{
			var TotalSoftCodes1 = code.split('<a href="https://www.youtube.com/');
			var TotalSoftGallery_Video_Video = code.split('<a href="')[1].split('">')[0]; 
			jQuery('#TotalSoftCalendar_URL_Video_1').val(TotalSoftGallery_Video_Video); 
			if(code.indexOf('list')>0 || code.indexOf('index')>0)
			{
				var TotalSoftCodes2= TotalSoftCodes1[1].split("=");
				var TotalSoftCodeSrc = TotalSoftCodes2[1].split('&');

				jQuery('#TotalSoftCalendar_URL_Video_2').val('https://www.youtube.com/embed/'+TotalSoftCodeSrc[0]);
				jQuery('#TotalSoftCalendar_URL_Image_2').val('http://img.youtube.com/vi/'+TotalSoftCodeSrc[0]+'/mqdefault.jpg');
				if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
					clearInterval(nIntervId);
				}				
			}
			else if(code.indexOf('embed')>0)
			{
				var TotalSoftCodes1=code.split('[embed]');
				var TotalSoftCodes2=TotalSoftCodes1[1].split('[/embed]');
				if(TotalSoftCodes2[0].indexOf('watch?')>0)
				{
					var TotalSoftCodes3=TotalSoftCodes2[0].split('=');
					
					jQuery('#TotalSoftCalendar_URL_Video_2').val('https://www.youtube.com/embed/'+TotalSoftCodes3[1]);
					jQuery('#TotalSoftCalendar_URL_Image_2').val('http://img.youtube.com/vi/'+TotalSoftCodes3[1]+'/mqdefault.jpg');
					if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
						clearInterval(nIntervId);
					}	
				}
				else
				{
					var TotalSoftCodeSrc=TotalSoftCodes2[0];
					var TotalSoftImsrc=TotalSoftCodeSrc.split('embed/');

					jQuery('#TotalSoftCalendar_URL_Video_2').val(TotalSoftCodeSrc);
					jQuery('#TotalSoftCalendar_URL_Image_2').val('http://img.youtube.com/vi/'+TotalSoftImsrc[1]+'/mqdefault.jpg');
					if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
						clearInterval(nIntervId);
					}	
				}
			}
			else
			{
				var TotalSoftCodes2= TotalSoftCodes1[1].split('=');
				var TotalSoftCodeSrc = TotalSoftCodes2[1].split('">https://');

				jQuery('#TotalSoftCalendar_URL_Video_2').val('https://www.youtube.com/embed/'+TotalSoftCodeSrc[0]);
				jQuery('#TotalSoftCalendar_URL_Image_2').val('http://img.youtube.com/vi/'+TotalSoftCodeSrc[0]+'/mqdefault.jpg');
				if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
					clearInterval(nIntervId);
				}				
			}	
		}
		else if(code.indexOf('https://youtu.be/')>0)
		{
			var TotalSoftCodes1 = code.split('<a href="https://youtu.be/'); 
			var TotalSoftCodeSrc = TotalSoftCodes1[1].split('">https://');

			jQuery('#TotalSoftCalendar_URL_Video_2').val('https://www.youtube.com/embed/'+TotalSoftCodeSrc[0]);
			jQuery('#TotalSoftCalendar_URL_Image_2').val('http://img.youtube.com/vi/'+TotalSoftCodeSrc[0]+'/mqdefault.jpg');
			jQuery('#TotalSoftCalendar_URL_Video_1').val('https://www.youtube.com/watch?v='+TotalSoftCodeSrc[0]);			

			if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
				clearInterval(nIntervId);
			}				
		}
		else if(code.indexOf('https://vimeo.com/')>0)
		{
			if(code.indexOf('embed')>0)
			{
				var s1=code.split('[embed]https://vimeo.com/');
				var src=s1[1].split('[/embed]');
				if(src[0].length>9)
				{
					var real_src=src[0].split('/');
					src[0]=real_src[2];
				}
				jQuery('#TotalSoftCalendar_URL_Video_2').val('https://player.vimeo.com/video/'+src[0]);
				jQuery('#TotalSoftCalendar_URL_Video_1').val('https://vimeo.com/'+src[0]);			

				var ajaxurl = object.ajaxurl;
				var data = {
				action: 'TSoftCal_Vimeo_Video_Image', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
				foobar: 'https://player.vimeo.com/video/'+src[0], // translates into $_POST['foobar'] in PHP
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#TotalSoftCalendar_URL_Image_2').val(response);
					if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
						clearInterval(nIntervId);
					}				
				});		
   			}
			else
			{
				var s1 = code.split('<a href="https://vimeo.com/'); 
				var src = s1[1].split('">https://');
				if(src[0].length>9)
				{
					var real_src=src[0].split('/');
					src[0]=real_src[2];
				}
				jQuery('#TotalSoftCalendar_URL_Video_2').val('https://player.vimeo.com/video/'+src[0]);
				jQuery('#TotalSoftCalendar_URL_Video_1').val('https://vimeo.com/'+src[0]);			

				var ajaxurl = object.ajaxurl;
				var data = {
				action: 'TSoftCal_Vimeo_Video_Image', // wp_ajax_my_action / wp_ajax_nopriv_my_action in ajax.php. Can be named anything.
				foobar: 'https://player.vimeo.com/video/'+src[0], // translates into $_POST['foobar'] in PHP
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#TotalSoftCalendar_URL_Image_2').val(response);
					if(jQuery('#TotalSoftCalendar_URL_Video_2').val().length>0){
						clearInterval(nIntervId);
					}				
				});		
			}		
		}
		else if(code.indexOf('img')>0)
		{
			var s=code.split('src="'); 
			var src=s[1].split('"');
			jQuery('#TotalSoftCalendar_URL_Video_1').val(src[0]);	
			jQuery('#TotalSoftCalendar_URL_Image_2').val(src[0]);
			if(jQuery('#TotalSoftCalendar_URL_Image_2').val().length>0){
				clearInterval(nIntervId);
			}				
		}		
	},100)	
}
function Total_Soft_Calendar_Free_Options()
{
	alert("Those options are given for free version. You can change those values but can't save them. For changing and saving you must buy pro versions.");
}