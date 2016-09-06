<?php
require_once 'include/_universal.php';
$x = new universal('servers','server',2);
$x->database('servers','id','game_name');
$x->permissions('add',1);
$x->permissions('del',1);
$x->permissions('mod',1);
$x->add_notes('add','add servers to the current server list (as linked to from the top of the page).  
            if you don\'t see the game you want to add below, you can always <a href="admin_games.php">add more games</a>. <br /><br />do not forget to put the port at the end of the IP address.');
//$x->add_delmod_query("SELECT *,games.name AS game_name FROM servers LEFT JOIN games using (gameid) WHERE tourneyid=0");
$x->add_delmod_query("SELECT *,ipaddress as game_name FROM servers");

$x->start_elements();
$x->add_selectlist('gameid',1,1,0,'game',array(),'SELECT * FROM games ORDER BY name','gameid','name');
$x->add_text('ipaddress',1,1,0,'ip address and port (ie: 10.0.0.2:27015)',array(),255);
//$x->add_text('queryport',1,1,0,'query port (ie: 27015)',array(),10);

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>