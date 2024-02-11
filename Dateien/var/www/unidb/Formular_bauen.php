<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./scripts/jquery-3.6.0.js"></script>
<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css">
<script type="text/javascript" src="./scripts/jquery-ui.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<script type="text/javascript" src="./Formular_bauen.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel='StyleSheet' href='dtree.css' type='text/css' />
<script type='text/javascript' src='dtree.js'></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<style type="text/css">
#db_Bereich {
    width: 100%;
    height: 800px;
}
</style>
<?php
	include('Sitzung.php');
	include './mobil.php';
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("Formular_bauen.php");
	//editiertes Formular speichern
	if($Aktion==$Text[1]) {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[82];
			exit;
		}
		//zuerst die Einstellungen des Formulars
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenbank', ?), `Bezeichnung` = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$x = uKol_schreiben(1,$query, "ssii",[htmlentities($Datenbank), htmlentities($Bezeichnung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenquelle', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Datenquelle), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Darstellung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[$Darstellung, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($JS), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tabellenzeilen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Tabellenzeilen), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Headererweiterung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Hintergrundfarbe), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Navigationsbereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[$Navigationsbereich, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Bei_Start', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bei_Start), $Baum_ID, $Server_ID]);
		$DB_Bereich = str_replace("'","§§§", $DB_Bereich);
		$DB_Bereich = str_replace('"',"@@@", $DB_Bereich);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($DB_Bereich), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'current', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($current), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Replikation', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "iii",[$Replikation, $Baum_ID, $Server_ID]);
	}
	if($Aktion == "löschen") {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[82];
			exit;
		}
		$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
		$Timestamp = "";
	}
	//Formular einlesen
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[82];
		exit;
	}
	if ($Timestamp > ""){
		$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Darstellung' as CHAR) as `Darstellung`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Tabellenzeilen' as CHAR) as `Tabellenzeilen`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Navigationsbereich' as CHAR) as `Navigationsbereich`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'current' as CHAR) as `current`, column_get(`Inhalt`, 'Replikation' as CHAR) as `Replikation` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Darstellung' as CHAR) as `Darstellung`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Tabellenzeilen' as CHAR) as `Tabellenzeilen`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Navigationsbereich' as CHAR) as `Navigationsbereich`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'current' as CHAR) as `current`, column_get(`Inhalt`, 'Replikation' as CHAR) as `Replikation` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	mysqli_stmt_close($stmt);
	$Hintergrundfarbe = $line["Hintergrundfarbe"];
	$Darstellung = $line["Darstellung"];
	$bed_Format = html_entity_decode($line["bed_Format"]);
	$Datenquelle = html_entity_decode($line["Datenquelle"]);
	$Bei_Start = html_entity_decode($line["Bei_Start"]);
	$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
	$JS = html_entity_decode($line["JS"]);
	$Replikation = $line["Replikation"];
	$current = html_entity_decode($line["current"]);
	$Headererweiterung = html_entity_decode($line["Headererweiterung"]);
	$Datenquelle = str_replace(" class=\"\"", "", $Datenquelle);
	$Datenbank = html_entity_decode($line["Datenbank"]);
	$_SESSION['Datenbank'] = null;
	$Navigationsbereich = $line["Navigationsbereich"];
	$Tabellenzeilen = $line["Tabellenzeilen"];
	$DB_Bereich = html_entity_decode($line["DB_Bereich"]);
	$DB_Bereich = str_replace("§§§","'", $DB_Bereich);
	$DB_Bereich = str_replace("@@@",'"', $DB_Bereich);
	if(strlen($Tabellenzeilen) == 0) {$Tabellenzeilen = 15;}
	echo $Headererweiterung;
	echo "\n<title>".$Text[0]."</title>\n";
	echo "\n</head>\n";
	if(strlen($Hintergrundfarbe) > 0) {
		echo "<body class='allgemein' style='background-color: ".$Hintergrundfarbe.";'>\n";
	} else {
		echo "<body class='allgemein'>\n";
	}
	//Verbindung zur Datenbank herstellen
	$sqlhostname = "localhost";
	$db_Satz = mysqli_connect($sqlhostname,$_SESSION['DB_User'],$_SESSION['DB_pwd'],$Datenbank);
	mysqli_query($db_Satz, 'set character set utf8;');
	
	//Felder einlesen
	if(substr($Datenquelle,-1,1) == ";") {
		$query = substr($Datenquelle,0,strlen($Datenquelle)-1)." LIMIT 1;";
	} else {
		$query = $Datenquelle." LIMIT 1;";
	}
	$stmt1 = mysqli_prepare($db_Satz,$query);
	mysqli_stmt_execute($stmt1);
	$result = mysqli_stmt_get_result($stmt1);
	mysqli_stmt_close($stmt1);
	$Feldliste = "";
 	while ($finfo = mysqli_fetch_field($result)) {
 		$Feldliste = $Feldliste."@@@";
 		$Feldliste = $Feldliste.$finfo->name;
	}
	mysqli_free_result($result);

	//Verzeichnis mit Hintergrundbildern einlesen
	$Dateiliste = [];
	$Bildverzeichnis = "./Bild/";
	if (is_dir($Bildverzeichnis)){
		if ($handle = opendir($Bildverzeichnis)) {
			while (($file = readdir($handle)) !== false) {
				if($file !="." and $file !=".." and $file !="Hilfe") {
					$Dateiliste[] = $file;
				}
			}
			closedir($handle);
			sort($Dateiliste);
			$Dateien = "";
			$i = 0;
			while($i < count($Dateiliste)) {
				$Dateien = $Dateien.";".$Dateiliste[$i];
				$i = $i + 1;
			}
			$Dateien = substr($Dateien, 1, strlen($Dateien));
			$Hintergrundbilder = "<input type='hidden' id='bilderliste' name='Bildliste' value = '".$Dateien."'>\n";
		}
	}

	//Alle Elementvorlagen finden und auflisten
	$Typauswahl = "";
	$query="SELECT * FROM `Elementvorlagen` WHERE `Auswahl` = 1 AND `Formular` = 1;";
   $stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line_Elemente = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Typauswahl = $Typauswahl.$line_Elemente[$_SESSION['Sprache']].",".$line_Elemente['Elternelement'].";";
	}
	mysqli_stmt_close($stmt);
	$Typauswahl = substr($Typauswahl,0,-1);
	$Elementvorlagen = "<input id='Typauswahl' name='Typauswahl' value='".$Typauswahl."' type='hidden'>\n";

	//Versionsliste erstellen
	$Versionsliste = "";
	$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			$Versionen = 320;
			$Versionsliste = $Versionsliste."<td>".$Text[49].":&nbsp;<select name='Timestamp' id='timestamp' onchange='document.forms[\"Einstellungen\"].submit();'><option></option>";
			while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				if($line1["Timestamp"] == $Timestamp) {
					$Versionsliste = $Versionsliste."<option selected>".$line1["Timestamp"]."</option>";
				} else {
					$Versionsliste = $Versionsliste."<option>".$line1["Timestamp"]."</option>";
				}
			}
			$Versionsliste = $Versionsliste."</select></td>";
		}
		mysqli_stmt_close($stmt1);

	
	echo "<form id='Einstellungen' name='Einstellungen' action='./Formular_bauen.php' method='post' target='_self'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input id='hintergrundfarbe' name='Hintergrundfarbe' type='hidden' value='".$Hintergrundfarbe."'>\n";
	echo "<input id='navigationsbereich' name='Navigationsbereich' type='hidden' value='".$Navigationsbereich."'>\n";
	echo "<input id='headererweiterung' name='Headererweiterung' type='hidden' value='".$Headererweiterung."'>\n";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	$JS1 = str_replace("'", "§§§", $JS);
	$JS1 = str_replace('"', "@@@", $JS1);
	echo "<input id='js_code' name='JS' type='hidden' value='".$JS1."'>\n";
	$current1 = str_replace("'", "§§§", $current);
	$current1 = str_replace('"', "@@@", $current1);
	echo "<input id='current' name='current' type='hidden' value='".$current1."'>\n";
	echo "<input id='form_tabellenzeilen' name='Tabellenzeilen' type='hidden' value='".$Tabellenzeilen."'>\n";
	echo "<input id='form_replikation' name='Replikation' type='hidden' value='".$Replikation."'>\n";
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";
	echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";




	if($mobil == 1) {
		include ("./Formular_bauen_navi_mobil.php");
	} else {
		include ("./Formular_bauen_navi.php");
	}

	
	echo "<input id='Baum_ID' name='Baum_ID' type='hidden' value='".$Baum_ID."'>\n";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<input id='Bezeichnung' name='Bezeichnung' type='hidden' value='".$line["Bezeichnung"]."'>\n";
	echo "<input id='Datenquelle' name='Datenquelle' type='hidden' value='".$Datenquelle."'>\n";
	echo "<input id='bei_start' name='Bei_Start' type='hidden' value='".$Bei_Start."'>\n";
	echo "<input id='Datenbank' name='Datenbank' type='hidden' value='".$Datenbank."'>\n";
	echo "<input id='darstellung' name='Darstellung' type='hidden' value='".$Darstellung."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input id ='feldliste' name='Feldliste' value='".$Feldliste."' type='hidden'>\n";
	echo "<input id ='db_bereich' name='DB_Bereich' value='' type='hidden'>\n";
	echo $Hintergrundbilder;
	echo $Elementvorlagen;
	echo "</form>\n";
	$DB_Bereich = str_replace(" onclick=\"auswaehlen(this);\"", "", $DB_Bereich);
	$DB_Bereich = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $DB_Bereich);
	if ($mobil==1){
		$DB_Bereich = str_replace("<div", "<div ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<img", "<img ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<iframe", "<iframe ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<input", "<input ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<textarea", "<textarea ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<select", "<select ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
	}else{
		$DB_Bereich = str_replace("<div", "<div onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<img", "<img onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<iframe", "<iframe onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<input", "<input onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<textarea", "<textarea onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<select", "<select onclick=\"auswaehlen(this);\"", $DB_Bereich);
	}
	
	
	echo "<div id='db_Bereich' style='font-size: 12px; ondrop='drop(event)' ondragover='allowDrop(event)'>".$DB_Bereich;
	mysqli_stmt_close($stmt);
	mysqli_close($db);
	echo "</div>\n<script type='text/javascript'>\n";
	$JS = str_replace("§§§","'", $JS);
	$JS = str_replace("@@@",'"', $JS);
	echo $JS;
//	echo "\nfunction current() {\n";
//	$current = str_replace("§§§","'", $current);
//	$current = str_replace("@@@",'"', $current);
//	echo $current;
//	echo "\n}\n";
	echo "</script>\n";
?>
</body>
</html>
