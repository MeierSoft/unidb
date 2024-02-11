<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script type="text/javascript" src="./scripts/suneditor.min.js"></script>
<script type="text/javascript" src="./scripts/de.js"></script>
<link href="./css/suneditor.min.css" rel="stylesheet">
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include 'mobil.php';
$Text = Translate("Notiz_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<form id='formular' action='Baum2.php' method='post' target='Hauptrahmen'>";
echo "<textarea name='Notiz' style='display:none;' id='editor'>";
echo "</textarea><br>";
echo '<script src="./scripts/editor.js"></script>';
echo "<input value='".$Text[1]."' type='submit' name='Aktion' onclick='speichern();'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[2]."' type='reset' name='abbrechen'>";
echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
echo "<input value='".$original."' type='hidden' name='original'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
mysqli_close($db);
?>
</form>
<script type="text/javascript" >
	function speichern() {
		document.getElementById("editor").innerHTML = editor.getContents();
		document.forms.formular.submit();
	}
</script>
</body>
</html>
