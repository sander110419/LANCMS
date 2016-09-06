<?php
// displays the small window with submitted scores (by the players).

require_once 'include/_universal.php'; 
$x = new universal(get_lang("plural"),get_lang("singular"),1);
$x->display_smallwindow_top(); ?>
<table border="02 cellpadding="4" width="100%"><tr><td>
<?php
if (!$x->is_secure() || empty($_GET) || empty($_GET['id']) || empty($_GET['matchid'])) { ?>
	<script language="JavaScript">
	<!--
	window.close();
	// -->
	</script>
	<?php
} elseif($x->is_secure()) { 
	$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']));
	if (current_security_level() >= 2 || (current_security_level() >= 1 && $tournament['moderatorid'] == $_COOKIE['userid'])) {
		$data = $dbc->database_query("SELECT id,team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$_GET["matchid"]."'"); ?>
		<script language="JavaScript">
		<!--
		function user_alert(usrname) {
			alert("user: "+usrname);
		} // -->
		</script>
		<table border="0" cellpadding="2" width="100%" class="sm"><tr><td>
		<tr>
			<td><b><u><?php echo get_lang("teamname"); ?></u></b></td>
			<?php
			$vote = $dbc->database_query("SELECT userid FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$_GET["matchid"]."' GROUP BY userid ORDER BY userid");
			while($score_votes = $dbc->database_fetch_assoc($vote)) { 
				if($tournament["per_team"]>1) {
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$score_votes["userid"]."'"));
				} else {
					$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$score_votes["userid"]."'"));
				} ?>
				<td><a href="javascript:user_alert('<?php echo $team["name"]; ?>')"><b><u><?php echo get_lang("id"); ?></u></b></a><br /></td>
				<?php
			} ?>
			</td>
		</tr>
		<?php
		while($row = $dbc->database_fetch_assoc($data)) { 
			if($tournament["per_team"]>1) {
				$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$row["team"]."'"));
			} else {
				$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$row["team"]."'"));
			} ?>
			<tr><td><b><?php echo $teaminfo["name"]; ?></b></td>
			<?php
			$vote = $dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$_GET["matchid"]."' AND entry_id='".$row["id"]."' ORDER BY userid");
			while($score_votes = $dbc->database_fetch_assoc($vote)) {
				echo "<td>".$score_votes["entry_val"]."</td>";
			}
			?>
			</tr>
			<?php
		} ?>
		</table>
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
