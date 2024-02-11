<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Text = Translate("Bild_neu.php");
echo "<title>".$Text[3]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";


include './mobil.php';
$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));

echo "<form action='Baum2.php' method='post' target='Baum'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
echo "<input value='".$Text[4]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[1]."' type='reset' name='abbrechen'>";
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
echo "<input value='".$Bezeichnung."' type='hidden' name='Bezeichnung'>";
echo "<input value='".$original."' type='hidden' name='original'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "</form>";
mysqli_close($db);
?>
</body>
</html>
