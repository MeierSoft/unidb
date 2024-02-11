<style type="text/css">
.Tabelle_Ueberschrift {
	font-weight: bold;
}

.Tabellenzeile {
	font-weight: normal;
	background-color: #E5E5E5;
}
</style>


<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
header('Content-Disposition: attachment;filename=Export_'.$exTitel.'.html');
header('Content-Type: text/html; charset=utf-8');
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
	//Kopfzeile schreiben
	$Kopz = "<table cellpadding='5px'><tr class='Tabelle_Ueberschrift'><td>Tagname</td><td>Timestamp</td><td>Value</td>";
	if(strlen($uTime) > 0) {$Kopz = $Kopz."<td>uTime</td>";}
	if(strlen($vt) > 0) {$Kopz = $Kopz."<td>vt</td>";}
	if(strlen($vt_interpol) > 0) {$Kopz = $Kopz."<td>vt_interpol</td>";}
	$Kopz = $Kopz."</tr>";
	echo $Kopz;
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
	while ($z < $Anzahl) {
		$Ausgabe = "<tr class='Tabellenzeile'><td>".$line_Tag["Tagname"]."</td><td>".$a[$z]."</td><td>".floatval($b[$z]);
		if(strlen($uTime) > 0) {$Ausgabe = $Ausgabe."</td><td>".$c[$z];}
		if(strlen($vt) > 0) {$Ausgabe = $Ausgabe."</td><td>".floatval($d[$z]);}
		if(strlen($vt_interpol) > 0) {$Ausgabe = $Ausgabe."</td><td>".floatval($e[$z]);}
		$Ausgabe = $Ausgabe."</td></tr>";
		echo $Ausgabe;		
		$z = $z + 1;
	} 
	echo "<tr height='30px'><td></td></tr>";
	mysqli_stmt_close($stmt);
}
echo "</table>";
// schliessen der Verbindung
mysqli_close($dbDH);
mysqli_close($db);
?>
