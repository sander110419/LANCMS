<?php
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
$x = new universal('seeding','',2);
if ($x->is_secure()) {
	$x->display_top(); ?>
	<b>administrator</b>: seeding<br />
	<br />
	<?php
	if (empty($_POST)) {
		$temp = $dbc->database_query('SELECT * FROM tournaments WHERE lockstart=0 ORDER BY name');
		if($dbc->database_num_rows($temp)) { ?>
			<font size=1><b>unstarted tournaments:</b><br /></font>
			<form action="<?php echo get_script_name(); ?>" method="GET">
			&nbsp;&nbsp;<select name="show" size="1" style="font: 10px Verdana;"><option value=""></option>
			<?php
			while ($row = $dbc->database_fetch_array($temp)) { ?>
				<option value="<?php echo $row["tourneyid"]; ?>"<?php echo (!empty($_GET['show'])&&$row["tourneyid"]==$_GET["show"]?" selected":""); ?>><?php echo $row["name"]; ?></option>
				<?php
			} 
			?>
			</select> <input type="submit" value="go" class="formcolors"><br />
			</form>
			<br />
			<table border=0 cellpadding=4 cellspacing=4 width="420" class="centerd"><tr><td><?php
			if (!empty($_GET) && !empty($_GET["show"])) {
				$data = $dbc->database_query("SELECT * FROM tournaments WHERE lockstart='0' AND (random='0' OR random IS NULL) AND tourneyid='".(int)$_GET['show']."'");
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
						team lock: <b><?php echo ($tournament["lockteams"]?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></b><br />
						join lock: <b><?php echo ($tournament["lockjoin"]?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></b><br />
						<br />
						<?php
					} ?>
					<table border=0 cellpadding=4 cellspacing=4 width="99%"><tr><td colspan="2">
					<?php 
					begitem('update seeds'); ?>
					<form action="<?php echo get_script_name(); ?>" method="POST">
					<input type="hidden" name="tourneyid" value="<?php echo $tournament["tourneyid"]; ?>">
					</td></tr>
					<?php
					if ($tournament['per_team'] == 1) {
						$query = $dbc->database_query("SELECT tournament_players.*,users.username AS name FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament['tourneyid']."' ORDER BY name");
					} else {
						$query = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY name");
					} 
					$num_of_teams = $dbc->database_num_rows($query);
					while ($row = $dbc->database_fetch_assoc($query)) { ?>
						<tr>
							<td width="30"><input type="text" name="seed_<?php echo $row["id"]; ?>" maxlength="4" style="width: 30px" value="<?php echo (!empty($row["ranking"])&&$row['ranking']<=$num_of_teams?$row["ranking"]:""); ?>"></td>
							<td width="100%"><?php echo $row["name"]; ?></td>
						</tr>
						<?php
					} ?>
					<tr><td colspan="2"><br />
					<div align="right"><input type="submit" name="submit" value="update seeds" style="width: 120px" class="formcolors"></form></div>
					<br />
					<?php enditem('update seeds'); ?>
					</td></tr></table>
					<div style="padding: 4px 2px 4px 4px; width: 100%; border: 1px dotted <?php echo $colors['primary']; ?>">
					<strong>options</strong>: 
					<form action="admin_generic.php" method="POST" style="display: inline">
						<input type="hidden" name="ref" value="<?php echo get_script_name().'?show='.(int)$_GET['show']; ?>">
						<input type="hidden" name="case" value="erase_seeding">
						<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
						<input type="submit" name="submit" value="clear all seeds" class="formcolors2">
					</form>
					<?php
					if(!ALP_TOURNAMENT_MODE) { ?>
						<form action="admin_generic.php" method="POST" style="display: inline">
							<input type="hidden" name="ref" value="<?php echo get_script_name().'?show='.(int)$_GET['show']; ?>">
							<input type="hidden" name="case" value="lock_teams">
							<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
							<input type="submit" name="submit" value="lock teams" class="formcolors2">
						</form>
						<form action="admin_generic.php" method="POST" style="display: inline">
							<input type="hidden" name="ref" value="<?php echo get_script_name().'?show='.(int)$_GET['show']; ?>">
							<input type="hidden" name="case" value="unlock_teams">
							<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
							<input type="submit" name="submit" value="unlock teams" class="formcolors2">
						</form>
						<?php
					} ?>
					</div>
					<?php
				} else { 
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE random='1' AND tourneyid='".(int)$_GET['show']."'"))) { ?>
						you can't seed random team tournaments.  they are random.<br /><br />
						<?php
					} elseif ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['show']."' AND lockstart='1'"))) { ?>
						the tournament you've selected has already begun.  you cannot add a team.  to un-start your tournament and erase the brackets, go <a href="admin_tournament_unstart.php">here</a>.<br /><br />
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
			there are no unstarted tournaments in the database.  you can add a tournament by going <a href="admin_tournament.php">here</a>.<br /><br />
			<?php
		}
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value("tourneyid")."'"));
		if (!$tournament['lockstart']) {
			if (!$valid->is_error()) {
				$allgood = true;

				if ($tournament['per_team'] == 1) {
					$query = $dbc->database_query("SELECT tournament_players.*,users.username AS name FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament["tourneyid"]."' ORDER BY name");
				} else {
					$query = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY name");
				} 

				while ($row = $dbc->database_fetch_assoc($query)) {
					if($valid->get_value("seed_".$row["id"])>0) {
						$seed = $valid->get_value("seed_".$row["id"]);
					} else {
						$seed = $dbc->database_num_rows($query)+1;
					}
					if ($tournament['per_team'] == 1) {
						if (!$dbc->database_query("UPDATE tournament_players SET ranking='".$seed."' WHERE id='".$row["id"]."'")) {
							$allgood = false;
						}
					} else {
						if (!$dbc->database_query("UPDATE tournament_teams SET ranking='".$seed."' WHERE id='".$row["id"]."'")) {
							$allgood = false;
						}
					}
				}
				
				if ($allgood) {
					echo "your seeds were successfully inserted.<br /><br />&nbsp;&nbsp;&gt;&nbsp;<a href=\"admin_seeding.php?show=".$tournament["tourneyid"]."\">seed again</a>.<br /><br />";
				} else {
					echo 'there was an error and your seeds were not inserted.  try it again.<br /><br />';
				}
			} else {
				$valid->display_errors();
			}
		} else {
			echo 'the tournament you are trying to modify has already been started.  nice try.<br /><br />';
		}
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}