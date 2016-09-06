<?php
/*
 * $Id: chng_staff.php,v 1.2 2005/12/02 20:29:39 travispk Exp $
 */
require_once 'include/_universal.php';
$x = new universal('staff fields','staff field',2);
$x->database('staff','staffid','display');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add','data fields added here will be displayed on the staff page for each defined staff member<br />there is only special handling on a field if it is labeled "photo", this is the only field that will be treated as an image url in the staff page');
$x->add_delmod_query("SELECT staffid, name, priority, enabled, CONCAT(priority,': ',name) AS display FROM staff");

$x->add_related_link('add/modify staff details','admin_staff.php',2);
$x->add_related_link('view staff','staff.php',1);

$x->start_elements();
$x->add_text("name",1,1,0,"field name",array("empty" => "you forgot to enter a field name!"),150);
$x->add_text("priority",1,1,0,"priority",array(),10);
$x->add_checkbox("enabled",0,1,0,"enabled");

if(empty($_POST)&&$x->is_secure()&&$toggle['staff']) {
    $x->display_top();
    $x->display_form();
    $x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()&&$toggle['staff']) {
    $x->display_results();
} else {
    $x->display_slim("you are not authorized to view this page.");
}
?>
