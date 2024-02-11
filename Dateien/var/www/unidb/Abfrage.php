<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">
<script src="./jquery-3.3.1.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<script src="./colresizable/colResizable-1.6.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script src="./Abfrage.js"></script>
<style>
	.scroll{
		overflow-x:scroll;
		max-width:100%;
		position:relative;
	}
</style>

<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
include '../mobil.php';
$Text = Translate("Abfrage.php");
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[46];
	exit;
}
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
//Verbindung zur Datenbank herstellen
$_SESSION['DB_Server'] = "localhost";
$dbAbfrage = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],'unidb');
mysqli_query($dbAbfrage, 'set character set utf8;');
//Abfrage einlesen
if ($Timestamp > ""){
	$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Datenbank' AS CHAR) AS `Datenbank`, column_get(`Inhalt`, 'SQL' AS CHAR) AS `SQL`, column_get(`Inhalt`, 'Beschreibung' AS CHAR) AS `Beschreibung` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	$Abfrage = "Baumhistorie";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
} else {
	$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Datenbank' AS CHAR) AS `Datenbank`, column_get(`Inhalt`, 'SQL' AS CHAR) AS `SQL`, column_get(`Inhalt`, 'Beschreibung' AS CHAR) AS `Beschreibung` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	$Abfrage = "Baum";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$geloescht = $line["geloescht"];
if($Abfrage == "Baum") {
	$Timestamp = "";
} else {
	$Timestamp = $line["Timestamp"];
}
mysqli_stmt_close($stmt);
$SQL = html_entity_decode($line["SQL"]);
//$SQL = htmlspecialchars_decode($SQL);
$Datenbank = html_entity_decode($line["Datenbank"]);
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
$Beschreibung = html_entity_decode($line["Beschreibung"]);
echo "<title>".$Bezeichnung."</title>";
echo "</head>";
echo "<body class='allgemein'>";
//obere Schalterleiste
if ($mobil==1){
	echo "<form action='Baum2.php' method='post' target='_self' id='phpform' name='phpform'>";
}else{
	echo "<form action='Baum2.php' method='post' target='Baum' id='phpform' name='phpform'>";
}
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
echo "<input id='Baum_ID' name='Baum_ID' type='hidden' value='".$Baum_ID."'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>";
echo '<input id="SQL" name="SQL" type="hidden" value="'.$SQL.'">';
echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>";
echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>";
echo "<input type='hidden' id='aktion' name='Aktion' value = ''>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>";
//Datenbankliste füllen
$query = "SHOW DATABASES;";
$stmt = mysqli_prepare($dbAbfrage,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$DB_Liste = "";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	if($line["Database"] == $Datenbank) {
		$DB_Liste = $DB_Liste."<option selected>".$line["Database"]."</option>";
	} else {
		$DB_Liste = $DB_Liste."<option>".$line["Database"]."</option>";
	}
}
mysqli_stmt_close($stmt);
//Tabellenliste füllen
$Tabellenliste = "<select class='Auswahl_Liste_Element' value='' id='tabellenliste' name='Tabellenliste' onchange='Tabelle_anzeigen();'>";
$Tabellenliste = $Tabellenliste."<option></option>";
if(strlen($Datenbank) > 0) {
	$query = "SHOW TABLES FROM `".$Datenbank."`;";
	$stmt1 = mysqli_prepare($dbAbfrage,$query);
	mysqli_stmt_execute($stmt1);
	$result = mysqli_stmt_get_result($stmt1);
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Tabellenliste = $Tabellenliste."<option>".$line["Tables_in_".$Datenbank]."</option>";
	}
	mysqli_stmt_close($stmt1);
}
$Tabellenliste = $Tabellenliste."</select>";
echo '<div class="schaltfl_mobil">';
	if($mobil != 1) {echo '<div class="schaltfl_mobil" style="width: 800px;">';}
		echo '<div id="schaltfl_1" style="width: 25%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[1].'</div>';
		echo '<div id="schaltfl_2" style="width: 25%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[0].'</div>';
		echo '<div id="schaltfl_3" style="width: 25%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[13].'</div>';
		echo '<div id="schaltfl_4" style="width: 25%;" onclick="umschalten(4);" class="schaltfl_mobil">'.$Text[47].'</div>';
	if($mobil != 1) {echo '</div>';}
?>
</div>

<div class="bereich_mobil" name="Dokument_nav" id="dokument_nav">
<table class='Text_einfach' cellpadding='5' style="padding-top: 15px;"><tr>
<?php
if($anzeigen == 1) {
	if($geloescht == 1) {
		if ($mobil==1){
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[45]."</a></td>";
		} else {
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[45]."</a></td>";
		}
	} else {
		if ($Abfrage == "Baum"){
			echo "<td><a href='./loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a></td>";
  		 	echo "<td><a href='./verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a></td>";
  			echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[5]."</a></td>";
  		}
  	}
	echo "</tr><tr><td colspan = '2'>".$Text[18]."&nbsp;<input class='Text_Element' type='text' name='Bezeichnung' value='".$Bezeichnung."'></td>";
	if ($Abfrage == "Baum" and $geloescht != 1){echo "<td><input type='submit' name='Aktion' value='".$Text[6]."' onclick='abspeichern()'></td>";}
}
$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
$stmt1 = mysqli_prepare($db,$abfrage);
mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);
if(mysqli_num_rows($result1) > 0) {
	echo "</tr><tr><td colspan = '2'>".$Text[42]."&nbsp;<select name='Timestamp' id='timestamp' onchange='Vers_zeigen();'><option></option>";
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
mysqli_close($db);
if($Abfrage == "Baumhistorie") {
	echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[43]."</a></td>";
	echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[44]."</a></td>";
}
echo "</tr><tr><td>&nbsp;</td></tr></table></div>";

echo '<div class="bereich_mobil" name="Abfrage_nav" id="abfrage_nav">';
echo "<table class='Text_einfach' cellpadding='5' style='padding-top: 15px;'><tr>";
echo "<td></td><td><div onclick='Abfrage_ausfuehren();'><a href='#'>".$Text[7]."</a></div></td>";
echo "<td><div onclick='SQL_zeigen();'><a href='#'>".$Text[8]."</a></div></td>";
echo "<td><div onclick='SQL_einlesen();'><a href='#'>".$Text[9]."</a></div></td>";
echo "</tr><tr><td align = 'right'>".$Text[10]."</td><td><input class='Text_Element' type='text' name='Begrenzung' id='begrenzung' value='100' style='width: 30px;'></td>";
echo "</tr><tr><td align = 'right'>".$Text[11]."</td><td><input class='Text_Element' type='text' id='ab_satz' value='0' style='width: 30px;'></td>";
echo "</tr><tr><td align = 'right'>".$Text[12]."</td><td><input class='Text_Element' type='checkbox' name='Duplikate' id='duplikate'></td>";
echo "</tr><tr><td>&nbsp;</td></tr></table></div>";

echo '<div class="bereich_mobil" name="Datenbank_nav" id="datenbank_nav">';
echo "<table class='Text_einfach' cellpadding='5' style='padding-top: 15px;'><tr>";
echo "<td valign='top'><font size='-1'>".$Text[13]."<br></font><select class='Auswahl_Liste_Element' id='datenbank' name='Datenbank' value='".$Datenbank."' onchange='Tabellen_fuellen();'>".$DB_Liste."</select></td>";
echo "<td valign='top'><font size='-1'>".$Text[14]."<br></font>".$Tabellenliste."</td>";
echo "<td valign='top'><font size='-1'>".$Text[15]."<br></font><select class='Auswahl_Liste_Element' id='abfragetyp' name='Abfragetyp' value = 'SELECT' onchange='Auswahltabelle_bauen(1);'><option selected>SELECT</option><option>UPDATE</option><option>INSERT</option><option>DELETE</option></select></td>";
echo "</tr><tr><td>&nbsp;</td></tr></table></div>";

echo '<div class="bereich_mobil" name="Sonstiges_nav" id="sonstiges_nav">';
echo "<table class='Text_einfach' cellpadding='5' style='padding-top: 15px;'><tr>";
echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('26');\">".$Text[17]."</a><td>";
$Feldbreite = "400";
if($mobil == 1) {$Feldbreite = "200";}
echo "<td>".$Text[16]."</td><td><textarea name='Beschreibung' id='beschreibung' style='width: ".$Feldbreite."px; height: 18px;'>".$Beschreibung."</textarea></td>";
echo "</tr><tr><td>&nbsp;</td></tr></table></div>";
mysqli_close($dbAbfrage);
?>
</form>

</body>
</html>