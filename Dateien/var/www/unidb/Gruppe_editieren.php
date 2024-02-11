<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<style type="text/css">
#Schrift {
  font-family: Helvetica,Arial,sans-serif;
}
</style>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Gruppe_editieren.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[15];
	exit;
}
include './mobil.php';
if($modus >"") {
	if (count($_SESSION['Tagliste']) == 0){
   	$_SESSION['Tagliste'] = Null;
   	$Tagliste = Null;
	} else {
		$Tagliste = $_SESSION['Tagliste'];
	}
} else {
	$_SESSION['Tagliste'] = NULL;
}
include 'conf_DH.php';

//Modus=entfernen
if ($modus=="entfernen"){
	$Tagliste[$Zeile]=0;
	$x=0;
	for($i=0;$i <= count($Tagliste); $i++) {
		if($Tagliste[$i]!=0) {
			$Tagliste_neu[$x]=$Tagliste[$i];
			$x++;
		}
	}
	unset($Tagliste);
	$Tagliste = $Tagliste_neu;
	$_SESSION['Tagliste'] = $Tagliste;
	//$modus="speichern";
}

//Modus=rauf
if ($modus=="rauf"){
	if ($Zeile > 0){
		$Tag1=$Tagliste[$Zeile];
		$Tag2=$Tagliste[$Zeile-1];
		$Tagliste[$Zeile]=$Tag2;
		$Tagliste[$Zeile-1]=$Tag1;
		$_SESSION['Tagliste'] = $Tagliste;
		//$modus="speichern";
	}
}
//Modus=runter
if ($modus=="runter"){
	if ($Zeile < count($Tagliste)-1){
		$Tag1=$Tagliste[$Zeile];
		$Tag2=$Tagliste[$Zeile+1];
		$Tagliste[$Zeile]=$Tag2;
		$Tagliste[$Zeile+1]=$Tag1;
		$_SESSION['Tagliste'] = $Tagliste;
		//$modus="speichern";
	}
}
//Modus=anfuegen
if ($modus=="anfuegen"){
	$Pfad = "%";
	if(substr($Tag,0,1) == "/") {
		$pos = strrpos($Tag, "/");
		$Pfad = substr($Tag, 0, $pos + 1);
		$Tag = substr($Tag, $pos + 1);
	}
	$query = "SELECT Tag_ID FROM Tags WHERE Path=? AND Tagname=?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tag);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	array_push($Tagliste, $line["Tag_ID"]);
	$_SESSION['Tagliste'] = $Tagliste;
	//$modus="speichern";
	mysqli_stmt_close($stmt);
}

//Modus=speichern
if ($modus=="speichern"){
	for($i=0;$i <= count($Tagliste); $i++) {
		$Tags = $Tags.$Tagliste[$i].",";
	}
	$Tags = str_replace(",,", ",", $Tags);
	while(substr($Tags, strlen($Tags)-1) == ",") {
		$Tags = substr($Tags, 0, strlen($Tags)-1);
	}
	while(substr($Tags, 0, 1) == ",") {	
		$Tags=substr($Tags, 1, strlen($Tags));
	}
	$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sii",[$Tags, $Baum_ID, $Server_ID]);
	$query = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "sii",[$Tags_Pfad, $Baum_ID, $Server_ID]);
}
//Daten lesen und aufbereiten
$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Tags' as CHAR(1024)) as `Tags`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR(1024)) as `Tags_Pfad` FROM Baum WHERE Baum_ID= ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line_Gruppe = mysqli_fetch_array($result, MYSQLI_ASSOC);
if($_SESSION['Tagliste'] != null) {
	$Tagliste = $_SESSION['Tagliste'];
} else {
	$Tagliste = explode(",", $line_Gruppe["Tags"]);
	$_SESSION['Tagliste'] = $Tagliste;
}

for($i=0;$i <= count($Tagliste); $i++) {
	$Tags = $Tags.$Tagliste[$i].",";
}
$Tags = str_replace(",,", ",", $Tags);
while(substr($Tags, strlen($Tags)-1) == ",") {
	$Tags = substr($Tags, 0, strlen($Tags)-1);
}
while(substr($Tags, 0, 1) == ",") {	
	$Tags=substr($Tags, 1, strlen($Tags));
}

$Bezeichnung = html_entity_decode($line_Gruppe["Bezeichnung"]);
mysqli_stmt_close($stmt);
echo "<form action='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='Baum' name='Form_umbenennen'>";
echo "<input id='inhalt_tagsuche_dialog' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<input type='hidden' id='Tagliste1' name='Tagliste' value = '".$Tags."'>\n";
echo "<table><tr><td align='right'>".$Text[1]."</td><td colspan='3'><input class='Text_Element' value='".$Bezeichnung."' type='text' name='Bezeichnung' size='60'></td></tr>";
echo "<tr><td>".$Text[2]."</td><td colspan='3'><input class='Text_Element' value='".$line_Gruppe["Tags_Pfad"]."' type='text' name='Tags_Pfad' size='60'></td></tr>\n";
echo "<tr style='line-height: 3';><td></td><td>";
if ($mobil==1){
	echo "<a href='Baum2.php' target='_self'>".$Text[3]."</a>";
} else {
	echo "<a href='Gruppe.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a>";
}
echo "</td><td><input class='Schalter_Element' value='".$Text[4]."' type='submit' name='Aktion'></td><td><input class='Schalter_Element' id=\"Hilfe\" value='".$Text[5]."' type='button' name='Hilfe' onclick=\"Hilfe_Fenster('10');\"></td></tr></table><br>\n";
echo "</form>";
echo "<form action='Gruppe_editieren.php?modus=anfuegen&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='_self' id='editieren' name='editieren'>";
echo "<input id='inhalt_tagsuche_dialog1' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>\n";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<table id='Schrift'; style='font-size: 14px';>";
echo "<tr style='font-weight:bold'><td>".$Text[6]."</td><td>".$Text[7]."</td></tr>";
echo "<span id='Schrift'; style='font-weight:normal; font-size: 14px'>";
$x=0;
for($i=0;$i < count($Tagliste); $i++) {
	if(strlen($Tagliste[$i]) > 0) {
		$query_Value = "SELECT Value, Timestamp FROM akt WHERE Point_ID = ? ORDER BY Timestamp DESC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query_Value);
		mysqli_stmt_bind_param($stmt, "i", $Tagliste[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$query = "SELECT Tagname, Dezimalstellen, Description, EUDESC FROM Tags WHERE Tag_ID = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Tagliste[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Wert=round($line_Value['Value'],$line_Tag["Dezimalstellen"]);
		$Zeitpunkt = $line_Value['Timestamp'];
		$Tagname = html_entity_decode($line_Tag["Tagname"]);
		$Description = html_entity_decode($line_Tag["Description"]);
		echo "<tr bgcolor='#E5E5E5'><td>".$Tagname."</td><td>".$Description."</td><td width=50px><a href='Gruppe_editieren.php?Zeile=".$x."&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&modus=rauf'>".$Text[8]."</a></td><td width=60px><a href='Gruppe_editieren.php?Zeile=".$x."&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&modus=runter'>".$Text[9]."</a></td><td width=80px><a href='Gruppe_editieren.php?Zeile=".$x."&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&modus=entfernen'>".$Text[10]."</a></td></tr>";
		$x++;
	}
}
echo "</tbody></table><br>";
echo "<span id='Schrift'; style='font-weight:normal; font-size: 14px'>";
echo "<table><tr><td>".$Text[11]."</td><td colspan='4'><input class='Text_Element' id='Tag' name='Tag' type='text' size='60' maxlength='255'></td></tr>";
echo "<tr style='line-height: 3';><td>";

echo "</td><td><input class='Schalter_Element' value='".$Text[12]."' type='submit' name='Aktion'></td><td align='center'><input class='Schalter_Element' type='button' name='Tag_suchen_Schalter' value='".$Text[13]."' onclick='Eingabefeld_sichtbar(1)'></td>\n";
echo "</form><form action='Gruppe_editieren.php?modus=speichern&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='_self' id='tags_tauschen' name='Tags_tauschen'>\n";
echo "<input id='inhalt_tagsuche_dialog2' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<td align='right'><input class='Schalter_Element' name='Pfade_editieren' value='".$Text[14]."' type='button' onclick='Pfade_Dialog_oeffnen();'></td></tr></table>\n";
echo "</form></span>";
mysqli_close($db);
mysqli_close($dbDH);
?>

<script type="text/javascript">
jsPanel.defaults.resizeit.minWidth = 30;
jsPanel.defaults.resizeit.minHeight = 52;
var T_Text = new Array;

let tagsuche_geschlossen = function(event) {
	document.getElementById("inhalt_tagsuche_dialog").value = "";
	document.getElementById("inhalt_tagsuche_dialog1").value = "";
	document.getElementById("inhalt_tagsuche_dialog2").value = "";
}
document.addEventListener('jspanelclosed', tagsuche_geschlossen, false);

$(window).on('load',function() {;
	T_Text = JSON.parse(document.getElementById("translation").value);
	if (document.getElementById("inhalt_tagsuche_dialog").value + document.getElementById("inhalt_tagsuche_dialog1").value + document.getElementById("inhalt_tagsuche_dialog2").value > "") {Eingabefeld_sichtbar(0);}
		
});

function uebertragen() {
	var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
	document.editieren.Tag.value = Ergebnis[0] + Ergebnis[1];
	jsPanel.activePanels.getPanel("Dialog").close();
}


function Eingabefeld_sichtbar(neu) {
	if (neu == 0) {
		if (document.getElementById("inhalt_tagsuche_dialog").value > "") {
			var strReturn = document.getElementById("inhalt_tagsuche_dialog").value;
		} else {
			if (document.getElementById("inhalt_tagsuche_dialog1").value > "") {
				var strReturn = document.getElementById("inhalt_tagsuche_dialog1").value;
			} else {
				if (document.getElementById("inhalt_tagsuche_dialog2").value > "") {
					var strReturn = document.getElementById("inhalt_tagsuche_dialog2").value;
				}
			}
		}
	} else {
		var strReturn = "";
		jQuery.ajax({
			url: "Test_Tag_suchen.php?Suchtext=" + document.editieren.Tag.value,
			success: function (html) {
   			strReturn = html;
			},
			async: false
		});
	}
	var panel = jsPanel.create({
		id: 'Tagsuche',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '500 275',
		content: strReturn,
  		contentOverflow: 'hidden',
  		callback: function (panel) {
  			$(this).on('mouseleave', function () {
				var Pos = jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).content[0].children[0].Ergebnis.value.search(" - ");
				var Tagname = jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).content[0].children[0].Ergebnis.value.substring(0, Pos);  
				document.editieren.Tag.value=Tagname;
				//panel.close();
			});
	  	}
	});
	document.getElementById("inhalt_tagsuche_dialog").value = document.getElementById("Tagsuche").content.innerHTML;
	document.getElementById("inhalt_tagsuche_dialog1").value = document.getElementById("Tagsuche").content.innerHTML;
	document.getElementById("inhalt_tagsuche_dialog2").value = document.getElementById("Tagsuche").content.innerHTML;
}

function Pfade_Dialog_oeffnen(){
	jQuery.ajax({
		url: "DH_Tagliste_ermitteln.php?Tagliste=" + document.Form_umbenennen.Tagliste1.value + "&Pfad=" + document.Form_umbenennen.Tags_Pfad.value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'PfadeDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '600 275',
		content: strReturn,
	});
}

function Pfadaenderung_uebernehmen() {
	i = 0;
	Feld = document.Dialog_Tagaustausch[0];
	try{	
		while (Feld.type != "button"){
			if (Feld.checked == true) {
				Tag_alt_neu = Feld.value.split(",");
				document.Form_umbenennen.Tagliste1.value = document.Form_umbenennen.Tagliste1.value.replace(Tag_alt_neu[0], Tag_alt_neu[1]);
			}
			i = i + 1;
			Feld = document.Dialog_Tagaustausch[i];
		}
	} catch (err) {}
	try {PfadeDialog.close();} catch (err) {}
}

</script>
</body>
</html>