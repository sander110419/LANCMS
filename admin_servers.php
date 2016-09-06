<?php
require_once 'include/_universal.php';
$x = new universal((!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations'),(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'server':'location'),2);
$x->database('servers','id','name');
$x->permissions('mod',2);
$x->add_notes('mod','modify the current '.(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations').' in the database for a tournament.'.(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?' displayed as a string to the user.  these servers are not yet set up to be queried with gameq.':''));
$x->add_delmod_query("SELECT * FROM servers WHERE tourneyid='".(!empty($_GET['id'])?(int)$_GET['id']:'')."'");

$x->start_elements();
$x->add_text('name',0,1,0,(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'server':'location').' name',array(),100);
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { 
	$x->add_text('ipaddress',0,1,0,'server ip address',array(),255);
	$x->add_text('queryport',0,1,0,'query port (if not default)',array(),10);
}
if (empty($_POST) && $x->is_secure() && !empty($_GET['id']) && 
    $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".(!empty($_GET['id'])?(int)$_GET['id']:'')."'"))) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	if (empty($_GET['id'])) {
		$x->display_slim('specify the tournament to edit.','disp_tournament.php');
	} elseif (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".(!empty($_GET['id'])?(int)$_GET['id']:'')."'"))) {
		$x->display_slim('no '.(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations').' to edit in that tournament.','disp_tournament.php'.(!empty($_GET['id'])?'?id='.(int)$_GET['id']:''));
	}
}
?>