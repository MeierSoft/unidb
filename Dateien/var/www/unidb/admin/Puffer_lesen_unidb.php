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
$abfrage = "SELECT * FROM `Puffer` ORDER BY `Timestamp` DESC LIMIT ".($page - 1) * $size.",".$size.";";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = array();
$Zaehler = 0;
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$line["id"] = $Zaehler;
	$Antwort[] = $line;
	$Zaehler += 1;
}
//$abfrage = "SELECT count(`Puffer_ID`) AS `Anzahl` FROM `Puffer`".$Filter;
$abfrage = "SELECT count(`Puffer_ID`) AS `Anzahl` FROM `Puffer`;";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Seiten = intval($line["Anzahl"]/$size);
if($line["Anzahl"]/$size > $Seiten) {$Seiten = $Seiten + 1;}
echo(json_encode(["last_page"=>$Seiten, "data"=>$Antwort]));
mysqli_stmt_close($stmt);
mysqli_close($db);
?>