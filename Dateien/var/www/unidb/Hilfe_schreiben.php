<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<title>Hilfe schreiben</title>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<style>
	form {
		margin: 0;
	}
	textarea {
		display: block;
	}
body {font-family: Arial;}
</style>
<script type="text/javascript">
	_editor_url = "./editor/";
	_editor_lang = "de";
</script>
<script type="text/javascript" src="./editor/htmlarea.js"></script>
<script type="text/javascript">
	HTMLArea.loadPlugin("ContextMenu");
	HTMLArea.onload = function() {
		var editor_de = new HTMLArea("editor_de");
		editor_de.registerPlugin(ContextMenu);
		editor_de.generate();
		var editor_en = new HTMLArea("editor_en");
		editor_en.registerPlugin(ContextMenu);
		editor_en.generate();
		var editor_nl = new HTMLArea("editor_nl");
		editor_nl.registerPlugin(ContextMenu);
		editor_nl.generate();
		var editor_fr = new HTMLArea("editor_fr");
		editor_fr.registerPlugin(ContextMenu);
		editor_fr.generate();
	};
	HTMLArea.init();
</script>
<script type="text/javascript">
	function umschalten(Sprache) {
		if (Sprache == "DE") {
			document.getElementById("div_de").style.display = "block";
			editor_de.nextElementSibling.style.height = "700px";
		} else {
			document.getElementById("div_de").style.display = "none";
		}
		if (Sprache == "EN") {
			document.getElementById("div_en").style.display = "block";
			editor_en.nextElementSibling.style.height = "700px";
		} else {
			document.getElementById("div_en").style.display = "none";
		}
		if (Sprache == "NL") {
			document.getElementById("div_nl").style.display = "block";
			editor_nl.nextElementSibling.style.height = "700px";
		} else {
			document.getElementById("div_nl").style.display = "none";
		}
		if (Sprache == "FR") {
			document.getElementById("div_fr").style.display = "block";
			editor_fr.nextElementSibling.style.height = "700px";
		} else {
			document.getElementById("div_fr").style.display = "none";
		}
	}
</script>
<?php
include('./Sitzung.php');
header("X-XSS-Protection: 1");
if($Aktion == "neuer Hilfetext") {
	$Hilfe_ID = 0;
	$Dokument = "";
	$Dialog = "";
	$DE = "";
	$EN = "";
	$NL = "";
	$FR = "";
	$aktuelle_sprache = "de_text";
	$Aktion = "speichern"; 
}

if($Aktion == "speichern") {
	if($Hilfe_ID < 1) {
		$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Dokument));
		$Dialog = htmlentities(mysqli_real_escape_string($db, $Dialog));
		$DE = htmlentities(mysqli_real_escape_string($db, $DE));
		$EN = htmlentities(mysqli_real_escape_string($db, $EN));
		$NL = htmlentities(mysqli_real_escape_string($db, $NL));
		$FR = htmlentities(mysqli_real_escape_string($db, $FR));
		//neuer Text
		$stmt = mysqli_prepare($db, "INSERT INTO `Hilfe` (`Hilfe_ID`, `Dokument`, `Dialog`, `DE`, `EN`, `NL`, `FR`) VALUES (NULL, ?, ?, ?, ?, ?, ?);");
		mysqli_stmt_bind_param($stmt, "ssssss", $Dokument, $Dialog, $DE, $EN, $NL, $FR);

	} else {
		$stmt = mysqli_prepare($db, "UPDATE `Hilfe` SET `Dokument` = ?, `Dialog` = ?, `DE` = ?, `EN` = ?, `NL` = ?, `FR` = ? WHERE `Hilfe_ID` = ?;");
		mysqli_stmt_bind_param($stmt, "ssssssi", $Dokument, $Dialog, $DE, $EN, $NL, $FR, $Hilfe_ID);
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$req = mysqli_prepare($db,$query);
	if($Hilfe_ID < 1) {
		$query = "SELECT LAST_INSERT_ID() as ID FROM `Hilfe`;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Hilfe_ID = $line["ID"];
		mysqli_stmt_close($stmt);
	}
}

$query = "SELECT * FROM Hilfe WHERE Hilfe_ID = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "i", $Hilfe_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Dokument = html_entity_decode($line["Dokument"]);
$Dialog = html_entity_decode($line["Dialog"]);
$DE = html_entity_decode($line["DE"]);
$EN = html_entity_decode($line["EN"]);
$FR = html_entity_decode($line["FR"]);
$NL = html_entity_decode($line["NL"]);
echo "</head><body>";
echo "<form action='Hilfe_schreiben.php' method='post'>";
echo "<table cellpadding = '3px' width='1600px'><tr><td width='40px' align = 'right'>Hilfe_ID:</td><td width='100px'><input name='Hilfe_ID' type='text' size='1' value = '".$Hilfe_ID."'>&nbsp&nbsp&nbsp<input value='einlesen' type='submit' name='Aktion'></td></tr>";
echo "<tr><td align = 'right' width='40px'>Dokument:</td><td width='120px'><input name='Dokument' type='text' size='30' maxlength='30' value = '".$Dokument."'></td>";   
echo "<td align = 'right' width='40px'>Dialog:</td><td width='120px'><input name='Dialog' type='text' size='30' maxlength='30' value = '".$Dialog."'></td>";   
echo "<td width='40px'><input value='DE' type='button' onclick='umschalten(\"DE\");'></td><td width='40px'><input value='EN' type='button' onclick='umschalten(\"EN\");'></td><td width='40px'><input value='NL' type='button' onclick='umschalten(\"NL\");'></td><td width='40px'><input value='FR' type='button' onclick='umschalten(\"FR\");'></td><td width='500px'></td></tr>";
echo "<tr><td colspan='9'>";
echo "<div id='div_de' style='display:block;'><textarea id='editor_de' name='DE' style='width:1200px; height:700px; visibility:visible;'>".$DE."</textarea></div>";
echo "<div id='div_en' style='display:none;'><textarea id='editor_en' name='EN' style='width:1200px; height:700px; visibility:visible;'>".$EN."</textarea></div>";
echo "<div id='div_nl' style='display:none;'><textarea id='editor_nl' name='NL' style='width:1200px; height:700px; visibility:visible;'>".$NL."</textarea></div>";
echo "<div id='div_fr' style='display:none;'><textarea id='editor_fr' name='FR' style='width:1200px; height:700px; visibility:visible;'>".$FR."</textarea></div></td></tr>";
echo "<tr><td colspan='2'><input value='speichern' type='submit' name='Aktion'></td><td colspan='2'><input value='abbrechen' type='reset' name='abbrechen'></td></tr></table>";
echo "</form>";

// schliessen der Verbindung
mysql_close($db);
?>
</body>
</html>