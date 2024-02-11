<?php
	//Get und POST einlesen
	foreach($_GET as $key => $value){${$key}=$value;}
	session_start();
	header("X-XSS-Protection: 1");
	include('funktionen.inc.php');
	$Text = Translate("DH_Tag_suchen.php");
	require 'conf_DH.php';
	$Antwort = "";
	$Suchtext = htmlentities(mysqli_real_escape_string($dbDH, $Suchtext));

	$query = "SELECT * FROM Tags WHERE Tagname LIKE ? OR Description LIKE ? OR Path LIKE ? ORDER BY Tagname ASC;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "sss", $Suchtext, $Suchtext, $Suchtext);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		//Point_ID für den Tag finden um die Berechtigung zu testen
		$Point_ID = Point_ID_finden($line_Tag["Tag_ID"], $dbDH);
		if($Point_ID > 0) {
			$Tagname = html_entity_decode($line_Tag["Tagname"]);
			$Description = html_entity_decode($line_Tag["Description"]);
			$Antwort = $Antwort.$line_Tag["Path"].",".$Tagname.",".$Description."@@@";
		}
	}
	if($Antwort > "") {
		echo substr($Antwort,0,strlen($Antwort)-3);
	} else {
		echo $Text[0];
	}
	mysqli_stmt_close($stmt);
	// schliessen der Verbindung
	mysqli_close($dbDH)
?>