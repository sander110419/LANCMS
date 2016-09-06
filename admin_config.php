<?php
require_once 'include/_universal.php';

$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('config','','');
$x->permissions('mod',1);
$x->start_elements();

if(!ALP_TOURNAMENT_MODE) {
	$x->add_text('name',1,1,0,get_lang('desc_name'),array('empty' => get_lang('error_name')),100);
	$x->add_text('org',1,1,0,get_lang('desc_org'),array('empty' => get_lang('error_org')),100);
	$x->add_text('location',1,1,0,get_lang('desc_location'),array('empty' => get_lang('error_location')),50);
	// TODO : populate country selectlist with more options
	$x->add_select('country',1,1,0,get_lang('desc_country'),array('empty' => get_lang('error_country')),array("us"=>"us","outside us"=>"outside us"));
	$x->add_text('max',1,1,0,get_lang('desc_max'),array('empty' => get_lang('error_max')),5);
	$x->add_datetime('datetimestart',0,1,1,get_lang('desc_datetimestart'),array(),array(date('U')));
	$x->add_datetime('datetimeend',0,1,1,get_lang('desc_datetimeend'),array(),array(date('U')));
	$x->add_text('email',1,1,0,get_lang('desc_email'),array('empty' => get_lang('error_email')),40);
	$x->add_text('websiteurl',1,1,0,get_lang('desc_websiteurl'),array('empty' => get_lang('error_websiteurl')),40);
}
$x->add_select('alp_tournament_mode',1,1,0,get_lang('desc_alp_tournament_mode'),array(),array(1=>get_lang('yes'),0=>get_lang('no')));
$x->add_select('alp_tournament_mode_computer_games',1,1,0,get_lang('desc_alp_tournament_mode_computer_games'),array(),array(1=>get_lang('mode_games'),0=>get_lang('mode_sports')));

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