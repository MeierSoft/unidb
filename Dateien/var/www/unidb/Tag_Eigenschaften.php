<?php
	header("Content-Type: text/html; charset=iso-8859-1");
	require_once 'conf_DH.php';
	if($Eigenschaften == "") {$Eigenschaften = "*";}
	$query = "SELECT ".$Eigenschaften." FROM Tags WHERE Tagname LIKE ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "s", $Tag);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	//Berechtigung prüfen
	$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);
	if($Point_ID > 0) {
		$Ausgabe=$line;
		echo json_encode($Ausgabe);
	}
	// schliessen der Verbindung
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH);
?>