<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
$query="SELECT * FROM `Elemente_Eigenschaften` WHERE `Baum_ID` = ? AND `Elemente_ID` = ? AND `Server_ID` = ?"; 
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "iii", $Baum_ID, $Elemente_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Ergebnis = "";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	if(strlen($Ergebnis) == 0) {
		$Ergebnis = $Ergebnis.json_encode($line);
	} else {
		$Ergebnis = $Ergebnis."@@@".json_encode($line);
	}
}
echo $Ergebnis;
mysqli_stmt_close($stmt);
mysqli_close($db);
?>
