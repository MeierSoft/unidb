<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$query = "SELECT DISTINCT `Hilfe_ID`,`Eltern_ID`,`Titel_".$_SESSION['Sprache']."` AS `Bezeichnung` FROM `Hilfe` WHERE `aktiv` = 1 AND (`Titel_".$_SESSION['Sprache']."` like '%".$Suchtext."%' OR `".$_SESSION['Sprache']."` like '%".$Suchtext."%');";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = "<table class='table'>\n";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$Antwort = $Antwort."<tr><td><a href='Hilfe3.php?Hilfe_ID=".$line["Hilfe_ID"]."&nreg=0' target='Hilfe_Hauptrahmen'>".$line["Bezeichnung"]."</a></td></tr>\n";
}
$Antwort = $Antwort."</table>\n";
echo $Antwort;
mysqli_stmt_close($stmt);
mysqli_close($db);
?>