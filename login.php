<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('singular'),get_lang('log in'),0);
if ($x->is_secure()) { 
	if (current_security_level() >= 1) { 
		if(empty($_GET['ref'])) {
			$x->display_slim(get_lang('al_loged',1),'index.php');
		} else {
			$x->display_top(); ?>
			<strong><?php get_lang('log in',1) ?></strong>:<br />
			<br />
			<?php get_lang('al_loged',1) ?><br />
			<?php
			$x->display_bottom();
		}
	} else {
		if (empty($_POST)) { 
			$x->display_top(); 
			if (!$master['authbyiponly']) { ?>
				<script language="javascript" type="text/javascript" src="include/_md5.js"></script>
				<script language="javascript" type="text/javascript"> 
				<!-- 
				function doLogin() { 
				if(document.loginm.passwd.value != "") document.loginm.passwd.value = calcMD5(document.loginm.passwd.value);
				document.loginm.javascript.value = "yes";
				} 
				// --> 
				</script>
				<?php
			} ?>
			<strong>log in</strong>:<br />
			<br />
			<table border="0" cellpadding="2" cellspacing="2" width="400" class="centerd"><tr><td>
			<form action="login.php" method="post" name="loginm"><?php if(!$master['authbyiponly']) { ?><input type="hidden" name="javascript" value=""><?php } ?>
			<font size="1">&nbsp;<?php get_lang('username',1) ?><br /></font>
			<?php
			if ($master['loginselect']) { ?>
				<select name="username" style="width: 99%; font-size: 11px"><option value=""></option>
				<?php
				$data = $dbc->query('SELECT username FROM users ORDER BY username');
				while($row = $data->fetchRow()) { ?>
					<option value="<?php echo $row['username']; ?>"><?php echo $row['username']; ?></option>
					<?php
				} ?>
				</select>
				<?php
			} else { ?>
				<input type="text" name="username" maxlength="40" style="width: 99%" /><br />
				<?php
			}
			if (!$master['authbyiponly']) { ?>
				<font size=1>&nbsp;<?php get_lang('password',1) ?> <br /></font>
				<input type="password" name="passwd" maxlength="34" style="width: 99%" /><br />
				<?php
			} ?>
			<input type="hidden" name="ref" value="<?php echo (preg_match('/^[a-zA-Z_0-9\-\/]*[a-zA-Z_0-9\-]+\.php$/',utf8_decode($_GET['ref']))?utf8_decode($_GET['ref']):''); ?>">
			<font size=1>&nbsp;<a href="register.php"><?php get_lang("cp_register") ?></a></font><br />
			<?php
			if (!$master['authbyiponly']) { ?>
				<font size=1>&nbsp;<a href="passwd.php"><?php get_lang('forgot',1) ?></a></font><br />
				<font size=1 color="<?php echo $colors['blended_text']; ?>"> <?php get_lang('cp_security',1) ?><br /></font>
				<?php
			} ?>
			<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
			<div align="right"><input type="submit" value="<?php get_lang('log in',1) ?>" class="button"<?php if(!$master['authbyiponly']) { ?> onClick="doLogin(); return true;"<?php } ?> /></div>
			</form>
			</td></tr></table>
			<?php
			if(!$master['authbyiponly']) { ?>
				<font size=1><?php get_lang('security_long',1) ?></font><br />
				<?php
			}
			$x->display_bottom();
		} else { 
			include 'include/cl_validation.php';
			$valid = new validate();
			
			if (!$master['authbyiponly']) { 
				if ($valid->get_value('javascript')=='') {
					if($valid->get_value('passwd')!='') $valid->set_value('passwd',md5($valid->get_value('passwd')));
				}
			}
			
			$data = $dbc->database_query("SELECT * FROM users WHERE username='".$valid->get_value('username')."'");
			if ($dbc->database_num_rows($data)) {
				$row = $dbc->database_fetch_assoc($data);
				if (!$master['authbyiponly']) { 
					if ($valid->is_empty('passwd',get_lang('blank_pass'))) {
						$temporary = crypt($valid->get_value('passwd'), $row['passwd']);
						$valid->is_same($temporary,$row['passwd'], get_lang('bad_pass'));
					}
				} else {
					if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM users WHERE username='".$valid->get_value("username")."' AND recent_ip='".$_SERVER['REMOTE_ADDR']."'"))) {
						$valid->add_error(get_lang("bad_ip"));
					}
				}
			} else {
				$valid->add_error(get_lang('bad_user'));
			}
			
			$valid->is_empty('username', get_lang('blank_user'));
			
			if (!$valid->is_error()) {
				$row = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM users WHERE username='".$valid->get_value('username')."'"));
				$allgood = true;

				mt_srand((double)microtime() * 1000000);
				$sessionid = md5(uniqid(mt_rand(),1));
				if (!$master['authbyiponly']) {
					$holder = " recent_ip='".$_SERVER['REMOTE_ADDR']."',";
				} else {
					$holder = "";
				}
				if(!$dbc->database_query("UPDATE users SET".$holder." sesid='".$sessionid."' where userid='".$row['userid']."';")) {
					$allgood = false;
				}
				if ((date('U')-date('U',$end))>0) {
					$expire = date('U')+345600; // current time + 4 days
				} else {
					$expire = date('U',$end)+86400; // end of the lan + 1 day.
				}
				if (!setcookie('username',$row['username'],$expire)) {
					$allgood = false;
				}
				if (!setcookie('userid',$row['userid'],$expire)) {
					$allgood = false;
				}
				if (!setcookie('sesid',$sessionid,$expire)) {
					$allgood = false;
				}
				
				if ($allgood) {
					$s = 'you have been successfully logged in.';
				} else {
					$s = 'error! login unsuccessful.';
				}
				if(!empty($_POST['ref'])) $x->display_slim($s,$_POST['ref']);
				else $x->display_slim($s);
			} else {
				$x->display_top();
				$valid->display_errors();
				$x->display_bottom();
			}
		}
	}
} else {
	$x->display_slim(get_lang('noauth'),(!empty($_GET['ref'])?urldecode($_GET['ref']):''));
}
?>