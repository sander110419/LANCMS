<?php
require_once 'include/_universal.php';
$x = new universal('tournaments','tournament',2);
$x->database('tournaments','tourneyid','name');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add','this is the preliminary step in creating a tournament.  this makes the tournament available for teams to join.  if the game you wish to play isn\'t listed under "games" below, you must go to the <a href="admin_games.php">administrator games page</a> to add it.');
$x->add_crutch('lockstart');
$x->add_extra('del',array(array('DELETE FROM tournament_matches','tourneyid'),array('DELETE FROM tournament_matches_teams','tourneyid'),array('DELETE FROM tournament_teams','tourneyid'),array('DELETE FROM tournament_players','tourneyid'),array('DELETE FROM servers','tourneyid')));

$x->start_elements();
$array = array();
foreach ($tournament_types as $key => $val) {
	$array[$key] = '('.$val[1].') '.$val[0];
}
$x->add_text('name',1,1,1,'tournament name',array('empty' => 'you forgot to enter a tournament name.'),255);
if(!ALP_TOURNAMENT_MODE) $x->add_radio('tentative',0,1,0,'classify as tentative?',array(),array('1' => 'yes','0' => 'no'));
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $x->add_selectlist('gameid',1,1,1,'game (<a href="admin_games.php">add more games</a>)',array('empty' => 'you forget to input the game played in the tournament.'),'SELECT * FROM games ORDER BY name','gameid','name');
$x->add_select('ttype',1,1,1,'tournament type (1 is the shortest, 5 is the longest)',array('empty' => 'you forget to input the tournament type.'),$array);
$x->add_selectlist('moderatorid',0,1,0,'use non-admin tournament moderator?',array(),'SELECT * FROM users WHERE priv_level < 2','userid','username');
if(!ALP_TOURNAMENT_MODE) $x->add_radio('random',0,0,1,'random teams?',array(),array(1 => 'yes',0 => 'no'));
// decrease to maximum number in database?
if(!ALP_TOURNAMENT_MODE) {
	$x->add_text('per_team',1,0,1,'maximum players per team',array('empty' => 'you forgot to enter the maximum players per team!'),10);
} else {
	$x->add_hidden_dos('per_team',2);
}
$x->add_text('max_teams',0,0,1,'maximum number of teams (empty/0 = no max)',array(),15);
if($toggle['schedule']) $x->add_datetime('itemtime',0,1,1,'date and time of tournament',array(),array(date('U')));
if(!ALP_TOURNAMENT_MODE) {
	$x->add_text('url_stats',0,1,0,'url to statistics for the tournament',array(),255);
}
if ($toggle['marath']) {
	$x->add_radio('marathon',0,1,0,'part of the global marathon tournament?',array(),array('1' => 'yes','0' => 'no'));
}
if(!ALP_TOURNAMENT_MODE) {
	$x->add_radio('lockjoin',0,1,1,'lock team joining and quitting?',array(),array('1' => 'yes','0' => 'no'));
} else {
	$x->add_hidden_dos('lockjoin',1);
}
if(!ALP_TOURNAMENT_MODE) {
	$x->add_radio('lockteams',0,1,1,'lock team creation?',array(),array('1' => 'yes','0' => 'no'));
} else {
	$x->add_hidden_dos('lockteams',1);
}
$x->add_textarea('notes',0,1,0,'tournament rules/notes',array(),10);
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $x->add_textarea('settings',0,1,0,'server settings',array(),10);

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
<body>
<center>
<h1>HOW DOES THIS WORK?</h1>
1. Boiloff:<br />
A simple system, just enter the amount of points someone makes in the little white square and press the "+"<br />
The system will calculate the winner automatically at the top of the page.<br />
Finished the round? press the "finished" button.<br />
Want to start another round? Press the "finished" button, then<br />
click on the teamnames that you want in the second round, then press the "unfinished" button.<br />
<br />
2. Single Elimination:<br />
Mostly useful for shooters/highscore games in an elimination-bracket system.<br />
Input the total score whenever somebody scores a point into the correct white square.<br />
Make sure to look at the arrows!<br />
press the "+" butten everytime you input the total score, and the winning team will be put in the second bracket.<br />
<br />
3. Consolation:<br />
Same as number 2, but minimum 5 teams ( best in a 2v2 setting) where every team will battle every other team.<br />
enter the score in a match, press the little "+" button, and the teams will be rearranged in the next bracket.<br />
<br />
4. Double Elimination.<br />
Each team gets 2 chances to advance, if they lose 2 times, they are eliminated.<br />
<br />
5. Round Robin.<br />
Each team plays 5 rounds against other teams, highest score wins.<br />
</center>
</body>

