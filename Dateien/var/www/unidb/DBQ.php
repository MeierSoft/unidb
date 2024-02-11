<?php
include('Sitzung.php');
session_start();
header("X-XSS-Protection: 1");
//Verbindung zur Datenbank herstellen
if($_SESSION['Datenbank'] == null) {$_SESSION['Datenbank'] = $_SESSION['DB_DB'];}
if($DB == "") {$DB = $_SESSION['Datenbank'];}
$db1 = mysqli_connect("localhost",$_SESSION['DB_User'],$_SESSION['DB_pwd'],$DB);

mysqli_query($db1, 'set character set utf8;');
if($Fun == "") {
	$query = "SELECT `".$Fel."` FROM `".$Tab."`";
} else {
	$query = "SELECT ".$Fun."(`".$Fel."`) AS `".$Fel."` FROM `".$Tab."`";
}
if($Bed == "") {
	$query = $query.";";
} else {
	 $query = $query." WHERE ".$Bed.";";
}
$stmt = mysqli_prepare($db1,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
echo $line[$Fel];
mysqli_stmt_close($stmt);
mysqli_close($db1);
?>
