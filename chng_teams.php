<?php
require_once 'include/_universal.php';
include_once 'include/tournaments/_tournament_functions.php';
$x = new universal('add or change team information','team',1);
$x->display_top();
if ($x->is_secure()) {
	if (empty($_POST)) {
		if (!empty($_GET)&&!empty($_GET["id"])&&$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET['id']."'"))) { 
			$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET["id"]."'")); 
			if ($tournament['per_team']>1&&((!$tournament['random']&&!$tournament['lockstart'])||($tournament['random']&&$tournament['lockstart']))) { ?>
				<b><?php echo $tournament['name']; ?></b> <font color="<?php echo $colors['blended_text']; ?>">//</font> <font class="sm"><font color="<?php echo $colors['blended_text']; ?>"><b><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</b></font><br /></font>
				<br />
				<script language="JavaScript">
				<!-- 
				function goTo() {
					document.location.href = document.othermenu.othergo.value;
				} 
				// -->
				</script>
				<div align="right"><form name="othermenu"><font class="sm"><b><a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>">go back</a> &nbsp;|&nbsp; display teams: </b></font><select name="othergo" style="width: 250px; font: 10px Verdana" onChange="goTo()"><option value=""></option><?php
				$data = $dbc->query('SELECT tourneyid,name FROM tournaments ORDER BY name');
				while($row = $data->fetchRow()) { ?>
					<option value="disp_teams.php?id=<?php echo $row["tourneyid"]; ?>"><?php echo $row['name']; ?></option>
					<?php
				} ?></select></form></div>
				<br />
				<?php
				$team = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'");
				if($dbc->database_num_rows($team)) { 
					if(!$tournament['lockstart']||($tournament['lockstart']&&$tournament['random'])) { 
						$teaminfo = $dbc->database_fetch_assoc($team); ?>
						<table border=0 cellpadding=4 cellspacing=4 width="400" align="center"><tr><td>
						<?php begitem('modify tournament team'); ?>
						<form action="chng_teams.php" method="POST">
						<input type="hidden" name="type" value="modify">
						<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
						<input type="hidden" name="teamid" value="<?php echo $teaminfo['teamid']; ?>">
						<a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>"><b>to modify the captain or delete users, go here.</b></a><br />
						<br />
						<font size=1><b>team name</b> &nbsp;&nbsp;<font color="<?php echo $colors['primary']; ?>">(required)</font><br /></font>
						<input type="text" name="teamname" maxlength=30 style="width: 99%" value="<?php echo $teaminfo['name']; ?>"><br />
						<font size=1><b>team signature</b> (nickname of your clan, appended to the beginning or end of your name.)<br /></font>
						<input type="text" name="teamsig" maxlength=30 style="width: 99%" value="<?php echo $teaminfo['sig']; ?>"><br />
						<font size=1><b>team signature placement</b> (where to add your team signature, to the end or the beginning of the player name.)<br /></font>
						<input type="radio" name="teamsigplace" value="1" class="radio"<?php echo ($teaminfo['sigplace']?' checked':''); ?>> prefix (beginning) <input type="radio" name="teamsigplace" value="2" class="radio"<?php echo ($teaminfo['sigplace']==2?' checked':''); ?>> suffix (ending) <br />
						<br />
						<div align="right"><input type="submit" value="modify team"></div>
						</form>
						<?php enditem('modify tournament team'); ?>
						</td></tr></table>
						<?php
					} else {
						echo '<br />this tournament has already been started.  you are not allowed to access these functions after the tournament has begun, unless for a random team tournament.<br /><br />';
					}
				} else {
					if(!$tournament['lockstart']&&is_under_max_teams($tournament['tourneyid'])) { 
						if((!$tournament['lockteams']&&!$tournament['lockjoin'])) {?>
							<table border=0 cellpadding=4 cellspacing=4 width="400" align="center"><tr><td>
							<?php begitem('create new tournament team'); ?>
							<?php 
							if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'"))) { ?>
								<b>warning</b>: creating a new team will remove you from your current team.<br />
								<br />
								<?php
							} ?>
							<form action="chng_teams.php" method="POST">
							<input type="hidden" name="type" value="add">
							<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
							<font size=1><b>captain</b><br /></font>
							<?php
							$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$_COOKIE['userid']."'"));
							echo $temp["username"]."<br />";
							?>
							<font size=1><b>team name</b> <font color="<?php echo $colors['primary']; ?>">(required)</font><br /></font>
							<input type="text" name="teamname" maxlength=30 style="width: 99%"><br />
							<font size=1><b>team signature</b> (nickname of your clan, appended to the beginning or end of your name.)<br /></font>
							<input type="text" name="teamsig" maxlength=30 style="width: 99%"><br />
							<font size=1><b>team signature placement</b> (where to add your team signature, to the end or the beginning of the player name.)<br /></font>
							<input type="radio" name="teamsigplace" value="1" class="radio"> prefix (beginning) <input type="radio" name="teamsigplace" value="2" class="radio"> suffix (ending) <br />
							<div align="right"><input type="submit" value="create team"></div>
							</form>
							<br />
							<?php enditem('create new tournament team'); ?>
							</td></tr></table>
					<?php
						} else {
							echo 'the administrator has locked the teams. during this period, players are not allowed to create teams.<br /><br />';
						}
					} else {
						if($tournament['lockstart']) {
							echo 'this tournament has already been started.  you are not allowed to access these functions after the tournament has begun.<br /><br />';
						} else {
							echo 'this tournament has a maximum limit on the number of teams that can enter, and is currently full.<br /><br />';
						}
					}
				} 
			} else {
				if($tournament['random']) echo 'random team information cannot be modified until after the tournament has started.<br /><br />';
				elseif($tournament['lockstart']) echo 'the tournament has already started.<br /><br />';
				elseif($tournament['per_team']==1) echo 'this is a one player tournament, not a team tournament.<br /><br />';
			}
		}
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();

		if($valid->get_value('tourneyid')!=0) {
			$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value('tourneyid')."'"));
			if(!$tournament['lockstart']&&$tournament['per_team']>1) {
				if($valid->get_value('type')=='add'&&!$tournament['lockjoin']&&!$tournament['lockteams']) {
					$valid->is_empty('teamname','please input a team name.');
					if(!is_under_max_teams($tournament['tourneyid'])) {
						$valid->add_error("maximum ".get_what_teams_called($tournament['tourneyid'],0).' limit already reached.');
					}
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND name='".$valid->get_value('teamname')."'"))) { 
						$valid->add_error("your team name is already being used for this tournament."); 
					}
				
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"))) { 
						$valid->add_error('you are already a captain for this tournament.'); 
					}
	
					if(!$valid->is_error()) {
						if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'"))) {
							if($dbc->database_query("DELETE FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'")) {
								echo 'you were successfully removed from your old team.<br /><br />';
							} else {
								echo 'there was an error you were not removed from your old team.<br /><br />';
							}
						}
						
						$length = strlen($valid->get_value('teamsig'));
						if(substr($valid->get_value('teamsig'),$length-1,$length)==' ') {
							$holder = rtrim($valid->get_value('teamsig'))."&nbsp;";
						} else {
							$holder = $valid->get_value('teamsig');
						}
						if($dbc->database_query("INSERT INTO tournament_teams (tourneyid,name,captainid,sig,sigplace) VALUES ('".$tournament['tourneyid']."','".$valid->get_value('teamname')."','".$_COOKIE['userid']."','".$holder."','".$valid->get_value('teamsigplace')."')")&&$dbc->database_query ("INSERT tournament_players (tourneyid,userid,teamid) VALUES ('".$tournament['tourneyid']."','".$_COOKIE['userid']."','".$dbc->database_insert_id()."')")) {
							echo 'your team, '.$valid->get_value('teamname').', was successfully created.  players may now join your team.<br /><br /> &gt; <a href="chng_teams.php?id='.$tournament['tourneyid'].'">modify your team</a>.<br /><br />';
						} else {
							echo 'there has been an error while adding your team.  it was _not_ added.<br />';
						}
					} else {
						$valid->display_errors();
					}
				}
			}
			if((!$tournament['lockstart']||($tournament['lockstart']&&$tournament['random']))&&$tournament['per_team']>1) {
				if($valid->get_value('type')=='modify') {
					$team = $dbc->database_query("SELECT * FROM tournament_teams WHERE captainid='".$_COOKIE['userid']."' AND tourneyid='".$valid->get_value('tourneyid')."'"); 
					if($dbc->database_num_rows($team)) {
						$teaminfo = $dbc->database_fetch_assoc($team);
						$valid->is_empty('teamname','please input a team name.');
	
						if(!$valid->is_error()) {
							$length = strlen($valid->get_value('teamsig'));
							if(substr($valid->get_value('teamsig'),$length-1,$length)==' ') {
								$holder = rtrim($valid->get_value('teamsig'))."&nbsp;";
							} else {
								$holder = $valid->get_value('teamsig');
							}
							$query = "UPDATE tournament_teams SET name='".$valid->get_value('teamname')."', sig='".$holder."', sigplace='".$valid->get_value('teamsigplace')."' WHERE id='".$teaminfo['id']."' AND tourneyid='".$tournament['tourneyid']."'";
							if($dbc->database_query($query)) {
								echo 'team information successfully changed.<br /><br /> &gt; <a href="chng_teams.php?id='.$tournament['tourneyid'].'">modify information again</a>.<br /><br />';
							} else {
								echo 'there was an unexpected error and your team information was unable to be changed.<br /><br />';
							}
						} else {
							$valid->display_errors();
						}	
					} else {
						echo 'you are not authorized to view this page.';
					}
				}
			} else {
				echo 'this tournament has already been started.  you are not allowed to access these functions after the tournament has begun, unless for a random team tournament.<br /><br />';
			}
		} else {
			echo 'you must specify a tournament.<br /><br />';
		}
	}
} else {
	echo 'you are not authorized to view this page.<br /><br />';
}
$x->display_bottom();
?>
