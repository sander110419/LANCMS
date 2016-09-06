<?php
require_once 'include/_universal.php';
$x = new universal('benchmarks','benchmark',1);
$x->database('users_benchmarks','id','name');
$x->permissions('mod',2);
$x->add_delmod_query("SELECT users_benchmarks.*,benchmarks.name FROM users_benchmarks LEFT JOIN benchmarks ON users_benchmarks.benchid=benchmarks.id WHERE users_benchmarks.userid='".$_COOKIE['userid']."'");
$x->add_notes('mod','modify your benchmarks for the benchmarking competition.  remember that to prove that you actually acheived these benchmarks, you must upload screenshots of your score in the benchmarking program.');

$x->add_related_link('modify benchmarks available to user.','admin_benchmarks.php',2);
$x->add_related_link('view benchmark high scores.','benchmarks.php',0);
if($toggle['uploading']) $x->add_related_link('upload screenshots of your benchmarks.','upload.php?type=benchmarks',1);

$x->start_elements();
$x->add_text('value',0,1,0,'benchmark value',array(),20);

if(empty($_POST)&&$x->is_secure()&&$toggle['benchmarks']) {
	$data = $dbc->database_query("SELECT * FROM benchmarks ORDER BY name");
	while($row = $dbc->database_fetch_assoc($data)) {
		if(!$dbc->database_num_rows($dbc->database_query("SELECT * FROM users_benchmarks WHERE benchid='".$row['id']."' AND userid='".$_COOKIE['userid']."'"))) {
			$dbc->database_query("INSERT INTO users_benchmarks (benchid,userid) VALUES ('".$row['id']."','".$_COOKIE['userid']."')");
		}
	}
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()&&$toggle['benchmarks']) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>