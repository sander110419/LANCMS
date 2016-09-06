<?php
require_once 'include/_universal.php'; 
$x = new universal('files','files',0);
$x->display_smallwindow_top($colors['cell_background']);
require_once 'include/cl_display_dir.php';
if (isset($_GET['type'])) {
    display_dir($_GET['type'], 0);
}
$x->display_smallwindow_bottom();
?>