<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),'',2);
if ($x->is_secure()) { ?>
	<html><body bgcolor="#ffffff" text="#000000">
	<?php
	$data = $dbc->query('SELECT prizeid, prizename FROM prizes');
	while ($row = $data->fetchRow()) { ?>
		<table border=0 cellpadding=2 cellspacing=1 bgcolor="#000000" width="100%">
			<?php
			$counter = 0;
			$dara = $dbc->query('SELECT userid FROM prizes_votes WHERE prizeid = '.(int)$row['prizeid'].' OR getall = 1');
			while($tow = $dara->fetchRow()) {
				$user = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$tow['userid']);
				if($counter%4==0) { echo '<tr>'; } ?>
				<td bgcolor=#ffffff width="25%">
				<img src="img/pxt.gif" width="1" height="3" border="0"><br />
				<div align="center">
					<font size=1 face="verdana">
						<?php echo $user; ?><br />
						<?php echo substr($row['prizename'],0,20).(strlen($row['prizename'])>20?'...'.substr($row['prizename'],(strlen($row['prizename'])-5),strlen($row['prizename'])):''); ?><br />	
					</font>
				</div>
				<img src="img/pxt.gif" width="1" height="3" border="0"><br />
				</td>
				<?php echo ((($dara->numRows()-1)==$counter)&&((($counter+1)%4)!=0)?'<td colspan='.(($counter+1)%4).' bgcolor="#ffffff">&nbsp;</td>':''); ?>
				<?php
				if ($counter%4==3){ echo '</tr>'; }
				$counter++;
			}
			?>
		</table><br />			
	<?php
	} ?>
	</body>
	</html>
	<?php
} else { 
	$x->display_slim(get_lang('noauth'));
} 
?>