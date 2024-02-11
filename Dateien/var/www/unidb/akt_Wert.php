<?php
foreach ($_REQUEST as $key=>$val){${$key}=$val;}
include('Sitzung.php');
header("X-XSS-Protection: 1");
require_once 'conf_DH.php';
$Pfad = "%";
if(substr($Tagname,0,1) == "/") {
	$pos = strrpos($Tagname, "/");
	$Pfad = substr($Tagname, 0, $pos);
	$Tagname = substr($Tagname, $pos + 1);
}
if(substr($Pfad,-1,1) != "/") {$Pfad = $Pfad."/";}
	
$abfrage = "SELECT `Tag_ID`, `EUDESC`, `Dezimalstellen` FROM `Tags` WHERE `Path` = ? AND `Tagname` = ?;";
$stmt = mysqli_prepare($dbDH, $abfrage);
mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);	
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
$Einheit = $line["EUDESC"];
$Dezimalstellen = intval($line["Dezimalstellen"]);
$Point_ID = Point_ID_finden($line["Tag_ID"], $dbDH);
mysqli_stmt_close($stmt);
$query = "SELECT `Value`, `Timestamp` FROM `akt` WHERE `Point_ID` =".$Point_ID." ORDER BY `Timestamp` DESC LIMIT 1;";
$req = mysqli_query($dbDH,$query);
$line_Value = mysqli_fetch_array($req, MYSQLI_ASSOC);
$Wert=round($line_Value['Value'],$Dezimalstellen);
$Zeitpunkt = $line_Value['Timestamp'];
//$Zeitpunkt = substr($Zeitpunkt, 11, 8);

echo $Wert.",".$Zeitpunkt.",".$Einheit;
mysqli_close($dbDH);
?>
