<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->database('users_pizza','id','username');
$x->permissions('add',1);
$x->permissions('mod',1);
$x->permissions('del',1);
$x->add_delmod_query("SELECT users_pizza.*,users.username FROM users_pizza LEFT JOIN users USING(userid)");

$x->add_related_link(get_lang('link_admin_pizza_list'),'admin_pizza_list.php',2);
$x->add_related_link(get_lang('link_chng_pizza'),'chng_pizza.php',2);
$x->add_related_link(get_lang('link_pizza'),'pizza.php',1);
$x->add_related_link(get_lang('link_pizza_list'),'pizza_list.php',1);

$x->start_elements();
$x->add_selectlist("pizzaid",1,1,1,get_lang('desc_pizzaid')." (<a href=\"chng_pizza.php\">".get_lang('link_chng_pizza')."</a>)",array("empty" => get_lang('error_pizzaid')),"select * from pizza WHERE enabled='1'","pizzaid","pizza");
$x->add_text("quantity",1,1,0,get_lang('desc_quantity'),array("empty" => get_lang('error_quantity')),150);
$x->add_checkbox("delivered",0,1,0,get_lang('desc_delivered'));
$x->add_checkbox("paid",0,1,0,get_lang('desc_paid'));
$x->add_selectlist("userid",1,1,1,get_lang('desc_userid'),array("empty" => get_lang('error_userid')),"select * from users","userid","username");

if(empty($_POST)&&$x->is_secure()&&$toggle['foodrun']) {
	$x->display_top();
 	$x->display_form();
	$x->display_bottom();
} elseif(!empty($_POST)&&$x->is_secure()&&$toggle['foodrun']) {
	if (isset($_POST['summary_page'])) {
  	$x->display_results($_POST['summary_page'],1);
  } else {
  	$x->display_results();
  }
} else {
	$x->display_slim(get_lang('noauth'));
}
?>
