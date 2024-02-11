<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
//ini_set( 'session.use_only_cookies', '1','SameSite=None', 'Secure' );
ini_set( 'session.use_trans_sid', '0' );
session_start();
//Get und POST einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
foreach($_POST as $key => $value){
	${$key}=$value;
}
header("X-XSS-Protection: 1");
date_default_timezone_set("Europe/Berlin");
// Funktionen einbinden
include('./funktionen.inc.php');
// Datenbankverbindung öffnen
if (!isset($db)) {
	$db = db_connect();
}
// Benutzer prüfen
if (!checkUser($db)) {resetUser();}
// Benutzer abmelden
if ($_GET['benutzer'] == 'abmelden') {resetUser();}

?>