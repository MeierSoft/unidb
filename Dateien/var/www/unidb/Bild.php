<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 0.8, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">
<script src="./scripts/jquery-3.6.0.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/gauge.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Bild.php");

$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `geloescht`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Hintergrundbild' as CHAR) as `Hintergrundbild`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR) as `Tags_Pfad`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Hintergrundfarbe = $line["Hintergrundfarbe"];
$Headererweiterung = html_entity_decode($line["Headererweiterung"]);
$JS = html_entity_decode($line["JS"]);
echo "\n".$Headererweiterung;
echo "\n<title>".$line["Bezeichnung"]."</title>\n";
echo "</head>\n";
if($Hintergrundfarbe > "") {
	echo "<body class='allgemein' style='Background-color: ".$Hintergrundfarbe.";'>\n";
}else {
	echo "<body class='allgemein'>";
}
include ('mobil.php');
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[19];
	exit;
}
if($mobil == 1) {
	echo "<font size='2'><form id='phpform' name='phpform' action='Bild.php' method='post' target='_self'>";
} else {
	echo "<font size='2'><form id='phpform' name='phpform' action='Bild.php' method='post' target='_self'><table><tr>";
}
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
if(isset($_POST['Intervall'])) {$_SESSION['Intervall']=$_POST['Intervall'];};
if (!isset($_SESSION['Intervall'])){
   $_SESSION['Intervall'] = 60;
   $Intervall=60;
} else {
	$Intervall=$_SESSION['Intervall'];
}

if (!isset($_SESSION['Zeitstempel'])){
   $_SESSION['Zeitstempel'] = $Text[1];
   $Zeitstempel=$_SESSION['Zeitstempel'];
}
if ($Zeitstempel==""){$Zeitstempel=$Text[1];}

if ($Spanne<1){$Spanne=86400;}
if ($Zeitstempel==$Text[1] or $Zeitstempel==""){
	$Zeitpunkt=time();
}else{
	$Zeitpunkt=strtotime($Zeitstempel);
}

if ($Spanne<1){$Spanne=1;}
If ($Spanne=='1 h'){
	$Spanne=3600;
}
If ($Spanne=='4 h'){
	$Spanne=14400;
}
If ($Spanne=='8 h'){
	$Spanne=28800;
}
If ($Spanne==$Text[12]){
	$Spanne=86400;
}
If ($Spanne==$Text[13]){
	$Spanne=172800;
}
If ($Spanne==$Text[14]){
	$Spanne=604800;
}
If ($Spanne==$Text[15]){
	$Spanne=1209600;
}
If ($Spanne==$Text[16]){
	$Spanne=2592000;
}
If ($Spanne==$Text[17]){
	$Spanne=31536000;
}
If ($schieben=='<'){
	$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt-$Spanne));
}
If ($schieben=='>'){
	$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt+$Spanne));	
}
If ($schieben==$Text[1]){
	$Zeitstempel=$Text[1];
}

if($mobil == 1) {
	include ("./Bild_navi_mobil.php");
} else {
	include ("./Bild_navi.php");
}

echo "<div id='DH_Bereich' style='font-size: 12px;'>\n";
$Inhalt = html_entity_decode($line["Inhalt"]);
$Inhalt = str_replace(" class=\"\"", "", $Inhalt);
$Inhalt = str_replace(" class=\"context-menu-two\"", "", $Inhalt);
$Inhalt=str_replace("verlinken=2", "verlinken=1", $Inhalt);
$Inhalt=str_replace("verlinken=\"2\"", "verlinken=1", $Inhalt);
$bed_Format = html_entity_decode($line["bed_Format"]);
$Bei_Start = html_entity_decode($line["Bei_Start"]);
$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);

echo $Inhalt;
echo "</div></div></font>\n";
mysqli_close($db);
?>
<script type='text/javascript'>
<?php
if($line['Hintergrundbild'] != "") {echo "	document.body.style.background = \"url('".$line['Hintergrundbild']."') no-repeat top 60px left 0px\";\n";}
//echo "	document.body.style.backgroundColor = '".$Hintergrundfarbe."';\n";
echo "	document.body.class='allgemein';\n";
$Intervall1=(int)$Intervall * 1000;
?>
DH_Bereich = document.getElementById('DH_Bereich');
DH_Elemente=[];
var Instr2 = [];
<?php echo "Intervall=".$Intervall1.";\n";?>
var T_Text = new Array;
var Bedingungen = {};
$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	//bedingte Formatierung einrichten
	try {Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
		i = 1;
		while(Bedingungen[i] != undefined) {
			Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
			i = i + 1;
		}	
	} catch (err) {}
	einrichten();
	lesen();
	Start();
<?php
	$Bei_Start = str_replace("§§§","'", $Bei_Start);
	$Bei_Start = str_replace("@@@",'"', $Bei_Start);
	echo "\n	function Start() {\n";
	echo $Bei_Start."\n";
	echo "	}\n";
	echo "	var refreshId = setInterval(function() {lesen();}, Intervall);\n";
	echo "});\n";
	$JS = str_replace("§§§","'", $JS);
	$JS = str_replace("@@@",'"', $JS);
	echo "\n".$JS."\n\n";
?>
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
	if (Tab == 2) {
		if (document.getElementById("zeit_schieben").style.display == "block") {
			document.getElementById("zeit_schieben").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("zeit_schieben").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("zeit_schieben").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 3) {
		if (document.getElementById("links").style.display == "block") {
			document.getElementById("links").style.display = "none"
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("links").style.display = "block"
			document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("links").style.display = "none";
		document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
	}
}
	
function Regler_schieben() {
	var Zeitspanne = parseFloat(document.getElementById("zeitspanne").value);
	var pos_alt = parseFloat(document.getElementById("pos_Regler_alt").value);
	var alter_Zeitpunkt = document.getElementById("Zeitstempel").value;
	if (alter_Zeitpunkt == T_Text[1]){
		var Zeitobjekt = new Date();
	} else {
		var Zeitobjekt = new Date(alter_Zeitpunkt);
	}
	alter_Zeitpunkt = Zeitobjekt.getTime() / 1000;
	var neuer_Zeitpunkt = alter_Zeitpunkt - Zeitspanne / 100 * (pos_alt - parseFloat(document.getElementById("schieberegler").value));
	var Zeitstempel = new Date(neuer_Zeitpunkt * 1000);
  	var Jahr = Zeitstempel.getFullYear(Zeitstempel).toString();
   var Monat = (Zeitstempel.getMonth(Zeitstempel) + 1).toString();
  	if (Monat.length == 1){Monat = "0" + Monat;}
   var Tag = Zeitstempel.getDate(Zeitstempel).toString();
  	if (Tag.length == 1){Tag = "0" + Tag;}
  	document.getElementById("Zeitstempel").value = Jahr + "-" + Monat + "-" + Tag + " " + Zeitstempel.toLocaleTimeString('de-DE');
  	document.getElementById("pos_Regler_alt").value = document.getElementById("schieberegler").value;
  	document.forms["Einstellungen"].submit();
}

function einrichten() {
	DH_Elemente=[];
	var ausgewaehlt;
	//Elementliste als Objekt - Array erstellen
	for (i=0;i<DH_Bereich.childNodes.length;i++) {
		ausgewaehlt=DH_Bereich.childNodes[i];
		einrichten2();
	}
	try {
		ausgewaehlt = Element_aus_Fenster();
		einrichten2();
	} catch (err) {}
	function einrichten2() {
		if (ausgewaehlt.nodeName=="DIV") {
			try{
				var feine_Teilung="";
				try {feine_Teilung=ausgewaehlt.attributes.feine_teilung.value;} catch (err) {}
				var verlinken="";
				try {verlinken=ausgewaehlt.attributes.verlinken.value;} catch (err) {}
				var Einheit="";
				try {Einheit=ausgewaehlt.attributes.einheit.value;} catch (err) {}
				var Ausdruck="";
				try {Ausdruck=ausgewaehlt.attributes.ausdruck.value;} catch (err) {}
				var Gruppe="";
				try {Gruppe=ausgewaehlt.attributes.gruppe.value;} catch (err) {}
				var aktualisieren="";
				try {aktualisieren=ausgewaehlt.attributes.aktualisieren.value;} catch (err) {}
				var Punkt = 0;
				try {Punkt=ausgewaehlt.attributes.tag_id.value;} catch (err) {}
				var Typ = ausgewaehlt.attributes.typ.value;
				var Min=0;
				var Max=0;
				var Teilung=0;
				var Einheit="";
				try {Einheit=ausgewaehlt.attributes.Einheit.value;} catch (err) {}
				var wert_anzeigen=""
				try {wert_anzeigen=ausgewaehlt.attributes.wert_anzeigen.value;} catch (err) {}
				var Breite=ausgewaehlt.style.width;
				Breite = Breite.substr(0,Breite.length-2);
				var Hoehe=ausgewaehlt.style.height;
				Hoehe = Hoehe.substr(0,Hoehe.length-2);
				var Zeitraum=0;
				try {Zeitraum=ausgewaehlt.attributes.zeitraum.value;} catch (err) {}
				try {Max=parseFloat(ausgewaehlt.attributes.max.value);} catch (err) {}
				try {Min=parseFloat(ausgewaehlt.attributes.min.value);} catch (err) {}
				try {Teilung = (Max-Min)/10;} catch (err) {}
				var Eigenschaften = {
					id: ausgewaehlt.id,
					Punkt: Punkt,
					Typ: Typ,
					Min: Min,
					Max: Max,
					Teilung: Teilung,
					Hoehe: Hoehe,
					Breite: Breite,
					Zeitraum: Zeitraum,
					aktualisieren: aktualisieren,
					Gruppe: Gruppe,
					Ausdruck: Ausdruck,
					Einheit: Einheit,
					verlinken: verlinken,
					feine_Teilung: feine_Teilung,
					Element: ausgewaehlt,
					wert_anzeigen: wert_anzeigen
				}
				DH_Elemente.push(Eigenschaften);
			} catch (err) {}
		} else {
			try {
				if (ausgewaehlt.attributes.onclick1.value.length > 0) {ausgewaehlt.setAttribute("onclick", ausgewaehlt.attributes.onclick1.value);}
			} catch (err) {}
		}
	} 
	//Elemente einrichten, wenn noetig
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].Typ == "Instrument") {
			var Instrument = DH_Elemente[i].Element.firstElementChild.contentDocument;
			Instrument.documentElement.attributes.height.value=DH_Elemente[i].Hoehe+"px";
			Instrument.documentElement.attributes.width.value=DH_Elemente[i].Breite+"px";
			//nicht zu viele Nachkommastellen
			if (parseInt(DH_Elemente[i].Teilung*1000)/1000 != 0) {
				var Hilfswert = 1000;
			} else {
				var Hilfswert = 10000;
			}
			DH_Elemente[i].Teilung=Math.round(DH_Elemente[i].Teilung*Hilfswert)/Hilfswert;
			DH_Elemente[i].Min=Math.round(DH_Elemente[i].Min*Hilfswert)/Hilfswert;
			for (x=0; x < 11; x++) {
				Skalenwert=Math.round((x*DH_Elemente[i].Teilung+DH_Elemente[i].Min)*Hilfswert)/Hilfswert;
				Instrument.getElementById("Beschriftung" + x.toString()).textContent = Skalenwert;
			}
			Instrument.getElementById("svg").attributes.height.value = DH_Elemente[i].Element.style.height;
			Instrument.getElementById("svg").attributes.width.value = DH_Elemente[i].Element.style.width;
			Instrument.getElementById("Einheit").textContent = DH_Elemente[i].Einheit;
			if (DH_Elemente[i].verlinken == "1"){
				Instrument.getElementById("Link").attributes["xlink:href"].value="./Trend.php?Tag_ID="+DH_Elemente[i].Punkt;
			} else{
				Instrument.getElementById("Link").removeAttribute("xlink:href");
			}
			if (DH_Elemente[i].feine_Teilung=="1") {
				Instrument.getElementById("feine_Teilung").style.display = "inline";
			} else {
				Instrument.getElementById("feine_Teilung").style.display = "none";
			}
		}
		if (DH_Elemente[i].Typ == "Instrument 2") {
			try {document.getElementById(DH_Elemente[i].id).removeChild(document.getElementById(DH_Elemente[i].id).firstChild);} catch (err) {}
			Instr2[DH_Elemente[i].id] = Gauge(
				document.getElementById(DH_Elemente[i].id), {
  					dialStartAngle: 180,
  			      dialEndAngle: 0,
  		   	   min: DH_Elemente[i].Min,
  		      	max: DH_Elemente[i].Max,
        			value: 0,
		   	  	viewBox: "0 0 100 57",
  	     			color: function(value) {return "#5ee432";}
				}
			);
			
			if (DH_Elemente[i].verlinken == "1"){
				tempDiv = document.getElementById(DH_Elemente[i].id);
				tempDiv.innerHTML = "<a class='gauge-container two' href='./Trend.php?Tag_ID="+DH_Elemente[i].Punkt + "'>" + tempDiv.firstChild.outerHTML + "</a>"; 
			}
		}
	}
}
				
function lesen() {
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].aktualisieren=="1"){		
			var strReturn = "";
			try {var ausdruck = encodeURIComponent(DH_Elemente[i].Ausdruck);} catch (err) {ausdruck = "";}
			var Zeitstempel = document.Einstellungen.Zeitstempel.value;
			if (Zeitstempel != T_Text[1]){
				try {
					var neuesDatum = new Date(document.Einstellungen.Zeitstempel.value);
					var Zeitpunkt = neuesDatum.getTime();
					Zeitpunkt = Math.round(Zeitpunkt/1000);
				} catch (err) {Zeitpunkt = "jetzt";}
			} else {
				var Zeitpunkt = "jetzt";
			}
			if (DH_Elemente[i].Typ == "Instrument 2") {
				var strURL = "Wert.php?Point_ID=" + DH_Elemente[i].Punkt + "&Einheit=" + DH_Elemente[i].Einheit + "&verlinken=" + DH_Elemente[i].verlinken + "&Typ=Tag&Zeitpunkt=" + Zeitpunkt + "&Ausdruck=" + ausdruck;
			} else {
				var strURL = "Wert.php?Tag_ID=" + DH_Elemente[i].Punkt + "&wert_anzeigen=" + DH_Elemente[i].wert_anzeigen + "&Einheit=" + DH_Elemente[i].Einheit + "&verlinken=" + DH_Elemente[i].verlinken + "&Typ=" + DH_Elemente[i].Typ + "&Zeitpunkt=" + Zeitpunkt + "&Breite=" + DH_Elemente[i].Breite + "&Hoehe=" + DH_Elemente[i].Hoehe + "&Zeitraum=" + DH_Elemente[i].Zeitraum + "&Gruppe=" + DH_Elemente[i].Gruppe + "&Ausdruck=" + ausdruck + "&min=" + DH_Elemente[i].Min + "&max=" + DH_Elemente[i].Max + "&Intervall=" + document.Einstellungen.Intervall.value;
			}
			jQuery.ajax({
				url: strURL,
				success: function (html) {
         	   strReturn = html;
	        	},
   	     	async: false
    		});
			strReturn=strReturn.split("@");
			if (DH_Elemente[i].Typ == "Instrument") {
				var wert = parseFloat(strReturn[0]);
				var Instrument = DH_Elemente[i].Element.firstChild.contentDocument.firstChild;
				try {Instrument.getElementById("Link").attributes["xlink:title"].value=strReturn[1];} catch (err) {}
				Instrument.getElementById("Wert").innerHTML = wert;
				if (wert < DH_Elemente[i].Min) {wert = parseFloat(DH_Elemente[i].Min);}
				if (wert > DH_Elemente[i].Max) {wert = parseFloat(DH_Elemente[i].Max);}
				var Winkel = ((wert-DH_Elemente[i].Min)/DH_Elemente[i].Teilung)*25;
				Instrument.getElementById("Zeiger").attributes.transform.value = "rotate(" + Winkel.toString() + " 100 100)";
			} else {
				if (DH_Elemente[i].Typ == "Instrument 2") {
					if (strReturn[0].substr(0,2) == "<a") {
						var wert = parseFloat(strReturn[0].substr(strReturn[0].indexOf(">") + 1,strReturn[0].length));
					} else {
						var wert = parseFloat(strReturn[0]);
					}
					document.getElementById(DH_Elemente[i].id).firstChild.firstChild.childNodes[1].firstChild.textContent = wert.toString()
				} else {
					document.getElementById(DH_Elemente[i].id).innerHTML = strReturn[0];
				}
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value=strReturn[1];} catch (err) {}
			}
		}
	}
	bed_Formatierung();
}


function Zeitschieben(Richtung){
   var Zeitstempeltmp = document.Einstellungen.Zeitstempel.value;
   var Zeitspanne = document.Einstellungen.Spanne.value;
   if (Zeitspanne == "1 h"){Zeitspanne=3600000;}
   if (Zeitspanne == "4 h"){Zeitspanne=14400000;}
   if (Zeitspanne == "8 h"){Zeitspanne=28800000;}
   if (Zeitspanne == T_Text[12]){Zeitspanne=86400000;}
   if (Zeitspanne == T_Text[13]){Zeitspanne=172800000;}
   if (Zeitspanne == T_Text[14]){Zeitspanne=604800000;}
   if (Zeitspanne == T_Text[15]){Zeitspanne=1209600000;}
   if (Zeitspanne == T_Text[16]){Zeitspanne=2592000000;}
   if (Zeitspanne == T_Text[17]){Zeitspanne=31536000000;}
   if (Zeitstempeltmp==T_Text[1]){
       var Zeitobjekt = new Date();
       Zeitpunkt = Zeitobjekt.getTime();
   } else {
       var Zeitobjekt = new Date(Zeitstempeltmp);
       var Jahr = Zeitstempeltmp.substr(0,4);
       var Monat = Zeitstempeltmp.substr(5,2);
       var Tag = Zeitstempeltmp.substr(5,2);
       var Stunde = Zeitstempeltmp.substr(11,2);
       var Minute = Zeitstempeltmp.substr(14,2);
       var Sekunde = Zeitstempeltmp.substr(17,2);
       Zeitpunkt = Zeitobjekt.getTime(Jahr, Monat, Tag, Stunde, Minute, Sekunde, "0");
   }
	if (Richtung == T_Text[1]){
		var Zeitobjekt = new Date();
		Zeitpunkt = Zeitobjekt.getTime();
		document.Einstellungen.Zeitstempel.value = T_Text[1];
	} else {
		if (Richtung == "<"){Zeitpunkt = Zeitpunkt - Zeitspanne;}
		if (Richtung == ">"){Zeitpunkt = Zeitpunkt + Zeitspanne;}
	   Zeitstempeltmp = new Date(Zeitpunkt);
   	var Jahr = Zeitstempeltmp.getFullYear(Zeitstempeltmp).toString();
	   var Monat = (Zeitstempeltmp.getMonth(Zeitstempeltmp) + 1).toString();
   	if (Monat.length == 1){Monat = "0" + Monat;}
	   var Tag = Zeitstempeltmp.getDate(Zeitstempeltmp).toString();
   	if (Tag.length == 1){Tag = "0" + Tag;}
   	document.Einstellungen.Zeitstempel.value= Jahr + "-" + Monat + "-" + Tag + " " + Zeitstempeltmp.toLocaleTimeString('de-DE');
   }
   lesen();
}

function Elem(Elementname) {
	Bereich = document.getElementById("DH_Bereich");
	for (z = 0; z < Bereich.childNodes.length; z++) {
		try {
			if (Bereich.childNodes[z].attributes.elementname.value == Elementname) {return Bereich.childNodes[z].id;}
		} catch (err) {}
	}
	return "Error";
}

function Elem_Wert(Elementname) {
	try {
		Element = Elem(Elementname);
		Wert = parseFloat(document.getElementById(Element).value);
		if (isNaN(Wert) == true) {Wert = document.getElementById(Element).value;}
		if (Wert == undefined) {
			if (document.getElementById(Element).innerHTML.substr(0,2) == "<a") {
				Wert = parseFloat(document.getElementById(Element).firstChild.innerHTML);
			} else {
				Wert = parseFloat(document.getElementById(Element).innerHTML);
			}
			if (isNaN(Wert) == true) {Wert = document.getElementById(Element).innerHTML;}
		}
		return Wert;
	} catch (err) {}
	return "Error";
}

function DBQ(DB,Funktion,Feld,Tabelle,Bedingung) {
	strReturn = "Error";
	jQuery.ajax({
		url: "DBQ.php",
		success: function (html) {
   		strReturn = html;
		},
		type: 'POST',
		data: {DB: DB,Fun: Funktion,Fel: Feld, Tab: Tabelle, Bed: Bedingung},
		async: false
	});
	return strReturn
}

function bed_Formatierung() {
	i = 1;
	while (Bedingungen[i] != undefined) {
		Ziel = document.getElementById(Elem(Bedingungen[i].Element));
		if (Bedingungen[i].orig_Stil == "") {
			Bedingungen[i].orig_Stil = Ziel.attributes.style.cloneNode();
			Bedingungen[i].orig_class = Ziel.attributes.className;
		}
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
		Wert = Function('return ' + Bedingungen[i].Bedingung)();
		if (Wert == true) {
			if (Ziel.firstChild != undefined) {
				Ziel.firstChild.style = Ziel.style;				
				Ziel = Ziel.firstChild;
			}
			Ziel.className = Bedingungen[i].class;
			Ziel.style.fontFamily = Bedingungen[i]["font-family"];
			Ziel.style.fontStyle = Bedingungen[i]["font-style"];
			Ziel.style.color = Bedingungen[i]["color"];
			Ziel.style.fontSize = Bedingungen[i]["font-size"];
			Ziel.style.fontWeight = Bedingungen[i]["font-weight"];
			Ziel.style.display = Bedingungen[i]["sichtbar"];
			Ziel.style.borderColor = Bedingungen[i]["Rahmenfarbe"];
			Ziel.style.borderStyle = Bedingungen[i]["border-style"];
			Ziel.style.borderWidth = Bedingungen[i]["Rahmenbreite"];
			Ziel.style.backgroundColor = Bedingungen[i]["background-color"];
		} else {
			Ziel.setAttribute("style",Bedingungen[i].orig_Stil.value);
			Ziel.attributes.className = Bedingungen[i].orig_class;
		}
		i = i + 1;
	}	
}
</script>
</body>
</html>