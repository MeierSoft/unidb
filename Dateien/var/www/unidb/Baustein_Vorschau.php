<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	$query = "SELECT column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt` FROM `Bausteine` WHERE `Baustein_ID` = ?;";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "i", $Baustein_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Inhalt = html_entity_decode($line["Inhalt"]);
	echo $Inhalt;
	mysqli_stmt_close($stmt);
	mysqli_close($db);
?>