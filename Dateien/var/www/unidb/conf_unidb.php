<?php
	$sqlhostname = "localhost";
	$login = "";
	$password = "";
	$base = "unidb";
	// Verbindung herstellen und Verbindungskennung zurück geben
	$db = mysqli_connect($sqlhostname,$login,$password,$base) or die( 'Verbindungsfehler!' );
	mysqli_query($db, 'set character set utf8;');
?>
