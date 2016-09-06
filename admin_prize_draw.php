<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('plural'),2);
if ($toggle['prizes']) {
	if ($x->is_secure()) {
		if ($dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes')) > 0) {
			if ($dbc->database_result($dbc->database_query('SELECT lock_prizes FROM prizes_control'), 0) == 1) {
				function draw_prize($prizeID)
                {
                    global $dbc;
					$prizeGroup = $dbc->database_result($dbc->database_query('SELECT prizegroup FROM prizes WHERE prizeid=' . (int)$prizeID), 0);
					$numEligible = $dbc->database_num_rows($dbc->database_query('
						SELECT users.userid, users.username, prizes_votes.*
						FROM users
						LEFT JOIN prizes_votes
						ON users.userid=prizes_votes.userid
						LEFT JOIN prizes_unwinners
						ON prizes_unwinners.userid=users.userid
						WHERE (prizes_votes.prizeid=' . (int)$prizeID . ' OR prizes_votes.getall=1) 
						AND prizes_unwinners.userid IS NULL'
					));
					if ($numEligible == 0) $dbc->database_query("DELETE FROM prizes_unwinners");
					$numWon = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizegroup=" . $prizeGroup . " AND ignoreit=0"));
					if ($numWon >= $numEligible) $dbc->database_query("UPDATE prizes_winners SET ignoreit=1 WHERE prizegroup=" . $prizeGroup);
					// most extensive mysql query EVAR!! ph33r!  -rob
					$winner = $dbc->database_fetch_array($dbc->database_query("
						SELECT users.userid, users.username, prizes_votes.*, prizes_winners.*
						FROM users
						LEFT JOIN prizes_votes 
						ON users.userid=prizes_votes.userid
						LEFT JOIN prizes_unwinners 
						ON prizes_unwinners.userid=users.userid 
						LEFT JOIN prizes_winners 
						ON (prizes_winners.winnerid = users.userid AND prizes_winners.prizegroup = " . $prizeGroup . ") AND prizes_winners.ignoreit != 1 
						WHERE (prizes_votes.prizeid=" . (int)$prizeID . " OR prizes_votes.getall=1) 
						AND prizes_winners.prizegroup IS NULL 
						AND prizes_unwinners.userid IS NULL 
						ORDER BY RAND() 
						LIMIT 1"
					));
					return $winner;
				}
				
				if ($_POST['drawPrizeID']) {
					$prize = $dbc->database_fetch_array($dbc->database_query('SELECT * FROM prizes WHERE prizeid=' . (int)$_POST['drawPrizeID']));
					$winner = draw_prize($_POST['drawPrizeID']);
					$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . ",0,0,0)");
				}
				if (isset($_POST['drawGroupID'])) {
					$prizesInGroup = $dbc->database_query("SELECT * FROM prizes WHERE prizegroup=" . $_POST['drawGroupID'] . " AND tourneyid=0");
					while ($prize = $dbc->database_fetch_array($prizesInGroup)) {
						unset($numAlreadyWon);
						unset($maxGroupOrder);
						if ($prize['prizequantity']  > 1) {
							$numAlreadyWon = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $prize['prizeid']));
							if ($numAlreadyWon > 0) $maxGroupOrder = $dbc->database_result($dbc->database_query("SELECT MAX(group_order) FROM prizes_winners WHERE prizeid=" . $prize['prizeid']), 0) + 1;
							else $maxGroupOrder = 1;
							for ($i = $maxGroupOrder; $i <= $prize['prizequantity']; $i++) {
								unset($winner);
								$winner = draw_prize($prize['prizeid']);
								$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . "," . $i . ",0,0)");
							}
						} else {
							$winner = draw_prize($prize['prizeid']);
							$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . ",0,0,0)");
						}
					}
				}
				if ($_POST['drawQuantityID']) {
					$prize = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM prizes WHERE prizeid=" . $_POST['drawQuantityID']));
					$numAlreadyWon = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $prize['prizeid']));
					if($numAlreadyWon > 0) $maxGroupOrder = $dbc->database_result($dbc->database_query("SELECT MAX(group_order) FROM prizes_winners WHERE prizeid=" . $prize['prizeid']), 0) + 1;
					else $maxGroupOrder = 1;
					$winner = draw_prize($_POST['drawQuantityID']);
					$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . "," . $maxGroupOrder . ",0,0)");
				}
				if ($_POST['claimID']) {
					$dbc->database_query("UPDATE prizes_winners SET claimed=1 WHERE prizeid=" . $_POST['claimID']);
				}
				if ($_POST['claimQuantityID']) {
					$dbc->database_query("UPDATE prizes_winners SET claimed=1 WHERE prizeid=" . $_POST['claimQuantityID'] . " AND group_order=" . $_POST['claimQuantityGroupNum']);
				}
				if ($_POST['claimTourney']) {
					$dbc->database_query("UPDATE prizes SET tourneyclaim=1 WHERE prizeid=" . $_POST['claimTourney']);
				}
				if ($_POST['unclaimID']) {
					$dbc->database_query("UPDATE prizes_winners SET claimed=0 WHERE prizeid=" . $_POST['unclaimID']);
				}
				if ($_POST['unclaimQuantityID']) {
					$dbc->database_query("UPDATE prizes_winners SET claimed=0 WHERE prizeid=" . $_POST['unclaimQuantityID'] . " AND group_order=" . $_POST['unclaimQuantityGroupNum']);
				}
				if ($_POST['drawQuantityPrizeID']) {
					$prize = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM prizes WHERE prizeid=" . $_POST['drawQuantityPrizeID']));
					$numAlreadyWon = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $prize['prizeid']));
					if($numAlreadyWon > 0) $maxGroupOrder = $dbc->database_result($dbc->database_query("SELECT MAX(group_order) FROM prizes_winners WHERE prizeid=" . $prize['prizeid']), 0) + 1;
					else $maxGroupOrder = 1;
					for($i = $maxGroupOrder; $i <= $prize['prizequantity']; $i++) {
						unset($winner);
						$winner = draw_prize($prize['prizeid']);
						$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . "," . $i . ",0,0)");
					}
				}
				if ($_POST['redrawID']) {
					$prize = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $_POST['redrawID']));
					$dbc->database_query("INSERT INTO prizes_unwinners VALUES(" . $prize['winnerid'] . ")");
					$dbc->database_query("DELETE FROM prizes_winners WHERE prizeid=" . $prize['prizeid']);
					$winner = draw_prize($_POST['redrawID']);
					$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . ",0,0,0)");
				}
				if ($_POST['redrawQuantityID']) {
					$prize = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $_POST['redrawQuantityID'] . " AND group_order=" . $_POST['redrawQuantityGroupNum']));
					$dbc->database_query("DELETE FROM prizes_winners WHERE id=" . $prize['id']);
					$dbc->database_query("INSERT INTO prizes_unwinners VALUES(" . $prize['winnerid'] . ")");
					$winner = draw_prize($prize['prizeid']);
					$dbc->database_query("INSERT INTO prizes_winners VALUES(''," . $prize['prizeid'] . "," . $prize['prizegroup'] . "," . $winner['userid'] . "," . $_POST['redrawQuantityGroupNum'] . ",0,0)");
				}
				if ($_POST['tourneyPrizeOverride']) {
					$dbc->database_query("UPDATE prizes SET tourneyid=0, tourneyplace=0 WHERE prizeid=" . $_POST['tourneyPrizeOverride']);
				}
				
				
				$x->display_top(); ?>
				<b><?php echo get_lang('administrator'); ?></b>: <?php echo get_lang('draw_prizes'); ?><br />
				<br />
				<?php
				$x->add_related_link(get_lang('link_admin_prizes'),'admin_prizes.php',2);
				$x->add_related_link(get_lang('link_admin_prize_control'),'admin_prize_control.php',2);
				$x->add_related_link(get_lang('link_admin_prizes_print'),'admin_prizes_print.php',2);
				$x->add_related_link(get_lang('link_chng_prizes'),'chng_prizes.php',1);
				$x->add_related_link(get_lang('link_disp_prizes'),'disp_prizes.php',0);
				$x->display_related_links();
				unset($prizeWinners);
				$prizew = $dbc->database_query("SELECT * FROM prizes_winners LEFT JOIN users ON users.userid=prizes_winners.winnerid ORDER BY prizeid ASC");
				while ($p = $dbc->database_fetch_array($prizew)) {
					if ($p['group_order'] > 0) {
						$prizeWinners[$p['prizeid']][$p['group_order']] = array(
							"id" => $p['id'],
							"winnerid" => $p['winnerid'],
							"winnername" => $p['username'],
							"claimed" => $p['claimed']
						);
					} else {
						$prizeWinners[$p['prizeid']] = array(
							"id" => $p['id'],
							"winnerid" => $p['winnerid'],
							"winnername" => $p['username'],
							"group_order" => $p['group_order'],
							"claimed" => $p['claimed']
						);
					}
				}
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_unwinners"))) { ?>
					<b><?php echo get_lang('note'); ?></b>: <a href="admin_prize_control.php"><?php echo get_lang('absentees'); ?></a><br />
					<br /><?php
				} ?>
				<b><?php echo get_lang('random_prizes'); ?></b>:<br />
				<br />
				<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr style="font-weight: bold; color: <?php echo $colors['blended_text']; ?>" class="sm" bgcolor="<?php echo $colors['cell_title']; ?>">
						<td><?php echo get_lang('col_name'); ?></td>
						<td><?php echo get_lang('col_value'); ?></td>
						<td>&nbsp;</td>
						<td><?php echo get_lang('col_winner'); ?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<?php
					$counter = 0;
					$prizeGroups = $dbc->database_query("SELECT DISTINCT prizegroup FROM prizes ORDER BY prizegroup ASC");
					while ($prizeGroup = $dbc->database_fetch_array($prizeGroups)) { ?>
						<tr>
						<td colspan="4"><b><?php echo get_lang('prize_group'); ?> #<?php print $prizeGroup['prizegroup']; ?></b></td><?php
						$prizesInGroup = $dbc->database_query("SELECT * FROM prizes WHERE prizegroup=" . $prizeGroup['prizegroup'] . " AND tourneyid=0");
						$prizesInGroupDrawn = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizegroup=" . $prizeGroup['prizegroup']));
						$accum = 0;
						while ($p = $dbc->database_fetch_array($prizesInGroup)) { $accum += $p['prizequantity']; }
						if ($prizesInGroupDrawn < $accum) { ?>
							<td colspan="2" align="center"><form action="<?php echo get_script_name(); ?>" method="post"><input type="hidden" name="drawGroupID" value="<?php print $prizeGroup['prizegroup']; ?>"><input type="submit" value="<?php echo get_lang('submit_draw_group'); ?>" class="formcolors"></form></td><?php
						} else { ?>
							<td colspan="2">&nbsp;</td><?php
						} ?>
						</tr>
						<?php
						$thisGroup = $dbc->database_query("SELECT * FROM prizes WHERE prizegroup=" . $prizeGroup['prizegroup'] . " AND tourneyid=0 ORDER BY prizevalue ASC, prizename ASC");
						while ($prize = $dbc->database_fetch_array($thisGroup)) { ?>
							<tr<?php print ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":''); ?>>
							<td><?php get_arrow(); ?>&nbsp;<a href="disp_prizes.php#<?php echo $prize['prizeid']; ?>"><?php print $prize['prizename']; ?></a><?php if($prize['prizequantity']>1) { ?> <font class="sm">(<?php echo get_lang('quantity'); ?>: <?php echo $prize['prizequantity']; ?>)</font><?php } ?></td>
							<td><?php print (MONEY_PREFIX ? MONEY_SYMBOL : '').$prize['prizevalue'].(!MONEY_PREFIX ? MONEY_SYMBOL : ''); ?></td>
							<td class="sm"><?php 
								if ($prize['prizepicture']) { ?>
									[<a href="<?php print $prize['prizepicture']; ?>" border="0" target="_blank"><?php echo get_lang('image'); ?></a>]<?php
								} else print "&nbsp;"; ?>
							</td>
							<td><form action="<?php print get_script_name(); ?>" method="post"><?php
								if (!is_array($prizeWinners[$prize['prizeid']]) || $prize['prizequantity'] > 1) {
									$eligibleUsers = $dbc->database_query("
										SELECT users.userid, users.username, prizes_votes.*
										FROM users
										LEFT JOIN prizes_votes
										ON users.userid=prizes_votes.userid
										LEFT JOIN prizes_unwinners
										ON prizes_unwinners.userid=users.userid
										WHERE (prizes_votes.prizeid=" . $prize['prizeid'] . " OR prizes_votes.getall=1) 
										AND prizes_unwinners.userid IS NULL"
									); ?>
									<select name="eligibleUsers" size="1" >
									<option SELECTED>-- <?php echo get_lang('eligible'); ?> --</option><?php
									while($u = $dbc->database_fetch_array($eligibleUsers)) { ?>
										<option><?php print $u['username']; ?></option><?php
									} ?>
									</select></form><?php
								} else { print $prizeWinners[$prize['prizeid']]['winnername']; } ?>
							</td><?php
							if ($prize['prizequantity'] > 1) { 
								$numDrawn = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizeid=" . $prize['prizeid']));
								if($numDrawn < $prize['prizequantity']) { ?>
									<td colspan="2" align="center"><form action="<?php echo get_script_name(); ?>" method="post"><input type="hidden" name="drawQuantityPrizeID" value="<?php print $prize['prizeid']; ?>"><input type="submit" value="<?php echo get_lang('submit_draw_all'); ?>" class="formcolors"></form></td><?php
								} else { ?>
									<td colspan="2">&nbsp;</td><?php
								}
							} else {
								if ($prizeWinners[$prize['prizeid']]['claimed']) { ?>
									<td><form action="<?php print get_script_name(); ?>" method="post">
									<input type="hidden" name="unclaimID" value="<?php print $prize['prizeid']; ?>"
									<td colspan="2" align="center"><?php echo get_lang('claimed'); ?>. <input type="submit" value="<?php echo get_lang('submit_unclaim'); ?>" class="formcolors"></form></td><?php
								}
								elseif (is_array($prizeWinners[$prize['prizeid']])) { ?>
									<td><form action="<?php echo get_script_name(); ?>" method="post">
									<input type="hidden" name="claimID" value="<?php print $prize['prizeid']; ?>">
									<input type="submit" value="<?php echo get_lang('submit_claim'); ?>" class="formcolors"></form></td>
									<td><form action="<?php echo get_script_name(); ?>" method="post">
									<input type="hidden" name="redrawID" value="<?php print $prize['prizeid']; ?>">
									<input type="submit" value="<?php echo get_lang('submit_redraw'); ?>" class="formcolors"></form></td><?php
								} else { ?>
									<td colspan="2" align="center"><form action="<?php echo get_script_name(); ?>" method="post">
									<input type="hidden" name="drawPrizeID" value="<?php print $prize['prizeid']; ?>">
									<input type="submit" value="<?php echo get_lang('submit_draw'); ?>" class="formcolors"></form></td><?php
								}
							} ?>
							</tr><?php
							if ($prize['prizequantity'] > 1) { 
								for ($i = 1; $i <= $prize['prizequantity']; $i++) { ?>
									<tr<?php print ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":''); ?>>
									<td colspan="3"><font color="<?php echo $colors['blended_text']; ?>"><?php spacer(16); get_arrow(); ?>&nbsp;<?php print $i; ?></font></td>
									<td>
									<?php
									if($prizeWinners[$prize['prizeid']][$i]['winnerid']) {
										print $prizeWinners[$prize['prizeid']][$i]['winnername'];
									} else { echo '&nbsp;'; } 
									if ($prizeWinners[$prize['prizeid']][$i]['claimed']) { ?>
										<td colspan="2" align="center">
										<form action="<?php echo get_script_name(); ?>" method="post">
										<input type="hidden" name="unclaimQuantityID" value="<?php print $prize['prizeid']; ?>">
										<input type="hidden" name="unclaimQuantityGroupNum" value="<?php print $i; ?>">
										<?php echo get_lang('claimed'); ?>. <input type="submit" value="<?php echo get_lang('submit_unclaim'); ?>" class="formcolors"></form></td><?php
									}
									elseif ($prizeWinners[$prize['prizeid']][$i]['winnerid']) { ?>
										<td><form action="<?php echo get_script_name(); ?>" method="post">
										<input type="hidden" name="claimQuantityID" value="<?php print $prize['prizeid']; ?>">
										<input type="hidden" name="claimQuantityGroupNum" value="<?php print $i; ?>">
										<input type="submit" value="<?php echo get_lang('submit_claim'); ?>" class="formcolors"></form></td>
										<td><form action="<?php echo get_script_name(); ?>" method="post">
										<input type="hidden" name="redrawQuantityID" value="<?php print $prize['prizeid']; ?>">
										<input type="hidden" name="redrawQuantityGroupNum" value="<?php print $i; ?>">
										<input type="submit" value="<?php echo get_lang('submit_redraw'); ?>" class="formcolors"></form></td><?php
									} else { ?>
										<td colspan="2" align="center"><form action="<?php echo get_script_name(); ?>" method="post">
										<input type="hidden" name="drawQuantityID" value="<?php print $prize['prizeid']; ?>">
										<input type="submit" value="<?php echo get_lang('submit_draw'); ?>" class="formcolors"></form></td><?php
									} ?>
									</tr><?php
								}
							}
							$counter++;
						}
					} ?>
					</table><br /><br /><?php
						
					function allscores($teamid)
                    {
		                global $tournament, $dbc;
		                $totalscore = 0;
		                $data = $dbc->database_query("SELECT score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$teamid."'");
		                while($row = $dbc->database_fetch_assoc($data)) {
		                        $totalscore += $row["score"];
		                }
		                return $totalscore;
	        		}
					
					function get_top_four($tourneyid)
                    {
                        global $dbc;
						$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));
						include "include/tournaments/scoring_".$tournament["ttype"].".php";
						return array($first_id,$second_id,$third_id,$fourth_id);
					}
					unset($tourneyPlacers);
					$tournaments = $dbc->database_query("SELECT * FROM tournaments");
					if ($dbc->database_num_rows($tournaments)) {
						while ($t = $dbc->database_fetch_array($tournaments)) {
							$placers = get_top_four($t['tourneyid']);
							$tourneyPlacers[$t['tourneyid']] = array(1 => $placers[0],$placers[1],$placers[2],$placers[3]);
						} 
						$won = false;
						foreach ($tourneyPlacers AS $tid => $tar) {
							if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes WHERE tourneyid=" . $tid)) > 0) {
                                $won = true;
                            }
						}
						if (is_array($tourneyPlacers) && $won) { ?>
							<b><?php echo get_lang('tournament_prizes'); ?>:</b><br /><br />
							<table border="0" cellpadding="3" cellspacing="0" width="100%">
							<tr style="font-weight: bold; color: <?php echo $colors['blended_text']; ?>" class="sm" bgcolor="<?php echo $colors['cell_title']; ?>"><td>name</td><td>value</td><td>&nbsp;</td><td><?php echo get_lang('col_tournament'); ?></td><td><?php echo get_lang('col_winners'); ?></td><td colspan="2">&nbsp;</td></tr><?php
							$tournamentPrizes = $dbc->database_query("SELECT * FROM prizes LEFT JOIN tournaments ON tournaments.tourneyid=prizes.tourneyid WHERE prizes.tourneyid > 0 AND tournaments.lockstart=1");
							while ($tp = $dbc->database_fetch_array($tournamentPrizes)) {
								if ($tourneyPlacers[$tp['tourneyid']][$tp['tourneyplace']]) { ?>
									<tr bgcolor="<?php print $bgc; ?>"><td valign="top"><?php print $tp['prizename']; ?></td>
									<td valign="top"><?php print $tp['prizevalue']; ?></td>
									<td valign="top" class="sm"><?php 
									if ($tp['prizepicture']) { ?>
										[<a href="<?php print $tp['prizepicture']; ?>" border="0" target="_blank"><?php echo get_lang('image'); ?></a>]<?php
									} else print "&nbsp;"; ?></td>
									<td valign="top" class="sm"><?php echo $tp['tourneyplace'].($tp['tourneyplace']==1?'st':($tp['tourneyplace']==2?'nd':($tp['tourneyplace']==3?'rd':($tp['tourneyplace']==4?'th':''))))." in <a href=\"tournaments.php?id=".$tp['tourneyid']."\">".$tp['name']."</a>"; ?></td>
									<td valign="top" class="sm"><?php 
									if ($tp['per_team'] == 1) {
										print $dbc->database_result($dbc->database_query("SELECT username FROM users WHERE userid=" . $tourneyPlacers[$tp['tourneyid']][$tp['tourneyplace']]), 0); 
									} else {
										$playerData = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='" . $tp["tourneyid"] . "' AND tournament_players.teamid='" . $tourneyPlacers[$tp['tourneyid']][$tp['tourneyplace']] . "' ORDER BY username");
										while($p = $dbc->database_fetch_array($playerData)) {
											print $p['username'] . '<br />';
										}
									} ?></td>
									<form action="<?php echo get_script_name(); ?>" method="post">
									<td> <?php
									if ($tp['tourneyclaim']) {
										print get_lang('claimed').'.';
									} else { ?>
										<input type="hidden" name="claimTourney" value="<?php print $tp['prizeid']; ?>">
										<input type="submit" value="<?php echo get_lang('submit_claim'); ?>" class="formcolors"><?php
									} ?>
									</td></form>
									<form action="<?php echo get_script_name(); ?>" method="post">
									<td><?php
									if (!$tp['tourneyclaim']) { ?> 
										<input type="hidden" name="tourneyPrizeOverride" value="<?php print $tp['prizeid']; ?>">
										<input type="submit" value="<?php echo get_lang('submit_draw_instead'); ?>" class="formcolors"><?php
									} else { 
                                        print '&nbsp;'; 
                                    }
                                    ?>
									</td>
									</form></tr><?php
									$bgc == $colors['cell_alternate'] ? $bgc = '' : $bgc = $colors['cell_alternate'];
								}
							} ?>
							</table><br /><br /><?php
						}
					}
				$x->display_bottom();
			} else {
                $x->display_slim(get_lang('error_must_be_locked'),"admin_prize_control.php");
            }
		} else {
            $x->display_slim(get_lang('error_no_prizes'),"admin_prizes.php");
        }
	} else {
        $x->display_slim(get_lang('noauth'));
    }
} else {
    $x->display_slim(get_lang('error_prizes_disabled')); 
}    
?>
