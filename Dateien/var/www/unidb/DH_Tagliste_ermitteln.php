<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">

<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("DH_Tagliste_ermitteln.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
require_once 'conf_DH.php';
echo "<div style='position: absolute; left: 10px; top: 10px;'>\n";
echo "<form name = 'Dialog_Tagaustausch'>\n";
echo "<table cellspacing = '3'>\n";
echo "<tr class = 'Tabelle_Ueberschrift' bgcolor='#E5E5E5'><td>Tagname</td><td>".$Text[1]."</td><td>".$Text[2]."</td></tr>\n";
$Tags = explode(",",$Tagliste);
$Zaehler = 0;
$gefunden = array();
for ($i = 0; $i < count($Tags); $i++){
	$abfrage = "SELECT `Path`, `Tagname` FROM `Tags` Where `Tag_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "i", $Tags[$i]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Tago = $line["Tagname"];
	$Pfado = $line["Path"];
	$abfrage = "SELECT `Tag_ID` FROM `Tags` Where `Tagname` = ? AND `Path` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "ss", $Tago, $Pfad);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if(mysqli_num_rows($result) > 0){
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Tag_IDn = $line["Tag_ID"];
		echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$Tago."</td><td class='Tabelle_Zelle'>".$Text[3]."</td><td><input value='".$Tags[$i].",".$Tag_IDn."' type='checkbox' name='T".$Zaehler."' checked></td></tr>\n";
	} else {
		echo "<tr class='Tabellenzeile'><td class='Tabelle_Zelle'>".$Tago."</td><td class='Tabelle_Zelle'>".$Text[4]."</td><td></td></tr>\n";
	}
}
mysqli_stmt_close($stmt);
mysqli_close($dbDH);
echo "</table><br><br>\n";
echo "<input type='button' name='übernehmen' value='".$Text[2]."' class='Schalter_Element' onclick='Pfadaenderung_uebernehmen();'>\n";
?>
</form></div></body></html>
