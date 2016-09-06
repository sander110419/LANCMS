<?php 
require_once 'include/_universal.php';
$x = new universal('benchmarking competition','',0);
$x->display_top();
if ($x->is_secure() && $toggle['benchmarks']) { ?>
	<strong>benchmarks</strong>:<br />
	<br />
	<?php
	$x->add_related_link('modify your benchmarks.','chng_benchmarks.php',1);
	if ($toggle['uploading']) { 
        $x->add_related_link('upload screenshots of your benchmarks.','upload.php?type=benchmarks',1);
    }
	$x->display_related_links(); ?>
	<br />
	<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<tr><td colspan="4" bgcolor="<?php echo $colors['cell_title']; ?>"><strong><font color="<?php echo $colors['primary']; ?>">Composite Scores</font></strong></td></tr>	
	<?php
	$data = $dbc->database_query("SELECT * FROM benchmarks WHERE composite='0' ORDER BY name");
	$benchids = array();
	while ($row = $dbc->database_fetch_assoc($data)) {
		$benchids[$row['id']] = array($row['name'],$row['deflate']);
	}
	$counter = 0;
	$additional = '';
	foreach ($benchids as $key => $val) {
		if($counter!=0) $additional .= ' OR';
		$additional .= " benchid='".$key."'";
		$counter++;
	}
	$composite = array();
	$data = $dbc->database_query('SELECT DISTINCT userid FROM users_benchmarks WHERE '.$additional);
	while ($row = $dbc->database_fetch_assoc($data)) {
		$user_composite = 0;
		$query = "SELECT * FROM users_benchmarks WHERE userid='".$row['userid']."' AND value!=0 AND (".$additional.") ORDER BY benchid";
		$temp = $dbc->database_query($query);
		while($temprow = $dbc->database_fetch_assoc($temp)) {
			$benchmark = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM benchmarks WHERE id='".$temprow['benchid']."'"));
			$user_composite += ($benchmark['deflate']/100)*$temprow["value"];
		}
		if ($user_composite!=0) { $composite[$row['userid']] = $user_composite; }
	} ?>
	<tr class="sm"><td><strong><u>#</u></strong></td><td><strong><u>username</u></strong></td><td colspan=2><strong><u>value</u></strong></td></tr>
	<?php
	$counter = 1;
	arsort($composite);
	foreach ($composite as $key=>$val) { 
		if ($counter == 1 && $master['benchmarkleader'] != $key) {
            $dbc->database_query("UPDATE master SET benchmarkleader='".$key."'");
        }
		$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$key."'")); ?>
		<tr><td><?php echo $counter; ?></td><td><a href="disp_users.php?id=<?php echo $user['userid']; ?>"><?php echo $user['username']; ?></a></td><td colspan=2><?php echo ($val==ceil($val)?round($val):$val); ?></td></tr>
		<?php
		$counter++;
	} ?>
	<tr><td colspan=4>
		<br />
		<br />
		<table border="0" cellpadding="3" cellspacing="0" class="centerd">
		<tr bgcolor="<?php echo $colors['cell_title']; ?>" class="sm"><td><strong>eligible benchmarks</strong></td><td><strong>weight</strong></td></tr>
		<?php
		foreach ($benchids as $val) { ?>
			<tr class="sm"><td><strong><?php echo $val[0];?></strong></td><td><div align="center"><?php echo $val[1]; ?>%</div></td></tr>
			<?php
		} ?>
		<tr><td colspan=2 class="sm">
			scores are multiplied by the specified weight<br />
			and added to each users composite score.</td></tr>
		</table>
		<br />
		<br />
	</td></tr>	
	<?php
	$data = $dbc->database_query("SELECT * FROM benchmarks ORDER BY name"); 
	while ($row = $dbc->database_fetch_assoc($data)) { 
		$users = $dbc->database_query("SELECT * FROM users_benchmarks WHERE benchid='".$row['id']."' AND value!=0 ORDER BY value DESC LIMIT 20"); 
		if ($dbc->database_num_rows($users)) { ?>
			<tr><td colspan=4 bgcolor="<?php echo $colors['cell_title']; ?>"><strong><?php echo (!$row['composite']?"<font color=\"".$colors['primary']."\">":""); ?><?php echo $row['name']; ?><?php echo (!$row['composite']?"</font>":''); ?></strong></td></tr>
			<tr><td><strong><u>#</u></strong></td><td><strong><u>username</u></strong></td><td><strong><u>value</u></strong></td><td>&nbsp;</td></tr>
			<?php
			$counter = 1;
			while ($usersrow = $dbc->database_fetch_assoc($users)) { 
				$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".$usersrow['userid']."'")); 
				$picture = array('gif','pjpeg','jpg','jpeg','bmp','png','psd','tif','tiff');
				$extension = "";
				for ($i = 0; $i < sizeof($picture); $i++) {
					if(file_exists(getcwd().'/files/benchmarks/'.$user['username'].'_'.$row['id'].'.'.$picture[$i])) $extension = $picture[$i];
				}
				if(!empty($extension)) {
					$holder = '<a href="files/benchmarks/'.$user['username'].'_'.$row['id'].'.'.$extension.'"><strong>screenshot</strong></a>';
				} else {
					$holder = '&nbsp;';
				} ?>
				<tr><td><?php echo $counter; ?></td><td><a href="disp_users.php?id=<?php echo $user['userid']; ?>"><?php echo $user['username']; ?></a></td><td><?php  echo ($usersrow['value']==ceil($usersrow['value'])?round($usersrow['value']):$usersrow['value']); ?></td><td><?php echo $holder; ?></td></tr>
				<?php
				$counter++;
			} ?>
			<tr><td colspan=4>&nbsp;</td></tr>
			<?php
		}
	} ?>
	</table>
	<?php
} else {
	echo 'you are not authorized to view this page.<br /><br />';
}
$x->display_bottom();