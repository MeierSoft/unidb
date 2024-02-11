<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//DE"
"http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
include('Sitzung.php');
require_once 'mobil.php';
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Webseite.php");

$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[7];
	exit;
}
if ($Timestamp > ""){
	$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
} else {
	$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
}	
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Inhalt = html_entity_decode($line["Inhalt"]);
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
mysqli_stmt_close($stmt);
echo "<title>".$Bezeichnung."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
//Navigation
echo "<font size='2'><table cellspacing='10px'><tr>";

//if ($mobil==1){echo "<td><a href='Baum2.php' target='_self'>".$Text[2]."</a></td></tr></table><table><tr>";}
if($anzeigen == 1) {
	if ($Timestamp == null) {
		if ($mobil==1){
			if($line["geloescht"] != 1) {echo "<td><a href='Seite_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a></td>";}
		} else {
			if($line["geloescht"] != 1) {echo "<td><a href='Seite_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></td>";}
		}
		if ($line["geloescht"] != 1) {
			echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[4]."</a></td>";
			echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[6]."</a></td>";
			if ($mobil==1){
				echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[5]."</a></td>";
				if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";}
			} else {
				echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[5]."</a></td>";
				if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";}
			}
		} else {
			if ($mobil==1){
				echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";
			} else {
				echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";
			}
		}
	}
}
if ($mobil==1){echo "<td><a href='Baum2.php' target='_self'>".$Text[4]."</a></td>";}
echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('15');\">".$Text[1]."</a></td>\n";
echo "</tr></table></form><hr>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
//Ende Navigation
$Inhalt = str_replace('1px solid black"', '', $Inhalt);
$Inhalt = str_replace(' ontouchend="auswaehlen(this);"', '', $Inhalt);
//$Inhalt = str_replace(' ondblclick="Dialog_oeffnen();"', '', $Inhalt);
$Inhalt = str_replace(' draggable="true"', '', $Inhalt);
$Inhalt = str_replace(' onclick="auswaehlen(this);"', '', $Inhalt);
echo $Inhalt;
mysqli_close($db);
?>
</body>
</html>
