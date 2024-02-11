<?php
//Get einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
require_once 'conf_DH.php';
$Antwort = array();
$Pfad = "%";
if(substr($Tagname,0,1) == "/") {
	$pos = strrpos($Tagname, "/");
	$Pfad = substr($Tagname, 0, $pos + 1);
	$Tagname = substr($Tagname, $pos + 1);
}
$abfrage = "SELECT * FROM `Tags` Where `Path` like ? AND `Tagname` like ?;";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Antwort[] = $line;
echo(json_encode($Antwort));
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
?>