<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
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
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include 'mobil.php';
$Text = Translate("Notiz.php");
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[11];
	exit;
}
if($Aktion == "speichern") {
	$Bezeichnung = strip_tags($Bezeichnung);
	$Inhalt = htmlentities($Notiz);
	$query = "UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`, 'html', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Inhalt, $Baum_ID, $Server_ID]);
}
if($Aktion == "löschen") {
	$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
	$Timestamp = "";
}

if ($Baum_ID > 0){
	if ($Timestamp > ""){
		$abfrage = "SELECT `Timestamp`, `Bezeichnung`, column_get(`Inhalt`, 'html' as CHAR) as `Notiz` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$abfrage = "SELECT `Timestamp`, `geloescht`, `Bezeichnung`, column_get(`Inhalt`, 'html' as CHAR) as `Notiz` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<title>".$line["Bezeichnung"]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<form id='phpform' name='phpform' action='Notiz.php' method='post' target='_self'>";
	echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
	echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input type='hidden' id='bezeichnung' name='Bezeichnung' value = '".$line["Bezeichnung"]."'>\n";
	echo "<input type='hidden' id='notiz' name='Notiz' value = '".$line["Notiz"]."'>\n";
	mysqli_stmt_close($stmt);
	echo "<font size='2'><table cellpadding='5px'><tr>";
	echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('19');\">".$Text[1]."</a></td>\n";
	if($anzeigen == 1) {
		if ($Timestamp == null) {
			if ($mobil==1){
				if($line["geloescht"] != 1) {echo "<td><a href='Notiz_bearbeiten.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[5]."</a></td>";}
			} else {
				if($line["geloescht"] != 1) {echo "<td><a href='Notiz_bearbeiten.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[5]."</a></td>";}
			}
			if ($line["geloescht"] != 1) {
				echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[2]."</a></td>";
				echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[6]."</a></td>";
				if ($mobil==1){
					echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a></td>";
					if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[10]."</a></td>";}
				} else {
					echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></td>";
					if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[10]."</a></td>";}
				}
			} else {
				if ($mobil==1){
					echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[10]."</a></td>";
				} else {
					echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[10]."</a></td>";
				}
			}
		} else {
			echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[8]."</a></td>";
			echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[9]."</a></td>";
		}
	}
	
	if($line["geloescht"] != 1) {
		$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			echo "<td>".$Text[7].": </td><td><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
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
	}
	echo "</tr></table><hr>";
	if($mobil == 1) {echo "<h3>".html_entity_decode($line["Bezeichnung"])."</h3><hr>";}
	echo html_entity_decode($line["Notiz"]);
}else{
	echo "</tr></table><hr>";
}
echo "</form>\n";
mysqli_close($db);
?>
<script type="text/javascript">
	function Vers_wiederherstellen(Variante) {
		if (Variante == "wiederherstellen") {
			document.forms["phpform"].aktion.value = "speichern";
		} else {
			document.forms["phpform"].aktion.value = "löschen";
		}
		document.forms["phpform"].submit();
	}
	
</script>
</body>
</html>
