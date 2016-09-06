<?php
// this file is not needed if !ALP_TOURNAMENT_MODE_COMPUTER_GAMES
require_once 'include/_universal.php';
$x = new universal('team types','team type',2);
$x->database('tournament_teams_type','id','name');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add','these are the tournament team types automatically assigned in the brackets.  this will tell the teams which side to join in game.');
//$x->add_delmod_query("SELECT tournament_teams_type.*,games.name FROM tournament_teams_type LEFT JOIN games USING(gameid)");
$x->add_delmod_query("SELECT *,concat(onename, ' vs ', twoname) as name FROM tournament_teams_type");

$x->start_elements();
//$x->add_selectlist('gameid',1,1,1,'game (<a href="admin_games.php">add more games</a>)',array('empty' => 'you forget to input the game.'),'SELECT * FROM games','gameid','name');
$x->add_text('onename',1,1,0,'team one name',array('empty' => 'you forgot to enter a team type name!'),100);
$x->add_text('onecolor',0,1,0,'color of the team - in hex [ie: #ff0000] or in standard color name format [ie: red]',array(),10);
$x->add_text('twoname',1,1,0,'team two name',array('empty' => 'you forgot to enter a team type name!'),100);
$x->add_text('twocolor',0,1,0,'color of the team - in hex [ie: #ff0000] or in standard color name format [ie: red]',array(),10);

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>