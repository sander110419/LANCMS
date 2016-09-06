<?php
session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);define('C_HOST','localhost');// MySQL host name (usually:localhost)
define('C_USER','kak');// MySQL username
define('C_PASS','kak');// MySQL password
define('C_BASE','kak');// MySQL database
define('C_PATH','/chat');// Document Path

include('inc.db.php');

?>