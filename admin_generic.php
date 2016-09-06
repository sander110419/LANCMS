<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
if($x->is_secure()) {
	if(!empty($_POST['case'])&&!empty($_POST['ref'])) {
		$err = FALSE;
		$queries = array();
		switch($_POST['case']) {
			case 'erase_seeding':
				$queries[] = "UPDATE tournament_teams SET ranking=NULL WHERE tourneyid='".(int)$_POST['tourneyid']."'";
				$queries[] = "UPDATE tournament_players SET ranking=NULL WHERE tourneyid='".(int)$_POST['tourneyid']."'";
				break;
			case 'lock_teams':
				$queries[] = 'UPDATE tournaments SET lockteams=1, lockjoin=1 WHERE tourneyid='.(int)$_POST['tourneyid'];
				break;
			case 'unlock_teams':
				$queries[] = 'UPDATE tournaments SET lockteams=0, lockjoin=0 WHERE tourneyid='.(int)$_POST['tourneyid'];
				break;
			case 'boiloff_finished':
				$queries[] = 'UPDATE tournaments SET lockfinish=1 WHERE tourneyid='.(int)$_POST['tourneyid'];
				break;
			case 'boiloff_unfinished':
				$queries[] = 'UPDATE tournaments SET lockfinish=0 WHERE tourneyid='.(int)$_POST['tourneyid'];
				break;
		}
		foreach($queries as $val) {
			if(!$dbc->database_query($val)) {
				$err = TRUE;
			}
		}
		if($err) $str = 'unknown error!';
		else $str = get_lang('success');
		$x->display_slim($str,$_POST['ref']);
	} else {
		$x->display_slim(get_lang('incorrect'));
	}
} else {
	$x->display_slim(get_lang('noauth'));
}