<?php
//This is the new config file, almost all settings have been moved to the database.
//You no longer need to edit this file,
//unless the installer is not able to edit this file because of permissions.
//Just run the installer at /install/install.php

//Database server connection settings
$database['type']     = 'mysql';
$database['user']     = 'root';
$database['passwd']   = 'bobobo';
$database['database'] = 'lantastic';
$database['server']   = 'localhost';

//Do not change strict_mode, unless you know what you are doing
$database['strict_mode']   = -1;
?>