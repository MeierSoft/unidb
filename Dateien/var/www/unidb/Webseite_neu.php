<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
include( 'Sitzung.php' );
header("X-XSS-Protection: 1");
$Text = Translate("Webseite_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<form action='Baum2.php' method='post' target='Baum'>";
echo "<input value='".$Text[1]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[2]."' type='reset' name='abbrechen'>";
echo "<input value='".$Bezeichnung."' type='hidden' name='Bezeichnung'>";
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
echo "<input value='".$original."' type='hidden' name='original'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "</form>";

// schliessen der Verbindung
mysqli_close($db);
?>

</body>
</html>
