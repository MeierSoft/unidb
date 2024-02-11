<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
//Verbindung zur Datenbank herstellen
$_SESSION['DB_Server'] = "localhost";
$db1 = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$Datenbank);
mysqli_query($db1, 'set character set utf8;');

$query = "SHOW COLUMNS FROM ".$Tabelle.";"; 
$stmt = mysqli_prepare($db1,$query);
//mysqli_stmt_bind_param($stmt, "s", $Tabelle);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line["Field"] = "*";
$Ergebnis = json_encode($line);
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$Ergebnis = $Ergebnis."@@@".json_encode($line);
}
echo $Ergebnis;
mysqli_stmt_close($stmt);
mysqli_close($db1);
?>
