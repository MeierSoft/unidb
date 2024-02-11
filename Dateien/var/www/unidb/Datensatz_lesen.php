<?php
session_start();
include('Sitzung.php');
header("X-XSS-Protection: 1");
//Verbindung zur Datenbank herstellen
$db = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);
if($db == false) {
    $_SESSION['DB_Server'] = "localhost";
    $db = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);
}
mysqli_query($db, 'set character set utf8;');
$Antwort = array();
$Ergebnis = array();
$stmt = mysqli_prepare($db,$SQL);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result);
$finfo = mysqli_fetch_fields($result);
foreach ($finfo as $val) {
	$Ergebnis["Feld"] = $val->name;
	$Ergebnis["Wert"] = $line[$val->name];
	$Antwort[] = $Ergebnis;
}
echo(json_encode($Antwort));
mysqli_stmt_close($stmt);
mysqli_close($db);
?>