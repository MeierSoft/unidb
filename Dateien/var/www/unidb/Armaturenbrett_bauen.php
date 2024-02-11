<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./scripts/jquery-3.6.0.js"></script>
<script src="./scripts/jquery-ui.js"></script>
<link rel="stylesheet" href="./css/jquery-ui.css">
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<script type="text/javascript" src="./Armaturenbrett_bauen.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link href="./css/Armaturenbrett.css" rel="stylesheet">
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
<title>Armaturenbrett erstellen</title>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
   include './mobil.php';
   include './conf_DH.php';
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head>";
	$Text = Translate("Bilder_bauen.php");
	//editiertes Bild speichern
	if($Aktion == "wiederherstellen") {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[102];
			exit;
		}
		$query = "UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`, 'Inhalt', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Inhalt_orig, $Baum_ID, $Server_ID]);
		$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Spalten, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Hintergrundfarbe, $Baum_ID, $Server_ID]);
	}	
	if($Aktion == $Text[107]) {
		$anzeigen = Berechtigung($Baum_ID, $Server_ID);
		if($anzeigen == 0) {
			echo $Text[102];
			exit;
		}
		$Inhalt_orig = stripslashes($Inhalt);
		$Inhalt=str_replace("\r\n\r\n","",$Inhalt);
		$Inhalt=str_replace("'","\'",$Inhalt);
		$Inhalt = htmlentities($Inhalt);
		$Bezeichnung = htmlentities($Bezeichnung);
		//$Inhalt = mysqli_real_escape_string($db, $Inhalt);
		$query="UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`,'Inhalt','".$Inhalt."') WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "sii", [$Bezeichnung, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Spalten', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Spalten, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'bed_Format', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii",[htmlentities($Bed_Format), $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Hintergrundfarbe', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Hintergrundfarbe, $Baum_ID, $Server_ID]);
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
		$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Spalten' as CHAR) as `Spalten`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Spalten' as CHAR) as `Spalten`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Bild = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Hintergrundfarbe = $line_Bild["Hintergrundfarbe"];
	if($Hintergrundfarbe > "") {
		echo "<body class='allgemein' style='Background-color: ".$Hintergrundfarbe.";'>\n";
	} else {
		echo "<body class='allgemein'>\n";
	}
	$Bezeichnung = html_entity_decode($line_Bild["Bezeichnung"]);
	echo "<form action='Armaturenbrett_bauen.php' method='post' target='_self' name='phpform'>";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	$bed_Format = html_entity_decode($line_Bild["bed_Format"]);
	echo "<input name='Spalten' id='spalten' value='".$line_Bild['Spalten']."' type='hidden'>\n";
	echo "<input name='Hintergrundfarbe' id='hintergrundfarbe' value='".$Hintergrundfarbe."' type='hidden'>\n";
	echo "<input name='uebernehmen' value='".$Text[0]."' type='hidden'>\n";
	echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
	echo "<input type='hidden' id='inhalt' name='Inhalt_orig' value = '".$line_Bild["Inhalt"]."'>\n";
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";

	if ($mobil==1){
		echo "<input name='mobil' value='1' type='hidden'>\n";
	} else {
		echo "<input name='mobil' value='0' type='hidden'>\n";
	}
	echo "<input name='Bezeichnung' value='".$Bezeichnung."' type='hidden'>\n";
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
				$Versionsauswahl1 = "Version</td><td>";
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
		include('./Armaturenbrett_bauen_navi_mobil.php');
	} else {
		include('./Armaturenbrett_bauen_navi.php');
	}

	echo "</font>";

	//Alle Elementvorlagen finden und auflisten
	$Typauswahl = "";
	$query="SELECT `Vorlage_ID`, `".$_SESSION['Sprache']."` AS `Bezeichnung` FROM `Vorlagen` WHERE `Typ` = 'Element';";
   $stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Typauswahl = $Typauswahl.$line['Bezeichnung'].";";
	}
	mysqli_stmt_close($stmt);
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
	//Falls das AB neu ist, dann ertmal 5 Spalten anlegen
	if(strlen($Text) < 50) {
		for($x = 0; $x <= 4; $x++) {
			$Text = $Text."<div class='column' id='Spalte_".$x."'></div>\n";
		}
	}
	echo $Text."</div>\n";
	mysqli_close($db);
?>	
</body>
</html>