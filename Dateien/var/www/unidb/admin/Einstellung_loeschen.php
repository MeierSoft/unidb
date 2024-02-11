<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
include('../Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head><body class='allgemein' style='font-size: 14px;'>";
echo "<title>Einstellung löschen</title>";
$query = "SELECT * FROM `Einstellungen` WHERE `Einstellung_ID`=".$Einstellung_ID;
if($DB == "DH") {
	include '../conf_DH_schreiben.php';
	$req = mysqli_query($dbDH,$query);
} else {
	include '../conf_unidb.php';
	$req = mysqli_query($db,$query);
}
$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
echo"Soll die ausgewählte Einstellung wirklich gelöscht werden?<br><br>";
echo "<b><font size=+1>".$line["Parameter"]."</font></b><br><br>";
if ($mobil==1){
	echo "<form action='Einstellungen2.php' method='post'>";
}else{
	echo "<form action='Einstellungen2.php' method='post' target='Gruppenbaum'>";
}
echo "<input type='hidden' name='DB' value='".$DB."'>";
echo "<input value='".$Einstellung_ID."' type='hidden' name='Einstellung_ID'>";
echo "<table cellpadding='10px'><tr><td><input value='loeschen' class='Schalter_Element' type='submit' name='Aktion'></td><td><input value='abbrechen' class='Schalter_Element' type='reset' name='Aktion'></td></tr></table>";
echo "</form>";
// schliessen der Verbindung
if($DB == "DH") {
	mysqli_close($dbDH);
} else {
	mysqli_close($db);
}
?>
</body>
</html>
