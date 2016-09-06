<?php
require_once 'include/_universal.php';
$x = new universal('change your password','password',1);
$x->display_top();
if ($x->is_secure()) { 
	if (empty($_POST)) { ?>
		<script language="javascript" src="_md5.js"></script>
		<script language="javascript">
		<!-- 
		function doLogin() { 
		if(document.modify.current_passwd.value != "") document.modify.current_passwd.value = calcMD5(document.modify.current_passwd.value);
		if(document.modify.new_passwd.value != "") document.modify.new_passwd.value = calcMD5(document.modify.new_passwd.value);
		if(document.modify.new_passwd_confirm.value != "") document.modify.new_passwd_confirm.value = calcMD5(document.modify.new_passwd_confirm.value);
		document.modify.javascript.value = "yes";
		} 
		// --> 
		</script>
		<b>change password</b>:<br />
		<br />
		<table border=0 cellpadding=4 cellspacing=4 width="400" class="centerd"><tr><td>
		<form action="chng_passwd.php" method="POST" name="modify">
		<input type="hidden" name="javascript" value="">
		<font size=1><b>current password</b><br /></font>
		<input type="password" name="current_passwd" maxlength=32 style="width: 99%"><br />
		<br />
		<font size=1><b>new password</b><br /></font>
		<input type="password" name="new_passwd" maxlength=32 style="width: 99%"><br />
		<font size=1><b>new password confirm</b><br /></font>
		<input type="password" name="new_passwd_confirm" maxlength=32 style="width: 99%"><br />
		<br />
		<div align="right"><input type="submit" value="change passwd" onClick="doLogin(); return true;" class="formcolors"></div>
		</form>
		</td></tr></table>
		<?php
	} else { 
		require_once 'include/cl_validation.php';
		$valid = new validate();

		if($valid->get_value('javascript')=='') {
			if($valid->get_value('current_passwd')!='') {
				$valid->set_value('current_passwd',md5($valid->get_value('current_passwd')));
			}
			if($valid->get_value('new_passwd')!='') {
				$valid->set_value('new_passwd',md5($valid->get_value('new_passwd')));
			}
			if($valid->get_value('new_passwd_confirm')!='') {
				$valid->set_value('new_passwd_confirm',md5($valid->get_value('new_passwd_confirm')));
			}
		}
		
		if(!empty($userinfo['passwd'])) {
			$holder_passwd = $userinfo['passwd'];
		}

		if($valid->is_empty('current_passwd','the current_passwd field is blank.')) {
			$temp = crypt($valid->get_value('current_passwd'), $holder_passwd);
		}

		if(empty($temp)||empty($holder_passwd)) {
			$valid->add_error('your password is incorrect.');
		} else {
			$valid->is_same($temp,$holder_passwd,'your password is incorrect.');
		}
	
		$valid->is_empty('new_passwd','the new password field is blank.');
		$valid->is_empty('new_passwd_confirm','the new password confirm field is blank.');
		$valid->is_same($valid->get_value('new_passwd'),$valid->get_value('new_passwd_confirm'),'your new password does not match the new password confirm field.');

		if(!$valid->is_error()) {
			$holder_passwd = crypt($valid->get_value('new_passwd'));
			if($dbc->database_query ("UPDATE users SET passwd='".$holder_passwd."' WHERE userid='".$_COOKIE["userid"]."'")) {
				echo 'your password has been successfully updated.  don\'t forget it.  it\'s a big hassle to get the admin to reset your password.<br /><br /> &gt; <a href="chng_passwd.php">change your password again</a>.<br /><br />';
			} else {
				echo 'there has been an error updating your password with the database.  your password have not been updated.<br />';
			}
		} else {
			$valid->display_errors();
		}
	}
} else {
	echo 'you are not authorized to view this page.<br /><br />';
}
$x->display_bottom();
?>