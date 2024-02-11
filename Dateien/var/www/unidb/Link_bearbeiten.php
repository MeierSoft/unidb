<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Link_bearbeiten.php");
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[3];
	exit;
}
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
//Daten einlesen
$abfrage = "SELECT Bezeichnung FROM Baum WHERE Baum_ID = ? AND `Server_ID` = ?";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
//Form bauen
echo "<form action='Link_neu.php' method='post' target='_self'>";
echo $Text[1].":<br>";
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
echo "<input name='Bezeichnung' value ='".$Bezeichnung."' type='text' size='50' maxlength='50'><br><br>";   
echo "<input value='".$Text[2]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Baum_ID."' type='hidden' name='Baum_ID'>";
echo "<input name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "</form>";
// schliessen der Verbindung
mysqli_close($db);
?>
</body>
</html>
