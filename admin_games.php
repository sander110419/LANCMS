<?php
// not needed if ALP_TOURNAMENT_MODE_COMPUTER_GAMES
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('games','gameid','name');
$x->permissions('add',1);
$x->permissions('del',1);
$x->permissions('mod',1);
$x->add_notes('add',get_lang('notes_add'));

$array = array();
$dir   = 'img/map_thumbnails';
$dh    = opendir($dir);
while (false !== ($filename = readdir($dh))) {
    if (is_dir($dir.'/'.$filename) && $filename != '.' && $filename != '..' && $filename != 'CVS') {
        $array[$filename] = $filename;
    }
}

$x->start_elements();
$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),255);
$x->add_text('current_version',0,1,0,get_lang('desc_current_version'),array(),40);
if(!ALP_TOURNAMENT_MODE) {
	$x->add_text('url_update',0,1,0,get_lang('desc_url_update'),array(),255);
	$x->add_text('url_maps',0,1,0,get_lang('desc_url_maps'),array(),255);
}
$x->add_select('thumbs_dir',0,1,0,'map thumbnails directory (in img/map_thumbnails/)',array(),$array);

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim(get_lang('noauth'));
}
?>