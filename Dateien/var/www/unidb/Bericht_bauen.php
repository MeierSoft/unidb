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
<script type="text/javascript" src="./Bericht_bauen.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel='StyleSheet' href='dtree.css' type='text/css' />
<script type='text/javascript' src='dtree.js'></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
	include('./Sitzung.php');
	include './mobil.php';
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("Bericht_bauen.php");
	//Konstanten
	$DIN_A = array();
	$DIN_A[0]["Hoehe"] = 1189;
	$DIN_A[0]["Breite"] = 841;
	$DIN_A[1]["Hoehe"] = 841;
	$DIN_A[1]["Breite"] = 594;
	$DIN_A[2]["Hoehe"] = 594;
	$DIN_A[2]["Breite"] = 420;
	$DIN_A[3]["Hoehe"] = 420;
	$DIN_A[3]["Breite"] = 297;
	$DIN_A[4]["Hoehe"] = 297;
	$DIN_A[4]["Breite"] = 210;
	$DIN_A[5]["Hoehe"] = 210;
	$DIN_A[5]["Breite"] = 148;
	$DIN_A[6]["Hoehe"] = 148;
	$DIN_A[6]["Breite"] = 105;
	//editierter Bericht speichern
	if($Aktion==$Text[1]) {
		//zuerst die Einstellungen des Berichtes
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenbank', ?), `Bezeichnung` = ? WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ssii",[htmlentities($Datenbank), htmlentities($Bezeichnung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Datenquelle', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$Datenquelle = str_replace("'", "\'", $Datenquelle);
		uKol_schreiben(0,$query, "sii",[htmlentities($Datenquelle), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($JS), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Headererweiterung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Hintergrundfarbe), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Seiteneinstellungen', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[$Seiteneinstellungen, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Faktor', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[$Faktor, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Bei_Start', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bei_Start), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'DB_Bereich', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($DB_Bereich), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
	}
	if($Aktion == "löschen") {
		$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
		$Timestamp = "";
	}
	//Bericht einlesen
	if ($Timestamp > ""){
		$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Faktor' as CHAR) as `Faktor`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Seiteneinstellungen' as CHAR) as `Seiteneinstellungen`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Faktor' as CHAR) as `Faktor`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Seiteneinstellungen' as CHAR) as `Seiteneinstellungen`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	mysqli_stmt_close($stmt);
	$Hintergrundfarbe = $line["Hintergrundfarbe"];
	if($Hintergrundfarbe == null) {$Hintergrundfarbe = "#ffffff";}
	$bed_Format = html_entity_decode($line["bed_Format"]);
	$Datenquelle = html_entity_decode($line["Datenquelle"]);
	$Datenquelle = str_replace("\'", "'", $Datenquelle);
	$Bei_Start = html_entity_decode($line["Bei_Start"]);
	$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
	$JS = html_entity_decode($line["JS"]);
	$Headererweiterung = html_entity_decode($line["Headererweiterung"]);
	$Datenquelle = str_replace(" class=\"\"", "", $Datenquelle);
	$Datenbank = html_entity_decode($line["Datenbank"]);
	$Seiteneinstellungen = $line["Seiteneinstellungen"];
	$Faktor = $line["Faktor"];
	if($Faktor == "") {$Faktor = 1;}
	$DB_Bereich = html_entity_decode($line["DB_Bereich"]);
	if(strlen($Tabellenzeilen) == 0) {$Tabellenzeilen = 15;}
	echo $Headererweiterung;
	echo "\n<title>".$Text[0]."</title>\n";
	echo "\n</head>\n<body class='allgemein'>\n";
	//Verbindung zur Datenbank herstellen
	$sqlhostname = "localhost";
	$db_Satz = mysqli_connect($sqlhostname,$_SESSION['DB_User'],$_SESSION['DB_pwd'],$Datenbank) or die( $Text[2] );
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
    
	echo "<form id='Einstellungen' name='Einstellungen' action='./Bericht_bauen.php' method='post' target='_self'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
	echo "<input id='hintergrundfarbe' name='Hintergrundfarbe' type='hidden' value='".$Hintergrundfarbe."'>\n";
	echo "<input id='headererweiterung' name='Headererweiterung' type='hidden' value='".$Headererweiterung."'>\n";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	echo "<input id='js_code' name='JS' type='hidden' value='".$JS."'>\n";
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";
	echo "<input id='seiteneinstellungen' name='Seiteneinstellungen' type='hidden' value='".$Seiteneinstellungen."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<table style='position: absolute; top: 8px; left: 7px; height: 33px; background: #FCEDD9;' class='Text_einfach'>\n";
	echo "<tr><td>".$Text[3]."</td>\n";
	$Versionen = 0;
	if($geloescht == 1) {
		if ($mobil==1){
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[41]."</a></td>";
		} else {
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[41]."</a></td>";
		}
	} else {
		$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			$Versionen = 208;
			echo "<td>".$Text[42].":&nbsp;<select name='Timestamp' id='timestamp' onchange='document.forms[\"Einstellungen\"].submit();'><option></option>";
			while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				if($line1["Timestamp"] == $Timestamp) {
					echo "<option selected>".$line1["Timestamp"]."</option>";
				} else {
					echo "<option>".$line1["Timestamp"]."</option>";
				}
			}
			echo "</select></td>";
		}
		mysqli_stmt_close($stmt1);
		if ($Timestamp > ""){
			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[43]."</a>&nbsp;&nbsp;&nbsp;</td>";
			echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[44]."</a>&nbsp;&nbsp;&nbsp;</td>";
			echo "<td><input class='Schalter_Element' name='Bericht_einstellen' value='".$Text[8]."' type='button' onclick='Bericht_Dialog_oeffnen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[75]."' type='button' onclick='bed_Format_Dialog();'></td>\n";
			echo "<td><input style='display: block; width: 85px;' class='Schalter_Element' id='deckblatt_zeigen' name='Deckblatt_zeigen' value='".$Text[76]."' type='button' onclick='Deckblatt_umschalten(\"Deckblatt\");'></td>\n";
			echo "<td><input class='Schalter_Element' id='bericht_zeigen' name='Bericht_zeigen' value='".$Text[77]."' type='button' onclick='Deckblatt_umschalten(\"Bericht\");' style='display: none;'></td>\n";
			echo "</tr></table>\n";
			echo "<input id='aktion' name='Aktion' value='' type='hidden'>\n";
			echo "<input name='Raster' value='0' type='hidden'>\n";
			echo "<table style='position: absolute; top: 8px; left: 1000px;";
		} else {
			echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[7]."' type='submit' onclick='abspeichern();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bericht_einstellen' value='".$Text[8]."' type='button' onclick='Bericht_Dialog_oeffnen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[75]."' type='button' onclick='bed_Format_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Navigationsdialog' value='Navi' type='button' onclick='Navi_Dialog();'></td>\n";
			echo "<td><input style='display: block; width: 85px;' class='Schalter_Element' id='deckblatt_zeigen' name='Deckblatt_zeigen' value='".$Text[76]."' type='button' onclick='Deckblatt_umschalten(\"Deckblatt\");'></td>\n";
			echo "<td><input style='display: none;' class='Schalter_Element' id='bericht_zeigen' name='Bericht_zeigen' value='".$Text[77]."' type='button' onclick='Deckblatt_umschalten(\"Bericht\");'></td>\n";
			echo "</tr></table></div>\n";
			echo "<table style='position: absolute; top: 8px; left: ".($Versionen + 470)."px; height: 33px; background: #FCEDD9;' class='Text_einfach'><tr><td>".$Text[5]."</td><td><input class='Schalter_Element' name='erstellen' value='".$Text[9]."' type='button' onclick='Elementtyp_aussuchen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[10]."' type='button' onclick='Element_entfernen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='kopieren' value='".$Text[11]."' type='button' onclick='Element_kopieren();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[8]."' type='button' onclick='Element_Dialog_oeffnen();'></td>\n";
			echo "</tr></table>\n";
			echo "<table style='position: absolute; top: 8px; left: ".($Versionen + 810)."px; height: 33px; background: #FCEDD9;' class='Text_einfach'><tr><td>".$Text[4]."</td><td><input class='Text_Element' name='Raster' value='0' type='Text' size='3'></td>\n";
			echo "</tr></table>\n";
			echo "<table style='position: absolute; top: 8px; left: ".($Versionen + 900)."px;";
		}
	}
	echo " height: 33px; background: #FCEDD9;' class='Text_einfach'><tr><td>".$Text[78].":&nbsp;&nbsp;<input class='Text_Element' id='vergr' value='' type='number' style='width: 50px;' onchange=\"Format_einstellen(1);\"></td><td width='10px'></td><td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('30');\"></td>\n";
	echo "</tr></table>\n";
	echo "<input id='Baum_ID' name='Baum_ID' type='hidden' value='".$Baum_ID."'>\n";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<input id='Bezeichnung' name='Bezeichnung' type='hidden' value='".$line["Bezeichnung"]."'>\n";
	echo "<input id='Datenquelle' name='Datenquelle' type='hidden' value='".$Datenquelle."'>\n";
	echo "<input id='bei_start' name='Bei_Start' type='hidden' value='".$Bei_Start."'>\n";
	echo "<input id='Datenbank' name='Datenbank' type='hidden' value='".$Datenbank."'>\n";
	echo "<input id='faktor' name='Faktor' type='hidden' value='".$Faktor."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input id ='feldliste' name='Feldliste' value='".$Feldliste."' type='hidden'>\n";
	echo "<input id ='db_bereich' name='DB_Bereich' value='' type='hidden'>\n";
	//Alle Elementvorlagen finden und auflisten
	$Typauswahl = "";
	$query="SELECT * FROM `Elementvorlagen` WHERE `Auswahl` = 1 AND `Bericht` = 1;";
   $stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line_Elemente = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Typauswahl = $Typauswahl.$line_Elemente[$_SESSION['Sprache']].",".$line_Elemente['Elternelement'].";";
	}
	mysqli_stmt_close($stmt);
	$Typauswahl = substr($Typauswahl,0,-1);
	echo "<input id ='Typauswahl' name='Typauswahl' value='".$Typauswahl."' type='hidden'>\n";
	echo "</form>\n";
	$DB_Bereich = str_replace(" onclick=\"auswaehlen(this);\"", "", $DB_Bereich);
	$DB_Bereich = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $DB_Bereich);
	if ($mobil==1){
		$DB_Bereich = str_replace("<div", "<div ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<img", "<img ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<input", "<input ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<textarea", "<textarea ontouchend=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Seitenkopf\"", "<div id=\"Seitenkopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Berichtskopf\"", "<div id=\"Berichtskopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Detailkopf\"", "<div id=\"Detailkopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Detailbereich\"", "<div id=\"Detailbereich\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Detailfuss\"", "<div id=\"Detailfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Berichtsfuss\"", "<div id=\"Berichtsfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"Seitenfuss\"", "<div id=\"Seitenfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div ontouchend=\"auswaehlen(this);\" id=\"arbeitsbereich\"", "<div id=\"arbeitsbereich\"", $DB_Bereich);
	}else{
		$DB_Bereich = str_replace("<div", "<div onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<img", "<img onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Seitenkopf\"", "<div id=\"Seitenkopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Berichtskopf\"", "<div id=\"Berichtskopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Detailkopf\"", "<div id=\"Detailkopf\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Detailbereich\"", "<div id=\"Detailbereich\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Detailfuss\"", "<div id=\"Detailfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Berichtsfuss\"", "<div id=\"Berichtsfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"Seitenfuss\"", "<div id=\"Seitenfuss\"", $DB_Bereich);
		$DB_Bereich = str_replace("<div onclick=\"auswaehlen(this);\" id=\"arbeitsbereich\"", "<div id=\"arbeitsbereich\"", $DB_Bereich);
		$DB_Bereich = str_replace("<input", "<input onclick=\"auswaehlen(this);\"", $DB_Bereich);
		$DB_Bereich = str_replace("<textarea", "<textarea onclick=\"auswaehlen(this);\"", $DB_Bereich);
	}
	$Hintergrund = "";
	if(strlen($Hintergrundfarbe) > 0) {$Hintergrund = "background-color: ".$Hintergrundfarbe."; ";}
	echo "<div id='db_Bereich' style='".$Hintergrund."position: absolute; top: 50px; left: 10px; border: solid; border-width: 1px;' ondrop='drop(event)' ondragover='allowDrop(event)' onclick='Markierungen_entfernen(this)'>".$DB_Bereich;
	mysqli_stmt_close($stmt);
	mysqli_close($db);
	echo "</div>\n<script type='text/javascript'>\n";
	echo $JS;
	echo "</script>\n";
?>
</body>
</html>
