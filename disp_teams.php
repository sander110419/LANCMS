<?php
require_once 'include/_universal.php';
include_once 'include/tournaments/_tournament_functions.php';
$x = new universal('display teams','',0);
if ($x->is_secure()) { 
	$x->display_top(0,0); ?>
	<table border="0" cellpadding="8" width="100%"><tr><td>
	<?php
	if (!empty($_GET) && !empty($_GET['id']) && $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']))) { 
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id'])); ?>
		<script language="JavaScript">
		<!-- 
		function goTo() {
			if(document.othermenu.othergo.value!="") document.location.href = document.othermenu.othergo.value;
		} 
		// -->
		</script>
		<div align="right"><form name="othermenu"><font class="sm"><strong>display teams: </strong></font><select name="othergo" style="width: 250px; font: 10px Verdana" onChange="goTo()"><?php
		$data = $dbc->query('SELECT tourneyid, name FROM tournaments ORDER BY name');
		while($row = $data->fetchRow()) { ?>
			<option value="disp_teams.php?id=<?php echo $row['tourneyid']; ?>"<?php echo (!empty($_GET['id']) && $row['tourneyid'] == $_GET['id']?' selected':''); ?>><?php echo $row['name']; ?></option>
			<?php
		} ?></select>&nbsp;&nbsp;&nbsp;[<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><strong>back to tournament information</strong></a>]</form></div>
			<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><font class="tourneytitle"><?php echo $tournament['name']; ?></font></a><br />
			<font class="sm"><strong><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</strong><br /></font>
			<br />
			<?php
			display_tournament_menu($tournament['tourneyid'],1,1);
			?>
		<table border=0 cellpadding=0 cellspacing=0 style="width: 600px; font-size: 11px" align="center" class="centerd">
		<?php
		if (!$tournament['lockstart'] && current_security_level() >= 2 && !ALP_TOURNAMENT_MODE) { ?>
			<tr>
				<td>
					<div style="padding: 4px 2px 4px 4px; width: 100%; border: 1px dotted <?php echo $colors['primary']; ?>">
					<strong>options</strong>: 
					<form action="admin_generic.php" method="POST" style="display: inline">
						<input type="hidden" name="ref" value="<?php echo basename(get_script_name()).'?id='.$_GET['id']; ?>">
						<input type="hidden" name="case" value="<?php if($tournament['lockjoin'] && $tournament['lockteams']) { echo "un"; } ?>lock_teams">
						<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
						<input type="submit" name="submit" value="<?php if($tournament['lockjoin'] && $tournament['lockteams']) { echo "un"; } ?>lock teams" class="formcolors2">
					</form>
					</div>
				</td>
			</tr>
			<?php
			
		}
		if (!$tournament['lockstart'] && $tournament['random']) { 
			$n = $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_players WHERE tourneyid='.(int)$tournament['tourneyid'])); 
			if ($n%$tournament['per_team'] !=0 || $n == 0) { ?>
				<tr>
				<td height="30">
					<table height="100%" width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid <?php echo $colors['cell_title']; ?>">
						<tr>
						<td valign="middle">
						 &gt; &gt; &gt; <strong>random teams: need <?php echo ($tournament['per_team']-$n%$tournament['per_team']); ?> more player<?php echo ($tournament['per_team']-$n%$tournament['per_team']>1?'s':''); ?> <?php if($n>$tournament['per_team']) { ?>or <?php echo ($n%$tournament['per_team']); ?> less player<?php echo ($n%$tournament['per_team']>1?'s':''); ?> <?php } ?>for the tournament!</strong>
						</td>
						</tr>
					</table>
					<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
				</td>
				</tr>
				<?php
			}
		}
		if (current_security_level() >= 1 && !ALP_TOURNAMENT_MODE) {
			if( !$tournament['lockstart'] || $tournament['random'] || ( $tournament['ttype']==11&&$tournament['lockstart']&&!$tournament['random'] )) { ?>
				<tr>
				<td height="30">
					<table height="100%" width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid <?php echo $colors['cell_title']; ?>">
						<tr>
						<td valign="middle">
							&gt; &gt; &gt; <?php
							$txt = get_what_teams_called($tournament['tourneyid']);
							$teams = get_num_teams($tournament['tourneyid'],0);
							$under_limit = is_under_max_teams($tournament['tourneyid']);
							echo $teams." ".$txt.($tournament['max_teams']>0?' ['.$tournament['max_teams'].' '.$txt.' maximum]':'');
							?><br /><?php
								$is_playing = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'"));
								$is_captain = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
								if ($tournament['per_team']>1) $is_pugging = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."' AND teamid=0"));
								else $is_pugging = false;
								if (!$is_playing && !$tournament['lockjoin'] && !$tournament['lockteams'] && (!$tournament['lockstart'] || ($tournament['ttype'] == 11 && $tournament['lockstart'] && !$tournament['random']))) { 
									if (($tournament['random'] && !$tournament['lockstart']) || $tournament['per_team'] == 1) { 
										if ($under_limit) { ?>
											&gt; &gt; &gt; <a href="chng_update_teams.php?id=<?php echo $tournament['tourneyid']; ?>&act=add" style="color:<?php echo $colors['primary']; ?>"><strong>sign up for this tournament.</strong></a>
											<?php
										}
									} else { 
										if ($under_limit) { ?>
											&gt; &gt; &gt; <a href="chng_teams.php?id=<?php echo $tournament['tourneyid']; ?>" style="color: <?php echo $colors['primary']; ?>"><strong>create your own team.</strong></a>
											<?php
										}
										if (!$tournament['lockstart']) { 
											if ($under_limit) { ?>
												&nbsp;&nbsp;OR&nbsp;&nbsp; 
												<?php
											} else { ?>&gt; &gt; &gt; <?php } ?><a href="chng_update_teams.php?id=<?php echo $tournament['tourneyid']; ?>&act=add&u=1" style="color: <?php echo $colors['primary']; ?>"><strong>sign up without a team (pug).</strong></a><?php
										}
									}
								} elseif ($is_playing && !$is_captain && !$tournament['lockjoin'] && !$tournament['lockteams'] && !$tournament['lockstart']) { ?>
									&gt; &gt; &gt; <a href="chng_update_teams.php?id=<?php echo $tournament['tourneyid']; ?>&act=del" style="color: <?php echo $colors['primary']; ?>"><strong>withdraw from this tournament.</strong></a> 
									<?php 
									if (!$tournament['random'] && $tournament['per_team']>1) { 
										if ($under_limit) { ?>
											&nbsp;&nbsp;OR&nbsp;&nbsp; <a href="chng_teams.php?id=<?php echo $tournament['tourneyid']; ?>" style="color: <?php echo $colors['primary']; ?>"><strong>create your own new team.</strong></a>
											<?php
										}
										if (!$is_pugging) { 
											if ($under_limit) { ?>
												&nbsp;&nbsp;OR&nbsp;&nbsp; 
												<?php
											} else { ?>&gt; &gt; &gt; <?php } ?><a href="chng_update_teams.php?id=<?php echo $tournament['tourneyid']; ?>&act=add&u=1" style="color: <?php echo $colors['primary']; ?>"><strong>move to unassigned status (pug).</strong></a><br /><?php
										}
									}
								} elseif ($is_playing && $is_captain) { ?>
									&gt; &gt; &gt; <a href="chng_teams.php?id=<?php echo $tournament['tourneyid']; ?>" style="color: <?php echo $colors['primary']; ?>"><strong>modify the name and signature of your team.</strong></a>
									<?php
								} else { ?>
									<font color="<?php echo $colors['cell_title']; ?>"><strong>locked.</strong></font><br />
									<?php
								} ?>
						</td>
						</tr>
					</table>
					<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
				</td>
				</tr>
				<?php
			}
		}
		$counter = 1;
		if (ALP_TOURNAMENT_MODE) { 
			$seeding = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND ranking>0"));
			$data = $dbc->database_query("SELECT name,ranking FROM tournament_teams WHERE tourneyid='".$_GET['id']."' ORDER BY name"); 
			if($seeding) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["primary"]; ?>">
							<tr>
							<td width="20">&nbsp;</td>
							<td valign="middle"><strong>team name</strong></a></td>
							<td width="50" valign="middle" align="left"><font color="<?php echo $colors['primary']; ?>"><strong>seed</strong></font></td>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
			} 
			while ($team = $dbc->database_fetch_assoc($data)) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["cell_title"]; ?>">
							<tr>
							<td width="20"><font color="<?php echo $colors['cell_title']; ?>" class="sm">[<?php echo $counter; ?>]</font></td>
							<td valign="middle"><strong><?php echo $team["name"]; ?></strong></a></td>
							<?php
							if ($seeding) { ?>
								<td width="50" align="left"><font color="<?php echo $colors['primary']; ?>"><strong><?php echo ($team['ranking']>0&&$team['ranking']<=$dbc->database_num_rows($data)?$team['ranking']:''); ?></strong></font></td>
								<?php
							} ?>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
				$counter++;
			} 
		} elseif ($tournament['per_team'] == 1 || ($tournament['random'] && !$tournament['lockstart'])) { 
			$seeding = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND ranking>0"));
			$data = $dbc->database_query("SELECT tournament_players.userid as userid,tournament_players.ranking AS ranking,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$_GET['id']."' ORDER BY username"); 
			if($seeding) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["primary"]; ?>">
							<tr>
							<td width="20">&nbsp;</td>
							<td valign="middle"><strong>team name</strong></a></td>
							<td width="50" valign="middle" align="left"><font color="<?php echo $colors['primary']; ?>"><strong>seed</strong></font></td>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
			} 
			while ($player = $dbc->database_fetch_assoc($data)) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["cell_title"]; ?>">
							<tr>
							<td width="20"><font color="<?php echo $colors['cell_title']; ?>" class="sm">[<?php echo $counter; ?>]</font></td>
							<td valign="middle"><a href="disp_users.php?id=<?php echo $player['userid']; ?>"><strong><?php echo $player["username"]; ?></strong></a></td>
							<?php
							if ($seeding) { ?>
								<td width="50" align="left"><font color="<?php echo $colors['primary']; ?>"><strong><?php echo ($player['ranking']>0&&$player['ranking']<=$dbc->database_num_rows($data)?$player['ranking']:''); ?></strong></font></td>
								<?php
							} ?>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
				$counter++;
			} 
		} else {
			$seeding = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND ranking>0"));
			$data = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY name"); 
			$is_captain = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
			if($seeding) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["primary"]; ?>">
							<tr>
							<td valign="middle"><?php spacer(14); ?><strong>team name</strong>&nbsp;&nbsp;<font color="<?php echo $colors['primary']; ?>">[<strong>seed</strong>]</font>
							</td>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
			}
			while ($team = $dbc->database_fetch_assoc($data)) { 
				$captain = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$team['captainid']."'")); 
				$players = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$_GET['id']."' AND tournament_players.teamid='".$team['id']."' ORDER BY username"); 
				$numplayers = $dbc->database_num_rows($players);
				?>
				<tr>
					<td height="70">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php if($numplayers==$tournament['per_team']) { echo $colors['blended_text']; } else { echo $colors['cell_title']; } ?>">
						<tr>
						<td valign="middle">
							<img src="img/pxt.gif" width="14" height="1" border="0" alt="" /><strong><?php echo $team["name"]; ?></strong><?php
							if($seeding&&$team['ranking']>0&&$team['ranking']<=$dbc->database_num_rows($data)) { ?>
								&nbsp;<font color="<?php echo $colors['primary']; ?>">[<strong><?php echo $team['ranking']; ?></strong>]</font>
								<?php
							} ?><br />
							<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
							<font class="sm">
								<img src="img/pxt.gif" width="28" height="1" border="0" alt="" />tag: <?php echo $team['sig']; ?><br />
								<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
								<img src="img/pxt.gif" width="28" height="1" border="0" alt="" />captain: <?php echo $captain['username']; ?><br />
								<font color="<?php echo $colors['blended_text']; ?>">
								<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
								<?php 
								if($numplayers<$tournament['per_team']) { ?>
									<img src="img/pxt.gif" width="28" height="1" border="0" alt="" /><strong>players: <?php echo $numplayers; ?>/<?php echo $tournament['per_team']; ?> &nbsp;&nbsp;(need <?php echo ($tournament['per_team']-$numplayers); ?> more)</strong><br />
									<?php
								} elseif($numplayers==$tournament['per_team']) { ?>
									<img src="img/pxt.gif" width="28" height="1" border="0" alt="" /><strong>team full.</strong><br />
									<?php
								} ?>
								</font>
							</font>
						</td>
						<td width="200" align="right">
						<form<?php if(current_security_level()>=1&&$team['captainid']==$_COOKIE['userid']) { echo ' action="chng_update_teams.php" method="POST"'; } ?>>
							<?php 
							if(current_security_level()>=1&&$team['captainid']==$_COOKIE['userid']) { ?>
								<input type="hidden" name="id" value="<?php echo $tournament['tourneyid']; ?>">
								<input type="hidden" name="teamid" value="<?php echo $team['id']; ?>">
								<?php
							} ?>
							<select name="userid" size="<?php if(current_security_level()>=1&&$team['captainid']==$_COOKIE['userid']) { echo '1'; } else { echo '6'; } ?>" style="width: 190px; color: <?php echo $colors['text']; ?>; background-color: <?php echo $colors['cell_background']; ?>">
							<?php
							if (current_security_level() >= 1 && $team['captainid'] == $_COOKIE['userid']) { 
								echo '<option value=""></option>';
								$temp = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$_GET['id']."' AND tournament_players.teamid='".$team['id']."' AND tournament_players.userid!='".$team['captainid']."' ORDER BY username");
							} else {
								$temp = $players;
							}
							while ($playerinfo = $dbc->database_fetch_assoc($temp)) { ?>
								<option value="<?php echo $playerinfo['userid']; ?>"><?php echo ($team['sigplace']==1?$team['sig']:'').$playerinfo['username'].($team['sigplace']==0?$team['sig']:''); ?></option>
								<?php
							}
							?>
							</select>
							<?php
							if (current_security_level() >= 1 && $team['captainid'] == $_COOKIE['userid']) { ?>
								<div align="center"><font class="sm"><?php if(!$tournament['lockteams']&&!$tournament['lockjoin']&&!$tournament['lockstart']) { ?><input type="radio" name="act" value="del" class="radio" /> delete <input type="radio" name="act" value="pug" class="radio" /> pug <?php } ?><input type="radio" name="act" value="cpt" class="radio" /> captain<br /></font></div>
								<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />
								<input type="reset" name="reset" value="reset" style="font: 10px Verdana;" /> <input type="submit" name="submit" value="submit" style="font: 10px Verdana;" />
								<?php
							} ?>
						</form>
						</td>
						<?php
						if (current_security_level() >= 1 && !$tournament['lockstart'] && !$tournament['lockjoin']) { ?>
							<td width="160" bgcolor="<?php echo $colors['cell_title']; ?>">
								<div align="center"><strong><?php
								if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."' AND userid='".$_COOKIE['userid']."'"))) {
									echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\">quit this team</a>.<br />";
									if($team['captainid']==$_COOKIE['userid']) {
										echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=delt\">delete this team</a>.<br />";
									}
								} else {
									if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."'"))<$tournament['per_team']) {
										echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add&teamid=".$team['id']."\">join this team</a>.";
									} else {
										echo 'team is full.';
									}
								}
								?></strong></div>
							</td>
							<?php
						} ?>
					</tr>
					</table>
					<br />
					</td>
				</tr>
				<?php
			}
			$data = $dbc->database_query("SELECT tournament_players.userid as userid,tournament_players.ranking AS ranking,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament['tourneyid']."' AND tournament_players.teamid=0 ORDER BY username");
			if ($dbc->database_num_rows($data)) { ?>
				<tr>
					<td height="30">
						<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors['cell_title']; ?>">
							<tr>
							<td colspan="2"> &gt; &gt; &gt; <strong>unassigned "pug" players</strong></td>
							</tr>
						</table>
						<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
					</td>
				</tr>
				<?php
				if ($is_captain) {
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'"));
					$players = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$team['id']."'"));
					if ($players < $tournament['per_team']) {
						$not_full = true;
					} else {
						$not_full = false;
					}
				}
				while ($player = $dbc->database_fetch_assoc($data)) { ?>
					<tr>
						<td height="30">
							<table height="100%" width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid <?php echo $colors['cell_title']; ?>">
								<tr>
								<td width="20"><font color="<?php echo $colors['blended_text']; ?>"><?php echo $counter; ?></font></td>
								<td valign="middle"><a href="disp_users.php?id=<?php echo $player['userid']; ?>"><strong><?php echo $player['username']; ?></strong></a></td>
								<td width="120"><?php
								if($is_captain&&$not_full) { ?>
									<a href="chng_update_teams.php?id=<?php echo $tournament['tourneyid']; ?>&act=draft&uid=<?php echo $player['userid']; ?>"><strong>draft this player.</strong></a>
									<?php
								} else { echo '&nbsp;'; } ?></td>
								</tr>
							</table>
							<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />	
						</td>
					</tr>
					<?php
					$counter++;
				} 
			}
		}?>
		</table>
		<?php
	} else { ?>
		<font class="sm">
		&nbsp;please select a tournament:<br />
		</font>
		<?php
		$data = $dbc->database_query("SELECT * FROM tournaments ORDER BY name");
		while ($row = $dbc->database_fetch_assoc($data)) { ?>
			&nbsp;&nbsp;&nbsp;&gt;&nbsp;<a href="<?php echo get_script_name(); ?>?id=<?php echo $row['tourneyid']; ?>"><?php echo $row['name']; ?></a><br />
			<?php
		} ?>
		<br />
		<?php
	} ?>
	</td></tr></table><?php
	$x->display_bottom(0,0);
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>

