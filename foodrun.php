<?php
require_once 'include/_universal.php';
$x = new universal('food runs','food run',1);
$x->database('foodrun','itemid','headline');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add','food runs are organized excursions for sustinance.  if you\'re getting hungry and want to take a drive, and are willing to take along a few others, advertise the run here.');
$x->add_delmod_query('SELECT * FROM foodrun WHERE userid='.(int)$_COOKIE['userid']);

$x->add_related_link('modify restaurants in the area.','admin_restaurant.php',2);
$x->add_related_link('restaurants in the area.','restaurants.php',0);
if($toggle['pizza']) $x->add_related_link('want pizza? <strong>go here</strong>','pizza.php',0);

$x->start_elements();
$x->add_text('headline',1,1,0,'destination',array('empty' => 'the destination of your food run (usually a restaurant or grocery store name) is required.'),60);
$x->add_selectlist('userid',0,1,0,'posted by',array(),'SELECT userid,username FROM users WHERE userid='.(int)$_COOKIE['userid'],'userid','username',0);
$x->add_datetime('itemtime',1,1,0,'time of food run',array('empty' => 'the time of your food run is required.'),array(date('U')));

if (empty($_POST) && $x->is_secure() && $toggle['foodrun']) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure() && $toggle['foodrun']) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>