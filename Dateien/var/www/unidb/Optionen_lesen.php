<?php
session_start();
include('./Sitzung.php');
$query = "SELECT * FROM `Vorlageneigenschaften_Optionen` WHERE `Vorlageneigenschaften_ID` = ? ORDER BY `Vorlageneigenschaften_Optionen_ID` ASC;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "i", $Vorlageneigenschaften_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$Antwort = "";
while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$Antwort = $Antwort."<option value='".$line["Optionswert_EN"]."'>".$line["Optionswert_".$_SESSION["Sprache"]]."</option>";
}
echo $Antwort;
mysqli_stmt_close($stmt);
mysqli_close($db);
?>