<?php
require_once 'include/_universal.php';
// universal('plural name', 'singular name', minimum security level)
$x = new universal('','',0);
// database('table name','unique id','order by')
$x->database('','','');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
//$x->add_notes('','');
//$x->add_delmod_query('');

$x->start_elements();

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	//$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
