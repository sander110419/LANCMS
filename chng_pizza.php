<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),1);
$x->database('pizza','pizzaid','pizza');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));

$x->add_related_link(get_lang('link_admin_pizza'),'admin_pizza.php',2);
$x->add_related_link(get_lang('link_admin_pizza_list'),'admin_pizza_list.php',2);
$x->add_related_link(get_lang('link_pizza'),'pizza.php',1);
$x->add_related_link(get_lang('link_pizza_list'),'pizza_list.php',1);

$x->start_elements();
$x->add_text("pizza",1,1,0,get_lang('desc_pizza'),array("empty" => get_lang('error_pizza')),150);
$x->add_text("description",0,1,0,get_lang('desc_description'),array(),255);
$x->add_text("price",1,1,0,get_lang('desc_price'),array(),10);
$x->add_checkbox("enabled",0,1,0,get_lang('desc_enabled'));

if(empty($_POST)&&$x->is_secure()&&$toggle['foodrun']) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()&&$toggle['foodrun']) {
	$x->display_results();
} else {
	$x->display_slim(get_lang('noauth'));
}
?>
