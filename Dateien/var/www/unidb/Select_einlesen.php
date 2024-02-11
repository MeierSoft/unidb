<?php
include('Sitzung.php');
session_start();
header("X-XSS-Protection: 1");
//include('funktionen.inc.php');
//Verbindung zur Datenbank herstellen
$db1 = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Datenbank']);
if($db1 == false) {
    $_SESSION['DB_Server'] = "localhost";
    $db1 = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);
}
mysqli_query($db1, 'set character set utf8;');
$stmt = mysqli_prepare($db1,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Ergebnis = "";
$finfo = mysqli_fetch_fields($result);
$Spalten = count($finfo);
while ($row = mysqli_fetch_row($result)) {
	if($Spalten > 1) {
		$Ergebnis = $Ergebnis."<option value=\"".$row[0]."\">".$row[1]."</option>";
	} else {
		$Ergebnis = $Ergebnis."<option>".$row[0]."</option>";
	}
}
echo $Ergebnis;
mysqli_stmt_close($stmt);
mysqli_close($db1);
?>
