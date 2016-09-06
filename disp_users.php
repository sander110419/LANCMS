<?php
include "include/_universal.php";
$x = new universal("user bio","user bio",0);
if($x->is_secure()) { 
	$x->display_top();
	$data = $dbc->database_query("SELECT * FROM users WHERE userid='".$_GET["id"]."'");
	if($row = $dbc->database_fetch_assoc($data)) {
		begitem($row["username"]); 
		if($toggle["marath"]) {
			start_module("main","","","","160","right"); ?>
			<div align="center"><a href="themarathon.php"><b>the marathon</b></a><br />
			<br />
			<font class="sm">rank</font><br />
			<font style="font: 40px Arial; font-weight: bold"><?php echo (!empty($row["marathon_rank"])?$row["marathon_rank"]:'--'); ?></font><br />
			<br />
			<font class="sm">total points</font><br />
			<font style="font: 40px Arial; font-weight: bold"><?php echo (isset($row['marathon_points'])?$row['marathon_points']:''); ?></font><br />
			<br />
			<font class="sm">tournament score</font><br />
			<font style="font: 40px Arial; font-weight: bold"><?php echo (isset($row['marathon_points_tourney'])?$row['marathon_points_tourney']:''); ?></font><br />
			</div>
			<?php
			end_module();
		} ?>
		<table border=0>
		<tr><td><a href="users.php?show=0" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">real name</font></a> </td><td><?php echo $row["first_name"]." ".$row["last_name"]; ?><br /></td></tr>
		<?php if(!empty($row["gaming_group"])) { ?><tr><td><a href="users.php?show=0" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">gaming group</font></a> </td><td><?php echo $row["gaming_group"]; ?><br /></td></tr><?php } ?>
		<?php if(!empty($row["quote"])) { ?><tr><td><font color="<?php echo $colors["blended_text"]; ?>" size=1>quote</font></a> </td><td><i><?php echo $row["quote"]; ?></i><br /></td></tr><?php } ?>
		<?php if(($row["display_email"]&&!empty($row["email"]))||current_security_level()>=2) { ?><tr><td><a href="users.php?show=1" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">email</font></a> </td><td><a href="mailto:<?php echo $row["email"]; ?>"><?php echo $row["email"]; ?></a><br /></td></tr><?php } ?>
		<?php if(!empty($row["gender"])) { ?><tr><td><a href="users.php?show=4" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">gender</font></a> </td><td><?php echo $row["gender"]; ?><br /></td></tr><?php } ?>
		<tr><td><a href="users.php?show=8" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">time of arrival</font></a></td><td><?php echo disp_datetime(strtotime($row["date_of_arrival"]), 0); ?></td></tr>
		<?php if(!empty($row["date_of_departure"])) { ?><tr><td><a href="users.php?show=8" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">time of departure</font></a> </td><td><?php echo disp_datetime(strtotime($row["date_of_departure"]),1); ?></td></tr><?php } ?>
		<?php if($row["display_ip"]||current_security_level()>=2) { ?><tr><td><a href="users.php?show=9" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">ip address</font></a></td><td><?php echo $row["recent_ip"]; ?><br /></td></tr><?php } ?>
		<?php if(!empty($row["room_loc"])&&$toggle["seating"]) { ?><tr><td><a href="seating.php" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">seating map</font></a> </td><td><a href="seating.php?c=<?php echo $row["room_loc"]; ?>">find <?php echo $row["username"]; ?></a><br /></td></tr><?php } ?>
		<?php
		$gamerequest = $dbc->database_query("SELECT * FROM game_requests WHERE userid='".$row["userid"]."'");
		if($toggle["gamerequests"]) { ?>
 			<tr><td><a href="gamerequest.php" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">open play game requests</font></a></td><td><b><?php echo ($dbc->database_num_rows($gamerequest)?$dbc->database_num_rows($gamerequest):0); ?></b><br /></td></tr>
			<?php
		}
		$foodruns = $dbc->database_query("SELECT * FROM foodrun WHERE userid='".$row["userid"]."'");
		if($toggle["foodrun"]) { ?>
 			<tr><td><a href="disp_schedule.php" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">food runs</font></a></td><td><b><?php echo ($dbc->database_num_rows($foodruns)?$dbc->database_num_rows($foodruns):0); ?></b><br /></td></tr>
			<?php
		}
		$techsupport = $dbc->database_query("SELECT * FROM techsupport WHERE userid='".$row["userid"]."'");
		if($toggle["techsupport"]) { ?>
 			<tr><td><a href="techsupport.php" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">tech support requests</font></a></td><td><b><?php echo ($dbc->database_num_rows($techsupport)?$dbc->database_num_rows($techsupport):0); ?></b><br /></td></tr>
			<?php
		}
		$techsupport = $dbc->database_query("SELECT * FROM techsupport WHERE fixer='".$row["userid"]."'");
		if($toggle["techsupport"]) { ?>
 			<tr><td><a href="techsupport.php" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">tech support requests solved</font></a></td><td><b><?php echo ($dbc->database_num_rows($techsupport)?$dbc->database_num_rows($techsupport):0); ?></b><br /></td></tr>
			<?php
		}
		if($toggle['filesharing']) {
			include 'include/_gaming_rig_db.php';
				  if(!empty($row["sharename"])) { ?><tr><td><a href="users.php?show=1" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">sharename</font></a> </td><td><?php echo $row["sharename"];?><br /></td></tr><?php } ?>
			<?php if($row["ftp_server"]) { ?><tr><td><a href="users.php?show=2" style="color: <?php echo $colors["blended_text"]; ?>"><font class="sm">ftp_server</font></a> </td><td><?php if($row["ftp_server"]) { echo "yes [<a href=\"ftp://".$row["recent_ip"]."/\"><font size=1><b>connect</b></font></a>]"; } else { echo "no"; } ?><br /></td></tr><?php } ?>
		<?php 
		}
		if($toggle['gamingrigs']) { ?>
			<tr><td colspan=2><br /><b>gaming rig</b> &nbsp;&nbsp;<font size=1><?php if(current_security_level()>=1) { ?>[ <a href="chng_userinfo.php"><b>modify own</b></a> ]<?php } ?>&nbsp;&nbsp;[ <a href="users.php?show=5"><b>show all</b></a> ]<br /></font><br /></td></tr>
			<tr><td colspan=2><?php
			if($row['comp_proc'] == 'AMD') { ?><img src="<?php echo (file_exists($master['currentskin'].'amd.gif')?$master['currentskin'].'amd.gif':'img/rigs/amd.gif'); ?>" width="56" height="16" border="1" alt="amd"><?php spacer(4); }
			if($row['comp_proc'] == 'Intel') { ?><img src="<?php echo (file_exists($master['currentskin'].'intel.gif')?$master['currentskin'].'intel.gif':'img/rigs/intel.gif'); ?>" width="56" height="16" border="1" alt="intel"><?php spacer(4); }
			if($row['comp_gfx_gpu'] == 'ATI') { ?><img src="<?php echo (file_exists($master['currentskin'].'ati.gif')?$master['currentskin'].'ati.gif':'img/rigs/ati.gif'); ?>" width="23" height="16" border="1" alt="ati"><?php spacer(4); }
			if($row['comp_gfx_gpu'] == 'Nvidia') { ?><img src="<?php echo (file_exists($master['currentskin'].'nvidia.gif')?$master['currentskin'].'nvidia.gif':'img/rigs/nvidia.gif'); ?>" width="28" height="16" border="1" alt="nvidia"><?php spacer(4); }
			?></td></tr>
			<tr><td><font color="<?php echo $colors["blended_text"]; ?>" size=1>processor</font> </td><td><?php echo $row["comp_proc"].' '.$row['comp_proc_type'].(!empty($row['comp_proc_spd'])?' at '.$row['comp_proc_spd'].' MHz':''); ?><br /></td></tr>
			<tr><td><font color="<?php echo $colors["blended_text"]; ?>" size=1>memory</font> </td><td><?php echo (!empty($row['comp_mem'])?$row["comp_mem"].' MB ':'').$row['comp_mem_type']; ?><br /></td></tr>
			<tr><td><font color="<?php echo $colors["blended_text"]; ?>" size=1>storage</font> </td><td><?php echo (!empty($row['comp_hdstorage'])?$row["comp_hdstorage"].' GB':''); ?><br /></td></tr>
			<tr><td><font color="<?php echo $colors["blended_text"]; ?>" size=1>graphics</font> </td><td><?php echo $row['comp_gfx_gpu'] . ' - ' . $row['comp_gfx_type']; ?><br /></td></tr>
			<?php
		} 
		if($toggle['caffeine']) { 
			$counter = 0;
			$users = $dbc->database_query("SELECT * FROM users WHERE caffeine_mg!=0 ORDER BY caffeine_mg DESC");
			while($urow = $dbc->database_fetch_assoc($users)) {
				$counter++;
				if($urow['userid']==$_GET['id']) {
					break;
				}
			}
			$num_users = $dbc->database_num_rows($users);
			?>
			<tr><td colspan=2><br /><b>caffeine</b> &nbsp;&nbsp;<font size=1><?php 
				if(current_security_level()>=1) { 
					?>[ <a href="caffeine.php?action=add"><b>add</b></a> ]<?php 
				}
				?>&nbsp;&nbsp;[ <a href="caffeine.php"><b>show all</b></a> ]<br /></font><br /></td></tr>
			<tr>
				<td><font color="<?php echo $colors["blended_text"]; ?>" size="1">current milligram count:</font> </td>
				<td><?php echo $row['caffeine_mg']; ?> mg <?php
				if($row['caffeine_mg'] > 0) {
					?>&nbsp;&nbsp;<font class="sm">[rank <?php echo $counter.' / '.$num_users; ?>]<?php
				} ?></td>
			</tr>
			<?php
		}
		if($toggle["benchmarks"]) { ?>
			<tr><td colspan=2><br /><b>benchmarks</b> &nbsp;&nbsp;<font size=1><?php if(current_security_level()>=1) { ?>[ <a href="chng_benchmarks.php"><b>modify own</b></a> ]<?php } ?>&nbsp;&nbsp;[ <a href="benchmarks.php"><b>show all</b></a> ]<br /></font><br /></td></tr>
			<?php
			$query = $dbc->database_query("SELECT * FROM benchmarks");
			while($benchmark = $dbc->database_fetch_assoc($query)) { 
				$value = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users_benchmarks WHERE userid='".$_GET["id"]."' AND benchid='".$benchmark["id"]."'"));
				
				$counter = 0;
				$users = $dbc->database_query("SELECT * FROM users_benchmarks WHERE benchid='".$benchmark['id']."' AND value!=0 ORDER BY value DESC");
				while($urow = $dbc->database_fetch_assoc($users)) {
					$counter++;
					if($urow['userid']==$_GET['id']) {
						break;
					}
				}
				$num_users = $dbc->database_num_rows($users); ?>
				<tr>
					<td><font color="<?php echo $colors["blended_text"]; ?>" size="1"><?php echo strtolower($benchmark["name"]); ?>:</font> </td>
					<td><?php  echo ($value["value"]==ceil($value["value"])?round($value["value"]):$value["value"]); ?> 
							<?php 
							if(!empty($value['value'])) { 
								?>&nbsp;&nbsp;<span class="sm">[rank <?php echo $counter.'/'.$num_users; ?>]</span><?php 
							} ?>
					</td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<br />
		<?php
		enditem($row["username"]); ?>
		<br />
		<div align="right">[<a href="users.php"><font style="font-size: 11px"><b>back to all users</b></font></a>]</div>
		<?php
	} else { ?>
		the user you are trying to find no longer exists.<br /><br />
		<?php
	}
	$x->display_bottom();
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>
