<?php
require_once 'include/_universal.php';

$gameport['halflife']               = array( 27015, 27016, 27017, 27018, 27019 );
$gameport['unreal tournament']      = array( 7777, 8000, 8100, 8200, 8300 );
$gameport['unreal tournament 2003'] = array( 7777, 8000, 8100, 8200, 8300 );
$gameport['quake 3']                = array( 27960, 27961, 27962, 27963, 27964 );
$gameport['battlefield 1942']       = array( 14567, 14568, 14569, 14570, 14571 );

$x = new universal('ALP satellite control panel','',2);
if ($x->is_secure()) { 
	if (empty($_GET) && empty($_POST)) {
		$x->display_top();
		begitem('ALP satellite');
		$query = "SELECT * FROM pid WHERE name='clientlisten';";
		$result = $dbc->database_query($query);
		if ($dbc->database_num_rows($result) != 0) {
			$sock = socket_create(AF_INET, SOCK_STREAM, 0);
			if ( $ret = @socket_connect($sock, $address, $port)) {
				socket_read( $sock, 2048 );
				socket_write( $sock, "hello", strlen( "hello" ) );
				echo '<H2>Currently Listening for Clients</H2>'."\n";
			} else {
				echo '<H2>ALP SATELLITE IS NOT LISTENING FOR CLIENTS!</H2>'."\n";
			}
		} else {
			echo '<H2>ALP SATELLITE IS NOT LISTENING FOR CLIENTS!</H2>'."\n";
		}
		echo 'ALP satellite is still in early beta.  Please be patient with the interface.<br /><br />';
		$query = "SELECT * FROM satellite_clients WHERE ip!='127.0.0.1' ORDER BY game,ip";
		$result = $dbc->database_query($query);
		if ($dbc->database_num_rows($result)) {
			echo( "<TABLE>\n<TR><TH>IP/port<TH>Stats<TH>Game<TH>Servers\n" );
			while( $row = $dbc->database_fetch_array( $result ) )
			{
				$ram = ceil( $row['ram'] / 32768 ) * 32;
				echo( "<TR><TD>" . $row['ip'] );
				echo( "<TD>${row['cpu']} @ ${row['mhz']}MHz $ram MB <TD>" . $row['game'] );
				echo( "<TD><SELECT onChange=\"document.location.href = this.value\">\n" );
				for( $i=1; $i <= 5; $i++ )
				{
					$temp = get_script_name() . "?action=update&ip=" . $row['ip'] . "&num=$i";
					if( $i == $row['servers'] ) echo( "<option value='$temp' SELECTED>$i</option>\n" );
					else echo( "<option value='$temp'>$i</option>\n" );
				}
				echo( "</SELECT>\n" );
				for( $i = 1; $i <= $row['servers']; $i++ )
				{
					$game = $row['game'];
					$port = $gameport[$game][$i-1];
					$link = get_script_name() . "?action=manage&ip=" . $row['ip'] . "&server=$i";
					echo( "<TR><TD>\n<TABLE><TR><TD width=10><TD>:<A HREF='$link'>$port</A></TABLE>\n<TD>" );
					if( $row['running' . ($i)] )
					{
						echo( "running: <A HREF=\"".get_script_name()."?action=stop&ip=${row['ip']}&server=$i\">stop</A>" );
						if( $game == 'halflife' )
							echo( " <A HREF=\"rcon_command.php?ip=${row['ip']}&port=$port\" target=\"_new\">rcon</A>\n" );
					}
					else echo( "stopped: <A HREF=\"".get_script_name()."?action=start&ip=${row['ip']}&server=$i\">start</A>" );
				}
			}
			echo( "</TABLE>\n" );
			echo( "<A HREF=\"".get_script_name()."?action=delserver\">manually remove a server from the list</A>\n" );
		} else { ?>
			<b>there are no satellites logged into alp.  are you sure that the dns name of your webserver is named 'alp'?</b><br /><br />
			<br />
			<?php
		}
		enditem('ALP satellite');
		$x->display_bottom();
	}
	elseif( $_GET['action'] == 'delserver' )
	{
		$x->display_top();
		begitem("delete satellites");
		echo( "<FORM action=\"".get_script_name()."?action=delservernow\" method=\"post\">\n" );
		$result = $dbc->database_query( "SELECT * FROM satellite_clients WHERE ip!='127.0.0.1' ORDER BY game,ip" );
		while( $row = $dbc->database_fetch_array( $result ) )
		{
			echo( "<INPUT TYPE=\"checkbox\" name=\"servers[]\" value=\"${row['ip']}\"> ${row['ip']} ${row['game']}<br />\n" );
		}
		echo( "<INPUT TYPE=\"submit\" value=\"remove selected servers\">\n" );
		echo( "</FORM>\n" );
		enditem("delete satellites");
		$x->display_bottom();
	}
	elseif( $_GET['action'] == 'start' || $_GET['action'] == 'stop' )
	{
		$address = $_GET['ip'];
		//$port = "10000";
		$result = $dbc->database_query( "SELECT cert FROM satellite_clients WHERE ip='$address';" );
		$row = $dbc->database_fetch_array( $result );
		$cert = $row['cert'];
		$result = $dbc->database_query( "SELECT cert FROM satellite_clients WHERE ip='127.0.0.1';" );
		$row = $dbc->database_fetch_array( $result );
		$key = $row['cert'];
		$sock = socket_create( AF_INET, SOCK_STREAM, 0 );
		$ret = socket_connect( $sock, $address, $port );
		getMessage( $sock, $key );
		sendMessage( $sock, $_GET['action'], $cert );
		getMessage( $sock, $key );
		sendMessage( $sock, $_GET['server'], $cert );
		if( $_GET['action'] == 'start' ) $dbc->database_query( "UPDATE satellite_clients SET running${_GET['server']}='1' WHERE ip='${_GET['ip']}';" );
		else $dbc->database_query( "UPDATE satellite_clients SET running${_GET['server']}='0' WHERE ip='${_GET['ip']}';" );
		$x->display_slim("success!",get_script_name());
	}
	elseif( $_GET['action'] == 'manage' )
	{
		$x->display_top();
		begitem("edit satellite");
		echo( "<A HREF='".get_script_name()."'>return to satellite list</A><br />\n" );
		$result = $dbc->database_query( "SELECT * FROM satellite_clients WHERE ip='${_GET['ip']}';" );
		$row = $dbc->database_fetch_array( $result );
		$ram = ceil( $row['ram'] / 32768 ) * 32;
		$game = $row['game'];
		$theport = $gameport[$game][$_GET['server'] - 1];
		echo( "<H2>${_GET['ip']}:$theport ${row['cpu']} at ${row['mhz']}MHz  with $ram MB RAM</H2>\n" );
		echo( "<TABLE border=0>\n" );
		echo( "<TR><TH>File<TH>Currently<TH>Upload New\n" );
		$result = $dbc->database_query( "SELECT filename, name, satellite_config.path FROM satellite_config LEFT JOIN satellite_config_inuse USING (path) WHERE IP='${_GET['ip']}' AND server='${_GET['server']}' ORDER BY name;" );
		echo( "<FORM enctype=\"multipart/form-data\"action=\"".get_script_name()."?action=upload&ip=${_GET['ip']}&server=${_GET['server']}&game=$game\" method=\"post\">\n" );
		echo( "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1000000\">\n" );
		while( $row = $dbc->database_fetch_array( $result ) )
		{
			echo( "<TR><TD>${row['name']}<TD><select name=\"filename\" OnChange=\"JavaScript:location.href='?action=ftp&ip=${_GET['ip']}&server=${_GET['server']}&path=${row['path']}&file='+this.value\">\n" );
			$files = $dbc->database_query( "SELECT filename FROM satellite_config_files WHERE game='$game' AND path='${row['path']}' ORDER BY filename;" );
			$select = $dbc->database_query( "SELECT filename FROM satellite_config_inuse WHERE ip='${_GET['ip']}' AND server='${_GET['server']}' AND path='${row['path']}';" );
			$selected = $dbc->database_fetch_array( $select );
			while( $file = $dbc->database_fetch_array( $files ) )
			{
				if( $selected['filename'] == $file['filename'] ) echo( "<option value=\"${file['filename']}\" selected>${file['filename']}</option>\n" );
				else echo( "<option value=\"${file['filename']}\">${file['filename']}</option>\n" );
			}
			$name = explode( '/', $row['path'] );
			array_shift( $name );
			array_shift( $name );
			$name = strtr( implode( '/', $name ), array( '.' => '^', '!' => '', '/' => '~' ) );	
			echo( "</select><TD><input name='$name' type='file'>\n" );
		}
		echo( "<input type=\"submit\" value=\"Upload\"></form>\n" );
		echo( "</TABLE>\n" );
		echo( "<A HREF=\"".get_script_name()."?action=delconf&ip=${_GET['ip']}&server=${_GET['server']}&game=$game\">remove uploaded config files</A><br />\n" );
		echo( "<A HREF='".get_script_name()."'>return to satellite list</A><br />\n" );
		enditem("edit satellite");
		$x->display_bottom;
	}
	elseif( $_GET['action'] == 'upload' )
	{
		$result = $dbc->database_query( "SELECT path FROM satellite_config WHERE game='${_GET['game']}';" );
		while( $row = $dbc->database_fetch_array( $result ) )
		{
			$filename = explode( '/', $row['path'] );
			array_shift( $filename );
			array_shift( $filename );
			$filename = strtr( implode( '/', $filename ), array( '.' => '^', '!' => '', '/' => '~' ) );	
			if( $_FILES[$filename]['error'] === 0 )
			{
				$name = $_FILES[$filename]['name'];
				$path = strtr( $filename, array( '^' => '.', '~' => '/' ) );
				$out .= "Uploading file $name... ";
				$duplicate = $dbc->database_query( "SELECT * FROM satellite_config_files WHERE path='${row['path']}' AND filename='$name';" );
				if( $dbc->database_num_rows( $duplicate ) )
				{
					$out .= "Error: a file named $name has already been uploaded.";
					$delay = 5;
				}
				else
				{
					move_uploaded_file( $_FILES[$filename]['tmp_name'], getcwd() . "/files/config_files/" . $path . "/" . $name );
					$dbc->database_query( "INSERT INTO satellite_config_files VALUES( '${row['path']}', '${_GET['game']}', '$name' );" );
					$delay = 1;
				}
				$out .= "<br />\n";
			}
		}
		$x->display_slim( $out."<br />\n", get_script_name() . "?action=manage&ip=${_GET['ip']}&server=${_GET['server']}", $delay );
	}
	elseif( $_GET['action'] == 'ftp' ) // need to add error detection if ftp connect fails
	{
		$result = $dbc->database_query( "SELECT passwd,game FROM satellite_clients WHERE ip='${_GET['ip']}';" );
		$row = $dbc->database_fetch_array( $result );
		$game = $row['game'];
		$passwd = $row['passwd'];
		$path = strtr( $_GET['path'], array( "!" => "-" . $_GET['server'] ) );
		$localpath = explode( '/', $_GET['path'] );
		array_shift( $localpath );
		array_shift( $localpath );
		$localpath = getcwd() . "/files/config_files/" . strtr( implode( '/', $localpath ), array( '!' => '' ) ) . "/" . $_GET['file'];
		
		$ftp_conn = ftp_ssl_connect( $_GET['ip'] );
		ftp_login( $ftp_conn, 'root', $passwd );
		$str = "putting $localpath to $path";
		ftp_put( $ftp_conn, $path, $localpath, FTP_BINARY );
		ftp_close( $ftp_conn );
		$dbc->database_query( "UPDATE satellite_config_inuse SET filename='${_GET['file']}' WHERE ip='${_GET['ip']}' AND path='${_GET['path']}' AND server='${_GET['server']}';" );
		$x->display_slim($str . "<br />\n",get_script_name() . "?action=manage&ip=${_GET['ip']}&server=${_GET['server']}" , 2);
	}
	elseif( $_GET['action'] == 'delconf' )
	{
		$x->display_top();
		begitem("delete config files");
		echo( "<FORM action=\"".get_script_name()."?action=delconfnow&ip=${_GET['ip']}&server=${_GET['server']}\" method=\"post\">\n" );
		$result = $dbc->database_query( "SELECT name, satellite_config.path, filename FROM satellite_config LEFT JOIN satellite_config_files USING (path) WHERE filename != 'default' AND satellite_config_files.game='${_GET['game']}' ORDER BY name,filename; " );
		while( $row = $dbc->database_fetch_array( $result ) )
		{
			$filepath = explode( '/', $row['path'] . "/" . $row['filename'] );
			array_shift( $filepath );
			array_shift( $filepath );
			$filepath = strtr( implode( '/', $filepath ), array( '.' => '^', '!' => '', '/' => '~' ) );
			
			echo( "<INPUT type=\"checkbox\" name=\"files[]\" value=\"$filepath\"> ${row['name']} ${row['filename']}<br />\n" );
		}
		echo( "<INPUT type=\"submit\" name=\"submit\" value=\"delete selected files\">\n" );
		echo( "</FORM>\n" );
		enditem("delete config files");
		$x->display_bottom();
	}
	elseif( $_GET['action'] == 'delconfnow' )
	{
		foreach( $_POST['files'] as $thefile )
		{
			$thefile = strtr( $thefile, array( '^' => '.', '~' => '/' ) );
			$temp = explode( "/", $thefile );
			$filename = array_pop( $temp );
			array_shift( $temp );
			$configname = implode( '/', $temp );
			$out .= "Deleting file " .  $thefile . "...";
		
			$result = $dbc->database_query( "SELECT ip FROM satellite_config_inuse WHERE filename='$filename' AND LOCATE( '$configname', path );" );
			if( $dbc->database_num_rows( $result ) )
			{
				$out .= "Error: file is on use on ";
				while( $row = $dbc->database_fetch_array( $result ) )
				{
					$out .= $row['ip'] . " ";
				}
			}
			else
			{
				unlink( getcwd() . "/files/config_files/" . $thefile );
				$dbc->database_query( "DELETE FROM satellite_config_files WHERE filename='$filename' AND LOCATE( '$configname', path );" );
			}
			$out .= "<br />\n";
		}
		$x->display_slim($out."<br />\n", get_script_name() . "?action=manage&ip=${_GET['ip']}&server=${_GET['server']}" , 2 );
	}
	elseif( $_GET['action'] == 'delservernow' )
	{
		foreach( $_POST['servers'] as $theserver )
		{
			$dbc->database_query( "DELETE FROM satellite_clients WHERE ip='$theserver';" );
			$dbc->database_query( "DELETE FROM satellite_config_inuse WHERE ip='$theserver';" );
		}
		$x->display_slim("your satellites were deleted", get_script_name(), 2);
	}
	elseif( $_GET['action'] == 'update' )
	{
		$dbc->database_query( "UPDATE satellite_clients SET servers='${_GET['num']}' WHERE ip='${_GET['ip']}';" );
		$x->display_slim("success!", get_script_name() , 2 );
	}
} else {
	$x->display_slim("you are not authorized to view this page." );
}
?>
