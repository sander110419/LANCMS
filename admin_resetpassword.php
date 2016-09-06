<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('plural'),3);
$x->display_top();
if ($x->is_secure()) { 
	if (empty($_POST)) { ?>
		<b><?php echo get_lang('sadministrator'); ?></b>: <?php echo get_lang('plural'); ?><br />
		<br />
		<table border="0" cellpadding="4" cellspacing="4" width="400" class="centerd"><tr><td>
		<?php
		begitem(get_lang('plural')); ?>
		<form action="<?php echo get_script_name(); ?>" method="POST" name="modify">
		<script language="javascript" src="include/_md5.js"></script>
		<script language="javascript">
		<!-- 
		function doLogin() { 
		if(document.modify.new_passwd.value != "") document.modify.new_passwd.value = calcMD5(document.modify.new_passwd.value);
		if(document.modify.new_passwd_confirm.value != "") document.modify.new_passwd_confirm.value = calcMD5(document.modify.new_passwd_confirm.value);
		document.modify.javascript.value = "yes";
		} 
		// --> 
		</script>
		<input type="hidden" name="javascript" value="" />
		<font size="1"><b><?php echo get_lang('username'); ?></b><br /></font>
		<select name="username" style="width: 99%"><option value=""></option>
		<?php
		$data = $dbc->query('SELECT userid, username FROM users ORDER BY username');
		while ($row = $data->fetchRow()) { ?>
			<option value="<?php echo $row['userid']; ?>"><?php echo $row['username']; ?></option>
			<?php
		}
		?>
		</select><br />
		<br />
		<font size="1"><b><?php echo get_lang('newpass'); ?></b><br /></font>
		<input type="password" name="new_passwd" maxlength="60" style="width: 99%" /><br />
		<font size="1"><b><?php echo get_lang('confirm'); ?></b><br /></font>
		<input type="password" name="new_passwd_confirm" maxlength="60" style="width: 99%" /><br />
		<br />
		<div align="right"><input type="submit" name="submit" value="<?php echo get_lang('submit_reset'); ?>" style="width: 120px" onClick="doLogin(); return true;" class="formcolors" /></div>
		</form>
		<?php
		enditem(get_lang('plural')); ?>
		</td></tr></table>
		<?php
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
		
		if ($valid->get_value('javascript')=='') {
			if ($valid->get_value('new_passwd')!='') $valid->set_value('new_passwd',md5($valid->get_value('new_passwd')));
			if ($valid->get_value('new_passwd_confirm')!='') $valid->set_value('new_passwd_confirm',md5($valid->get_value('new_passwd_confirm')));
		}
		
		$valid->is_empty('username',get_lang('error_username'));		
		$valid->is_empty('new_passwd',get_lang('error_new_passwd'));
		$valid->is_empty('new_passwd_confirm',get_lang('error_new_passwd_confirm'));
		$valid->is_same($valid->get_value('new_passwd'),$valid->get_value('new_passwd_confirm'),get_lang('error_new_passwd_same'));		
		
		if (!$valid->is_error()) {
			$holder_passwd = crypt($valid->get_value('new_passwd'));
			if ($dbc->database_query ("UPDATE users SET passwd='".$holder_passwd."' WHERE userid='".$valid->get_value("username")."'")) {
				echo get_lang('update_success');
                echo "<br /><br /> &gt; <a href=\"admin_resetpassword.php\">".get_lang('update_another')."</a>.<br /><br />";
			} else {
				echo get_lang('update_error')."<br />";
			}
		} else {
			$valid->display_errors();
		}
	}
} else {
	$x->display_slim(get_lang('noauth'));
}
$x->display_bottom();
?>