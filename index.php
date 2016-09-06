<?php
require_once 'include/_universal.php';
$x = new universal('','',0);
if ($x->is_secure()) { 
	if(ALP_TOURNAMENT_MODE) {
		header('Location: tournaments.php');
	} else {
		$x->display_top(0);
		function index_modules($side)
	    {
			global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims, $dbc, $result;
			if ($side == 'right') { 
				?><td width="<?php echo $container['horizontalpadding']; ?>"><img src="img/pxt.gif" width="<?php echo $container['horizontalpadding']; ?>" height="1" border="0" alt="" /></td><?php
			} ?>
			<td width="<?php echo $container['indexmodule']; ?>" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="<?php echo $container['indexmodule']; ?>">
				<?php
				$result = $dbc->query("SELECT * FROM `modules` WHERE `ordernum` != '0' ORDER BY `ordernum` ASC");
				$half_modules = array();
				while($row = $result -> fetchRow()) {
					$req = $row['required'];
					$file = $row['file'];
					if($toggle[$req] || $req == NULL)
						$half_modules[] = $file;
				}
				foreach ($half_modules as $val) { ?>
					<tr><td valign="top">
					<?php 
					start_module();
					require_once 'include/modules/'.$val;
					end_module(); ?>
					</td></tr>
					<?php
				} ?>
				</table>	
			</td>
			<?php
			if ($side == 'left') { 
				?><td width="<?php echo $container['horizontalpadding']; ?>"><img src="img/pxt.gif" width="<?php echo $container['horizontalpadding']; ?>" height="1" border="0" alt="" /></td><?php
			}
		} ?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		<?php 
		if ($container['index_modules'] == 'left') index_modules($container['index_modules']);
		
		$half_modules = array();
		$data = $dbc->database_query("SELECT caffeine_id, COUNT(1) AS count FROM caffeine WHERE userid='".$_COOKIE['userid']."' GROUP BY caffeine_id ORDER BY count DESC LIMIT 10");
		if (current_security_level() >= 1 && $dbc->database_num_rows($data)&&$toggle['caffeine']) $half_modules[] = 'mod_quickcaffeine.php'; 
		if ($toggle['gamerequests']) $half_modules[] = 'mod_gamerequests.php';
		if ($toggle['servers']) $half_modules[] = 'mod_servers.php';
		// to add more modules to the middle on the index page, just push the module file onto the $half_modules array. 
		?>
		<td width="100%" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>
			<?php
			$query = "SELECT * FROM news WHERE hide_item=0 ORDER BY itemtime DESC";
			if (empty($_GET['all'])) $query .= ' LIMIT 5';
			$beh = $dbc->database_query($query);
			if ($dbc->database_num_rows($beh)) {
				start_module();
				while($behrow = $dbc->database_fetch_assoc($beh)) {
					$user = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$behrow['userid']."'")); ?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><font color="<?php echo $colors['primary']; ?>"><strong><?php echo $behrow['headline']; ?></strong><br /></font></td>
						<td><div align="right"><font class="sm"><font color="<?php echo $colors['blended_text']; ?>">[<strong><?php echo $user['username']; ?></strong>]	[<?php echo (!empty($behrow['itemtime']) ? disp_datetime(strtotime($behrow['itemtime'])) : 'Invalid Date'); ?>]</font><?php adminlink('admin_news.php?mod=1&q='.$behrow['itemid']); ?><br /></font></div></td>
					</tr>
					<tr><td colspan="2">
					<?php
					$article = $behrow['news_article'];
					$article = str_replace("&lt;","<",$article);
					$article = str_replace("&gt;",">",$article);
					$article = strip_tags($article,'<a><strong><b><i><u><font><img>');
					?>
					<?php echo nl2br($article); ?>
					</td></tr>
					</table>
					<img src="img/pxt.gif" width="1" height="12" border="0" alt="" /><br />
					<?php dotted_line(4,4); ?>
					<img src="img/pxt.gif" width="1" height="12" border="0" alt="" /><br />
					<?php
				} ?>
				<?php
				if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM news WHERE hide_item=0 ORDER BY itemtime DESC"))>5) { ?>
					<div align="right"><font class="sm">[<a href="index.php<?php echo (empty($_GET['all'])?'?all=1':''); ?>"><strong><?php echo (!empty($_GET['all'])?'hide old':'view all'); ?> news</strong></a>]</font></div>
					<?php
				}
				end_module();
			} else {
				$l_counter = 1;
				start_module(); ?>
				<font color="<?php echo $colors['primary']; ?>"><strong>Welcome to <?php echo $lan['name']; ?></strong><br /></font>
				<?php
				ob_start(); ?>
				This is the intranet software we're going to be using to coordinate the flow of information.  We have a variety of things going on here, but here are a few hints for the website:<br />
				<br />
				<?php
				if($toggle['prizes']) { 
					echo $l_counter; 
					?>. Make sure you <a href="chng_prizes.php">register for prizes</a>.  Read the guide on the page to decide which method of registration you want to use.<br />
					<br /><?php
					$l_counter++;
				}
				if($toggle['hlsw']) {
					echo $l_counter;
					?>. If you see this icon: <img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="HLSW"> next to a server ip, you can click on it to launch HLSW to connect to the server.  HLSW is a multi-game server connection tool.  To join a game using HLSW, click the icon, and it will bring up the IP address in the HLSW window.  Hit enter, right click on the server in the server list and click on Connect.  It will make it much easier to join tournament servers.<br />
					<br /><?php
					$l_counter++;
				}
				echo $l_counter;
				?>. You're free to participate in the other events we have available:<br />
				<?php
				if($toggle['caffeine'] || $toggle['marath'] || $toggle['benchmarks']) {
						spacer(1,4,1);
						if($toggle['caffeine']) { 
							spacer(16); get_arrow(); ?>&nbsp;<a href="caffeine.php">Caffeine Log</a><br /><?php
						}
						if($toggle['marath']) {
							spacer(16); get_arrow(); ?>&nbsp;<a href="themarathon.php">The Marathon Global Tournament</a><br /><?php
						}
						spacer(16); get_arrow(); ?>&nbsp;<a href="tournaments.php">Tournaments</a><br /><?php
						if($toggle['benchmarks']) {
							spacer(16); get_arrow(); ?>&nbsp;<a href="benchmarks.php">Benchmarking Competition</a><br /><?php
						}
					$l_counter++;
				}
				$default_news = ob_get_contents();
				ob_end_clean();
				echo $default_news;
				if(current_security_level()>=2) {
					//$default_news = str_replace('\'','\\\'',$default_news);
					//$default_news = str_replace("<br />","\n",$default_news);
					$default_news = htmlspecialchars($default_news);
					$default_news = str_replace("&lt;br /&gt;","<br />",$default_news);
					//$default_news = nl2br($default_news);
					?>
					<br />
					<strong>source</strong>: (copy this into the <a href="admin_news.php">administrator news page</a> to save this post)<br />
					<div style="border: 1px dashed <?php echo $colors['blended_text']; ?>; margin: 4px 4px 4px 4px; padding: 4px 4px 4px 4px; font: 11px 'Courier New';"><?php echo $default_news; ?></span>
					<?php
				}
				end_module();
			} ?>
			</td></tr>
			<?php
			foreach ($half_modules as $val) { ?>
				<tr><td valign="top">
				<?php 
				start_module();
				include 'include/modules/'.$val;
				end_module(); ?>
				</td></tr>
				<?php
			} ?>
			</table>
		</td>
		<?php 
		if ($container['index_modules'] =='right') index_modules($container['index_modules']); ?>
		</tr>
		</table>
		<?php
		$x->display_bottom(0);
	}
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
