<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("kopieren.php");
$anzeigen = Berechtigung($original, $Server_ID);
if($anzeigen == 0) {
	echo $Text[4];
	exit;
}
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo '<font color="#0000ff" size="3" face="Arial">'.$Text[0].'</font><br><br><font size="3" face="Arial">';
echo '<form action="neu2.php" method="post" target="_self">';
$query = "SELECT `Vorlage` FROM Baum WHERE Baum_ID=? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "ii", $original, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);	
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

echo "<input type='hidden' name='original' value = '".$original."'>";
echo "<input type='hidden' name='Vorlage_ID' value = ".$line["Vorlage"].">";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo $Text[1].":<br>";
echo "<input type='text' name='Bezeichnung' size='50' value = '".$Bezeichnung."'><br><br>";
echo "<input type='submit' name='Aktion' value ='".$Text[2]."'>";
echo "<input id='Hilfe' value='".$Text[3]."' type='button' name='Hilfe' onclick='Hilfe_Fenster(\"22\");'>";
?>
</form>
</font>
</body>
</html>