<?php
require_once 'include/_universal.php';
// universal('plural name', 'singular name', minimum security level)
$x = new universal('Sponsors','',0);
if ($x->is_secure()) {
	$x->display_top();
	?>
		<strong>Chat</strong>:<br />
		<br />
 


		
 
		
<table align="center" border="0" height="800px" width="100%" cellpadding="0" cellspacing="0" bgcolor="<?php echo $colors["cell_title"]; ?>">
  <tr><td>
<iframe src="/chat/index.html" height="100%" width="100%" frameborder="0"></iframe>
    </td>
  </tr>
</table>
<br />
<?php
		$counter++;
	}
	        echo '</CENTER>';
	$x->display_bottom();
?>
