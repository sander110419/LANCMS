<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('users','userid','username');
$x->permissions('mod',1);
$x->add_notes('mod',get_lang('notes_mod'));
if(!empty($_GET['id'])) $x->add_delmod_query("SELECT * FROM users WHERE userid='".(int)$_GET['id']."'");

require_once 'include/_gaming_rig_db.php';

$x->start_elements();
$x->add_text("sharename",0,1,0,"share address ( \\\compname\share or ftp://compname )",array(),35);
$x->add_radio('ftp_server',0,1,0,get_lang('desc_ftp_server'),array(),array('1'=>'yes','0'=>'no'));
$x->add_displaystring('<br /><b>'.get_lang('singular').'</b><br />');
$x->format('table');
	$x->format('row');
		$x->format('cell');
			$x->add_select('comp_proc',0,1,0,'cpu (brand)',array(),$x_processors);
		$x->format('endcell');
		$x->format('cell');
			$x->add_text('comp_proc_type',0,1,0,'cpu (type)',array(),60);
		$x->format('endcell');
		$x->format('cell');
			$x->add_text('comp_proc_spd',0,1,0,'cpu (<b>MHz</b>) (numeric only)',array(),60);
		$x->format('endcell');
	$x->format('endrow');
$x->format('endtable');

$x->format('table');
	$x->format('row');
		$x->format('cell');
			$x->add_select('comp_mem',0,1,0,'RAM (amount)',array(),$x_mem_sizes);
		$x->format('endcell');
		$x->format('cell');
			$x->add_select('comp_mem_type',0,1,0,'RAM (type)',array(),$x_mem_types);
		$x->format('endcell');
	$x->format('endrow');
$x->format('endtable');
$x->add_select('comp_hdstorage',0,1,0,'total hard disk capacity',array(),$x_storage);
$x->format("table");
	$x->format("row");
		$x->format("cell");
			$x->add_select("comp_gfx_gpu",0,1,0,"gpu (brand)",array(),$x_gpus);		
		$x->format("endcell");
		$x->format("cell");
			$x->add_text("comp_gfx_type",0,1,0,"graphics (chipset and amount of memory)",array(),60);
		$x->format("endcell");
	$x->format("endrow");
$x->format("endtable");

if (empty($_POST) && $x->is_secure()) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif (!empty($_POST) && $x->is_secure()) {
	$x->display_results();
} else {
	$x->display_slim(get_lang('noauth'),'users.php?show=5');
}
?>