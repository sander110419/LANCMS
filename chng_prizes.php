<?php
include "include/_universal.php";
$x = new universal("enter prize drawings","prizes",1);
if($x->is_secure()&&$toggle["prizes"]) { 
	$prizeControl = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM prizes_control LIMIT 1"));
	$x->display_top();
	if(empty($_POST)||$prizeControl['lock_prizes']) { ?>
		<b>enter prize drawings</b>: <br />
		<br />
		<?php
		$x->add_related_link("prize database","admin_prizes.php",2);
		$x->add_related_link("prize control panel","admin_prize_control.php",2);
		$x->add_related_link("print off prize slips for drawing.","admin_prizes_print.php",2);
		$x->add_related_link("draw prizes interactively.","admin_prize_draw.php",2);
		$x->add_related_link("view all prizes.","disp_prizes.php",0);
		$x->display_related_links(); ?>
		<br />
		<?php
		if($prizeControl['lock_prizes']) { ?>
			<b>note</b>: prize registration has been locked because prizes are in the process of being drawn.  do you want to see if <a href="disp_prizes.php">you've won anything?</a><br />
			<br /><?php
		}
		begitem("enter prize drawings");
		if(!$prizeControl['lock_prizes']) { ?>
			<b>note</b>: there are advantages to both methods of prize registration.  if you enter to win every prize available, you will have a greater chance at winning something.  however, if you don't want to waste your chance at winning on a lesser prize that you don't really want, only enter into the prize registrations that you wish to win.  this will guarantee that if you win, you'll win something you want.<br />
			<br />
			<b>note</b>: you can only win one prize per group, unless every eligible user has won a prize already from that group.  if you're signing up for prizes individually, there are some nuances to this, so please take some time to think it out.  your choices may screw you out of free stuff if you're not careful!<br />
			<br />
			<?php
		} 
		$getall = $dbc->database_num_rows($dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND getall='1'"));
		$either = $dbc->database_num_rows($dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."'")); ?>
		<?php if(!$prizeControl['lock_prizes']) { ?><form action="<?php echo get_script_name(); ?>" method="POST"><?php } ?>
		<table border=0 cellpadding=3 cellspacing=0 width="100%">
		<tr><td width="28"><?php if(!$prizeControl['lock_prizes']) { ?><input type="radio" name="allprizes" value="0" class="radio"<?php echo (!$either?" checked":""); ?>><?php } else {
				if(!$either) { ?>
					<font style="color: <?php echo $colors['primary']; ?>; font-size: 14px; font-weight: bolder; border: medium solid <?php echo $colors['primary']; ?>; padding: 0px 4px 0px 4px">X</font><?php
				} else { echo "&nbsp;"; }
			} ?></td>
			<td colspan=2>
				<?php dotted_line(8,8); ?>
				<b>don't enter any prize drawings.</b><br />
				<?php dotted_line(8,8); ?>
			</td></tr>
		<tr><td colspan=3>
		<br />
		<div align="center"><font color="<?php echo $colors["primary"]; ?>">-<b>or</b>-</font></div>
		<br />
		</td></tr>
		<tr><td width="28"><?php if(!$prizeControl['lock_prizes']) { ?><input type="radio" name="allprizes" value="1" class="radio"<?php echo ($getall?" checked":""); ?>><?php } else { 
				if($getall) { ?>
					<font style="color: <?php echo $colors['primary']; ?>; font-size: 14px; font-weight: bolder; border: medium solid <?php echo $colors['primary']; ?>; padding: 0px 4px 0px 4px">X</font><?php
				} else { echo "&nbsp;"; }
			} ?></td>
			<td colspan=2>
				<?php dotted_line(8,8); ?>
				<b>enter to win every prize available.</b><br />
				<?php dotted_line(8,8); ?>
			</td></tr>
		<tr><td colspan=3>
		<br />
		<div align="center"><font color="<?php echo $colors["primary"]; ?>">-<b>or</b>-</font></div>
		<br />
		</td></tr>
		<tr><td width="28"><?php if(!$prizeControl['lock_prizes']) { ?><input type="radio" name="allprizes" value="2" class="radio"<?php echo ($getall==0&&$either!=0?" checked":""); ?>><?php } else { 
				if($getall==0&&$either!=0) { ?>
					<font style="color: <?php echo $colors['primary']; ?>; font-size: 14px; font-weight: bolder; border: medium solid <?php echo $colors['primary']; ?>; padding: 0px 4px 0px 4px">X</font><?php
				} else { echo "&nbsp;"; }
			} ?></td>
			<td colspan=2>
				<?php dotted_line(8,8); ?>
				<b>enter drawings individually:</b><br />
				<?php dotted_line(8,8); ?>
			</td></tr>
		<tr><td colspan=3>&nbsp;</td></tr>
		<?php 
		$counter = 0;
		$data = $dbc->database_query("SELECT * from prizes WHERE tourneyid=0 AND tourneyplace=0");
		while($row = $dbc->database_fetch_assoc($data)) {
			$temp = $dbc->database_num_rows($dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND prizeid='".$row["prizeid"]."'")); ?>
			<tr<?php echo ($counter%2==1?" bgcolor=\"".$colors["cell_alternate"]."\"":""); ?>>
			<td valign="top"><a name="<?php echo $row['prizeid']; ?>"></a><?php if(!$prizeControl['lock_prizes']) { ?><input type="checkbox" name="<?php echo $row["prizeid"]; ?>" value="1" class="radio"<?php echo ($temp?" checked":""); ?>><?php } else {
				if($temp) { ?>
					<font style="color: <?php echo $colors['secondary']; ?>; font-size: 14px; font-weight: bolder; border: medium solid <?php echo $colors['secondary']; ?>; padding: 0px 4px 0px 4px">X</font><?php
				} else { echo "&nbsp;"; }
			} ?></td>
			<td width="218"><div align="center">
				<?php 
				if(!empty($row["prizepicture"])) { ?>
					<img src="<?php echo $row['prizepicture']; ?>" height="163" border="0" alt="<?php echo $row['prizename']; ?>">
					<?php
				} else {
					spacer(218,1,1); 
					spacer(1,50,0,'absmiddle'); ?><font class="smm" color="<?php echo $colors['blended_text']; ?>">picture not available.</font>
					<?php
				} ?></div>
			</td>
			<td valign="top"><a href="disp_prizes.php#<?php echo $row['prizeid']; ?>"><?php echo $row["prizename"]; ?></a><br />
				<br />
				<table border="0" class="sm">
					<tr><td>quantity: </td><td><?php echo $row["prizequantity"]; ?></td></tr>
					<tr><td>value: </td><td><?php echo ($row["prizevalue"]!=0?(MONEY_PREFIX ? MONEY_SYMBOL : '').$row["prizevalue"].(!MONEY_PREFIX ? MONEY_SYMBOL : ''):"&nbsp;"); ?></td></tr>
					<tr><td>group: </td><td><?php echo $row['prizegroup']; ?></td></tr>
				</table>
				<br />
				<br />
			</td>
			</tr>
			<?php 
			$counter++;
		} ?>
		</table>
		<?php
		if(!$prizeControl['lock_prizes']) { ?>
			<br />
			<div align="right"><input type="submit" value="enter drawings" style="width: 160px" class="formcolors"></div>
			</form>
			<?php
		} ?>
		<br />
		<?php
		enditem("enter prize drawings");
	} elseif(!$prizeControl['lockprizes']) { 
		include "include/cl_validation.php";
		$valid = new validate();
		
		$valid->is_empty("allprizes","you must choose one method of registration, either all prizes or individually.");
		if($valid->get_value("allprizes")==2&&sizeof($_POST)==1) {
			$valid->set_value("allprizes",0);
		}
		
		if(!$valid->is_error()) {
			if($valid->get_value("allprizes")==1) {
				$temp = $dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."'");
				if($dbc->database_num_rows($temp)) {
					if($dbc->database_query("DELETE from prizes_votes WHERE userid='".$_COOKIE["userid"]."'")&&$dbc->database_query("INSERT into prizes_votes (userid, getall) VALUES ('".$_COOKIE["userid"]."','1')")) {
						echo "your prize registration was successful.<br /><br /> &gt; <a href=\"chng_prizes.php\">change your prize registration</a>.<br /><br />";
					} else {
						echo "there was an error and you prize registration was _not_ sent through.";
					}
				} else {
					if($dbc->database_query("INSERT into prizes_votes (userid, getall) VALUES ('".$_COOKIE["userid"]."','1')")) {
						echo "your prize registration was successful.<br /><br /> &gt; <a href=\"chng_prizes.php\">change your prize registration</a>.<br /><br />";
					} else {
						echo "there was an error and you prize registration was _not_ sent through.";
					}
				}
			
			} elseif($valid->get_value("allprizes")==2) {
				$keys = array_keys($_POST);
				$allgood = true;
				for($i=0;$i<sizeof($keys);$i++) {
					if($keys[$i]!="allprizes"&&$valid->get_value($keys[$i])) {
						$temp = $dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND prizeid='".$keys[$i]."'");
						if(!$dbc->database_num_rows($temp)) {
							if($dbc->database_query("INSERT into prizes_votes (userid, prizeid) VALUES ('".$_COOKIE["userid"]."','".$keys[$i]."');")) {
							} else {
								$allgood = false;
							}
						}
					}
				}
				$temp = $dbc->database_query("SELECT * from prizes");
				while($row = $dbc->database_fetch_array($temp)) {
					$temp2 = $dbc->database_query("SELECT * from prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND prizeid='".$row["prizeid"]."'");
					if(!array_key_exists($row["prizeid"],$_POST)&&$dbc->database_num_rows($temp2)) {
						if($dbc->database_query("DELETE FROM prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND prizeid='".$row["prizeid"]."'")) {
						} else {
							$allgood = false;
						}					
					}
				}
				if(!$dbc->database_query("DELETE from prizes_votes WHERE userid='".$_COOKIE["userid"]."' AND getall='1'")) {
					$allgood = false;
				}		
				if($allgood) {
					echo "your prize registration/update was successful.<br /><br /> &gt; <a href=\"chng_prizes.php\">change your prize registration</a>.<br /><br />";
				} else {
					echo "there was an error and you prize registration was _not_ sent through.";
				}
			} else {
				if($dbc->database_query("DELETE from prizes_votes WHERE userid='".$_COOKIE["userid"]."'")) {
					echo "your prize un-registration was successful.<br /><br /> &gt; <a href=\"chng_prizes.php\">change your prize un-registration</a>.<br /><br />";
				} else {
					echo "there was an error and you prize un-registration was _not_ sent through.";
				}
			}
		} else {
			$valid->display_errors();
		}
	}
	$x->display_bottom();
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>