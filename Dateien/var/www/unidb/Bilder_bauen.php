<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<script type="text/javascript" src="./Bilder_bauen.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel='StyleSheet' href='dtree.css' type='text/css' />
<script type='text/javascript' src='dtree.js'></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/gauge.js"></script>
<style type="text/css">
#DH_Bereich {
    width: 100%;
    height: 800px;
}
.rectangle {
    border: 1px dotted #000000;
    position: absolute;
}
</style>

<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	if ($_SESSION['Zeitpunkt']=="") {
		$Zeitpunkt="jetzt";
	} else {
		$Zeitpunkt=$_SESSION['Zeitpunkt'];
	}
   include './mobil.php';
   include './conf_DH.php';
	header("X-XSS-Protection: 1");
	$Text = Translate("Bilder_bauen.php");
	if($Timestamp == $Text[101]) {$Timestamp = "";}
	//editiertes Bild speichern
	if($Aktion == "wiederherstellen") {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[102];
			exit;
		}
		$query = "UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`, 'Inhalt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Inhalt_orig, $Baum_ID, $Server_ID]);
		$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundbild', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Hintergrundbild, $Baum_ID, $Server_ID]);
		$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Tags_Pfad, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Hintergrundfarbe, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($JS), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Headererweiterung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Bei_Start', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bei_Start), $Baum_ID, $Server_ID]);
	}
	if($Aktion == $Text[6]) {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[102];
			exit;
		}
		$Inhalt=str_replace(" ontouchend=\"auswaehlen(this);\"","",$Inhalt);
		$Inhalt=str_replace(" onclick=\"auswaehlen(this);\"","",$Inhalt);
		$Inhalt_orig = stripslashes($Inhalt);
		$Inhalt=str_replace("\r\n\r\n","",$Inhalt);
		$Inhalt=str_replace("'","\'",$Inhalt);
		$Inhalt = htmlentities($Inhalt);
		$Bezeichnung = htmlentities($Bezeichnung);
		$Bei_Start = html_entity_decode($Bei_Start);
		$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
		$JS = html_entity_decode($JS);
		//$Inhalt = mysqli_real_escape_string($db, $Inhalt);
		$query="UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`,'Inhalt','".$Inhalt."') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "sii", [$Bezeichnung, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundbild', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		//$Hintergrundbild = mysqli_real_escape_string($db, $Hintergrundbild);
		uKol_schreiben(0,$query, "sii", [$Hintergrundbild, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Tags_Pfad, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Hintergrundfarbe, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'JS', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($JS), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Headererweiterung', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Headererweiterung), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Bei_Start', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bei_Start), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
	}
	if($Aktion == "löschen") {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[102];
			exit;
		}
		$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
		$Timestamp = "";
	}
	//Bild einlesen
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[102];
		exit;
	}
	if ($Timestamp > ""){
		$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Hintergrundbild' as CHAR) as `Hintergrundbild`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR) as `Tags_Pfad`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Hintergrundbild' as CHAR) as `Hintergrundbild`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR) as `Tags_Pfad`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Bild = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Hintergrundfarbe = $line_Bild["Hintergrundfarbe"];
	$Headererweiterung = html_entity_decode($line_Bild["Headererweiterung"]);
	$JS = html_entity_decode($line_Bild["JS"]);
	echo $Headererweiterung;
	echo "\n<title>Bilder erstellen</title>\n";
	echo "</head>";

	if(strlen($Hintergrundfarbe) > 0) {
		echo "<body class='allgemein' style='background-color: ".$Hintergrundfarbe.";'>\n";
	} else {
		echo "<body class='allgemein'>\n";
	}
	$Bezeichnung = html_entity_decode($line_Bild["Bezeichnung"]);
	echo "<form action='Bilder_bauen.php' method='post' target='_self' name='phpform'>";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	$bed_Format = html_entity_decode($line_Bild["bed_Format"]);
	echo "<input name='Hintergrundbild' id='hintergrundbild' value='".$line_Bild['Hintergrundbild']."' type='hidden'>\n";
	echo "<input name='Hintergrundfarbe' id='hintergrundfarbe' value='".$Hintergrundfarbe."' type='hidden'>\n";
	echo "<input name='uebernehmen' value='".$Text[0]."' type='hidden'>\n";
	echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
	echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
	echo "<input type='hidden' id='inhalt' name='Inhalt_orig' value = '".$line_Bild["Inhalt"]."'>\n";
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";
	$JS1 = str_replace("'", "§§§", $JS);
	$JS1 = str_replace('"', "@@@", $JS1);
	echo "<input id='js_code' name='JS' type='hidden' value='".$JS1."'>\n";
	echo "<input id='headererweiterung' name='Headererweiterung' type='hidden' value='".$Headererweiterung."'>\n";
	$Bei_Start = html_entity_decode($line_Bild["Bei_Start"]);
	$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
	echo "<input id='bei_start' name='Bei_Start' type='hidden' value='".$Bei_Start."'>\n";
	
	if ($mobil==1){
		echo "<input name='mobil' value='1' type='hidden'>\n";
	} else {
		echo "<input name='mobil' value='0' type='hidden'>\n";
	}
	echo "<input name='Bezeichnung' value='".$Bezeichnung."' type='hidden'>\n";
	echo "<input name='Tags_Pfad' value='".$line_Bild["Tags_Pfad"]."' type='hidden'>\n";

	//Multistates einlesen
	$query = "SELECT DISTINCT * FROM `Multistates` WHERE `User_ID` = ? OR `User_ID` = 0 ORDER BY `User_ID` DESC, `Gruppe` ASC;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $_SESSION['User_ID']);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Multistates = "";
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Multistates = $Multistates.";".$line["Multistate_ID"].",".$line["Gruppe"];
	}
	mysqli_stmt_close($stmt);
	$Dateien = substr($Multistates, 1, strlen($Multistates));
	echo "<input name='Multistates' value='".$Multistates."' id='multistates' type='hidden'>\n";
	//Ende Multistates einlesen
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
		$Versionsauswahl = "";
		$Versionsauswahl1 = "";
		if($line["geloescht"] != 1) {
			$query = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
			$stmt1 = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
			mysqli_stmt_execute($stmt1);
			$result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0) {
				$Versionsauswahl1 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Version</td><td>";
				$Versionsauswahl = $Versionsauswahl."<td><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();' style='width: 60px;'><option></option>";
				while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
					if($line1["Timestamp"] == $Timestamp) {
						$Versionsauswahl = $Versionsauswahl."<option selected>".$line1["Timestamp"]."</option>";
					} else {
						$Versionsauswahl = $Versionsauswahl."<option>".$line1["Timestamp"]."</option>";
					}
				}
				$Versionsauswahl = $Versionsauswahl."</select></td>";
			}
			mysqli_stmt_close($stmt1);
		}
	}
	if($mobil == 1) {
		include('./Bilder_bauen_navi_mobil.php');
	} else {
		include('./Bilder_bauen_navi.php');
	}

	echo "</font>";

	//Verzeichnis mit Hintergrundbildern einlesen
	$Dateiliste = [];
	$Bilderverzeichnis = "./Bilder/";
	if (is_dir($Bilderverzeichnis)){
		if ($handle = opendir($Bilderverzeichnis)) {
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
			echo "<input type='hidden' id='bilderliste' name='Bilderliste' value = '".$Dateien."'>\n";
		}
	}
	//Alle Elementvorlagen finden und auflisten
	$Typauswahl = "";
	$query = "SELECT * FROM `Elementvorlagen` WHERE `Auswahl` = 1 AND `Bild` = 1;";
//var_dump($db);
//var_dump($line_Bild["Inhalt"]);
   $stmt = mysqli_prepare($db, $query);
//var_dump($db);
//var_dump($line_Bild["Inhalt"]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line_Elemente = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Typauswahl = $Typauswahl.$line_Elemente[$_SESSION['Sprache']].";";
	}
	mysqli_stmt_close($stmt);
	$Typauswahl = substr($Typauswahl,0,-1);
	$Elementvorlagen = "<input id='Typauswahl' name='Typauswahl' value='".$Typauswahl."' type='hidden'>\n";

	//Alle Bausteine finden und auflisten
	$Bausteinauswahl = "";
	$query="SELECT `Baustein_ID`, `Bezeichnung` FROM `Bausteine` WHERE `User_ID` = 0 OR `User_ID` = ? ORDER BY `User_ID` DESC, `Bezeichnung` ASC;";
   $stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "i", $_SESSION['User_ID']);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Bausteinauswahl = $Bausteinauswahl.$line['Baustein_ID'].",".$line['Bezeichnung'].";";
	}
	mysqli_stmt_close($stmt);
	//$Text = $line_Bild['Inhalt'];
	$Text = html_entity_decode($line_Bild["Text"]);
	$Text = str_replace("'", "&quot;", $Text);
	echo "<input name='Inhalt' value='".$Text."' type='hidden'>\n";
	echo "<input id ='Typauswahl' name='Typauswahl' value='".$Typauswahl."' type='hidden'>\n";
	echo "<input id ='Bausteinauswahl' name='Bausteinauswahl' value='".$Bausteinauswahl."' type='hidden'>\n";
	echo "</form></div>\n";	
	echo "<div id='DH_Bereich' style='font-size: 12px;'>\n";

	$Text = html_entity_decode($line_Bild["Inhalt"]);
	//$Text = $line_Bild["Inhalt"];
	$Text = str_replace(" class=\"\"", "", $Text);
	$Text = str_replace(" class=\"context-menu-two\"", "", $Text);
	$Text = str_replace(" onclick=\"auswaehlen(this);\"", "", $Text);
	$Text = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $Text);
	if ($mobil==1){
		$Text = str_replace("<div", "<div ontouchend=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<img", "<img ontouchend=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<input", "<input ontouchend=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<textarea", "<textarea ontouchend=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<select", "<select ontouchend=\"auswaehlen(this);\"", $Text);
	}else{
		$Text = str_replace("<div", "<div onclick=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<img", "<img onclick=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<input", "<input onclick=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<textarea", "<textarea onclick=\"auswaehlen(this);\"", $Text);
		$Text = str_replace("<select", "<select onclick=\"auswaehlen(this);\"", $Text);
	}
	echo $Text;
	echo "</div>\n";
		echo "</div>\n<script type='text/javascript'>\n";
	$JS = str_replace("§§§","'", $JS);
	$JS = str_replace("@@@",'"', $JS);
	echo $JS;
	echo "</script>\n";
	mysqli_close($db);
?>	
</body>
</html>