<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./tinymce/tinymce.min.js"></script>
<script src="./tinymce/langs/de.js"></script>
<script type="text/javascript" src="./scripts/jquery.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include 'mobil.php';
$Text = Translate("Notiz_bearbeiten.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[5];
	exit;
}
//Daten einlesen
$abfrage = "SELECT `Server_ID`, Bezeichnung, column_get(Inhalt, 'html' as CHAR) as Notiz FROM `Baum` where Baum_ID = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
//Form bauen
if ($mobil==1){
	echo "<form id='formular' action='Baum2.php' method='post' target='_self'>";
}else{
	echo "<form id='formular' action='Baum2.php' method='post' target='Baum'>";
}
$Notiz = html_entity_decode($line["Notiz"]);
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);

echo '<div class="page-wrapper box-content">';
echo "<textarea class='content' name='Notiz' id='editor'>".$Notiz."</textarea>";
?>
</div>
<script>
	$(document).ready(function() {
		$('.content').richText();
	});
	tinymce.init({
		selector: 'textarea#editor',
		language: 'de',
		plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link codesample table charmap nonbreaking anchor insertdatetime advlist lists help charmap quickbars',
		menubar: 'edit view insert format tools table help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap | fullscreen  preview print | insertfile image link anchor codesample',
		toolbar_sticky: true,
		quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		toolbar_mode: 'sliding',
		contextmenu: 'link image table'
	});
</script>
<?php
echo $Text[1].": <input value='".$Bezeichnung."' type='text' name='Bezeichnung'>&nbsp&nbsp&nbsp";
echo "<input value='".$Text[2]."' type='submit' name='Aktion'>&nbsp&nbsp&nbsp";
echo "<input value='".$Baum_ID."' type='hidden' name='Baum_ID'>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
if ($mobil==1){
	echo "<a href='Baum2.php' target='_self'>".$Text[3]."</a>";
} else {
	echo "<a href='Notiz.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a>";
}
echo "</form>";
// schliessen der Verbindung
mysqli_close($db);
?>
</body>
</html>