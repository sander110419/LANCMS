<?php
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
$x = new universal('start tournament','',2);
if ($x->is_secure()) { 
	$x->display_top();
	if (empty($_POST)) { ?>
		<b>administrator</b>: start tournament<br />
		<br />
		<?php
		$data = $dbc->database_query('SELECT * FROM tournaments ORDER BY name'); 
		if($dbc->database_num_rows($data)) {
			begitem('start tournament'); ?>
			<table border="0" cellpadding="0" cellspacing="0" width="99%">
			<?php
			$counter = 0;
			while($tournament = $dbc->database_fetch_assoc($data))	{ ?>
				<tr<?php echo ($counter%2==0?" bgcolor=\"".$colors['cell_title']."\"":''); ?>>
				<td valign="top" width="33%">
				<font class="sm"><b>&nbsp;<?php echo ($tournament['random']?'random ':'').$tournament_types[$tournament['ttype']][0]; ?> tournament</b><br /></font>
				&nbsp;<?php echo $tournament['name']; ?><br />
				</td><td valign="top"<?php echo ($tournament['per_team']==1?"width=\"33%\" colspan=3":"width=\"11%\""); ?>>
				<?php
				if ($tournament['per_team'] == 1) {
					$teamcount = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'"));
				} else {
					if ($tournament['random'] && !$tournament['lockstart']) {
						$players = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'"));
						$teamcount = floor($players/$tournament['per_team']);
						if ($players%$tournament['per_team']==0) {
							$freeslots = 0;
						} else {
							$freeslots = $tournament['per_team']-$players%$tournament['per_team'];
						}
						$fullteams = $players;
					} else {
						$teams = $dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'");
						if(!ALP_TOURNAMENT_MODE) {
							$fullteams = 0;
							$freeslots = 0;
							while($g = $dbc->database_fetch_assoc($teams)) {
								$players = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$g['id']."'"));
								if($players==$tournament['per_team']) {
									$fullteams++;
								} else {
									$freeslots += ($tournament['per_team']-$players);
								}
							}
						}
						$teamcount = $dbc->database_num_rows($teams);
					}
				}
				?><font class="sm"><b><a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>"><?php echo ($tournament['per_team']==1?'players':'teams'); ?></a></b><br /></font>
				<?php echo $teamcount; ?>
				</td>
				<?php
				if($tournament["per_team"]>1&&!ALP_TOURNAMENT_MODE) { ?>
					<td valign="top" width="11%">
					<font class="sm"><b><a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>"><?php echo ($tournament['random']&&!$tournament['lockstart']?'players':'full teams'); ?></a></b><br /></font>
					<?php echo $fullteams; ?><br />
					</td><td valign="top" width="11%">
					<font class="sm"><b><a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>">free spots</a></b><br /></font>
					<?php echo $freeslots; ?><br />
					</td>
					<?php
				} else { ?>
					<td colspan="2">&nbsp;</td>
					<?php
				} ?>
				<td valign="<?php echo (!$tournament['lockstart']?'bottom':'middle'); ?>" align=right>
				<?php 
					if(!$tournament['lockstart']) { 
						if($teamcount<$tournament_types[$tournament['ttype']][2]) {
							$temp = $tournament_types[$tournament['ttype']][2]-$teamcount;
							echo "<br /><font color=\"".$colors['primary']."\"><b>need ".$temp." more team".($temp!=1?"s":"").".&nbsp;</b></font><br />"; 
						} else {
							echo '&nbsp;';
						}
					} else { 
						echo '<b>tournament in progress</b>.'; 
					} ?>
				</td>
				</tr>
				<?php
				if($teamcount>=$tournament_types[$tournament['ttype']][2] && !$tournament['lockstart']) { ?>
					<tr<?php echo ($counter%2==0?" bgcolor=\"".$colors['cell_title']."\"":""); ?>><td colspan="4">
					<form action="<?php echo get_script_name(); ?>" method="POST">
					<input type="hidden" name="type" value="2">
					<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
						<table border=0 cellpadding=1 cellspacing=0>
						<tr><td class="sm"><a href="#tip0"><b><?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'); ?></b></a>&nbsp;&nbsp;</td><td><input type="text" name="servers" value="0" maxlength="3" style="width: 155px"></td></tr>
						<?php
						if($tournament["random"]) { ?>
							<tr><td class="sm"><a href="#tip9"><b>random type</b></a></td><td>
							<select name="randomtype" style="font-size: 10px; width: 160px">
								<option value="rank">random by gamer rankings</option>
								<option value="true">completely random</option>
							</select><br />
							</td></tr>
							<?php
						}
						if($tournament['ttype']==1) {
							if($teamcount>=11) { ?>
								<tr><td class="sm"><a href="#tip8"><b>free for all?</b></a>&nbsp;&nbsp;</td>
									<td class="sm"><input type="radio" value="1" name="ffa" class="radio"> yes  <input type="radio" value="0" name="ffa" class="radio" checked> no</td></tr>
								<tr><td class="sm" valign="top">teams per split:</td><td class="sm"><select name="rrsplit" style="font-size: 10px; width: 160px"><?php
								for($i=3;$i<ceil($teamcount/2);$i++) {
									if((($i*ceil($teamcount/$i))-$teamcount)<($i/2)&&ceil($teamcount/$i)!=2) { 
										$temp = log(ceil($teamcount/$i))/log(2); 
										if($temp-round($temp)==0) { ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?> teams and <?php echo ceil($teamcount/$i); ?> groups</option>
											<?php
										}
									}
								} ?></select></td></tr>
								<?php
							} else { ?>
								<tr><td class="sm" colspan=2"><b>need <?php echo (11-$teamcount); ?> more teams for FFA.</b><input type="hidden" name="ffa" value="0"><br /></td></tr>
 								<?php
							}
						} else { ?>
							<input type="hidden" value="0" name="ffa">
							<?php
						}
						//Let's display all team types instead $t = $dbc->database_query("SELECT * FROM tournament_teams_type WHERE gameid='".$tournament['gameid']."'");" .
						$t = $dbc->database_query("SELECT * FROM tournament_teams_type");
						if(!$tournament["ffa"]&&$tournament['ttype']!=12&&$dbc->database_num_rows($t)) { ?>
							<tr><td class="sm"><a href="#tip7"><b>teams</b></a></td><td>
							<select name="teamcolors" style="font-size: 10px; width: 190px"><option value=""></option>
								<?php
								while($trow = $dbc->database_fetch_assoc($t)) { ?>
									<option value="<?php echo $trow['id']; ?>"><?php echo $trow['onename']." vs ".$trow['twoname']; ?></option>
									<?php
								} ?>
							</select><br />
							</td></tr>
							<?php
						} else { ?>
							<input type="hidden" name="teamcolors" value="0"><?php
						} ?>
						</table>
					</td>
					<td align="right" valign="top">
					<input type="submit" value="start tournament" style="width: 160px"></form>
					</td></tr>
					<?php	
				} ?>
				<tr<?php echo ($counter%2==0?" bgcolor=\"".$colors['cell_title']."\"":''); ?>><td colspan=5><img src="img/pxt.gif" width="1" height="12" border="0"><br /></td></tr>
				<?php
				$counter++;
			} ?>
			</table><br />
			<br />
			<b>guide to starting a tournament:</b><br />
			<br />
			<font class="sm">
			<b><u>boiloff tournaments</u></b>: because the number of winners promoted is determined dynamically by the administrator, scores inputted by teams will not
			automatically promote winners to the next round.<br />
			<br />
			<a name="tip0"></a><b><u><?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'); ?></u></b>: the number of <?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations (fields, courts, tables, etc)'); ?> you have available for the tournament.  these will be displayed on the tournament 
			display and will be automatically assigned to matches (with modification available of course).  <b>enter 0 to disable this feature</b>.<br />
			<br />
			<a name="tip1"></a><b><u>random method</u></b>: the random method is the algorithm used to choose the teams for random tournaments.  you can
			choose to do random by rankings, which sorts all the gamers into a list ordered by their random ranking and divides
			up the gamers evenly so to have more "even" random teams.  truely random just picks gamers from a virtual hat and 
			puts them on teams.  this method may put more than one good player on a team and has a higher probability of being 
			unfair.<br />
			<br />
			<!--<a name="tip2"></a><b><u>round to switch</u></b>: combination tournaments switch between tournament types at a certain round number in order
			to save time, weed out the weaker teams and let the serious teams play more games, or for a variety of other reasons.<br />
			<br />-->
			<a name="tip8"></a><b><u>free for all</u></b>: (only available for single elimination) a free for all tournament contains x number of teams (or players) playing 
			against each other at the same time, with the top y teams advancing.  <b>the number of groups must be a power of two to keep things as fair as possible</b>.  this 
			way, there are no byes, there are only some groups with less teams in them to play against each other.<br />
			<br />
			<a name="tip7"></a><b><u>teams</u></b>: starting team can be assigned randomly to assure the most even gameplay.  this option is highly recommended, especially for
			round robin tournaments.  in a bracketed system, often a team will be stuck at the top of the bracket or the bottom of the bracket throughout the tournament, and in
			a round robin system, a team will be on the left or the right a majority of the time.  assigning starting teams randomly is the best way to go.<br />
			<br />
			<!--<a name="tip3"></a><b><u>time limit</u></b>: for ladder tournaments, the time limit is the amount of time the tournament will run before standings
			are final. <b>enter 0 to disable this feature</b>.<br />
			<br />
			<a name="tip4"></a><b><u>play for third</u></b>: in single elimination tournaments, an extra round is required to determine 3rd and 4th place, whether
			that determination is needed for the marathon tournament, or for prizes that you're giving away.  choose yes to play that extra
			round, or no to not.<br />
			<br />
			<a name="tip5"></a><b><u>double final match</u></b>: in double elimination tournaments, the winner of the winners bracket will play the winner of the losers
			bracket in the final round.  if you want the winner of the winners to have to lose twice in order for the winner of the losers to win
			the tournament, turn this on.<br />
			<br />
			<a name="tip6"></a><b><u>teams per split</u></b>: in round robin split tournaments, this is the number of teams per split.<br />
			<br />--></font>
			<?php
			enditem('start tournament');
		} else { ?>
			there are no tournaments in the database to start.<br />
			<br />
			<?php
		}
	} else { 
		require_once 'include/cl_validation.php';
		$valid = new validate();
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value('tourneyid')."'"));
		if(!$tournament['lockstart']) {
			if($valid->get_value('type')==2) {
				if($valid->get_value('servers')=='') $valid->set_value('servers',0);
				if($tournament['random']) $valid->is_empty('randomtype','please specify a method to determine the random teams.');
				if($tournament['ttype']==1||$tournament['ttype']==2||$tournament['ttype']==3) { //||$tournament["ttype"]==4||$tournament["ttype"]==5
					//$valid->is_empty('ffa','please specify the if the tournament is a free for all.');
					if($valid->get_value('ffa')==1) $valid->is_empty('rrsplit','how many teams per match in the ffa?');
				}
				if(!$valid->is_error()) { ?>
					<b>administrator</b>: start tournament<br />
					<br />
					tournament: <b><?php echo $tournament['name']; ?></b><br />
					<br />
					<?php 
					$allgood = true;
					if($valid->get_value('ffa')!=0&&$tournament['ttype']==1) {
						if(!$dbc->database_query("UPDATE tournaments SET ffa='".$valid->get_value('ffa')."', rrsplit='".$valid->get_value('rrsplit')."' WHERE tourneyid='".$tournament['tourneyid']."'")) {
							$allgood = false;
						} else {
							echo 'free for all tournament status and teams per ffa match updated.<br />';
						}
					}
					if($valid->get_value('teamcolors')!=0&&$valid->get_value('ffa')==0) {
						if(!$dbc->database_query("UPDATE tournaments SET teamcolors='".$valid->get_value('teamcolors')."' WHERE tourneyid='".$tournament['tourneyid']."'")) {
							$allgood = false;
						} else {
							echo 'default team assignments updated.<br />';
						}
					}
					if(!$allgood) { 
						echo 'there was an unknown attempting to update your tournament options.<br /><br />'; 
					}
					if($tournament['random']) { 
						begitem('creating tournament teams'); ?>
						<?php
						if($valid->get_value('randomtype')=='true') { ?>
							&nbsp;note: this page can be refreshed to create different teams.<br />
							<br />
							<?php
							$allgood = true;
							$z = date("U");
							$temp = $dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$valid->get_value("tourneyid")."' ORDER BY rand(".mt_rand(0,$z).")");

							$num_players = $dbc->database_num_rows($temp);
							$teams = ceil($num_players/$tournament['per_team']);
							
							$rosters = array();
							for($i=0;$i<$teams;$i++) {
								array_push($rosters,array());
							}
							
							$i = 0;
							while($row = $dbc->database_fetch_assoc($temp)) {
								$z = $i%$teams;
								$rosters[$z][sizeof($rosters[$z])] = $row['userid'];
								$i++;
							}
							if(!$dbc->database_query("DELETE FROM tournament_teams WHERE tourneyid='".$valid->get_value('tourneyid')."'")) {
								$allgood = false;
							}
							for($i=0;$i<sizeof($rosters);$i++) {
								if(!$dbc->database_query("INSERT INTO tournament_teams (tourneyid,name,captainid,sig,sigplace) VALUES ('".$valid->get_value('tourneyid')."','team ".($i+1)."','".$rosters[$i][0]."','','')")) {
									$allgood = false;
								}
								echo "<font class=\"sm\"><b>team ".($i+1)."</b><br /></font>";
								$teamid = $dbc->database_insert_id();
								for($j=0;$j<sizeof($rosters[$i]);$j++) {
									if(!$dbc->database_query("UPDATE tournament_players SET teamid='".$teamid."' WHERE userid='".$rosters[$i][$j]."' AND tourneyid='".$valid->get_value('tourneyid')."'")) {
										$allgood = false;
									}
									$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$rosters[$i][$j]."'"));
									echo '&nbsp;&nbsp;'.$user['username'].'<br />';
								}
								echo '<br />';
							} 
							if($allgood) {
								echo '&nbsp;your random teams were successfully created.<br /><br />';
							} else {
								echo '&nbsp;there was an error creating the random teams.  please refresh the page and try again.<br /><br />';
							}
						} elseif($valid->get_value('randomtype')=='rank') {
							$allgood = true;
							$dbc->database_query("DELETE FROM tournament_teams WHERE tourneyid='".$valid->get_value('tourneyid')."'");
							$profs = array();
							for($i=0;$i<=10;$i++) {
								array_push($profs,array());
							}
							$result = $dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$valid->get_value('tourneyid')."'");
							while($row = $dbc->database_fetch_assoc($result)) {
								$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$row['userid']."'"));
								$profs[$temp['proficiency']][] = $row['userid'];
							}
							for($i=0;$i<=10;$i++) {
								sort($profs[$i]);
							}
							
							$num_users = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$valid->get_value("tourneyid")."'"));
							
							$teams = ceil($num_users/$tournament['per_team']);
							$counter = 0;
							$up = true;
							$teamcounter++;
							$base = 0;
							for($i=10;$i>=0;$i--) {
								if(sizeof($profs[$i])>0) {
									for($j=0;$j<sizeof($profs[$i]);$j++) {
										if($counter<$teams) {
											if(!$dbc->database_query("INSERT INTO tournament_teams (tourneyid,name,captainid) VALUES ('".$valid->get_value('tourneyid')."','team ".$teamcounter."','".$profs[$i][$j]."')")) {
												$allgood = false;
											}
											$teamcounter++;
											if($counter==0) $base = $dbc->database_insert_id();
										}
										$z = $counter%$teams;
										if($up) {
											if(!$dbc->database_query("UPDATE tournament_players SET teamid='".($z+$base)."' WHERE tourneyid='".$valid->get_value('tourneyid')."' AND userid='".$profs[$i][$j]."';")) {
												$allgood = false;
											}
										} else {
											if(!$dbc->database_query("UPDATE tournament_players SET teamid='".($teams-$z-1+$base)."' WHERE tourneyid='".$valid->get_value('tourneyid')."' AND userid='".$profs[$i][$j]."';")) {
												$allgood = false;
											}
										}
										if($z==($teams-1)) $up = !$up;
										$counter++;
									}
								}
							}
							if($allgood) {
								echo "&nbsp;your teams were successfully created based on proficiency ratings.<br /><br />";
							} else {
								echo "&nbsp;there was an error creating the teams based on proficiency ratings.  please refresh the page and try again.<br /><br />";
							}
						} ?>
						<?php
						enditem('creating tournament teams');
					} ?>
					<form action="<?php echo get_script_name(); ?>" method="POST">
					<input type="hidden" name="type" value="3">
					<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
					<input type="hidden" name="servers" value="<?php echo $valid->get_value('servers'); ?>">
					<?php
					if($valid->get_value('ffa')!=0&&$tournament['ttype']==1&&$valid->get_value('rrsplit')!='') {
						if($tournament['per_team']==1) $query = "SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'";
						else $query = "SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'";
						$number_of_groups = ceil($dbc->database_num_rows($dbc->database_query($query))/$valid->get_value('rrsplit'));
						$number_of_rounds = ceil(log($number_of_groups)/log(2));
						if($number_of_rounds>=1) { ?>
							<br />
							<?php begitem('number of teams to advance per round'); ?>
							<font class="sm"><b>tip!</b> the number of teams advancing in the successive round can be no more than 2 x the number of teams advanced in the previous round.  for example: if you have 2 teams advancing from the first round,
							that means 4 teams will play per match in the second round, and no more than the top 3 teams can advance to the next round.  THINK
							ABOUT THIS DECISION BEFORE YOU CLICK TO THE NEXT SCREEN -- THESE OPTIONS CANNOT BE MODIFIED.<br />
							<br />
							<?php if(($tournament['ttype']==1||$tournament['ttype']==6)&&$valid->get_value('playforthird')) { ?>
							if 2 teams advance from each match in the semifinals, an extra match to determine third place is not required, as long as you input all the scores for the final match into the brackets.<br />
							<?php } ?></font>
							<table border="0" cellpadding="0" cellspacing="0"><tr class="sm">
							<input type="hidden" name="number_of_rounds" value="<?php echo $number_of_rounds; ?>">
							<?php
							for($i=1;$i<=$number_of_rounds;$i++) { ?>
								<td width="80">
								<b>round <?php echo $i; ?></b> ->&nbsp;<br />
								<br />
								matches: <?php echo ($number_of_groups/$i); ?><br />
								<br />
								<select name="round_<?php echo $i; ?>_advance" style="font-size: 10px; width: 80px">
								<?php 
								if($i==1) $temp = $valid->get_value('rrsplit');
								else $temp = 2*($valid->get_value('rrsplit')-1);
								for($j=1;$j<$temp;$j++) { ?>
									<option value="<?php echo $j; ?>"><?php echo $j; ?></option>
									<?php
								} ?>
								</select><br />
								<?php
								if($i==1) { ?>
									of <?php echo ceil($dbc->database_num_rows($dbc->database_query($query))/$number_of_groups); ?> teams advance.<br />
									<?php
								} else { ?>
									of 2 x (from round <?php echo ($i-1); ?>)<br />
									<?php
								} ?>
								</td>
								<?php
							} ?>
							<td valign="top" width="80"><nobr>&nbsp;final match ->&nbsp;</td>
							<td valign="top" width="80"><nobr>&nbsp;<b>winner</b></td>
							</tr></table>
							<br />
							<?php
							enditem('number of teams to advance per round');
						}
					}

					if($valid->get_value('servers')>0) { ?>
						<?php 
						begitem('tournament '.(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations')); ?>
						<table border="0" cellpadding="2" cellspacing="2" width="400" class="centerd"><tr><td>
						<tr><td colspan="4"><font class="sm">list your <?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'); ?> in order from best to worst (the best <?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'); ?> will be used more frequently).</font></td></tr>
						<tr>
							<td valign="bottom"><b><u>#</u></b></td>
							<td valign="bottom"><b><u><?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'server':'location'); ?> name</u></b></td>
							<?php
							if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
								<td valign="bottom"><b><u>ip address : port</u></b><br /><font class="sm">this is optional, however, if formatted correctly, this will display a hlsw connect link to the server.</font></td>
								<td valign="bottom"><b><u>query port</u></b><br /><font class="sm">this is also optional, but if you want the server query features AND your game server is running on a non-standard game port, you'll need it.</font></td></tr>
								<?php
							}
							for($i=0;$i<$valid->get_value('servers');$i++) { ?>
								<tr>
									<td><?php echo ($i+1); ?></td>
									<td width="100%"><input type="text" name="server_<?php echo $i; ?>" style="width: 97%" maxlength="100" value="<?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'Server':'Field'); ?> <?php echo ($i+1); ?>"></td>
									<?php
									if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
										<td width="120" align="left"><input type="text" name="server_<?php echo $i; ?>_ip" style="width: 120px" maxlength="255"></td>
										<td width="120" align="left"><input type="text" name="server_<?php echo $i; ?>_queryport" style="width: 120px" maxlength="255"></td>
										<?php
									} ?>
								</tr>
								<?php
							} ?>
							
						</table>
						<br />
						<?php 
						enditem('tournament '.(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'));
					} 
					if(!$tournament['ffa']&&$valid->get_value('servers')==0&&!$tournament['random']) { ?>
						your input did <b>not</b> suck, but this page appears useless because you have no <?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'play locations'); ?> to input, no free for all options to configure, and
						you're not doing random teams.  so just click the button to move on.<br /><br />
						<?php 
					} ?>
					<div align="right"><input type="submit" value="start tournament" style="width: 160px"></div>
					</form>
					<?php
				} else {
					$valid->display_errors();
				}
			} elseif($valid->get_value('type')==3) {
				$valid->is_empty('tourneyid','please specify a tournament to start.');
				if(!$valid->is_error()) { 
					$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value("tourneyid")."'")); ?>
					<b>administrator</b>: start tournament<br />
					<br />
					<?php
					if($valid->get_value('servers')>0) {
						$allgood = true;
						if(!$dbc->database_query("DELETE FROM servers WHERE tourneyid='".$tournament['tourneyid']."'")) {
							$allgood = false;
						}
						for($i=0;$i<$valid->get_value("servers");$i++) {
							if(!$dbc->database_query("INSERT INTO servers (tourneyid,name".(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?',ipaddress,queryport':'').") VALUES ('".$tournament['tourneyid']."', '".($valid->get_value("server_".$i)==''?(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'Server':'Location').' '.($i+1):$valid->get_value("server_".$i))."'".(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?", '".$valid->get_value("server_".$i."_ip")."', '".$valid->get_value("server_".$i."_queryport")."'":'').")")) {
								$allgood = false;
							}
						}
						if($allgood) {
							echo "tournament ".(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations')." list successfully created.<br /><br />";
						} else {
							echo "there was an error while attempting to insert tournament ".(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations')." into the database.  please try again.<br /><br />";
						}
					}

					$allgood = true;
					if(!$dbc->database_query("UPDATE tournaments SET lockstart='1' WHERE tourneyid='".$tournament['tourneyid']."'")&&!$dbc->database_query("DELETE from tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."'")&&!$dbc->database_query("DELETE FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."'")) {
						$allgood = false;
					}
					
					include "include/tournaments/start_".$tournament["ttype"].".php";
					
                    if($tournament['per_team']==1) $query = "SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'";
                    else $query = "SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'";
                    $number_of_groups = ceil($dbc->database_num_rows($dbc->database_query($query)) / ($tournament["rrsplit"]==0?2:$tournament["rrsplit"]));
                    $number_of_rounds = ceil(log($number_of_groups)/log(2));

					$allgood = true;
					if($tournament["ffa"]&&$tournament["rrsplit"]>0) {
						$previous_advance = 0;
						for($i=1;$i<=$number_of_rounds;$i++) {
							$previous_advance = $valid->get_value("round_".$i."_advance");
							if($i>1&&$valid->get_value("round_".$i."_advance")>(2*$previous_advance)) {
								$temp = 1;
							} else {
								$temp = $valid->get_value("round_".$i."_advance");
							}
							if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='".$temp."' WHERE rnd='".$i."' AND tourneyid='".$tournament["tourneyid"]."'")) {
								$allgood = false;
							}
						}
						$row = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(rnd) as rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."'"));
						if($number_of_rounds<$row["rnd"]) {
							for($i=($number_of_rounds+1);$i<$row["rnd"];$i++) {
								if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='1' WHERE rnd='".$i."' AND tourneyid='".$tournament["tourneyid"]."'")) {
									$allgood = false;
								}
							}
						}
						if($allgood) {
							echo "team advancement information successfully entered.<br /><br />";
						} else {
							echo "there was an error while attempting to input team advancing information into the database.  please try again.<br /><br />";
						}
					}
					echo "tournament successfully started.  you may view your tournament at the <a href=\"disp_tournament.php?id=".$tournament["tourneyid"]."\">tournament display</a> page.<br /><br />";
				} else {
					$valid->display_errors();	
				}
			}
		} else {
			echo "you are not authorized to view this page after a tournament has already started.<br /><br />";
		}
	}
	$x->display_bottom();
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>
