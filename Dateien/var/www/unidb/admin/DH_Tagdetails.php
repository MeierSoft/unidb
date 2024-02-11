<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="refresh" content="60">
<div class="Text_einfach" style="position: absolute; left: 10px; top: 10px;">
<table cellpadding="2" cellspacing="3">
<?php
include('../Sitzung.php');
header("X-XSS-Protection: 1");
$Text = Translate("DH_Tagdetails.php");
echo "<title>".$Text[0]."</title>\n";
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>\n";
echo "<body class='allgemein'>\n";
include '../conf_DH.php';
if($Point_ID > 0) {
	$query = "SELECT * FROM Tagtable WHERE Point_ID = ? ORDER BY Tag_ID ASC LIMIT 1;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Point_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Tag_ID = $line["Tag_ID"];
	mysqli_stmt_close($stmt);
}
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
$query = "SELECT * FROM Tagtable WHERE Tag_ID = ? ORDER BY Path, Tagname ASC;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);	
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
echo "<tr class='Tabellenzeile'><td class='Text_fett'>Tag_ID</td><td class='Text_fett'>".$Text[1]."</td><td class='Text_fett'>Tagname</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Tag_ID"]."</td><td class='Tabelle_Zelle'>".$line["Path"]."</td><td class='Tabelle_Zelle'>".$line["Tagname"]."</td></tr>\n";
echo "</table cellpadding='2' cellspacing='3'><br>\n";
$query = "SELECT * FROM Tagtable WHERE Point_ID = ? AND Tag_ID <> ? ORDER BY Path, Tagname ASC;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "ii", $Point_ID, $Tag_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if(mysqli_num_rows($result) > 0) {
	echo $Text[2].":<br>\n";
	echo "<table><tr class='Tabellenzeile'><td class='Text_fett'>Tag_ID</td><td class='Text_fett'>".$Text[1]."</td><td class='Text_fett'>Tagname</td></tr>\n";
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Tag_ID"]."</td><td class='Tabelle_Zelle'>".$line["Path"]."</td><td class='Tabelle_Zelle'>".$line["Tagname"]."</td></tr>\n";
	}
	echo "</table><br>\n";
}
mysqli_stmt_close($stmt);
#Jetzt den Point darstellen
echo "<br><br>".$Text[3].":<br>\n";
$query = "SELECT * FROM Points WHERE Point_ID =?;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "i", $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);	
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
echo "<table cellpadding='2' cellspacing='3'><tr class='Tabellenzeile'><td class='Text_fett'>Point_ID</td><td class='Text_fett'>".$Text[1]."</td><td class='Text_fett'>Pointname</td><td class='Text_fett'>".$Text[4]."</td><td class='Text_fett'>".$Text[5]."</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Point_ID"]."</td><td class='Tabelle_Zelle'>".$line["Path"]."</td><td class='Tabelle_Zelle'>".$line["Pointname"]."</td><td class='Tabelle_Zelle'>".$line["Description"]."</td><td class='Tabelle_Zelle'>".$line["EUDESC"]."</td></tr>\n";
echo "<tr><td><br></td></tr><tr class='Tabellenzeile'><td class='Text_fett'>".$Text[6]."</td><td class='Text_fett'>step</td><td class='Text_fett'>".$Text[7]."</td><td class='Text_fett'>scan</td><td class='Text_fett'>".$Text[8]."</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Interface"]."</td><td class='Tabelle_Zelle'>".$line["step"]."</td><td class='Tabelle_Zelle'>".$line["Dezimalstellen"]."</td><td class='Tabelle_Zelle'>".$line["scan"]."</td><td class='Tabelle_Zelle'>".$line["Mittelwerte"]."</td></tr>\n";
echo "<tr><td><br></td></tr><tr class='Tabellenzeile'><td class='Text_fett'>Archiv</td><td class='Text_fett'>".$Text[9]."</td><td class='Text_fett'>minarch</td><td class='Text_fett'>".$Text[10]."</td><td class='Text_fett'>".$Text[11]."</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["archive"]."</td><td class='Tabelle_Zelle'>".$line["compression"]."</td><td class='Tabelle_Zelle'>".$line["minarch"]."</td><td class='Tabelle_Zelle'>".$line["Scale_min"]."</td><td class='Tabelle_Zelle'>".$line["Scale_max"]."</td></tr>\n";
echo "<tr><td><br></td></tr><tr class='Tabellenzeile'><td class='Text_fett'>".$Text[12]." 1</td><td class='Text_fett'>".$Text[12]." 2</td><td class='Text_fett'>".$Text[12]." 3</td><td class='Text_fett'>".$Text[12]." 4</td class='Text_fett'><td class='Text_fett'>".$Text[12]." 5</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Property_1"]."</td><td class='Tabelle_Zelle'>".$line["Property_2"]."</td><td class='Tabelle_Zelle'>".$line["Property_3"]."</td><td class='Tabelle_Zelle'>".$line["Property_4"]."</td><td class='Tabelle_Zelle'>".$line["Property_5"]."</td></tr>\n";
echo "<tr><td><br></td></tr><tr class='Tabellenzeile'><td class='Text_fett'>Info</td><td class='Text_fett'>".$Text[13]."</td><td class='Text_fett'>".$Text[14]."</td><td class='Text_fett'>".$Text[15]."</td><td class='Text_fett'>".$Text[16]."</td></tr>\n";
echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$line["Info"]."</td><td class='Tabelle_Zelle'>".$line["Point_Type"]."</td><td class='Tabelle_Zelle'>".$line["Intervall"]."</td><td class='Tabelle_Zelle'>".$line["first_value"]."</td><td class='Tabelle_Zelle'>".$line["Changedate"]."</td></tr></table>\n";

mysqli_close($dbDH);
mysqli_close($db);
?>
</div></body></html>

