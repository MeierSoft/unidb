<?php
$sqlhostname = "localhost";
$login = "DH_write";
$password = "";
$base = "DH";
$Rechner = "localhost";
$dbDH = mysqli_connect($sqlhostname,$login,$password,$base);
mysqli_query($dbDH, 'set character set utf8;');
//mysqli_query($db,'set character set latin1;');
?>
