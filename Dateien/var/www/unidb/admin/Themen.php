<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Einstellung</title>
	<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<?php
include('../Sitzung.php');
header("X-XSS-Protection: 1");
if($_SESSION['admin'] != 1) {exit;}
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Themen.php");
function css_Datei_schreiben($Thema,$Thema_ID) {
	$db = db_connect();
	$query = "SELECT `Themen_Parameter`.`Klasse`, `Themen_Parameter`.`Parameter`, `Themen_Parameter_Werte`.`Wert` FROM `Themen_Parameter` RIGHT JOIN `Themen_Parameter_Werte` ON `Themen_Parameter`.`Themen_Parameter_ID` = `Themen_Parameter_Werte`.`Themen_Parameter_ID` WHERE `Themen_Parameter`.`Themen_ID` =".$Thema_ID." ORDER BY `Themen_Parameter_Werte`.`Themen_Parameter_ID` ASC;";
	$req = mysqli_query($db,$query);
	$Inhalt = "";
	$Klasse = "";
	while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		if($line["Klasse"] != $Klasse) {
			if($Inhalt > "") {$Inhalt = $Inhalt."}\n\n";}
			$Inhalt = $Inhalt.".".$line["Klasse"]." {\n";
			$Klasse = $line["Klasse"];
		}
		$Inhalt = $Inhalt."	".$line["Parameter"].": ".$line["Wert"].";\n";
	}
	$Inhalt = $Inhalt."}";
	//Habe dafuer noch keine Loesung
	$Inhalt = $Inhalt."\n@media screen and (max-width: 380px) {\n	.schaltfl_mobil {\n		font-size: 100%;\n	}\n	.Text_Element {\n		font-size: 85%;\n	}\n	.Schalter_fett_Element {\n		font-size: 85%;\n	}\n	.Textbox {\n		Bereich_mobi: 100%;\n	}\n}";
	file_put_contents("../css/DH/".$Thema.".css", $Inhalt);
}

include '../mobil.php';
if ($mobil==1){
	echo "<br><font size='4'><b>".$Parameter."</font></b><br>";
} else {
	echo "</head>";
	echo "<body class='allgemein'>";
}

//neues Thema speichern
if($Aktion == $Text[1] and strlen($Bezeichnung) > 0) {
	$query = "INSERT INTO `Themen` (`Thema`) VALUES ('".$Bezeichnung."');";
	uKol_schreiben($query);
	$query = "SELECT `Themen_ID` FROM `Themen` Where `Thema` = '".$Bezeichnung."';";
	$req = mysqli_query($db,$query);
	$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
	$Themen_ID = $line["Themen_ID"];
	$query = "SELECT MAX(`Themen_Parameter_ID`) AS `max_ID` FROM `Themen_Parameter`;";
	$req = mysqli_query($db,$query);
	$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
	$max_ID = intval($line["max_ID"]) + 1;
	$query = "SELECT * FROM `Themen_Parameter` Where `Themen_ID` = 1;";
	$req = mysqli_query($db,$query);
	$query = "INSERT INTO `Themen_Parameter` (`Themen_ID`, `Klasse`, `Parameter`) VALUES (";
	while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		$query = $query.$Themen_ID.", '".$line["Klasse"]."' ,'".$line["Parameter"]."'),(";
	}
	$query = substr($query, 0, strlen($query) - 2).";";
	uKol_schreiben($query);
	$query = "SELECT `Themen_Parameter_ID` FROM `Themen_Parameter` WHERE `Themen_ID` = ".$Thema_ID.";";
	$req = mysqli_query($db,$query);
	$query = "INSERT INTO `Themen_Parameter_Werte` (`Wert`, `Themen_Parameter_ID`) VALUES (";
	while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		$query = $query."'".${"f".$line["Themen_Parameter_ID"]}."', ".$max_ID."),(";
		$max_ID++;
	}
	$query = substr($query, 0, strlen($query) - 2).";";
	uKol_schreiben($query);
	css_Datei_schreiben($Bezeichnung,$Themen_ID);
}

//speichern
if($Aktion == $Text[2]) {
	$query = "SELECT `Themen_Parameter_ID` FROM `Themen_Parameter` WHERE `Themen_ID` = ".$Thema_ID.";";
	$req = mysqli_query($db,$query);
	while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		$query = "UPDATE `Themen_Parameter_Werte` SET `Wert` = '".${"f".$line["Themen_Parameter_ID"]}."' WHERE `Themen_Parameter_ID` = ".$line["Themen_Parameter_ID"].";";
		uKol_schreiben(1,$query,"","");
	}
	css_Datei_schreiben($Thema,$Thema_ID);
}
$query = "SELECT * FROM `Themen` ORDER BY `Thema` ASC;";
$req = mysqli_query($db,$query);
echo "<form action='./Themen.php' method='post' target='_self' id='phpform' name='phpform'>";
echo "<table>";
echo "	<tr>";
echo '		<td style="font-size: 16px; font-weight: bold;" align="right">'.$Text[3].'</td><td style="width 10px;"></td>';
echo "		<td>";
echo "<select id='thema' name='Thema' value='".$Thema."' size='1' onchange='document.forms.phpform.submit();'>";
while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
	if($Thema == NULL) {$Thema = $line["Thema"];}
	if($line["Thema"] == $Thema) {
		echo "<option selected>".$line["Thema"]."</option>";
		$Thema_ID = $line["Themen_ID"];
	} else {
		echo "<option>".$line["Thema"]."</option>";
	}
}
echo "<input type='hidden' name='Thema_ID' value=".$Thema_ID.">";
echo "<input type='hidden' name='Bezeichnung' id='bezeichnung' value=".$Thema_ID.">";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
echo "</select></td>";
echo "<td width='20px'></td><td><input type='submit' value='".$Text[1]."' name='Aktion' onclick='namen_festlegen();'></td>";
echo "<td width='20px'></td><td><input type='submit' value='".$Text[2]."' name='Aktion'></td>";
echo "<td width='20px'></td><td><input type='color'></td>";
echo "</tr></table><br><table style='width: 330px;'>";

//Parameter und Werte
$query = "SELECT `Themen_ID` FROM `Themen` WHERE (`Thema` ='".$Thema."') ORDER BY `Themen`.`Thema` ASC LIMIT 0,1;";
$req = mysqli_query($db,$query);
$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
$query = "SELECT `Themen_Parameter`.`Themen_Parameter_ID`, `Themen_Parameter`.`Themen_ID`, `Themen_Parameter`.`Klasse`, `Themen_Parameter`.`Parameter`, `Themen_Parameter_Werte`.`Themen_Parameter_Werte_ID`, `Themen_Parameter_Werte`.`Wert` FROM `Themen_Parameter` RIGHT JOIN `Themen_Parameter_Werte` ON `Themen_Parameter`.`Themen_Parameter_ID` = `Themen_Parameter_Werte`.`Themen_Parameter_ID` WHERE (`Themen_Parameter`.`Themen_ID` = ".$line["Themen_ID"].") ORDER BY `Themen_Parameter`.`Klasse` ASC;";
$req = mysqli_query($db,$query);
$Klasse = "";
while($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
	if($Klasse != $line["Klasse"]) {
		$Klasse = $line["Klasse"];
		echo "<tr style='height: 40px;'><td colspan='4' style='font-weight: bold; vertical-align: bottom;'>".$Klasse."</td></tr><tr>";
	}
	echo "<tr><td colspan='2' align='right'>".$line["Parameter"]."</td><td><input type='text' name='f".$line["Themen_Parameter_Werte_ID"]."' value='".$line["Wert"]."'></td></tr>";
}
echo "</table></form>";
// schliessen der Verbindung
mysqli_close($db);
?>
<script type="text/javascript" >
var T_Text = new Array;
T_Text = JSON.parse(document.getElementById("translation").value);

function namen_festlegen() {
	Bezeichnung = prompt(T_Text[4], "Bezeichnung");
	if (Bezeichnung != null && Bezeichnung.length > 0) {
		//Testen ob es den Namen schon gibt
		Liste = document.getElementById('thema').childNodes;
		gefunden = 0;
		for (z = 0; z < Liste.length; z++) {
			if (Liste[z].innerHTML == Bezeichnung) {
				gefunden = 1;
				break;
			}
		}
		if (gefunden == 0) {
			document.getElementById('bezeichnung').value = Bezeichnung;
			document.forms.phpform.submit();
		} else {
			alert(T_Text[5]);
			document.getElementById('bezeichnung').value = "";
			document.getElementById('aktion').value = "";
		}
	}
}
</script>

</body>
</html>