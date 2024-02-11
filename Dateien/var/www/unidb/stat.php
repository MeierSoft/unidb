<?php
$sqlhostname = "localhost";
$login = "root";
$password = "Heinrich";
$base = "mysql";
$db = mysqli_connect($sqlhostname,$login,$password,$base) or die( 'Verbindungsfehler!' );
$stmt = mysqli_prepare($db, 'SHOW STATUS;');
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	echo $line["Variable_name"].":".$line["Value"].";";
}
mysqli_stmt_close($stmt);
mysqli_close($db);
?>