<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('caffeine_types','id','name');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));

$x->start_elements();
$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),255);

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