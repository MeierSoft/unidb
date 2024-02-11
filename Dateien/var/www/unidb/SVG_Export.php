<?php
session_start();
header("X-XSS-Protection: 1");
//include('funktionen.inc.php');
include('Sitzung.php');
$abfrage = "SELECT column_get(`Inhalt`, 'SVG' as CHAR) as `SVG` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
echo html_entity_decode($line["SVG"]);
mysqli_stmt_close($stmt);
mysqli_close($db);
?>