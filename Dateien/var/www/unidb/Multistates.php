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
<script src="./scripts/Multistates.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<font size="2">
<?php
include 'Sitzung.php';
include 'conf_DH_schreiben.php';
include './mobil.php';
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Multistates.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
$upload_folder = 'Multistates/';
if ($speichern == $Text[1]){
	$query = "INSERT INTO `Multistates` (`User_ID`,`Gruppe`) VALUES(?,?);";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "is", $_SESSION['User_ID'], $Gruppe);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Gruppe_Liste = mysqli_insert_id($dbDH);
}

if ($speichern == $Text[2]){
	$query = "UPDATE `Multistates` SET `Gruppe` = ? WHERE Multistate_ID = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "si", $Gruppe, $Gruppe_Liste);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
}

if ($speichern == $Text[3]){
	$query = "SELECT * FROM `Multistates_Detail` WHERE Multistate_ID = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Gruppe_Liste);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	//Zunächst die Datensätze in Multistates_Setail entfernen.	
	while ($line_Detail = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		//Nachsehen, ob das Bild noch woanders verwendet wird. Wenn nein, dann die Datei löschen.
		$query = "SELECT `Bild` FROM `Multistates_Detail` WHERE `Multistate_Detail_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $line_Detail["Multistate_detail_ID"]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$query = "SELECT COUNT(`Bild`) AS Anzahl FROM `Multistates_Detail` WHERE `Bild` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "s", $line["Bild"]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Anzahl = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if($line_Anzahl["Anzahl"] == 1){
			unlink($upload_folder."/".$line["Bild"]);
		}
		$sql = "DELETE FROM `Multistates_Detail` WHERE `Multistate_Detail_ID` = ".$line_Detail["Multistate_detail_ID"].";";
		$result = mysqli_query($dbDH,$sql);
	}
	$query = "DELETE FROM `Multistates` WHERE Multistate_ID = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Gruppe_Liste);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Gruppe_Liste = null;
}

if ($Bedingung_editieren == $Text[4]){
	$sql = "INSERT INTO `Multistates_Detail` (`Multistate_ID`) VALUES(".$Gruppe_Liste.");";
	$result = mysqli_query($dbDH,$sql);
}
if ($Bedingung_editieren == $Text[2]){
	$sql = "UPDATE `Multistates_Detail` SET `Operant` ='".$Operant."', `Wert` ='".$Wert."', `Bild` ='".$Bild."' WHERE `Multistate_Detail_ID` =".$Multistate_Detail_ID1.";";
	$result = mysqli_query($dbDH,$sql);
}

if ($Bedingung_editieren == $Text[5]){
	//Nachsehen, ob das Bild noch woanders verwendet wird. Wenn nein, dann die Datei löschen.
	$query = "SELECT `Bild` FROM `Multistates_Detail` WHERE `Multistate_Detail_ID` = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Multistate_Detail_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$query = "SELECT COUNT(`Bild`) AS Anzahl FROM `Multistates_Detail` WHERE `Bild` = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "s", $line["Bild"]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Anzahl = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if($line_Anzahl["Anzahl"] == 1){
		unlink($upload_folder."/".$line["Bild"]);
	}
	$sql = "DELETE FROM `Multistates_Detail` WHERE `Multistate_Detail_ID` = ".$Multistate_Detail_ID.";";
	$result = mysqli_query($dbDH,$sql);
}

echo "<form id='phpform' name='phpform' action='Multistates.php' method='post' target='_self' enctype='multipart/form-data'>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<input type='hidden' id='upload_folder' value = '".$upload_folder."'>\n";
//Verzeichnis einlesen
$Dateiliste = [];
if (is_dir($upload_folder)){
	if ($handle = opendir($upload_folder)) {
		while (($file = readdir($handle)) !== false) {
			if($file !="." and $file !="..") {
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
		echo "<input type='hidden' id='dateiliste' name='Dateiliste' value = '".$Dateien."'>\n";
	}
}
echo "<table class='Text_einfach' style='background: #FCEDD9; height: 50px;'><tr><td>".$Text[6]."</td><td>".$Text[7]."</td><td></td><td></td><td></td><td></td><td>".$Text[8]."</td></tr>\n";
echo "<tr><td><select class='Auswahl_Liste_Element' id='gruppe_liste' name='Gruppe_Liste' size='1' onchange = 'Gruppe_einlesen();'>";
if($Gruppe_Liste == null) {
	$Liste = 0;
	echo "<option value = '0'></option>\n";
} else {
	$Liste = intval($Gruppe_Liste);
}
$Gruppe_Name = "";
//Gruppen einlesen
$query = "SELECT DISTINCT * FROM `Multistates` WHERE User_ID = ? ORDER BY `Gruppe` ASC;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['User_ID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	if($line["Multistate_ID"] == $Liste) {
		echo "<option value = '".$line["Multistate_ID"]."' selected>".$line["Gruppe"]."</option>\n";
		$Gruppe_Name = $line["Gruppe"];
	} else {
		echo "<option value = '".$line["Multistate_ID"]."'>".$line["Gruppe"]."</option>\n";
	}
}
echo "</select></td>\n";
mysqli_stmt_close($stmt);
echo "<td><input class='Text_Element' id = 'gruppe_bezeichnung' name='Gruppe' value='".$Gruppe_Name."' type='Text'></td>\n";
echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[2]."' type='submit'></td>\n";
echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[1]."' type='submit' onclick='neues_Multistate();'></td>\n";
echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[3]."' type='submit'></td>\n";
echo "<td width = '20px'></td>\n";
echo "<td><input class='Schalter_Element' name='Hilfe' value='".$Text[9]."' type='button' onclick=\"Hilfe_Fenster('23');\"></td>\n";
echo "</tr></table><br><br>\n";
if ($Gruppe_Liste > "0"){
	echo "<table cellpadding = '3px'><tr><td><b>".$Text[10]."</b></td><td><b>".$Text[11]."</b></td><td><b>".$Text[12]."</b></td></tr>";
	$query_Gruppe = "SELECT * FROM `Multistates_Detail` WHERE `Multistate_ID` = '".$Gruppe_Liste."';";
	$req_Gruppe = mysqli_query($dbDH,$query_Gruppe);
	while ($line_Gruppe = mysqli_fetch_array($req_Gruppe, MYSQLI_ASSOC)) {
		echo "<tr class='Text_einfach' bgcolor='#DECECE'>";
		echo "<td align='center'>".$line_Gruppe["Operant"]."</td>\n";
		echo "<td align='right'>".$line_Gruppe["Wert"]."</td>\n";
		echo "<td><a href='javascript:void(0);' onclick='Bild_zeigen(\"./".$upload_folder.$line_Gruppe["Bild"]."\");'>".$line_Gruppe["Bild"]."</a></td>\n";
		echo "<td><input class='Schalter_Element' value='".$Text[13]."' type='button' name='Bedingung_editieren' onclick='Bedingungeditieren(".$Liste.",".$line_Gruppe["Multistate_detail_ID"].",\"".$line_Gruppe["Operant"]."\",\"".$line_Gruppe["Wert"]."\",\"".$line_Gruppe["Bild"]."\");'></td>\n";
		echo "<td><input class='Schalter_Element' value='".$Text[5]."' type='submit' name='Bedingung_editieren' onclick='Bedingung_entfernen(".$line_Gruppe["Multistate_detail_ID"].");'></td></tr>\n";
	}
}

if($Gruppe_Name > "") {
	echo "<tr valign='bottom' style='height: 50px;'><td colspan='3'><input class='Schalter_Element' value='".$Text[4]."' type='submit' id='neue_Bedingung' name='Bedingung_editieren'></td><td></td></tr>\n";
}
echo "</table>\n<input type='hidden' id='multistate_detail_id' name='Multistate_Detail_ID' value = '".$Multistate_Detail_ID."'>\n";
echo "</form>\n";

// schliessen der Verbindung
mysqli_close($dbDH);
?>
</body>
</html>
