<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">
<script src="./scripts/jquery-3.6.0.js"></script>
<link href="Navigation.css" rel="stylesheet">
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./colresizable/colResizable-1.6.min.js"></script>
<script src="./Bericht.js"></script>
<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css">
<script type="text/javascript" src="./scripts/jquery-ui.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Bericht.php");
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[10];
	exit;
}
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
//Bericht einlesen
$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Faktor' as CHAR) as `Faktor`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Seiteneinstellungen' as CHAR) as `Seiteneinstellungen`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_stmt_close($stmt);
$JS = html_entity_decode($line["JS"]);
$Headererweiterung = html_entity_decode($line["Headererweiterung"]);
$bed_Format = html_entity_decode($line["bed_Format"]);
$Hintergrundfarbe = $line["Hintergrundfarbe"];
$Bei_Start = html_entity_decode($line["Bei_Start"]);
$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
$Datenquelle = html_entity_decode($line["Datenquelle"]);
$Datenquelle = str_replace("\'", "'", $Datenquelle);
$DB_Bereich = html_entity_decode($line["DB_Bereich"]);
$Seiteneinstellungen = $line["Seiteneinstellungen"];
$Faktor = $line["Faktor"];
if($Faktor == "") {$Faktor = 1;}
echo $Headererweiterung;
echo "\n<title>".html_entity_decode($line["Bezeichnung"])."</title>\n";
echo "\n</head>\n<body class='allgemein'>\n";
include 'mobil.php';
$_SESSION['Bericht_Datenquelle'] = $Datenquelle;
$_SESSION['Datenbank'] = $line["Datenbank"]; 
$Datenbank = $line["Datenbank"];
$_SESSION['Form_DB'] = $Datenbank;
$_SESSION['DB_DB'] = $Datenbank;

echo "<form id='Einstellungen' name='Einstellungen' action='./Bericht.php' method='post' target='_self'>\n";
echo "<input id='benutzer' name='Benutzer' type='hidden' value='".$_SESSION['User_ID']."'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<input id='datenquelle' name='Datenquelle' type='hidden' value='".$Datenquelle."'>\n";
echo "<input id='datenbank' name='Datenbank' type='hidden' value='".$Datenbank."'>\n";
echo "<input id='faktor' name='Faktor' type='hidden' value='".$Faktor."'>\n";
echo "<input name='Baum_ID' id='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "<input name='Form_Filterliste' id='Form_Filterliste' value='".$Form_Filterliste."' type='hidden'>\n";
echo "<input id='js_code' name='JS' type='hidden' value='".$JS."'>\n";
echo "<input id='seiteneinstellungen' name='Seiteneinstellungen' type='hidden' value='".$Seiteneinstellungen."'>\n";
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
echo "<input id='aktion' name='Aktion' type='hidden' value=''>\n";
echo "<input id='vergr' value='' type='hidden'>\n";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "</form>\n";
//echo "<font size='2'>
echo "<table cellpadding='3px'><tr><td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('29');\">".$Text[1]."</a></td>\n";
if($anzeigen == 1) {
	if ($mobil==1){
		if($line["geloescht"] != 1) {echo "<td><a href='Bericht_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a></td>";}
	} else {
		if($line["geloescht"] != 1) {echo "<td><a href='Bericht_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></td>";}
	}
	if ($line["geloescht"] != 1) {
		echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[7]."</a></td>";
		echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a></td>";
		if ($mobil==1){
			echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[2]."</a></td>";
			if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[8]."</a></td>";}
		} else {
			echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[2]."</a></td>";
			if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[8]."</a></td>";}
		}
	} else {
		if ($mobil==1){
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[8]."</a></td>";
		} else {
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[8]."</a></td>";
		}
	}
}
//echo "<td width='10px'></td><td>Skalierung in %:<input class='Text_Element' id='vergr' value='' type='number' style='width: 50px;' onchange=\"Format_einstellen(1);\"></td>\n";
echo "<td width='10px'></td><td></td>\n";
echo "<td><input class='Schalter_fett_Element' value='|<' id='erste_seite' type='button' onclick='Seite_waehlen(\"erste\");'></td>\n";
echo "<td><input class='Schalter_fett_Element' value='<' id='zurueckg' type='button' onclick='Seite_waehlen(\"-\");'></td>\n";
echo "<td>Seite<input class='Text_Element' id='seite_akt' value='1' type='text' style='width: 20px;' onchange='Seite_waehlen(\" \");'>/<span id='anz_seiten'></span></td>\n";
echo "<td><input class='Schalter_fett_Element' value='>' id='vorg' type='button' onclick='Seite_waehlen(\"+\");'></td>\n";
echo "<td><input class='Schalter_fett_Element' value='>|' id='letzte_seite' type='button' onclick='Seite_waehlen(\"letzte\");'></td>\n";
echo "<td><input class='Schalter_Element' value='".$Text[9]."' id='pdf' type='button' onclick='pdf_erstellen();'></td></tr></table>\n";

$DB_Bereich = str_replace(' onclick="auswaehlen(this);"', '', $DB_Bereich);
$DB_Bereich = str_replace(' ontouchend="auswaehlen(this);"', '', $DB_Bereich);
$DB_Bereich = str_replace(' class="context-menu-two context-menu-active"', '', $DB_Bereich);
$DB_Bereich = str_replace(' class="context-menu-two"', '', $DB_Bereich);
$DB_Bereich = str_replace(' class="context-menu-three"', '', $DB_Bereich);
$DB_Bereich = str_replace(' draggable="true"', '', $DB_Bereich);
$DB_Bereich = str_replace(' ondragstart="drag(event)"', '', $DB_Bereich);
$DB_Bereich = str_replace(' class="context-menu-one"', '', $DB_Bereich);
$DB_Bereich = str_replace(' onclick1', ' onclick', $DB_Bereich);
$DB_Bereich = str_replace(' ondrop="drop(event);"', '', $DB_Bereich);
$DB_Bereich = str_replace(' ondragover="allowDrop(event);"', '', $DB_Bereich);
$DB_Bereich = str_replace(' cursor: crosshair;', '', $DB_Bereich);
mysqli_stmt_close($stmt);
echo "</form>\n";

$Hintergrund = "";
if(strlen($Hintergrundfarbe) > 0) {$Hintergrund = "background-color: ".$Hintergrundfarbe."; ";}
echo "<div id='db_Bereich' style='".$Hintergrund."position: absolute; left: 10px; top: 50px; border: solid; border-width: 1px; height: 400px; width: 300px;'>".$DB_Bereich."</div>";
echo "<form id='PDF_anfordern' name='PDF_anfordern' action='./PDF-Bericht.php' method='post' target='_blank'>\n";
echo "<input id='pdf_inhalt' name='PDF_Inhalt' type='hidden' value=''>\n";
echo "<input id='seit_eig' name='Seit_Eig' type='hidden' value=''>\n";
echo "</form>\n";
mysqli_close($db);
?>
</div>
<script type="text/javascript">
$(window).on('load',function() {
	Start();
<?php
	echo $Bei_Start."\n";
	echo "});\n\n";
	echo $JS."\n\n";
?>
</script>
</body>
</html>