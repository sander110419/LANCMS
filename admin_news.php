<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('news','itemid','headline');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));

$x->start_elements();
$x->add_text('headline',1,1,0,get_lang('desc_headline'),array('empty' => get_lang('error_headline')),255);
$x->add_selectlist('userid',0,1,0,get_lang('desc_userid'),array(),'SELECT userid, username FROM users WHERE priv_level > 1','userid','username',0);
$x->add_datetime('itemtime',0,1,1,get_lang('desc_itemtime'),array(),array(date('U')));
$x->add_textarea('news_article',1,1,0,get_lang('desc_news_article'),array('empty' => get_lang('error_news_article')),5,0);
$x->add_checkbox('hide_item',0,1,0,get_lang('desc_hide_item'),array(),0);
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