<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),3);
$x->database('users','userid','username');
$x->permissions('mod',2);
$x->add_notes('mod',get_lang('notes_mod'));
$x->add_delmod_query("SELECT * FROM users WHERE username!='".$lan['admin']."' ORDER BY username");

$x->start_elements();
$x->add_select('priv_level',0,1,0,'',array(),array(1 => get_lang('user'),2 => get_lang('administrator'),3 => get_lang('sadministrator')));

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