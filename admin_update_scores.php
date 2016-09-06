<?php
include "include/_universal.php"; 
$x = new universal("free for all scores","",1);
$x->display_smallwindow_top(); ?>
<table border=0 cellpadding=4 width="100%"><tr><td>
<?php
if(!$x->is_secure()||empty($_GET)||empty($_GET["id"])||empty($_GET["matchid"])||empty($_GET["i"])||empty($_GET["j"])) { ?>
	<script language="JavaScript">
	<!--
	window.close();
	// -->
	</script>
	<?php
} else { 
	$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_GET["id"]."'"));
	if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) { 
		$match = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE id='".$_GET["matchid"]."'"));
		$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$_GET["id"]."' AND matchid='".$_GET["matchid"]."' ORDER by top DESC,team DESC"); ?>
		<b>round <?php echo $match["rnd"]; ?> - match <?php echo $match["mtc"]; ?></b><br />
		<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br />
		<form action="admin_update_tournaments.php" method="POST">
		<input type="hidden" name="id" value="<?php echo $tournament["tourneyid"]; ?>">
		<input type="hidden" name="matchid" value="<?php echo $_GET["matchid"]; ?>">
		<input type="hidden" name="i" value="<?php echo $_GET["i"]; ?>">
		<input type="hidden" name="j" value="<?php echo $_GET["j"]; ?>">
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<?php
		while($row = $dbc->database_fetch_assoc($data)) { 
			if($tournament["per_team"]==1) { 
				$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username as name FROM users WHERE userid='".$row["team"]."'")); 
			} else {
				$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$row["team"]."'")); 
			} ?>
			<tr>
				<td><font class="sm"><b><?php echo (strlen($team["name"])>24?substr($team["name"],0,22)."...":$team["name"]); ?></b><br /></font></td>
				<td width="16"><input type="text" name="<?php echo $row["id"]; ?>_score" maxlength="20" style="font-size: 9px; width: 14px;" value="<?php echo (isset($row["score"])?$row["score"]:""); ?>"></td>
			</tr>
			<?php
		} ?>
		</table>
		<img src="img/pxt.gif" width="1" height="4" border="0" alt=""><br /><input type="submit" value="update" style="font-size: 9px; width: 99%">
		</form>
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
$x->display_smallwindow_bottom();
?>
