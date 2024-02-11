<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
require_once '../conf_DH.php';
$Bedingungen = " WHERE ";
if($von > "") {$Bedingungen = $Bedingungen."`Timestamp` >= '".$von."' AND ";}
if($bis > "") {$Bedingungen = $Bedingungen."`Timestamp` <= '".$bis."' AND ";}
if($source > "") {$Bedingungen = $Bedingungen."`Source` like '".$source."' AND ";}
if($text > "") {$Bedingungen = $Bedingungen."`Text` like '".$text."' AND ";}
if($Bedingungen > " WHERE ") {
	$Bedingungen = substr($Bedingungen,0,strlen($Bedingungen) - 4);
} else {
	$Bedingungen = " ";
}
$abfrage = "SELECT * FROM `Log`".$Bedingungen."ORDER BY `Timestamp` DESC LIMIT ".($page - 1) * $size.",".$size.";";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = array();
$Zaehler = 0;
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$line["id"] = $Zaehler;
	$Antwort[] = $line;
	$Zaehler += 1;
}
$abfrage = "SELECT count(`Timestamp`) AS `Anzahl` FROM `Log`".$Bedingungen.";";
$stmt = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Seiten = intval($line["Anzahl"]/$size);
if($line["Anzahl"]/$size > $Seiten) {$Seiten = $Seiten + 1;}
echo(json_encode(["last_page"=>$Seiten, "data"=>$Antwort]));
mysqli_stmt_close($stmt);
mysqli_close($db);
?>