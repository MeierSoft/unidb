<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 0.5, maximum-scale=5.0" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script src="./scripts/plotly-latest.min.js"></script>
<script src="./scripts/plotly-locale-de-latest.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
require_once 'conf_DH_schreiben.php';
include 'mobil.php';
include('Trend_funktionen.php');
$Text = Translate("Trend.php");

$Anfangszeit = hrtime(true);
$akt = 0;
if ($Spanne<1){$Spanne=86400;}
if($Hoehe == null) {$Hoehe = 700;}
if ($Hoehe < 1){$Hoehe=700;}
if($Breite == null) {$Breite = 1600;}
if ($Breite < 1){$Breite=1600;}

if($Zeitpunkt > "") {
	$Ende = $Zeitpunkt;
} else {
	if($Zeitzahl > "") {
		$Ende = strftime('%Y-%m-%d %H:%M:%S',($Zeitzahl));
	}
}
if ($Ende < 1){
	$Ende = strftime('%Y-%m-%d %H:%M:%S',time());
	$akt = 1;
}
if(time() - $Zeitzahl < 61) {
	$akt = 1;
}
if($Tag_ID < 1) {
	$stmt = mysqli_prepare($dbDH, "SELECT `Tag_ID`, `Description`, `step` FROM `Tags` Where `Path` = ? AND `Tagname` = ?;");
	mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tag);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	if ($row[0]>0){
		$Tag_ID = $row[0];
		$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
		$Bezeichnung = html_entity_decode($row[1]);
		$step = $row[2];
	}
}
if ($step == ""){
	$stmt = mysqli_prepare($dbDH, "SELECT `step` FROM `Tags` Where `Tag_ID` = ?;");
	mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	if ($row[0]>''){
		$step= $row[0];
	}
}
if ($Tag == ''){
	$stmt = mysqli_prepare($dbDH, "SELECT `Path`, `Tagname`, `step` FROM `Tags` Where `Tag_ID` = ?;");
	mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	if ($row[0]>''){
		$Tag= $row[1];
		$Pfad= $row[0];
		$step= $row[2];
	}
}

$stmt = mysqli_prepare($dbDH, "SELECT `Description`, `EUDESC`, `Dezimalstellen`, `first_value`, `Tagname` FROM `Tags` Where `Tag_ID` = ?;");
mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_row($result);
mysqli_stmt_close($stmt);
$Tagname = html_entity_decode($row[4]);
$Bezeichnung = html_entity_decode($row[0]);
$EUDESC=$row[1];
$Dezimalstellen=$row[2];
$first_value=$row[3];
//Wenn die Seite erstmalig aufgerufen wird und noch keine weiteren Tags angeklickt wurden, dann muessen hier die Variablen entsprechend vorbelegt werden
if ($Anzahl_Tags == NULL) {
	${"Tag".$Tag_ID} = 1;
	$Anzahl_Tags = 1;
	$neuer_Trend = 1;
}

if ($Spanne<1){$Spanne=1;}
If ($Spanne=='1 h'){
	$Spanne = 3600;
}
If ($Spanne=='4 h'){
	$Spanne = 14400;
}
If ($Spanne=='8 h'){
	$Spanne = 28800;
}
If ($Spanne == $Text[1]){
	$Spanne = 86400;
}
If ($Spanne == $Text[2]){
	$Spanne = 172800;
}
If ($Spanne == $Text[3]){
	$Spanne = 604800;
}
If ($Spanne == $Text[4]){
	$Spanne = 1209600;
}
If ($Spanne == $Text[5]){
	$Spanne = 2592000;
}
If ($Spanne == $Text[6]){
	$Spanne = 31536000;
}
If ($schieben == '<'){
	$Ende = strftime('%Y-%m-%d %H:%M:%S',(strtotime($Ende) - $Spanne));
	$akt = 0;	
}
If ($schieben == '>'){
	$Ende = strftime('%Y-%m-%d %H:%M:%S',(strtotime($Ende) + $Spanne));	
	$akt = 0;
}
If ($schieben == $Text[7]){
	$Ende = strftime('%Y-%m-%d %H:%M:%S',time());
	$akt = 1;
}
if ($Zeit_merken>""){
	$Ende = strftime('%Y-%m-%d %H:%M:%S',(strtotime($Zeit_merken)));
}
$Start = strftime('%Y-%m-%d %H:%M:%S',(strtotime($Ende) - $Spanne));

if($first_value > $Start) {
	$Start = $first_value;
}
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
echo "<title>".$Tagname." - ".$Bezeichnung."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<form name = 'Einstellungen' action='./Trend.php' method='post' target='_self'>";
echo "<input name='mobil' id='mobil' value='".$mobil."' type='hidden'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";

if($mobil == 1) {
	include ("./Trend_navi_mobil.php");
} else {
	include ("./Trend_navi.php");
}

$query = "select column_get(`Inhalt`, 'Tags' as CHAR(200)) as `Tags` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line_weitere_Tags = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Tagliste = explode(",", $line_weitere_Tags["Tags"]);
echo "<table><tr><td><strong>".$Bezeichnung." (</strong></td><td><font color='red'>".$Text[21].":</font></td><td><strong>".$letzter_Wert."</strong> ".$EUDESC." ".$Text[22]." ".$letzter_Wert_Zeit." ".$Text[23]."</td>";
echo "<td><font color='red'>".$Text[24].":</font></td>";
$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID = ? AND Timestamp > ? ORDER BY Timestamp ASC;";
$stmt = mysqli_prepare($dbDH, $query);
mysqli_stmt_bind_param($stmt, "is", $Point_ID, $row[1]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$Wert = round($line_Value['Value'],$Dezimalstellen);
	$Zeitpunkt = $line_Value['Timestamp'];
	$Zeitpunkt = substr($Zeitpunkt, 11, 8);
	echo "<td><strong>".$Wert."</strong> ".$EUDESC." ".$Text[22]." ".$Zeitpunkt."</td>";
}
mysqli_stmt_close($stmt);
echo "<td><strong>)</strong></tr>";
echo "<input value='".$Point_ID."' type='hidden' name='alte_Point_ID'>";
echo "<input value='".$Point_ID."' type='hidden' name='Point_ID'>";
echo "</table>";
echo "<input name='Tag' value= '".$Tag."' type='hidden'>";
echo "<input name='Tag_ID' value= '".$Tag_ID."' type='hidden'>";
echo "<input name='Baum_ID' value= '".$Baum_ID."' type='hidden'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";

echo '<div id="myPlot"></div>';

//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);
$xd = array();
$yd = array();
$Tagprop = array();
if($Art == "") {
	$Art = "Rohwerte";
}
$Art2 = $Art;
if ($Art == "Rohwerte" or $Art == "Ruwe waarden" or $Art == "raw values" or $Art == "Valeurs brutes"){
	$Art2 = "rV";
}
$i = 1;
$Anzahl_akt_Tags = 0;
$Wert = array();
if($Tagliste[0] == Null) {$Tagliste[0] = $Tag_ID;}
for ($x = 0;$x < count($Tagliste);$x++){
	if (${"Tag".$Tagliste[$x]} > 0){
		$Anzahl_akt_Tags++;
		$i = $i + 1;
		$yd[$i] = "[";
		$xd[$i] = "[";
		//Point_ID für den Tag finden
		$Point_ID = Point_ID_finden($Tagliste[$x], $dbDH);
		//Tagdetails aus der Tabelle Tags lesen
		$query = "SELECT `Tagname`, `Scale_max`, `Scale_min`, `first_value`, `EUDESC`, `Description`, `Dezimalstellen`, `step` FROM `Tags` Where `Tag_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Tagliste[$x]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Tags = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$first_value = $line_Tags["first_value"];
		if($first_value > $Start) {
			$Start2 = $first_value;
		}else {
			$Start2 = $Start;
		}
		echo "<input value='".$Start2."' type='hidden' name='Startzeitpunkt'>";
		$Tagprop[$i]['Tagname'] = html_entity_decode($line_Tags['Tagname']);
		$Tagprop[$i]['step'] = $line_Tags['step'];
		$Tagprop[$i]['Description'] = html_entity_decode($line_Tags['Description']);
		$Tagprop[$i]['Scale_max'] = $line_Tags['Scale_max'];
		$Tagprop[$i]['Scale_min'] = $line_Tags['Scale_min'];
		$Tagprop[$i]['first_value'] = $line_Tags['first_value'];
		$Tagprop[$i]['EUDESC'] = html_entity_decode($line_Tags['EUDESC']);
		$Tagprop[$i]['Point_ID'] = $Point_ID;
		$Tagprop[$i]['Dezimalstellen'] = $line_Tags['Dezimalstellen'];
		mysqli_stmt_close($stmt);
		//Gibt es benutzerspezifische Einstellungen fuer die Skala?
		$query = "SELECT * FROM `User_Skalen` WHERE `User_ID` = ? AND `Point_ID` = ?;";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_bind_param($stmt, "ii", $_SESSION['User_ID'], $Point_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(mysqli_num_rows($result) > 0){
			$Tagprop[$i]['Scale_max'] = $line['max'];
			$Tagprop[$i]['Scale_min'] = $line['min'];
		}
		mysqli_stmt_close($stmt);

		if($Tagprop[$i]['first_value'] > $Start) {
			$Start = $Tagprop[$i]['first_value'];
		}
		$Werte = lesen($Art2, $Point_ID, $Start, $Ende,0 ,0, 0, 0, 0);
		$a = $Werte[1];
		$b = $Werte[0];
		for ($z = 0;$z < count($a);$z++){
			$yd[$i] = $yd[$i].$a[$z].",";
		}
		for ($z = 0;$z < count($b);$z++){
			$xd[$i] = $xd[$i]."'".$b[$z]."',";
		}
		if(strlen($yd[$i]) > 1) {$gefunden = 1;}
		//Wenn nichts im Archiv gefunden wurde, dann ist der letzte Wert aus akt der Startwert mit dem Zeitstempel Start
		$Wert[2] = (string)$letzter_Wert;
		if($gefunden == 0) {
			$erste_Zeit = $Start;
		}
		if ($akt == 1){
			//letzte Werte aus der akt Tabelle holen
			$query = "SELECT `Value`, `Timestamp` FROM `akt` WHERE `Point_ID` = ? AND `Timestamp` > ? ORDER BY `Timestamp` ASC;";
			$stmt = mysqli_prepare($dbDH, $query);
			$xl = strlen($xd[$i]);
			$letzte_Zeit = substr($xd[$i], $xl-21, 19);
			mysqli_stmt_bind_param($stmt, "is", $Point_ID, $letzte_Zeit);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			while ($line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if($gefunden == 0) {
					//Startwert interpolieren
					$Zeitdifferenz=$line_Value['uTime']-$erste_Zeit;
					if($Zeitdifferenz==0) {
						$Wert[1] = $Start;
						$Wert[2] = (string)$erster_Wert;
					}else {
						$Steigung=($line_Value['Value']-$erster_Wert)/$Zeitdifferenz;
						$berechnet=(strtotime($Start)-$erste_Zeit)*$Steigung+$erster_Wert;
						$Wert[1]=$Start;
						$Wert[2]=(string)$berechnet;
					}
					$yd[$i] = $yd[$i].$Wert[2].",";
					$xd[$i] = $xd[$i]."'".$Wert[1]."',";
					$gefunden=1;
				}
				$Wert[1] = $line_Value['Timestamp'];
				$Wert[2] = (string)$line_Value['Value'];
				$letzter_Wert = (string)$line_Value["Value"];
				$yd[$i] = $yd[$i].$Wert[2].",";
				$xd[$i] = $xd[$i]."'".$Wert[1]."',";
			}
			mysqli_stmt_close($stmt);
			// Den Trend bis zum angegebenen Zeitpunkt zeichnen. Daher den letzten Wert zusaetzlich mit dem Zeitstempel des Scalenendes aufnehmen.
			$Wert[1] = $Ende;
			$Wert[2] = (string)$letzter_Wert;
			$yd[$i] = $yd[$i].$Wert[2].",";
			$xd[$i] = $xd[$i]."'".$Wert[1]."',";
		} else {
			if($Tagprop[$i]['step'] == 1) {
				$Wert[1] = $Ende;
			}
			$yd[$i] = $yd[$i].$Wert[2].",";
			$xd[$i] = $xd[$i]."'".$Wert[1]."',";
		}
	}
	$Wert[2] = null;
	$yd[$i] = substr($yd[$i],0,strlen($yd[$i])-1);
	$xd[$i] = substr($xd[$i],0,strlen($xd[$i])-1);
	$yd[$i] = $yd[$i]."]";
	$xd[$i] = $xd[$i]."]";
}

echo((hrtime(true) - $Anfangszeit)/1e+6);
//Tagproperties in ein verstecktes Feld schreiben, damit JS darauf zugreifen kann
echo "<input id='tagproperties' name='Tagproperties' type='hidden' value='".json_encode($Tagprop)."'>\n";
$Position_L = 0;
$Position_R = 1;
$Abstand = 0.025;
$Abstand2 = 60;
$Anf = intval(($Anzahl_akt_Tags + 1) / 2) * $Abstand2 / $Breite;
$End = 1 - intval($Anzahl_akt_Tags / 2) * $Abstand2 / $Breite;
if($Anf < 0.05) {$Anf=0.01;}
//echo $Anf."---".$End;
echo "<script>\n";
echo "var data = [\n";
for ($i = 0; $i < $Anzahl_akt_Tags; $i++){
	if($Tagprop[$i + 2]['step'] == 1) {
		$Shape = "hv";
	} else {
		$Shape = "linear";
	}
	if($i > 0) {
		echo "{x: ".$xd[$i + 2].", y: ".$yd[$i + 2].", yaxis: 'y".($i+1)."', mode: 'lines', name: '".$Tagprop[$i + 2]['Description']."', line: {shape: '".$Shape."'},},\n";
	} else {
		echo "{x: ".$xd[$i + 2].", y: ".$yd[$i + 2].", mode: 'lines', name: '".$Tagprop[$i + 2]['Description']."', line: {shape: '".$Shape."'},},\n";
	}
}
?>
];
var config = {
	locale: 'de',
	displaylogo: false,
	displayModeBar: true
};
var layout = {
	colorway : ['#0000ff', '#00ff00', '#ff0000', '#182844', '#BFBFBF', '#6f4d96', '#3d3b72'],
	width: visualViewport.width,
<?php	
if($mobil == 1) {
	echo "	height: screen.availHeight + 250,\n";
} else {
	echo "	height: screen.availHeight - 280,\n";
}
echo "  	yaxis: {range: [".$Tagprop[2]['Scale_min'].", ".$Tagprop[2]['Scale_max']."], title: '".$Tagprop[2]["EUDESC"]."', side: 'left', position: 0, color: Plotly.Plots.layoutAttributes.colorway.dflt[0]},\n";
for ($i=1; $i < $Anzahl_akt_Tags; $i++){
	if($i/2 == intval($i/2)) {
		$Seite = "left";
		$Position = $Position_L + $Abstand * $i; 
	} else {
		$Seite = "right";
		$Position = $Position_R - $Abstand * $i;
	}
	echo "yaxis".($i + 1).": {range: [".$Tagprop[$i + 2]['Scale_min'].", ".$Tagprop[$i + 2]['Scale_max']."], title: '".$Tagprop[$i + 2]["EUDESC"]."', side: '".$Seite."', overlaying: 'y', position: ".$Position.", color: Plotly.Plots.layoutAttributes.colorway.dflt[".$i."]},\n";
}
echo "	xaxis: {domain: [".$Anf.", ".$End."]},\n";
?>
	//title: "Temperaturverlauf",
	showlegend: true,
	legend: {"orientation": "h"}
};

// Display using Plotly
Plotly.newPlot("myPlot", data, layout, config);
</script>

<?php
echo "<b>Tags</b><br><table size='100%'><tr>";
if($Tagliste[0] == Null) {$Tagliste[0] = $Tag_ID;}
$Spalte = 0;
$i = 0;
$Anzahl_Tags = 0;
$zweite_Zeile = "";
while ($Anzahl_Tags < count($Tagliste)) {
	$variable = "Tag".$Tagliste[$i];
	//Tagname und Beschreibung lesen
	$query = "SELECT `Description`, `Tagname`, `EUDESC`, `Dezimalstellen` FROM `Tags` Where `Tag_ID` = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Tagliste[$i]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Tagname = html_entity_decode($line['Tagname']);
	$Point_ID = Point_ID_finden($Tagliste[$i], $dbDH);
	$Description = html_entity_decode($line['Description']);
	$EUDESC = html_entity_decode($line['EUDESC']);
	$Dezimalstellen = html_entity_decode($line['Dezimalstellen']);
	if($mobil == 1) {
		$Link = "<a href='./DH_Tagdetails.php?Tag_ID=".$Tagliste[$i]."' target = '_blank'>".$Tagname."</a>";
	} else{
		$Link = "<a href='javascript:void(0);' onclick='Tagdetails($Tagliste[$i]);'>".$Tagname."</a>";
	}
	if (${$variable} > 0){
		echo "<td><span title='".$Description."'><input type='checkbox' id='tag".$Tagliste[$i]."' name='Tag".$Tagliste[$i]."' value='1' checked onchange='chkbox_umschalten(\"tag".$Tagliste[$i]."\");'>".$Link."</span></td>";
	}else{
		echo "<td><span title='".$Description."'><input type='checkbox' id='tag".$Tagliste[$i]."' name='Tag".$Tagliste[$i]."' value='0' onchange='chkbox_umschalten(\"tag".$Tagliste[$i]."\");'>".$Link."</span></td>";
	}
	$zweite_Zeile = $zweite_Zeile."<td><a href='javascript:void(0);' onclick=\"Tabelle_aufrufen('Tag_ID=".$Tagliste[$i]."&Start=".$Start."&Ende=".$Ende."&Bezeichnung=".$Description."&EUDESC=".$EUDESC."&Dezimalstellen=".$Dezimalstellen."&Art=".$Art."');\">".$Text[20]."</a></td>";
	$i++;
	$Anzahl_Tags++;
}

if($neuer_Trend==1){$Tag1=1;}
echo "</tr><tr>".$zweite_Zeile."</table>";
echo "<input type = 'hidden' id = 'zeitspanne' value= '".$Spanne."'>\n";
echo "<input id='anzahl_tags' name='Anzahl_Tags' value= '".$Anzahl_Tags."' type='hidden'>\n";
echo "</form>\n";
// schliessen der Verbindung
mysqli_close($dbDH);
mysqli_close($db);
?>

<script type="text/javascript">
	var T_Text = new Array;
	T_Text = JSON.parse(document.getElementById("translation").value);
	
	function Tagdetails(Tag_ID) {
		try {
			panel.close('tagdetails');
		} catch (err) {}
		jQuery.ajax({
			url: "./DH_Tagdetails.php?Tag_ID=" + Tag_ID,
			success: function (html) {
   			strReturn = html;
			},
  			async: false
  		});
		panel = jsPanel.create({
			dragit: {
        		snap: true
        	},
			id: 'tagdetails',
			position: 'left-top 10 10',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '600 600',
			headerTitle: <?php echo "'".$Text[25]."'";?>,
			content: strReturn,
		});
	}
	
	function umschalten(Tab) {
		if (Tab == 1) {
			if (document.getElementById("zeit_einstellen").style.display == "block") {
				document.getElementById("zeit_einstellen").style.display = "none"
				document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
			} else {
				document.getElementById("zeit_einstellen").style.display = "block"
				document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
			}
		} else {
			document.getElementById("zeit_einstellen").style.display = "none";
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		}
		if (Tab == 3) {
			if (document.getElementById("sonstiges").style.display == "block") {
				document.getElementById("sonstiges").style.display = "none"
				document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
			} else {
				document.getElementById("sonstiges").style.display = "block"
				document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
			}
		} else {
			document.getElementById("sonstiges").style.display = "none";
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		}
	}

	 function chkbox_umschalten(Feld) {
	 	 if (document.getElementById(Feld).checked == true) {
	 	 	document.getElementById(Feld).value = "1";
	 	 } else {
	 	 	document.getElementById(Feld).value = "0";
	 	 }
	 }
	 
	 function skalen_einstellen() {
	 	var Tags = JSON.parse(document.getElementById("tagproperties").value);
	 	var Anzahl_Tags = parseInt(document.getElementById("anzahl_tags").value);
	 	var Inhalt = '<div style="position: absolute; top: 10px; left: 10px;"><table><tr><td><table cellspan="3px" id="point_tab" cellpadding="2px"><tr class="Tabellenzeile"><td class="Tabelle_Ueberschrift">Point_ID</td><td class="Tabelle_Ueberschrift">Tagname</td><td class="Tabelle_Ueberschrift">' + T_Text[35] + '</td><td class="Tabelle_Ueberschrift">min</td><td class="Tabelle_Ueberschrift">max</td></tr>';
	 	for (i = 0; i < Anzahl_Tags + 2; i++) {
	 		try {
	 			Inhalt = Inhalt + "<tr class='Tabellenzeile'><td><span class='Text_Element Point_Feld'>" + Tags[i].Point_ID + "</span></td><td>" + Tags[i].Tagname + "</td><td>" + Tags[i].Description + "</td>";
				Inhalt = Inhalt + "<td><input class='Text_Element min_Feld' value= '" + Tags[i].Scale_min + "' type='text' style=' width: 70px;'></td>";	 			
				Inhalt = Inhalt + "<td><input class='Text_Element max_Feld' value= '" + Tags[i].Scale_max + "' type='text' style=' width: 70px;'></td></tr>";
	 		} catch (err) {}
		}
	 	Inhalt = Inhalt + "</table></td></tr><tr><td><table width='100%'><tr height='40px'><td align='left'><input class='Schalter_Element' value='" + T_Text[36] + "' type='button' onclick='try {skalen_einstellen_dialog.close();} catch (err) {}'></td></td><td align='right'><input class='Schalter_Element' value='" + T_Text[37] + "' type='button'onclick='skalen_uebernehmen();'></td></tr></table></td></tr></table></div>";
	 	panel = jsPanel.create({
			dragit: {
        		snap: true
        	},
			id: 'skalen_einstellen_dialog',
			theme: 'info',
			position: 'left-top 10 10',
			headerControls: {
				size: 'xs'
			},
			contentSize: '600 400',
			headerTitle: <?php echo "'".$Text[25]."'";?>,
			content: Inhalt,
		});
	 	
	 }
	 
	 function skalen_uebernehmen() {
	 	var min_Felder = document.getElementsByClassName("min_Feld");
	 	var max_Felder = document.getElementsByClassName("max_Feld");
	 	var Point_ID_Felder = document.getElementsByClassName("Point_Feld");
	 	for (i = 0; i < min_Felder.length; i++) {
	 		jQuery.ajax({
				url: "./Skalen_editieren.php",
				type: 'POST',
				data: {Point_ID: Point_ID_Felder[i].innerHTML, min: min_Felder[i].value, max: max_Felder[i].value},
				async: false
			});
	 	}
	 	try {skalen_einstellen_dialog.close();} catch (err) {}
	 	document.forms["Einstellungen"].submit();
	 }
	 
	 function Tabelle_aufrufen(Parameter) {
		try {
			panel.close('datentabelle');
		} catch (err) {}
		jQuery.ajax({
			url: "./Tabelle.php?" + Parameter,
			success: function (html) {
   			strReturn = html;
			},
  			async: false
  		});
  		strReturn = '<div style="position: absolute; top: 10px; left: 10px;">' + T_Text[33] + ':&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="link" href="javascript:void(0);" onclick="csv_exportieren();">csv</a><br><br>' + strReturn + '</div>';
  		strReturn = strReturn + '<form style="display: none;" name="ex" method="post" action="./Tabelle_exp_csv.php" target="_blank">\n';
		strReturn = strReturn + '<input type="hidden" name="exStart">\n';
		strReturn = strReturn + '<input type="hidden" name="exEnde">\n';
		strReturn = strReturn + '<input type="hidden" name="exTitel">\n';
		strReturn = strReturn + '<input type="hidden" name="exParameterfeld">\n';
		strReturn = strReturn + '<input type="hidden" name="exverteilen" value="0">\n';
		strReturn = strReturn + '</form>\n';
  		var Hoehe = <?php if($mobil == 1) {echo "screen.availHeight + 270";} else {echo "screen.availHeight - 280";}?>;
		panel = jsPanel.create({
			dragit: {
        		snap: true
        	},
			id: 'datentabelle',
			position: 'left-top 10 10',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '450 ' + Hoehe,
			content: strReturn,
			headerTitle: <?php echo "'".$Text[25]."'";?>
		});
	}
	
function csv_exportieren() {
	document.forms.ex.action = "./Tabelle_exp_csv.php";
	document.forms.ex.exParameterfeld.value = Parameter_festlegen();
	document.forms.ex.exStart.value = document.forms.Einstellungen.Startzeitpunkt.value;
	document.forms.ex.exEnde.value = document.forms.Einstellungen.Ende.value;
	document.forms.ex.exTitel.value = document.forms.Einstellungen.Tag.value;
	document.forms.ex.submit();
}

function Parameter_festlegen() {
	var Parameterfeld = [];
	Arttext = document.forms.Einstellungen.Art.value;
	if (Arttext == "Rohwerte") {Arttext = "Rohdaten";}
	Parameterfeld[0] = {
		Tag_ID: document.forms.Einstellungen.Tag_ID.value,
		Art: Arttext,
		uTime: 0,
		vt: 0,
		vt_interpol: 0
	}
	return JSON.stringify(Parameterfeld);
}
</script>
</body>
</html>

