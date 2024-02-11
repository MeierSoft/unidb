<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./jquery-3.3.1.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link rel="stylesheet" href="./css/richtext.min.css">
<script type="text/javascript" src="./scripts/jquery.js"></script>
<script src="./tinymce/tinymce.min.js"></script>
<script src="./tinymce/langs/de.js"></script>
<script type="text/javascript" src="./Seite_bauen.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include './mobil.php';
	$Text = Translate("Seite_bauen.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[12];
		exit;
	}
	//editierte Seite speichern
	if ($Aktion1 == $Text[5]){
		$HTML_Text = htmlentities($HTML_Text);
		$HTML_Text=str_replace("dotted","hidden",$HTML_Text);
		$HTML_Text=str_replace("double","hidden",$HTML_Text);
		$HTML_Text=str_replace("dashed","hidden",$HTML_Text);
		$HTML_Text = str_replace(" onclick=\"auswaehlen(this);\"", "", $HTML_Text);
		$HTML_Text = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $HTML_Text);
		$Bezeichnung = strip_tags($Bezeichnung);
		$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Inhalt', ?, 'max_ID', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ssiii", [$Bezeichnung, $HTML_Text, $max_ID, $Baum_ID, $Server_ID]);
	}
	if($Aktion == "abspeichern") {
		$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Inhalt', ?, 'max_ID', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		if($max_ID == null) {$max_ID = 0;}
		uKol_schreiben(1,$query, "ssiii", [strip_tags($Bezeichnung), $Inhalt, $max_ID, $Baum_ID, $Server_ID]);
	}
	if($Aktion == "löschen") {
		$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
		$Timestamp = "";
	}
	if ($Timestamp > ""){
		$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'max_ID' as CHAR) as `max_ID` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'max_ID' as CHAR) as `max_ID` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}	
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
   echo "<form action='./Seite_bauen.php' method='post' target='_self' name='phpform' id='phpform'>";
   echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
  	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input type='hidden' id='inhalt' name='Inhalt' value = '".$line["Inhalt"]."'>\n";
   echo "<input name='HTML_Text' type='hidden'>";
   echo "<input name='ausgewaehlt' type='hidden'>";
   echo "<input name='max_ID' value='".$line["max_ID"]."' type='hidden'>";
   echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";

	if($mobil == 1) {
		include ("./Seite_bauen_navi_mobil.php");
	} else {
		include ("./Seite_bauen_navi.php");
	}

   echo "<div style = \"z-index: 3; position: absolute; width: 100%; top: 35px\"><hr></div>";
   echo "</form>";
   $tempText = html_entity_decode($line["Inhalt"]);
   $tempText = str_replace("hidden", "dotted", $tempText);
   $tempText = str_replace(" onclick=\"auswaehlen(this);\"", "", $tempText);
	$tempText = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $tempText);
   if ($mobil==1){
   	$tempText = str_replace("<div", "<div ontouchend=\"auswaehlen(this);\"",$tempText);
   } else {
   	$tempText = str_replace("<div", "<div onclick=\"auswaehlen(this);\"",$tempText);
   }
	$tempText="<div id='Inhalt'>".$tempText."</div>";
   echo $tempText;
	mysqli_close($db);
?>
</body>
</html>