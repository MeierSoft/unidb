<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<link rel='StyleSheet' href='dtree.css' type='text/css' />
<script type='text/javascript' src='dtree.js'></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<title>unidb</title>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo '<div id="Suchergebnis"></div>';
	echo "<input id='sprache' name='Sprache' type='hidden' value='".$_SESSION['Sprache']."'>\n";
	if ($_SESSION['Sprache'] == "DE") {$Inhalt = "Inhaltsverzeichnis";}
	if ($_SESSION['Sprache'] == "EN") {$Inhalt = "table of contents";}
	if ($_SESSION['Sprache'] == "NL") {$Inhalt = "inhoudsopgave";}
	if ($_SESSION['Sprache'] == "FR") {$Inhalt = "table des matières";}
	echo "<div class='dtree'>\n";
	echo "<script type='text/javascript'>\n";
	echo "d = new dTree('d');\n";
	echo "d.add('33','-1','".$Inhalt."','','','Hauptrahmen');\n";
	$query = "SELECT `Hilfe_ID`,`Eltern_ID`,`Titel_".$_SESSION['Sprache']."` AS `Bezeichnung` FROM `Hilfe` WHERE `aktiv` = 1;";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
		echo "d.add('".$line["Hilfe_ID"]."','".$line["Eltern_ID"]."','".$Bezeichnung."','Hilfe3.php?Hilfe_ID=".$line["Hilfe_ID"]."&nreg=0','','Hilfe_Hauptrahmen');\n";
	}
	echo "document.write(d);\n</script>\n</div><br>";
	mysqli_stmt_close($stmt);
?>
