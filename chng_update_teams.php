<?php
require_once 'include/_universal.php';
include_once 'include/tournaments/_tournament_functions.php';
$x = new universal('tournament teams','team',1);
if ($x->is_secure()) {
	if (empty($_GET)&&empty($_POST)) {
		$x->display_slim('incorrect usage.','disp_teams.php');
	} elseif (!empty($_POST) && !empty($_POST['id']) && !empty($_POST['teamid']) && !empty($_POST['userid'])) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_POST['id']));
		$team = $dbc->database_query('SELECT * FROM tournament_teams WHERE tourneyid='.(int)$tournament['tourneyid'].' AND id='.(int)$_POST['teamid'].' AND captainid='.(int)$_COOKIE['userid']);
		$allgood = true;
		if (!empty($_POST['act']) && ((!$tournament['lockstart']) || ($tournament['lockstart'] && $_POST['act']=='cpt')) && !$tournament['lockjoin'] && !$tournament['lockteams'] && $dbc->database_num_rows($team)) {
			$teaminfo = $dbc->database_fetch_assoc($team);
			// act: cpt (allowed after tournament start)
			if ($_POST['act'] == 'del') {
				// delete a player
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_POST['userid']."'"))) {
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$teaminfo['id']."' AND userid='".$_POST['userid']."'"))) {
						if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$teaminfo['id']."' AND userid='".$_POST['userid']."'")) {
							$allgood = false;
						}
					} else {
						$error = 'user is not on your team.';
					}
				} else {
					$error = 'user is not playing in the tournament.';
				}
			} elseif ($_POST['act'] == 'cpt') {
				// make a new player captain
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_POST['userid']."'"))) {
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$teaminfo['id']."' AND userid='".$_POST['userid']."'"))) {
						if (!$dbc->database_query("UPDATE tournament_teams SET captainid='".$_POST['userid']."' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$teaminfo['id']."'")) {
							$allgood = false;
						}
					} else {
						$error = 'user is not on your team.';
					}
				} else {
					$error = 'user is not playing in the tournament.';
				}
			} elseif ($_POST['act'] == 'pug') {
				// move a player to pug
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_POST['userid']."'"))) {
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$teaminfo['id']."' AND userid='".$_POST['userid']."'"))) {
						if (!$dbc->database_query("UPDATE tournament_players SET teamid=0 WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_POST['userid']."'")) {
							$allgood = false;
						}
					} else {
						$error = 'user is not on your team.';
					}
				} else {
					$error = 'user is not playing in the tournament.';
				}
			}
		} else {
			$error = 'incorrect usage.';
		}
		if (!empty($error)) {
			$str = $error;
		} else {
			if($allgood) {
				$str = 'success.';
			} else {
				$str = 'error!';
			}
		}
		$x->display_slim($str,'disp_teams.php'.(!empty($_POST['id'])?'?id='.$_POST['id']:''),2);
	} elseif (!empty($_GET) && !empty($_GET['id'])) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']));
		$allgood = true;
		if (!empty($_GET['act']) && !$tournament['lockstart'] && !$tournament['lockjoin'] && !$tournament['lockteams']) {
			// act: del, add
			if ($_GET['act'] == 'add') {
				if ($tournament['per_team'] == 1 || $tournament['random']) {
					if (is_under_max_teams($tournament['tourneyid'])) {
						if (!$dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_players WHERE tourneyid='.(int)$tournament['tourneyid'].' AND userid='.(int)$_COOKIE['userid']))) {
							if (!$dbc->database_query('INSERT INTO tournament_players (tourneyid,userid,teamid) VALUES ('.(int)$tournament['tourneyid'].','.(int)$_COOKIE['userid'].',0)')) {
								$allgood = false;
							}
						} else {
							$error = 'you are already playing in the tournament!';
						}
					} else {
						$error = 'maximum '.get_what_teams_called($tournament['tourneyid'],0).' limit reached.';
					}
				} elseif ($tournament['per_team'] > 1) {
					if (!empty($_GET['teamid'])) {
						if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE id='".$_GET['teamid']."' AND tourneyid='".$tournament['tourneyid']."'"))) {
							if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) {
								if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$_GET['teamid']."' AND userid='".$_COOKIE['userid']."'"))) {
									if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$_GET['teamid']."'"))<$tournament['per_team']) {
										if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$_GET['teamid']."'"))==0) {
											if (!$dbc->database_query("UPDATE tournament_teams SET captainid='".$_COOKIE['userid']."' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$_GET['teamid']."'")) {
												$allgood = false;
											}
										}
										if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'")||!$dbc->database_query("INSERT INTO tournament_players (tourneyid,userid,teamid) VALUES ('".$tournament['tourneyid']."','".$_COOKIE['userid']."','".$_GET['teamid']."')")) {
											$allgood = false;
										}
									} else {
										$error = 'that team is full.';
									}
								} else {
									$error = 'you are already on this team.';
								}
							} else {
								$error = 'you are a captain on another team in the tournament!';
							}
						} else {
							$error = 'that team does not exist.';
						}
					} elseif (!empty($_GET['u']) && $_GET['u'] == 1) {
						// join a new team
						if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) {
							if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'")||!$dbc->database_query("INSERT INTO tournament_players (tourneyid,userid,teamid) VALUES ('".$tournament['tourneyid']."','".$_COOKIE['userid']."','0')")) {
								$allgood = false;
							}
						} else {
							$error = 'you are a captain on another team in the tournament!';
						}
					} else {
						$allgood = false;
					}
				}
			} elseif ($_GET['act'] == 'del') {
				// quit the tournament
				if ($tournament['per_team'] > 1 && !$tournament['random']) {
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) {
						$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
						$next_captain = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."' AND userid!='".$_COOKIE['userid']."' ORDER BY RAND() LIMIT 1"));
						if (!$dbc->database_query("UPDATE tournament_teams SET captainid='".$next_captain['userid']."' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$team['id']."'")) {
							$allgood = false;
						}
					}
				}
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'"))) {
					if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."' AND userid='".$_COOKIE['userid']."'")) {
						$allgood = false;
					}
					if (!$dbc->database_query("DELETE FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$userinfo['userid']."'")) {
						$allgood = false;
					}
				} else {
					$error = 'you are not playing in the tournament!';
				}
			} elseif ($_GET['act'] == 'delt') {
				// delete a team
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) {
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
					if (!$dbc->database_query("DELETE FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$team['id']."'")||!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."'")) {
						$allgood = false;
					}
					if (!$dbc->database_query("DELETE FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$userinfo['userid']."'")) {
						$allgood = false;
					}
				} else {
					$error = 'you can\'t delete the team if you\'re not the captain.';
				}
			} elseif ($_GET['act'] == 'draft') {
				// draft a pug
				if ($tournament['per_team'] > 1 && !$tournament['random'] && !empty($_GET['uid'])) {
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) {
						$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
						if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."'"))<$tournament['per_team']) {
							if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid>0 AND userid='".$_GET['uid']."'"))) {
								$error = 'the player you\'re trying to draft has already been drafted.';
							} else {
								if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='0' AND userid='".$_GET['uid']."'"))) {
									if (!$dbc->database_query("UPDATE tournament_players SET teamid='".$team['id']."' WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_GET['uid']."'")) {
										$allgood = false;
									}
								} else {
									$error = 'the player you\'re trying to draft isn\'t participating any more.';
								}
							}
						} else {
							$error = 'your team is already full.';
						}
					} else {
						$error = 'you aren\'t the captain of any team in this tournament.';
					}
				} else {
					$allgood = false;
				}
			}
		} else {
			$allgood = false;
		}
		
		if (!empty($error)) {
			$str = $error;
		} else {
			if($allgood) {
				$str = 'success.';
			} else {
				$str = 'error!';
			}
		}
		$x->display_slim($str,'disp_teams.php'.(!empty($_GET['id'])?'?id='.$_GET['id']:''),2);
	} else {
		$x->display_slim('incorrect usage.','disp_teams.php');
	}
}
?>