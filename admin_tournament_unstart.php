<?php
include "include/_universal.php";
$x = new universal("un-start tournaments","tournament",2);
$x->database("tournaments","tourneyid","name");
$x->permissions("mod",1);
$x->add_notes("mod","un-starting a tournament and then starting it again will erase all tournament matches and scores currently under the tournament.  be very careful when using this.  if you screw up and accidentally unstart a tournament here you didn't want to, you'll need to edit the lockstart field in the tournaments database table to save your data.");
$x->add_delmod_query("SELECT * FROM tournaments WHERE lockstart=1");

$x->start_elements();
$x->add_radio("lockstart",0,1,0,"",array(),array("1" => "already started", "0" => "un-start it!"));

if(empty($_POST)&&$x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>