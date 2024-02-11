<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Bericht_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
include '../mobil.php';
$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
if($original > "") {$Aktion = $Text[1];}
//neuer Bericht speichern
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
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Seiteneinstellungen' as CHAR) as `Seiteneinstellungen`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Faktor' as CHAR) as `Faktor` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_orig = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenbank', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Datenbank"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenquelle', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Datenquelle"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Seiteneinstellungen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Seiteneinstellungen"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["JS"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Headererweiterung"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["Hintergrundfarbe"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["DB_Bereich"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[$line_orig["bed_Format"], $Baum_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Faktor', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ".$_SESSION["Server_ID"].";";
		uKol_schreiben(0,$query, "si",[strval($line_orig["Faktor"]), $Baum_ID]);
	} else {
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Seiteneinstellungen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",["Groesse:DIN A4,Format:Hochformat,linker_Rand:20,rechter_Rand:10,oberer_Rand:10,unterer_Rand:10,beidseitig:0", $Baum_ID, $_SESSION["Server_ID"]]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'faktor', '3.635') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "ii",[$Baum_ID, $_SESSION["Server_ID"]]);
		$DB_Bereich = '<div id="deckblattbereich" style="display: none; z-index: 0; position: absolute; left: 0px; width: 100px; top: 0px; height: 140px; border: 1px dotted;"></div><div id="arbeitsbereich" style="z-index: 0; position: absolute; left: 0px; width: 100px; top: 0px; height: 140px; border: 1px dotted;">';
		$DB_Bereich = $DB_Bereich.'<div id="Seitenkopf" onclick="Markierungen_entfernen(this)" style="z-index: 1; position: absolute; height: 20px; left: 0px; top: 0px; border: 1px dotted;"><table style="width: 100%;height: inherit;"><tbody><tr valign="bottom"><td name="Seitenkopf_links" align="left">'.$Text[4].'</td><td name="Seitenkopf_mitte" style=" font-size: 16px;" align="center"></td><td name="Seitenkopf_rechts" align="right"></td></tr></tbody></table></div>';
		$DB_Bereich = $DB_Bereich.'<div id="Berichtskopf" onclick="Markierungen_entfernen(this)" ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 20px; border: 1px dotted; height: 20px;">'.$Text[5].'</div>';
		$DB_Bereich = $DB_Bereich.'<div id="Detailkopf" onclick="Markierungen_entfernen(this)" ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 40px; border: 1px dotted; height: 20px;">'.$Text[6].'</div>';
		$DB_Bereich = $DB_Bereich.'<div id="Detailbereich" onclick="Markierungen_entfernen(this)" ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 60px; border: 1px dotted; height: 20px;">'.$Text[9].'</div>';
		$DB_Bereich = $DB_Bereich.'<div id="Detailfuss" onclick="Markierungen_entfernen(this)" ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 80px; border: 1px dotted; height: 20px;">'.$Text[7].'</div>';
		$DB_Bereich = $DB_Bereich.'<div id="Berichtsfuss" onclick="Markierungen_entfernen(this)" ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 100px; border: 1px dotted; height: 20px;">'.$Text[8].'</div>';
		$DB_Bereich = $DB_Bereich.'<div id="Seitenfuss" onclick="Markierungen_entfernen(this) ondrop="drop(event)" ondragover="allowDrop(event)" style="z-index: 1; position: absolute; top: 120px; border: 1px dotted; height: 20px;"><table style="width: 100%; left: 0px; height: inherit;"><tbody><tr valign="bottom"><td name="Seitenkopf_links" align="left">'.$Text[10].'</td><td name="Seitenkopf_mitte" style=" font-size: 16px;" align="center"></td><td name="Seitenkopf_rechts" align="right"></td></tr></tbody></table></div></div>';
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($DB_Bereich), $Baum_ID, $_SESSION["Server_ID"]]);
	}
	mysqli_close($db);
	if ($mobil==1){
		header("location: ./Baum2.php");
	}else{
		header("location: ./unidb.html");
	}
}

echo "<form action='Bericht_neu.php' method='post' target='Hauptrahmen'>";
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
