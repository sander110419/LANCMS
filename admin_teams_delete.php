<?php
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
$x = new universal('delete teams'.(!ALP_TOURNAMENT_MODE?'/players':''),'',0);
if ($x->is_secure()) {
	$x->display_top(); ?>
	<b>administrator</b>: delete teams<?php echo (!ALP_TOURNAMENT_MODE?'/players':''); ?><br />
	<br />
	<?php
	if (empty($_POST)) {
		$temp = $dbc->query("SELECT tourneyid, name FROM tournaments WHERE lockstart='0' OR (ttype='11' AND random!='1') ORDER BY name");
		if($temp->numRows()) { ?>
			<font size=1><b>unstarted <!--(or started non-random ladder) -->tournaments:</b><br /></font>
			<form action="<?php echo get_script_name(); ?>" method="get">
			&nbsp;&nbsp;<select name="show" size="1" style="font: 10px Verdana;"><option value=""></option>
			<?php
			while($row = $temp->fetchRow()) { ?>
				<option value="<?php echo $row['tourneyid']; ?>"<?php echo (!empty($_GET['show']) && $row['tourneyid']==$_GET['show']?' selected':''); ?>><?php echo $row['name']; ?></option>
				<?php
			}
			?>
			</select> <?php
			if(!ALP_TOURNAMENT_MODE) { ?>
				<select name="filter" size="1" style="font: 10px Verdana;"><option value=""></option>
				<option value="0"<?php echo (empty($_GET['filter'])||$_GET['filter']=='0'?' selected':''); ?>>delete teams</option><option value="1"<?php echo ($_GET['filter']==1?' selected':''); ?>>delete players</option></select>
				<?php
			} ?>
			<input type="submit" value="go" class="formcolors"><br />
			</form>
			<br />
			<table border=0 cellpadding=4 cellspacing=4 width="420" class="centerd"><tr><td>
			<?php
			if (!empty($_GET) && !empty($_GET['show'])) {
				$data = $dbc->database_query("SELECT * FROM tournaments WHERE (lockstart='0' OR (ttype='11' AND random!='1')) AND tourneyid='".$_GET['show']."'");				
				if ($dbc->database_num_rows($data)) {
					$tournament = $dbc->database_fetch_assoc($data); ?>
					<font face="arial" size="5"><b><?php echo $tournament['name']; ?></b></font><br />
					<font class="sm"><b><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</b><br /></font>
					<br />
					<?php
					display_tournament_menu($tournament['tourneyid'],1,1); ?>
					current teams: <strong><?php echo get_num_teams($tournament['tourneyid']);
					if($tournament['max_teams']) echo ' / '.$tournament['max_teams'];
					?></strong><br /><?php
					if(!ALP_TOURNAMENT_MODE) { ?>
						team lock: <b><?php echo ($tournament['lockteams']?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></b><br />
						join lock: <b><?php echo ($tournament['lockjoin']?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></b><br />
						<br />
						<?php
					} ?>
					<table border=0 cellpadding=4 cellspacing=4 width="99%"><tr><td>
					<?php
					if ( ( !empty($_GET['filter']) && $_GET['filter'] == 1 ) || $tournament['per_team'] == 1) {
						$templ = 0;
					} else {
						$templ = 1;
					}
					if ($templ == 0 && $tournament['ttype'] == 11 && $tournament['lockstart'] && $tournament['per_team'] > 1) {
						echo 'you cannot delete single players in ladder tournaments after they have started.<br /><br />';
					} else {
						begitem('delete '.($templ==0?'player':'team')); ?>
						<form action="<?php echo get_script_name(); ?>" method="post">
						<input type="hidden" name="type" value="del">
						<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
						<input type="hidden" name="templ" value="<?php echo $templ; ?>">
						<font size=1><b><?php echo ($templ==0?'player':'team'); ?> name</b> (required)<br /></font>
						<select name="id" style="width: 99%"><option value=""></option>
						<?php 
						if ($tournament['per_team'] == 1 && $tournament['lockstart'] && $tournament['ttype'] == 11) {
							$ladder = array(' AND in_ladder=0','');
						} elseif ($tournament['lockstart'] && $tournament['ttype'] == 11) {
							$ladder = array('',' AND tournament_players.in_ladder=0');
						}
						if ($templ) {
							$query = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'".$ladder[0]." ORDER BY name");
						} else {
							$query = $dbc->database_query("SELECT tournament_players.*,users.username AS name FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament['tourneyid']."'".$ladder[1]." ORDER BY name");
						}
						while ($row = $dbc->database_fetch_assoc($query)) { ?>
							<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option><?php
						}
						?>
						</select><br />
						<br />
						<div align="right"><input type="submit" name="submit" value="<?php echo "delete ".($templ==0?'player':'team'); ?>" style="width: 120px" class="formcolors"></div>			
						</form>
						<br />
						<?php enditem('delete '.($templ==0?'player':'team'));
					} ?>
					</td></tr></table>
				<?php
				} else { 
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET['how']."'"))) { ?>
						the tournament you've selected has already begun.  you cannot add a team.  to un-start your tournament and erase the brackets, go <a href="admin_tournament_unstart.php">here</a>.  remember, you can add teams to a non-random tournament after it has started.<br /><br />
					<?php
					} else { ?>
						the tournament you've selected cannot be found in the database.  please try again.<br /><br />
						<?php
					}
				}
			} else { 
				echo 'you didn\'t select a tournament.  you can do so with the select box above.<br /><br />';
			} ?>
			<br />
			</td></tr></table>
		<?php
		} else { ?>
			there are no unstarted or non-random ladder tournaments in the database to add to.  you can add a tournament by going <a href="admin_tournament.php">here</a>.<br /><br />
			<?php
		}
	} else {
		include 'include/cl_validation.php';
		$valid = new validate();
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value('tourneyid')."'"));
		if (!$tournament['lockstart'] || ($tournament['random'] !=1 && $tournament['ttype'] == 11)) {
			if ($valid->get_value('type') == 'del') {
				$templ = $valid->get_value('templ');
				$valid->is_empty('id','you didn\'t select the name of the '.($templ==0?'player':'team').' you want to delete!');
				if ($templ == 0 && $tournament['ttype'] == 11 && $tournament['lockstart'] && $tournament['per_team'] > 1) {
					$valid->add_error('you cannot delete single players in ladder tournaments after they have started.');
				}
				if ( $tournament['per_team'] == 1 && $tournament['lockstart'] && $tournament['ttype'] == 11) {
					// delete players from one player team tournaments
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."' AND in_ladder=1"))) {
						$valid->add_error('you cannot delete a player that is already participating in the ladder.');
					}
				} elseif ($tournament['lockstart'] && $tournament['ttype'] == 11) {
					// delete players and teams from team tournaments
					if ($templ == 1) {
						// teams
						if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."' AND in_ladder=1"))) {
							$valid->add_error('you cannot delete a team that is already participating in the ladder.');
						}
					} else {
						// players
						if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."' AND in_ladder=1"))) {
							$valid->add_error('you cannot delete a player that is already participating in the ladder.');
						}
					}
				}
				if (!$valid->is_error()) {
					$allgood = true;
					if ($templ == 0) {
						// delete player
						$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."'"));
						if ($tournament['per_team']>1) {
							if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$team['userid']."'"))) {
								$temp = $dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['teamid']."' AND id!='".$valid->get_value('id')."' ORDER BY RAND() LIMIT 1");
								if ($dbc->database_num_rows($temp)) {
									$next_captain = $dbc->database_fetch_assoc($temp);
									if (!$dbc->database_query("UPDATE tournament_teams SET captainid='".$next_captain['userid']."' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$team['teamid']."'")) {
										$allgood = false;
									}
								} else {
									if (!$dbc->database_query("UPDATE tournament_teams SET captainid='0' WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$team['teamid']."'")) {
										$allgood = false;
									}
								}
							}
						}
						if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."'")) {
							$allgood = false;
						}
						if (!$dbc->database_query("DELETE FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$team['userid']."'")) {
							$allgood = false;
						}
					} elseif ($templ == 1) {
						// delete team
						
						$tdata = $dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$valid->get_value('id')."'");
						while ($trow = $dbc->database_fetch_assoc($tdata)) {
							if (!$dbc->database_query("DELETE FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$trow['userid']."'")) {
								$allgood = false;
							}
						}
						if (!$dbc->database_query("DELETE FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$valid->get_value('id')."'")) {
							$allgood = false;
						}
						if (!$dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$valid->get_value('id')."'")) {
							$allgood = false;
						}
					}
					if ($allgood) {
						echo 'your '.($templ==0?'player':'team').' was successfully deleted.<br /><br />&nbsp;&nbsp;&gt;&nbsp;<a href="admin_teams_delete.php?show='.$tournament['tourneyid'].(!ALP_TOURNAMENT_MODE?'&filter='.$templ:'').'">delete another '.($templ==0?'player':'team').'</a>.<br /><br />';
					} else {
						echo 'there was an error and your '.($templ==0?'player':'team')." was not deleted.  try it again.<br /><br />";
					}
				} else {
					$valid->display_errors();
				}
			}
		} else {
			echo 'the tournament you are trying to modify has already been started.  nice try.<br /><br />';
		}
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>