<?php
require_once 'include/_universal.php';
$x = new universal('tournaments','tournament',1);
if ($x->is_secure()) {
	if (empty($_GET) && empty($_POST)) {
		$x->display_slim('incorrect usage.','disp_tournament.php');
	} elseif (!empty($_POST) && !empty($_POST["id"])) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_POST['id']));
		if (current_security_level() >= 2 || (current_security_level() >= 1 && $tournament['moderatorid'] == $_COOKIE['userid'])) {
			$allgood = true;
			if (!empty($_POST['matchid']) && !empty($_POST['server'])) {
				// change server for a match
				if (!$dbc->database_query("UPDATE tournament_matches SET server='".$_POST['server']."' WHERE id='".$_POST['matchid']."'")) {
					$allgood = false;
				}
				if ($allgood) {
					$str = 'success.';
				} else {
					$str = 'error!';
				}
			}
			if($tournament['ttype']==12&&!empty($_POST['matchid'])&&!empty($_POST['onlyscore'])) {
				// update boiloff score for one team.
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$_POST['onlyscore']."' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$_POST['matchid']."'")) {
					$str = 'unknown error';
				} else {
					$str = 'success';
				}
			} elseif ($tournament['ffa']) { 
				require_once 'include/_top_smallwindow.php';
				if (!empty($_GET['matchid']) && !empty($_GET['i']) && !empty($_GET['j'])) {
					$str = 'incorrect usage';
				} else {
					$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST['matchid']."'");
					while ($row = $dbc->database_fetch_assoc($data)) {
						if ($_POST[$row['id'].'_score']===0) $temp = "'0.0'";
						elseif($_POST[$row['id'].'_score']==='') $temp = 'NULL';
						else $temp = "'".$_POST[$row['id'].'_score']."'";
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE id='".$row['id']."'")) {
							$allgood = false;
						}
					}
					$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST['id']."' AND rnd='".($_POST['i']+1)."' AND mtc='".ceil($_POST['j']/4)."' AND bracket='".$_POST['b']."'"));
					$previouswinners = array();
					$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$nextmatch['id']."' AND top='".($_POST['j']%4==2?'1':'0')."'");
					while ($row = $dbc->database_fetch_assoc($data)) {
						if ($row['team']!=0) $previouswinners[] = $row['team'];
					}
					$nextmatch_slots = ceil($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$nextmatch['id']."'"))/2);
					$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST['matchid']."' ORDER BY score DESC LIMIT ".$nextmatch_slots);
					$baseid = $dbc->database_fetch_assoc($dbc->database_query("SELECT MIN(id) AS id FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$nextmatch['id']."' AND top='".($_POST['j']%4==2?'1':'0')."'"));
					$counter = 0;
					$newwinners = array();
					while($row = $dbc->database_fetch_assoc($data)) {
						if ($row['team'] != 0) { $newwinners[] = $row['team']; }
						if (!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$row['team']."' WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$nextmatch['id']."' AND id='".($baseid['id']+$counter)."'")) {
							$allgood = false;
						}
						$counter++;
					}
					$garbage = array_diff($previouswinners,$newwinners);
					if(sizeof($garbage)>0) {
						$query = "UPDATE tournament_matches_teams SET team='0',score=NULL WHERE tourneyid='".$tournament['tourneyid']."' AND (";
						$counter = 1;
						foreach($garbage as $val) {
							$query .= "team='".$val."'";
							if(sizeof($garbage)!=$counter) $query .= ' OR ';
							$counter++;
						}	
						$query .= ") AND matchid<'".$_POST['matchid']."'";
						if(!$dbc->database_query($query)) {
							$allgood = false;
						}
					}
					if($allgood) {
						$str = 'success.';
					} else {
						$str = 'error!';
					}
				} ?>
				<br />
				<div align="center"><b><?php echo $str; ?></b></div>
				<script language="javascript"><!--
				opener.location.reload(true);
				setTimeout('window.close()',1000);
				// --></script>
				<?php
				require_once "include/_bot_smallwindow.php"; 
			} else {
				if(!empty($_POST["i"])&&!empty($_POST["j"])&&isset($_POST["topscore"])&&isset($_POST["bottomscore"])&&!empty($_POST["matchid"])&&!empty($_POST["b"])) {
					if($tournament["per_team"]==1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
					} elseif($tournament["per_team"]>1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
					}
					$NHPT = pow(2,ceil(log($n)/log(2)));
					$NLPT = pow(2,floor(log($n-1)/log(2)));
	
					if($_POST["b"]=="w") {
						$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".$_POST["i"]."' AND bracket='w' ORDER BY id LIMIT 1"));
						$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".($_POST["i"]+1)."' AND mtc='".ceil($_POST["j"]/4)."' AND bracket='w'"));
					} else {
						if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
							if(($_POST["i"]+1)%2==0&&$_POST["i"]>1) $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".($_POST["i"]+1)."' AND mtc='".ceil($_POST["j"]/2)."' AND bracket='".$_POST["b"]."'"));
							else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".($_POST["i"]+1)."' AND mtc='".ceil($_POST["j"]/4)."' AND bracket='".$_POST["b"]."'"));
						} else {
							if(($_POST["i"]+1)%2==1&&$_POST["i"]>1)	$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".($_POST["i"]+1)."' AND mtc='".ceil($_POST["j"]/2)."' AND bracket='".$_POST["b"]."'"));
							else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd='".($_POST["i"]+1)."' AND mtc='".ceil($_POST["j"]/4)."' AND bracket='".$_POST["b"]."'"));
						}
					}
					if($_POST["topscore"]===0) $temp = "'0.0'";
					elseif($_POST["topscore"]==='') $temp = "NULL";
					else $temp = "'".$_POST["topscore"]."'";
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE matchid='".$_POST["matchid"]."' AND top='1'")) {
						$allgood = false;
					}
					if($_POST["bottomscore"]===0) $temp = "'0.0'";
					elseif($_POST["bottomscore"]==='') $temp = "NULL";
					else $temp = "'".$_POST["bottomscore"]."'";
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE matchid='".$_POST["matchid"]."' AND top='0'")) {
						$allgood = false;
					}

					if($_POST["bottomscore"]==''&&$_POST["topscore"]=='') {
						$currwinner["team"] = 0;
					} else {
						$currwinner = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$_POST["matchid"]."' AND top='".($_POST["topscore"]>$_POST["bottomscore"]?"1":"0")."'"));
					}
					
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".(($_POST["j"]%4==1)||($_POST["j"]%4==2))."'"));
					$previouswinner = $temp["team"];
					if($_POST["b"]=="l") {
						if($tournament["per_team"]==1) {
							$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
						} elseif($tournament["per_team"]>1) {
							$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
						}
						$maxrounds = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(rnd) as rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l'"));
						if(($_POST["i"]+1)==$maxrounds["rnd"]) {
							$top = 0;
						} else {
							$NHPT = pow(2,ceil(log($n)/log(2)));
							$NLPT = pow(2,floor(log($n-1)/log(2)));
							if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
								if(($_POST["i"]+1)%2==0) $top = 1;
								else $top = (($_POST["j"]%4==1)||($_POST["j"]%4==2));
							} else {
								if(($_POST["i"]+1)%2==1) $top = 1;
								else $top = (($_POST["j"]%4==1)||($_POST["j"]%4==2));
							}
						}
					} else {
					 	$top = (($_POST["j"]%4==1)||($_POST["j"]%4==2));
					}
	
					if(($_POST["topscore"]!=$_POST["bottomscore"]||($_POST["bottomscore"]==''&&$_POST["topscore"]==''))&&$previouswinner!=$currwinner["team"]) {
						if($previouswinner!=0) {
							$data = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' AND rnd>'".$_POST["i"]."' AND bracket='".$_POST["b"]."' ORDER BY id DESC");
							while($row = $dbc->database_fetch_assoc($data)) {
								if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'"))) {
									if($row["rnd"]>($_POST["i"]+1)) {
										if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='0' WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'")) {
											$allgood = false;
										}
									}
									if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=NULL WHERE matchid='".$row["id"]."'")) {
										$allgood = false;
									}
								}
							}
						}
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$currwinner["team"]."' WHERE matchid='".$matchinfo["id"]."' AND top='".$top."'")) {
							$allgood = false;
						}
						if($_POST["b"]=="w"&&!empty($_POST["L_rnd"])&&!empty($_POST["L_mtc"])&&isset($_POST["L_top"])&&!empty($_POST["matchid"])) {
							$loser = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$_POST["matchid"]."' AND team!='".$currwinner["team"]."'"));
							$losers_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$_POST["L_rnd"]."' AND mtc='".$_POST["L_mtc"]."' AND bracket='l'"));
							if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$loser["team"]."' WHERE matchid='".$losers_match["id"]."' AND top='".$_POST["L_top"]."'")) {
								$allgood = false;
							}
						}
					}
				} elseif($_POST["i"]==0&&($_POST["j"]==0||$_POST["j"]==1)&&$_POST["b"]=="l"&&isset($_POST["topscore"])&&isset($_POST["bottomscore"])) {
					$allgood = true;
					$winners_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
					$losers_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));
					$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='0' AND mtc='".$_POST["j"]."' AND bracket='l'"));
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$_POST["topscore"]."' WHERE tourneyid='".$tournament["tourneyid"]."' AND top='1' AND matchid='".$winners_id["id"]."'")) {
						$allgood = false;
					}
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$_POST["bottomscore"]."' WHERE tourneyid='".$tournament["tourneyid"]."' AND top='0' AND matchid='".$losers_id["id"]."'")) {
						$allgood = false;
					}
					if($_POST["topscore"]!=$_POST["bottomscore"]) {
						$winner = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".($_POST["topscore"]>$_POST["bottomscore"]?$winners_id["id"]:$losers_id["id"])."'"));
						$query = "UPDATE tournament_matches_teams SET team='".$winner["team"]."' WHERE matchid='".$matchid["id"]."' AND tourneyid='".$tournament["tourneyid"]."' AND top='1'";
						if(!$dbc->database_query($query)) {
							$allgood = false;
						}
					}
					if($allgood) {
						$str = "success.";
					} else {
						$str = "error!";
					}
				} elseif(!empty($_POST["matchid"])&&isset($_POST["leftscore"])&&isset($_POST["rightscore"])) {
					$allgood = true;
					if($_POST["leftscore"]>$_POST["rightscore"]) {
						$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$_POST["matchid"]."' AND top='1'"));
						$winner = $temp["team"];
					} elseif($_POST["leftscore"]<$_POST["rightscore"]) {
						$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$_POST["matchid"]."' AND top='0'"));
						$winner = $temp["team"];
					}
					if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='".($_POST["leftscore"]!=$_POST["rightscore"]?$winner:"0")."' WHERE id='".$_POST["matchid"]."'")) {
						$allgood = false;
					}
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$_POST["leftscore"]."' WHERE matchid='".$_POST["matchid"]."' AND top='1'")||!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$_POST["rightscore"]."' WHERE matchid='".$_POST["matchid"]."' AND top='0'")) {
						$allgood = false;
					}
				} else { 
					$str = "incorrect usage.";
				}
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}
			}
			if(!$tournament["ffa"]) $x->display_slim($str,"disp_tournament.php".(!empty($_POST["id"])?"?id=".$_POST["id"]:""),1);
		} else {
			$x->display_slim("unauthorized.","disp_tournament.php".(!empty($_POST["id"])?"?id=".$_POST["id"]:""),1);
		}
	} elseif(!empty($_GET)&&!empty($_GET["id"])) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET["id"]."'"));
		if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) {
			if($tournament['ttype']==12) {
				if(!empty($_GET['matchid'])&&!empty($_GET['w'])&&empty($_GET['act'])) {
					$current_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET['id']."' AND id='".$_GET['matchid']."'"));
					$next_match = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET['id']."' AND rnd='".($current_match['rnd']+1)."'");
					if($dbc->database_num_rows($next_match)) {
						$next_match_info = $dbc->database_fetch_assoc($next_match);
						$next_match_id = $next_match_info['id'];
					} else {
						$servers = $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament['tourneyid']."'"));
						$dbc->database_query("INSERT INTO tournament_matches (tourneyid, rnd, mtc, server) VALUES ('".$tournament['tourneyid']."','".($current_match['rnd']+1)."','1','".($servers?"1":"0")."')");
						$next_match_id = $dbc->database_insert_id();
					}
					if($dbc->database_query("INSERT INTO tournament_matches_teams (tourneyid, matchid, team) VALUES ('".$tournament['tourneyid']."','".$next_match_id."','".$_GET['w']."')")) {
						$str = 'success.';
					} else {
						$str = 'unknown error.';
					}
				} elseif(!empty($_GET['matchid'])&&!empty($_GET['team'])&&!empty($_GET['act'])&&$_GET['act']=='del') {
					$t_bool = true;
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_GET['matchid']."'"))==1
						&&$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_GET['matchid']."' AND team='".$_GET['team']."'"))) {
						if(!$dbc->database_query("DELETE FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$_GET['matchid']."'")) {
							$t_bool = false;
						}
					}
					if(!$dbc->database_query("DELETE FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid>='".$_GET['matchid']."' AND team='".$_GET['team']."'")) {
						$t_bool = false;
					}
					$t_data = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd>1");
					while($t_row = $dbc->database_fetch_assoc($t_data)) {
						if(!$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$t_row['id']."'"))) {
							if(!$dbc->database_query("DELETE FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$t_row['id']."'")) {
								$t_bool = false;
							}
						}
					}
					if($t_bool) {
						$str = 'success.';
					} else {
						$str = 'unknown error.';
					}	
				} else {
					$str = 'incorrect usage';
				}
			} elseif(!empty($_GET["act"])&&$_GET["act"]=="del"&&!empty($_GET["matchid"])) {
				$allgood = true;
				if($tournament["ttype"]==10) {
					if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='0' WHERE tourneyid='".$_GET["id"]."' AND id='".$_GET["matchid"]."'")) {
						$allgood = false;
					}				
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=NULL WHERE tourneyid='".$_GET["id"]."' AND matchid='".$_GET["matchid"]."'")) {
						$allgood = false;
					}
				} else {
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='0' WHERE tourneyid='".$_GET["id"]."' AND id='".$_GET["matchid"]."'")) {
						$allgood = false;
					}
				}
				
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}			
			} elseif(!empty($_GET["i"])&&!empty($_GET["j"])&&!empty($_GET["w"])&&!empty($_GET["b"])) {
				//($i+1),ceil($j/4),($j%4==3)||($j%4==0)
				$allgood = true;
				if($tournament["per_team"]==1) {
					$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
				} elseif($tournament["per_team"]>1) {
					$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
				}
				$NHPT = pow(2,ceil(log($n)/log(2)));
				$NLPT = pow(2,floor(log($n-1)/log(2)));
				if($_GET["b"]=="w") {
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".$_GET["i"]."' AND bracket='w' ORDER BY id LIMIT 1"));
					$firstmatchid = $temp["id"];
					$lastmatchid = $temp["id"]-1+$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".$_GET["i"]."' AND bracket='w'"));
					$winnersmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT matchid FROM tournament_matches_teams WHERE team='".$_GET["w"]."' AND tourneyid='".$tournament["tourneyid"]."' AND matchid<=".$lastmatchid." AND matchid>=".$firstmatchid));
					$oldmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"])."' AND id='".$winnersmatch["matchid"]."' AND bracket='w'"));
					$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"]+1)."' AND mtc='".ceil($_GET["j"]/4)."' AND bracket='w'"));
				} else {
					if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
						if(($_GET["i"]+1)%2==0&&$_GET["i"]>1) $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"]+1)."' AND mtc='".ceil($_GET["j"]/2)."' AND bracket='".$_GET["b"]."'"));
						else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"]+1)."' AND mtc='".ceil($_GET["j"]/4)."' AND bracket='".$_GET["b"]."'"));
					} else {
						if(($_GET["i"]+1)%2==1&&$_GET["i"]>1) $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"]+1)."' AND mtc='".ceil($_GET["j"]/2)."' AND bracket='".$_GET["b"]."'"));
						else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd='".($_GET["i"]+1)."' AND mtc='".ceil($_GET["j"]/4)."' AND bracket='".$_GET["b"]."'"));
					}
				}
				
				if($tournament["ffa"]) {
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE team='".$_GET["w"]."' AND matchid='".$matchinfo["id"]."'"))) {
						// do nothing
					} elseif($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE team='0' AND matchid='".$matchinfo["id"]."' AND top='".(($_GET["j"]%4==1)||($_GET["j"]%4==2))."'"))) {
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$_GET["w"]."' WHERE matchid='".$matchinfo["id"]."' AND team='0' AND top='".(($_GET["j"]%4==1)||($_GET["j"]%4==2))."' LIMIT 1")) {
							$allgood = false;
						}
					} else {
						$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".(($_GET["j"]%4==1)||($_GET["j"]%4==2))."'"));
						$previouswinner = $temp["team"];
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$_GET["w"]."' WHERE matchid='".$matchinfo["id"]."' AND team='".$temp["team"]."' AND top='".(($_GET["j"]%4==1)||($_GET["j"]%4==2))."' LIMIT 1")) {
							$allgood = false;
						}
					}
				} else {
					if($tournament["per_team"]==1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
					} elseif($tournament["per_team"]>1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
					}
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".(($_GET["j"]%4==1)||($_GET["j"]%4==2))."'"));
					$previouswinner = $temp["team"];
					if($_GET["b"]=="l") {
						$maxrounds = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(rnd) as rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l'"));
						if(($_GET["i"]+1)==$maxrounds["rnd"]) {
							$top = 0;
						} else {
							$NHPT = pow(2,ceil(log($n)/log(2)));
							$NLPT = pow(2,floor(log($n-1)/log(2)));
							if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
								if(($_GET["i"]+1)%2==0) $top = 1;
								else $top = (($_GET["j"]%4==1)||($_GET["j"]%4==2));
							} else {
								if(($_GET["i"]+1)%2==1) $top = 1;
								else $top = (($_GET["j"]%4==1)||($_GET["j"]%4==2));
							}
						}
					} else {
					 	$top = (($_GET["j"]%4==1)||($_GET["j"]%4==2));
					}
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$_GET["w"]."' WHERE matchid='".$matchinfo["id"]."' AND top='".$top."'")) {
						$allgood = false;
					}
					if($_GET["b"]=="w"&&!empty($_GET["L_rnd"])&&!empty($_GET["L_mtc"])&&isset($_GET["L_top"])) {
						$loser = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$oldmatch["id"]."' AND team!='".$_GET["w"]."'"));
						$losers_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$_GET["L_rnd"]."' AND mtc='".$_GET["L_mtc"]."' AND bracket='l'"));
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$loser["team"]."' WHERE matchid='".$losers_match["id"]."' AND top='".$_GET["L_top"]."'")) {
							$allgood = false;
						}
					}
				}
				if($previouswinner!=0&&($previouswinner!=$_GET["w"]||(!empty($_GET["act"])&&$_GET["act"]=="del"))) {
					$data = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$_GET["id"]."' AND rnd>='".$_GET["i"]."' AND bracket='".$_GET["b"]."' ORDER BY id DESC");
					while($row = $dbc->database_fetch_assoc($data)) {
						if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'"))) {
							if(!empty($_GET["act"])&&$_GET["act"]=="del") $temp = 0;
							else $temp = 1;
							if($row["rnd"]>($_GET["i"]+$temp)) {
								if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='0' WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'")) {
									$allgood = false;
								}
							}
							if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=NULL WHERE matchid='".$row["id"]."'")) {
								$allgood = false;
							}
						}
					}
				}
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}
			} elseif($_GET["i"]==0&&($_GET["j"]==0||$_GET["j"]==1)&&!empty($_GET["w"])&&$_GET["b"]=="l") {
				$allgood = true;
				$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='0' AND mtc='".$_GET["j"]."' AND bracket='l'"));
				$query = "UPDATE tournament_matches_teams SET team='".$_GET["w"]."' WHERE matchid='".$matchid["id"]."' AND tourneyid='".$tournament["tourneyid"]."' AND top='1'";
				if(!$dbc->database_query($query)) {
					$allgood = false;
				}
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}
			} elseif(!empty($_GET["matchid"])&&!empty($_GET["w"])) {
				$allgood = true;
				$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT top_x_advance FROM tournament_matches WHERE id='".$_GET["matchid"]."'"));
				if($matchinfo["top_x_advance"]!=$_GET["w"]) {
					if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='".$_GET["w"]."' WHERE id='".$_GET["matchid"]."'")) {
						$allgood = false;
					}
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=NULL WHERE matchid='".$_GET["matchid"]."'")) {
						$allgood = false;
					}
				}
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}
			} else {
				$str = "incorrect usage.";
			}
			$x->display_slim($str,"disp_tournament.php".(!empty($_GET["id"])?"?id=".$_GET["id"]:""),1);
		} else {
			$x->display_slim("unauthorized.","disp_tournament.php".(!empty($_GET["id"])?"?id=".$_GET["id"]:""),1);
		}
	} else {
		$x->display_slim("incorrect usage.","disp_tournament.php");
	}
} ?>