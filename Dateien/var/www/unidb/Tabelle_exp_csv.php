<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
header('Content-Disposition: attachment;filename=Export_'.$exTitel.'.csv');
header('Content-Type: text/csv; charset=utf-8');
$output = fopen('php://output', 'w');
$Text = Translate("Tabelle.php");
require_once './conf_DH.php';
include('Trend_funktionen.php');
$x = str_replace('"', '', $exParameterfeld);
$x = substr($x, 2, strlen($x) - 4);
$Parameterfeld = explode("},{", $x);
for ($i=0; $i < count($Parameterfeld); $i++){
	$Tagfeld = explode(",", $Parameterfeld[$i]);
	$ar = explode(":", $Tagfeld[0]);
	$Tag_ID = $ar[1];
	$ar = explode(":", $Tagfeld[1]);
	$Art = $ar[1];
	$ar = explode(":", $Tagfeld[2]);
	$uTime = $ar[1];
	$ar = explode(":", $Tagfeld[3]);
	$vt = $ar[1];
	$ar = explode(":", $Tagfeld[4]);
	$vt_interpol = $ar[1];
	//Art bearbeiten
	if($Art == "" or $Art == NULL) {
		$Art = "Rohdaten";
	}
	if ($Art == "Rohdaten"){
		$Art = "rV";
	}
	//Point_ID für den Tag finden
	$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
	//Tageigenschaften lesen
	$query = "SELECT `Dezimalstellen`, `Tagname`, `Description` FROM `Tags` WHERE `Tag_ID` = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	//Kopfzeile schreiben
	$Kopz = array();
	$Kopz[] = "Tagname";
	$Kopz[] = "Timestamp";
	$Kopz[] = "Value";
	if($uTime > 0) {$Kopz[] = "uTime";}
	if($vt > 0) {$Kopz[] = "vt";}
	if($vt_interpol > 0) {$Kopz[] = "vt_interpol";}
	fputcsv($output, $Kopz);
	//Werte lesen
	$Werte = lesen($Art,$Point_ID, $exStart, $exEnde,0 ,0, $uTime, $vt_interpol, $vt);
	$Zeile = array();
	$a = $Werte[0];
	$b = $Werte[1];
	if($uTime == 1) {$c = $Werte[2];}
	if($vt_interpol == 1) {$d = $Werte[4];}
	if($vt == 1) {$e = $Werte[3];}
	$Anzahl =count($a);
	$z = 0;
	$Tagname = ["Tagname" => $line_Tag["Tagname"]];
	while ($z < $Anzahl) {
		$Zeile[] = $a[$z];
		$Zeile[] = floatval($b[$z]);
		if($uTime == 1) {$Zeile[] = $c[$z];}
		if($vt_interpol == 1) {$Zeile[] = floatval($d[$z]);}
		if($vt == 1) {$Zeile[] = floatval($e[$z]);}
		$Ausgabe = $Tagname + $Zeile;
		$Zeile = array();
		fputcsv($output, $Ausgabe);
		$z = $z + 1;
	} 
	$Ausgabe = array();
	fputcsv($output, $Ausgabe);
}
// schliessen der Verbindung
mysqli_close($dbDH);
mysqli_close($db);
?>
