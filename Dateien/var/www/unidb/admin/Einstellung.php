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
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>";
echo "<body class='allgemein' style='font-size: 14px;'>";

$query = "SELECT * FROM `Einstellungen` WHERE `Einstellung_ID` = ".$Einstellung_ID.";";
if($DB == "DH") {
	include '../conf_DH_schreiben.php';
	$req = mysqli_query($dbDH,$query);
} else {
	include '../conf_unidb.php';
	$req = mysqli_query($db,$query);
}
$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
echo "<table cellpadding='10px'><tr><td>Einstellung</td><td><a href='Einstellung_editieren.php?Einstellung_ID=".$Einstellung_ID."&DB=".$DB."'>editieren</a></td>";
echo "<td><a href='Einstellung_loeschen.php?Einstellung_ID=".$Einstellung_ID."&DB=".$DB."'>l&ouml;schen</a></td><td><a href='Einstellung_neu.php?Eltern_ID=".$line["Eltern_ID"]."&DB=".$DB."'>neu</a></a></td>";
echo "<td><a href='Einstellung_verschieben.php?Einstellung_ID=".$Einstellung_ID."&DB=".$DB."'>verschieben</a></td></tr></table><br>";
echo "<table>";
echo"<tr bgcolor='#E5E5E5'><td width=110pt><b>Parameter</b></td><td>".$line["Parameter"]."</td></tr>";
echo"<tr bgcolor='#E5E5E5'><td><b>Wert</b></td><td>".$line["Wert"]."</td></tr>";
if($DB == "DH") {
	echo"<tr bgcolor='#E5E5E5'><td><b>Zusatz</b></td><td>".$line["Zusatz"]."</td></tr>";
}
echo "</table>";
// schliessen der Verbindung
if($DB == "DH") {
	mysqli_close($dbDH);
} else {
	mysqli_close($db);
}
?>
</div></div>
</body>
</html>