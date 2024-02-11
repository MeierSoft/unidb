<?php
foreach($_GET as $key => $value){
	${$key}=$value;
}
session_start();
header("X-XSS-Protection: 1");
include('funktionen.inc.php');
include('Trend_funktionen.php');
require_once 'conf_DH.php';
require_once ('../jpgraph/jpgraph.php');
require_once ('../jpgraph/jpgraph_line.php');

$data = array();
$datax = array();
$Scala_max = "";
$Scala_min = "";
if($Zeitzahl == NULL) {$Zeitzahl = $Zeitpunkt;}
$Start = date("Y-m-d H:i:s", $Zeitzahl-$Zeitraum);
$Ende = date("Y-m-d H:i:s", $Zeitzahl);
//Point_ID für den Tag finden
if($Tag == NULL) {$Tag = $Tag_ID;}
$Point_ID = Point_ID_finden($Tag, $dbDH);

//Tagdetails aus der Tabelle Tags lesen
$query = "SELECT * FROM `Tags` Where `Point_ID` = ?;";
$stmt = mysqli_prepare($dbDH,$query);
mysqli_stmt_bind_param($stmt, "i", $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($line_Tags = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

	$Scala_max = $line_Tags["Scale_max"];
	$Scala_min = $line_Tags["Scale_min"];
	$EUDESC = $line_Tags["EUDESC"];
	$step = $line_Tags["step"];
}
mysqli_stmt_close($stmt);

//Gibt es benutzerspezifische Einstellungen fuer die Skala?
$query = "SELECT * FROM `User_Skalen` WHERE `User_ID` = ? AND `Point_ID` = ?;";
$stmt = mysqli_prepare($dbDH,$query);
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['User_ID'], $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
if(mysqli_num_rows($result) > 0){
	$Scala_max = $line['max'];
	$Scala_min = $line['min'];
}
mysqli_stmt_close($stmt);

$i = 0;
//Archivwerte für den Zeitraum lesen
$Werte = lesen("rV", $Point_ID, $Start, $Ende,1 ,1, 0, 0, 0);
while(count($Werte[0]) > $i and $Werte[0][$i] < $Start) {
	$i = $i + 1;
}
if($i > 0) {$i = $i -1;}
while($i < count($Werte[0])) {
	if($Werte[0][$i] < $Start) {
		$Zeitpu = strtotime($Werte[0][$i]);
		$Zeitpo = strtotime($Werte[0][$i + 1]);
		$Zeitps = strtotime($Start);
		if($Werte[1][$i + 1] != $Werte[1][$i]) {
			$Steigung = (floatval($Werte[1][$i + 1]) - floatval($Werte[1][$i])) / ($Zeitpo - $Zeitpu);
			$Wert = ($Zeitps - $Zeitpu) * $Steigung + floatval($Werte[1][$i]);
		} else {
			$Wert = floatval($Werte[1][$i]);
		}
		$datax[] = strtotime($Start);
		$data[] = $Wert;
		$letzte_Zeit = $Start;
	} else {
		if($Werte[0][$i] <= $Ende) {
			$Wert = floatval($Werte[1][$i]);
			$Zeitp = strtotime($Werte[0][$i]);
			if($i + 1 < count($Werte[0])) {
				if($Werte[0][$i + 1] > $Ende) {
					$Zeitpu = strtotime($Werte[0][$i]);
					$Zeitpo = strtotime($Werte[0][$i + 1]);
					$Zeitpe = strtotime($Ende);
					$Zeitp = $Zeitpe;
					if($Werte[1][$i + 1] != $Werte[1][$i]) {
						$Steigung = (floatval($Werte[1][$i + 1]) - floatval($Werte[1][$i])) / ($Zeitpo - $Zeitpu);
						$Wert = ($Zeitpe - $Zeitpu) * $Steigung + floatval($Werte[1][$i]);
					} else {
						$Wert = floatval($Werte[1][$i]);
					}
				}
			}
			$datax[] = $Zeitp;
			$data[] = $Wert;
			if ($akt==1) {$letzte_Zeit = strftime('%Y-%m-%d %H:%M:%S',$Zeitp);}
		}
	}
	$i = $i + 1;
}

if ($akt==1){
	//alle Werte aus der akt Tabelle holen, die >= Start sind
 	$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID = ? AND Timestamp >= ? ORDER BY Timestamp ASC;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "is", $Point_ID, $letzte_Zeit);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line_Werte = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$datax[] = strtotime($line_Werte['Timestamp']);
		$data[] = floatval($line_Werte['Value']);
	}
	mysqli_stmt_close($stmt);
}
mysqli_close($dbDH);

// Callback formatting function for the X-scale to convert timestamps
// to hour and minutes or dates
function TimeCallback($aVal) {
	global $Zeitraum;
	if($Zeitraum <= 86400) {
    	return Date('H:i', $aVal);
   } else {
   	return Date('d.m.y', $aVal);
   }
}

$adjstart = $Zeitpunkt-$Zeitraum;
$adjend = $adjstart + $Zeitraum;

$graph = new Graph($Breite,$Hoehe);
$graph->clearTheme();
$graph->SetMargin(40,15,10,20);

// Now specify the X-scale explicit but let the Y-scale be auto-scaled
// 0,0 -> senkrecht auto, $adjstart,$adjend -> waagerecht
$graph->SetScale("intlin",$Scala_min,$Scala_max,$adjstart,$adjend);
//$graph->title->Set("Example on TimeStamp Callback");

// Setup the callback and adjust the angle of the labels
$graph->xaxis->SetLabelFormatCallback('TimeCallback');
//$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->title->Set($EUDESC);

// Set the labels Mainticks, Minorticks
$graph->xaxis->scale->ticks->Set($Zeitraum / 4,$Zeitraum / 20);

$line = new LinePlot($data,$datax);
$line->SetColor('blue');
$line->SetWeight(2);
if ($step==1){$line->SetStepStyle();}
$graph->xgrid->Show();
$graph->ygrid->Show();
$graph->Add($line);

$graph->Stroke();

?>