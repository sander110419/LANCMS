<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('','tourneyid','');
$x->permissions('update',1);
$x->add_notes('update',get_lang('notes_update'));
$x->add_extra('update',array(array('UPDATE tournament_teams SET ranking=NULL','tourneyid'),array('UPDATE tournament_players SET ranking=NULL','tourneyid')));

$x->start_elements();
$x->add_selectlist('tourneyid',1,1,0,get_lang('desc_tourneyid'),array('empty' => get_lang('error_tourneyid')),'SELECT tourneyid,name FROM tournaments WHERE lockstart=0','tourneyid','name');
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