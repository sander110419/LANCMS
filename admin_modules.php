<?PHP
/*
Required SQL:
CREATE TABLE `alp`.`modules` (
  `moduleid` INTEGER UNSIGNED NOT NULL DEFAULT 0 AUTO_INCREMENT,
  `file` VARCHAR(45) NOT NULL DEFAULT '',
  `description` VARCHAR(45) NOT NULL DEFAULT '',
  `order` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`moduleid`)
)
TYPE = InnoDB;
*/
require_once 'include/_universal.php';
$x = new universal(get_lang('title'),get_lang('title'),2);
if($x->is_secure()) {
	$x->display_top();
	
	if($_POST['enable'] AND $_POST['moduleid2']) {
		$id = $_POST['moduleid2'];
		$result = $dbc->query("SELECT `moduleid`,`ordernum` FROM `modules` WHERE `ordernum` != '0' ORDER BY `ordernum` ASC");
		$z = 1;
		while($row = $result->fetchRow()) {
			extract($row);
			$orders[$moduleid] = $z;
			$z++;
		}
		$orders[$id] = $z;
		foreach($orders as $mid => $ordernum) {
			$dbc->query("UPDATE `modules` SET `ordernum` = '$ordernum' WHERE `moduleid` = '$mid'");
		}
	}
	if($_POST['disable'] AND $_POST['moduleid']) {
		$id = $_POST['moduleid'];
		$result = $dbc->query("SELECT `moduleid`,`ordernum` FROM `modules` WHERE `ordernum` != '0' AND `moduleid` != '$id' ORDER BY `ordernum` ASC");
		$z = 1;
		while($row = $result->fetchRow()) {
			extract($row);
			$orders[$moduleid] = $z;
			$z++;
		}
		$orders[$id] = 0;
		foreach($orders as $mid => $ordernum) {
			$dbc->query("UPDATE `modules` SET `ordernum` = '$ordernum' WHERE `moduleid` = '$mid'");
		}
	}
	if(($_POST['up'] || $_POST['down']) AND $_POST['moduleid']) {
		$id = $_POST['moduleid'];
		$result = $dbc->query("SELECT `moduleid`,`ordernum` FROM `modules` WHERE `ordernum` != '0' ORDER BY `ordernum` ASC");
		$z = 1;
		while($row = $result->fetchRow()) {
			extract($row);				
			$orders[$moduleid] = $z;
			$z++;
		}
		$curr = $orders[$id];
		if(($curr != 1 AND $_POST['up']) OR ($curr != count($orders) AND $_POST['down'])) {
			if($_POST['up'])
				$new = $curr - 1;
			else
				$new = $curr + 1;
			$chgid = array_search($new, $orders);
			$orders[$id] = $new;
			$orders[$chgid] = $curr;
		}
		foreach($orders as $mid => $ordernum) {
			$dbc->query("UPDATE `modules` SET `ordernum` = '$ordernum' WHERE `moduleid` = '$mid'");
		}
	}
	
	echo get_lang('add_notes');
	$result = $dbc->query("SELECT * FROM `modules` WHERE `ordernum` != '0' ORDER BY `ordernum` ASC");
	$rows = $result->numRows();
	echo "<form method=\"POST\"><table align=center><tr><td rowspan='4'><strong>".get_lang('enabled')."</strong><br /><select name=moduleid size=15>";
	
	while($row = $result->fetchrow()) {
		extract($row);
		echo"<option value=$moduleid>$description</option>";
	}
	echo "</select></td><td align=center valign=center><input type=submit name=enable value=\"<-".get_lang('enable')."\"><br /><br /><br /><input type=submit name=up value=\"".get_lang('up')."\"><br /><br /><br /><input type=submit name=down value=\"".get_lang('down')."\"><br /><br /><br /><input type=submit name=disable value=\"".get_lang('disable')."->\"></td>";
	$result = $dbc->query("SELECT * FROM `modules` WHERE `ordernum` = '0' ORDER BY `description`");
	$rows = $result->numRows();
	echo "<td rowspan='4'><strong>".get_lang('disabled')."</strong><br /><select name=moduleid2 size=15>";
		while($row = $result->fetchrow()) {
		extract($row);
		echo"<option value=$moduleid>$description</option>";
	}
	echo "</select></td></tr></table>";
	$x->display_bottom();
} else {
	$x->display_slim(get_lang('noauth'));
}