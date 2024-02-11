<?php
foreach($_GET as $key => $value){
	${$key}=$value;
}
require_once './conf_DH.php';
include('Trend_funktionen.php');
$Points = [96,97,151,152,154,155,183,223,224,225,226];
$Zeitstempel = strftime('%Y-%m-%d %H:%M:%S',($ab));
//Werte aus der akt Tabelle holen
$query = "SELECT * FROM `akt` WHERE `Timestamp` > '".$Zeitstempel."' AND (`Point_ID` = ".$Points[0];
$i = 1;
while($i < count($Points)) {
	$query = $query." OR `Point_ID` = ".$Points[$i];
	$i = $i + 1;
}
$query = $query.");";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Ergebnis = "";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$Ergebnis = $Ergebnis.$line["Point_ID"].",".$line["Timestamp"].",".$line["Value"].";";
}
mysqli_stmt_close($stmt);

//Werte aus dem Archiv holen
$i = 0;
$Ende = strftime('%Y-%m-%d %H:%M:%S',(time()));
while ($i < count($Points)) {
	$Werte = lesen("rV", $Points[$i], $Zeitstempel, $Ende,0 ,0, 0, 0, 0);
	$x = 0;
	while ($x < count($Werte[1])) {
		$Ergebnis = $Ergebnis.$Points[$i].",'".$Werte[0][$x]."',".$Werte[1][$x].";";
		$x = $x + 1;
	}
	$i = $i + 1;
}
echo substr($Ergebnis,0,-1);

// schliessen der Verbindung
mysqli_close($dbDH);
?>