<?php
//Get einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
require_once 'conf_DH.php';
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
$abfrage = "SELECT `".$Eigenschaft."` FROM `Tags` Where `Point_ID` = ".$Point_ID.";";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_row($result);
echo $row[0];
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>