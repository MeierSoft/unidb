<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
$query="SELECT Vorlage_ID, column_list(`Eigenschaften`) FROM `Vorlagen` WHERE `DE` = ? AND `Typ` = 'Element';";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "s", $Bezeichnung);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Text=$line["column_list(`Eigenschaften`)"];
$Felder=explode("`,`", $Text);

for($x=0; $x < count($Felder); $x++) {
	$Feld[$x]=str_replace("`", "", $Felder[$x]);
}
$query="SELECT ";
for($x=0; $x < count($Felder); $x++) {
	$query=$query."column_get(`Eigenschaften`, '".$Feld[$x]."' as CHAR(1000)) as `".$Feld[$x]."`, ";
}
$query=$query."FROM `Vorlagen` where `Vorlage_ID` = ".$line["Vorlage_ID"].";";
$query=str_replace(", FROM", " FROM", $query);
$req = mysqli_query($db,$query);
$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
$Ausgabe=$line;
echo json_encode($Ausgabe);
mysqli_stmt_close($stmt);
mysqli_close($db);
?>
