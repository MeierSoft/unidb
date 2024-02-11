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
$Text = Translate("neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo '<font color="#0000ff" size="3" face="Arial">'.$Text[0].'</font><br><br><font size="3" face="Arial">';
$query="SELECT `Vorlage_ID`, `".$_SESSION['Sprache']."` AS `Bezeichnung`, column_get(`Eigenschaften`, 'Datei' as CHAR) as `Datei` FROM `Vorlagen` where `Typ` ='Dokument';";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
echo "<form action='neu2.php' method='post' target='_self'>";
echo $Text[1].":<br><select name='Vorlage_ID' size='1'>";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	echo "<option value='".$line["Vorlage_ID"]."'>".strip_tags($line['Bezeichnung'])."</option>";
}
mysqli_stmt_close($stmt);
mysqli_close($db);
echo "</select><br><br>".$Text[2].":<br>";
echo "<input type='text' name='Bezeichnung' size='50'><br><br>";
echo "<input type='submit' name='Aktion' value ='".$Text[3]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input id=\"Hilfe\" value='".$Text[4]."' type='button' name='Hilfe' onclick='Hilfe_Fenster(\"11\");'>";
?>
</form>
</font>
</body>
</html>