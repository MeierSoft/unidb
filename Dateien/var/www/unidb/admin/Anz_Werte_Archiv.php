<?php
session_start();
include('../Sitzung.php');
include('Trend_funktionen.php');
if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');
$abfrage = "SELECT count(Point_ID) AS Werte FROM `Archiv` WHERE `Point_ID` = ?;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "i", $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
echo $line["Werte"];
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>