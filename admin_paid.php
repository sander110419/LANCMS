<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('users','userid','username');
$x->permissions('mod',2);
$x->add_notes('mod',get_lang('notes_mod'));

$x->start_elements();
$x->add_radio('paid',0,1,0,'',array(),array('1' => get_lang('yes'), '0' => get_lang('no')));

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