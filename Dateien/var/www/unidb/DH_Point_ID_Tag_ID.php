<?php
//Get einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
require_once 'conf_DH.php';
$abfrage = "SELECT `Tag_ID` FROM `Tags` Where `Point_ID` = ? ORDER BY `Tag_ID` ASC LIMIT 1;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "s", $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_row($result);
$Tag_ID = $row[0];
mysqli_stmt_close($stmt);
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
if($Point_ID > 0) {echo $Tag_ID;}
mysqli_close($dbDH);
?>