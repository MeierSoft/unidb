<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );
//ini_set( 'session.SameSite', 'Lax', 'Secure' );

session_start();
header("X-XSS-Protection: 1");
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
if (isset($_POST['login'])) {
	// Benutzereingabe bereinigen
	$eingabe = cleanInput();
	// Benutzer anmelden
	$anmeldung = loginUser($eingabe['benutzername'], $eingabe['passwort'], $db);
	// Anmeldung war korrekt
	if ($anmeldung) {
		// Benutzer Identifikationsmerkmale in DB speichern
		$update = updateUser($_SESSION['DB_User'], $db);
		// Bei erfolgreicher Speicherung
		if ($update) {
			// Auf geheime Seite weiterleiten
			include 'mobil.php';
			if ($mobil==1) {
				header("location: Baum2.php");
			} else {
				header("location: unidb.html");
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<title>unidb</title>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<link href='./css/DH/default.css' rel='stylesheet'>
</head>
<body class='allgemein'>
<table><tr><td><img height="80px" src="./stat_Seiten/Logo.png" alt="Logo"></td><td>
<?php echo "<H1>".$Text[8]."</H1>"; ?>
</td></tr>
<form id='loginform' method='post' action='./index.php'>

<tr height="40px"><td><label for="sprache"></label></td><td><table><tr><td><a href="./index.php?Lang=DE" target="_self"><img src="Bilder/Flaggen/DE.png" alt="deutsch"></a></td><td><a href="./index.php?Lang=EN" target="_self"><img src="Bilder/Flaggen/EN.png" alt="english"></a></td><td><a href="./index.php?Lang=NL" target="_self"><img src="Bilder/Flaggen/NL.png" alt="nederlands"></a></td><td><a href="./index.php?Lang=FR" target="_self"><img src="Bilder/Flaggen/FR.png" alt="francais"></a></td></tr></table></td></tr>
<?php
echo "<tr><td align='right'><label for='benutzer'>".$Text[0].":</label></td><td><input type='text' name='benutzer' id='benutzer' value=''></td></tr>\n";
echo "<tr><td align='right'><label for='passwort'>".$Text[1].": </label></td><td><input type='password' name='passwort' id='passwort' value=''></td></tr>\n";
echo "<tr height='140px'><td colspan = '2' style='width: 260px;'>".$Text[7]."</td></tr>\n";
echo "<tr><td></td><td><input type='submit' name='login' id='login' value='".$Text[2]."' /></td></tr>\n";

$query = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'neue Benutzerkonten';";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
if($line["Wert"] != 'nein') {
	echo "<tr style='height: 50px;' valign='bottom'><td></td><td><a href='new_User.php'>".$Text[3]."</a></td></tr>\n";
} 
mysqli_stmt_close($stmt);
mysqli_close($db);

echo "<tr style='height: 30px;' valign='bottom'><td></td><td><a href='Passwort_vergessen.php?Lang=".$Lang."'>".$Text[6]."</a></td></tr>\n";
echo "</table>\n";
echo "<input type='hidden' name='Lang' id='lang' value='".$_SESSION["Sprache"]."'>\n";
echo "</form>\n";
?>
</body>
</html>
