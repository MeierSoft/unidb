<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');
$Antwort = array();
$Zaehler = 0;
$abfrage = "SELECT * FROM `Tagtable` WHERE `Point_ID` = ".$Point_ID." ORDER BY `Tag_ID` ASC;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$line["id"] = $Zaehler;
	$Antwort[] = $line;
	$Zaehler += 1;
}
echo(json_encode($Antwort));
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>