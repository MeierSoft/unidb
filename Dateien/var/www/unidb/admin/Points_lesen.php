<?php
include('../Sitzung.php');
session_start();
//if($_SESSION['admin'] != 1) {exit;}
include( '../conf_DH.php');

$Sortierung = "";
if($sorters != "") {$Sortierung = " ORDER BY `".$sorters."`";}
$Antwort = array();
$Zaehler = 0;
$Seiten = 1;
if($Points > "") {
	$Pointliste = explode(",", $Points);
	$Punkte = "(";
	foreach($Pointliste as $Point_ID) {
$Punkte = $Punkte."`Point_ID` = ".$Point_ID." OR ";
	}
	$Punkte = substr($Punkte,0,strlen($Punkte)-4).")";
	$abfrage = "SELECT * FROM `Points` WHERE ".$Punkte." ".$Sortierung." LIMIT ".($page - 1) * $size.",".$size.";";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
$abfrage = "SELECT * FROM `akt` WHERE `Point_ID` = ".$line["Point_ID"]." ORDER BY `Timestamp` DESC LIMIT 1;";
$stmt1 = mysqli_prepare($dbDH,$abfrage);
mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);
$line_Tag = mysqli_fetch_array($result1, MYSQLI_ASSOC);
$line["id"] = $Zaehler;
$line["Value"] = $line_Tag["Value"];
$line["Timestamp"] = $line_Tag["Timestamp"];
$Antwort[] = $line;
$Zaehler += 1;
	}
	$abfrage = "SELECT count(`Point_ID`) AS `Anzahl` FROM `Points` WHERE ".$Punkte.";";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Seiten = intval($line["Anzahl"]/$size);
	if($line["Anzahl"]/$size > $Seiten) {$Seiten = $Seiten + 1;}
}
echo(json_encode(["last_page"=>$Seiten, "data"=>$Antwort]));
mysqli_stmt_close($stmt);
mysqli_close($db);
?>