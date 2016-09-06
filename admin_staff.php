<?php
/*
 * $Id: admin_staff.php,v 1.2 2005/12/02 20:29:38 travispk Exp $
 */
require_once 'include/_universal.php';
$x = new universal('staff details','staff detail',2);
$x->database('users_staff','id','display');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add','only one entry allowed for each user/field combo.<br /><br />If you are trying to add a field and keep getting uknown error, then the field must already be defined.');
$x->add_delmod_query("SELECT users_staff.*,users.username,CONCAT(users.username,' : ',staff.name) AS display FROM users_staff LEFT JOIN users USING(userid) LEFT JOIN staff ON users_staff.staffid = staff.staffid");

$x->add_related_link('add/modify staff fields','chng_staff.php',2);
$x->add_related_link('view staff','staff.php',1);

$x->start_elements();
$x->add_selectlist("staffid",1,1,1,"staff field (<a href=\"chng_staff.php\">add more fields</a>)",array("empty" => "you forget to select the field."),"SELECT staffid,name FROM staff WHERE enabled='1'","staffid","name");
$x->add_selectlist("userid",1,1,1,"staff member",array("empty" => "you forget to select a user."),"SELECT userid,username FROM users WHERE priv_level > 1","userid","username");
$x->add_textarea("data",1,1,0,"field data",array("empty" => "you forgot to enter field data!"),5);

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
