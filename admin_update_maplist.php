<?php
// this file is not needed if !ALP_TOURNAMENT_MODE_COMPUTER_GAMES
// this file is only used for round robin tournaments.
require_once 'include/_universal.php'; 
$x = new universal("map list","",1);
$x->display_smallwindow_top();
if($x->is_secure()) { ?>
	<table border="0" cellpadding="4" width="100%"><tr><td>
	<?php
	if ((empty($_GET) || empty($_GET['id'])) && empty($_POST)) { ?>
		<script language="JavaScript" type="text/javascript">
		<!--
		window.close();
		// -->
		</script>
		<?php
	} elseif (empty($_POST)) { 
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id'])); 
		if (current_security_level() >= 2 || (current_security_level() >= 1 && $tournament['moderatorid'] == $_COOKIE['userid'])) { ?>
			<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br />
			<form action="admin_update_maplist.php" method="POST">
			<input type="hidden" name="id" value="<?php echo $tournament["tourneyid"]; ?>">
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
			<?php
			$data = $dbc->database_query("SELECT DISTINCT rnd,map FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY rnd");
			while($row = $dbc->database_fetch_assoc($data)) {  ?>
				<tr>
					<td width="16"><font class="sm"><b><?php echo $row["rnd"]; ?></b><br /></font></td>
					<td><input type="text" name="<?php echo $row["rnd"]; ?>_map" maxlength="20" style="font-size: 9px; width: 95%" value="<?php echo (!empty($row["map"])?$row["map"]:""); ?>"></td>
				</tr>
				<?php
			} ?>
			</table>
			<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br /><input type="submit" value="update" style="font-size: 9px; width: 99%">
			</form>
			<?php
		} else { ?>
			<script language="JavaScript" type="text/javascript">
			<!--
			window.close();
			// -->
			</script>
			<?php
		}
	} elseif(!empty($_POST)) { 
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_POST["id"]."'")); 
		if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) {
			$data = $dbc->database_query("SELECT DISTINCT rnd FROM tournament_matches WHERE tourneyid='".$_POST["id"]."' ORDER BY rnd");
			$allgood = true;
			while($row = $dbc->database_fetch_assoc($data)) {
				if(!$dbc->database_query("UPDATE tournament_matches SET map='".$_POST[$row["rnd"]."_map"]."' WHERE tourneyid='".$_POST["id"]."' AND rnd='".$row["rnd"]."'")) {
					$allgood = false;
				}
			} 
			if($allgood) $str = "success.";
			else $str = "error!";
			?>
			<br />
			<div align="center"><b><?php echo $str; ?></b></div>
			<script language="javascript" type="text/javascript"><!--
			opener.location.reload(true);
			setTimeout('window.close()',1000);
			// --></script>
			<?php
		} else { ?>
			<script language="JavaScript" type="text/javascript">
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
