<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript">
var T_Text = new Array;
$(window).on('load',function() {;
	T_Text = JSON.parse(document.getElementById("translation").value);
});
</script>

<?php
include 'Sitzung.php';
include 'conf_DH_schreiben.php';
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("hochladen.php");
echo "<title>".$Text[0]."</title>";
echo "</head>";
echo "<body class='allgemein'>";
echo "<font face='Arial'>";
echo "<h3>".$Text[0]."</h3><br>";
if($Bestimmung != "") {
	if($Bestimmung == "Multistate") {
		$max_size = 307200; //300 KByte
		$Ordner = "./Multistates/";
	}
	if($Bestimmung == "Hintergrundbild") {
		$max_size = 1048576; //1 MByte
		$Ordner = "./Bilder/";
	}
	//Datei hochladen
	$filename = pathinfo($_FILES['Datei']['name'], PATHINFO_FILENAME);
	$extension = strtolower(pathinfo($_FILES['Datei']['name'], PATHINFO_EXTENSION));
	//Überprüfung der Dateiendung
	if($filename !=null) {
		$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
		if(!in_array($extension, $allowed_extensions)) {
 			die($Text[1]);
		}
		//Überprüfung der Dateigröße
		if($_FILES['Datei']['size'] > $max_size) {
 			die($Text[2]);
		}
 		//Überprüfung dass das Bild keine Fehler enthält
		if(function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
 			$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
 			$detected_type = exif_imagetype($_FILES['Datei']['tmp_name']);
 			if(!in_array($detected_type, $allowed_types)) {
 				die($Text[3]);
 			}
		}
		//Pfad zum Upload
		$new_path = $Ordner.$filename.'.'.$extension;
		//Neuer Dateiname falls die Datei bereits existiert
		if(file_exists($new_path)) { //Falls Datei existiert, hänge eine Zahl an den Dateinamen
 			$id = 1;
 			do {
 				$new_path = $Ordner.$filename.'_'.$id.'.'.$extension;
 				$id++;
 			} while(file_exists($new_path));
		}
		//Alles okay, verschiebe Datei an neuen Pfad
		move_uploaded_file($_FILES['Datei']['tmp_name'], $new_path);
		echo $Text[4].': <a href="'.$new_path.'">'.$new_path.'</a><br><br>';
	}
}

echo "<form id='phpform' name='phpform' action='hochladen.php' method='post' target='_self' enctype='multipart/form-data'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
echo "<input type='hidden' id='Ordner' name='Ordner' value = '".$Ordner."'>";
echo '<table cellspacing="5px" cellpadding="5px" class="table">';
echo '<tr><td class="Text_einfach" align="right">'.$Text[5].'</td>';
echo "<td class='Tabelle_Zelle'>";
echo '	<select class="Auswahl_Liste_Element" id="bestimmung" name="Bestimmung" size="1">';
echo "		<option value = ''>".$Text[6]."</option>";
echo "		<option value = '".$Text[7]."'>".$Text[7]."</option>";
echo "		<option value = '".$Text[8]."'>".$Text[8]."</option>";
echo "	</select>";
echo '</td><td><a href="javascript:void(0);" onclick="Hilfe_Fenster(\'24\');">'.$Text[9].'</a></td></tr>';
echo '<tr><td class="Text_einfach" align="right">'.$Text[10].'</td><td class="Tabelle_Zelle"><input style="font-size: 11px;" type="file" name="Datei"></td></tr>';
echo '<tr><td></td><td class="Tabelle_Zelle"><input class="Schalter_Element" value="'.$Text[11].'" type="submit" id="datei_hochladen" name="Datei_hochladen"></td></tr>';
?>
</table>
</form>
</font>
</body>
</html>