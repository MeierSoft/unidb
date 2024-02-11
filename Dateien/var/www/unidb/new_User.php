<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
session_start();
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
// Funktionen einbinden
include('funktionen.inc.php');
require_once('./class.phpmailer.php');
require_once('./class.smtp.php');
// Datenbankverbindung öffnen
$db = db_connect();
$Text = Translate("new_User.php");

//Anmeldemodus feststellen und das Programm beenden, wenn dieser auf nein steht.
$query = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'neue Benutzerkonten';";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Anmeldemodus = $line["Wert"];
mysqli_stmt_close($stmt);
if($Anmeldemodus == "nein") {
	mysqli_close($db);
	exit;
}
				
function Pfad_finden($Pfad,$db) {
	$vorhanden = -1;
	$query="SELECT `Baum_ID` FROM `Baum` WHERE `Path` like '".$Pfad."%' LIMIT 1;";
	$stmt1 = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt1);
	$result = mysqli_stmt_get_result($stmt1);
	$vorhanden = mysqli_affected_rows($db);
	mysqli_stmt_close($stmt1);
	return $vorhanden;
}				
					
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<span style='font-family:Verdana,Arial'>\n";
echo "<h3>".$Text[1]."</h3>\n";

//Wenn ein Server nicht erreichbar ist, dann die Aktion abbrechen
if($_SESSION['uV_Daten'] == "_" || $_SESSION['uV_Daten'] == null || $_SESSION['uV_Daten'] == NULL) {
	$_SESSION['uV_Daten'] = uKol_verbinden();
}
for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
	if($_SESSION['uV_Daten'][$i]["Database"] == "") {
		echo $Text[25];
		exit;
	}
}
if (isset( $_POST['adduser'] )){
	// Wurde ein ausreichend langer Benutzername angegeben?
	if (strlen($_POST['benutzer'])<5) {
		echo "<font color='#FF0000'>".$Text[2]."</font><br>";
		$_POST['adduser'] = NULL;
	}
	// Wurde ein ausreichend langes Passwort angegeben?
	if (strlen($_POST['passwort_1'])<7) {
		echo "<font color='#FF0000'>".$Text[4]."</font><br>";
	 	$_POST['adduser'] = NULL;
	}
	// Stimmen die  beiden eingegebenen Passwörter überein?
	if ($_POST['passwort_1']!=$_POST['passwort_2']) {
		echo "<font color='#FF0000'>".$Text[20]."</font><br>";
		$_POST['adduser'] = NULL;
	}
	// Wurde das Feld voller Name ausgefüllt?
	if (strlen($_POST['Full_Name'])<5) {
		echo "<font color='#FF0000'>".$Text[5]."</font><br>";
		$_POST['adduser'] = NULL;
	}	
 	// Wurde das Feld eMail ausgefüllt?
	$s = '/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i';
	if(preg_match($s, $_POST['eMail'])){
		$eMail = $_POST['eMail'];
	}else{
		echo "<font color='#FF0000'>".$Text[6]."</font><br>";
		$_POST['adduser'] = NULL;
	}
	// Wurde die eMail oder der Benutzername bereits verwendet?
	$query = "SELECT * FROM `User` WHERE `UserName` = LOWER(?);";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "s", strtolower($_POST['benutzer']));
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if(mysqli_num_rows($result) > 0) {
		echo "<font color='#FF0000'>".$Text[23]."</font><br>";
		$_POST['adduser'] = NULL;
		mysqli_stmt_close($stmt);
	} else {
		$query = "SELECT * FROM `User` WHERE `eMail` = LOWER(?);";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "s", strtolower($_POST['eMail']));
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if(mysqli_num_rows($result) > 0) {
			echo "<font color='#FF0000'>".$Text[24]."</font><br>";
			$_POST['adduser'] = NULL;
		}
	}
	mysqli_stmt_close($stmt);
	if (isset( $_POST['adduser'] )){
		$Benutzer = $_POST['benutzer'];
		//max_User_ID einlesen und um 1 hochsetzen, damit die Server synchron bleiben
		$query = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'max_User_ID';";
		$ergebnis = mysqli_query($db, $query);
		$datensatz = mysqli_fetch_array($ergebnis);
		$max_User_ID = intval($datensatz['Wert']) + 1;
		$query = "UPDATE `Einstellungen` SET `Wert` = ? WHERE `Parameter` = 'max_User_ID';";
		uKol_schreiben(1, $query, "s",[$max_User_ID]);
		if (mysqli_connect_errno() == 0) {
			$_POST = array_map( 'stripslashes', array_map( 'trim', $_POST ));
			$salt          = substr( md5( microtime() ), 0, 10 );
			$pw_mit_salt   = md5( $_POST['passwort_1'] . $salt );
			$zuletzt_aktiv = '0000-00-00 00:00:00';
			$fehlversuche  = 0;
			$aktiviert     = 0;
			if($Anmeldemodus == "automatisch") {$aktiviert = 1;}
			$Full_Name	  = $_POST['Full_Name'];
			$eMail			  = $_POST['eMail'];
			// Benutzer in DB schreiben
			$sql = "INSERT INTO `User` (`User_ID`, `UserName`, `Full_Name`, `eMail`, `Password`, `Password_extension`, `ip`, `User_info`, `login`, `last_active`, `mistrials`, `activated`)	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			uKol_schreiben(1, $sql, "isssssssssii",[$max_User_ID,$_POST['benutzer'],$_POST['Full_Name'],$_POST['eMail'],$pw_mit_salt,$salt,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],md5( $_SERVER['REQUEST_TIME'] ),$zuletzt_aktiv,$fehlversuche,$aktiviert]);
			$query = "SELECT * FROM `User` WHERE `User_ID` = ".$max_User_ID.";";
			$ergebnis = mysqli_query($db, $query);
			if (mysqli_num_rows($ergebnis) == 1) {
				if($_SESSION['uV_Daten'] == "_" || $_SESSION['uV_Daten'] == null || $_SESSION['uV_Daten'] == NULL) {
					$_SESSION['uV_Daten'] = uKol_verbinden();
				}
				$query = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'useradmin';";
				$stmt = mysqli_prepare($db,$query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$SUser = $line["Wert"];
				$query = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'useradmin Passwort';";
				$stmt = mysqli_prepare($db,$query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Spwd = $line["Wert"];
				mysqli_stmt_close($stmt);
				$_SESSION['uV_Daten'] = uKol_verbinden();
				for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
					if($_SESSION['uV_Daten'][$i]["Database"] > "") {
						$uVerb = mysqli_connect($_SESSION['uV_Daten'][$i]["IP"],$SUser,$Spwd,"mysql");
						if ($uVerb == false) {
							$_SESSION['uV_Daten'][$i]["Database"] = "";
						} else {
							mysqli_query($uVerb, 'START TRANSACTION;');
							mysqli_query($uVerb, 'set character set utf8;');
							mysqli_query($uVerb,"CREATE USER `".strtolower($_POST['benutzer'])."`@`localhost` IDENTIFIED BY '".$pw_mit_salt."';");
							mysqli_query($uVerb,"GRANT SELECT ON `unidb`.`User_Pfade` TO `".strtolower($_POST['benutzer'])."`@`localhost`;");
							mysqli_query($uVerb,"GRANT SELECT ON `unidb`.`User_Baum` TO `".strtolower($_POST['benutzer'])."`@`localhost`;");
							mysqli_query($uVerb,"GRANT SELECT ON `DH`.`User_Tags` TO `".strtolower($_POST['benutzer'])."`@`localhost`;");
							mysqli_query($uVerb,"GRANT SELECT ON `DH`.`User_akt` TO `".strtolower($_POST['benutzer'])."`@`localhost`;");
							mysqli_query($uVerb,"GRANT SELECT ON `unidb`.`User_ID` TO `".strtolower($_POST['benutzer'])."`@`localhost`;");
							mysqli_query($uVerb,"FLUSH PRIVILEGES;");
							mysqli_query($uVerb, 'COMMIT;');
						}
					}
				}
				//Benutzerpfade anlegen
				$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Vorlagen neue Benutzer' AND `Eltern_ID` = 0;";
				$stmt = mysqli_prepare($db, $query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Vorlagen_ID = $line['Einstellung_ID'];
				mysqli_stmt_close($stmt);
				$query="SELECT `Parameter`, `Wert` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Vorlagen_ID.";";
				$stmt = mysqli_prepare($db, $query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$Wurzel = 0;
					if($line["Parameter"] == "Pfad root") {$Wurzel = 1;}
					while(strpos($line["Wert"], "$") > 0) {
						$Start_Vname = strpos($line["Wert"], "$");
						$Ende_Vname = strpos($line["Wert"], "/", $Start_Vname);
						$Vname = substr($line["Wert"], $Start_Vname + 1, $Ende_Vname - 1 - $Start_Vname);
						$line["Wert"] = substr($line["Wert"], 0, $Start_Vname).${$Vname}.substr($line["Wert"], $Ende_Vname);
					}
					if(Pfad_finden($line['Wert'],$db) > 0) {
						$sql = "INSERT INTO `User_Path` (`User_ID`, `Path`, `root`) VALUES (?, ?, ?);";
						uKol_schreiben(1, $sql, "isi",[$max_User_ID, $line['Wert'], $Wurzel]);
					} else {
						$Pfadteile = array();
						$Pfadteile = explode("/", $line["Wert"]);
						$tempPfad = "";
						$tempPfad1 = $tempPfad;
						for($i=1;$i < count($Pfadteile); $i++) {
							$tempPfad = $tempPfad."/".$Pfadteile[$i];
							if(Pfad_finden($tempPfad,$db) == 0) {
								//Eltern_ID ermitteln
								$query = "SELECT `Server_ID`, `Baum_ID` FROM `Baum` WHERE CONCAT (`Path`, '/', `Bezeichnung`) LIKE '".$tempPfad1."' OR CONCAT (`Path`, '/', `Bezeichnung`) LIKE '/".$tempPfad1."';";
 								$stmt2 = mysqli_prepare($db, $query);
								mysqli_stmt_execute($stmt2);
								$result2 = mysqli_stmt_get_result($stmt2);
								$line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
								$Eltern_ID = $line2["Server_ID"]."_".$line2["Baum_ID"];
								mysqli_stmt_close($stmt2);
								//neue leere Notiz anlegen
								$query = "INSERT INTO Baum (`Server_ID`, Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('html', ?));";
								uKol_schreiben(1,$query, "isissis", [$_SESSION["Server_ID"], $tempPfad1, $max_User_ID, $Eltern_ID, $Pfadteile[$i], 1, ""]);
							}
							$tempPfad1 = $tempPfad;
						}
						$sql = "INSERT INTO `User_Path` (`User_ID`, `Path`, `root`) VALUES (?, ?, ?);";
						uKol_schreiben(1, $sql, "isi",[$max_User_ID, $line['Wert'], $Wurzel]);
					}
				}
				mysqli_stmt_close($stmt);
				//ggf Meldung ausgeben
				
				if($Anmeldemodus == 'Freigabe erforderlich') {
					echo $Text[7].'<br>';
					echo $Text[8].'<br>'; 
					echo $Text[9].'<br>';
					echo $Text[10];
				} else {
					echo $Text[26].'<br>';
				}
  	         echo "<br><br><a href='index.php'>".$Text[11]."</a><br><br>";
				$mailtext = "Neuer Benutzer: ".$_POST['benutzer']."\nName: ".$_POST['Full_Name']."\nAdresse: ".$_POST['eMail'];
				$mail = new PHPMailer();
	         //$mail->SMTPDebug = 1; // Kann man zu debug Zwecken aktivieren
				$mail->IsSMTP();
				//Eltern_ID auslesen
				$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'eMail Konfiguration';";
				$stmt = mysqli_prepare($db,$query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Eltern_ID = $line["Einstellung_ID"];
				mysqli_stmt_close($stmt);
				//eMail Koniguration
				$query="SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
				$stmt = mysqli_prepare($db,$query);
				mysqli_stmt_bind_param($stmt, "i", $Eltern_ID);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					if($line["Parameter"] == "Host") {$mail->Host = $line["Wert"];}
					if($line["Parameter"] == "CharSet") {$mail->CharSet = $line["Wert"];}
					if($line["Parameter"] == "SMTPAuth" and $line["Wert"] == "true") {$mail->SMTPAuth = true;}
					if($line["Parameter"] == "SMTPSecure") {$mail->SMTPSecure = $line["Wert"];}
					if($line["Parameter"] == "Port") {$mail->Port = $line["Wert"];}
					if($line["Parameter"] == "Username") {$mail->Username = $line["Wert"];}
					if($line["Parameter"] == "Password") {$mail->Password = $line["Wert"];}
					if($line["Parameter"] == "SetFrom") {$mail->SetFrom($line["Wert"]);}
					if($line["Parameter"] == "addReplyTo") {$mail->addReplyTo($line["Wert"]);}
					if($line["Parameter"] == "addAddress") {$mail->addAddress($line["Wert"]);}
					if($line["Parameter"] == "Subject") {$mail->Subject = $line["Wert"];}
				}
				mysqli_stmt_close($stmt);
         	$mail->isHTML(true);
				$mail->Body = $mailtext;
				if(!$mail->Send()) {
					echo "";
				}
  	         exit;
			} else {
				echo $Text[12].'<br><br>';
			}
		} else {
			//echo 'Die Datenbank konnte nicht erreicht werden. Folgender Fehler trat auf: <span class="hinweis">' .mysqli_connect_errno(). ' : ' .mysqli_connect_error(). '</span>';
     		echo $Text[13];
		}
	}
}

echo "<br><table>";
echo '<form id="adduserform" method="post" action="./new_User.php">';
echo '<tr><td align="right">'.$Text[14].'</td><td><input type="text" name="benutzer" id="benutzer" value="'.$_POST['benutzer'].'"></td><td>'.$Text[21].'</td></tr>';
echo '<tr><td align="right">'.$Text[15].'</td><td><input type="password" name="passwort_1" id="passwort_1" value="'.$_POST['passwort_1'].'"></td><td>'.$Text[22].'</td></tr>';
echo '<tr><td align="right">'.$Text[16].'</td><td><input type="password" name="passwort_2" id="passwort_2" value="'.$_POST['passwort_2'].'"></td></tr>';
echo '<tr><td align="right">'.$Text[17].'</td><td colspan="2"><input type="text" name="eMail" id="eMail"  size="40" value="'.$_POST['eMail'].'"></td></tr>';
echo '<tr><td align="right">'.$Text[18].'</td><td colspan="2"><input type="text" name="Full_Name" id="Full_Name" size="40" value="'.$_POST['Full_Name'].'"></td></tr>';
echo '<tr height="40px"><td></td><td><input type="submit" name="adduser" id="adduser" value="'.$Text[19].'">';
echo "</td></tr></table>";
mysqli_close($db);
?>
</form>
</span>
</body>
</html>
