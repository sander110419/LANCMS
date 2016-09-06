<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('game_requests','id','name');
$x->permissions('del',1);
$x->permissions('mod',1);
//$x->add_delmod_query("SELECT game_requests.*,games.name FROM game_requests LEFT JOIN games USING (gameid)");
$x->add_delmod_query("SELECT *,ipaddress as name FROM game_requests");

$x->add_related_link(get_lang('link_gamerequest'),'gamerequest.php',1);

$x->start_elements();
$x->add_selectlist('gameid',0,1,0,get_lang('desc_gameid'),array(),'SELECT * FROM games','gameid','name');
$x->add_selectlist('userid',1,1,0,get_lang('desc_userid'),array(),'SELECT * FROM users','userid','username');
$x->add_text('gamename',0,1,0,get_lang('desc_gamename'),array(),255);
$x->add_text('ipaddress',1,1,0,get_lang('desc_ipaddress'),array(),255);
$x->add_text('queryport',0,1,0,get_lang('desc_queryport'),array(),10);
$x->add_datetime('itemtime',0,1,0,get_lang('desc_itemtime'),array(),array(date('U')));

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