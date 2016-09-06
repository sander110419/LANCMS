<?php
require_once 'include/_universal.php';
$x = new universal("prizes","prizes",0);
if($toggle['prizes']) {
	if($x->is_secure()) {
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes")) > 0) {
			$x->display_top(); ?>
			<b>prizes</b>:<br />
			<br />
			<?php
			$x->add_related_link("prize database","admin_prizes.php",2);
			$x->add_related_link("prize control panel","admin_prize_control.php",2);
			$x->add_related_link("print off prize slips for drawing.","admin_prizes_print.php",2);
			$x->add_related_link("draw prizes interactively.","admin_prize_draw.php",2);
			$x->add_related_link("register for prizes.","chng_prizes.php",1);
			$x->display_related_links();

			function get_top_four($tourneyid) {
                global $dbc;
				$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));
				include "include/tournaments/scoring_".$tournament["ttype"].".php";
				return array($first_id,$second_id,$third_id,$fourth_id);
			}
		    function allscores($teamid) {
	            global $tournament, $dbc;
	            $totalscore = 0;
	            $data = $dbc->database_query("SELECT score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$teamid."'");
	            while($row = $dbc->database_fetch_assoc($data)) {
	                    $totalscore += $row["score"];
	            }
	            return $totalscore;
		    } ?>
			<b>note</b>: each eligible user is allowed one prize per group.  if the number of prizes in a group is larger than the number of eligible users, the drawing will start fresh with the full list of eligible users.<br />
			<br />
			<table border="0" cellpadding="3" cellspacing="0" class="centerd" width="100%"><?php
			$counter = 0;
			$allPrizes = $dbc->database_query("SELECT * FROM prizes ORDER BY prizegroup, prizename, prizevalue");
			while($row = $dbc->database_fetch_assoc($allPrizes)) {
				if($row['tourneyid']>0) {
					$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$row['tourneyid']."'"));
					$top4 = get_top_four($row['tourneyid']);
				}
				 ?>
				<tr<?php echo ($counter%2==1?" bgcolor=\"".$colors["cell_alternate"]."\"":""); ?>>
				<td width="218"><a name="<?php echo $row['prizeid']; ?>"></a><div align="center">
					<?php 
					if(!empty($row["prizepicture"])) { ?>
						<img src="<?php echo $row['prizepicture']; ?>" height="163" border="0" alt="<?php echo $row['prizename']; ?>" />
						<?php
					} else {
						spacer(218,1,1); 
						spacer(1,50,0,'absmiddle'); ?><font class="smm" color="<?php echo $colors['blended_text']; ?>">picture not available.</font>
						<?php
					} ?></div>
				</td>
				<td valign="top"><?php echo $row["prizename"]; ?><br />
					<br />
					<table border="0" class="sm">
						<tr><td>quantity: </td><td><?php echo $row["prizequantity"]; ?></td></tr>
						<tr><td>value: </td><td><?php echo ($row["prizevalue"]!=0?(MONEY_PREFIX ? MONEY_SYMBOL : '').$row["prizevalue"].(!MONEY_PREFIX ? MONEY_SYMBOL : ''):"&nbsp;"); ?></td></tr>
						<tr><td>group: </td><td><?php echo $row['prizegroup']; ?></td></tr>
						<tr><td>type: </td><td><?php 
						if($row['tourneyid']>0) {
							echo "tournament prize: ".$row['tourneyplace'].($row['tourneyplace']==1?'st':($row['tourneyplace']==2?'nd':($row['tourneyplace']==3?'rd':($row['tourneyplace']==4?'th':''))))." place in <a href=\"tournaments.php?id=".$row['tourneyid']."\">".$tournament['name']."</a>.";
						} else {
							echo "random door prize.";
						}
						 ?></td></tr>
						<tr><td valign="top"><b>winner<?php echo ($row['prizequantity']>0?'s':''); ?></b>:</td><td><?php
						if($dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_display_groups WHERE prizegroup='".$row['prizegroup']."'"))) {
							if($row['tourneyid']>0) {
								if($tournament['per_team']>1) {
									$winners = $dbc->database_query("SELECT tournament_players.*,users.username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.teamid='".$top4[$row['tourneyplace']-1]."'");
								} else {
									$winners = $dbc->database_query("SELECT * FROM users WHERE userid='".$top4[$row['tourneyplace']-1]."'");
								}
							} else {
								$winners = $dbc->database_query("SELECT prizes_winners.*,users.username,users.userid FROM prizes_winners LEFT JOIN users ON prizes_winners.winnerid=users.userid WHERE prizes_winners.prizeid='".$row['prizeid']."'");
							}
							while($pwinners = $dbc->database_fetch_assoc($winners)) { ?>
								<?php echo ($pwinners['userid']==$userinfo['userid']?"<font color=\"".$colors['primary']."\"><b>":"").$pwinners['username'].($pwinners['userid']==$userinfo['userid']?"</b></font> &lt;-- <font color=\"".$colors['primary']."\"><b>WINNAR!</b></font>":""); ?><br />
								<?php
							}
						}
						?></td></tr>
					</table>
					<br />
					<br />
				</td>
				</tr>
				<?php
				$counter++;
			} ?>
			</table><?php
			$x->display_bottom();
		} else $x->display_slim("there are no prizes in the database.");
	} else $x->display_slim("you are not authorized to view this page.");
} else $x->display_slim("the administrator has disabled prizes for this LAN."); ?>