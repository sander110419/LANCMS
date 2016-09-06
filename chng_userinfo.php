<?php
require_once 'include/_universal.php';
$x = new universal('change profile','profile',1);
$x->database('users','userid','username');
$x->permissions('mod',1);
$x->add_delmod_query('SELECT * FROM users WHERE userid='.(int)$userinfo['userid']);

$x->start_elements();
$array = array();
for ($i=0; $i <= 10; $i++) {
	if ($i != 0) {
		$array[$i] = $i.'0%';
	} else {
		$array[$i] = $i.'%';
	}
}
$languages = get_langlist();
$skins = get_skinlist();
$dates = get_datelist();

$x->add_notes("mod","please update your profile. all fields are optional, you may only fill in the ones you wish to answer.");
$x->add_text('first_name',0,0,0,'first name',array(),30);
$x->add_text('last_name',0,0,0,'last name',array(),30);
$x->add_select('proficiency',0,!$master['proficiencylock'],0,'gamer proficiency rating (how many of the other gamers at the lan party do you think you can kill in a one-on-one in your favorite game?) (used for random team tournaments)',array(),$array);
$x->add_text('email',0,1,0,'email address',array(),60);
$x->add_radio('display_email',0,1,0,'allow others to see your email address?',array(),array('1' => 'yes','0' => 'no'));
$x->add_radio('display_ip',0,1,0,'allow others to see your ip address?',array(),array('1' => 'yes','0' => 'no'));
$x->add_radio('gender',0,1,0,'gender',array(),array('male' => 'male', 'female' => 'female', '' => 'i don\'t wish to specify.'));
$x->add_text('gaming_group',0,1,0,'gaming group',array(),20);
$x->add_text('quote',0,1,0,'quote',array(),255);
$x->add_select('language',0,1,0,'language (blank option for site default)',array(),$languages);
if(!$master['skin_override'])
	$x->add_select('skin',0,1,0,'skin (blank option for site default)',array(),$skins);
$x->add_select('dateformat',0,1,0,'date format (blank option for site default)',array(),$dates);
$x->add_datetime('date_of_departure',0,1,0,'date of departure',array(),array($end));

if($toggle['filesharing']) {
	$x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;file sharing&gt;</b></font><br />");
		$x->add_text("sharename",0,1,0,"share address ( \\\compname\share or ftp://compname )",array(),35);
		$x->add_radio("ftp_server",0,1,0,"have an ftp server?",array(),array("1"=>"yes","0"=>"no"));
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/file sharing&gt;</b></font><br />");
}

if($toggle["gamingrigs"]) {
	include 'include/_gaming_rig_db.php';
	$x->add_displaystring("<font color=\"".$colors["primary"]."\"><b>&lt;gaming rig&gt;</b></font><br />");
	$x->format("table");
		$x->format("row");
			$x->format("cell");
				$x->add_select("comp_proc",0,1,0,"cpu (brand)",array(),$x_processors);
			$x->format("endcell");
		$x->format("endrow");
	$x->format("endtable");
	
	$x->format("table");
		$x->format("row");
			$x->format("cell");
				$x->add_text("comp_proc_type",0,1,0,"cpu (type)",array(),60);
			$x->format("endcell");
			$x->format("cell");
				$x->add_text("comp_proc_spd",0,1,0,"cpu (<b>GHz</b>) (numeric only)",array(),60);
			$x->format("endcell");
		$x->format("endrow");
	$x->format("endtable");
	
	$x->format("table");
		$x->format("row");
			$x->format("cell");
				$x->add_select("comp_mem",0,1,0,"RAM (amount)",array(),$x_mem_sizes);
			$x->format("endcell");
			$x->format("cell");
				$x->add_select("comp_mem_type",0,1,0,"RAM (type)",array(),$x_mem_types);
			$x->format("endcell");
		$x->format("endrow");
	$x->format("endtable");
	$x->add_select("comp_hdstorage",0,1,0,"total hard disk capacity",array(),$x_storage);
	$x->format("table");
		$x->format("row");
			$x->format("cell");
				$x->add_select("comp_gfx_gpu",0,1,0,"gpu (brand)",array(),$x_gpus);		
			$x->format("endcell");
			$x->format("cell");
	$x->add_text("comp_gfx_type",0,1,0,"graphics (chipset and amount of memory)",array(),60);
			$x->format("endcell");
		$x->format("endrow");
	$x->format("endtable");
	$x->add_displaystring("<font color=\"".$colors["secondary"]."\"><b>&lt;/gaming rig&gt;</b></font><br />");
}

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>