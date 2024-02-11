<?php
foreach ($_REQUEST as $key=>$val){${$key}=$val;}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
require_once 'conf_DH.php';
include('Trend_funktionen.php');
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
	echo "0,Sie können erst Werte ab ".$line["first_value"]." abfragen.";
	mysqli_close($dbDH);
	exit;
}
$Einheit = $line["EUDESC"];
$Dezimalstellen = intval($line["Dezimalstellen"]);
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);

//Array erzeugen
$Werte = array();

$Wertex = lesen("rV", $Point_ID, $von, $bis,1 ,1 , 1, 1, 1);
$Wert1 = $Wertex[1][0];
$Zeit1 = $Wertex[2][0];
$i = 1;
while ($i < count($Wertex[0]) - 1){
	if($Wertex[0][$i] != "") {
		$Wert[0] = $Wertex[1][$i];
		$Wert[1] = $Wertex[2][$i];
		$Wert[2] = $Wertex[4][$i];
		$Wert[3] = $Wertex[3][$i];
		$Werte[] = $Wert;
	}
	$i++;
}
$maxIndex = count($Wertex[0]) - 1;
$Wert2 = $Wertex[1][$i];
$Zeit2 = $Wertex[2][$i];
//jetzt kann gerechnet werden
$ZP_von = strtotime($von);
$ZP_bis = strtotime($bis);
if($step == 1) {
	//interpolierter Wert zum Startzeitpunkt
	$deltaZeit = $ZP_von - $Zeit1;
	//neuer vt für den ersten Wert nach dem Startzeitpunkt
	$vt = $Wert1 * ($Werte[0][1] - $ZP_von);
	//aufsummieren der vt
	for ($i = 1; $i < $maxIndex; $i++) {
		if($Werte[$i][2] != null) {$vt = $vt + $Werte[$i][2];}
	}
	//interpolierter Wert zum Endezeitpunkt
	//neuer vt für den Wert zum Endezeitpunkt hinzuaddieren
	if($Werte[$maxIndex][0] != null) {$vt = $vt + $Werte[$maxIndex][0] * ($ZP_bis - $Werte[$maxIndex][1]);}
} else {
	//interpolierter Wert zum Startzeitpunkt
	$deltaZeit = $ZP_von - $Zeit1;
	$Diff = $Wert1 - $Werte[0][0];
	$Steigung = $Diff / $deltaZeit;
	$Startwert = ($ZP_von - $Zeit1) * $Steigung + $Wert1;
	//neuer interpolierter vt für den ersten Wert nach dem Startzeitpunkt
	$vt = ($Werte[0][0] + $Startwert) / 2 * ($Werte[0][1] - $ZP_von);
	//aufsummieren der vt
	for ($i = 0;$i < $maxIndex;$i++){
		$vt = $vt + $Werte[$i][3];
	}
	//interpolierter Wert zum Endezeitpunkt
	$maxIndex = count($Werte) - 1;
	$deltaZeit = $Zeit2 - $ZP_bis;
	$Diff = $Werte[$maxIndex][0] - $Wert2;
	$Steigung = $Diff / $deltaZeit;
	$i = 1;
	while($Wertex[0][$maxIndex - $i] > $bis) {$i = $i +1;}
	$i = $maxIndex - $i;
	$Endewert = ($ZP_bis - $Werte[$i][1]) * $Steigung + $Werte[$i][0];
	//neuer interpolierter vt für den Wert zum Endezeitpunkt hinzuaddieren
	$vt = $vt + ($Endewert + $Werte[$i][0]) / 2 * ($ZP_bis - $Werte[$i][1]);
}
//Mittelwert berechnen
$Wert = $vt / ($ZP_bis - $ZP_von);
$Wert = round($Wert,$Dezimalstellen);
echo $Wert.",".$Einheit;
mysqli_close($dbDH);
?>