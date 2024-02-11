<?php
session_start();
include('./Sitzung.php');
if($user == null) {$db = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['DB_DB']);}
if($user == "Bericht") {$db = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);}
if ($db == null) {
	$db = mysqli_connect("localhost",$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);
}
mysqli_query($db, 'set character set utf8;');
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = array();
$finfo = mysqli_fetch_fields($result);
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	foreach ($finfo as $val) {
		$line[$val->name] = html_entity_decode($line[$val->name]);
	}
	$Antwort[] = $line;
}
echo(json_encode($Antwort));
mysqli_stmt_close($stmt);
mysqli_close($db);
?>