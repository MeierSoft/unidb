<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');
$abfrage = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = ? AND `Parameter` = ?;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "is", $Schnittstellen_ID, $Schnittstelle);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$abfrage = "SELECT `Zusatz` FROM `Einstellungen` WHERE `Eltern_ID` = ".$line["Einstellung_ID"]." AND `Parameter` = 'Script';";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
echo $line["Zusatz"];
mysqli_close($dbDH);
?>