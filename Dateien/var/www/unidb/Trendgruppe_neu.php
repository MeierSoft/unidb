<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Trendgruppe_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<form action='Baum2.php' method='post' target='Hauptrahmen'>";
echo "<input value='".$Text[1]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[2]."' type='reset' name='abbrechen'>";
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
echo "<input value='".$original."' type='hidden' name='original'>";
echo "<input value='".$Bezeichnung."' type='hidden' name='Bezeichnung'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
mysqli_close($db);
?>
</form>
</body>
</html>