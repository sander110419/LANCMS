<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('prizes','prizeid','prizename');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));

$x->add_related_link(get_lang('link_admin_prize_control'),'admin_prize_control.php',2);
$x->add_related_link(get_lang('link_admin_prizes_print'),'admin_prizes_print.php',2);
$x->add_related_link(get_lang('link_admin_prize_draw'),'admin_prize_draw.php',2);
$x->add_related_link(get_lang('link_chng_prizes'),'chng_prizes.php',1);
$x->add_related_link(get_lang('link_disp_prizes'),'disp_prizes.php',0);

$x->start_elements();
$array = array(1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th');
$x->add_text("prizename",1,1,0,get_lang('desc_prizename'),array("empty" => get_lang('error_prizename')),255);
$x->add_text("prizequantity",1,1,0,get_lang('desc_prizequantity'),array("empty" => get_lang('error_prizequantity')),20);
$x->add_text("prizevalue",0,1,0,get_lang('desc_prizevalue'),array(),12);
$x->add_text("prizepicture",0,1,0,get_lang('desc_prizepicture'),array(),255);
$x->add_text("prizegroup",0,1,0,get_lang('desc_prizegroup'),array(),11);
$x->add_selectlist("tourneyid",0,1,0,get_lang('desc_tourneyid'),array(),"SELECT * FROM tournaments","tourneyid","name");
$x->add_select("tourneyplace",0,1,0,get_lang('desc_tourneyplace'),array(),$array);

if (empty($_POST) && $x->is_secure() && $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners")) == 0) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure() && $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners")) == 0) {
	$x->display_results();
} elseif ($dbc->database_result($dbc->database_query("SELECT lock_prizes FROM prizes_control"), 0) == 1) {
	$x->display_slim(get_lang('error_locked'),"admin_prize_control.php");
} else {
	$x->display_slim(get_lang('noauth'));
}
?>