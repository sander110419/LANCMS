<head>
<meta http-equiv="refresh" content="10" > 
<style type="text/css">
.refresh {
	color: #F00;
	font-size: large;
}
</style>
</head>
<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),2);
$x->display_top();
if($toggle['foodrun']&&$x->is_secure()) { ?>
	<b><?php echo get_lang('administrator'); ?></b>: <?php echo get_lang('singular'); ?><br>
	<br>
	<?php
	$x->add_related_link(get_lang('link_admin_pizza'),'admin_pizza.php',2);
    $x->add_related_link(get_lang('link_chng_pizza'),'chng_pizza.php',2);
    $x->add_related_link(get_lang('link_pizza'),'pizza.php',1);
    $x->add_related_link(get_lang('link_pizza_list'),'pizza_list.php',1);
	$x->display_related_links(); ?>
	<br>
	<b><?php echo get_lang('singular'); ?></b>:<br>
	<br>
	<?php 
	$data = $dbc->database_query("SELECT * FROM users_pizza LEFT JOIN pizza USING (pizzaid)");
	// id 	userid 	pizzaid 	quantity 	paid 	delivered 	id 	pizza 	description 	price
	$summary = Array();
	$pizzas = Array();
	$totals = Array();
	while($row = $dbc->database_fetch_assoc($data)) {
		$summary[$row['pizzaid']]['delivered'] += ($row['delivered'] * $row['quantity']);
		$totals['delivered'] += ($row['delivered'] * $row['quantity']);
		if ($row['delivered'] > 0) {
		    $summary[$row['pizzaid']]['quantity'] += 0;
			$totals['quantity'] += 0;
		} else {
			$summary[$row['pizzaid']]['quantity'] += $row['quantity'];
			$totals['quantity'] += $row['quantity'];
		}
		if ($row['paid'] > 0) {
			$summary[$row['pizzaid']]['paid'] += ($row['paid'] * $row['quantity']);
			$totals['paid'] += ($row['paid'] * $row['quantity']);
			$summary[$row['pizzaid']]['price'] += 0;
			$totals['price'] += 0;
		} else {
			$summary[$row['pizzaid']]['paid'] += 0;
			$totals['paid'] += 0;
			$summary[$row['pizzaid']]['price'] += ($row['price']*$row['quantity']);
			$totals['price'] += ($row['price']*$row['quantity']);
		}
		if (!in_array($row['pizzaid'],$pizzas)) {
			$pizzas[$row['pizzaid']] = $row['pizza'];
		}
	}
	?>
    <table border="0" cellpadding="3" cellspacing="0" width="100%" class="smm">
	<tr>
    	<td bgcolor="<?php echo $colors['cell_title']; ?>"><a href="<?php echo get_script_name(); ?>?o=t">Time ordered</a></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><a href="<?php echo get_script_name(); ?>?o=n"><?php echo get_lang('tr_username'); ?></a></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><a href="<?php echo get_script_name(); ?>?o=p"><?php echo get_lang('tr_pizza'); ?></a></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_quantity'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_paid'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_delivered'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_id'); ?></td>
	</tr>
	<?php
  switch ($_GET['o']) {
    case "p":
      $pizza_sort = "pizzaid";
      break;
    case "n":
    default:
      $pizza_sort = "id";
    break;
	case "t":
      $pizza_sort = "time";
      break;
  }
  $data = $dbc->database_query("SELECT users_pizza.*,users.username FROM users_pizza LEFT JOIN users USING (userid) ORDER BY $pizza_sort DESC");
	// id 	userid 	pizzaid 	quantity 	paid 	delivered 	id 	pizza 	description 	price
	$counter = 0;
	while($row = $dbc->database_fetch_assoc($data)) {
	?>
	<tr>
    	<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $row['time']; ?></td>
		<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $row['username']; ?></td>
		<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $pizzas[$row['pizzaid']]; ?></td>
		<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $row['quantity']; ?></td>
		<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>>
      <?php
      if ($row['paid']) {
        echo get_lang('yes');
      } else {
      ?>
      <form id="payment" name="payment" action="/admin_pizza.php" method="POST">
          <input id="p-type" type="hidden" name="type" value="mod">
          <input id="p-sum" type="hidden" name="summary_page" value="/admin_pizza_list.php">
      		<input id="p-id" type="hidden" name="id" value="<?php echo $row['id']; ?>">
      		<input id="p-one_row" type="hidden" name="_one_row_only" value="2">
      		<input id="p-hid" type="hidden" name="_hidden_id" value="0&o=<?php echo $_GET['o']; ?>">
      		<input id="p-pid" type="hidden" name="pizzaid" value="<?php echo $row['pizzaid']; ?>">
      		<input id="p-qty" type="hidden" name="quantity" value="<?php echo $row['quantity']; ?>">
      		<input id="p-paid" type="hidden" name="paid" value="1">
      		<input id="p-uid" type="hidden" name="userid" value="<?php echo $row['userid']; ?>">
          <input id="p-submit" type="submit" value="Genoeg LC?" class="formcolors">
      </form>
      <?php
      }
      ?>
    </td>
		<td<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>>
      <?php
      if ($row['delivered']) {
        echo get_lang('yes');
      } elseif ($row['paid']) {
      ?>
      <form id="delivery" name="delivery" action="/admin_pizza.php" method="POST">
          <input id="d-type" type="hidden" name="type" value="mod">
          <input id="d-sum" type="hidden" name="summary_page" value="/admin_pizza_list.php">
      		<input id="d-id" type="hidden" name="id" value="<?php echo $row['id']; ?>">
      		<input id="d-one_row" type="hidden" name="_one_row_only" value="2">
      		<input id="d-hid" type="hidden" name="_hidden_id" value="0&o=<?php echo $_GET['o']; ?>">
      		<input id="d-pid" type="hidden" name="pizzaid" value="<?php echo $row['pizzaid']; ?>">
      		<input id="d-qty" type="hidden" name="quantity" value="<?php echo $row['quantity']; ?>">
      		<input id="d-del" type="hidden" name="delivered" value="1">
      		<input id="d-paid" type="hidden" name="paid" value="1">
      		<input id="d-uid" type="hidden" name="userid" value="<?php echo $row['userid']; ?>">
          <input id="d-submit" type="submit" value="deliver this" class="formcolors">
      </form>
      <?php
      } else {
        echo get_lang('no');
      }
      ?>
    </td>
	</tr>

	<?php
		$counter++;
	}
	?>
	</table>
    <br>
    <br>
	<table border="0" cellpadding="3" cellspacing="0" width="100%" class="smm">
	<tr>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_pizza'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_numorder'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_numdelivered'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_numpaid'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_remain'); ?></td>
	</tr>
	<?php
	$counter = 0;
	while(list($key,$val) = each($pizzas)) {
	?>
	<TR>
		<TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $pizzas[$key]; ?></TD>
		<TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $summary[$key]['quantity']; ?></TD>
		<TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $summary[$key]['delivered']; ?></TD>
		<TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $summary[$key]['paid']; ?></TD>
		<TD<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $summary[$key]['price']; ?></TD>
	</TR>
		<?php
		$counter++;
	} // for
	?>
	<tr>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('total'); ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo $totals['quantity']; ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo $totals['delivered']; ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo $totals['paid']; ?></td>
		<td bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo $totals['price']; ?></td>
	</tr>
	</table>
	<br>
	<b><?php echo get_lang('singular').' '.get_lang('detail'); ?></b>:<br>
	<br>
	<?php
} else {
	echo get_lang('noauth')."<br><br>";
}
$x->display_bottom();
?>
<body>
<center> <p style="color:red">AUTOREFRESHES EVERY 10 SECONDS</center>
</body>