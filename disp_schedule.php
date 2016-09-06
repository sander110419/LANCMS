<?php
require_once 'include/_universal.php';
$x = new universal('schedule','',0);
if ($x->is_secure() && $toggle['schedule']) { 
	$x->display_top(); ?>
	<b>schedule</b>:<br /><br />
	<?php
	$x->add_related_link('add/modify schedule entries','admin_schedule.php',2);
	$x->add_related_link('add/modify tournament entries','admin_tournament.php',2);
	if(!ALP_TOURNAMENT_MODE) $x->add_related_link('modify all foodruns','admin_foodrun.php',2);
	if($toggle["foodrun"]) $x->add_related_link('add/modify food runs','foodrun.php',1);
	$x->display_related_links();
	echo '<br />';
	if ($toggle['foodrun']) {
		$holder = ' UNION (SELECT itemtime,headline,-1,userid,0 FROM foodrun)';
	} else {
		$holder = '';
	}
	
	$query = "(SELECT itemtime,name,tourneyid,0 AS userid, 0 AS itemtime_priv FROM tournaments) UNION (SELECT itemtime,headline,0,0 AS userid,itemtime_priv FROM schedule WHERE itemtime_priv<=".current_security_level().")".$holder." order by itemtime;";
	$data = $dbc->database_query($query);
	$counter = 0; 
	if(!ALP_TOURNAMENT_MODE) { ?>
		the schedule on the sidebar lists foodruns (maximum of 4), tournaments, and miscellaneous schedule items occuring in the next hour.  the schedule appearing below lists everything.<br />
		<br />
		<?php
	} ?>
	<table border=0 cellpadding=0 cellspacing=0 width="100%">
	<?php
	if(!ALP_TOURNAMENT_MODE) { ?>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr><td colspan=3 bgcolor="<?php echo $colors['cell_title']; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr>
			<td width="160"><font color="<?php echo $colors['blended_text']; ?>"><?php echo disp_datetime($start, 0); ?></font><br /><?php spacer(160); ?></td>
			<td class="smm"><?php spacer(70); ?></td>
			<td width="100%">&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><b>start</b>&nbsp;</font></font></td>
		</tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr><td colspan=3 bgcolor="<?php echo $colors['cell_title']; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<?php
	}
	while($row = $dbc->database_fetch_array($data)) { ?>
			<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
			<tr><td colspan=3 bgcolor="<?php echo (round((date('U',strtotime($row['itemtime']))-date('U'))/60)>0&&round((date('U',strtotime($row['itemtime']))-date('U'))/60)<60&&$row['tourneyid']!=-1?$colors['alert']:$colors['cell_title']); ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
			<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr<?php echo ($row['tourneyid']==-1?" style=\"color: ".$colors['blended_text']."\"":""); ?>>
			<td><?php echo (!empty($row['itemtime'])?disp_datetime(strtotime($row['itemtime']), 0):'&nbsp;'); ?></td>
			<td class="smm" align="right"><?php 
			if(!empty($row['itemtime'])) {
				echo (round((date('U',strtotime($row['itemtime']))-date('U'))/60)<60&&round((date('U',strtotime($row['itemtime']))-date('U'))/60)>0?"<font color=\"".$colors['alert']."\">":""); ?><?php echo get_time_diff(date('U'),date('U',strtotime($row['itemtime']))); spacer(10); ?><?php echo (round((date('U',strtotime($row['itemtime']))-date('U'))/60)<60&&round((date('U',strtotime($row['itemtime']))-date("U"))/60)>0?"</font>":""); 
			} else {
				echo '&nbsp;';
			} ?></td>
			<td>&nbsp;<?php 
				echo ($row['itemtime_priv']==2?"<font class=\"smm\">admin: </font>":($row['itemtime_priv']==3?"<font class=\"smm\">super admin: </font>":'')).($row['tourneyid']>0?'<b>tournament</b>: ':($row['tourneyid']==-1?'food run: ':''));
				echo ($row['tourneyid']>0?"<a href=\"tournaments.php?id=".$row['tourneyid']."\"":""); ?><?php echo (!empty($row['itemtime']) && (date('U',strtotime($row['itemtime']))-date('U'))<3600&&$row['tourneyid']>=0?"<font color=\"".$colors['primary']."\"><b>":''); ?><?php echo ($row['tourneyid']>0?"<u>":""); ?><b><?php echo $row['name']; ?></b><?php if($row['userid']>0) { ?> &nbsp;&nbsp;<font class="sm">[ <?php $user = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$row["userid"]."'")); echo "<a href=\"disp_users.php?id=".$row["userid"]."\">".$user["username"]."</a>"; ?> ] </font><?php } ?><?php echo ($row["tourneyid"]>0?"</u>":""); ?><?php echo (!empty($row['itemtime']) && (date("U",strtotime($row["itemtime"]))-date("U"))<3600&&$row["tourneyid"]>=0?"</b></font>":""); ?><?php echo ($row["tourneyid"]>0?"</a>":""); ?></td>
		</tr>
			<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
			<tr><td colspan=3 bgcolor="<?php echo (round((date('U',strtotime($row['itemtime']))-date('U'))/60)>0&&round((date('U',strtotime($row['itemtime']))-date('U'))/60)<60&&$row['tourneyid']!=-1?$colors['alert']:$colors['cell_title']); ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
			<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
			<?php
		$counter++;
	}
	if(!ALP_TOURNAMENT_MODE) { ?>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr><td colspan=3 bgcolor="<?php echo $colors['cell_title']; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr>
			<td><font color="<?php echo $colors['blended_text']; ?>">
            <?php echo disp_datetime($end);	?></font></td>
			<td class="smm">&nbsp;</td>
			<td>&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><b>end</b>&nbsp;</font></td>
		</tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<tr><td colspan=3 bgcolor="<?php echo $colors['cell_title']; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt=""></td></tr>
		<tr><td colspan=3><img src="img/pxt.gif" width="1" height="4" border="0" alt=""></td></tr>
		<?php
	} ?>
	</table>
	<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>