<?php
require_once 'include/_universal.php'; 
$x = new universal('submit scores','',1);
$x->display_smallwindow_top();
if ($x->is_secure()) {
	require_once 'include/tournaments/fnc_promotewinner.php'; ?>
	<table border="0" cellpadding="4" width="100%"><tr><td>
	<?php
	if ((empty($_GET) || empty($_GET['id']) || empty($_GET['matchid'])) && empty($_POST)) { ?>
		<script language="JavaScript">
		<!--
		window.close();
		// -->
		</script>
		<?php
	} elseif (empty($_POST)) { 
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET['id']."'"));
		if ($tournament['per_team'] > 1) {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE["userid"]."'"));
			$team = $teaminfo['id'];
		} else {
			$team = $_COOKIE['userid'];
		}
		if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$_GET['matchid']."' AND team='".$team."'"))) { 
			$match = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE id='".$_GET['matchid']."'"));
			$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_GET['matchid']."' ORDER by top DESC,team DESC"); ?>
			<b>round <?php echo $match["rnd"]; ?> - match <?php echo $match['mtc']; ?></b><br />
			<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br />
			<?php
			$no_winner = no_winner($tournament['tourneyid'],$_GET['matchid'],$_GET['i'],$_GET['j'],$_GET['b'],$_GET['L_rnd'],$_GET['L_mtc'],$_GET['L_top']); 
			if($no_winner) { ?>
				<form action="chng_update_scores.php" method="POST">
				<input type="hidden" name="id" value="<?php echo $tournament['tourneyid']; ?>">
				<input type="hidden" name="matchid" value="<?php echo $_GET['matchid']; ?>">
				<input type="hidden" name="i" value="<?php echo (!empty($_GET['i'])?$_GET['i']:''); ?>">
				<input type="hidden" name="j" value="<?php echo (!empty($_GET['j'])?$_GET['j']:''); ?>">
				<input type="hidden" name="b" value="<?php echo (!empty($_GET['b'])?$_GET['b']:''); ?>">
				<input type="hidden" name="L_rnd" value="<?php echo (!empty($_GET['L_rnd'])?$_GET['L_rnd']:''); ?>">
				<input type="hidden" name="L_mtc" value="<?php echo (!empty($_GET['L_mtc'])?$_GET['L_mtc']:''); ?>">
				<input type="hidden" name="L_top" value="<?php echo (!empty($_GET['L_top'])?$_GET['L_top']:''); ?>">
				<?php 
			} ?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<?php
			while ($row = $dbc->database_fetch_assoc($data)) { 
				$score_vote = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."' AND matchid='".$_GET['matchid']."' AND entry_id='".$row['id']."'"));
				if ($tournament['per_team'] == 1) { 
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username as name FROM users WHERE userid='".$row['team']."'")); 
				} else {
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$row['team']."'")); 
				} ?>
				<tr>
					<td><font class="sm"><b><?php echo (strlen($team['name'])>24?substr($team['name'],0,22).'...':$team['name']); ?></b><br /></font></td>
					<td width="16"><?php if($no_winner) { ?><input type="text" name="<?php echo $row['id']; ?>_score" maxlength="20" style="font-size: 9px; width: 14px;" value="<?php echo (isset($score_vote['entry_val'])?$score_vote['entry_val']:''); ?>"><?php } else { echo (isset($score_vote['entry_val'])?$score_vote['entry_val']:''); } ?></td>
				</tr>
				<?php
			} ?>
			</table>
			<?php
			if($no_winner) { ?>
				<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br /><input type="submit" value="update" style="font-size: 9px; width: 99%">
				</form>
				<br />
				<?php
				if($tournament['ttype']!=12) echo '<font class="smm">'.get_scores_submitted($tournament['tourneyid'],$_GET['matchid']).' of the required '.get_scores_needed($tournament['tourneyid'],$_GET['matchid']).' scores have been submitted.</font>';
			} else { ?>
				<font class="smm"><br />a winner for this match has already been advanced. your score input is displayed above.</font>
				<?php
			}
		} else { ?>
			<script language="JavaScript">
			<!--
			window.close();
			// -->
			</script>		
			<?php
		}
	} elseif (!empty($_POST)) { 
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_POST['id']."'"));
		$no_winner = no_winner($tournament['tourneyid'],$_POST['matchid'],$_POST['i'],$_POST['j'],$_POST['b'],$_POST['L_rnd'],$_POST['L_mtc'],$_POST['L_top']); 
		if ($tournament['per_team'] > 1) {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
			$team = $teaminfo['id'];
		} else {
			$team = $_COOKIE['userid'];
		}
		if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$_POST['matchid']."' AND team='".$team."'"))&&$no_winner) { 
			if(!$dbc->database_query("DELETE FROM tournament_matches_score_votes WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST['matchid']."' AND userid='".$_COOKIE['userid']."'")) {
				$allgood = false;
			}
			$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST['matchid']."' ORDER BY top DESC, team DESC");
			$allgood = true;
			while ($row = $dbc->database_fetch_assoc($data)) {
				$query = "INSERT INTO tournament_matches_score_votes (tourneyid,matchid,userid,entry_id,entry_val) VALUES ('".$tournament['tourneyid']."','".$_POST['matchid']."','".$_COOKIE['userid']."','".$row['id']."','".$_POST[$row['id'].'_score']."')";
				if (!$dbc->database_query($query)) {
					$allgood = false;
				}
			} 
			
			// promote winner automatically
				if ($tournament['ttype']!=12&&!is_score_discrep($tournament['tourneyid'],$_POST['matchid'])) 
				{
					if ($tournament['ttype'] == 10 && !empty($tournament['tourneyid']) && !empty($_POST['matchid'])) 
					{
						$scores = array();
						$teamsdata = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST['matchid']."' ORDER BY top DESC, team DESC");
						while($teamsrow = $dbc->database_fetch_assoc($teamsdata)) 
						{
							if ($teamsrow['top']) 
							{
                                $scores['left'] = $_POST[$teamsrow['id'].'_score'];
							} else { 
                                $scores['right'] = $_POST[$teamsrow['id'].'_score'];
                            }
						}
						promote_winner($tournament['tourneyid'],$_POST['matchid'],$scores);
					} 
					elseif (($tournament['ttype'] == 4 || $tournament['ttype'] == 5) && !empty($tournament['tourneyid']) && !empty($_POST['matchid']) && !empty($_POST['i']) && !empty($_POST['j']) && !empty($_POST['b']) && !empty($_POST['L_rnd']) && !empty($_POST['L_mtc']) && !empty($_POST['L_top'])) 
					{
						$scores = array();
						$teamsdata = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST["matchid"]."' ORDER BY top DESC, team DESC");
						while($teamsrow = $dbc->database_fetch_assoc($teamsdata)) {
							$scores[$teamsrow['id'].'_score'] = $_POST[$teamsrow['id'].'_score'];
						}
						promote_winner($tournament['tourneyid'],$_POST['matchid'],$scores,$_POST['i'],$_POST['j'],$_POST['b'],$_POST['L_rnd'],$_POST['L_mtc'],$_POST['L_top']);
					} 
					elseif ($tournament['ttype'] == 1 && !empty($tournament['tourneyid']) && !empty($_POST['matchid']) && !empty($_POST['i']) && !empty($_POST['j']) && !empty($_POST['b'])) 
					{
						$scores = array();
						$teamsdata = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$_POST["matchid"]."' ORDER BY top DESC, team DESC");
						$best .= "sdlkfad";
						while($teamsrow = $dbc->database_fetch_assoc($teamsdata)) 
						{
							$best .= $_POST[$teamsrow["id"]."_score"]."t ";
							if($teamsrow['top']) $scores['top'] = $_POST[$teamsrow["id"]."_score"];
							else $scores['bottom'] = $_POST[$teamsrow["id"]."_score"];
						}
						promote_winner($tournament['tourneyid'],$_POST['matchid'],$scores,$_POST['i'],$_POST['j'],$_POST['b']);
					}
				}
			if ($allgood) {
                $str = 'success.';
            } else {
                $str = 'error!';
            }
			?>
			<br />
			<div align="center"><b><?php echo $str; ?></b></div>
			<script language="javascript"><!--
			opener.location.reload(true);
			setTimeout('window.close()',1000);
			// --></script>
			<?php
		} else { ?>
			<script language="JavaScript">
			<!--
			window.close();
			// -->
			</script>
			<?php
		}
	} ?>
	</td></tr></table>
	<?php
}
$x->display_smallwindow_bottom();
?>
