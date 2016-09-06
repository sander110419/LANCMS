<BODY TEXT="0097AE">
<FONT FACE="arial">
<?php
$username = "root";
$password = "bobobo";
$hostname = "localhost"; 
$dbhandle = mysql_connect($hostname, $username, $password) 
 or die("Unable to connect to MySQL");
$selected = mysql_select_db("lantastic",$dbhandle) 
  or die("Could not select examples");
$result = mysql_query("SELECT * FROM users WHERE userid='".$_GET["id"]."'");
while ($row = mysql_fetch_array($result)) {
   echo "<strong>Balance: </strong>".$row{'balance'};
}
mysql_close($dbhandle);
?>
 LC
 </FONT>
</BODY>