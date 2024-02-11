<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
if(intval($Replikation) == 1) {
	include("Formular_func.inc.php");
	if(FKol_schreiben($SQL,$DB) == 1) {echo "Die Verbindung zu einem der Server ist unterbrochen, daher können momentan keine Daten hinzugefügt, gelöscht oder geändert werden.";}
} else {
	$db_Satz = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Datenbank']);
	$stmt = mysqli_prepare($db_Satz,$SQL);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	mysqli_close($db_Satz);
}
?>