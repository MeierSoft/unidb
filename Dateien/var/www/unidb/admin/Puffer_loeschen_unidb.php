<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$ID.";";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	${$line["Parameter"]} = $line["Wert"];
}
$db = mysqli_connect($IP,$User,$Password,$Database);
$stmt = mysqli_prepare($db,$SQL);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($db);
?>