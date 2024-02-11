<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
$Sortierung = "";
if($sorters != "") {$Sortierung = " ORDER BY `".$sorters."`";}
if($size == NULL or $size =="") {$size = '1';}
$abfrage = "SELECT `User_ID` AS `id`,`User_ID`,`UserName`,`Password`,`last_active`,`mistrials`,`Full_Name`,`eMail`,`activated`,`Admin`,`Zeitstempel`,`Sprache` FROM `User`".$Sortierung." LIMIT ".($page - 1) * $size.",".$size.";";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Zaehler = 0;
$Antwort = array();
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$line["id"] = $Zaehler;
	$Antwort[] = $line;
	$Zaehler += 1;
}
$abfrage = "SELECT count(`User_ID`) AS `Anzahl` FROM `User`;";
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