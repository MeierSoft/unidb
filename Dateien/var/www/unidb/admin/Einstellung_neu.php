<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
   <title>Einstellung erstellen</title>
   <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
   <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<?php
include('../Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include '../mobil.php';
if ($mobil==1){
	echo "<br>";
} else {
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	echo "</head><body class='allgemein'>";
}
echo "<H4>neue Einstellung erstellen</H4>";
echo "<font size='2'><form action='Einstellungen2.php' method='post' target='Gruppenbaum'>";
echo "<input type='hidden' name='DB' value='".$DB."'>";
echo "<table>";
echo "<tr><td>Parameter</td><td><input class='Text_Element' name='Parameter' type='text' size='50'></td></tr>";
echo "<tr><td>Wert</td><td><input class='Text_Element' name='Wert' type='text' size='50'></td></tr>";
if($DB == "DH") {
	echo "<tr><td>Zusatz</td><td><input class='Text_Element' name='Zusatz' type='text' size='50'></td></tr>";
}
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "</table><br>";
echo "<table cellpadding='10px'><tr><td><input class='Schalter_Element' value='neue Einstellung speichern' type='submit' name='Aktion'></td><td><input class='Schalter_Element' value='abbrechen' type='reset' name='abbrechen'></td></tr></table>";
echo "</form></font>";
?>
</body>
</html>
