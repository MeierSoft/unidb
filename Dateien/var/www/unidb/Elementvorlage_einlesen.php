<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
if($alle == 0) {
	$query = "SELECT * FROM `Vorl_Eigenschaften` WHERE `Auswahl` = 1 AND `Vorlage` = ?;";
} else {
	$query = "SELECT * FROM `Vorl_Eigenschaften` WHERE `Vorlage` = ?;";
}
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", $Bezeichnung);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Ergebnis = "";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$Ergebnis = $Ergebnis.json_encode($line)."@@@";
}
if ($Ergebnis != "") {
	$Ergebnis = substr($Ergebnis,0,strlen($Ergebnis)-3);
}
echo $Ergebnis;

mysqli_stmt_close($stmt);
mysqli_close($db);
?>
