<?php
foreach ($_REQUEST as $key=>$val){${$key}=$val;}
session_start();
include('funktionen.inc.php');
require_once 'conf_DH.php';
include('Trend_funktionen.php');
if($typ == "Rohwert" or $typ == "ROHWERT") {$Art = "rV";}
if($typ == "Stundenmittelwert" or $typ == "STUNDENMITTELWERT") {$Art = "hMW";}
if($typ == "Tagesmittelwert" or $typ == "TAGESMITTELWERT") {$Art = "dMW";}
if($typ == "Min-Wert Stunde" or $typ == "MIN-WERT STUNDE") {$Art = "hMin";}
if($typ == "Max-Wert Stunde" or $typ == "MAX-WERT STUNDE") {$Art = "hMax";}
if($typ == "Min-Max-Werte stündlich" or $typ == "MIN-MAX-WERTE STÜNDLICH") {$Art = "hMinMax";}
if($typ == "Min-Wert Tag" or $typ == "MIN-WERT TAG") {$Art = "dMin";}
if($typ == "Max-Wert Tag" or $typ == "MAX-WERT TAG") {$Art = "dMax";}
if($typ == "Min-Max-Wert Tag" or $typ == "MIN-Max-WERT TAG") {$Art = "dMinMax";} 
//Taginfos ermitteln
$Pfad = "%";
if(substr($Tagname,0,1) == "/") {
	$pos = strrpos($Tagname, "/");
	$Pfad = substr($Tagname, 0, $pos + 1);
	$Tagname = substr($Tagname, $pos + 1);
}
$abfrage = "SELECT `Tag_ID`, `EUDESC`, `Dezimalstellen`, `first_value` FROM `Tags` WHERE `Path` = ? AND `Tagname` = ?;";
$stmt = mysqli_prepare($dbDH, $abfrage);
mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
if($line["first_value"] > $Zeitpunkt) {
	echo "Sie können erst Werte ab ".$line["first_value"]." abfragen.";
	mysqli_close($dbDH);
	exit;
}
$Einheit = $line["EUDESC"];
$Dezimalstellen = intval($line["Dezimalstellen"]);
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);
if($interpoliert == "Wert davor" or $interpoliert == "interpoliert" or $interpoliert == "WERT DAVOR" or $interpoliert == "INTERPOLIERT") {
	$Werte = lesen($Art, $Point_ID, $Zeitpunkt, $Zeitpunkt,1 ,0 , 0, 0, 0);
	$Wert = $Werte[1][0];
	$Wert1 = $Werte[1][0];
	$Zeit1 = strtotime($Werte[0][0]);
	$Zeitpunkt_db = $Werte[0][0];
}
if($interpoliert == "Wert danach" or $interpoliert == "interpoliert" or $interpoliert == "WERT DANACH" or $interpoliert == "INTERPOLIERT") {
	$Werte = lesen($Art, $Point_ID, $Zeitpunkt, $Zeitpunkt,0 ,1 , 0, 0, 0);
	$Wert = $Werte[1][0];
	$Wert2 = $Werte[1][0];
	$Zeit2 = strtotime($Werte[0][0]);
	$Zeitpunkt_db = $Werte[0][0];
}
if($interpoliert == "interpoliert" or $interpoliert == "INTERPOLIERT") {
	$deltaZeit = $Zeit2 - $Zeit1;
	$Diff = $Wert2 - $Wert1;
	$Steigung = $Diff / $deltaZeit;
	$Zielzeitpunkt = strtotime($Zeitpunkt);
	$Wert = ($Zielzeitpunkt - $Zeit1) * $Steigung + $Wert1;
	$Zeitpunkt_db = $Zeitpunkt; 
}	
	
$Wert = round($Wert, $Dezimalstellen);
echo $Wert.",".$Zeitpunkt_db.",".$Einheit;
mysqli_close($dbDH);
?>