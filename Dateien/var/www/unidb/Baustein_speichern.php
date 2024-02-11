<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
	//$Baustein_Inhalt = htmlentities(mysqli_real_escape_string($db, $Baustein_Inhalt));
	$query = "INSERT INTO Bausteine (User_ID, Bezeichnung, Inhalt) VALUES (?, ?, COLUMN_CREATE('Baustein_Inhalt', ?));";
	uKol_schreiben(1,$query, "iss", [$_SESSION['User_ID'], $Bezeichnung, $Baustein_Inhalt]);
?>