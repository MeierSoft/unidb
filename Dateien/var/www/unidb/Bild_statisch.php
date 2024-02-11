<!DOCTYPE html>
<html><head>
<meta name='viewport' content='width=device-width, initial-scale = 1.0, maximum-scale=5.0' />
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<meta http-equiv="content-style-type" content="text/css">
<script type='text/javascript' src='./jquery-1.11.2.min.js'></script>
<script type="text/javascript" src="./scripts/gauge.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/default.css' rel='stylesheet'>";
include './mobil.php';
include './conf_unidb.php';
$Text = Translate("Bild.php");

echo "<title>Bild</title>\n";
echo "</head>\n";
$Intervall = 60;
if ($Zeitstempel==""){$Zeitstempel="jetzt";}
//Bild einlesen
$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `geloescht`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Hintergrundbild' as CHAR) as `Hintergrundbild`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR) as `Tags_Pfad`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line_Bild = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Hintergrundfarbe = $line_Bild["Hintergrundfarbe"];
if($Hintergrundfarbe > "") {
	echo "<body class='allgemein' style='Background-color: ".$Hintergrundfarbe.";'>\n";
}else {
	echo "<body class='allgemein'>";
}
echo "<form id='Einstellungen' name='Einstellungen' action='./Bild.php' method='post' target='_self'>\n";
echo "<input id='Baum_ID' type='hidden' value='".$Baum_ID."'>\n";
echo "<input id='Zeitstempel' name='Zeitstempel' type='hidden' value='".$Zeitstempel."'>\n";
echo "<input id='Intervall' name='Intervall' type='hidden' value='".$Intervall."'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "</Form>\n";


echo "<div id='DH_Bereich' style='font-size: 12px; top: -60px; position: absolute; Background-color: ".$line_Bild["Hintergrundfarbe"].";'>\n";
echo html_entity_decode($line_Bild["Inhalt"]);
echo "</div>\n";
mysqli_close($db);
?>
<script type='text/javascript'>
<?php
echo "	document.body.style.background = \"url('".$line_Bild['Hintergrundbild']."') no-repeat top 0px left 0px\";\n";
echo "	document.body.style.backgroundColor = '".$Hintergrundfarbe."';\n";
$Intervall1=(int)$Intervall * 1000;
?>
document.body.class='Text_normal';
var T_Text = new Array;
T_Text = JSON.parse(document.getElementById("translation").value);
DH_Bereich = document.getElementById('DH_Bereich');
DH_Elemente=[];
var Bedingungen = {};
var Instr2 = [];
$(window).on('load',function() {
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
<?php
echo "	var refreshId = setInterval(function() {lesen();}, ".$Intervall1.");\n";
echo "});\n";
?>

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
				var strURL = "Wert.php?Point_ID=" + DH_Elemente[i].Punkt + "&Einheit=" + DH_Elemente[i].Einheit + "&verlinken="+DH_Elemente[i].verlinken + "&Typ=Tag&Zeitpunkt="+Zeitpunkt+"&Ausdruck="+ausdruck;
			} else {
				var strURL = "Wert.php?Tag_ID="+DH_Elemente[i].Punkt+"&wert_anzeigen="+DH_Elemente[i].wert_anzeigen+"&Einheit="+DH_Elemente[i].Einheit+"&verlinken="+DH_Elemente[i].verlinken+"&Typ="+DH_Elemente[i].Typ+"&Zeitpunkt="+Zeitpunkt+"&Breite="+DH_Elemente[i].Breite+"&Hoehe="+DH_Elemente[i].Hoehe+"&Zeitraum="+DH_Elemente[i].Zeitraum+"&Gruppe="+DH_Elemente[i].Gruppe+"&Ausdruck="+ausdruck+"&min="+DH_Elemente[i].Min+"&max="+DH_Elemente[i].Max+"&Intervall="+document.Einstellungen.Intervall.value;
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
					document.getElementById(DH_Elemente[i].id).innerHTML=strReturn[0];
				}
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value=strReturn[1];} catch (err) {}
			}
		}
	}
	bed_Formatierung();
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