<?php
require_once 'include/_universal.php';
$x = new universal('change profile','profile',2);
$x->database('users','userid','username');
$x->permissions('mod',1);
$x->add_notes('mod','modify a user\'s profile.');
$x->add_delmod_query("SELECT * FROM users WHERE userid='".(!empty($_GET['id'])?$_GET['id']:'')."'");
$x->start_elements();
$array = array();
for ($i = 0; $i <= 10; $i++) {
	if ($i != 0) {
		$array[$i] = $i.'0%';
	} else {
		$array[$i] = $i.'%';
	}
}
$x->add_text('first_name',0,1,0,'first name',array(),30);
$x->add_text('last_name',0,1,0,'last name',array(),30);
$x->add_text('email',0,1,0,'email address',array(),60);
$x->add_radio('display_email',0,1,0,'allow others to see your email address?',array(),array('1' => 'yes','0' => 'no'));
$x->add_radio('display_ip',0,1,0,'allow others to see your ip address?',array(),array('1' => 'yes','0' => 'no'));
$x->add_radio('gender',0,1,0,'gender',array(),array('male' => 'male', 'female' => 'female', 'i don\'t wish to specify.' => 'i don\'t wish to specify.'));
$x->add_text('gaming_group',0,1,0,'gaming group',array(),20);
$x->add_datetime('date_of_departure',0,1,0,'date of departure',array(),array($end));

if (empty($_POST) && $x->is_secure() && !empty($_GET['id'])) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} elseif (empty($_GET['id'])) {
	$x->display_slim('select a user to edit.','users.php?show=');
} else {
	$x->display_slim('you are not authorized to view this page.','users.php?show=');
}
?>