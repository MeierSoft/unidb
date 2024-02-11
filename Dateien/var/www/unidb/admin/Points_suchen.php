<html>
<head>
<?php
	//Get und POST einlesen
	foreach($_GET as $key => $value){
		${$key}=$value;
	}
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	include('../funktionen.inc.php');
	$Text = Translate("Points_suchen.php");
	if($_SESSION['admin'] != 1) {exit;}
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	require '../conf_DH.php';
	echo "<div style='position: absolute; left: 10px; top: 10px;'><form name='Point_finden'>\n";
	echo "<b>".$Text[1]."</b>\n";
	echo "<select id='Liste' name='Ergebnis' size='15' multiple>\n";
	$Pointname = htmlentities(mysqli_real_escape_string($dbDH, $Pointname));
	$Path = htmlentities(mysqli_real_escape_string($dbDH, $Path));
	if($Point_ID == "%") {
		$query = "SELECT * FROM `Points` WHERE `Pointname` LIKE ? AND `Path` LIKE ? AND `Interface` LIKE ? ORDER BY `Path` ASC, `Pointname` ASC;";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_bind_param($stmt, "sss", $Pointname, $Pfad, $Schnittstelle);
	} else {
		$query = "SELECT * FROM `Points` WHERE `Point_ID` = ?;";
		$stmt = mysqli_prepare($dbDH,$query);		
		mysqli_stmt_bind_param($stmt, "i", $Point_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if($line["Point_ID"] > 0) {
			$Pointname = html_entity_decode($line["Pointname"]);
			$Description = html_entity_decode($line["Description"]);
			$Path = html_entity_decode($line["Path"]);
			$Interface = html_entity_decode($line["Interface"]);
			echo "<option value='".$line["Point_ID"]."'>".$Path." - ".$Pointname." - ".$Description." - ".$Interface."</option>";
		}
	}
	echo "</select>\n";
	echo "<br><input class='Schalter_Element' id='uebernehmen' name='Uebernehmen' value='".$Text[2]."' type='button' style='border-left: 0px;' onclick='uebertragen()'>\n";
	echo "</form></div>\n";
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH)
?>
</body>
</html>
