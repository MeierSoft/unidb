<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
require_once 'conf_DH.php';
$query = "SELECT * FROM `User_Skalen` WHERE `User_ID` = ? AND `Point_ID` = ?;";
$stmt = mysqli_prepare($dbDH,$query);
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['User_ID'], $Point_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Saetze = mysqli_num_rows($result);
mysqli_stmt_close($stmt);
mysqli_close($db);
if($Saetze > 0) {
	$query = "Update `User_Skalen` SET `min` = ".$min.", `max` = ".$max." WHERE `User_ID` = ".$_SESSION['User_ID']." AND `Point_ID` = ".$Point_ID.";";
} else {
	$query = "INSERT INTO `User_Skalen` (`User_ID`, `Point_ID`, `min`, `max`) VALUES (".$_SESSION['User_ID'].", ".$Point_ID.", ".$min.", ".$max.");";
}
Kol_schreiben($query);
?>