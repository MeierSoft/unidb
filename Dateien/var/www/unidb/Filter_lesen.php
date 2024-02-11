<?php
session_start();
include('./Sitzung.php');
$query = "SELECT * FROM `Userdef_Filter` WHERE `Baum_ID` = ? AND `User_ID` = ? AND `Server_ID` = ? ORDER BY `Filtername` ASC;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "iii", $Baum_ID, $_SESSION["User_ID"], $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = array();
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$Antwort[] = $line;
}
echo json_encode($Antwort);
mysqli_stmt_close($stmt);
mysqli_close($db);
?>