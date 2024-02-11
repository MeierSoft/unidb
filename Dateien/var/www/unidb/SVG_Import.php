<?php
include 'Sitzung.php';
header("X-XSS-Protection: 1");
$Text = Translate("hochladen.php");
$Datei = $_FILES['Datei']['tmp_name'];
if(strlen($Datei) > 0) {
	echo '<script>SVG_einlesen("'.$Datei.'");</script>';
} else {
	echo "<form id='phpform' name='phpform' action='SVG_Import.php' method='post' target='_self' enctype='multipart/form-data'>";
	echo $Text[10].'&nbsp;<input style="font-size: 11px;" type="file" name="Datei"><br><br>';
	echo '<input value="'.$Text[11].'" type="submit" name="Datei_hochladen">';
	echo "</form>";
}
?>
