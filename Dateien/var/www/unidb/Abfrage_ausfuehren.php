<?php
//include('Sitzung.php');
session_start();
header("X-XSS-Protection: 1");
//Get und POST einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
foreach($_POST as $key => $value){
	${$key}=$value;
}
//include('funktionen.inc.php');
//Verbindung zur Datenbank herstellen
$db1 = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$Datenbank);
mysqli_query($db1, 'set character set utf8;');
$stmt = mysqli_prepare($db1,$SQL_Text);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Ergebnis = "<tr class='Tabelle_Ueberschrift'>";
$finfo = mysqli_fetch_fields($result);
foreach ($finfo as $val) {
	$Ergebnis = $Ergebnis."<td>".$val->name."</td>";
}
$Ergebnis = $Ergebnis."</tr>";	
while ($row = mysqli_fetch_row($result)) {
	$i = 0;
	$Ergebnis = $Ergebnis."<tr>";
	while ($i < count($row)) {
		$Ergebnis = $Ergebnis."<td>".$row[$i]."</td>";
		$i = $i + 1;
	}
	$Ergebnis = $Ergebnis."</tr>";
}
echo $Ergebnis;
mysqli_stmt_close($stmt);
mysqli_close($db1);
?>