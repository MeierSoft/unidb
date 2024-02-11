<?php
if($_SESSION['Hilfeliste'] == null) {$_SESSION['Hilfeliste'] = "";}

function db_connect() {
	require 'conf_unidb.php';
	$_SESSION['DB_Server'] = $sqlhostname;
	$_SESSION['DB_DB'] = $base;
	return $db;
}

function Pfad_ermitteln($Baum_ID,$Server_ID){
	$BaumID = $Baum_ID;
	if(gettype($BaumID) == "string") {
		if(strpos($BaumID,"_") > 0) {
			$BaumID = intval(substr($BaumID,strpos($BaumID,"_") + 1,strlen($BaumID)));
			$Server_ID = intval(substr($Baum_ID,0, strpos($Baum_ID,"_")));
		}
	}
	$db = db_connect();
	$Bezeichnung = "";
	while($BaumID != 0) {
		$stmt = mysqli_prepare($db,"SELECT Eltern_ID, Bezeichnung FROM Baum WHERE Baum_ID = ? AND Server_ID = ?;");
		mysqli_stmt_bind_param($stmt, "ii", $BaumID, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$BaumID = intval(substr($line["Eltern_ID"],strpos($line["Eltern_ID"],"_") + 1,strlen($line["Eltern_ID"])));
		$Server_ID = intval(substr($line["Eltern_ID"],0,strpos($line["Eltern_ID"],"_")));
		$Bezeichnung = $line["Bezeichnung"]."/".$Bezeichnung;
	}
	mysqli_close($db);
	$Bezeichnung = substr($Bezeichnung, 0, strlen($Bezeichnung) - 1);
	return "/".$Bezeichnung;
}

function Point_ID_finden($Tag_ID, $dbDH) {
	$db = db_connect();
	$query="SELECT `Path` FROM `User_Path` WHERE `User_ID` = ?;";
	$stmt1 = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt1, "i", $_SESSION['User_ID']);
	mysqli_stmt_execute($stmt1);
	$result1 = mysqli_stmt_get_result($stmt1);
	while ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
		$query="SELECT `Point_ID` FROM `Tags` WHERE `Tag_ID` = ? AND LEFT(`Path`, LENGTH('".$line1["Path"]."')) LIKE '".$line1["Path"]."';";
		$stmt2 = mysqli_prepare($dbDH,$query);
		mysqli_stmt_bind_param($stmt2, "i", $Tag_ID);
		mysqli_stmt_execute($stmt2);
		$result2 = mysqli_stmt_get_result($stmt2);
		if(mysqli_num_rows($result2) > 0) {
			$line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
			mysqli_stmt_close($stmt2);
			mysqli_close($db);
			return $line2["Point_ID"];
		}
		mysqli_stmt_close($stmt2);
	}
	mysqli_stmt_close($stmt1);
	mysqli_close($db);
	return 0;
}

function Berechtigung($Baum_ID, $Server_ID) {
	$Resultat = 0;
	$db = db_connect();
	//Wenn es sich um den Eigentuemer handelt, dann hat er die Berechtigung
	$query="SELECT `owner`, `Path` FROM `Baum` WHERE `Baum_ID` = ? AND Server_ID = ?;";	
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line1 = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	if(intval($line1["owner"]) != intval($_SESSION['User_ID'])) {
		$query="SELECT `Path` FROM `User_Path` WHERE `User_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "i", $_SESSION['User_ID']);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			if(strlen($line["Path"]) <= strlen($line1["Path"])) {
				if($line["Path"] == substr($line1["Path"], 0, strlen($line["Path"]))) {
					if($_SESSION['Admin'] == 1) {
						$Resultat = 1;
					} else {
						$Resultat = 2;
					}
				}
			}
		}
		mysqli_stmt_close($stmt);
	} else {
		$Resultat = 1;
	}
	mysqli_close($db);
	return $Resultat;
}

function Translate($Dokument) {
	$db = db_connect();
	$query = "SELECT `Nummer`, `Text_".$_SESSION['Sprache']."` AS `Text` FROM `Translation` WHERE `Dokument` = ? ORDER BY `Nummer` ASC;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "s", $Dokument);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Text = array();
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		//$Text1["Text"] = $line["Text"];
		$Text[$line["Nummer"]] = $line["Text"]; 
	}
	mysqli_stmt_close($stmt);
	mysqli_close($db);
	return $Text;
}

function Kol_schreiben($query) {
	include('./conf_DH_schreiben.php');
	if($_SESSION['V_Daten'] == "_" || $_SESSION['V_Daten'] == null || $_SESSION['V_Daten'] == NULL) {
		$_SESSION['V_Daten'] = Kol_verbinden();
	}
	for ($i=0;$i<count($_SESSION['V_Daten']);$i++){
		if($_SESSION['V_Daten'][$i]["Database"] > "") {
			$Verb = mysqli_connect($_SESSION['V_Daten'][$i]["IP"],$_SESSION['V_Daten'][$i]["User"],$_SESSION['V_Daten'][$i]["Password"],$_SESSION['V_Daten'][$i]["Database"]);
			if ($Verb == false) {
				$_SESSION['V_Daten'][$i]["Database"] = "";
			} else {
				mysqli_query($Verb, 'set character set utf8;');
				$query = str_replace("\'","'",$query);
				$result = mysqli_query($Verb, $query);
				mysqli_close($Verb);
			}
		} else {
			//In den Puffer schreiben
			$query = str_replace("'","\'",$query);
			$SQL = "INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('".$_SESSION['V_Daten'][$i]["IP"]."', '".$query."');";
			$stmt = mysqli_prepare($dbDH, $SQL);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		}
	}
}

function uKol_schreiben($Start,$query,$Typen,$Ar) {
	$Insert_gebaut = 0;
	$db = db_connect();
	$query = str_replace("\'","'",$query);
	$ID_Feld = "";
	$query1 = "";
	$Tabelle = "";
	$query1_gepuffert = 0;
	if($_SESSION['uV_Daten'] == "_" || $_SESSION['uV_Daten'] == null || $_SESSION['uV_Daten'] == NULL) {
		$_SESSION['uV_Daten'] = uKol_verbinden();
	}
	for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
		if($_SESSION['uV_Daten'][$i]["Database"] > "") {
			$uVerb = mysqli_connect($_SESSION['uV_Daten'][$i]["IP"],$_SESSION['uV_Daten'][$i]["User"],$_SESSION['uV_Daten'][$i]["Password"],$_SESSION['uV_Daten'][$i]["Database"]);
			if ($uVerb == false) {
				$_SESSION['uV_Daten'][$i]["Database"] = "";
			} else {
				mysqli_query($uVerb, 'set character set utf8;');
				//ggf den unveraenderten Datensatz zuerst in die Baumhistorie schreiben
				if(strtolower(substr($query,0,6)) == "update") {
					if($Tabelle == "") {
						$pos = 7;
						if(substr($query, $pos, 1) == "`") {$pos = $pos + 1;}
						while(substr($query, $pos, 1) != " " and substr($query, $pos, 1) != "`") {
							$Tabelle = $Tabelle.substr($query, $pos, 1);
							$pos = $pos + 1;
						}
						if($Tabelle == "Baum" and strpos($query, "geloescht") == 0 and $Start == 1) {
							$Bedingung = substr($query, strpos(strtolower($query), "where"));
							$query1 = "SELECT * FROM `Baum` ".$Bedingung;
							$Ar1 = $Ar[count($Ar)-2];
							$Ar2 = $Ar[count($Ar)-1];
							$query1 = substr($query1, 0, strpos($query1, "?")).$Ar1.substr($query1, strpos($query1, "?") + 1,strlen($query1));
							$query1 = substr($query1, 0, strpos($query1, "?")).$Ar2.substr($query1, strpos($query1, "?") + 1,strlen($query1));
							$stmt1 = mysqli_prepare($uVerb,$query1);
							mysqli_stmt_execute($stmt1);
							$result = mysqli_stmt_get_result($stmt1);
							$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
							$abfrage = "SELECT `Inhalt`, column_list(`Inhalt`) FROM `Baum` ".$Bedingung;
							$Ar1 = $Ar[count($Ar)-2];
							$Ar2 = $Ar[count($Ar)-1];
							$abfrage = substr($abfrage, 0, strpos($abfrage, "?")).$Ar1.substr($abfrage, strpos($abfrage, "?") + 1,strlen($abfrage));
							$abfrage = substr($abfrage, 0, strpos($abfrage, "?")).$Ar2.substr($abfrage, strpos($abfrage, "?") + 1,strlen($abfrage));
							$req = mysqli_query($uVerb,$abfrage);
							$linetemp = mysqli_fetch_array($req, MYSQLI_ASSOC);
							$Text = $linetemp["column_list(`Inhalt`)"];
							$dynFeld = explode("`,`", $Text);
							for($x=0; $x < count($dynFeld); $x++) {
								$dynFeld[$x] = str_replace("`", "", $dynFeld[$x]);
							}
							$query2 = "SELECT ";
							for($x=0; $x < count($dynFeld); $x++) {
								$query2 = $query2."column_get(`Inhalt`, '".$dynFeld[$x]."' as CHAR) as `".$dynFeld[$x]."`, ";
							}
							$query2 = substr($query2, 0, strlen($query2) - 2);
							$query2 = $query2." FROM `Baum` ".$Bedingung;
							$Ar1 = $Ar[count($Ar)-2];
							$Ar2 = $Ar[count($Ar)-1];
							$query2 = substr($query2, 0, strpos($query2, "?")).$Ar1.substr($query2, strpos($query2, "?") + 1,strlen($query2));
							$query2 = substr($query2, 0, strpos($query2, "?")).$Ar2.substr($query2, strpos($query2, "?") + 1,strlen($query2));
							$stmt2 = mysqli_prepare($uVerb,$query2);
							mysqli_stmt_execute($stmt2);
							$result2 = mysqli_stmt_get_result($stmt2);
							$line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
							$query1 = "INSERT INTO `Baumhistorie` (`Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Path`, `owner`, `Bezeichnung`, `Vorlage`, `Inhalt`) VALUES ('".$line["Timestamp"]."', ".$line["Server_ID"].", ".$line["Baum_ID"].", '".$line["Eltern_ID"]."', '".$line["Path"]."', ".$line["owner"].", '".$line["Bezeichnung"]."', ".$line["Vorlage"].", COLUMN_CREATE(";
							for($x=0; $x < count($dynFeld); $x++) {
								$query1 = $query1."'".$dynFeld[$x]."', '".$line2[$dynFeld[$x]]."', ";
							}
							$query1 = substr($query1, 0, strlen($query1) - 2);
							$query1 = $query1."));";
							mysqli_stmt_close($stmt2);
						}
					}
				} else {
					if(strtolower(substr($query,0,6)) == "insert") {
						if($Tabelle == "") {
							$pos = 12;
							if(substr($query, $pos, 1) == "`") {$pos = $pos + 1;}
							while(substr($query, $pos, 1) != " " and substr($query, $pos, 1) != "`") {
								$Tabelle = $Tabelle.substr($query, $pos, 1);
								$pos = $pos + 1;
							}
						}
						if($Tabelle == "Baum" and $Start == 1 and $Insert_gebaut == 0) {
							$query2 = "SELECT MAX(`Baum_ID`) AS `ID` FROM `Baum`;";
							$stmt2 = mysqli_prepare($uVerb,$query2);
							mysqli_stmt_execute($stmt2);
							$result2 = mysqli_stmt_get_result($stmt2);
							$line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
							mysqli_stmt_close($stmt2);
							$Baum_ID = intval($line2["ID"]) + 1;
							$links = "INSERT INTO Baum (`Baum_ID`, ";
							$pos = strpos($query,"(") + 1;
							$pos2 = strpos($query,"VALUES (") + 8;
							$Mitte = substr($query, $pos,$pos2 - $pos);
							$rechts = substr($query, $pos2);
							$query = $links.$Mitte.$Baum_ID.", ".$rechts;
							$Insert_gebaut = 1;
						}
					}
				}
				$stmt = mysqli_prepare($uVerb,$query);
				if($Typen > "") {mysqli_stmt_bind_param($stmt, $Typen, ...$Ar);}
				mysqli_stmt_execute($stmt);
				$Zeilen = mysqli_affected_rows($uVerb);
				if($query1 > "") {
					$stmt = mysqli_prepare($uVerb,$query1);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				}
				mysqli_close($uVerb);
			}
		} else {
			//In den Puffer schreiben
			$query = str_replace("'","\'",$query);
			for ($x=0;$x<count($Ar);$x++){
				if($Ar[$x] == null) {$Ar[$x] = "";}
			}
			$Ar = json_encode($Ar);
			$SQL = "INSERT INTO `Puffer` (`Server`, `SQL_Text`, `Typen`, `Ar`) VALUES ('".$_SESSION['uV_Daten'][$i]["IP"]."', '".str_replace("'","\'",$query1)."', '".$Typen."', '".$Ar."');";
			$stmt = mysqli_prepare($db, $SQL);
			mysqli_stmt_execute($stmt);
			$SQL = "INSERT INTO `Eingangspuffer` (`Server`, `SQL_Text`, `Typen`, `Ar`) VALUES ('".$_SESSION['uV_Daten'][$i]["IP"]."', '".str_replace("'","\'",$query1)."', '".$Typen."', '".$Ar."');";
			$stmt = mysqli_prepare($db, $SQL);
			mysqli_stmt_execute($stmt);
			if($query1_gepuffert == 0 and $query1 > "") {
				$SQL = "INSERT INTO `Eingangspuffer` (`Server`, `SQL_Text`, `Typen`, `Ar`) VALUES ('".$_SESSION['uV_Daten'][$i]["IP"]."', '".str_replace("'","\'",$query1)."', '', '');";
				$stmt = mysqli_prepare($db, $SQL);
				try {
    				mysqli_stmt_execute($stmt);
				} catch (exception $e) {}
				$query1_gepuffert = 1;
			}
			mysqli_stmt_close($stmt);
		}
	}
	return $Zeilen;
}

function Kol_verbinden() {
	include('./conf_DH_schreiben.php');
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` ='Kollektiv';";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Eltern_ID = $line["Einstellung_ID"];
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Eltern_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Verb_daten = array();
	$Server = array();
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Verb = array();
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
		$stmt1 = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt1, "i", $line["Einstellung_ID"]);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
			$Verb[$line1["Parameter"]] = $line1["Wert"];
		}
		$Verb_daten[] = $Verb;
	}
	return $Verb_daten;
	mysqli_stmt_close($stmt);
	mysqli_stmt_close($stmt1);
}

function uKol_verbinden() {
	$db = db_connect();
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` ='Kollektiv';";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Eltern_ID = $line["Einstellung_ID"];
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "i", $Eltern_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Verb_daten1 = array();
	$Verb_daten = array();
	$Server = array();
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Verb = array();
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
		$stmt1 = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt1, "i", $line["Einstellung_ID"]);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
			$Verb[$line1["Parameter"]] = $line1["Wert"];
		}
		$Verb_daten1[] = $Verb;
	}
	$i = 0;
	while($i < count($Verb_daten1)) {
		if($Verb_daten1[$i]["Server_ID"] == $_SESSION["Server_ID"]) {
			$Verb_daten[] = $Verb_daten1[$i];
			array_splice($Verb_daten1,$i,1);
			$i = count($Verb_daten1) + 1;
		} else {
			$i = $i + 1;
		}
	}
	$i = 0;
	while($i < count($Verb_daten1)) {
		$Verb_daten[] = $Verb_daten1[$i];
		$i = $i + 1;
	}
	return $Verb_daten;
	mysqli_stmt_close($stmt);
	mysqli_stmt_close($stmt1);
}

//Benutzer abmelden
function resetUser() {
	$_SESSION['Hilfeliste'] = null;
	session_destroy();
	header( 'location: index.php' );
	exit;
}

function cleanInput() {
	// Maskierende Slashes aus POST Array entfernen
	//if (get_magic_quotes_gpc()) {
		$eingabe['benutzername'] = stripslashes( $_POST['benutzer'] );
		$eingabe['passwort']     = stripslashes( $_POST['passwort'] );
	//} else {
		//$eingabe['benutzername'] = $_POST['benutzer'];
		//$eingabe['passwort']     = $_POST['passwort'];
	//}
	// Trimmen
	$eingabe['benutzername'] = trim( $eingabe['benutzername'] );
	$eingabe['passwort']     = trim( $eingabe['passwort'] );
	// In Kleinschrift umwandeln
	$eingabe['benutzername'] = strtolower( $eingabe['benutzername'] );
	// Eingabe zurückgeben
	return $eingabe;
}

function loginUser($benutzer, $passwort, $db) {
	$_SESSION['Hilfeliste'] = null;
	$sql = "SELECT	`UserName`, `Password_extension`,`User_ID`, `Password` FROM	`User` WHERE (LOWER(`UserName`) = '" .mysqli_real_escape_string($db, $benutzer). "' OR LOWER(`eMail`) = '" .mysqli_real_escape_string($db, $benutzer). "') AND `activated` = 1;";
	$ergebnis = mysqli_query($db, $sql);
	// Wurde ein Datensatz gefunden, existiert dieser Benutzername, also prüfen wir ob die Anmeldedaten korrekt ist
	if (mysqli_num_rows($ergebnis) == 1) {
		$datensatz = mysqli_fetch_array( $ergebnis );
		$benutzer = strtolower($datensatz['UserName']);
		mysqli_free_result($ergebnis);
		// Anmeldepasswort vorbereiten
		$zusatz = $datensatz['Password_extension'];
		$anmeldepw = md5( $passwort.$zusatz );
		$_SESSION['DB_User']= strtolower($datensatz['UserName']);
		$_SESSION['DB_pwd']= $datensatz['Password'];
		$_SESSION['User_ID']= $datensatz['User_ID'];
		$sql = "SELECT `User_ID`, `mistrials` FROM `User` WHERE LOWER(`UserName`) = '" .mysqli_real_escape_string($db, $benutzer). "' AND `Password` = '" .mysqli_real_escape_string($db, $anmeldepw). "' AND `activated` = 1;";
		$ergebnis = mysqli_query($db, $sql);
		// Prüfen ob ein Datensatz gefunden wurde. In dem Fall stimmen die Anmeldedaten
		if (mysqli_num_rows($ergebnis) == 1) {
			$angriff = mysqli_fetch_array( $ergebnis );
			if ($angriff['mistrials'] != 0) {
				$sql = "UPDATE `User` SET `mistrials` = 0 WHERE LOWER(`UserName`) = ? LIMIT 1;";
				//uKol_schreiben(1, $sql, "s",[mysqli_real_escape_string($db, $benutzer)]);
				$stmt = mysqli_prepare($db,$sql);
				mysqli_stmt_bind_param($stmt, "s", mysqli_real_escape_string($db, $benutzer));
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
			//Server_ID ermitteln
			$sql = "SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'Server_ID';";
			$ergebnis = mysqli_query($db, $sql);
			$line = mysqli_fetch_array($ergebnis);
			$_SESSION["Server_ID"] = $line["Wert"];
			mysqli_free_result($ergebnis);
			// Korrekte Anmeldung zurückgeben
			return true;
		} else {
			// Das angegebene Passwort war nicht korrekt, also gehen wir von einem Angriffsversuch aus und erhöhen den Zaehler der fehlerhaften Anmeldeversuche
			$sql = "UPDATE	`User` SET `mistrials` = `mistrials` + 1 WHERE LOWER(`UserName`) = ?	LIMIT	1";
			//uKol_schreiben(1, $sql, "s",[mysqli_real_escape_string($db, $benutzer)]);
			$stmt = mysqli_prepare($db,$sql);
			mysqli_stmt_bind_param($stmt, "s", mysqli_real_escape_string($db, $benutzer));
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			// Abfragen ob das Limit von 5 Fehlversuche erreicht wurde und in diesem Fall ...
			$sql = "SELECT	`mistrials`	FROM `User`	WHERE	LOWER(`UserName`) = '" .mysqli_real_escape_string($db, $benutzer). "'";
			$ergebnis = mysqli_query($db, $sql);
			$anzahl = mysqli_fetch_array($ergebnis);
			mysqli_free_result( $ergebnis );
			// ... das Konto deaktivieren
			if ($anzahl['mistrials'] > 4) {
				$sql = "UPDATE	`User` SET `mistrials` = 0, `activated` = 0 WHERE LOWER(`UserName`) = ?	LIMIT	1;";
				//uKol_schreiben(1, $sql, "s",[mysqli_real_escape_string($db, $benutzer)]);
				$stmt = mysqli_prepare($db,$sql);
				mysqli_stmt_bind_param($stmt, "s", mysqli_real_escape_string($db, $benutzer));
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
		}
	}
}

function updateUser($benutzer, $db) {
	$sql = "UPDATE `User` SET `ip` = ?, `User_info` = ?, `login` = ?, `last_active` = NOW() WHERE LOWER(`UserName`) = ? LIMIT 1;";
	//$Zeilen = uKol_schreiben(1, $sql, "ssss",[mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']), mysqli_real_escape_string($db, $_SERVER['HTTP_USER_AGENT']), mysqli_real_escape_string($db, md5($_SERVER['REQUEST_TIME'])), mysqli_real_escape_string($db, $benutzer)]);
	$stmt = mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt, "ssss", mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']), mysqli_real_escape_string($db, $_SERVER['HTTP_USER_AGENT']), mysqli_real_escape_string($db, md5($_SERVER['REQUEST_TIME'])), mysqli_real_escape_string($db, $benutzer));
	mysqli_stmt_execute($stmt);
	$Zeilen = $stmt->affected_rows;
	mysqli_stmt_close($stmt);
	// Prüfen ob der Datensatz aktualisiert wurde
	if ($Zeilen == 1) {
		$_SESSION['angemeldet']   = true;
		$_SESSION['benutzername'] = $benutzer;
		$_SESSION['anmeldung']    = md5( $_SERVER['REQUEST_TIME'] );
		return true;
	}
}

function checkUser($db) {
	// Alte Session löschen und Sessiondaten in neue Session transferieren 
	//session_regenerate_id( true );
	if ($_SESSION['angemeldet'] !== true) return false;
	// Benutzerdaten aus DB laden 
	$sql = "SELECT `Sprache`, `ip`, `User_info`, `login`, `Admin`, `Full_Name`, UNIX_TIMESTAMP(`last_active`) as last_active, Thema FROM `User` WHERE `UserName` = '" .mysqli_real_escape_string($db, $_SESSION['benutzername']). "' AND `activated` = 1;";
	$ergebnis = mysqli_query($db, $sql);
	if (mysqli_num_rows( $ergebnis ) == 1)	{
		$benutzerdaten = mysqli_fetch_array( $ergebnis );
		// Resourcen freigeben 
		mysqli_free_result( $ergebnis );
		$_SESSION['Full_Name'] = $benutzerdaten['Full_Name'];
		$_SESSION['Admin'] = $benutzerdaten['Admin'];
		$_SESSION['admin'] = $benutzerdaten['Admin'];
		$_SESSION['Thema'] = $benutzerdaten['Thema'];
		$_SESSION['Sprache'] = $benutzerdaten['Sprache'];
		// Daten aus der DB mit den Benutzerdaten vergleichen
		if ($benutzerdaten['ip'] != $_SERVER['REMOTE_ADDR']) return false;
		if ($benutzerdaten['User_info'] != $_SERVER['HTTP_USER_AGENT']) return false;
		if ($benutzerdaten['login'] != $_SESSION['anmeldung']) {
			//return false;
			$_SESSION['anmeldung'] = $benutzerdaten['login'];
		}
//		if (($benutzerdaten['last_active'] + 14400) <= $_SERVER['REQUEST_TIME']) return false;
	} else {
		return false;
	}
	// Wenn die Benutzerdaten in Ordnung sind, dann die letzte Aktivität aktualisieren 
	$sql = "UPDATE `User` SET `last_active` = NOW() WHERE LOWER(`UserName`) = ? LIMIT 1;";
	//uKol_schreiben(1, $sql, "s",[mysqli_real_escape_string($db, $_SESSION['benutzername'])]);
	$stmt = mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt, "s", mysqli_real_escape_string($db, $_SESSION['benutzername']));
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	//mysqli_query($db, $sql);
	// Status zurückgeben 
	return true;
}

?>