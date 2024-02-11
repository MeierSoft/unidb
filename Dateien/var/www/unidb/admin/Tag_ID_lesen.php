<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');
$abfrage = "SELECT `Tag_ID` FROM `Tagtable` WHERE `Tagname` = ? AND `Path` = ?;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "ss", $Tagname, $Path);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
echo $line["Tag_ID"];
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>