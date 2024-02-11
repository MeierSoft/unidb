<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
require_once '../conf_DH.php';
$Sortierung = "";
if($sorters != "") {$Sortierung = " ORDER BY `".$sorters."`";}
$abfrage = "SELECT * FROM `Meldungen`".$Sortierung." LIMIT ".($page - 1) * $size.",".$size.";";
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
$abfrage = "SELECT count(`Meldungen_ID`) AS `Anzahl` FROM `Meldungen`;";
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