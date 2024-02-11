<?php
foreach($_GET as $key => $value){
	${$key}=$value;
}

//include('Sitzung.php');
require "./autoload.php";
require_once '../conf_DH.php';
session_start();

include('../funktionen.inc.php');
include('../Trend_funktionen.php');
if ($Point_ID > 0){
	$Tag_ID = $Point_ID;
}
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);

//Define the object
$sparkline = new Davaxi\Sparkline();

$data = array();

//letzten Wert vor dem Start des Trends aus dem Archiv lesen
//Tagdetails aus der Tabelle Tags lesen
$query = "SELECT `first_value` FROM `Tags` Where `Tag_ID` = ?;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line_Tags = mysqli_fetch_array($result, MYSQLI_ASSOC);
$first_value=$line_Tags["first_value"];
$Ende = strftime('%Y-%m-%d %H:%M:%S',$Zeitpunkt);
$Start = strftime('%Y-%m-%d %H:%M:%S',$Zeitpunkt - $Zeitraum);

if($first_value > $Start) {
	$Start2 = $first_value;
}else {
	$Start2 = $Start;
}
mysqli_stmt_close($stmt);
if($line_Tags["first_value"]>$Start) {
	$Start = $line_Tags["first_value"];
}

//erster Wert mit dem letzten Wert aus der Tabelle akt als Vorbelegung fuellen
$query = "SELECT `Timestamp`, `Value` FROM `akt` Where `Point_ID` = ? AND `Timestamp` >= ? ORDER BY `Timestamp` DESC Limit 1;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "is", $Point_ID, $Start2);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($line_Werte = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$Wert[0] = "";
	$erste_Zeit = strtotime($line_Werte["Timestamp"]);
	$erster_Wert = $line_Werte["Value"];
	$letzter_Wert = $line_Werte["Value"];
}
mysqli_stmt_close($stmt);
$gefunden = 0;
$Werte = lesen("rV",$Point_ID, $Start2, $Start2,1 ,0, 0, 0, 0);

if($step == 0) {
	$erste_Zeit = strtotime($Werte[0][0]);
} else {
	$erste_Zeit = strtotime($Start2);
}
$data[] = floatval($Werte[1][0]);

$i = 0;
$Werte = lesen("rV", $Point_ID, $Start2, $Ende,0 ,0, 0, 0, 0);
while($i < count($Werte[0])) {
	$Wert = floatval($Werte[1][$i]);
	$data[] = $Wert;
	$i = $i + 1;
}

if(count($data) > 0) {$gefunden = 1;}
//Wenn nichts im Archiv gefunden wurde, dann ist der letzte Wert aus akt der Startwert mit dem Zeitstempel Start
//$Wert[0] = $letzter_Wert;
if($gefunden == 0) {
	$erste_Zeit = $Start;
}

if ($akt == 1){
	//letzte Werte aus der akt Tabelle holen
	$query = "SELECT `Value`, `Timestamp` FROM `akt` WHERE `Point_ID` = ? AND `Timestamp` > ? ORDER BY `Timestamp` ASC;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "is", $Point_ID, $letzte_Zeit);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if($gefunden==0) {
			//Startwert interpolieren
			$Zeitdifferenz=strtotime($line_Value['Timestamp'])-$erste_Zeit;
			if($Zeitdifferenz==0) {
				$Wert = $erster_Wert;
			}else {
				$Steigung=($line_Value['Value']-$erster_Wert)/$Zeitdifferenz;
				$berechnet=(strtotime($Start)-$erste_Zeit)*$Steigung+$erster_Wert;
				$Wert = $berechnet;
			}
			$data[] = $Wert;
			$gefunden=1;
		}
		$letzter_Wert=$line_Value["Value"];
		$data[] = $line_Value['Value'];
	}
	mysqli_stmt_close($stmt);
	// Den Trend bis zum angegebenen Zeitpunkt zeichnen. Daher den letzten Wert zusaetzlich mit dem Zeitstempel des Scalenendes aufnehmen.
	$data[] = $letzter_Wert;
}

//Wegen Bug in sparkline alle Werte aus dem Array um den Minimumwert reduzieren
$Min = min($data);

foreach ($data as $i => $value) {
	$data[$i] = $value - $Min;
}

$sparkline->setWidth($Breite);
$sparkline->setHeight($Hoehe);
$sparkline->setData($data);
$sparkline->display();
mysqli_close($db);
mysqli_close($dbDH);
?>