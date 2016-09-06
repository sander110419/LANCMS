<?php
require_once 'include/_universal.php'; 
$x = new universal('map list','',1);
$x->display_smallwindow_top();
if ($x->is_secure()) { 
?>
	<table border="0" cellpadding="4" width="100%"><tr><td>
	<div align="center"><b>gamer of the hour</b></div>
	<br />
	<?php echo $master['gamerhour']; ?><br />
	<br />
<?php
	if (!empty($_GET['id'])) {
		$goth = $dbc->queryRow('SELECT username,quote FROM users WHERE userid='.(int)$_GET['id']); 
?>
		<div align="center"><strong><?php echo $goth['username']; ?></strong><br />
		<span class="sm"><?php echo $goth['quote']; ?></span></div>
<?php
	} 
?>
	</td></tr></table>
<?php
} else {
?>
	<script language="JavaScript" type="text/javascript">
	<!--
	window.close();
	// -->
	</script>
<?php
}
?>