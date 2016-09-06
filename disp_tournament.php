<?php
require_once 'include/_universal.php';
require_once 'include/tournaments/_tournament_functions.php';
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) require_once 'include/gamelauncher/hlsw_supported_games.php';
$x = new universal('tournament standings','',0);
if ($x->is_secure() && empty($_GET['recache'])) { 
	$x->display_top(0,0);
	if (!empty($_GET) && !empty($_GET['id'])) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['id']."'"));
		$gameinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT short FROM games WHERE gameid='".$tournament['gameid']."'")); ?>
		<table border="0" cellpadding="8" width="100%"><tr><td>
		<div align="right"><form name="menu"><?php
		if ($tournament['lockstart']) { 
			?><script language="JavaScript">
			<!-- 
			function goTo() {
				if(document.menu.go.value!="") document.location.href = document.menu.go.value;
			} 
			function newWindow(url,width,height,name) {
				window.open(url,name,"width="+width+", height="+height+",resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,copyhistory=no,screenX=50,screenY=150,left=50,top=150");
			} // -->
			</script><font class="sm"><strong>display standings</strong>: </font><select name="go" style="width: 250px; font: 10px Verdana" onChange="goTo()"><?php
			$data = $dbc->query('SELECT tourneyid,name FROM tournaments WHERE lockstart=1 ORDER BY name');
			while($row = $data->fetchRow()) { 
				$link = make_tournament_link($row['tourneyid']); ?>
				<option value="<?php echo $link; ?>"<?php echo (!empty($_GET['id']) && $row['tourneyid'] == $_GET['id']?' selected':''); ?>><?php echo $row['name']; ?></option>
				<?php
			} ?></select>&nbsp;&nbsp;&nbsp;<?php
		}
		if (current_security_level() >= 2 && $tournament['lockstart'] && $master['caching']) { 
			?>[<a href="disp_tournament.php?recache=1&id=<?php echo $tournament['tourneyid']; ?>"><strong>recache this page</strong></a>]&nbsp;&nbsp;&nbsp;<?php
		} elseif (current_security_level() == 0 && !empty($_GET['bot']) && $_GET['bot'] == 1 && $tournament['lockstart'] && !ALP_TOURNAMENT_MODE) { 
			?>[<a href="disp_tournament.php?id=<?php echo $tournament['tourneyid']; ?>"><strong>trying to submit your score?</strong></a>]&nbsp;&nbsp;&nbsp;<?php
		} ?>[<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><strong>back to tournament information</strong></a>]</form></div>
		<a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><font class="tourneytitle"><?php echo $tournament['name']; ?></font></a><br />
		<font class="sm"><strong><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?> tournament</strong><br /></font>
		<br />
		<?php
		display_tournament_menu($tournament['tourneyid'],1,1);
		if($tournament["lockstart"]) { ?>
			<table border=0 cellpadding=0 cellspacing=0 class="sm">
			<?php
			$servers = $dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY id");
			$servers_arr = array();
			if($dbc->database_num_rows($servers)!=0) {
				$counter = 0; 
				while($row = $dbc->database_fetch_assoc($servers)) {  ?>
					<tr>
					<?php if($counter==0) { $baseid = $row['id']; ?><td rowspan=<?php echo $dbc->database_num_rows($servers); ?> valign=top><font color="<?php echo $colors['primary']; ?>">&lt;<strong><?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations'); ?></strong>&gt;&nbsp;&nbsp;</font><br /></td><?php } ?>
					<td>&nbsp;&nbsp;&nbsp;<?php echo ($row['id']-$baseid+1); ?> //</td>
					<td>&nbsp;&nbsp;<strong><?php echo $row['name']; ?></strong></td>
					<td>&nbsp;<?php 
					if(!empty($row['ipaddress'])) { ?>
						&nbsp;&nbsp;<?php if(!empty($hlsw_supported_games[$gameinfo['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle" /></a><?php } ?>&nbsp;&nbsp;<?php if(!empty($hlsw_supported_games[$gameinfo['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><?php } ?><?php echo $row['ipaddress']; ?><?php if(!empty($hlsw_supported_games[$gameinfo['short']])&&$toggle['hlsw']) { ?></a><?php }
					} ?></td>
					</tr>
					<?php
					$servers_arr[$row['id']-$baseid+1] = array($row['name'],$row['ipaddress']);
					$counter++;
				} 
				if (current_security_level() >= 2) { ?>
					<tr><td>&nbsp;</td><td colspan="3">&nbsp;&nbsp;&nbsp;<a href="admin_servers.php?id=<?php echo $tournament["tourneyid"]; ?>"><strong>edit these <?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'servers':'locations'); ?></strong></a></td></tr>
					<?php
				} ?>
				<tr><td colspan="4"><br /></td></tr>
				<?php
			} 
			if (!empty($tournament['teamcolors'])) { 
				$teams = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament['teamcolors']."'")); ?>
				<tr><td rowspan="2" valign="top"><font color="<?php echo $colors['primary']; ?>">&lt;<strong>teams</strong>&gt;&nbsp;&nbsp;</font><br /></td>
                                            <td bgcolor="<?php echo $teams['onecolor']; ?>"><img src="img/pxt.gif" width="10" height="10" border="0" alt="" /></td><td colspan=2>&nbsp;&nbsp;1 - start as <strong><?php echo $teams['onename']; ?></strong></td></tr>
				<tr><td bgcolor="<?php echo $teams['twocolor']; ?>"><img src="img/pxt.gif" width="10" height="10" border="0" alt="" /></td><td colspan=2>&nbsp;&nbsp;2 - start as <strong><?php echo $teams['twoname']; ?></strong></td></tr>
				<?php
			} ?>
			</table>
			<?php
			if (tournament_is_secure($tournament['tourneyid'])) { ?>
				<font class="sm"><br />
				<strong>note</strong>: press the <font style="font-size: 7px; color: <?php echo $colors['alert']; ?>;"><u><strong>X</strong></u></font> button to <?php if($tournament['ttype'] == 10) { ?>erase all scores in that match.<?php } else { ?>delete a team from a match.<?php } ?><br />
				<?php if($tournament['ttype']!=12&&!ALP_TOURNAMENT_MODE) { ?><strong>note</strong>: if there is a discrepancy between user submitted scores, they will not advance to the next round and the score button will look like <font class="smm">&lt;<img src="img/scores.gif" width="10" height="8" border="0" alt="submitted scores" />&gt;</font> instead of <img src="img/scores.gif" width="10" height="8" border="0" alt="submitted scores" />.<br /><?php } ?>
				<?php if($tournament['ffa']) { ?><strong>note</strong>: in a free for all tournament, scores are required in the last match to determine 2nd, 3rd, and 4th place.<br /><?php } ?>
				</font>
				<?php
			} ?>
			</td></tr></table>
			<?php
			function allscores($teamid) {
				global $tournament, $dbc;
				$totalscore = 0;
				$data = $dbc->database_query("SELECT score FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND team='".$teamid."'");
				while($row = $dbc->database_fetch_assoc($data)) {
					$totalscore += $row['score'];
				}
				return $totalscore;
			}
			require 'include/tournaments/scoring_'.$tournament['ttype'].'.php';
			require 'include/tournaments/scoring_display_'.$tournament['ttype'].'.php';
			require 'include/tournaments/display_'.$tournament['ttype'].'.php';
		} else {
			echo 'this page was not meant to be viewed until the tournament has started.<br /><br />';
		}
	} else { 
		select_tournament(1);
	}
	$x->display_bottom();
} elseif ($master['caching'] && !empty($_GET['recache']) && $_GET['recache'] && !empty($_GET['id']) && $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['id']."'")) && current_security_level() >= 2) {
	// code to cache the page
	$x->display_top(); ?>
	attempting to recache your tournament page...<br />
	<br />
	<?php
	$handle = fsockopen('localhost', 80, $errno, $errstr, 5);
	$destination = fopen('_tournament_'.(int)$_GET['id'].'.html','w');
	socket_set_blocking($handle,true);
	socket_set_timeout($handle, 2);
	if($handle) {
		fputs ($handle, 'GET /disp_tournament.php?id='.(int)$_GET['id']."&bot=1;?HTTP/1.0\r\nHost:localhost\r\n\r\n");

		while(!feof($handle)) {
	    	$buffer = fgets($handle, 4096);
			fwrite($destination,$buffer);
		}
	}
	fclose($destination);
	fclose($handle); ?>
	<strong>recache successful</strong>: <a href="disp_tournament.php?id=<?php echo (int)$_GET['id']; ?>">return to tournament</a><br />
	<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>