<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
require_once './conf_DH.php';
$Text = Translate("Tabelle.php");
include('Trend_funktionen.php');
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";

if($Art == "raw values") {$Art = "Rohwerte";}
if($Art == "Ruwe waarden") {$Art = "Rohwerte";}
if($Art == "Valeurs brutes") {$Art = "Rohwerte";}

if($Art == "Rohwerte") {
	$uebersetzt = $Text[3];
} else {
	$uebersetzt = $Art;
}

$Ergebnis = "<b>".$Tag."   ".$Bezeichnung."</b>   ".$Text[1].": <b>".$uebersetzt."</b><br><br>";
$Ergebnis = $Ergebnis."<table cellpadding='5' id='trendtabelle'>";

if ($Art == "Rohwerte") {$Art = "rV";}
if ($Art == "") {$Art = "rV";}

//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);

if ($akt==1){
	//letzter Wert aus der akt Tabelle holen
	$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID = ? AND ".$Art2." ORDER BY Timestamp DESC LIMIT 1;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "i", $Point_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Ergebnis = $Ergebnis."<tr><td>".$line_Value['Timestamp']."</td><td>".round($line_Value['Value'],$Dezimalstellen)."</td><td>".$EUDESC."</td></tr>";
	}
	mysqli_stmt_close($stmt);
}

$Werte = lesen($Art,$Point_ID, $Start, $Ende,0 ,0, 0, 0, 0);
$a = $Werte[1];
$b = $Werte[0];
$Anzahl =count($a);
$i = 0;
while ($i < $Anzahl) {
	$Ergebnis = $Ergebnis."<tr><td>".$b[$i]."</td><td>".round($a[$i],$Dezimalstellen)."</td><td>".$EUDESC."</td></tr>";
	$i = $i + 1;
} 
echo $Text[4]." ".$Anzahl.$Text[5]."<br><br>";
echo $Ergebnis;
// schliessen der Verbindung
mysqli_close($dbDH);
mysqli_close($db);
?>
</table>
</body>
</html>
