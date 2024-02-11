<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<title>unidb</title>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );
//ini_set( 'session.SameSite', 'Lax', 'Secure' );

session_start();
header("X-XSS-Protection: 1");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
foreach($_GET as $key => $value){
	${$key}=$value;
}
// Sicherstellen das die SID durch den Server vergeben wurde
// um einen möglichen Session Fixation Angriff unwirksam zu machen
if (!isset( $_SESSION['server_SID'] ))
{
	// Möglichen Session Inhalt löschen
	session_unset();
	// Ganz sicher gehen das alle Inhalte der Session gelöscht sind
	$_SESSION = array();
	// Session zerstören
	session_destroy();
	// Session neu starten
	session_start();
	// Neue Server-generierte Session ID vergeben
	session_regenerate_id();
	// Status festhalten
	$_SESSION['server_SID'] = true;
}

// Funktionen einbinden
include('./funktionen.inc.php');
// Variablen deklarieren
$_SESSION['angemeldet'] = false;
$db                  = '';
$eingabe                = array();
$anmeldung              = false;
$update                 = false;
$fehlermeldung          = '';

// Datenbankverbindung öffnen
$db = db_connect();
if ($Lang == "") {$Lang = $_SESSION['Sprache'];}
if ($Lang == "") {$Lang = "EN";}
if ($Lang != $_SESSION['Sprache']) {$_SESSION['Sprache'] = $Lang;}
$Text = Translate("index.php");

// Wenn das Formular abgeschickt wurde
if (isset( $_POST['login'] )) {
	// Benutzereingabe bereinigen
	$eingabe = cleanInput();
	// Benutzer anmelden
	
	$anmeldung = loginUser( $eingabe['benutzername'], $eingabe['passwort'], $db );
	// Anmeldung war korrekt
	if ($anmeldung) {
		// Benutzer Identifikationsmerkmale in DB speichern
		$update = updateUser( $eingabe['benutzername'], $db );
		// Bei erfolgreicher Speicherung
		if ($update) {
			// Auf geheime Seite weiterleiten
			include '../mobil.php';
			if ($mobil==1){
				header("location: admin_Auswahl.php");
			}else{
				header("location: admin.html");
			}
			exit;
		} else {
			$fehlermeldung = '<h3>'.$Text[4].'</h3>';
		}
	} else {
		$fehlermeldung = '<h3>'.$Text[5].'</h3>';
	}
}

?>
</head>
<body class='allgemein'>
<form id='loginform' method='post' action='./index.php'>
<table>
<tr height="40px"><td><label for="sprache"></label></td><td><table><tr><td><a href="./index.php?Lang=DE" target="_self"><img src="../Bilder/Flaggen/DE.png" alt="deutsch"></td><td></a><a href="./index.php?Lang=EN" target="_self"><img src="../Bilder/Flaggen/EN.png" alt="english"></td><td></a><a href="./index.php?Lang=NL" target="_self"><img src="../Bilder/Flaggen/NL.png" alt="nederlands"></td><td></a><a href="./index.php?Lang=FR" target="_self"><img src="../Bilder/Flaggen/FR.png" alt="francais"></a></td></tr></table></td></tr>
<?php
echo "<tr><td align='right'><label for='benutzer'>".$Text[0].":</label></td><td><input type='text' name='benutzer' id='benutzer' value=''></td></tr>\n";
echo "<tr><td align='right'><label for='passwort'>".$Text[1].": </label></td><td><input type='password' name='passwort' id='passwort' value=''></td></tr>\n";
echo "<tr><td></td><td><input type='submit' name='login' id='login' value='".$Text[2]."' /></td></tr>\n";
echo "</table>\n";
echo "<input type='hidden' name='Lang' id='lang' value='".$_SESSION["Sprache"]."'>\n";
echo "</form>\n";
echo "<br><br><br><p><a href='new_User.php'>".$Text[3]."</a></p>\n";
?>
</body>
</html>
