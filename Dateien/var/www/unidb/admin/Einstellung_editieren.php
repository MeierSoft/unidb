<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<style type="text/css">
	#Schrift {
		font-family: Helvetica,Arial,sans-serif;
	}
</style>
<title>Einstellung editieren</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<?php
	include('../Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head>";
	echo "<body class='allgemein'>";
	$query = "SELECT * FROM Einstellungen WHERE Einstellung_ID=".$Einstellung_ID;
	if($DB == "DH") {
		include '../conf_DH_schreiben.php';
		$req = mysqli_query($dbDH,$query);
	} else {
		include '../conf_unidb.php';
		$req = mysqli_query($db,$query);
	}
	$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
	echo "<span id='Schrift'; style='font-weight:normal; font-size: 14px'>";
	echo "<form action='Einstellungen2.php?modus=editieren&Einstellung_ID=".$Einstellung_ID."' method='post' target='Gruppenbaum'>";
	echo "<input type='hidden' name='DB' value='".$DB."'>";
	echo "<H4>Einstellung bearbeiten</H4>";
	echo "<table>";
	echo "<tr><td>Parameter</td><td><input class='Text_Element' name='Parameter' type='text' size='50' value='".$line["Parameter"]."'></td></tr>";
	echo "<tr><td>Wert</td><td><input class='Text_Element' name='Wert' type='text' size='50' value='".$line["Wert"]."'></td></tr>";
	if($DB == "DH") {
		echo "<tr><td>Zusatz</td><td><input class='Text_Element' name='Zusatz' type='text' size='50' value='".$line_Einstellung["Zusatz"]."'></td></tr>";
	}
	echo "</table><br>";
	echo "<table cellpadding='10px'><tr><td><input class='Schalter_Element' value='uebernehmen' type='submit' name='Aktion'></td><td><input class='Schalter_Element' value='abbrechen' type='reset' name='abbrechen'></td></tr></table>";
	echo "<input value='".$line["Eltern_ID"]."' type='hidden' name='Eltern_ID'>";
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
