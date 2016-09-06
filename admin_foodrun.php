<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('foodrun','itemid','headline');
$x->permissions('mod',1);
$x->permissions('del',1);
if (!$toggle['foodrun']) { $x->add_notes('del',get_lang('notes_del')); }

$x->start_elements();
$x->add_selectlist('userid',0,1,0,get_lang('desc_userid'),array(),'SELECT userid,username FROM users','userid','username',0);
$x->add_datetime('itemtime',1,1,0,get_lang('desc_datetime_leaving'),array('empty' => get_lang('error_datetime_leaving')),array(date('U')));
$x->add_text('headline',0,1,0,get_lang('desc_headline'),array('empty' => get_lang('error_headline')),60);

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