<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('lodging','itemid','name');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));

$x->start_elements();
$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),255);
$x->add_text('address',0,1,0,get_lang('desc_address'),array(),255);
$x->add_text('phone',0,1,0,get_lang('desc_phone'),array(),20);
$x->add_text('costpernight',0,1,0,get_lang('desc_costpernight'),array(),100);
$x->add_text('traveltime',0,1,0,get_lang('desc_traveltime'),array(),100);

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
