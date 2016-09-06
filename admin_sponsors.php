<?php
require_once 'include/_universal.php';
// universal('plural name', 'singular name', minimum security level)
$x = new universal('Sponsors','Sponsor',2);
// database('table name','unique id','order by')
$x->database('sponsors','id','sponsor');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_related_link('view sponsors','disp_sponsors.php',1);
$x->add_notes('add','When entering URLs enter fully qualified addresses ( http://serveraddress/imagefolder/image.gif )<br />');
$x->add_delmod_query('SELECT * FROM sponsors');

$x->start_elements();
$x->add_text('sponsor',1,1,0,'Sponsor Name',array('empty' => 'you forgot to enter Sponsor Name!'),50);
$x->add_text('sponsor_url',0,1,0,'Sponsor Website (URL)',array(),100);
$x->add_text('img_sidebar_url',0,1,0,'Sidebar Image (URL)',array(),100);
$x->add_text('img_banner_url',0,1,0,'Banner Image (URL)',array(),100);
$x->add_text('img_alt',0,1,0,'Image Alt Text',array(),100);
$x->add_text('caption',0,1,0,'Short Caption',array(),100);
$x->add_textarea('description',0,1,0,'Extended Description',array(),5);
$x->add_text('priority',1,1,0,'Priority Display Ranking',array('empty' => 'you forgot to enter the Priority Order!'),3);
$x->add_checkbox('enabled',0,1,0,'Visible');

/*
if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
*/
$x->display();
?>
