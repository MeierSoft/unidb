<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script src="./Geraete_Einstellungen.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
include('./Sitzung.php');
header("X-XSS-Protection: 1");
include('./conf_DH_schreiben.php');
include('/admin/DH_Admin_func.php');
$Text = Translate("Geraete_Einstellungen.php");
echo "<title>".$Text[0]."</title>\n";
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<h3>".$Text[0]."</h3>\n";
//Daten schreiben
if ($speichern==$Text[1]){
	$query = "UPDATE `Geraete` SET `Benutzername` = '".$Benutzername."', `Passwort` = '".$Passwort."', `Bezeichnung` = '".$Bezeichnung."', `Bemerkung` = '".$Bemerkung."' WHERE `Geraete_ID` = ".$Geraete_ID.";";
	Kol_schreiben($query);
}
//Tag zuordnen
if ($speichern1==$Text[2]){
	//neuer Point anlegen
	if($Tagname1 == "" and $Tagname2 > "") {
		$Tagname1 = $Pfad.$Tagname2;
		$query = "INSERT INTO `Points` (`Path`, `Pointname`, `Description`, `EUDESC`, `Interface`, `step`, `Info`, `Intervall`, `Scale_min`, `Scale_max`, `Property_1`, `Property_2`, `Property_3`, `Property_4`, `Property_5`, `compression`) VALUES ('".$Pfad."', '".$Tagname2."', '".$Beschreibung."', '".$Einheit."', '".$Interface."', ".$Step_Feld.", '".$Var_Name."', 0, 0, 100, 0, '', '', '', '', 0);";
		Kol_schreiben($query);
		//Point_ID des neuen Points ermitteln
		include( './conf_DH.php');
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = ? AND `Path` = ?;";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_bind_param($stmt, "ss", $Tagname2, $Pfad);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Point_ID = $line["Point_ID"];
		mysqli_stmt_close($stmt);
		//Wert in akt schreiben
		$query = "INSERT INTO `akt` (`Point_ID`, `Value`, `Timestamp`) VALUES (".$Point_ID.", 0, CURRENT_TIMESTAMP)";
		Kol_schreiben($query);
		//Tag erzeugen
		$query = "INSERT INTO `Tagtable` (`Point_ID`, `Path`, `Tagname`, `Tag_owner`) VALUES (".$Point_ID.", '".$Pfad."', '".$Tagname2."', ".$_SESSION['User_ID'].");";
		Kol_schreiben($query);
		//Meldung ausgeben
		Meldung_schreiben("comp", "Points einlesen", "Server");
	} else {
		//vorhandenen Point zuordnen
		//zuerst die Point_ID aus der Tag_ID ermitteln
		$Pfad = "%";
		if(substr($Tagname1,0,1) == "/") {
			$pos = strrpos($Tagname1, "/");
			$Pfad = substr($Tagname1, 0, $pos + 1);
			$Tagname = substr($Tagname1, $pos + 1);
		}
		$query = "SELECT `Tag_ID` FROM `Tags` WHERE `Path` = ? AND `Tagname` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);
		mysqli_stmt_close($stmt);
	}
	//speichern
	if($Point_ID == 0 or $Point_ID == "") {$Point_ID = "NULL";}
	$query = "UPDATE `Geraete_Points` SET `Point_ID` = ".$Point_ID." WHERE `Geraete_Points_ID` = ".$Var_Nummer.";";
	Kol_schreiben($query);
}

$Abfrage = "SELECT * FROM `Geraete` WHERE `User_ID`=".$_SESSION['User_ID'].";";
$req_Abfrage = mysqli_query($dbDH, $Abfrage);
$Geraeteliste = "<select class='Auswahl_Liste_Element' size='15' name='Geraete_ID' id = 'geraete_id' onclick='document.getElementById(\"formular\").submit();'>";
while ($line_Abfrage = mysqli_fetch_array($req_Abfrage)){
	if($line_Abfrage['Geraete_ID'] == $Geraete_ID) {
		$Geraeteliste = $Geraeteliste."<option selected value='".$line_Abfrage["Geraete_ID"]."'>".$line_Abfrage["Nummer"]."&nbsp;".$line_Abfrage["Bezeichnung"]."</option>";
	} else {
		$Geraeteliste = $Geraeteliste."<option value='".$line_Abfrage["Geraete_ID"]."'>".$line_Abfrage["Nummer"]."&nbsp;".$line_Abfrage["Bezeichnung"]."</option>";
	}
}
$Geraeteliste = $Geraeteliste."</select>";
$Abfrage = "SELECT * FROM `Geraete` WHERE `Geraete_ID`=".$Geraete_ID.";";
$req_Abfrage = mysqli_query($dbDH, $Abfrage);
$line_Abfrage = mysqli_fetch_array($req_Abfrage);
echo "<form action='./Geraete_Einstellungen.php' name='Formular' id='formular' method='post' target='_self'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<table cellpadding='5px' class='Text_einfach'>";
echo "<tr><td valign='top'>".$Geraeteliste."</td><td><table>";
echo "<tr><td align='right' width = '100px'>".$Text[3]."</td><td class = 'Text_fett'>".$line_Abfrage['Nummer']."</td></tr>";
echo "<tr><td align='right'>".$Text[4]."</td><td class = 'Text_fett'>".$line_Abfrage['lokale_IP']."</td></tr>";
echo "<tr><td align='right'>".$Text[5]."</td><td><input class='Text_Element' name='Bezeichnung' type='text' value='".$line_Abfrage['Bezeichnung']."'></td></tr>";
echo "<tr><td align='right'>".$Text[6]."</td><td><input class='Text_Element' name='Benutzername' type='text' value='".$line_Abfrage['Benutzername']."'></td></tr>";
echo "<tr><td align='right'>".$Text[7]."</td><td><input class='Text_Element' name='Passwort' type='password' value='".$line_Abfrage['Passwort']."'></td></tr>";
echo "<tr><td align='right' valign='top'>".$Text[8]."</td><td><Textarea class='Text_Element' name='Bemerkung' style='width: 380px; height: 90px;'>".$line_Abfrage['Bemerkung']."</textarea></td></tr>";
echo "</tr>";
echo "<tr height='35' valign='middle'><td></td><td><input class='Schalter_Element' value='".$Text[9]."' type='submit' id='speichern' name='speichern'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick=\"Hilfe_Fenster('27');\">".$Text[10]."</a></td></tr></td></tr></table>";
echo "</table>";
//Unterformular
echo "<table cellpadding = '3'><tr><td colspan = '8'><hr></td></tr><tr class='Tabelle_Ueberschrift' style = 'background-color: #E5E5E5;'><td>".$Text[11]."</td><td>Point_ID</td><td>Pointname</td><td>".$Text[12]."</td><td>".$Text[13]."</td><td colspan = '2'></td><td>Point</td></tr>";
$query = "SELECT `Geraete_Points_ID`, `Geraete_Points`.`Geraete_ID`, `Geraete_Points`.`var_Name`, `Geraete_Points`.`Point_ID`, `Points`.`Pointname`, `Points`.`Description`, `Points`.`EUDESC` FROM `Geraete_Points` INNER JOIN `Points` ON `Geraete_Points`.`Point_ID` = `Points`.`Point_ID` WHERE `Geraete_Points`.`Geraete_ID` = ".$Geraete_ID.";";
$result = mysqli_query($dbDH, $query);
while ($line = mysqli_fetch_array($result)){
	$var_Name = $line["var_Name"];
	if(substr($line["var_Name"],0,6) == "1wire_") {$var_Name = substr($line["var_Name"],6);}
	echo "<input id='var_".$line["Geraete_Points_ID"]."' type='hidden' value='".$line["Geraete_Points_ID"]."'>";
	$query = "SELECT `Tag_ID` FROM `Tagtable` WHERE `Point_ID` = ".$line["Point_ID"]." ORDER BY `Tag_ID` ASC LIMIT 1;";
	$result_Tag = mysqli_query($dbDH, $query);
	$line_Tag = mysqli_fetch_array($result_Tag);
	echo "<tr class = 'Tabellenzeile'><td>".$var_Name."</td><td>".$line["Point_ID"]."</td><td>".$line["Pointname"]."</td><td>".$line["Description"]."</td><td>".$line["EUDESC"]."</td><td><a href='javascript:void(0);' onclick='Tagdetails(".$line["Point_ID"].");'>".$Text[14]."</a></td><td><a href='./Trend.php?Tag_ID=".$line_Tag["Tag_ID"]."&Zeitpunkt=jetzt' target='_blank'>".$Text[15]."</a></td><td><a href='javascript:void(0);' onclick='Tag_zuordnen(".$line["Geraete_Points_ID"].",\"\",0);'>".$Text[16]."</a></td></tr>";
}
$query = "SELECT `Geraete_Points_ID`, `var_Name` FROM `Geraete_Points` WHERE `Geraete_ID` = ".$Geraete_ID." AND `Point_ID` = 0 ORDER BY `var_Name` ASC;";

$result = mysqli_query($dbDH, $query);
$i = 0;
while ($line = mysqli_fetch_array($result)){
	$var_Name = $line["var_Name"];
	if(substr($line["var_Name"],0,6) == "1wire_") {$var_Name = substr($line["var_Name"],6);}
	echo "<tr class = 'Tabellenzeile'><td>".$var_Name."</td><td><input id='var_".$line["Geraete_Points_ID"]."' type='hidden' value='".$line["Geraete_Points_ID"]."'></td><td></td><td></td><td></td><td></td><td></td><td><a href='javascript:void(0);' onclick='Tag_zuordnen(".$line["Geraete_Points_ID"].",\"".$var_Name."\",\"".$line_Abfrage['Nummer']."\");'>".$Text[16]."</a></td></tr>";
}
echo "</table>";
//Ende Unterformular
echo "</form>";


mysqli_close($db);
mysqli_close($dbDH);
?>
</span>
</body>
</html>
