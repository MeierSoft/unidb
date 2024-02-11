<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">
<script src="./scripts/jquery-3.6.0.js"></script>
<script src="./scripts/jquery-ui.js"></script>
<link rel="stylesheet" href="./css/jquery-ui.css">
<link href="./css/Armaturenbrett.css" rel="stylesheet">
<link href="./css/Armaturenbrett.css" rel="stylesheet">
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/gauge.js"></script>
<style type="text/css">
a {
	text-decoration: none;
}
</style>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Text = Translate("Bild.php");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";


$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, `geloescht`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt`, column_get(`Inhalt`, 'Spalten' as CHAR) as `Spalten`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
$Hintergrundfarbe = $line["Hintergrundfarbe"];
$Hintergr = "";
if($Hintergrundfarbe > "") {$Hintergr = "style='Background-color: ".$Hintergrundfarbe.";'";}
echo "<title>".$line["Bezeichnung"]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein' ".$Hintergr.">\n";
include ('mobil.php');
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[19];
	exit;
}
if($mobil == 1) {
	echo "<font size='2'><form id='phpform' name='phpform' action='Armaturenbrett.php' method='post' target='_self'>";
} else {
	echo "<font size='2'><form id='phpform' name='phpform' action='Armaturenbrett.php' method='post' target='Hauptrahmen'>";
}

echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
echo "<input type='hidden' id='gel' name='geloescht' value = '".$line["geloescht"]."'>\n";
echo "<input type='hidden' id='hintergrundfarbe' name='Hintergrundfarbe' value = '".$Hintergrundfarbe."'>\n";
echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
echo "<table><tr>";

if(isset($_POST['Intervall'])) {$_SESSION['Intervall']=$_POST['Intervall'];};
if (!isset($_SESSION['Intervall'])){
   $_SESSION['Intervall'] = 60;
   $Intervall=60;
} else {
	$Intervall=$_SESSION['Intervall'];
}

echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('5');\">".$Text[6]."</a></td>\n";

if($anzeigen == 1) {
	if ($mobil==1){
		if($line["geloescht"] != 1) {echo "<td><a href='Armaturenbrett_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[9]."</a></td>";}
	} else {
		if($line["geloescht"] != 1) {echo "<td><a href='Armaturenbrett_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[9]."</a></td>";}
	}
	echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[8]."</a></td>";
	echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[10]."</a></td>";
	if ($mobil==1){
		echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[7]."</a></td>";
		if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";}
	} else {
		echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[7]."</a></td>";
		if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";}
	}
}

echo "</form>\n";
echo "<form id='Einstellungen' name='Einstellungen' action='./Armaturenbrett.php' method='post' target='_self'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
$bed_Format = html_entity_decode($line["bed_Format"]);
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "<td>".$Text[5].":&nbsp;</td><td align='center'><input class='Text_Element' type='text' id='Intervall' name='Intervall' size='4' value= '".$Intervall."' onchange='var refreshId = setInterval(function() {lesen();}, document.Einstellungen.Intervall.value);'></td>\n";
echo "</tr></table>\n";
echo "</form></font>\n";

echo "<div id='DH_Bereich' style='font-size: 12px;'>\n";
$Inhalt = html_entity_decode($line["Inhalt"]);
$Inhalt = str_replace(" class=\"\"", "", $Inhalt);
$Inhalt = str_replace(" class=\"context-menu-two\"", "", $Inhalt);
$Inhalt = str_replace("verlinken=2", "verlinken=1", $Inhalt);
$Inhalt = str_replace("verlinken=\"2\"", "verlinken=1", $Inhalt);
$Inhalt = str_replace(" onclick=\"auswaehlen(this,1);\"", "", $Inhalt);
$Inhalt = str_replace(" ontouchend=\"auswaehlen(this,1);\"", "", $Inhalt);
echo $Inhalt;
echo "</div></div></font>\n";
mysqli_close($db);
?>
<script type='text/javascript'>
<?php
echo "	document.body.style.backgroundColor = '".$Hintergrundfarbe."';\n";
echo "	document.body.class='Text_normal';\n";
$Intervall1=(int)$Intervall * 1000;
?>
DH_Bereich = document.getElementById('DH_Bereich');
DH_Elemente=[];
<?php echo "Intervall=".$Intervall1.";\n";?>
var T_Text = new Array;
var Bedingungen = {};
var Instr2 = [];
$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	//bedingte Formatierung einrichten
	try {Bedingungen = JSON.parse(document.getElementById("bed_Format").value);} catch (err) {}
	einrichten();
	var refreshId = setInterval(function() {lesen();}, Intervall);
});

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
	
function einrichten() {
	DH_Elemente=[];
	var ausgewaehlt;
	//Elementliste als Objekt - Array erstellen
	for (i = 0;i < document.getElementsByClassName("portlet-content").length;i++) {
		ausgewaehlt = document.getElementsByClassName("portlet-content")[i].firstChild.firstChild;
		einrichten2();
	}
	function einrichten2() {
		if (ausgewaehlt.nodeName=="DIV") {
			try {
				var verlinken="";
				try {verlinken=ausgewaehlt.attributes.verlinken.value;} catch (err) {}
				var feine_Teilung="";
				try {feine_Teilung=ausgewaehlt.attributes.feine_teilung.value;} catch (err) {}
				var Einheit="";
				try {Einheit=ausgewaehlt.attributes.einheit.value;} catch (err) {}
				var Ausdruck="";
				try {Ausdruck=ausgewaehlt.attributes.ausdruck.value;} catch (err) {}
				var Gruppe="";
				try {Gruppe=ausgewaehlt.attributes.gruppe.value;} catch (err) {}
				var aktualisieren="";
				try {aktualisieren=ausgewaehlt.attributes.aktualisieren.value;} catch (err) {}
				var Punkt = 0;
				try {Punkt = ausgewaehlt.attributes.point_id.value;} catch (err) {}
				var Tag_ID = 0;
				try {Tag_ID = ausgewaehlt.attributes.tag_id.value;} catch (err) {}
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
					Tag_ID: Tag_ID,
					Typ: Typ,
					Min: Min,
					Max: Max,
					Teilung: Teilung,
					Hoehe: Hoehe,
					Breite: Breite,
					Zeitraum: Zeitraum,
					Einheit: Einheit,
					aktualisieren: aktualisieren,
					Gruppe: Gruppe,
					Ausdruck: Ausdruck,
					Einheit: Einheit,
					feine_Teilung: feine_Teilung,
					Element: ausgewaehlt,
					verlinken: verlinken,
					wert_anzeigen: wert_anzeigen
				}
				if (Eigenschaften.Typ == "Instrument 2") {
					try {document.getElementById(Eigenschaften.id).removeChild(document.getElementById(Eigenschaften.id).firstChild);} catch (err) {}
					Instr2[Eigenschaften.id] = Gauge(
						document.getElementById(Eigenschaften.id), {
   	  					dialStartAngle: 180,
	   			      dialEndAngle: 0,
	   		   	   min: Eigenschaften.Min,
	   		      	max: Eigenschaften.Max,
	         			value: 0,
				   	  	viewBox: "0 0 100 57",
	   	     			color: function(value) {return "#5ee432";}
						}
					); 
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
				Link = document.createElement("a");
				Link.setAttribute("href","./Trend.php?Tag_ID=" + DH_Elemente[i].Punkt);
				Link.setAttribute("target", "_blank");
				Link.setAttribute("id","Link " + DH_Elemente[i].id);
				Mutter = document.getElementById(DH_Elemente[i].id).parentElement;
				Link.appendChild(document.getElementById(DH_Elemente[i].id))
				Mutter.appendChild(Link);
			}
		}
	}
	lesen();
}



function lesen() {
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].aktualisieren=="1"){		
			var strReturn = "";
			try {var ausdruck = encodeURIComponent(DH_Elemente[i].Ausdruck);} catch (err) {ausdruck = "";}
			var Zeitpunkt = "jetzt";
			if (DH_Elemente[i].Typ == "Instrument 2") {
				var strURL = "Wert.php?Point_ID=" + DH_Elemente[i].Punkt + "&Tag_ID=" + DH_Elemente[i].Tag_ID + "&verlinken="+DH_Elemente[i].verlinken + "&Einheit=" + DH_Elemente[i].Einheit + "&Typ=Tag&Zeitpunkt=jetzt&Ausdruck="+ausdruck;
			} else {
				var strURL = "Wert.php?Point_ID=" + DH_Elemente[i].Punkt + "&Tag_ID=" + DH_Elemente[i].Tag_ID + "&Einheit=" + DH_Elemente[i].Einheit + "&wert_anzeigen=" + DH_Elemente[i].wert_anzeigen + "&Typ=" + DH_Elemente[i].Typ + "&Zeitpunkt=jetzt&Breite=" + DH_Elemente[i].Breite + "&Hoehe=" + DH_Elemente[i].Hoehe + "&verlinken="+DH_Elemente[i].verlinken + "&Zeitraum="+DH_Elemente[i].Zeitraum + "&Gruppe=" + DH_Elemente[i].Gruppe+"&Ausdruck=" + ausdruck + "&min=" + DH_Elemente[i].Min + "&max=" + DH_Elemente[i].Max;
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
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value = strReturn[1];} catch (err) {}
				var wert = parseFloat(strReturn[0]);
				var Instrument = DH_Elemente[i].Element.firstChild.contentDocument.firstChild;
				Instrument.getElementById("Wert").innerHTML = wert;
				if (wert < DH_Elemente[i].Min) {wert = parseFloat(DH_Elemente[i].Min);}
				if (wert > DH_Elemente[i].Max) {wert = parseFloat(DH_Elemente[i].Max);}
				var Winkel = ((wert-DH_Elemente[i].Min)/DH_Elemente[i].Teilung)*25;
				Instrument.getElementById("Zeiger").attributes.transform.value = "rotate(" + Winkel.toString() + " 100 100)";
			} else {
				if (DH_Elemente[i].Typ == "Instrument 2") {
					if (strReturn[0].substr(0,2) == "<a") {
						var wert = parseFloat(strReturn[0].substr(strReturn[0].indexOf(">") + 1, strReturn[0].length));
					} else {
						var wert = parseFloat(strReturn[0]);
					}
					//document.getElementById(DH_Elemente[8].id).firstChild.childNodes[1].textContent = wert.toString()
					Instr2[DH_Elemente[i].id].setValue(wert);
				} else {
					document.getElementById(DH_Elemente[i].id).innerHTML = strReturn[0];
				}
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value = strReturn[1];} catch (err) {}
			}
		}
	}
	bed_Formatierung();
}

function Elem(Elementname) {
	for (z = 0; z < document.getElementsByClassName("element").length; z++) {
		try {
			if (document.getElementById(document.getElementsByClassName("element")[z].id).attributes.elementname.value == Elementname) {return document.getElementsByClassName("element")[z].id;}
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

$(function() {
	$(".column").sortable({
     	connectWith: ".column",
     	handle: ".portlet-header",
     	cancel: ".portlet-toggle",
     	placeholder: "portlet-placeholder ui-corner-all"
	});
	$(".portlet")
     	.addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
     	.find(".portlet-header")
		.addClass( "ui-widget-header ui-corner-all" )
		.prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");
	$(".portlet-toggle").on("click", function() {
     	var icon = $( this );
     	if (this.classList.contains("ui-icon-minusthick")) {
     		this.parentElement.parentElement.setAttribute("orig_hoehe",this.parentElement.parentElement.style.height);
     		this.parentElement.parentElement.style.height = "28px";
     	} else {
     		this.parentElement.parentElement.style.height = this.parentElement.parentElement.attributes["orig_hoehe"].value;
     	}
     	icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
     	icon.closest(".portlet").find(".portlet-content").toggle();
	});
});
$(function() {
	$(".resizable").resizable();
});
</script>
</body>
</html>