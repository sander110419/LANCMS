<?php
include "include/_universal.php";
$x = new universal("open play game requests","game request",1);
$x->database("game_requests","id","name");
$x->permissions("add",1);
$x->permissions("del",1);
$x->permissions("mod",1);
$x->add_notes('add', 'open play game request is a place where attendees can add servers that they are running into a list so that other attendees may notice the server and jump on.  please add your server below.<br /><br />do not forget to put the port at the end of the IP address.');
//$x->add_delmod_query("SELECT game_requests.*,games.name FROM game_requests LEFT JOIN games USING (gameid) WHERE userid='".$userinfo["userid"]."'");
$x->add_delmod_query("SELECT *,ipaddress as name FROM game_requests WHERE userid='".$userinfo["userid"]."'");

$x->add_related_link("delete or modify game requests","admin_gamerequest.php",2);
$x->add_related_link("view all game requests","gamerequest.php",1);

$x->start_elements();
$x->add_selectlist('userid',1,1,0,'user',array(),'SELECT * FROM users WHERE userid='.(int)$userinfo['userid'],'userid','username',0);
$x->add_hidden_dos("itemtime",date("Y-m-d H:i:s"));
$x->add_selectlist("gameid",1,1,0,"game",array(),"SELECT * FROM games ORDER BY name","gameid","name");
//$x->add_text("gamename",0,1,0,"other game (if not in select list above)",array(),255);
$x->add_text("ipaddress",1,1,0,"ip address and port (ie: 10.0.0.2:27015)",array(),255);
//$x->add_text("queryport",1,1,0,"query port (ie: 27015)",array(),10);

if(empty($_POST)&&$x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()) {
	$x->display_results("gamerequest.php");
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>