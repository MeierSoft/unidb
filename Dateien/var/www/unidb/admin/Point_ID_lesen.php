<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');
$abfrage = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = ? AND `Path` = ?;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "ss", $Pointname, $Path);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
echo $line["Point_ID"];
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>