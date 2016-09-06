<?php
include 'include/_universal.php';
include_once 'include/cl_display.php';
include_once 'include/cl_pager.php';
$pager = new pager();
if ($master['useskinforcaffeine']) {
	$includefile = array('include/_top.php','include/_bot.php');
	$primary = $colors["cell_background"];
} else {
	$includefile = array('include/_caffeine_top.php','include/_caffeine_bot.php');
	$primary = '#00ff00';
}
function begtable()
{
	global $primary;
	echo "<table border=0 cellspacing=0 cellpadding=6 style=\"font-size: 13px; border: thin solid ".$primary.";\" width=100%><tr><td>";
}
function endtable()
{
	echo '</td></tr></table>';
}

if ($toggle['caffeine']) { 
    
	function caffeine_menu()
    {
		global $colors, $userinfo, $master, $primary; 
        if ($master['useskinforcaffeine']) { 
            ?><font color="<?php echo $colors['primary']; ?>"><b>caffeine log</b></font>&nbsp;<font class="sm"><?php 
        } ?>&nbsp;/<a href="<?php echo get_script_name(); ?>?action=information"><b>information</b></a>&nbsp;/<a href="<?php echo get_script_name(); ?>"><b>standings</b></a><?php 
        if(current_security_level()>=1) { 
            ?>&nbsp;/<a href="<?php echo get_script_name(); ?>?action=add"><b>add</b></a>&nbsp;/<a href="<?php echo get_script_name(); ?>?action=user&id=<?php echo $userinfo["userid"]; ?>"><b>delete</b></a><?php 
        } ?><br /><?php 
        if($master["useskinforcaffeine"]) { 
            echo "</font>"; 
        } ?>
		<br />
		<?php
	}
    
	if (empty($_POST)) {
		if (empty($_GET['action']) || ($_GET['action'] == 'user' && empty($_GET['id']) )) {
			require_once $includefile[0];

			if($master['useskinforcaffeine']) {
				start_module();
			}
			caffeine_menu();
			if (date('U')-date('U',$end)>0) {
				$str = get_time_diff(date('U',$start),date('U',$end),1); 
			} else {
				$str = get_time_diff(date('U',$start),date('U'),1);
			} ?>
			<font class="sm"><b>duration</b>: <?php echo $str; ?><br /><br /></font>
			<?php
			$temp = $dbc->database_query('SELECT caffeine_id, COUNT(1) AS count FROM caffeine GROUP BY caffeine_id ORDER BY count DESC LIMIT 10');
			$numbers = array('','one','two','three','four','five','six','seven','eight','nine');
			if($dbc->database_num_rows($temp)) {
				if($dbc->database_num_rows($temp)==1) {
					$holder = '';
					$ext = '';
				} elseif($dbc->database_num_rows($temp)<10) {
					$holder = $numbers[$dbc->database_num_rows($temp)];
					$ext = 's';
				} else {
					$holder = 'ten';
					$ext = 's';
				}
				echo '<font size=1>top '.$holder.' item'.$ext.':<br />';
			}
			$counter = 0;
			while($temprow = $dbc->database_fetch_assoc($temp)) {
				$data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM caffeine_items WHERE id=".$temprow['caffeine_id']));
				get_arrow();
				echo '&nbsp;<b>'.$data['name'].'</b>';
				$counter++;
				if($counter!=$dbc->database_num_rows($temp)) { 
					//echo ',&nbsp;'; 
					echo '<br />';
				}
			}
			if($dbc->database_num_rows($temp)) { echo '<br /></font>'; }
			?>
			<br />
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><form action="<?php echo get_script_name(); ?>" method="GET">
						<?php
						$URL_handler->get_hidden_form_elements(array('per'));
						?>
						<font class="sm">
						per page: <select name="per" style="font: 10px Verdana;"><?php
						if(empty($_GET['per'])) $_GET['per'] = 50;
						$per_page = array(	10 	=> 10,
											25 	=> 25,
											50 	=> 50,
											100 => 100);
						foreach($per_page as $key => $val) { 
								?><option value="<?php echo $key; ?>"<?php 
								echo (!empty($_GET['per'])&&$_GET['per']==$key?" selected":"");
								?>><?php echo $val; ?></option><?php
						} ?>
						</select>
						</font>
						<input type="submit" value="go" style="font: 10px Verdana;" class="formcolors" />
						</form>
					</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>
						<?php
						$num_rows = $dbc->database_num_rows($dbc->database_query('SELECT * FROM users where caffeine_mg!=0 ORDER BY caffeine_mg DESC'));
						echo $pager->display_numeric_links($URL_handler,$num_rows); ?>
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="6" style="border: thin solid <?php echo $primary; ?>; font-size: 13px;">
			<tr><td width="20">#</td><td><b>username</b></td><td>total (mg)</td><td>avg (mg) / hour</td><td>last entry</td><td><b>log</b></td></tr>
			<?php 
			$query = 'SELECT * FROM users where caffeine_mg!=0 ORDER BY caffeine_mg DESC';
				$query .= ' LIMIT ';
				$query .= (!empty($_GET[$pager->get_GET_start_var()]) ? $_GET[$pager->get_GET_start_var()] : 0);
				$query .= ',';
				$query .= (!empty($_GET[$pager->get_GET_per_var()]) && $_GET[$pager->get_GET_per_var()] <= 100 ? $_GET[$pager->get_GET_per_var()] : 50);

			$data = $dbc->database_query($query);
			$counter = (!empty($_GET[$pager->get_GET_start_var()]) ? $_GET[$pager->get_GET_start_var()]:0) + 1;
			while($row = $dbc->database_fetch_array($data)) { 
				if((date('U')-date('U',$end))>0) {
					$totaltime = date('U',$end) - date('U',strtotime($row['date_of_arrival']));
				} else {
					$totaltime = date('U') - date('U',strtotime($row['date_of_arrival']));
				}
				$temp = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM caffeine WHERE userid='".$row['userid']."' ORDER BY caffeine_date DESC"));
				if(round($row['caffeine_mg']/($totaltime/3600),2)>37.5) {
					$holder = '#ff0000';
				} elseif(round($row['caffeine_mg']/($totaltime/3600),2)>25) {
					$holder = '#ff9900';
				} elseif(round($row['caffeine_mg']/($totaltime/3600),2)>12.5) {
					$holder = '#ffff00';
				} else { 
					if($master['useskinforcaffeine']) $holder = $colors['text'];
					else $holder = '#009900';
				} ?>
				<tr style="color: <?php echo $holder; ?>; font-weight: bold"><td><?php echo $counter; ?></td><td><a href="disp_users.php?id=<?php echo $row['userid']; ?>" style="color: <?php echo $holder; ?>"><?php echo $row['username']; ?></a></td><td><?php echo $row['caffeine_mg']; ?><td><?php echo round($row['caffeine_mg']/($totaltime/3600),2); ?></td><td><?php echo (!empty($temp['caffeine_date']) ? disp_datetime(strtotime($temp['caffeine_date'])):''); ?></td><td><a href="<?php echo get_script_name(); ?>?action=user&id=<?php echo $row["userid"]; ?>"><b>log</b></a></td></tr>
				<?php
				$counter++;
			}
			if ($dbc->database_num_rows($data) == 0) {
				echo '<tr><td colspan="5">&nbsp;no users participating yet.</td></tr>';
			} ?>
			</table>
			<br />
			<table border=0 cellpadding=0 cellspacing=0>
			<tr><td bgcolor="#ffff00"><img src="img/pxt.gif" width="150" height="1" border="0" alt="" /></td><td>&nbsp;<font color="#ffff00" size=1>user is over the recommended allowance of caffeine (300mg/day).  ease up buddy.</font><br /></td></tr>
			<tr><td bgcolor="#ff9900"><img src="img/pxt.gif" width="150" height="1" border="0" alt="" /></td><td>&nbsp;<font color="#ff9900" size=1>user is over twice the recommended allowance of caffeine.  stop intake immediately.</font><br /></td></tr>
			<tr><td bgcolor="#ff0000"><img src="img/pxt.gif" width="150" height="1" border="0" alt="" /></td><td>&nbsp;<font color="#ff0000" size=1>user requires medical assistance.</font><br /></td></tr>
			</table>
			<font size=1><br /><b>note: avg(mg)/hour is calculated against the time you registered for your account.</b></font><br />
			<?php
			if ($master['useskinforcaffeine']) {
				end_module();
			}
			require_once $includefile[1];
		} elseif (!empty($_GET['action']) && $_GET['action'] == 'information') {
			require_once $includefile[0];
			if ($master['useskinforcaffeine']) {
				start_module();
			}
			caffeine_menu();
			begtable(); ?>
			welcome to your caffeine log.  here are the rules: every time you drink a beverage that contains some caffeine, 
			go to the add item page and submit it into the database under your user name.  you're on the honor system so play nice.  
			you are the only one that will know when you've had enough!  ONLY log your information passively.  DO NOT ACTIVELY CONSUME
			CAFFEINE TO INCREASE YOUR LOG STATISTICS.  active consumption of caffeine in order to have the highest numbers or
			show off will result in disqualification and removal from the lan.  caffeine is a very serious drug if you have too 
			much of it.<br /><br />
			<?php
			endtable();
			if ($master['useskinforcaffeine']) {
				end_module();
			}
			include $includefile[1];
		} elseif (!empty($_GET['action']) && $_GET['action'] == 'add') {
			require_once $includefile[0];
			if ($master['useskinforcaffeine']) {
				start_module();
			}
			caffeine_menu();
			begtable();
			if(current_security_level()>=1) {
				?>
				<b>add item</b><br /><br />
				<table border=0 cellpadding=2 cellspacing=0 width="100%" style="font-size: 13px">
				<form action="<?php echo get_script_name(); ?>" method="POST" name="caffeine">
				<input type="hidden" name="type" value="add">
				<tr><td valign="top">username: </td><td><b><?php echo $userinfo['username']; ?></b></td></tr>
				<tr><td valign="top">item: </td><td><select name="id"><option value=""></option>
				<?php
				$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT caffeine_id, COUNT(1) AS count FROM caffeine WHERE userid='".(int)$_COOKIE['userid']."' GROUP BY caffeine_id ORDER BY count DESC LIMIT 1"));
				$items = $dbc->database_query("SELECT caffeine_items.*,caffeine_types.name AS bah FROM caffeine_items LEFT JOIN caffeine_types ON caffeine_items.ttype=caffeine_types.id ORDER BY caffeine_items.ttype,caffeine_items.name");
				while($row = $dbc->database_fetch_assoc($items)) { ?>
					<option value="<?php echo $row['id']; ?>"<?php echo ($row['id']==$temp['caffeine_id']?' selected':''); ?>><?php echo $row['bah']; ?> :: <?php echo $row['name']; ?></option>
				<?php	
				} ?>
				</select><br /></td></tr>
				<tr><td valign="top">size: </td><td>
					<table border="0" class="sm" width="260"><tr><td valign="top" width="80"><input type="text" value="12" size="5" maxlength="3" name="oz"> </td><td>
						<font size="2"><input type="radio" name="oztype" value="0" checked class="radio"> ounces <input type="radio" value="1" name="oztype" class="radio"> liters<br /></font>
						<br />
						<b>regular soda cans are 12 oz.<br />
						red bull is 8 oz.<br />
						bawls is 10 oz.<br /></b>
					</td></tr></table>
				</td></tr>
				<tr><td valign="top">quantity: </td><td>
					<table border="0" class="sm" width="260"><tr><td valign="top" width="80">
						<select name="quantity"><option value=""></option>
						<?php
						for($i=1;$i<=3;$i++) { ?>
							<option value="<?php echo $i; ?>"<?php echo ($i==1?' selected':''); ?>><?php echo $i; ?></option>
						<?php	
						} ?>
						</select>
					</td><td>
						to ensure fair play, logs of multiple 3 quantity entries within any 15 minute time frame will be deleted.<br />
						<br />
						for example, if you log a 3 quantity entry now, and then log another 3 quantity entry in 10 minutes, your second entry will be deleted.<br />
					</td></tr></table>					
				</td></tr>
				</table>
				<div align="right"><input type="submit" value="add item" style="width: 120px"></div>
				</form>
				<?php
			} else {
				echo 'you are not authorized to view this page.';
			}
			endtable();
			if ($master['useskinforcaffeine']) {
				end_module();
			}
			include $includefile[1];
		} elseif(!empty($_GET['action']) && $_GET['action']=='user' && !empty($_GET['id'])) {
			include $includefile[0];
			if($master['useskinforcaffeine']) {
				start_module();
			}
			caffeine_menu();
			$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".(int)$_GET['id']."'"));
			?>
			<table width="100%" border=0 cellpadding=0 cellspacing=0><tr><td>&lt;<b><?php echo $user['username']; ?></b>'s user log&gt;<br /></td><td><div align="right">[<a href="<?php echo get_script_name(); ?>">back</a>]</div></td></tr></table>
			<br />
			<?php
			$temp = $dbc->database_query("SELECT caffeine_id, COUNT(1) AS count FROM caffeine WHERE userid='".$user['userid']."' GROUP BY caffeine_id ORDER BY count DESC LIMIT 3");
			$numbers = array('','one','two','three','four','five','six','seven','eight','nine');
			if($dbc->database_num_rows($temp)) {
				if($dbc->database_num_rows($temp)==1) {
					$holder = '';
					$ext = '';
				} elseif($dbc->database_num_rows($temp)<10) {
					$holder = $numbers[$dbc->database_num_rows($temp)];
					$ext = 's';
				} else {
					$holder = 'ten';
					$ext = 's';
				}
				echo '<font size=1>top '.$holder.' item'.$ext.' for this user:<br />';
			}
			while($temprow = $dbc->database_fetch_assoc($temp)) {
				$data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM caffeine_items WHERE id='".$temprow['caffeine_id']."'"));
				echo "<b>".$data['name']."</b><br />";
			}
			if($dbc->database_num_rows($temp)) { echo '<br /></font>'; }
			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="6" style="border: thin solid <?php echo $primary; ?>; font-size: 13px;">
			<tr><td width="20">#</td><td><b>type</b></td><td>time and date</td><td>amount (ounces)</td><td>amount (liters)</td><td>quantity</td><td>total (mg)</td><?php echo (current_security_level()>=1&&($_GET['id']==$userinfo['userid']||current_security_level()>=2)?'<td>&nbsp;</td>':''); ?></tr>
	<?php 
			$data = $dbc->database_query("SELECT * FROM caffeine where userid='".(int)$_GET['id']."' ORDER BY caffeine_date");
			$counter = 1;
			while($row = $dbc->database_fetch_assoc($data)) { 
				$item = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM caffeine_items WHERE id='".$row['caffeine_id']."'")); ?>
				<tr><td><?php echo $counter; ?></td><td><?php echo $item['name']; ?></td><td><?php echo disp_datetime(strtotime($row['caffeine_date'])); ?><td><?php echo $row['caffeine_oz']; ?> oz</td><td><?php echo round($row['caffeine_oz']*.029573529,2); ?> L</td><td><?php echo $row['caffeine_qty']; ?></td><td><?php echo $row['caffeine_total']; ?></td>
	<?php
				if(current_security_level()>=1&&($_GET['id']==$_COOKIE['userid']||current_security_level()>=2)) { ?>
					<td width="100">
					<form action="<?php echo get_script_name(); ?>" method="post">
					<input type="hidden" name="type" value="del">
					<input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
					<input type="submit" value="delete?" style="width: 100px">
					</form>
					</td></tr><?php
				}
				$counter++;
			} ?>
			</table>
			<br />
			<?php
			if ($master['useskinforcaffeine']) {
				end_module();
			}
			require_once $includefile[1];
		} else {
			require_once $includefile[0];
			if ($master['useskinforcaffeine']) {
				start_module();
			}
			caffeine_menu();
			begtable(); ?>
			cannot compute that action.  please try again or go to the <a href="<?php echo get_script_name(); ?>">caffeine log home page</a>
			and try to find your way from there.<br /><br />
			<?php
			endtable();
			if ($master['useskinforcaffeine']) {
				end_module();
			}
			require_once $includefile[1];
		}
	} else {
		require_once $includefile[0];
		if ($master['useskinforcaffeine']) {
			start_module();
		}
		caffeine_menu();
		begtable();
		if (current_security_level() >= 1) {
			require_once 'include/cl_validation.php';
			$valid = new validate();
			
			if($valid->get_value('type')=='add'||$valid->get_value('type')=='quickadd') {
				$valid->is_empty('id','you have to specify a caffeine item.');
				$valid->is_empty('oz','you have to specify an ounce amount.');
				if($valid->get_value('quantity')>3||$valid->get_value('quantity') < 1) {
					if($valid->get_value('quantity')==0) 
					{
						$valid->is_empty('quantity','you must specify a quantity.');
					} else {
						$valid->add_error('you must specifiy a valid quantity.');
					}
				}
				
				if(!$valid->is_error()) {
					$item = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM caffeine_items WHERE id='".$valid->get_value('id')."'"));
					if($valid->get_value('oztype')==1) {
						$oz = $valid->get_value('oz')*33.8140227;
					} else {
						$oz = $valid->get_value('oz');
					}
					if($valid->get_value("quantity")<3||!$dbc->database_num_rows($dbc->database_query("SELECT * FROM caffeine WHERE userid='".$userinfo['userid']."' AND caffeine_qty=3 AND (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(caffeine_date))<3600"))) {
						if($dbc->database_query("INSERT INTO caffeine (userid,caffeine_date,caffeine_id,caffeine_oz,caffeine_mult,caffeine_qty,caffeine_total) VALUES ('".$userinfo['userid']."','".date('Y-m-d H:i:s')."', '".$valid->get_value('id')."','".$oz."','".$item['caffeine_permg']."','".$valid->get_value('quantity')."','".($oz*$item['caffeine_permg']*$valid->get_value('quantity'))."')")) 
						{
							echo 'your item was successfully added.<br /><br /> &gt; ';
							if($valid->get_value('type')=='add') 
							{
								echo '<a href="'.get_script_name().'?action=add">add more caffeine';
							} else {
								echo '<a href="index.php">back to the home page';
							}
							echo '</a>.<br /><br />';
							$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(caffeine_total) AS total FROM caffeine WHERE userid='".(int)$_COOKIE['userid']."'"));
							if($dbc->database_query("UPDATE users SET caffeine_mg='".$temp["total"]."' WHERE userid='".(int)$_COOKIE['userid']."'")) 
							{
								echo 'your caffeine count was successfully updated.<br /><br />';
							} else {
								echo 'your caffeine count was not updated.  there was an error.  we\'ll try again when you add another caffeine item.  don\'t worry, your current item was not lost.<br /><br />';
							}
						} else {
							echo 'there was an error adding your caffeine log item.  please go back and try again.';
						}
					} else {
						echo '&nbsp;<b>error!</b> you already have a entry of 3 quantity in the last 15 minutes.  you\'ll have to wait to log another entry.<br /><br />';
					}
				} else {
					$valid->display_errors();
				}
			} elseif($valid->get_value('type')=='del') {
				$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE userid='".(int)$_COOKIE['userid']."'"));
				$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM caffeine WHERE id='".$valid->get_value('id')."'"));
				if($user["userid"]==$_COOKIE["userid"]||current_security_level()>=2) {
					if($dbc->database_query("DELETE FROM caffeine WHERE id='".$valid->get_value('id')."'")) {
						echo "your log entry was successfully deleted.<br /><br /> &gt; <a href=\"".get_script_name()."?action=user&id=".$user['userid']."\">delete another entry</a>.<br /><br />";
						$caffeine = 0;
						$temp = $dbc->database_query("SELECT * FROM caffeine WHERE userid='".$user['userid']."'");
						while($temprow = $dbc->database_fetch_array($temp)) {
							$caffeine += $temprow['caffeine_total'];
						}
						if($dbc->database_query("UPDATE users SET caffeine_mg='".$caffeine."' WHERE userid='".$user['userid']."'")) {
							echo 'your caffeine count was successfully updated.<br /><br />';
						} else {
							echo 'your caffeine count was not updated.  there was an error.  we\'ll try again when you add another caffeine item.<br /><br />';
						}
					} 
				} else {
					echo 'you are not authorized to view this page.';
				}
			} else {
				echo 'invalid type.  please go back and try again.';
			}
		} else {
			echo 'you are not authorized to view this page.';
		}
		endtable();
		if ($master['useskinforcaffeine']) {
			end_module();
		}
		require_once $includefile[1];
	}
} else {
	require_once $includefile[0];
	begtable();?>
	the administrator has disabled this feature.<br /><br />
	<?php
	endtable();
	require_once $includefile[1];
} ?>