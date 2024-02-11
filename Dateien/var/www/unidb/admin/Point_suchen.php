<?php
	//Get und POST einlesen
	foreach($_GET as $key => $value){
		${$key}=$value;
	}
	session_start();
	include('../funktionen.inc.php');
	if($_SESSION['admin'] != 1) {exit;}
	header("Content-Type: text/html; charset=utf-8");
	require '../conf_DH.php';
	$Text = Translate("Point_suchen.php");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<div style='position: absolute; left: 10px; top: 0px;'><b>".$Text[1]."</b></div>\n";
	echo "<div style='position: absolute; left: 10px; top: 30px;'><form name='Point_finden'>\n";
	echo "<select id='Liste' name='Ergebnis' size='15' onclick='uebertragen()'>\n";
	$Pointname = htmlentities(mysqli_real_escape_string($dbDH, $Pointname));
	$Path = htmlentities(mysqli_real_escape_string($dbDH, $Path));
	$query = "SELECT * FROM `Points` WHERE `Pointname` LIKE ? AND `Path` LIKE ? AND `Interface` LIKE ? ORDER BY `Path` ASC, `Pointname` ASC;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "sss", $Pointname, $Pfad, $Schnittstelle);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if($line["Point_ID"] > 0) {
			$Pointname = html_entity_decode($line["Pointname"]);
			$Description = html_entity_decode($line["Description"]);
			$Path = html_entity_decode($line["Path"]);
			$Interface = html_entity_decode($line["Interface"]);
			echo "<option>".$line["Point_ID"]." - ".$Path." - ".$Pointname." - ".$Description."</option>";
		}
	}
	echo "</select></form></div>";
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH)
?>
