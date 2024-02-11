<?php
//Get einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
require_once 'conf_DH.php';
if($Tagname > "") {
	$Pfad = "%";
	if(substr($Tagname,0,1) == "/") {
		$pos = strrpos($Tagname, "/");
		$Pfad = substr($Tagname, 0, $pos);
		$Tagname = substr($Tagname, $pos + 1);
	}
	$abfrage = "SELECT `Tag_ID` FROM `Tags` Where `Path` like ? AND `Tagname` like ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	$Tag_ID = $row[0];
	//Point_ID für den Tag finden
	$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
	if($Point_ID > 0) {echo $Tag_ID;}
} else {
	//Point_ID für den Tag finden
	$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
	$abfrage = "SELECT `Path`, `Tagname` FROM `Tags` Where `Tag_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo $line["Path"].",".$line["Tagname"];
}
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>