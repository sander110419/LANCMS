<?php
require_once 'include/_universal.php';
$x = new universal('toggle','toggle',2);
$x->database('toggle','','');
$x->permissions('mod',1);
$x->add_notes('mod','toggle the main features of ALP on and off depending on which you wish to have available.');

$x->start_elements();
$array = array(1 => 'yes', 0 => 'no');
if(!ALP_TOURNAMENT_MODE) {
	//$x->add_radio('satellite',0,1,0,'ALP satellites',array(),$array);
	$x->add_radio('benchmarks',0,1,0,'benchmark competition',array(),$array);
	$x->add_radio('caffeine',0,1,0,'caffeine log',array(),$array);
}
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $x->add_radio('hlsw',0,1,0,'HLSW server connects',array(),$array);
if(!ALP_TOURNAMENT_MODE) {
	$x->add_radio('uploading',0,1,0,'file uploading',array(),$array);
	$x->add_radio('files',0,1,0,'files',array(),$array);
	$x->add_radio('foodrun',0,1,0,'food runs',array(),$array);
    $x->add_radio('pizza',0,1,0,'pizza orders',array(),$array);
	$x->add_radio('gamerequests',0,1,0,'game requests',array(),$array);
	$x->add_radio('servers',0,1,0,'game servers',array(),$array);
	$x->add_radio('marath',0,1,0,'the marathon',array(),$array);
	$x->add_radio('music',0,1,0,'livestream',array(),$array);
}
if(!ALP_TOURNAMENT_MODE) $x->add_radio('prizes',0,1,0,'prize registration',array(),$array);
$x->add_radio('schedule',0,1,0,'schedule',array(),$array);
if(!ALP_TOURNAMENT_MODE) {
	$x->add_radio('seating',0,1,0,'seating map',array(),$array);
	$x->add_radio('shoutbox',0,1,0,'shoutbox',array(),$array);
}
$x->add_radio('messaging',0,1,0,'user messaging',array(),$array);
//$x->add_radio('currentattendance',0,1,0,'sidebar module -- current attendance',array(),$array);
if(!ALP_TOURNAMENT_MODE) {
	$x->add_radio('filesharing',0,1,0,'extra - file sharing info',array(),$array);
	$x->add_radio('gamerofthehour',0,1,0,'extra - gamer of the hour',array(),$array);
	$x->add_radio('gamingrigs',0,1,0,'extra - gaming rigs',array(),$array);
	$x->add_radio('policy',0,1,0,'extra - policy',array(),$array);
	$x->add_radio('techsupport',0,1,0,'extra - tech-support queue',array(),$array);
	//$x->add_radio('timeremaining',0,1,0,'extra - time remaining',array(),$array);
    $x->add_radio('staff',0,1,0,'extra - staff page',array(),$array);
	$x->add_radio('sponsors',0,1,0,'extra - chat',array(),$array);
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
