<?php
require_once 'include/_universal.php';
include_once 'include/cl_display.php';
$x = new universal('lodging','',0);
if ($x->is_secure()) { 
	$x->display_top(); ?>
		<strong>lodging</strong>:<br />
		<br />
		<?php
		$x->add_related_link('add/modify lodging','admin_lodging.php',2);
		$x->display_related_links(); 

		$y = new display('lodging','lodging item',0,0,'lodging','itemid','name');
		$y->add_field('name','business name',0,0);
		$y->add_field('address','address',0,0);
		$y->add_field('phone','phone #',0,0);
		$y->add_field('costpernight','cost per night',0,0);
		$y->add_field('traveltime','travel time from lan',0,0);
		$y->display_solo(); ?>
		<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>