<?php
require 'conf_unidb.php';

//wiederherstellen
if ($Aktion == "wiederherstellen"){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$query = "UPDATE `Baum` SET `geloescht` = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "iii",[0, $Baum_ID, $Server_ID]);
}

//Version aus Historie entfernen
if($Aktion == "Hist_löschen") {
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
	$Timestamp = "";
}
	
//Modus=umbenennen
if ($Aktion == $Text[9]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$Bezeichnung = strip_tags($Bezeichnung);
	$query = "UPDATE `Baum` SET `Bezeichnung` = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sii",[$Bezeichnung, $Baum_ID, $Server_ID]);
}

//neues Bild speichern
if ($Aktion == $Text[22]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['User_ID']);
	if ($Pfad == "/"){
		$Pfad = $Pfad.$Bezeichnung;
	} else {
		$Pfad = $Pfad."/".$Bezeichnung;
	}
	$query = "INSERT INTO Baum (Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt, `Server_ID`) VALUES (?, ?, ?, ?, ?, COLUMN_CREATE('Inhalt', '', 'Hintergrundbild', ''), ?);";
	uKol_schreiben(1,$query, "sissii",[$Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $_SESSION["Server_ID"]]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' AS CHAR) AS `Inhalt`, column_get(`Inhalt`, 'Tags_Pfad' AS CHAR) AS `Tags_Pfad`, column_get(`Inhalt`, 'Hintergrundbild' AS CHAR) AS `Hintergrundbild` FROM `Baum` WHERE `Server_ID` = ? AND `Baum_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $Server_ID, $original);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'Inhalt','".$line_orig["Inhalt"]."') WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "i", [$Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundbild', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si", [$line_orig["Hintergrundbild"], $original]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si", [$line_orig["Tags_Pfad"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[htmlentities($Bed_Format), $Baum_ID]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neues AB speichern
if ($Aktion == $Text[32]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['User_ID']);
	if ($Pfad == "/"){
		$Pfad = $Pfad.$Bezeichnung;
	} else {
		$Pfad = $Pfad."/".$Bezeichnung;
	}
	$query = "INSERT INTO Baum (Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt, `Server_ID`) VALUES (?, ?, ?, ?, ?, COLUMN_CREATE('Inhalt', '', 'Spalten', ''), ?);";
	uKol_schreiben(1,$query, "sissii",[$Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $_SESSION["Server_ID"]]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' AS CHAR) AS `Inhalt`, column_get(`Inhalt`, 'Spalten' AS CHAR) AS `Spalten` FROM `Baum` WHERE `Server_ID` = ? AND `Baum_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $Server_ID, $original);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'Inhalt','".$line_orig["Inhalt"]."') WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "i", [$Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si", [$line_orig["Spalten"], $original]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[htmlentities($Bed_Format), $Baum_ID]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neue Abfrage speichern
if ($Aktion == $Text[21]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID,$_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO Baum (`Path`, `owner`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `Inhalt`, `Server_ID`) VALUES (?, ?, ?, ?, ?, COLUMN_CREATE('Inhalt', '', 'Datenbank', ''),?);";
	uKol_schreiben(1,$query, "sissii", [$Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $_SESSION["Server_ID"]]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'SQL' AS CHAR) AS `SQL`, column_get(`Inhalt`, 'Datenbank' AS CHAR) AS `Datenbank`, column_get(`Inhalt`, 'Beschreibung' AS CHAR) AS `Beschreibung` FROM `Baum` WHERE `Server_ID` = ? AND `Baum_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $Server_ID, $original);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'Datenbank','".$line_orig["Datenbank"]."') WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "i", [$Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'SQL', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si", [$line_orig["SQL"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Beschreibung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si", [$line_orig["Beschreibung"], $Baum_ID]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neue Notiz speichern
if ($Aktion == $Text[20]){
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['Server_ID']);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$Text = htmlentities($Notiz, ENT_QUOTES);
	$query = "INSERT INTO Baum (`Server_ID`, Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('html', ?));";
	uKol_schreiben(1,$query, "isissis", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $Text]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'html' AS CHAR) AS `html` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'html', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["html"], $Baum_ID, intval($_SESSION["Server_ID"])]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}
//neue Zeichnung speichern
if ($Aktion == $Text[30]){
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['Server_ID']);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$Zeichnung = htmlentities($Zeichnung, ENT_QUOTES);
	$SVG = htmlentities($SVG, ENT_QUOTES);
	$query = "INSERT INTO Baum (`Server_ID`, Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('Zeichnung', '', 'SVG', ''));";
	uKol_schreiben(1,$query, "isissi", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Zeichnung' AS CHAR) AS `Zeichnung`, column_get(`Inhalt`, 'SVG' AS CHAR) AS `SVG` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET Inhalt = COLUMN_ADD(Inhalt, 'Zeichnung', ?), Inhalt = COLUMN_ADD(Inhalt, 'SVG', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ssii", [$line_orig["Zeichnung"], $line_orig["SVG"], $Baum_ID, intval($_SESSION["Server_ID"])]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neuer Link speichern
if ($Aktion == $Text[10]){
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	$Pfad = Pfad_ermitteln($Eltern_ID,$_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO Baum (Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt, Server_ID) VALUES (?, ?, ?, ?, ?, COLUMN_CREATE('Ziel', ?), ?);";
	uKol_schreiben(1,$query, "sissisi", [$Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $Ziel, $_SESSION["Server_ID"]]);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neue Gruppe speichern?
if ($Aktion == $Text[23]){
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['User_ID']);
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO `Baum` (`Server_ID`, `Path`, `owner`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `Inhalt`) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('Tags_Pfad', '', 'Tags', ''));";
	uKol_schreiben(1,$query, "isissi", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID]);
	echo "funktioniert";
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Tags_Pfad' AS CHAR) AS `Tags_Pfad`, column_get(`Inhalt`, 'Tags' AS CHAR) AS `Tags` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tags"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tags_Pfad"], $Baum_ID, $_SESSION["Server_ID"]]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neuer Export speichern?
if ($Aktion == $Text[34]){
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['User_ID']);
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO `Baum` (`Server_ID`, `Path`, `owner`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `Inhalt`) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('Tags_Pfad', '', 'Tags', '', 'von', '', 'bis', '', 'Art', '', 'Verw', '', 'uTime', '', 'vt', '', 'verteilen', '', 'vt_interpol', ''));";
	uKol_schreiben(1,$query, "isissi", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'von' as CHAR(1024)) as `Von`, column_get(`Inhalt`, 'bis' as CHAR(1024)) as `Bis`, column_get(`Inhalt`, 'Tags_Pfad' AS CHAR) AS `Tags_Pfad`, column_get(`Inhalt`, 'Tags' AS CHAR) AS `Tags`, column_get(`Inhalt`, 'Art' as CHAR(1024)) as `Art`, column_get(`Inhalt`, 'uTime' as CHAR(1024)) as `uTime`, column_get(`Inhalt`, 'vt' as CHAR(1024)) as `vt`, column_get(`Inhalt`, 'vt_interpol' as CHAR(1024)) as `vt_interpol`, column_get(`Inhalt`, 'Verw' as CHAR(1024)) as `Verw`, column_get(`Inhalt`, 'verteilen' as CHAR(1024)) as `verteilen` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tags"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tags_Pfad"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'von', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Von, $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'bis', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Bis, $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Art', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Art"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Verw', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Verw"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'uTime', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["uTime"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'vt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["vt"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'vt_interpol', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["vt_interpol"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'verteilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["verteilen"], $Baum_ID, $_SESSION["Server_ID"]]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neue Webseite speichern?
if ($Aktion == $Text[24]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION['User_ID']);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
 	$query = "INSERT INTO Baum (`Server_ID`, Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('Inhalt', '', 'max_ID', ''));";
	uKol_schreiben(1,$query, "isissi", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' AS CHAR) AS `Inhalt`, column_get(`Inhalt`, 'max_ID' AS CHAR) AS `max_ID` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Inhalt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Inhalt"], $Baum_ID, intval($_SESSION["Server_ID"])]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'max_ID', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "iii", [$line_orig["max_ID"], $Baum_ID, intval($_SESSION["Server_ID"])]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//neue Tabellenkalkulation speichern
if($Aktion == "neu_Tabkalk_speichern") {
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO `Baum` (`Server_ID`, Path, owner, `Eltern_ID`,`Bezeichnung`, `Vorlage`, `Inhalt`) VALUES (?, ?, ?, ?, ?, 16, COLUMN_CREATE('data', '', 'config', '', 'Spalten', '', 'Tabs', '', 'Kommentare', '', 'mZellen', ''));";
	uKol_schreiben(1,$query, "isiss", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung]);
	if($original > "") {
		$query = "SELECT  MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Baum_ID = $line["Baum_ID"];
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Spaltenformate' as Char) as `Spaltenformate`, column_get(`Inhalt`, 'data' as Char) as `data`, column_get(`Inhalt`, 'config' as Char) as `config`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten`, column_get(`Inhalt`, 'Tabs' as Char) as `Tabs`, column_get(`Inhalt`, 'mZellen' as Char) as `mZellen`, column_get(`Inhalt`, 'Kommentare' as Char) as `Kommentare` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'data', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["data"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'config', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["config"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Spalten"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tabs', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tabs"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'mZellen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["mZellen"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Kommentare', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Kommentare"], $Baum_ID, $_SESSION["Server_ID"]]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	} else {
		header("location: ./unidb.html");
	}
}
	
//neue Tabelle speichern?
if ($Aktion == $Text[33]){
	$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO `Baum` (`Server_ID`, Path, owner, `Eltern_ID`,`Bezeichnung`, `Vorlage`, `Inhalt`) VALUES (?, ?, ?, ?, ?, 21, COLUMN_CREATE('Inhalt', ?));";
	uKol_schreiben(1,$query, "isisss", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Blatt]);
	if($original > "") {
		$query = "SELECT `Baum_ID` FROM `Baum` WHERE `Bezeichnung`= ? AND `Server_ID` = ? ORDER BY `Baum_ID` DESC LIMIT 1;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "si", $Bezeichnung, $_SESSION["Server_ID"]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Baum_ID=$line["Baum_ID"];
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Spaltenformate' as Char) as `Spaltenformate`, column_get(`Inhalt`, 'Blatt' as Char) as `Blatt`, column_get(`Inhalt`, 'Zeilen' as Char) as `Zeilen`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spaltenformate', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Spaltenformate"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Blatt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Blatt"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Zeilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Zeilen"], $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Spalten"], $Baum_ID, $_SESSION["Server_ID"]]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	} else {
		header("location: ./unidb.html");
	}
}

//neue Trendgruppe speichern
if ($Aktion == $Text[26]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO Baum (`Server_ID`,Path, owner, Eltern_ID, Bezeichnung, Vorlage, Inhalt) VALUES (?, ?, ?, ?, ?, ?, COLUMN_CREATE('Zeitspanne', '86400', 'Trend_1', '', 'Trend_2', '', 'Trend_3', '', 'Trend_4', ''));";
	uKol_schreiben(1,$query, "isissi", [$_SESSION["Server_ID"], $Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID]);
	if($original > "") {
		$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Baum_ID = $line["Baum_ID"];
		mysqli_stmt_close($stmt);
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Tags_Pfad' AS CHAR) AS `Tags_Pfad`, column_get(`Inhalt`, 'Inhalt' AS CHAR) AS `Inhalt`, column_get(`Inhalt`, 'Zeitspanne' AS CHAR) AS `Zeitspanne`, column_get(`Inhalt`, 'Trend_1' AS CHAR) AS `Trend_1`, column_get(`Inhalt`, 'Trend_2' AS CHAR) AS `Trend_2`, column_get(`Inhalt`, 'Trend_3' AS CHAR) AS `Trend_3`, column_get(`Inhalt`, 'Trend_4' AS CHAR) AS `Trend_4` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Zeitspanne', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Zeitspanne"], $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Trend_1', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Trend_1"], $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Trend_2', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Trend_2"], $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Trend_3', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Trend_3"], $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Trend_4', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Trend_4"], $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$line_orig["Tags_Pfad"], $Baum_ID, $Server_ID]);
	}
	mysqli_close($db);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//editierte Abfrage speichern
if($Aktion == $Text[12]) {
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$Bezeichnung = htmlentities($Bezeichnung);
	$Beschreibung = htmlentities($Beschreibung);
	$SQL = htmlspecialchars($SQL, ENT_QUOTES);
	$SQL = htmlentities($SQL);
	$query="UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`,'Datenbank','".$Datenbank."') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sii", [$Bezeichnung, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'Beschreibung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Beschreibung, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'SQL', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$SQL, $Baum_ID, $Server_ID]);
}

//editierter Link speichern
if ($Aktion == $Text[13]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$Bezeichnung = strip_tags($Bezeichnung);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Ziel', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Ziel, $Baum_ID, $Server_ID]);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//editierte Gruppe speichern
if ($Aktion == $Text[14]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	//echo $Bezeichnung."<br>".$Tagliste."<br>".$Tags_Pfad."<br>";
	$Bezeichnung = strip_tags($Bezeichnung);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Tags_Pfad, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Tagliste, $Baum_ID, $Server_ID]);
}

//editierter Export speichern
if ($Aktion == $Text[35]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	//echo $Bezeichnung."<br>".$Tagliste."<br>".$Tags_Pfad."<br>";
	$Bezeichnung = strip_tags($Bezeichnung);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Tags_Pfad, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Tagliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'von', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Von, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'bis', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Bis, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Art', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Artliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'Verw', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Verwliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'uTime', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$uTimeliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'vt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$vtliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'vt_interpol', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$vt_interpolliste, $Baum_ID, $Server_ID]);
	$query = "UPDATE Baum SET Inhalt = COLUMN_ADD(Inhalt, 'verteilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Verteilen, $Baum_ID, $Server_ID]);
}

//editierte Notiz speichern
if ($Aktion == $Text[15]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$Bezeichnung = strip_tags($Bezeichnung);
	$Inhalt = htmlentities($Notiz);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'html', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Inhalt, $Baum_ID, $Server_ID]);
}

//editierte Tabelle_speichern
if ($Aktion == $Text[17]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$query = "UPDATE `Baum` SET `Bezeichnung`= ?, `Inhalt` = COLUMN_Add(`Inhalt`, 'Spaltenformate', ?), `Inhalt` = COLUMN_Add(`Inhalt`, 'Blatt', ?), `Inhalt` = COLUMN_Add(`Inhalt`, 'Zeilen', ?), `Inhalt` = COLUMN_Add(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sssiiii", [$Bezeichnung, $Spaltenformate, $Blatt, $Zeilen, $Spalten, $Baum_ID, $Server_ID]);
}

//editierte Tabellenkalkulation speichern
if ($Aktion == "edit_Tabkalk_speichern"){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$query = "UPDATE Baum SET Bezeichnung = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sii", [$Bezeichnung, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'data', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	$data = str_replace("'", "@@@", $data);
//	$data = str_replace('"', "µµµ", $data);
	uKol_schreiben(0,$query, "sii", [$data, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'config', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$config, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Spalten, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'mZellen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$mZellen, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Kommentare', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	$Kommentare = str_replace("'", "@@@", $Kommentare);
//	$Kommentare = str_replace('"', "µµµ", $Kommentare);
	uKol_schreiben(0,$query, "sii", [$Kommentare, $Baum_ID, $Server_ID]);
	$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tabs', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii", [$Tabs, $Baum_ID, $Server_ID]);
}

//löschen
if ($Aktion == $Text[18]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$query="SELECT count(`Baum_ID`) as Anzahl FROM `Baum` WHERE `Eltern_ID` = ? AND `geloescht` = 0;";
	$stmt = mysqli_prepare($db,$query);
	$ElternID = strval($Server_ID)."_".strval($Baum_ID);
	mysqli_stmt_bind_param($stmt, "s", $ElternID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	if ($line["Anzahl"]==0){
		// neu: Nur eine Löschmarkierung setzen
		$query = "UPDATE `Baum` SET `geloescht` = 1 WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ii", [$Baum_ID, $Server_ID]);
	}else {
		echo "<br><br><b>Der Eintrag kann nicht gelöscht werden, da ihm noch weitere Einträge untergeordnet sind.</b><br><br>";
	}
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

//verschieben
if ($Aktion == "verschieben"){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	if($Bezeichnung == "") {$Bezeichnung = strip_tags($_SESSION['Bezeichnung']);}
	$Pfad = Pfad_ermitteln($Eltern_ID,$_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query="UPDATE `Baum` SET `Eltern_ID` = ?,`Path` = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Eltern_ID, $Pfad, $Baum_ID, $Server_ID]);
	if ($mobil == 1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}
?>