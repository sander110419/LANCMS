<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
if ($x->is_secure()) {
	if (empty($_GET)) {
		$x->display_slim(get_lang('error_tournament'),'admin_tournament.php');
	} elseif (!empty($_GET) && !empty($_GET['id'])) {
		if ($dbc->database_num_rows($dbc->database_query('SELECT tourneyid FROM tournaments WHERE tourneyid='.(int)$_GET['id']))) {
			if ($_GET['lock']) {
				if ($dbc->database_query('UPDATE tournaments SET lockteams=1, lockjoin=1 WHERE tourneyid='.(int)$_GET['id'])) {
					$str = get_lang('success').' - '.get_lang('locked');
				} else {
					$str = get_lang('error_unknown');
				}
			} else {
				if ($dbc->database_query('UPDATE tournaments SET lockteams=0, lockjoin=0 WHERE tourneyid='.(int)$_GET['id'])) {
					$str = get_lang('success').' - '.get_lang('unlocked');
				} else {
					$str = get_lang('error_unknown');
				}
			}
			$x->display_slim($str,'disp_teams.php'.(!empty($_GET['id'])?'?id='.$_GET['id']:''),2);
		} else {
			$x->display_slim(get_lang('error_tournament_bad'),'admin_tournament.php');
		}
	} else {
		$x->display_slim(get_lang('incorrect'),'admin_tournament.php');
	}
} ?>