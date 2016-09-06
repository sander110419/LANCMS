<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('caffeine_items','id','name');
$x->permissions('add',1);
$x->permissions('del',1);
$x->permissions('mod',1);
$x->add_notes('add',get_lang('notes_add'));

$x->start_elements();
$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),255);
$x->add_text('caffeine_permg',1,1,0,get_lang('desc_caffeine_permg'),array('empty' => get_lang('error_caffeine_permg')),23);
$x->add_selectlist('ttype',0,1,0,get_lang('desc_ttype').' (<a href="admin_caffeine_types.php">'.get_lang('descother_ttype').'</a>)',array(),'SELECT * FROM caffeine_types','id','name');

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