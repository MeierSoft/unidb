<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./jquery-3.3.1.min.js"></script>
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link rel="stylesheet" type="text/css" hrf="./JS_Plot/jquery.jqplot.min.css" />
<link rel="stylesheet" href="./css/jquery-ui.css">
<script type="text/javascript" src="./jquery-1.12.4.js"></script>
<script src="./scripts/jquery-ui.js"></script>
<script type="text/javascript" src="./JS_Plot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="./JS_Plot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="./JS_Plot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="./JS_Plot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="./JS_Plot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="./JS_Plot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script>
	$(function() {
			$("#tabs").tabs();
   	 	$("#Konfig").tabs();
   	 	$("#Zahlen_Tabellen").tabs();
		}
	);
	function Vers_wiederherstellen(Variante) {
		if (Variante == "wiederherstellen") {
			document.getElementById("Formular").aktion.value = "histspeichern";
		} else {
			document.getElementById("Formular").aktion.value = "histlöschen";
		}
		document.getElementById("Formular").submit();
	}
</script>

<?php	
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include 'mobil.php';
	include('Trend_funktionen.php');
	$Text = Translate("Trendgruppe.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[43];
		exit;
	}
	echo "<div id='tabs' style='font-family: arial, helvetica, sans-serif; font-size: 11px; font-weight: bold;'>";
	echo "<ul>";
		echo "<li><a href='#Trends'><b>".$Text[1]."</b></a></li>";
		echo "<li><a href='#Zahlen'><b>".$Text[2]."</b></a></li>";
	if($anzeigen == 1) {
		echo "<li><a href='#Konfiguration'><b>".$Text[3]."</b></a></li>";
	}
	echo"	</ul>";
	echo"	<div id='Trends'>";
if ($Aktion == $Text[4]) {
	for ($i=1; $i < 5; $i++){
		$variable1="Trend_".$i;
		$variable2="Tag".$i;
		$variable3="Tagname".$i;
		${$variable2} = Tag_Tag_ID(${$variable3});
		${$variable1} = ${$variable1}."Tag".$i."@".${$variable2}."§";
		$variable2="Min".$i;		
		${$variable1} = ${$variable1}."Min".$i."@".${$variable2}."§";
		$variable2="Min".$i."auto";
		${$variable1} = ${$variable1}."Min".$i."auto@".${$variable2}."§";
		$variable2="Max".$i;
		${$variable1} = ${$variable1}."Max".$i."@".${$variable2}."§";
		$variable2="Max".$i."auto";
		${$variable1} = ${$variable1}."Max".$i."auto@".${$variable2}."§";
		for ($j=1; $j < 10; $j++){
			$variable2="Typ".$i."-".$j;
			${$variable1} = ${$variable1}."Typ".$i."-".$j."@".${$variable2}."§";
			$variable2="LBreite".$i."-".$j;		
			${$variable1} = ${$variable1}."LBreite".$i."-".$j."@".${$variable2}."§";
			$variable2="Stil".$i."-".$j;
			${$variable1} = ${$variable1}."Stil".$i."-".$j."@".${$variable2}."§";
			$variable2="step".$i."-".$j;
			${$variable1} = ${$variable1}."step".$i."-".$j."@".${$variable2}."§";
		}
		$variable2="farbe".$i;
		${$variable1} = ${$variable1}."farbe".$i."@".${$variable2}."§";
		${$variable1} = htmlentities(mysqli_real_escape_string($db, ${$variable1}));
	}
	//$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Zeitspanne', ?, 'Trend_1', ?, 'Trend_2', ?, 'Trend_3', ?, 'Trend_4', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(0,$query, "ssssssii",[$Bezeichnung, $Zeitspanne_akt, $Trend_1, $Trend_2, $Trend_3, $Trend_4, $Baum_ID, $Server_ID]);
}
if($Aktion == "histspeichern") {
	$Bezeichnung = strip_tags($Bezeichnung);
	$Inhalt = htmlentities($Notiz);
	$query = "UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`, 'html', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Inhalt, $Baum_ID, $Server_ID]);
}
if($Aktion == "histlöschen") {
	$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
	$Timestamp = "";
}
//Variablen einlesen
$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Zeitspanne' as CHAR) as `Zeitspanne`, column_get(`Inhalt`, 'Trend_1' as CHAR) as `Trend_1`, column_get(`Inhalt`, 'Trend_2' as CHAR) as `Trend_2`, column_get(`Inhalt`, 'Trend_3' as CHAR) as `Trend_3`, column_get(`Inhalt`, 'Trend_4' as CHAR) as `Trend_4` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
//$Bezeichnung = $line['Bezeichnung'];
$Zeitspanne = floatval($line['Zeitspanne']);
if($Zeitspanne_akt < 1) {
	$Zeitspanne_akt = $Zeitspanne;
} else {
	$Zeitspanne = $Zeitspanne_akt;
}
for ($i=1; $i < 5; $i++){
	$temp = "Trend_".$i;
	$temp1 = html_entity_decode($line[$temp]);
	$temp2 = explode("§", $temp1);
	for ($j=0; $j < count($temp2)-1; $j++){
		$temp3 = explode("@", $temp2[$j]);
		$temp = $temp3[0];
		${$temp} = $temp3[1];
	}
}
if ($Zeitspanne == ""){$Zeitspanne=strtotime($Ende)-strtotime($Start);}
if ($Zeitspanne<1){$Zeitspanne=86400;}
if ($Ende<1){
	$Ende=strftime('%Y-%m-%d %H:%M:%S',time());
	}
if ($Ende == $Text[5]){$Ende = strftime('%Y-%m-%d %H:%M:%S',time());}
if ($Zeitspanne<1){$Zeitspanne=1;}

$Start=strftime('%Y-%m-%d %H:%M:%S',(strtotime($Ende)-$Zeitspanne));

$Feld_Typ1 = $Text[42];
$Feld_Typ2 = "hMW";
$Feld_Typ3 = "hMinMax";
$Feld_Typ4 = "hMin";
$Feld_Typ5 = "hMax";
$Feld_Typ6 = "dMW";
$Feld_Typ7 = "dMinMax";
$Feld_Typ8 = "dMin";
$Feld_Typ9 = "dMax";

echo "<form id='Formular' action='./Trendgruppe.php' method='post' target='_self'>";
echo "<input name='Baum_ID' value= '".$Baum_ID."' type='hidden'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<table><tr><td>";
echo "<table>";
echo "<tr  align=\"center\"><td width=\"100px\">".$Text[6]."</td><td width=\"120px\">".$Text[7]."</td><td width=\"120px\">".$Text[8]."</td><td width=\"45px\">".$Text[9]."</td><td width=\"45px\">".$Text[10]."</td>";
if ($mobil==1){echo "<td></td><td><a href='Baum2.php' target='_self'>".$Text[11]."</a></td>";}
echo "</tr>";
echo "<tr  align=\"center\"><td><select class='Auswahl_Liste_Element' id='Zeitspanne' name='Zeitspanne' size='1' onchange = 'verschieben(\"unveraendert\");'>";

if ($Zeitspanne==3600){
		echo "<option value = '3600' selected>1 h</option>";
	}else{
		echo "<option value = '3600'>1 h</option>";
	}
if ($Zeitspanne==14400){
		echo "<option value = '14400' selected>4 h</option>";
	}else{
		echo "<option value = '14400'>4 h</option>";
	}
if ($Zeitspanne==28800){
		echo "<option value = '28800' selected>8 h</option>";
	}else{
		echo "<option value = '28800'>8 h</option>";
	}
if ($Zeitspanne==86400){
		echo "<option value = '86400' selected>".$Text[12]."</option>";
	}else{
		echo "<option value = '86400' >".$Text[12]."</option>";
	}
if ($Zeitspanne==172800){
		echo "<option value = '172800' selected>".$Text[13]."</option>";
	}else{
		echo "<option value = '172800' >".$Text[13]."</option>";
	}
if ($Zeitspanne==604800){
		echo "<option value = '604800' selected>".$Text[14]."</option>";
	}else{
		echo "<option value = '604800' >".$Text[14]."</option>";
	}
if ($Zeitspanne==1209600){
		echo "<option value = '1209600' selected>".$Text[15]."</option>";
	}else{
		echo "<option value = '1209600' >".$Text[15]."</option>";
	}
if ($Zeitspanne==2592000){
		echo "<option value = '2592000' selected>".$Text[16]."</option>";
	}else{
		echo "<option value = '2592000' >".$Text[16]."</option>";
	}
if ($Zeitspanne==31536000){
		echo "<option value = '31536000' selected>".$Text[17]."</option>";
	}else{
		echo "<option value = '31536000' >".$Text[17]."</option>";
	}

echo "</select></td>";
echo "<td><input class='Text_Element' id=\"StartFeld\" name='Start' value= '".$Start."' type='text' size='16' maxlength='31'></td>";
echo "<td><input class='Text_Element' id=\"EndeFeld\" name='Ende' value= '".$Ende."' type='text' size='16' maxlength='31'></td>";
echo "<td><input class='Schalter_fett_Element' id=\"zurueck\" value='<' type='button' name='schieben' onclick = 'verschieben(\"<\");'></td>";
echo "<td><input class='Schalter_fett_Element' id=\"vor\" value='>' type='submit' name='schieben' onclick = 'verschieben(\">\");'></td>";
echo "<td width=\"60px\"><input class='Schalter_Element' id=\"Schalter_jetzt\" value='".$Text[5]."' type='submit' name='schieben' onclick = 'verschieben(\"jetzt\");'></td>";
echo "<td width=\"90px\"><input class='Schalter_Element' id=\"Schalter_auffrischen\" value='".$Text[18]."' type='submit' name='Aktion'></td>";
echo "<td width=\"65px\"><input class='Schalter_Element' id=\"Hilfe\" value='".$Text[19]."' type='button' name='Hilfe' onclick=\"Hilfe_Fenster('1');\"></td></tr></table>";
$Anzahl_Tags = 0;
for ($i=1;$i<=4;$i++){
	for ($j=1;$j<=9;$j++){
		$variable1 = "Typ".$i."-".$j;
		$variable2 = "Tag".$i;
		if(${$variable1} == "1" and ${$variable2} != "" and ${$variable2} != 0) {$Anzahl_Tags=$Anzahl_Tags+1;}
	}
}

$Tag_cfg_Name = array(5);
$Tag_cfg_Pfad = array(5);
$Tag_cfg_step = array(5);
$Tag_cfg_MW = array(5);
$Tag_cfg_EUDESC = array(5);
$Tag_cfg_DESC = array(5);
$Trend_Zeit = array($Anzahl_Tags);
$Trend_Wert = array($Anzahl_Tags);
$Tagmin = array($Anzahl_Tags);
$Tagmax = array($Anzahl_Tags);
$Tagminauto = array($Anzahl_Tags);
$Tagmaxauto = array($Anzahl_Tags);
$Tagname = array($Anzahl_Tags);
$Pfad = array($Anzahl_Tags);
$Tagart = array($Anzahl_Tags);
$Tagbreite = array($Anzahl_Tags);
$Taglinie = array($Anzahl_Tags);
$Tagstep = array($Anzahl_Tags);
$Tagnr = array($Anzahl_Tags);
$Farbe = array(4);
$Tag = array(5);
$Typ = array(9);
$Typ[1] = "`Art` = 'rV'";
$Typ[2] = "`Art` = 'hMW'";
$Typ[3] = "(`Art` = 'hMin' OR `Art` = 'hMax')";
$Typ[4] = "`Art` = 'hMin'";
$Typ[5] = "`Art` = 'hMax'";
$Typ[6] = "`Art` = 'dMW'";
$Typ[7] = "(`Art` = 'dMin' OR `Art` = 'dMax')";
$Typ[8] = "`Art` = 'dMin'";
$Typ[9] = "`Art` = 'dMax'";

$x=0;
for ($i=1;$i<=4;$i++){
	$variable = "Tag".$i;
	$Tagkonfiguration = Point_ID_cfg(${$variable});
	$Tag_cfg_Pfad[$i] = $Tagkonfiguration[0];	
	$Tag_cfg_Name[$i] = $Tagkonfiguration[1];
	$Tag_cfg_step[$i] = $Tagkonfiguration[2];
	$Tag_cfg_MW[$i] = $Tagkonfiguration[3];
	$Tag_cfg_EUDESC[$i] = $Tagkonfiguration[4];
	$Tag_cfg_DESC[$i] = $Tagkonfiguration[5];
	$variable2 = "farbe".$i;
	$Farbe[$i] = ${$variable2};
	$variable2 = "Tag".$i;
	$Tag[$i] = ${$variable2};
	for ($j=1;$j<=9;$j++){
		$variable1 = "Typ".$i."-".$j;
		if(${$variable1} == "1" and $Tag[$i] != 0 and $Tag[$i] != "") {
			$x=$x+1;
			$Tagnr[$x] = $i;
			$Tagart[$x] = $Typ[$j];
			$variable1 = "Min".$i;
			$Tagmin[$x] = ${$variable1};
			$variable1 = "Max".$i;
			$Tagmax[$x] = ${$variable1};
			$variable1 = "Min".$i."auto";
			$Tagminauto[$x] = ${$variable1};
			$variable1 = "Max".$i."auto";
			$Tagmaxauto[$x] = ${$variable1};
			$variable1 = "Tag".$i;
			$Tagname[$x] = $Tag_cfg_Pfad[$x].${$variable1};
			$variable1 = "LBreite".$i."-".$j;
			$Tagbreite[$x] = ${$variable1};
			$variable1 = "Stil".$i."-".$j;
			$Taglinie[$x] = ${$variable1};
			$variable1 = "step".$i."-".$j;
			$Tagstep[$x] = ${$variable1};
		}
	}
}

for ($i=1;$i<=4;$i++){
	$variable = "Minx".$i;
	${$variable} = 10e10;
	$variable = "Maxx".$i;
	${$variable} = 1e-10;
}

for ($i=1;$i<=$Anzahl_Tags;$i++){
	if($Tagstep[$i] == "1" or $Tag_cfg_step[$Tagnr[$i]] == 1) {
		$step=1;
	} else {
		$step=0;
	}
	$Ergebnis = Daten_holen($Tag[$Tagnr[$i]],$Start,$Ende,$Tagart[$i],$step);
	$variable = "Trend".$i;
	${$variable} = $Ergebnis[0];
	$variable = "Minx".$Tagnr[$i];
	if($Ergebnis[1] < ${$variable}) {${$variable} = $Ergebnis[1];}
	$variable = "Maxx".$Tagnr[$i];
	if($Ergebnis[2] > ${$variable}) {${$variable} = $Ergebnis[2];}
	$variable = "Trend_Zeit".$i;
	${$variable} = $Ergebnis[3];
	$variable = "Trend_Wert".$i;
	${$variable} = $Ergebnis[4];
}	

$Stil = array(3);
$Stil[1] = "____";
$Stil[2] = "_ _ _";
$Stil[3] = ".....";

echo "<td>";
for ($i=1;$i<=4;$i++){
	$variable1 = "farbe".$i;
	echo "<td valign='top'><span style='color: ".${$variable1}.";'><table>";
	for ($j=1;$j<=9;$j++){
		$variable1 = "Typ".$i."-".$j;
		if(${$variable1} == "1" and $Tag_cfg_Name[$i] != "") {
			$variable2 = "Stil".$i."-".$j;
			if(${$variable2} == $Text[20]) {$x = 1;}
			if(${$variable2} == $Text[21]) {$x = 2;}
			if(${$variable2} == $Text[22]) {$x = 3;}
			$variable3 = "Feld_Typ".$j;
			echo "<tr><td><div title='".$Tag_cfg_DESC[$i]."'>".$Tag_cfg_Name[$i]."</div></td><td align = 'right'><div title='".$Tag_cfg_DESC[$i]."'>&nbsp;&nbsp;".${$variable3}."</div></td><td><div title='".$Tag_cfg_DESC[$i]."'>&nbsp;&nbsp;".$Stil[$x]."&nbsp;&nbsp;&nbsp;&nbsp;</td></div></tr>";
		}	
	}
	echo "</span></table></td>";
}
echo "</td></tr></table>";

echo "<input id = 'Zeitspanne_akt' name='Zeitspanne_akt' value= '".$Zeitspanne_akt."' type='hidden'>";

echo "<div id=\"chart1\" class=\"jqplot-target\" style=\"width:100%; height:800px;\"></div>\n";
echo "<script type=\"text/javascript\" class=\"code\">\n";
echo "	function Start(){\n";
echo "$.jqplot.config.enablePlugins = true;\n";
for ($i=1;$i<=$Anzahl_Tags;$i++){
	$variable = "Trend".$i;
	if($Tagminauto[$i] != "1") {
		$variable1="Minx".$Tagnr[$i];
		${$variable1} = $Tagmin[$i];
	}
	if($Tagmaxauto[$i] != "1") {
		$variable1="Maxx".$Tagnr[$i];
		${$variable1} = $Tagmax[$i];
	}
	echo "    var Trend".$i." = ".${$variable}."\n";
}
echo "    opts = {\n";
echo "      seriesDefaults: {\n";
echo "          showMarker: false,\n";
echo "           lineWidth: 2,\n";
echo "      },\n";
//echo "      series:[\n";
$Textx = "      series:[\n";
for ($i=1;$i<=$Anzahl_Tags;$i++){
	$variable = "farbe".$Tagnr[$i];
	$Textx = $Textx."      	{color: '".${$variable}."', yaxis: ";
	if($Tagnr[$i] == 1){
		$Textx = $Textx."'yaxis', ";
	} else {
		$Textx = $Textx."'y".$Tagnr[$i]."axis', ";
	}
	$Textx = $Textx."lineWidth: ".$Tagbreite[$i].", ";
	$Linie = "'solid'";
	if($Taglinie[$i] == "gestrichelt") {
		$Linie = "'dashed'";
	}
	if($Taglinie[$i] == "gepunktet") {
		$Linie = "'dotted'";
	}
	$Textx = $Textx."linePattern: ".$Linie."},\n";
}
$Textx = substr($Textx, 0, strlen($Textx)-2);
$Textx = $Textx."\n		],\n";
echo $Textx;
echo "      axesDefaults:{\n";
echo "      	rendererOptions:{\n";
echo "       		useSeriesColor: true,\n";
echo "       		borderWidth: 5,\n";
echo "      	},\n";

echo "      	numberTicks: 11,\n";
echo "      	tickOptions:{\n";
echo "				markSize: 10,\n";
echo "				fontSize: '12px',\n";
echo "      	}},\n";
echo "      axes: {\n";
echo "          xaxis: {\n";
echo "            renderer:$.jqplot.DateAxisRenderer,\n";
echo "            min:'".$Start."',\n";
echo "            max:'".$Ende."',\n";
if ($Zeitspanne < 86401){
	echo "            tickOptions:{formatString:\"<br>%H:%M:%S\",\n";
}
else{
	echo "            tickOptions:{formatString:\"<br>%d.%m %H:%M\",\n";
}
echo "             }\n";
echo "          },\n";
if($Tag1 !="") {
	echo "          yaxis: {\n";
	echo "           min:".$Minx1.",\n";
	echo "           max:".$Maxx1.",\n";
	echo "         label:'<div style=\"font-size: 14px;\"><br><br>".$Tag_cfg_EUDESC[1]."</div>',\n";
	echo "			},\n";
}
if($Tag2 !="") {
	echo "         y2axis: {\n";
	echo "           min:".$Minx2.",\n";
	echo "           max:".$Maxx2.",\n";
	echo "         label:'<div style=\"font-size: 14px;\"><br><br>".$Tag_cfg_EUDESC[2]."</div>',\n";
	echo "      	tickOptions:{\n";
	echo "				prefix: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',\n";
	echo "			}},\n";
}
if($Tag3 !="") {
	echo "         y3axis: {\n";
	echo "            prefix:'  ',\n";
	echo "           min:".$Minx3.",\n";
	echo "           max:".$Maxx3.",\n";
	echo "         label:'<div style=\"font-size: 14px;\"><br><br>".$Tag_cfg_EUDESC[3]."</div>',\n";
	echo "      	tickOptions:{\n";
	echo "				prefix: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',\n";
	echo "			}},\n";
}
if($Tag4 !="") {
	echo "			y4axis: {\n";
	echo "           min:".$Minx4.",\n";
	echo "           max:".$Maxx4.",\n";
	echo "         label:'<div style=\"font-size: 14px;\"><br><br>".$Tag_cfg_EUDESC[4]."</div>',\n";
	echo "      	tickOptions:{\n";
	echo "				prefix: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',\n";
	echo "			}},\n";
}
echo "      },\n";
echo "      cursor:{zoom:true}\n";
echo "  };\n";
echo "  plot1 = $.jqplot('chart1', [";
$Textx="";
for ($i=1;$i<=$Anzahl_Tags;$i++){
	$Textx = $Textx."Trend".$i.",";
}
$Textx = substr($Textx, 0, strlen($Textx)-1);
$Textx = $Textx."], opts);\n";
echo $Textx;
?>
  }
  	function verschieben(Richtung){
  		if (document.getElementById('Zeitspanne').value > 0) {
  			document.getElementById('Zeitspanne_akt').value = document.getElementById('Zeitspanne').value;
  		}
  		var Spanne = document.getElementById('Zeitspanne').value;
  		var Ende = new Date(document.getElementById('EndeFeld').value);
  		var Endezeitpunkt = Ende.getTime();
  		if (Richtung == '<') {
  			Endezeitpunkt = Endezeitpunkt - (Spanne * 1000);
		}
		if (Richtung == '>') {
  			Endezeitpunkt = Endezeitpunkt + (Spanne * 1000);
		}
		if (Richtung == 'jetzt') {
			var jetzt = new Date(); 
			Endezeitpunkt = jetzt.getTime();
		}
		var s = new Date();
		var Startzeit = s.setTime(Endezeitpunkt - (Spanne * 1000));
		var e = new Date();
		var Endezeit = e.setTime(Endezeitpunkt);
		var Monat = s.getMonth();
		Monat = Monat + 1;
		Monat = Monat.toString();
		if (Monat.length == 1) {Monat = '0' + Monat;}
		var Tag = s.getDate();
		Tag = Tag.toString();
		if (Tag.length == 1) {Tag = '0' + Tag;}
		document.getElementById('StartFeld').value = s.getFullYear() + "-" + Monat + "-" + Tag + " " + s.getHours() + ":" + s.getMinutes() + ":" + s.getSeconds();
		Monat = e.getMonth();
		Monat = Monat + 1;
		Monat = Monat.toString();
		if (Monat.length == 1) {Monat = '0' + Monat;}
		Tag = e.getDate();
		Tag = Tag.toString();
		if (Tag.length == 1) {Tag = '0' + Tag;}
		document.getElementById('EndeFeld').value = e.getFullYear() + "-" + Monat + "-" + Tag + " " + e.getHours() + ":" + e.getMinutes() + ":" + e.getSeconds();
		document.getElementById("Formular").submit();
  	}
	function anpassen(){
		document.getElementById('chart1').style.height = window.innerHeight-150+'px';
		//plot1.destroy();
		plot1.replot();
		//Start();
	}
	window.addEventListener('resize', anpassen);
	$(window).on('load',function(){
		Start();
		anpassen();
  	});
</script>

</div>

<div id="Zahlen">
<?php
echo "	<div id='Zahlen_Tabellen' style='font-family: arial, helvetica, sans-serif; font-size: 11px; font-weight: normal;'>\n";
	echo "		<ul>\n";
for($i=1; $i < 5; $i++) {
	echo "			<li><a href='#Tabelle".$i."'>".$Text[23]." ".$i."</a></li>\n";
}
echo "		</ul>\n";
for($i=1; $i < 5; $i++) {
	echo "		<div id='Tabelle".$i."' style='font-family: arial, helvetica, sans-serif; font-size: 12px; font-weight: normal;'>\n";
	echo "<table><tr height = '50' valign = 'top'>\n";
	echo "<td><input class='Schalter_Element'  name='Tagkonfiguration_Schalter' value='".$Text[24]."' type='button' onclick='Tagkonfiguration_oeffnen(".$Tag[$i].");'></td>";
	echo "<td><input id=\"Hilfe\" value='".$Text[19]."' class='Schalter_Element'  type='button' name='Hilfe' onclick=\"Hilfe_Fenster('2');\"></td></tr><tr>";
	for($j=1; $j <= $Anzahl_Tags; $j++) {
		if($Tagnr[$j] == $i) {
			$Art = $Tagart[$j];
			if($Art == "(`Art` = 'dMin' OR `Art` = 'dMax')") {$Art = "dMinMax";}
			if($Art == "(`Art` = 'hMin' OR `Art` = 'hMax')") {$Art = "hMinMax";}
			$Art = str_replace("`Art` = '", "", $Art);
			$Art = str_replace("'", "", $Art);
			$variable = "Trend_Wert".$j;
			$Werte = explode(",", ${$variable});
			$variable1 = "Trend_Zeit".$j;
			$Zeiten = explode(",", ${$variable1});
			echo "<td width = '250' valign='top' bgcolor='#EFEEEE'>Tag: <b>".$Tag_cfg_Name[$i]."</b>&nbsp;&nbsp;&nbsp;&nbsp;Art: <b>".$Art."</b><br><br><table><tr><td width = '130'><b>".$Text[25]."</b></td><td><b>".$Text[26]."</b></td></tr>\n";
			for($x=count($Zeiten); $x > 0; $x--) {
				echo "<tr><td>".$Zeiten[$x]."</td><td>".$Werte[$x]."</td></tr>\n";
			}
			echo "</table></td><td width = '20'></td>\n";
		}
	}
	echo "</td></tr></table>\n";
	echo "		</div>\n";
}
echo "	</div>\n";
echo "</div>\n";
if(Berechtigung($Baum_ID, $Server_ID) == 1) {
	echo "<div id='Konfiguration'>\n";
	echo "	<div id='Konfig' style='font-family: arial, helvetica, sans-serif; font-size: 11px; font-weight: bold;'>\n";
	echo "		<ul>\n";
	echo "			<li><a href='#allgemein'>".$Text[27]."</a></li>\n";
	for($i=1; $i < 5; $i++) {
		echo "			<li><a href='#Trend".$i."'>".$Text[23]." ".$i."</a></li>\n";
	}
	echo "		</ul>\n";
	echo "		<div id='allgemein'>\n";
	echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
	echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
	if ($Timestamp > ""){
		$abfrage = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Zeitspanne' as CHAR) as `Zeitspanne`, column_get(`Inhalt`, 'Trend_1' as CHAR) as `Trend_1`, column_get(`Inhalt`, 'Trend_2' as CHAR) as `Trend_2`, column_get(`Inhalt`, 'Trend_3' as CHAR) as `Trend_3`, column_get(`Inhalt`, 'Trend_4' as CHAR) as `Trend_4` FROM `Baumhistorie` where `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$abfrage = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Zeitspanne' as CHAR) as `Zeitspanne`, column_get(`Inhalt`, 'Trend_1' as CHAR) as `Trend_1`, column_get(`Inhalt`, 'Trend_2' as CHAR) as `Trend_2`, column_get(`Inhalt`, 'Trend_3' as CHAR) as `Trend_3`, column_get(`Inhalt`, 'Trend_4' as CHAR) as `Trend_4` FROM `Baum` where `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<input type='hidden' id='bezeichnung' name='Bezeichnung' value = '".$line["Bezeichnung"]."'>\n";
	echo "<input type='hidden' id='inhalt' name='Inhalt' value = '".$line["Inhalt"]."'>\n";
	echo "<input type='hidden' id='zeitspanne' name='Zeitspanne' value = '".$line["Zeitspanne"]."'>\n";
	echo "<input type='hidden' id='trend_1' name='Trend_1' value = '".$line["Trend_1"]."'>\n";
	echo "<input type='hidden' id='trend_2' name='Trend_2' value = '".$line["Trend_2"]."'>\n";
	echo "<input type='hidden' id='trend_3' name='Trend_3' value = '".$line["Trend_3"]."'>\n";
	echo "<input type='hidden' id='trend_4' name='Trend_4' value = '".$line["Trend_4"]."'>\n";
	mysqli_stmt_close($stmt);
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
		if ($Timestamp == null) {
			if ($line["geloescht"] != 1) {
				echo "<br><a href='./loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[28]."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='./verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[29]."</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[30]."</a>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Schalter_Element' id=\"Hilfe\" value='".$Text[19]."' type='button' name='Hilfe' onclick=\"Hilfe_Fenster('3');\">";
			} else {
				if ($mobil==1){
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a>&nbsp;&nbsp;&nbsp;";
				} else {
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a>&nbsp;&nbsp;&nbsp;";
				}
			}
		} else {
			echo "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>Aktuelle Version durch diese hier ersetzen.</a>";
			echo "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>Diese Version löschen.</a>";
		}
	}

	if($line["geloescht"] != 1) {
		$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Version vom:&nbsp;<select name='Timestamp' id='timestamp' onchange='document.getElementById(\"Formular\").submit();'><option></option>";
			while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				if($line1["Timestamp"] == $Timestamp) {
					echo "<option selected>".$line1["Timestamp"]."</option>";
				} else {
					echo "<option>".$line1["Timestamp"]."</option>";
				}
			}
			echo "</select>";
		}
		mysqli_stmt_close($stmt1);
	}
	echo "			<br><br><br><table><tr><td><td>".$Text[31].": </td><td><input class='Text_Element' id=\"Bezeichnung\" value='".$Bezeichnung."' type='text' name='Bezeichnung' size='40'></td>";
	echo "			<td>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Schalter_Element' id=\"speichern\" value='".$Text[4]."' type='submit' name='Aktion'></td></tr></table>\n";
	echo "			<input id=\"Vorlage_ID\" value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>\n";
	echo "			<input id=\"Baum_ID\" value='".$Baum_ID."' type='hidden' name='Baum_ID'>\n";
	echo "			<input id=\"Server_ID\" value='".$Server_ID."' type='hidden' name='Server_ID'>\n";
	echo "		</div>\n";

	$Farbe1 = "#000000";
	$Farbe2 = "#0000ff";
	$Farbe3 = "#00ff00";
	$Farbe4 = "#ff0000";

	for($i=1; $i < 5; $i++) {
		$variable = "Tag".$i;
		echo "		<div id='Trend".$i."'>\n";
		echo "			<table><tr><td>\n";
		echo "			<table style='font-family: arial, helvetica, sans-serif; font-size: 12px;' cellpadding='4' bgcolor='#07E5FC' width='1' frame='box'>\n";
		echo "				<tr bgcolor='#FAFDCE'>\n";
		echo "					<td align='right'>Tag</td>\n";
		$variable2="Tagname".$i;
		$leer = 1;
		if(${"Min".$i."auto"} == "") {$leer = 0;}
		echo "					<input type='hidden' name='Tag".$i."' value=".${$variable2}.">\n";
		${$variable2} = $Tag_cfg_Pfad[$i].$Tag_cfg_Name[$i];
		echo "					<td><input class='Text_Element' id = 'Tagname".$i."' type='text' name='Tagname".$i."' value='".${$variable2}."' size='25'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Schalter_Element' name='Tag_suchen_Schalter' value='".$Text[32]."' type='button' onclick=\"Eingabefeld_sichtbar_dialog('".$i."');\"></td>\n";
		echo "				</tr>\n";
		echo "				<tr bgcolor='#FAFDCE'>\n";
		echo "					<td align='right'>".$Text[33]."</td>\n";
		$variable2="Min".$i;
		$variable3="Min".$i."auto";
		$variable4 = "";
		if(${$variable3} == "1" or $leer == 1) {$variable4 = "checked";}
		echo "					<td><input class='Text_Element' type='text' name='Min".$i."' value='".${$variable2}."' size='3'>".$Text[34]."<input style='vertical-align: middle;'  type='checkbox' name='Min".$i."auto' value='1' ".$variable4."></td>\n";
		echo "				</tr>\n";
		echo "				<tr bgcolor='#FAFDCE'>\n";
		echo "					<td align='right'>".$Text[35]."</td>\n";
		$variable2="Max".$i;
		$variable3="Max".$i."auto";
		$variable4 = "";
		$leer = 1;
		if(${"Min".$i."auto"} == "") {$leer = 0;}
		if(${$variable3} == "1" or $leer == 1) {$variable4 = "checked";}
		echo "					<td><input class='Text_Element' type='text' name='Max".$i."' value='".${$variable2}."' size='3'>".$Text[34]."<input style='vertical-align: middle;'  type='checkbox' name='Max".$i."auto' value='1' ".$variable4."></td>\n";
		echo "				</tr>\n";
		echo "				<tr align='center' bgcolor='#FAFDCE'>\n";
		echo "					<td align='right'>".$Text[36]."</td>\n";
		$variable2="farbe".$i;
		$variable3="Farbe".$i;
		if (${$variable2} == "") {${$variable2} = ${$variable3};}
		echo "					<td><input id='farbe".$i."' name='farbe".$i."' value='".${$variable2}."' type='color'></td>\n";
		echo "				</tr>\n";
		echo "				<tr bgcolor='#FAFDCE'>\n";
		echo "					<td align='right'>".$Text[23]."</td>\n";
		echo "					<td>\n";
		echo "						<table width='280px'><tr align='middle'><td>".$Text[37]."</td><td>".$Text[38]."</td><td>".$Text[39]."</td><td>".$Text[40]."</td></tr>\n";
		echo "							<tr>\n";
		for($j=1; $j < 10; $j++) {
			$sperren = "";
			if($Tag_cfg_MW[$i] == 0 and $j > 1) {$sperren = " disabled";}
			$variable2="Typ".$i."-".$j;
			$variable3="Feld_Typ".$j;
			$variable4 = "";
			if(${$variable2} == "1") {$variable4 = "checked";}
			$variable5="Stil".$i."-".$j;
			$variable6 = "";
			if(${$variable5} == $Text[20]) {$variable6 = "selected";}
			$variable7 = "";
			if(${$variable5} == $Text[21]) {$variable7 = "selected";}
			$variable8 = "";
			if(${$variable5} == $Text[22]) {$variable8 = "selected";}
			echo "								<td align='right'>".${$variable3}.": <input style='vertical-align: middle;' type='checkbox' name='".$variable2."' value='1' ".$variable4.$sperren."></td>\n";
			echo "								<td><select class='Auswahl_Liste_Element' style='font-family: arial, helvetica, sans-serif; font-size: 12px;' name ='".$variable5."' value='".${$variable5}."'><option ".$variable6.">".$Text[20]."</option><option ".$variable7.">".$Text[21]."</option><option ".$variable8.">".$Text[22]."</option></select></td>\n";
			$variable2="LBreite".$i."-".$j;
			if (${$variable2} == "") {${$variable2} = "2";}
			echo "								<td><input class='Text_Element' type='text' name='LBreite".$i."-".$j."' value='".${$variable2}."' size='1'></td>\n";
			$variable2="step".$i."-".$j;
			$variable4 = "";
			$markieren = 0;
			$sperren = "";
			if($Tag_cfg_step[$i] == "1") {$sperren = " disabled";}
			if($j > 1 and $leer == 1) {$markieren = 1;}
			if($Tag_cfg_step[$i] == "1") {$markieren = 1;}
			if(${$variable2} == "1" or $markieren == 1) {$variable4 = "checked";}		
			echo "								<td><input style='vertical-align: middle;' type='checkbox' name='".$variable2."' value='1' ".$variable4.$sperren."></td>\n";
			echo "							</tr>\n";
			echo "							<tr>\n";
		}	
		echo "						</table>\n";
		echo "					</td>\n";
		echo "				</tr>\n";
		echo "			</table>\n";
		echo "			</td></tr>\n";
		echo "<tr height = '50px'><td><table width = '100%'><tr><td><input class='Schalter_Element' id=\"Hilfe\" value='".$Text[19]."' type='button' name='Hilfe' onclick=\"Hilfe_Fenster('4');\"></td><td align = 'right'><input class='Schalter_Element' id=\"speichern\" value='".$Text[4]."' type='submit' name='Aktion'></td></tr></table></td></tr>\n";
		echo "			</table>\n";
		echo "		</div>\n";
	}
}
echo "</form>";

function Tag_Tag_ID($Tagname) {
	require 'conf_DH.php';
	$Pfad = "%";
	if(substr($Tagname,0,1) == "/") {
		$pos = strrpos($Tagname, "/");
		$Pfad = substr($Tagname, 0, $pos + 1);
		$Tagname = substr($Tagname, $pos + 1);
	}
	$abfrage = "SELECT `Tag_ID` FROM `Tags` Where `Path` = ? AND `Tagname` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH);
	return intval($row[0]);
}

function Point_ID_cfg($Tag_ID) {
	require 'conf_DH.php';
	$abfrage = "SELECT `Path`, `Tagname`, `step`, `Mittelwerte`, `EUDESC`, `Description` FROM `Tags` Where `Tag_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "i", $Tag_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH);
	return array(html_entity_decode($row[0]), html_entity_decode($row[1]), intval($row[2]), intval($row[3]), html_entity_decode($row[4]), html_entity_decode($row[5]));
	//return array($row[0], intval($row[1]), intval($row[2]), $row[3], $row[4]);
}


function Tag_step($Tagname) {
	require 'conf_DH.php';
	$Pfad = "%";
	if(substr($Tagname,0,1) == "/") {
		$pos = strrpos($Tagname, "/");
		$Pfad = substr($Tagname, 0, $pos + 1);
		$Tagname = substr($Tagname, $pos + 1);
	}
	$abfrage = "SELECT `step` FROM `Tags` Where `Path` = ? AND `Tagname` = ?;";
	$stmt = mysqli_prepare($dbDH,$abfrage);
	mysqli_stmt_bind_param($stmt, "ss", $Pfad, $Tagname);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_row($result);
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH);
	return intval($row[0]);
}

function Daten_holen($Punkt,$Start,$Ende,$Art,$step) {
	require 'conf_DH.php';
	$Werte = array();
	if($Art == "(`Art` = 'dMin' OR `Art` = 'dMax')") {$Art = "dMinMax";}
	if($Art == "(`Art` = 'hMin' OR `Art` = 'hMax')") {$Art = "hMinMax";}
	$Art = str_replace("`Art` = '", "", $Art);
	$Art = str_replace("'", "", $Art);
	//Point_ID für den Tag finden
	$Punkt = Point_ID_finden($Punkt, $dbDH);
	$Min = 10e10;
	$Max = 10e-10;

	//Tagdetails aus der Tabelle Tags lesen
	$query = "SELECT `Tagname`, `Scale_max`, `Scale_min`, `first_value` FROM `Tags` Where `Point_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "i", $Punkt);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Tags = mysqli_fetch_array($result, MYSQLI_ASSOC);

	mysqli_stmt_close($stmt);
	if ($Scala_max=="" AND $Scala_min==""){
		$Scala_max=$line_Tags["Scale_max"];
		$Scala_min=$line_Tags["Scale_min"];
	}else{
		if ($line_Tags["Scale_max"]>$Scala_max){$Scala_max=$line_Tags["Scale_max"];}
		if ($line_Tags["Scale_min"]<$Scala_min){$Scala_min=$line_Tags["Scale_min"];}
	}

	if($line_Tags["first_value"] > $Start) {
		$Start = $line_Tags["first_value"];
	}

	//ersten Wert ggf interpolieren
	$Startzeitpunkt = strtotime($Start);
	$Werte = lesen($Art,$Punkt, $Start, $Start,1 ,0, 0, 0, 0);
	$alter_Zeitpunkt = strtotime($Werte[0][0]);
	$alter_Wert = floatval($Werte[1][0]);
	$letzter_Wert = floatval($Werte[1][0]);
	if($alter_Wert > $Max) {$Max = $alter_Wert;}
	if($alter_Wert < $Min) {$Min = $alter_Wert;}
	try {
		if($Werte[0][0] < $Start and $step == 0) {
			$alter_Wert = $Werte[1][0];
			$Werte = lesen($Art,$Punkt, $Start, $Start,0 ,1, 0, 0, 0);
			$Endezeitpunkt  = strtotime($Werte[0][0]);
	 		$Endewert = floatval($Werte[1][0]);
			$Zeitdifferenz = $Endezeitpunkt - $alter_Zeitpunkt;
			$Steigung = ($Endewert-$alter_Wert)/$Zeitdifferenz;
			$alter_Wert = ($Startzeitpunkt - $alter_Zeitpunkt) * $Steigung + $alter_Wert;
			if($alter_Wert > $Max) {$Max = $alter_Wert;}
			if($alter_Wert < $Min) {$Min = $alter_Wert;}
		}
	} catch (Throwable $t) {}
	$Trend = "[[\"".$Start."\",".$alter_Wert."],";
	$Trend_Zeit = $Start.",";
	$Trend_Wert = $alter_Wert.",";
	//Werte aus den Archiven lesen
	$Werte = lesen($Art,$Punkt, $Start, $Ende,1 ,0, 0, 0, 0);
	$zaehler = 0;
	while ($zaehler < count($Werte[0])){
		if($step == 1) {$Trend = $Trend."[\"".$Werte[0][$zaehler]."\",".$letzter_Wert."],";}
		$Trend = $Trend."[\"".$Werte[0][$zaehler]."\",".floatval($Werte[1][$zaehler])."],";
		$Trend_Zeit = $Trend_Zeit.$Werte[0][$zaehler].",";
		$Trend_Wert = $Trend_Wert.floatval($Werte[1][$zaehler]).",";
		$letzter_Zeitstempel = $Werte[0][$zaehler];
		$letzter_Wert = floatval($Werte[1][$zaehler]);
		if($letzter_Wert > $Max) {$Max = $letzter_Wert;}
		if($letzter_Wert < $Min) {$Min = $letzter_Wert;}
		$zaehler = $zaehler + 1;
	}

	$akt_lesen = 1;

	try {
		if($step == 0) {
			$Werte = lesen($Art,$Punkt, $Ende, $Ende,0 ,1, 0, 0, 0);
			$Endezeitpunkt = strtotime($Ende);
			$hoechster_Zeitpunkt = strtotime($Werte[0][0]);
			$hoechster_Wert = floatval($Werte[1][0]);
			if($Werte[0][0] > $Ende) {
				$akt_lesen = 0;
				$Zeitdifferenz = $hoechster_Zeitpunkt - strtotime($letzter_Zeitstempel);
				if($Zeitdifferenz > 0) {
					$Steigung = ($hoechster_Wert-$letzter_Wert)/$Zeitdifferenz;
					$letzter_Wert = ($Endezeitpunkt - strtotime($letzter_Zeitstempel)) * $Steigung + $letzter_Wert;
				}
				$Trend = $Trend."[\"".$Werte[0][0]."\",".$letzter_Wert."],";
				$Trend_Zeit = $Trend_Zeit.$Werte[0][0].",";
				$Trend_Wert = $Trend_Wert.$letzter_Wert.",";
				if($letzter_Wert > $Max) {$Max = $letzter_Wert;}
				if($letzter_Wert < $Min) {$Min = $letzter_Wert;}
			}
		}
	} catch (Throwable $t) {}
	try {	
		if($akt_lesen == 1) {
			//ggf noch Werte aus der Tabelle akt lesen
			if($Art == "rV") {
				$query = "SELECT `Timestamp`, `Value` FROM `akt` Where `Point_ID` = ? AND `Timestamp` > ? AND `Timestamp` <= ? ORDER BY `Timestamp` ASC;";
				$stmt = mysqli_prepare($dbDH,$query);
				mysqli_stmt_bind_param($stmt, "iss", $Punkt, $letzter_Zeitstempel, $Ende);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				while ($line_Werte = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					$Trend = $Trend."[\"".$line_Werte["Timestamp"]."\",".floatval($line_Werte["Value"])."],";
					$Trend_Zeit = $Trend_Zeit.$line_Werte["Timestamp"].",";
					$Trend_Wert = $Trend_Wert.floatval($line_Werte["Value"]).",";
					if(floatval($line_Werte["Value"]) > $Max) {$Max = floatval($line_Werte["Value"]);}
					if(floatval($line_Werte["Value"]) < $Min) {$Min = floatval($line_Werte["Value"]);}
				}
				mysqli_stmt_close($stmt);
			}
		}
	} catch (Throwable $t) {}	

	mysqli_close($dbDH);
	$Trend = substr($Trend, 0, strlen($Trend)-1);
	$Trend = $Trend."]";
	$Differenz = $Max - $Min;
	if($Differenz > 1000) {
		$Min = floor($Min/100)*100;
		$Max = floor($Max/100)*100+100;
	} elseif($Differenz>10) {
		$Min = floor($Min/10)*10;
		$Max = floor($Max/10+1)*10;
	}  elseif($Differenz>1) {
		$Min = floor($Min);
		$Max = floor($Max+1);
	} elseif($Differenz>0.1) {
		$Min = floor($Min*10)/10;
		$Max = floor($Max*10+1)/10;
	}
	return array($Trend, $Min, $Max, $Trend_Zeit, $Trend_Wert);
}	

// schliessen der Verbindung
mysqli_close($db);
?>

<script>
Tagnr = "";

function Tagkonfiguration_oeffnen(Tag_ID) {
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
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '600 600',
		headerTitle: <?php echo "'".$Text[41]."'";?>,
		content: strReturn,
	});
}

function Eingabefeld_sichtbar_dialog(i) {
	Tagnr = i;
	jQuery.ajax({
		url: "Test_Tag_suchen.php?Suchtext=" + document.getElementById('Tagname' + Tagnr).value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'Tagsuche',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '500 275',
		content: strReturn,
  		contentOverflow: 'hidden',
	});
}

function uebertragen() {
	try {
		var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
		document.getElementById('Tagname' + Tagnr).value = Ergebnis[0] + Ergebnis[1];
	}
	catch (err) {}
}
</script>

</body>
</html>