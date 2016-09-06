<?php
require_once 'include/_universal.php';
$x = new universal('add_dummy_users','add_dummy_users',2);
require_once 'include/cl_validation.php';
$valid = new validate();

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	//$x->display_form();
	?>
	So, you wanna add some dummy users to ALP, well, give me some parameters below.<br /><br />
	<form name="form1" method="post" action="">
	<table width="250" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>Username Prefix: </td>
			<td><input name="username_prefix" type="text" id="username_prefix" value="user" size="15"></td>
		</tr>
		<tr>
			<td>Number to start at: </td>
			<td><input name="num_to_start" type="text" id="num_to_start" value="1" size="15"></td>
		</tr>
		<tr>
			<td>How many to make: </td>
			<td><input name="num_of_users" type="text" id="num_of_users" value="100" size="15"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="Submit" value="Submit"></td>
		</tr>
	</table>
	</form>
<?php
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_top();
	//$x->display_results();
	$username_prefix = $valid->get_value('username_prefix');
	$num_of_users = $valid->get_value('num_of_users');
	$num_to_start = $valid->get_value('num_to_start');
	//$passwd = '$1$ts2.yl3.$/GOpSWz84KEmJ5EoHHeVP0';  //Password: pass
	
	for($i=$num_to_start;$i<$num_of_users+$num_to_start;$i++) {
		$username		= $username_prefix.$i;
		$first_name		= $username.'_firstname';
		$last_name		= $username.'_lastname';
		$passwd			= crypt(md5($username));
        $query          = "INSERT INTO `users` (dateformat, language, skin, priv_level, username, passwd, date_of_arrival, recent_ip, email, first_name, last_name) 
                                        VALUES ('".$master['dateformat']."', '".$master['currentlanguage']."', '".$master['currentskin']."', 1, '".$username."', '".$passwd."', '".date('Y-m-d H:i:s')."', '127.0.0.1', '', '".$first_name."', '".$last_name."')";
		$dbc->database_query($query);
		echo "added user: $username <br />";
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
