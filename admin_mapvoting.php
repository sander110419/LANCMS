<?php
require_once 'include/_universal.php';
$x = new universal('eligible maps','map',2);
if (empty($_POST) && (empty($_GET['id']) || !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['id']."'")))) {
	$x->display_top();
	select_tournament(0);
	$x->display_bottom();
} else {
	if (empty($_POST) && $x->is_secure()) {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']));
		$game = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM games WHERE gameid='.(int)$tournament['gameid']));
		if (!empty($game['thumbs_dir']) && file_exists(getcwd().'/img/map_thumbnails/'.$game['thumbs_dir'].'/')) {
			if ($handle = @opendir(getcwd().'/img/map_thumbnails/'.$game['thumbs_dir'].'/')) { 
				while (false!== ($file = @readdir($handle))) {
					if ($file != '.' && $file != '..'&&$file != 'CVS') {
						$tempfile = substr($file,0,strrpos($file,'.'));
						if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".(int)$tournament['tourneyid']."' AND filename='".$tempfile."'"))) {
							$dbc->database_query("INSERT INTO poll_maps (filename,tourneyid) VALUES ('".$tempfile."','".$tournament['tourneyid']."')");
						}
					}
				}
				@closedir($handle);
			}
		} else {
			$errbool = true;
		}
	}
	$x->database('poll_maps','id','filename');
	$x->permissions('add',1);
	$x->permissions('mod',2);
	$x->permissions('del',1);
	
	$str = 'map thumbnails for '.$game['name'].' should be located in the img/map_thumbnails/'.$game['thumbs_dir'].' directory (with an extension of .jpg, .gif, or .png).  input the file name below (excluding the extension, for example, a de_dust.jpg thumbnail is in img/map_thumbnails/, so I would type de_dust below in the file name field.)';
	$x->add_notes('add',$str);
	$x->add_notes('mod',$str);
	$x->add_notes('del','maps in this list that have a thumbnail located in img/map_thumbnails/'.$game['thumbs_dir'].' cannot be deleted.  if you delete a map here without removing the map thumbnail, it will be automatically re-added with a new id, thus effectively erasing any votes registered for that map.  so be careful!');
	
	$x->add_extra('del',array(array('DELETE FROM poll_votes_maps','id')));
	$x->add_delmod_query("SELECT poll_maps.*,tournaments.name AS tourneyname FROM poll_maps LEFT JOIN tournaments USING (tourneyid) WHERE tournaments.tourneyid='".$tournament['tourneyid']."'");
	
	$x->start_elements();
	$x->add_selectlist('tourneyid',1,0,0,'tournament',array(),"SELECT * FROM tournaments WHERE tourneyid='".(int)$_GET['id']."'",'tourneyid','name');
	$x->add_text('filename',0,0,0,$game['name'].' map name',array(),255);
	$x->add_checkbox('selected',0,1,0,'put this map in poll?',array(),0);
	//$x->add_textarea("filedesc",0,1,0,"map description",array(),3);
	
	if (empty($_POST) && $x->is_secure()) {
		if (!$errbool) {
			$x->display_top();
			$x->display_form();
			$x->display_bottom();
		} else {
			$tournament = $dbc->database_fetch_assoc($dbc->database_query('SELECT gameid FROM tournaments WHERE tourneyid='.(int)$_GET['id']));
			$x->display_slim('the map thumbnails directory for the tournament game is invalid.','admin_games.php?mod=1&q='.$tournament['gameid']);
		}
	} elseif (!empty($_POST) && $x->is_secure()) {
		$x->display_results();
	} else {
		$x->display_slim('you are not authorized to view this page.');
	}
}
?>