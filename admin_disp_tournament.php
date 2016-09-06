<?php
// TODO: Change this page to use SQuery or maybe even remove this page, what is it good for?
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
require_once 'include/_squery.php';
$x = new universal('tournament administration','',1);
if ($x->is_secure())
{
	$x->display_top(0,0);
	if (!empty($_GET) && !empty($_GET['id'])) {
		$tournament = $dbc->queryRow("SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['id']."'"); ?>
		<table border="0" cellpadding="8" width="100%"><tr><td>
		<div align="right"><form name="menu"><?php
		if ($tournament['lockstart']) {
			?><script language="JavaScript">
			<!--
			function goTo() {
				if(document.menu.go.value!="") document.location.href = document.menu.go.value;
			}
			function goTo2() {
				document.location.href = document.menu.go2.value;
			}
			// -->
			</script><font class="sm"><strong>display admin</strong>: </font><select name="go" style="font: 10px Verdana" onChange="goTo()"><?php
			$data = $dbc->query('SELECT tourneyid,name FROM tournaments WHERE lockstart=1 ORDER BY name');
			while($row = $data->fetchRow()) {
				$link = 'admin_disp_tournament.php?id='.$row['tourneyid'];
				?>
				<option value="<?php echo $link; ?>"<?php echo (!empty($_GET['id']) && $row['tourneyid'] == $_GET['id']?' selected':''); ?>><?php echo $row['name']; ?></option>
				<?php
			} ?></select>&nbsp;&nbsp;&nbsp;
			<font class="sm"><strong>display standings</strong>: </font><select name="go2" style="font: 10px Verdana" onChange="goTo2()"><option value="" selected></option><?php
			$data = $dbc->query('SELECT tourneyid,name FROM tournaments WHERE lockstart=1 ORDER BY name');
			while($row = $data->fetchRow()) {
				$link = make_tournament_link($row['tourneyid']); ?>
				<option value="<?php echo $link; ?>"><?php echo $row['name']; ?></option>
				<?php
			} ?></select>&nbsp;&nbsp;&nbsp;<?php
		}
		?>[<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><strong>back to tournament information</strong></a>]</form></div>
		<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><font class="tourneytitle"><?php echo $tournament['name']; ?></font></a><br />
		<font class="sm"><strong><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</strong><br /></font>
		<br />
		<?php
		if($tournament["lockstart"]) {
			function is_active_round($rnd, $tourneytype) {
				global $max_W_rnd;
				if($tourneytype==5 && isset($_GET['rnd']) && $_GET['rnd']==0 && $rnd==$max_W_rnd) {
					return true;	
				} elseif(isset($_GET['rnd']) && $_GET['rnd']==$rnd) {
					return true;
				} else {
					return false;	
				}
				return false;
			}
			display_tournament_menu($tournament['tourneyid']);
			?>
			<a href="admin_servers.php?id=<?php echo $tournament['tourneyid']; ?>">edit servers</a><br />
			<br />
			<?php
			$max_W_rnd = $dbc->queryOne("SELECT rnd FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'".($tournament['ttype']==1||$tournament['ttype']==4||$tournament['ttype']==5?" AND bracket='w'":'')." ORDER BY rnd DESC LIMIT 1");
			if($tournament['ttype']==5) $max_W_rnd++;
			$data = $dbc->query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'".($tournament['ttype']==1||$tournament['ttype']==4||$tournament['ttype']==5?" AND rnd!=0 AND rnd<".($tournament['ttype']==5||$tournament['ttype']==4?'=':'').$max_W_rnd:"")." GROUP BY rnd ORDER BY rnd");
			echo '<b>Active Round</b>: ';
			$counter = 0;
			while($row = $data->fetchRow()) {
				?><a href="<?php echo get_script_name().'?id='.$tournament['tourneyid'].'&rnd='.$row['rnd']; ?>"><?php
				echo (is_active_round($row['rnd'],$tournament['ttype'])?'<font style="font-size: 20px; color: '.$colors['primary'].'"><b>':'').$row['rnd'].(is_active_round($row['rnd'],$tournament['ttype'])?'</b></font>':'').'&nbsp;&nbsp;';
				$counter = $row['rnd'];
				?></a><?php
			}
			if($tournament['ttype']==5) {
				?><a href="<?php echo get_script_name().'?id='.$tournament['tourneyid'].'&rnd=0'; ?>"><?php
				echo (is_active_round($counter+1,$tournament['ttype'])?'<font style="font-size: 20px; color: '.$colors['primary'].'"><b>':'').($counter+1).(is_active_round($counter+1,$tournament['ttype'])?'</b></font>':'').'<br />';
				?></a><?php
			} 
			function disp_admin_round($rnd,$matchids)
			{
				global $tournament, $toggle, $colors, $max_W_rnd, $dbc, $hlsw_supported_games;
				$gameinfo = $dbc->queryOne("SELECT short FROM games WHERE gameid='".$tournament['gameid']."'");
				$gamemaps = $dbc->queryOne("SELECT thumbs_dir FROM games WHERE gameid='".$tournament['gameid']."'");
				$map = $dbc->queryOne("SELECT map FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".($rnd==$max_W_rnd?'0':$rnd)."'".($tournament['ttype']==1||$tournament['ttype']==4||$tournament['ttype']==5?" ORDER BY bracket":"")." LIMIT 1");
				if (file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.jpg')
							|| file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.gif')
							|| file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.png')) { ?>
					</tr><tr><td colspan="3"><?php
							$mapdir = 'img/map_thumbnails/'.$gamemaps.'/'.$map.(file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.jpg')?'.jpg':(file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.gif')?'.gif':(file_exists('img/map_thumbnails/'.$gamemaps.'/'.$map.'.png')?'.png':''))); 
							$mapdir_dimensions = getimagesize($mapdir); 
							?>
						<table border="0" cellpadding=0 cellspacing=1 bgcolor="<?php echo $colors['cell_title']; ?>" class="centerd"><tr><td id="" bgcolor="<?php echo $colors['cell_background']; ?>"><img src="<?php echo $mapdir; ?>" width="218" height="163" border="0" alt="<?php echo $map; ?>" /></td></tr></table>
					</td></tr><tr>
					<?php
				}
				?><td valign="top" width="50"><div align="center"><font class="sm">round</font><br />
					<font<?php echo (is_active_round($rnd,$tournament['ttype'])?" color=\"".$colors['primary']."\"":''); ?> style="font-size: 40px; font-weight: bolder"><?php echo $rnd; ?></font><br />
					<font class="sm"><?php echo $map; ?></font></div></td>
				<td valign="top">
				<table border="0" class="sm">
				<tr>
					<td style="border: 1px solid <?php echo $colors['border']; ?>"><b>match</b><br /><?php spacer(60); ?></td>
					<td style="border: 1px solid <?php echo $colors['border']; ?>"><b>bracket</b><br /><?php spacer(60); ?></td>
					<td style="border: 1px solid <?php echo $colors['border']; ?>"><b>server</b> <a href="admin_servers.php?id=<?php echo $tournament['tourneyid']; ?>" class="menu">[admin]</a><br /><?php spacer(180); ?></td>
				</tr><?php
				foreach($matchids as $val) 
				{
					if( $tournament['ttype']==5 && $rnd==$max_W_rnd) {
						$W_matchid = $dbc->queryOne("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".($max_W_rnd-1)."' AND bracket='w'");
						$L_matchid = $dbc->queryOne("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".($max_W_rnd-1)."' AND bracket='l'");
						$data = $dbc->query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND (matchid='".$W_matchid."' OR matchid='".$L_matchid."') AND team!=0");
					} else {
						$data = $dbc->query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$val."' AND team!=0");
					}
					$rnd_info = $dbc->queryRow("SELECT id,rnd,mtc,bracket,server,map FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$val."'");
					$server_ip = $dbc->queryOne("SELECT ipaddress FROM servers WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY id LIMIT ".($rnd_info['server']-1).",1");
					$server_queryport = $dbc->queryOne("SELECT queryport FROM servers WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY id LIMIT ".($rnd_info['server']-1).",1");
					$server_querystr2 = $dbc->queryOne("SELECT querystr2 FROM games WHERE gameid='".$tournament['gameid']."'");
					$numteams = $data->numRows();
					if($server_queryport == "") {
						$server_ip = explode(":",$server_ip);
						$queryport = $ipaddress['1'];
						$server_ip = $ipaddress['0'];
					} else {
						$queryport = $server_queryport;
					}
					if($numteams>1) {
						?>
						<tr bgcolor="<?php echo $colors['secondary']; ?>">
							<td><div align="center"><?php echo ($tournament['ttype']==5&&$rnd_info['rnd']==0?$max_W_rnd:($rnd_info['bracket']=='l'?($rnd_info['rnd']+1):$rnd_info['rnd'])).' - '.($tournament['ttype']==5&&$rnd_info['rnd']==0?'1':$rnd_info['mtc']); ?></div></td>
							<td><div align="center"><?php echo (!empty($rnd_info['bracket'])?strtoupper($rnd_info['bracket']):''); ?></div></td>
							<td><div align="center">#<?php echo $rnd_info['server']; ?> - <?php
							if(!empty($hlsw_supported_games[$gameinfo])&&$toggle['hlsw']) {
								?><a href="hlsw://<?php echo $server_ip; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle"><span style="text-decoration: none">&nbsp;</span><?php
							}
							echo $server_ip;
							if(!empty($hlsw_supported_games[$gameinfo])&&$toggle['hlsw']) {
								?></a><?php
							} ?></div></td>
						</tr>
						<?php
						if($rnd_info['bracket']=='l') {
							$round = $rnd_info['rnd']+1;
						} else {
							$round = $rnd_info['rnd'];	
						}
						if(is_active_round($rnd,$tournament['ttype'])) {
							$gameserver = queryServer($ipaddress,$queryport,$querystr2);
							/* ?>
							<pre><?php print_r($gameserver); ?></pre>
							<?php */
							//$hostname = $gameserver->htmlize($gameserver->servertitle);
							$hostname = $gameserver->servertitle;
							$game_ver = $gameserver->rules["gameversion"];
							$map = $gameserver->mapname;
							$num_players = $gameserver->numplayers;
							$max_players = $gameserver->maxplayers;?>
							<tr><td colspan='3'><font class='sm' color='<?php echo $colors['cell_title']; ?>'>
								<?php 
								if(sizeof($info)==1) {
									echo 'query unsuccessful. server may be down.';	
								}
								if(!empty($hostname)) { 
									echo $hostname.'<br />';
								}
								if(!empty($map) || !empty($num_players)&&!empty($max_players)) { 
										if(!empty($map)) { 
											echo $map; 
										} 
										if(!empty($num_players)&&!empty($max_players)) { 
											if(!empty($map)) { 
												?> with <?php 
											} 
											echo $num_players.' / '.$max_players; ?> players<?php 
										}
										echo '<br />';
								} ?>
								<?php
								$s_players = get_players($tournament['gameid'],$server_ip,$server_queryport);
								$s_players_accounted = array();
								?>
							</td></tr>
							<?php
							while($row = $data->fetchRow()) {
								$top = $row['top'];
								if($tournament['per_team']==1) {
									$teams = $dbc->query("SELECT username AS name FROM tournament_players LEFT JOIN users USING (userid) WHERE tourneyid='".$tournament['tourneyid']."' AND users.userid='".$row['team']."'");
								} else {
									$teams = $dbc->query("SELECT sig, name FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND id='".$row['team']."'");
								}
								if($teams->numRows()) {
									if(!empty($tournament["teamcolors"]))  {
										$colortemp = $dbc->queryRow("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'");
										$random = $dbc->queryOne("SELECT RAND(".$rnd_info['id'].") as random");
										$random = round($random);
										if(($random&&$top)||(!$random&&!$top)) {
											$startteam = $colortemp['onename'];
										} elseif(($random&&!$top)||(!$random&&$top)) {
											$startteam = $colortemp['twoname'];
										}
									} else {
										$startteam = '';
									}
									?><tr><td colspan="3">
									<table border="0" class="sm" width="100%"><?php
									while($teams_row = $teams->fetchRow()) {
										if($numteams>1) {
											// TODO: Figure out what the hell to do with player search...re-write function?
											if(!empty($startteam)) {
												echo '<tr><td colspan=\'3\' style=\'color: '.$colors['secondary'].'\'><div align=\'center\'><u>'.$teams_row['name'].' starts as '.$startteam.'</u></div></td></tr>';
											}
											if($tournament['per_team']>1) {
												$players = $dbc->query("SELECT username AS name FROM tournament_players	LEFT JOIN users USING (userid) WHERE tourneyid='".$tournament['tourneyid']."' AND teamid='".$row['team']."'");
												if($players->numRows()) {
													while($players_row = $players->fetchRow()) {
														$playersearch = search_for_player($players_row['name'],$tournament['gameid'],$server_ip,$server_queryport);
														if($playersearch!==false) {
															$s_players_accounted[$playersearch] = 1;	
														}
														echo '<tr><td width=\'100\'><nobr><b>'.$teams_row['name'].'</b></td><td width=\'60\'><nobr>'.$teams_row['sig'].'</td><td width=\'100%\'>'.$players_row['name'].($playersearch!==false?'<font color=\''.$colors['blended_text'].'\'> AS '.$s_players[$playersearch]['name'].'</font>':'').'</td><td width=\'30\'><nobr><B>'.($playersearch!==false?'IN':'OUT').'</B></td></tr>';
													}
												}
											} else {
												$playersearch = search_for_player($teams_row['name'],$tournament['gameid'],$server_ip,$server_queryport);
												if($playersearch!==false) {
													$s_players_accounted[$playersearch] = 1;	
												}
												echo '<tr><td colspan=\'3\' width=\'100%\'>'.$teams_row['name'].'</td><td width=\'30\'><nobr><B>'.($playersearch!==false?'IN':'OUT').'</B></td></tr>';
											}
										}
									}
									?></table></td></tr><?php
								}	
							} ?>
							<tr><td colspan='3'><table border='0' width='100%' class='sm'>
								<tr style="color: <?php echo $colors['secondary']; ?>"><td width='50%' valign='top'><B>unaccounted for</B></td><td width='50%'>
										<?php 
										if(sizeof($s_players) > 0) {
											foreach($s_players as $key => $val) {
												if(!isset($s_players_accounted[$key])) echo $val['name'].'<br />';
											}
											reset($s_players);
										}
										?>
								</td></tr></table>
							</td></tr>
							<?php
						}
					}
				}
				?></table>
				</td><?php
				// end disp_admin_round()
			}
			?>
			<table border="0" cellpadding="4" cellspacing="0" class="centerd" width="400">
			<?php
			$data = $dbc->query("SELECT rnd FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd!=0 GROUP BY rnd ORDER BY rnd");
			$counter = 0;
			while($row = $data->fetchRow())
			{
				if(is_active_round($row['rnd'],$tournament['ttype'])) {
					$matchids_array = array();
					$w_data = $dbc->query("SELECT id,rnd FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".$row['rnd']."'".($tournament['ttype']==1||$tournament['ttype']==4||$tournament['ttype']==5?" AND bracket='w'":"")." ORDER BY mtc");
					while($w_row = $w_data->fetchRow())
					{
						if($tournament['ttype']==4 && $w_row['rnd']==$max_W_rnd) {
						} else {
							$matchids_array[] = $w_row['id'];
						}
					}
					if(($tournament['ttype']==4||$tournament['ttype']==5)&&$row['rnd']>1)
					{
						$l_data = $dbc->query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".($row['rnd']-1)."' AND bracket='l' ORDER BY mtc");
						while($l_row = $l_data->fetchRow())
						{
							$matchids_array[] = $l_row['id'];
						}
					}
					?><tr><?php
					disp_admin_round($row['rnd'],$matchids_array);
					?></tr><?php
				}
				$counter = $row['rnd'];
			}
			if($tournament['ttype']==5 && is_active_round($counter+1,$tournament['ttype']))
			{
				$hack_matchid = $dbc->queryOne("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd=0 AND mtc=0");
				?><tr><?php
				disp_admin_round($counter+1,array($hack_matchid)); 
				?></tr><?php
			} ?>
			</table>
			<?php
		} else {
			echo 'this page was not meant to be viewed until the tournament has started.<br /><br />';
		}
	} else {
		select_tournament(1);
	}
	$x->display_bottom(0,0);
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>