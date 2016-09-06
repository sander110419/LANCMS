<?php
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
$x = new universal('add team','',0);
if ($x->is_secure()) {
	$x->display_top(); ?>
	<strong>administrator</strong>: add teams<br />
	<br />
	<?php
	if (empty($_POST)) {
		$temp = $dbc->database_query("SELECT * FROM tournaments WHERE lockstart='0' OR (ttype='11' AND random!='1') ORDER BY name");
		if ($dbc->database_num_rows($temp)) { ?>
			<font size="1"><strong>unstarted <!--(or started non-random ladder) -->tournaments:</strong><br /></font>
			<form action="<?php echo get_script_name(); ?>" method="GET">
			&nbsp;&nbsp;<select name="show" size="1" style="font: 10px Verdana;"><option value=""></option>
			<?php
			while($row = $dbc->database_fetch_array($temp)) { ?>
				<option value="<?php echo $row['tourneyid']; ?>"<?php echo (!empty($_GET['show'])&&$row['tourneyid'] == $_GET['show']?' selected':''); ?>><?php echo $row['name']; ?></option>
				<?php
			} 
			?>
			</select> <?php
			if(!ALP_TOURNAMENT_MODE) { ?><select name="filter" size="1" style="font: 10px Verdana;"><option value=""></option>
				<option value="0"<?php echo (empty($_GET['filter'])||$_GET['filter']=='0'?' selected':''); ?>>all players</option><option value="1"<?php echo (!empty($_GET['filter'])&&$_GET['filter']==1?' selected':''); ?>>players not on another team</option></select><?php 
			} ?>
			<input type="submit" value="go" class="formcolors" /><br />
			</form>
			<br />
			<table border="0" cellpadding="4" cellspacing="4" width="420" class="centerd"><tr><td>
			<?php
			if(!empty($_GET)&&!empty($_GET['show'])) {
				$data = $dbc->database_query("SELECT * FROM tournaments WHERE (lockstart='0' OR (ttype='11' AND random!='1')) AND tourneyid='".$_GET['show']."'");
				if($dbc->database_num_rows($data)) {
					$tournament = $dbc->database_fetch_assoc($data); ?>
					<font face="arial" size="5"><strong><?php echo $tournament['name']; ?></strong></font><br />
					<font class="sm"><strong><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</strong><br /></font>
					<br />
					<?php
					display_tournament_menu($tournament['tourneyid'],1,1); ?>
					current teams: <strong><?php echo get_num_teams($tournament['tourneyid']);
					if($tournament['max_teams']) echo ' / '.$tournament['max_teams'];
					?></strong><br /><?php
					if(!ALP_TOURNAMENT_MODE) { ?>
						team lock: <strong><?php echo ($tournament['lockteams']?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></strong><br />
						join lock: <strong><?php echo ($tournament['lockjoin']?"<font color=\"#00ff00\">on</font>":"<font color=\"#ff0000\">off</font>"); ?></strong><br />
						<?php
					} ?>
					<br />
					<table border="0" cellpadding="4" cellspacing="4" width="99%"><tr><td>
					<?php 
					if ($tournament['per_team'] == 1 || $tournament['random']) {
						$templ = 1;
					} else {
						$templ = $tournament['per_team'];
					}
					begitem('add '.($templ==1?'competitor':'team')); ?>
					<form action="<?php echo get_script_name(); ?>" method="POST">
					<input type="hidden" name="type" value="add" />
					<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>" />
					<input type="hidden" name="filter" value="<?php echo (!empty($_GET['filter'])?$_GET['filter']:''); ?>" />
					<?php
					if ($templ == 1) { 
						if (empty($_GET['filter']) && !ALP_TOURNAMENT_MODE) { ?>
							(p) = player is already competing in the tournament.<br />
							<?php
						} 
					} else { ?>
						<br />
						<font size=1><strong>team name</strong> (required)<br /></font>
						<input type="text" name="teamname" maxlength=30 style="width: 99%" /><br />
						<?php
						if(!ALP_TOURNAMENT_MODE) { ?>
							<font size=1><strong>team signature</strong> (nickname of the clan, appended to the beginning or end of your name.)<br /></font>
							<input type="text" name="teamsig" maxlength=30 style="width: 99%" /><br />
							<font size=1><strong>team signature placement</strong> (where to add the team signature, to the end or the beginning of the player name.)<br /></font>
							<input type="radio" name="teamsigplace" value="1" class="radio" /> prefix (beginning) <input type="radio" name="teamsigplace" value="2" class="radio" /> suffix (ending) <br />
							<br />
							<?php
							if (!$_GET['filter']) { ?>
								(c) = captain on another team, cannot be moved to the new team.<br />
								(p) = player on another team, can be moved to the new team.<br />
							<?php
							}
						}
					}
					if(!ALP_TOURNAMENT_MODE) {
						for ($i=0;$i<$templ;$i++) { 
							if (!empty($_GET['filter']) && $_GET['filter']==1) {
								$current = array();
								$temp = $dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$_GET['show']."'");
								while ($temprow = $dbc->database_fetch_array($temp)) {
									array_push($current,$temprow["userid"]);
								}
							} ?>
							<br />
							<?php 
							if ($templ > 1) { ?>
								<font size="1"><strong>player <?php echo ($i+1); ?><?php echo ($i==0?" [captain]</strong>":"</strong>"); ?><br /></font>
								<?php 
							} ?>
							<select name="player<?php echo $i; ?>" size="1" style="font: 12px Verdana; width:99%"><option value=""></option>
							<?php
							if ($_GET['filter'] == 1) {
								$query = "SELECT * FROM users";
								if (sizeof($current) > 0) {
									$query .= " WHERE";
									for ($j = 0; $j < sizeof($current); $j++) {
										if ($j == 0) {
											$query .= " userid!='".$current[$j]."'";
										} else {
											$query .= " AND userid!='".$current[$j]."'";
										}
									}
								}
								$query .= " ORDER BY username";
							} else {
								$query = "SELECT * FROM users ORDER BY username";
							}
							$temp = $dbc->database_query($query);
							while ($temprow = $dbc->database_fetch_assoc($temp)) { 
								if ($templ == 1) {
									$captain = false;
								} else {
									$captain = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$_GET["show"]."' AND captainid='".$temprow["userid"]."'"));
								}
								$player = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$_GET["show"]."' AND userid='".$temprow["userid"]."'")); ?>
								<option value="<?php echo $temprow["userid"]; ?>"><?php echo $temprow["username"]; ?> <?php echo ($captain?" (c)":($player?" (p)":"")); ?></option>
							<?php
							} ?>
							</select><br />
						<?php
						}
					} ?>
					<br />
					<br />
					<div align="right"><input type="submit" name="submit" value="<?php echo "add ".($templ==1?"competitor":"team"); ?>" style="width: 120px" class="formcolors"></div>			
					</form>
					<br />
					<?php enditem("add ".($templ == 1?'competitor':'team')); ?>
					</td></tr></table>
				<?php
				} else { 
					if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET["show"]."'"))) { ?>
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
		require_once 'include/cl_validation.php';
		$valid = new validate();
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value("tourneyid")."'"));
		if (!$tournament["lockstart"]||($tournament["random"]!=1&&$tournament["ttype"]==11)) {
			if ($tournament["random"]) {
				$templ = 1;
			} else {
				$templ = $tournament["per_team"];
			}
			if ($valid->get_value("type")=="add") {
				if ($templ>1) {
					$valid->is_empty("teamname","please input a team name.");
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND name='".$valid->get_value('teamname')."'"))) {
						$valid->add_error('the team name you inputted has already been used.');
					}
					if(!ALP_TOURNAMENT_MODE) {
						$users = array();
						$fullteam = true;
						for ($i=0;$i<$templ;$i++) {
							if ($valid->get_value("player".$i)!="") {
								if (in_array($valid->get_value("player".$i),$users)) {
									$username = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$valid->get_value("player".$i)."'"));
									$valid->add_error($username["username"]." is listed twice on the team.");
								} else {
									array_push($users,$valid->get_value("player".$i));
								}
							} elseif ($tournament["lockstart"]&&$tournament["ttype"]==11&&$tournament["random"]!=1) {
								$fullteam = false;
							}
						}
						if (!$fullteam) $valid->add_error("for non-random ladder tournaments that have already begun, the team you create must be full.");
						
						$captains = array();
						for ($i=0;$i<$templ;$i++) {
							if ($valid->get_value("player".$i)!="") {
								if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$valid->get_value("player".$i)."'"))) {
									$temprow = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$valid->get_value("player".$i)."'"));
									array_push($captains,$temprow["username"]);
								}
							}
						}
						
						if (sizeof($captains)>0) {
							$query = "the user".(sizeof($captains)!=1?"s":"").": ";
							for ($i=0;$i<sizeof($captains);$i++) {
								if ($i!=0) {
									$query .= ", ";
								} elseif ($i==(sizeof($captains)-2)) {
									$query .= ", and ";
								}
								$query .= $captains[$i];
							}
							$query .= (sizeof($captains)!=1?" are captains ":" is a captain on ")." on another team.  they cannot be transferred to a new team.";
							$valid->add_error($query);
						}
					}
				} else {
					$valid->is_empty("player0","please input a competitor name.");
					if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."' AND userid='".$valid->get_value("player0")."'"))) {
						$valid->add_error("the player you inputted is already participating in the tournament.");
					}
				}
				
				if (!$valid->is_error()) {
					$bool = true;
					if(!ALP_TOURNAMENT_MODE) {
						for ($i=0;$i<$tournament["per_team"];$i++) {
							if ($valid->get_value("player".$i)!=0) {
								if (!$dbc->database_query("DELETE FROM tournament_players WHERE userid='".$valid->get_value("player".$i)."' AND tourneyid='".$tournament["tourneyid"]."'")) {
									$bool = false;
								}
							}
						}
					}

					if ($templ!=1) {
						if ($tournament["lockstart"]&&$tournament["ttype"]==11&&$tournament["random"]!=1) {
							$temp_competitors = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
							$placing = array(",in_ladder, ladder_ranking",",1,'".($temp_competitors+1)."'");
						} else {
							$placing = array("","");
						}
						$query = "INSERT INTO tournament_teams (tourneyid,name".(!ALP_TOURNAMENT_MODE?",captainid,sig,sigplace":'').$placing[0].") VALUES ('".$tournament["tourneyid"]."','".$valid->get_value("teamname")."'".(!ALP_TOURNAMENT_MODE?",'".$valid->get_value("player0")."','".$valid->get_value("teamsig")."','".$valid->get_value("teamsigplace")."'":'').$placing[1].")";
						//$query = str_replace("''", "NULL", $query);
						if (!$dbc->database_query($query)) {
							$bool = false;
							echo $query."<br />";
						}
						if(!ALP_TOURNAMENT_MODE) $teamid = $dbc->database_insert_id();
					}
					if(!ALP_TOURNAMENT_MODE) {
						for ($i=0;$i<$templ;$i++) {
							if ($valid->get_value("player".$i)!=0) {
								if ($templ==1) {
									if ($tournament["lockstart"]&&$tournament["ttype"]==11&&$tournament["random"]!=1) {
										$temp_competitors = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
										$placing = array(",in_ladder, ladder_ranking",",1,'".($temp_competitors+1)."'");
									} else {
										$placing = array("","");
									}
									$query = "INSERT INTO tournament_players (tourneyid,userid,teamid".$placing[0].") VALUES ('".$tournament["tourneyid"]."','".$valid->get_value("player".$i)."','0'".$placing[1].")";
								} else {
									$query = "INSERT INTO tournament_players (tourneyid,userid,teamid) VALUES ('".$tournament["tourneyid"]."','".$valid->get_value("player".$i)."','".$teamid."')";
								}
								//$query = str_replace("''", "NULL", $query);
								if (!$dbc->database_query($query)) {
									$bool = false;
									echo $query."<br />";
								}
							}
						}
					}
					if ($bool) {
						echo "your ".($tournament["per_team"]!=1?"team":"competitor")." was successfully added.<br /><br />&nbsp;&nbsp;&gt;&nbsp;<a href=\"admin_teams.php?show=".$tournament["tourneyid"].($valid->get_value('filter')!=''?"&filter=".$valid->get_value("filter"):'')."\">add another ".($tournament["per_team"]!=1?"team":"competitor")."</a>.<br /><br />";
					} else {
						echo "there was an error and your ".($tournament["per_team"]!=1?"team":"competitor")." was not added.  try it again.<br /><br />";
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