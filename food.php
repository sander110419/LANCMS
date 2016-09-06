<?php
require_once '/include/_universal.php';
// the foodrun toggle will control this item's display aswell as food runs
$x = new universal(get_lang('plural'),get_lang('singular'),1);
$x->database('users_pizza','id','pizza');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_notes('add',get_lang('notes_add'));
$x->add_delmod_query("SELECT users_pizza.*,pizza.pizza FROM users_pizza LEFT JOIN pizza USING (pizzaid) WHERE users_pizza.userid='".$_COOKIE['userid']."'");

$x->add_related_link(get_lang('link_admin_pizza'),'admin_pizza.php',2);
$x->add_related_link(get_lang('link_admin_pizza_list'),'admin_pizza_list.php',2);
$x->add_related_link(get_lang('link_chng_pizza'),'chng_pizza.php',2);
$x->add_related_link(get_lang('link_pizza_list'),'pizza_list.php',1);

$x->start_elements();
$x->add_selectlist("pizzaid",1,1,0,get_lang('desc_pizzaid')." (<a href=\"pizza_list.php\">".get_lang('link_pizza_list')."</a>)",array("empty" => get_lang('error_pizzaid')),"SELECT pizzaid,pizza FROM pizza WHERE enabled='1'","pizzaid","pizza",1);
$x->add_text("quantity",1,1,0,get_lang('desc_quantity'),array("empty" => get_lang('error_quantity')),10);
$x->add_selectlist("userid",1,1,0,get_lang('desc_userid'),array(),"SELECT userid,username FROM users WHERE userid='".$_COOKIE["userid"]."'","userid","username",0);
global $master;
if ($master['pizza_orders_lock']) {
	$x->display_slim(get_lang('pizzas_locked'));
} elseif(empty($_POST)&&$x->is_secure()&&$toggle['foodrun']&&!$master['pizza_orders_lock ']) {
	$x->display_top();
	$x->display_form();
	$x->display_bottom();
	
} elseif(!empty($_POST)&&$x->is_secure()&&$toggle['foodrun']) {
	mail('sander.hilven@gmail.com', 'Food needed', 'Food needed');
	$x->display_results();
} else {
	$x->display_slim(get_lang('noauth'));
}
?>
