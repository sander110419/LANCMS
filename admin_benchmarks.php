<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('benchmarks','id','name');
$x->permissions('add',1);
$x->permissions('del',1);
$x->permissions('mod',1);
$x->add_notes('add',get_lang('notes_add'));
$x->add_crutch('composite');

$x->start_elements();
$array = array();
for ($i=0; $i <= 100; $i+=5) {
	$array[$i] = $i.'%';
}
$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),255);
$x->add_text('abbreviation',0,1,0,get_lang('desc_abbreviation'),array(),100);
$x->add_radio('composite',0,1,0,get_lang('desc_composite'),array(),array('yes','no'));
$x->add_select('deflate',0,1,1,get_lang('desc_deflate'),array(),$array);

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