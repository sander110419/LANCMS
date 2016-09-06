<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('schedule','itemid','headline');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);

$security = array(
	0 => get_lang('guest'),
	1 => get_lang('user'),
	2 => get_lang('administrator'),
	3 => get_lang('sadministrator')
	);

$x->start_elements();
$x->add_datetime('itemtime',1,1,0,get_lang('desc_itemtime'),array('empty' => get_lang('error_itemtime')),array(date('U')));
$x->add_select('itemtime_priv',0,1,0,get_lang('desc_itemtime_priv'),array(),$security,0);
$x->add_text('headline',1,1,0,get_lang('desc_headline'),array('empty' => get_lang('error_headline')),60);

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