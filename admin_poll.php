<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('poll','pollid','headline');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));
$x->add_extra('del',array(array('DELETE FROM poll_votes','pollid')));
$x->add_delmod_query('SELECT * FROM poll');

$x->add_related_link(get_lang('link_admin_mapvoting'),'admin_mapvoting.php',2);

$x->start_elements();
$x->add_text('headline',1,1,0,get_lang('desc_headline'),array('empty' => get_lang('error_headline')),255);
$x->add_checkbox('activepoll',0,1,0,get_lang('desc_activepoll'),array(),1);
for ($i=1; $i <= 15; $i++) {
	if ($i <= 2) {
		$req = 1;
		$empty = get_lang('error_choice');
	} else {
		$req = 0;
		$empty = "";
	}
	$x->add_text('choice'.$i,$req,0,0,get_lang('desc_choice').$i,array('empty' => $empty),255);
}

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