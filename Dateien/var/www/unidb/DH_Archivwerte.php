<?php
foreach ($_REQUEST as $key=>$val){${$key}=$val;}
session_start();
include('funktionen.inc.php');
require_once 'conf_DH.php';
include('Trend_funktionen.php');
if($typ == "Rohwert" or $typ == "Rohwerte" or $typ == "ROHWERT" or $typ == "ROHWERTE") {$Art = "rV";}
if($typ == "Stundenmittelwerte" or $typ == "STUNDENMITTELWERTE") {$Art = "hMW";}
if($typ == "Tagesmittelwert" or $typ == "TAGESMITTELWERTE") {$Art = "dMW";}
if($typ == "Min-Werte stündlich" or $typ == "Min-Wert Stunde" or $typ == "MIN-WERTE STÜNDLICH") {$Art = "hMin";}
if($typ == "Max-Werte stündlich" or $typ == "Max-Wert Stunde" or $typ == "MAX-WERTE STÜNDLICH") {$Art = "hMax";}
if($typ == "Min-Max-Werte stündlich" or $typ == "MIN-MAX-WERTE STÜNDLICH") {$Art = "hMinMax";}
if($typ == "Min-Werte täglich" or $typ == "Min-Wert Tag" or $typ == "MIN-WERTE TÄGLICH") {$Art = "dMin";}
if($typ == "Max-Werte täglich" or $typ == "Max-Wert Tag" or $typ == "MAX-WERTE TÄGLICH") {$Art = "dMax";}
if($typ == "Min-Max-Werte täglich" or $typ == "Min-Max-Wert Tag" or $typ == "MIN-Max-WERTE TÄGLICH") {$Art = "dMinMax";} 
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
if($line["first_value"] > $von) {
	echo "Sie können erst Werte ab ".$line["first_value"]." abfragen.,0";
	mysqli_close($dbDH);
	exit;
}
$Einheit = $line["EUDESC"];
$Dezimalstellen = intval($line["Dezimalstellen"]);
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);
$Ausgabe = $Einheit.";";
$Werte = lesen($Art,$Point_ID, $von, $bis,0 ,0, 1, 1, 1);
$a = $Werte[1];
$b = $Werte[0];
$c = $Werte[2];
$d = $Werte[3];
$e = $Werte[4];
$Anzahl =count($a);
$i = 0;
while ($i < $Anzahl) {
	$Ausgabe = $Ausgabe.$b[$i].",".round($a[$i],$Dezimalstellen).",".$c[$i].",".$d[$i].",".$e[$i].";";
	$i = $i + 1;
} 
$Ausgabe = substr($Ausgabe, 0, strlen($Ausgabe) - 1); 
echo $Ausgabe;
mysqli_close($dbDH);
?>