<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('master','','');
$x->permissions('mod',1);
//if(!ALP_TOURNAMENT_MODE) $x->add_notes('mod','if you are using the IP authentication method, PLEASE make sure that your DHCP server is working correctly before you go live.  if you change this feature from authenticating from ip to using passwords and users have no passwords in the database, there could be problems.  serious problems.');

$skins = get_skinlist();
$dates = get_datelist();
$languages = get_langlist();

$x->add_select('internetmode',1,1,0,get_lang('desc_internetmode'),array("" => ""),array(1=>get_lang('yes'),0=>get_lang('no')));
//$x->add_hidden_dos('currentlanguage','en'); 
$x->add_select('currentlanguage',1,1,0,get_lang('desc_currentlanguage'),array("empty" => get_lang('error_currentlanguage')),$languages);
$x->add_select('currentskin',1,1,0,get_lang('desc_currentskin'),array("empty" => get_lang('error_currentskin')),$skins);
$x->add_checkbox('skin_override',0,1,0,get_lang('desc_skin_override'),array(),0);
$x->add_select('dateformat',1,1,0,get_lang('desc_dateformat'),array("empty" => get_lang('error_dateformat')),$dates);
$x->add_checkbox('ip_register_lock',0,1,0,get_lang('desc_ip_register_lock'),array(),0);
if(!ALP_TOURNAMENT_MODE) {
	$x->add_checkbox('useskinforcaffeine',0,1,0,get_lang('desc_useskinforcaffeine'),array(),0);
	//$x->add_checkbox('authbyiponly',0,1,0,"instead of passwords, authenticate by ip only? (WARNING: MAKE SURE YOUR DHCP SETTINGS ARE CORRECT)",array(),0);
	//$x->add_checkbox('doublecheckpassword',0,1,0,"if authenticating by ip only, use passwords at registration as a backup plan? (just in case you want to switch to passwords later because of technical problems) -- RECOMMENDED",array(),0);
	
	$x->add_checkbox('loginselect',0,1,0,get_lang('desc_loginselect'),array(),0);
	$x->add_checkbox('proficiencylock',0,1,0,get_lang('desc_proficiencylock'),array(),0);
	$x->add_checkbox('alldates',0,1,0,get_lang('desc_alldates'),array(),0);
	$x->add_checkbox('pollsguest',0,1,0,get_lang('desc_pollsguest'),array(),0);
	//$x->add_checkbox('caching',0,1,0,"allow caching of tournament display pages?",array(),0);
	$x->add_text('max_file_upload_size',0,1,0,get_lang('desc_max_file_upload_size'),array(),20);
	$x->add_text('policyurl',0,1,0,get_lang('desc_policyurl'),array(),255);
	$x->add_text('files_redirect',0,1,0,get_lang('desc_files_redirect'),array(),255);
	$x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;".get_lang('str_misc')."&gt;</b></font><br />");
	$x->add_text('techsupport_index_limit',0,1,0,get_lang('desc_techsupport_index_limit'),array(),3);
	$x->add_text('shoutbox_index_limit',0,1,0,get_lang('desc_shoutbox_index_limit'),array(),3);
	$x->add_text('gamerhour',0,1,0,get_lang('desc_gamerhour'),array(),255);
	$x->add_textarea('important_info',0,1,0,get_lang('desc_important_info'),array(),4,1);
    $x->add_checkbox('pizza_orders_lock',0,1,0,get_lang('desc_pizza_orders_lock'),array(),0);
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/".get_lang('str_misc')."&gt;</b></font><br />");
    $x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;".get_lang('str_staff')."&gt;</b></font><br />");
	$x->add_text('staff_photo_url',0,1,0,get_lang('desc_staff_photo_url'),array(),200);
	$x->add_text('staff_photo_width',0,1,0,get_lang('desc_staff_photo_width'),array(),200);
    $x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/".get_lang('str_staff')."&gt;</b></font><br />");
    $x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;".get_lang('str_sponsors')."&gt;</b></font><br />");
	$x->add_text('sponsors_index_limit',0,1,0,get_lang('desc_sponsors_index_limit'),array(),3);
	$x->add_text('sponsors_width',0,1,0,get_lang('desc_sponsors_width'),array(),3);
	$x->add_text('sponsors_banner_width',0,1,0,get_lang('desc_sponsors_banner_width'),array(),3);
	$x->add_checkbox('sponsors_border',0,1,0,get_lang('desc_sponsors_border'),array(),0);
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/".get_lang('str_sponsors')."&gt;</b></font><br />");
	$x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;".get_lang('str_music')."&gt;</b></font><br />");
	$x->add_text('music_files',0,1,0,get_lang('desc_music_files'), array(),255);
	$x->add_text('music_min_time',0,1,0,get_lang('desc_music_min_time'), array(),3);
	$x->add_text('music_max_queue',0,1,0,get_lang('desc_music_max_queue'), array(),3);
	$x->add_text('music_stream_id',0,1,0,get_lang('desc_music_stream_id'),array(),20);
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/".get_lang('str_music')."&gt;</b></font><br />");
	$x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;".get_lang('str_ts')."&gt;</b></font><br />");
	$x->add_select('voice_mode',1,1,0,get_lang('desc_voice_mode'),array("" => ""),array("ts" => "teamspeak","vent" => "ventrilo"));
	$x->add_text('voice_name',0,1,0,get_lang('desc_voice_name'), array(),45);
	$x->add_text('voice_ip',0,1,0,get_lang('desc_voice_ip'), array(),45);
	$x->add_text('voice_pass',0,1,0,get_lang('desc_voice_pass'), array(),20);
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/".get_lang('str_ts')."&gt;</b></font><br />");
	
}

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim(get_lang('noauth'));
}
?>
