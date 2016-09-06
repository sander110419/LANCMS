#!/usr/bin/php -q
<?php
require_once 'include/_universal.php';

error_reporting (E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit (0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush ();

$dn = array( // set up some dumb cert info
	'countryName'            => 'US',
	'stateOrProvinceName'    => 'None',
	'localityName'           => 'None',
	'organizationName'       => 'None',
	'organizationalUnitName' => 'None',
	'commonName'             => 'None',
	'emailAddress'           => 'none@none.com'
);

$query = "SELECT cert FROM satellite_clients WHERE ip='127.0.0.1';";
$result = $dbc->database_query($query); // get server key from the database
if ($dbc->database_num_rows($result) == 0) // if no key exists, generate one
{
	$privkey = openssl_pkey_new(); // generate a new private key
	openssl_pkey_export( $privkey, $newkey ); // get ascii key
	openssl_free_key( $privkey ); // free memory
	
	$query = "SELECT ip FROM satellite_clients WHERE ip='127.0.0.1';";
	$result = $dbc->database_query( $query );
	if( $dbc->database_num_rows( $result ) == 0 )
	{
		$query = "INSERT INTO satellite_clients (ip, game, cert) VALUES('127.0.0.1', 'server', '$newkey');";
		$dbc->database_query( $query );
	}
	else
	{
		echo "Error: server key already exists!<br />\n";
	}
	$query = "SELECT cert FROM satellite_clients WHERE ip='127.0.0.1';";
	$result = $dbc->database_query( $query ); // get server key from the database
	if( $dbc->database_num_rows( $result ) == 0 ) die( "Error generating OpenSSL key.\n" );
}
$row = $dbc->database_fetch_array( $result );
$privkey = $row['cert'];
$csr = openssl_csr_new( $dn, $privkey ); // get ready to sign the key to a cert
$sscert = openssl_csr_sign( $csr, null, $privkey, 365 ); // create the cert
openssl_x509_export( $sscert, $cert ); // get an ascii cert
openssl_pkey_export( $privkey, $pkey ); // get an ascii key

// Check to see if a deamon is running, if so, kill it before forking a new one
$query = "SELECT pid FROM pid WHERE name='clientlisten';";
$result = $dbc->database_query( $query );
if( $dbc->database_num_rows( $result ) != 0 )
{
	$row = $dbc->database_fetch_array( $result );
	posix_kill( $row['pid'], 1 );
	$query = "DELETE FROM pid WHERE name='clientlisten';";
	$dbc->database_query( $query );
}

$pid = pcntl_fork();

if( $pid != 0 )
{
	$query = "INSERT INTO pid VALUES( 'clientlisten', '$pid' );";
	$dbc->database_query( $query );
	exit;
}

// starts listening on specificed port and address
if (($sock = socket_create (AF_INET, SOCK_STREAM, 0)) < 0) {
    die( "socket_create() failed: reason: " . socket_strerror ($sock) . "\n" );
}
if (($ret = socket_bind ($sock, $address, $port)) < 0) {
    die( "socket_bind() failed: reason: " . socket_strerror ($ret) . "\n" );
}
if (($ret = socket_listen ($sock, 5)) < 0) {
    die( "socket_listen() failed: reason: " . socket_strerror ($ret) . "\n" );
}

// event loop
do {
    if (($msgsock = socket_accept($sock)) < 0) {
        echo "socket_accept() failed: reason: " . socket_strerror ($msgsock) . "\n";
        break;
    }
    
    $msg = "\nWelcome to the ALP Satellite Server \n" . // send welcome message
        "To quit, type 'quit'. You probably shouldn't be here anyway. :P\n";
    socket_write($msgsock, $msg, strlen($msg));
    
    socket_getpeername( $msgsock, $peer_addr ); // get remote ip

    do {
        if (FALSE === ($buf = socket_read ($msgsock, 2048))) { // get message sent
            echo "socket_read() failed: reason: " . socket_strerror ($ret) . "\n";
            break 2;
        }
        if (!$buf = trim ($buf)) { // remove whitespace from input
            continue;
        }
	if ($buf == 'hello' ) // if a hello message is recieved
	{
		$query = "SELECT ip FROM satellite_clients WHERE ip='$peer_addr';";
		$result = $dbc->database_query( $query );
		if( $dbc->database_num_rows( $result ) == 0 )
		{
			socket_write( $msgsock, $cert, strlen( $cert ) ); // send the server cert to the client
			
			$message = getMessage( $msgsock, $pkey ); // get ack for cert step
			if( $message != 'cert' ) break; // if they don't send the corrrect next step, stop
			socket_write( $msgsock, "okay", strlen( "okay" ) ); // send ack
			$clientcert = getMessage( $msgsock, $pkey );  // get the cert
			socket_write( $msgsock, "okay", strlen( "okay" ) ); // send ack

			$message = getMessage( $msgsock, $pkey ); // get the ack for passwd step
			if( $message != 'passwd' ) break; // if they don't send the correct next step, stop
			socket_write( $msgsock, "okay", strlen( "okay" ) );
			$passwd = getMessage( $msgsock, $pkey ); // get client passwd
			socket_write( $msgsock, "okay", strlen( "okay" ) ); // send ack

			$message = getMessage( $msgsock, $pkey );
			if( $message != 'game' ) break; // if they don't send the correct next step, stop
			socket_write( $msgsock, "okay", strlen( "okay" ) ); // send ack
			$game = getMessage( $msgsock, $pkey ); // get game name

			$message = getMessage( $msgsock, $pkey );
			if( $message != 'misc' ) break; // if they don't send the correct next step, stop
			socket_write( $msgsock, "okay", strlen( "okay" ) ); //send ack
			$misc = getMessage( $msgsock, $pkey ); // get misc info
			
			$lines = explode( ';', $misc );
			foreach( $lines as $line )
			{
				$line = explode( '=', $line );
				$$line[0] = $line[1];
			}

			$dbc->database_query( "INSERT INTO satellite_clients VALUES( '$peer_addr', '$game', '$clientcert', '$passwd', 'FALSE', '$Model', '$MHz', '$RAM', '1', '0', '0', '0', '0');" );
			for( $i = 1; $i <= 5; $i++ )
			{
				$dbc->database_query( "INSERT INTO satellite_config_inuse( path ) SELECT path FROM satellite_config WHERE game='$game';" );
				$dbc->database_query( "UPDATE satellite_config_inuse SET ip='$peer_addr', filename='default', server='$i' WHERE ISNULL(ip);" );
			}
			break;
		}
		else
		{
			$error = "stop";
			socket_write( $msgsock, $error, strlen( $error ) );
			echo "Error: client $peer_addr is already in the database.<br />\n";
		}
	}
	$decrypt = decryptMessage( $buf, $pkey );
	if( $decrypt != 'plaintext' )
	{
		if( $decrypt == 'quit' ) $buf = 'quit';
		else if( $decrypt = 'goodbye' ) // unregister the client, its going way :(
		{
			$query = "SELECT cert FROM satellite_clients WHERE ip='$peer_addr';";
			$result = $dbc->database_query( $query );
			$row = $dbc->database_fetch_array( $result );
			$thecert = $row['cert'];
			$dbc->database_query( "DELETE FROM satellite_clients WHERE ip='$peer_addr';" );
			$dbc->database_query( "DELETE FROM satellite_config_inuse WHERE ip='$peer_addr';" );
			break;
		}
	}
        if ($buf == 'quit') { // close the connection to the client
            break;
        }
    } while (true);
    socket_close ($msgsock);
} while (true);

socket_close ($sock);

function decryptMessage( $buf, $key )
{
	if( substr( $buf, 0, 7 ) == 'encrypt' )
	{
		$buf = substr( $buf, 7 );
		$length = strpos( $buf, 'envelope' );
		$sealed = substr( $buf, 0, $length );
		$ekey = substr( $buf, $length + 8 );
		$privkey = openssl_get_privatekey( $key );
		openssl_open( $sealed, $open, $ekey, $privkey );
		return $open;
	}
	else
	{
		return 'plaintext';
	}
}

?>
