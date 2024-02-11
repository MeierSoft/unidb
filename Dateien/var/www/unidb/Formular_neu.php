<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Text = Translate("Formular_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
include '../mobil.php';
$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
if($original > "") {$Aktion = $Text[1];}
//neues Formular speichern
if ($Aktion==$Text[1]){
	$Bezeichnung = strip_tags($Bezeichnung);
	$Pfad = Pfad_ermitteln($Eltern_ID, $_SESSION["Server_ID"]);
	if ($Pfad == "/"){$Pfad = $Pfad.$Bezeichnung;}
	$query = "INSERT INTO `Baum` (`Path`, `owner`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `Inhalt`, `Server_ID`) VALUES (?, ?, NULL, ?, ?, ?, COLUMN_CREATE('Inhalt', '', 'Datenbank', ''), ?);";
	uKol_schreiben(0,$query, "sissii", [$Pfad, $_SESSION['User_ID'], $Eltern_ID, $Bezeichnung, $Vorlage_ID, $_SESSION["Server_ID"]]);
	$query = "SELECT MAX(`Baum_ID`) AS `Baum_ID` FROM `Baum` WHERE `Server_ID` = ".$_SESSION["Server_ID"].";";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Baum_ID = $line["Baum_ID"];
	mysqli_stmt_close($stmt);
	if($original > "") {
		$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Darstellung' as CHAR) as `Darstellung`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Tabellenzeilen' as CHAR) as `Tabellenzeilen`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Navigationsbereich' as CHAR) as `Navigationsbereich`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'current' as CHAR) as `current`, column_get(`Inhalt`, 'Replikation' as CHAR) as `Replikation` FROM `Baum` WHERE `Server_ID` = ? AND `Baum_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $Server_ID, $original);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenbank', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(1,$query, "si",[$line_orig["Datenbank"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenquelle', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Datenquelle"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Darstellung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Darstellung"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["JS"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tabellenzeilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Tabellenzeilen"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Headererweiterung"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Hintergrundfarbe"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Navigationsbereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Navigationsbereich"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["DB_Bereich"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["bed_Format"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'current', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["current"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Replikation', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "ii",[$line_orig["Replikation"], $Baum_ID]);
		
		//Benutzerdefinierte Filter kopieren
		$query = "SELECT * FROM `Userdef_Filter` WHERE `Baum_ID`=".$original." AND `Server_ID` = ".$Server_ID.";";
		$stmt1 = mysqli_prepare($db, $query);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		while($line = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
			$query = "INSERT INTO `Userdef_Filter` (`Server_ID`, `User_ID`, `Baum_ID`, `Filtername`, `Filtertext`) Values (?, ?, ?, ?, ?);";
			uKol_schreiben(1,$query, "iiiss",[$Server_ID, $line["User_ID"], $Baum_ID, $line["Filtername"], $line["Filtertext"]]);
		}
		mysqli_stmt_close($stmt1);
	} else {
		$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_CREATE('Datenbank', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "sii",[htmlentities($Datenbank), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenquelle', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Datenquelle), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Darstellung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Darstellung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($JS), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tabellenzeilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Tabellenzeilen), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Headererweiterung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Hintergrundfarbe), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Navigationsbereich', 1) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', '') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', '') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'current', '') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Replikation', '') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $Server_ID]);
	}
	mysqli_close($db);
	if ($mobil==1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

echo "<form action='Formular_neu.php' method='post' target='Hauptrahmen'>";
echo "<input value='".$Text[2]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[3]."' type='reset' name='abbrechen'>";
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
echo "<input value='".$Bezeichnung."' type='hidden' name='Bezeichnung'>";
echo "<input value='".$original."' type='hidden' name='original'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "</form>";
mysqli_close($db);
?>
</body>
</html>
