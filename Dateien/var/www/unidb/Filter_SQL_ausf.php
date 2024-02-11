<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$db_Satz = mysqli_connect("localhost","root","Heinrich","unidb") or die( 'Verbindungsfehler!' );
$SQL = str_replace('\"', '"', $SQL);
$stmt = mysqli_prepare($db_Satz,$SQL);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
?>