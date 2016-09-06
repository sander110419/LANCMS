<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
if ($toggle['prizes']) {
	if ($x->is_secure()) {
		if ($dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes')) > 0) {
		
			if ($_POST['lockprize']) {
				$dbc->database_query('UPDATE prizes_control SET lock_prizes=1');
			}
			if ($_POST['unlockprize']) {
				$dbc->database_query('UPDATE prizes_control SET lock_prizes=0');
			}
			
			
			$prizecontrol = $dbc->database_fetch_array($dbc->database_query('SELECT lock_prizes, draw_mode FROM prizes_control'));
			
			if (isset($_POST['rewin'])) {
				if($_POST['rewin'] > 0) $dbc->database_query("DELETE FROM prizes_unwinners WHERE userid=" . $_POST['rewin']);
			}
			
			if (!empty($_POST)) {
				$dbc->database_query('DELETE FROM prizes_display_groups');
				if (is_array($_POST['displayGroup'])) {
					foreach ($_POST['displayGroup'] AS $key => $val) {
						if ($_POST['displayGroup'][$key] == 'on') {
							if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_display_groups WHERE prizegroup=" . $key)) == 0) {
								$dbc->database_query("INSERT INTO prizes_display_groups VALUES(" . $key  .")");
							}
						} else {
							$dbc->database_query("DELETE FROM prizes_display_groups WHERE prizegroup=" . $key);
						}
					}
				}
			}
			if (isset($_POST['drawMode']) && $_POST['drawMode'] != $prizecontrol['draw_mode']) {
				$dbc->database_query("UPDATE prizes_control SET draw_mode=" . $_POST['drawMode']);
			}
			
			$x->display_top(); ?>
			<b><?php echo get_lang('administrator'); ?></b>: <?php get_lang('title_cp'); ?></b><br />
			<br />
			<?php
			$x->add_related_link(get_lang('link_admin_prizes'),'admin_prizes.php',2);
			$x->add_related_link(get_lang('link_admin_prizes_print'),'admin_prizes_print.php',2);
			$x->add_related_link(get_lang('link_admin_prize_draw'),'admin_prize_draw.php',2);
			$x->add_related_link(get_lang('link_chng_prizes'),'chng_prizes.php',1);
			$x->add_related_link(get_lang('link_disp_prizes'),'disp_prizes.php',0);
			$x->display_related_links(); 
			
			if (!$prizecontrol['lock_prizes']) { ?>
				<b><?php echo get_lang('note'); ?></b>: <?php echo get_lang('note_lock'); ?><br /><br /><?php
			}
			if ($prizecontrol['lock_prizes']) { ?>
				<b><?php echo get_lang('note'); ?></b>: <?php echo get_lang('note_locked'); ?> <?php
				$numWinners = $dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes_winners'));
				$numUnwinners = $dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes_unwinners'));
				if ($numWinners > 0 || $numUnwinners > 0) { ?>
					<?php echo get_lang('note_locked_drawn'); ?><br /><br /><?php
				} else { ?>
					<?php echo get_lang('note_locked_undrawn'); ?><br /><br />
					<form action="<?php echo get_script_name(); ?>" method="post">
					<input type="hidden" name="unlockprize" value="1">
					<input type="submit" value="<?php echo get_lang('submit_unlock'); ?>" class="formcolors">
					</form><br /><br /><?php
				} ?>
				<form action="<?php echo get_script_name(); ?>" method="post">
				<?php
                // <!-- Select Drawing Mode:<br />-->
				// MODES OF DRAWING
				// discrete mode (database value = 1): the admin draws prizes behind the scenes, then displays them to the users
				// show mode (database value = 2): the admin could, i.e. project the prize drawing onto a projector, then draw the
				//		prizes one by one for the users to see [ this mode is not yet available ]
                ?>
                <?php                
                /*
				<!-- <input type="radio" name="drawMode" value="1" <?php print ($prizecontrol['draw_mode'] == 1 || !$prizecontrol['draw_mode'] ? "CHECKED" : ""); ?>> Discrete Mode<br /> -->
				<!-- <input type="radio" name="drawMode" value="2" <?php print ($prizecontrol['draw_mode'] == 2 ? "CHECKED" : ""); ?>> Show Mode<br /> -->
                */ ?>
					<input type="hidden" name="drawMode" value="1"><br />
					<?php dotted_line(4,4); ?>
					<b><?php echo get_lang('title_winners'); ?></b>:<br />
					<?php dotted_line(4,4); ?>
					<br />
					<?php echo get_lang('note_winners'); ?><br />
					<br /><?php
					$groups = $dbc->database_query("SELECT DISTINCT(prizegroup) FROM prizes");
					unset($dispGroups);
					$displayGroups = $dbc->database_query("SELECT * FROM prizes_display_groups");
					while($dg = $dbc->database_fetch_array($displayGroups)) $dispGroups[$dg['prizegroup']] = 1;
					while($g = $dbc->database_fetch_array($groups)) {
						$prizesInGroup = $dbc->database_query("SELECT * FROM prizes WHERE prizegroup=" . $g['prizegroup'] . " AND tourneyid=0");
						$prizesInGroupDrawn = $dbc->database_num_rows($dbc->database_query("SELECT * FROM prizes_winners WHERE prizegroup=" . $g['prizegroup']));
						$accum = 0;
						while($p = $dbc->database_fetch_array($prizesInGroup)) $accum += $p['prizequantity'];
						if($prizesInGroupDrawn == $accum) { 
							get_arrow(); ?>&nbsp;<input type="checkbox" name="displayGroup[<?php print $g['prizegroup']; ?>]" <?php print ($dispGroups[$g['prizegroup']] ? "CHECKED" : ""); ?>> <?php echo get_lang('group').' '.$g['prizegroup']; ?><br /><?php
						} else { 
							get_arrow(); ?>&nbsp;<?php 
                            echo get_lang('prizegroup').$g['prizegroup'].': ';
                            sprintf( get_lang('prizes_drawn'), $prizesInGroupDrawn, $accum); ?><br />
							<?php
						}
					} ?>
				<br /><input type="submit" value="<?php echo get_lang('submit_settings'); ?>" class="formcolors">
				</form><br /><br /><?php
				if ($dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes_unwinners')) > 0) { ?>
					<?php dotted_line(4,4); ?>
					<b><?php echo get_lang('title_absentees'); ?></b>:<br />
					<?php dotted_line(4,4); ?>
					<br />
					<?php echo get_lang('note_absentees'); ?><br />
					<br />
					<form action="<?php echo get_script_name(); ?>" method="post">
					<select name="rewin" size="1">
					<option value="0">-- <?php echo get_lang('select_user'); ?> --</option><?php
					$unwinners = $dbc->database_query("SELECT prizes_unwinners.*,users.username FROM prizes_unwinners LEFT JOIN users ON users.userid = prizes_unwinners.userid ORDER BY username ASC");
					while($uw = $dbc->database_fetch_array($unwinners)) { ?>
						<option value="<?php print $uw['userid']; ?>"><?php print $uw['username']; ?></option><?php
					} ?>
					</select><input type="submit" value="<?php echo get_lang('submit_absentees'); ?>" class="formcolors"></form><?php
				}
			} else { ?>
				<b><?php echo get_lang('note'); ?></b>: <?php echo get_lang('note_unlocked'); ?><br /><br />
				<form action="<?php echo get_script_name(); ?>" method="post">
				<input type="hidden" name="lockprize" value="1">
				<b><?php echo get_lang('lockprize_sure'); ?> </b><input type="submit" value="<?php echo get_lang('submit_lockprize'); ?>" class="formcolors">
				</form><?php
			}
			
			
			$x->display_bottom();
		} else {
            $x->display_slim(get_lang('error_noprizes'),'admin_prizes.php');
        }
	} else{
        $x->display_slim(get_lang('noauth'));
    }
} else {
    $x->display_slim(get_lang('error_disabled')); 
}    
?>