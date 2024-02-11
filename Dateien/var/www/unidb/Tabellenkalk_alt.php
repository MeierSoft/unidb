<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<title>Tabellen</title>
<script src="./jquery.min.js"></script>
<link rel="stylesheet" href="./JS_Tab/dist/jexcel.css" type="text/css" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./JS_Tab/dist/jexcel.js"></script>
<script src="./JS_Tab/dist/jsuites.js"></script>
<link rel="stylesheet" href="./JS_Tab/dist/jsuites.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./JS_Tab/dist/icons.css?family=Open+Sans|Roboto|Dosis|Material+Icons">
<style>
body
 {font-family:verdana,helvetica,sans-serif;font-size:small;}
.testclass {border:2px dotted red;}
.testclass2 {background-image:url(images/sc-logo.gif);}
.smaller {font-size:smaller;}
.hide {display:none;}
</style>
<?php echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>"; ?>
</head>
<body class='allgemein' onresize="doresize();">
<form id="Formular" action='./Baum2.php' method='post' target='Baum' name='Formular'>
<table width="100%"><tr>
<td><input type="submit" name="Aktion" value="Tabellenkalk_speichern" onclick="abspeichern();" class="smaller"></td>
<?php
	include('Sitzung.php');
	if ($Baum_ID > 0){
		$query = "SELECT `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Blatt' as Char) as `Blatt`, column_get(`Inhalt`, 'config' as Char) as `config`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten` FROM `Baum` where `Baum_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "i", $Baum_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
	}
	echo "<input type= 'hidden' name='Baum_ID' value='".$Baum_ID."'>";
	echo "<input type= 'hidden' name='Eltern_ID' value='".$Eltern_ID."'>";
	echo "<textarea id='savestr' style='display:none;' name ='Blatt'>".$line["Blatt"]."</textarea>";
	echo "<textarea id='config' style='display:none;' name ='config'>".$line["config"]."</textarea>";
	echo "<textarea id='Spalten' style='display:none;' name ='Spalten'>".$line["Spalten"]."</textarea>";
	echo "<td>Dokument: <input type='text' name='Bezeichnung' value='".$line["Bezeichnung"]."'></td><td><a href='verschieben.php?Baum_ID=".$Baum_ID."'>verschieben</a></td><td><a href='./kopieren.php?original=".$Baum_ID."' target='_self'>kopieren</a></td><td><a href='loeschen.php?Baum_ID=".$Baum_ID."' target='Hauptrahmen'>l&ouml;schen</a></td>";
	echo "<td><input type='button' value='neu berechnen' class='smaller' onclick='neu_berechnen();' class='smaller'></td>";
	echo "<td><input type='button' value='html speichern' class='smaller' onclick='html_speichern();' class='smaller'></td>";
	echo "<td><input type='button' value='Werte abrufen' class='smaller' onclick='Werte_Dialog();' class='smaller'></td></tr></table>";
	mysqli_close($db);
?>
</form>

<div id="spreadsheet"></div>
<script>
$(document).ready(function() {
	//neu_berechnen();
});
var Blatt = [];
try{
	Blatt = JSON.parse(document.getElementById("savestr").innerHTML);
	}	catch (err) {}
var Aussehen = [];
try{
	Aussehen = JSON.parse(document.getElementById("config").innerHTML);
	}	catch (err) {}
var Spaltenkonfig = [];
try{
	Spaltenkonfig = JSON.parse(document.getElementById("Spalten").innerHTML);
	}	catch (err) {}

var Zeile = -1;
var Spalte = -1;

var selectionActive = function(instance, x1, y1, x2, y2, origin) {
    var cellName1 = jexcel.getColumnNameFromId([x1, y1]);
    var cellName2 = jexcel.getColumnNameFromId([x2, y2]);
    Spalte = parseInt(x1);
    Zeile = parseInt(y1);
    ZelleName = cellName1;
    return cellName1 + "," + cellName2;
}

var beforeChange = function(instance, cell, x, y, value) {
    var cellName = jexcel.getColumnNameFromId([x,y]);
    Spalte = parseInt(x);
    Zeile = parseInt(y);
    ZelleName = cellName;
}

var akt_Wert = function(Tagname,Zeitstempel,Einheit,Richtung) {
	var strReturn;
	if (Zeile == -1 || Spalte == -1){
		var gefunden = "";
		//Koordinaten der aktuellen Zelle finden
		var Suchtext = "=akt_Wert('" + Tagname + "'," + Zeitstempel.toString() + "," + Einheit.toString() + ",'" + Richtung + "')";
		while (gefunden == "" && Zeile <= Tabelle.jexcel.options.data.length) {
			Spalte = 0;
			while (gefunden == "" && Spalte <= Tabelle.jexcel.options.data[Zeile].length) {
				if (Tabelle.jexcel.options.data[Zeile][Spalte] == Suchtext) {
					gefunden = "gefunden";
				}
				Spalte = Spalte + 1;
			}
			Zeile = Zeile + 1;
		}
		Zeile = Zeile - 1;
		Spalte = Spalte - 1;
		//Ende Koordinaten suchen
	}
	var Tagname = Tabelle.jexcel.getCell(Tagname).innerHTML;
	jQuery.ajax({
		url: "akt_Wert.php?Tagname=" + Tagname,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefinied") {Richtung = "waagerecht";}
  	Ergebnis = strReturn.split(",");
  	if (Zeitstempel == 1){Attribute_Schreiben(Ergebnis[1]);}
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[2]);}
   return Ergebnis[0];
   
   function Attribute_Schreiben(Wert) {
   	if (Richtung == "senkrecht") {Zeile = Zeile + 1;}
  		if (Richtung == "waagerecht") {Spalte = Spalte + 1;}
  		if (Zeile >= Tabelle.jexcel.rows.length) {Tabelle.jexcel.insertRow();}
  		if (Spalte >= Tabelle.jexcel.colgroup.length) {Tabelle.jexcel.insertColumn();}
  		try {
  			Tabelle.jexcel.setValueFromCoords(Spalte,Zeile,Wert);
  		}
  		catch (err) {}
  	}
}

var Archivwerte = function(Tagname,Zeitstempel,von,bis,Einheit,vt,vt_interpol,unixzeit,typ,Richtung) {
	var strReturn;
	if (Zeile == -1 || Spalte == -1){
		var gefunden = "";
		//Koordinaten der aktuellen Zelle finden
		var Suchtext = "=Archivwerte('" + Tagname + "'," + Zeitstempel.toString() + "," + Einheit.toString() + ",'" + Richtung + "')";
		while (gefunden == "" && Zeile <= Tabelle.jexcel.options.data.length) {
			Spalte = 0;
			while (gefunden == "" && Spalte <= Tabelle.jexcel.options.data[Zeile].length) {
				if (Tabelle.jexcel.options.data[Zeile][Spalte] == Suchtext) {
					gefunden = "gefunden";
				}
				Spalte = Spalte + 1;
			}
			Zeile = Zeile + 1;
		}
		Zeile = Zeile - 1;
		Spalte = Spalte - 1;
		//Ende Koordinaten suchen
	}
	var Tagname = Tabelle.jexcel.getCell(Tagname).innerHTML;
	jQuery.ajax({
		url: "DH_Archivwerte.php?Tagname=" + Tagname + "&von=" + von + "&bis=" + bis + "&typ=" + typ,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	var WertePaar;
	var S = Spalte;
	var Z = Zeile;
	var S1 = S;
	var Z1 = Z;
	if (Zeitstempel == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel.setValueFromCoords(S1,Z,"Zeitstempel");
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel.setValueFromCoords(S,Z1,"Zeitstempel");
		}
	}
	if (vt == 1){
		if (Richtung == "senkrecht") {		
			S1 = S1 + 1;
			Tabelle.jexcel.setValueFromCoords(S1,Z,"vt");
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel.setValueFromCoords(S,Z1,"vt");
		}
	}
	if (vt_interpol == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel.setValueFromCoords(S1,Z,"vt interpoliert");
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel.setValueFromCoords(S,Z1,"vt interpoliert");
		}
	}
	if (unixzeit == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel.setValueFromCoords(S1,Z,"Unix Zeitstempel");
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel.setValueFromCoords(S,Z1,"Unix Zeitstempel");
		}
	}
  	Ergebnis = strReturn.split(";");
	Einheit_Ergebnis = Ergebnis[0];
	var Saetze = Ergebnis.length;
	for (Teil=1; Teil < Saetze; Teil++ ) {
  		if (Richtung == "senkrecht") {Z = Z + 1;}
		if (Richtung == "waagerecht") {S = S + 1;}
		S1 = S;
		Z1 = Z;
		if (Z >= Tabelle.jexcel.rows.length) {Tabelle.jexcel.insertRow();}
		if (S >= Tabelle.jexcel.colgroup.length) {Tabelle.jexcel.insertColumn();}
		try {
			WertePaar = Ergebnis[Teil].split(",");
			Tabelle.jexcel.setValueFromCoords(S,Z,WertePaar[1]);
			if (Zeitstempel == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel.setValueFromCoords(S1,Z,WertePaar[0]);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel.setValueFromCoords(S,Z1,WertePaar[0]);
				}
			}
			if (vt == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel.setValueFromCoords(S1,Z,WertePaar[3]);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel.setValueFromCoords(S,Z1,WertePaar[3]);
				}
			}
			if (vt_interpol == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel.setValueFromCoords(S1,Z,WertePaar[4]);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel.setValueFromCoords(S,Z1,WertePaar[4]);
				}
			}
			if (unixzeit == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel.setValueFromCoords(S1,Z,WertePaar[2]);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel.setValueFromCoords(S,Z1,WertePaar[2]);
				}
			}
		}
		catch (err) {}
	}
	if (Einheit == 1){return typ + " in " + Einheit_Ergebnis;
  	} else {
  		return "";
  	}
}


function html_speichern(){
	var htmlInhalt = "<html><table>" + document.getElementById('spreadsheet').childNodes[1].firstChild.childNodes[2].innerHTML + "</table></html>";
	htmlInhalt = htmlInhalt.replace(/<tr.*?>/g, "<tr>")
	htmlInhalt = htmlInhalt.replace(/<td.*?style/g, "<td style")
	var pom = document.createElement('a');
	var blob = new Blob([htmlInhalt], {type: 'text/html;charset=ISO-8859-1;'});
  	var url = URL.createObjectURL(blob);
	pom.href = url;
	pom.setAttribute('download', 'Kalk.html');
	document.body.appendChild(pom);
	pom.click();
	pom.parentNode.removeChild(pom);
}

function abspeichern() {
	var data = Tabelle.jexcel.getData();
	$('#savestr').val(JSON.stringify(data));
	document.getElementById('config').value = JSON.stringify(Tabelle.jexcel.getConfig().style);
	document.getElementById('Spalten').value = JSON.stringify(Tabelle.jexcel.getConfig().columns);
	document.Formular.submit();
}

function Dialog_ausblenden() {
	var Formular = document.getElementById("WerteDialog");
	if (Formular.funktion.value == "aktueller Wert"){
		document.getElementById("1").style.visibility = "hidden"; 
		document.getElementById("2").style.visibility = "hidden";
		document.getElementById("3").style.visibility = "hidden"; 
		document.getElementById("4").style.visibility = "hidden";
	} else {
		document.getElementById("1").style.visibility = "visible";
		document.getElementById("2").style.visibility = "visible";
		document.getElementById("3").style.visibility = "visible"; 
		document.getElementById("4").style.visibility = "visible";
	}
}

function Werte_Dialog(){
	var Richtung = "waagerecht";
	var Startzelle = "";
	var Endezelle = "";
	if (ISBLANK(Tabelle.jexcel.selectedContainer) == false) {
  		var Bereich = Tabelle.jexcel.selectedContainer;
  		var Ausgabebereich = Tabelle.jexcel.getHeader(Tabelle.jexcel.selectedContainer[0]) + (Tabelle.jexcel.selectedContainer[1] + 1).toString();
  		var as = Tabelle.jexcel.selectedContainer[0];
  		var Zeile = Tabelle.jexcel.selectedContainer[1];
  		var Tagnamenbereich = "";
  		var Panel_Inhalt = '<form  id="WerteDialog" name="aktWertDialog">';
  		Panel_Inhalt = Panel_Inhalt + '<input name="tagnamenss" value = "" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<input name="tagnamensz" value = "" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<input name="tagnamenes" value = "" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<input name="tagnamenez" value = "" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<input name="as" value = "' + as.toString() + '" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<input name="Zeile" value = "' + Zeile.toString() + '" type="hidden">';
		Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10"><tr><td align="right">Funktion</td><td><select name="funktion" size = "1" onchange="Dialog_ausblenden();"><option>aktueller Wert</option><option>Archivwerte</option></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td></td><td><div id="1">von<br><input name="von" size="15" value="" type="text"></div></td><td><div id="2">bis<br><input name="bis" size="15" value="" type="text"></div></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td align="right"><div id="3">Datentyp</div></td><td><div id="4"><select name="typ" size="1"><option>Rohwerte</option><option>Stundenmittelwerte</option><option>Tagesmittelwerte</option><option>Min-Werte stündlich</option><option>Max-Werte stündlich</option><option>Min & Max-Werte stündlich</option><option>Min-Werte täglich</option><option>Max-Werte täglich</option><option>Min & Max-Werte täglich</option></div></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">Ausgabe ab</td><td><input name="ausgabebereich" size="5" value="' + Ausgabebereich + '" type="text" readonly="readonly"></td></tr>'; 
		Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">Bereich Tagnamen</td><td><input name="tagnamenbereich" size="5" value="' + Tagnamenbereich + '" type="text" readonly="readonly"></td><td><input name="tagbereich_aussuchen" value="Markierung übernehmen" type="button" onclick="Tagbereich_auswaehlen();"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">Richtung</td><td><select name="richtung" size = "1">';
		if (Richtung == "waagerecht"){
			Panel_Inhalt = Panel_Inhalt + '<option selected="selected">waagerecht</option><option>senkrecht</option>';
		} else {
			Panel_Inhalt = Panel_Inhalt + '<option>waagerecht</option><option selected="selected">senkrecht</option>';
		}
		Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">Zeitstempel anzeigen</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" checked="checked"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">Einheit anzeigen</td><td><input name="einheit" value="einheit" type="checkbox" checked="checked"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">vt anzeigen</td><td><input name="vt" value="vt" type="checkbox"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">vt interpoliert anzeigen</td><td><input name="vt_interpol" value="vt interpol" type="checkbox"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">Unix Zeitstempel anzeigen</td><td><input name="unixzeit" value="unixzeit" type="checkbox"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="fertig" type="button" onclick="Funktion_Werte_schreiben();"></td></tr>';
		Panel_Inhalt = Panel_Inhalt + '</table></form>';

		jsPanel.create({
			id: 'aktWertDialog',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '600 550',
			headerTitle: 'Einstellung aktueller Wert',
			position: 'left-top 300 200',
			content:  Panel_Inhalt,
		});
		Dialog_ausblenden()
	}
}

function Tagbereich_auswaehlen(){
	var Formular = document.getElementById("WerteDialog");
	Formular.tagnamenbereich.value = selectionActive(Tabelle,Tabelle.jexcel.selectedContainer[0],Tabelle.jexcel.selectedContainer[1],Tabelle.jexcel.selectedContainer[2],Tabelle.jexcel.selectedContainer[3]);
	if (Tabelle.jexcel.selectedContainer[1] == Tabelle.jexcel.selectedContainer[3]) {Formular.richtung.value = "senkrecht";}
  	if (Tabelle.jexcel.selectedContainer[0] == Tabelle.jexcel.selectedContainer[2]) {Formular.richtung.value = "waagerecht";}
  	Formular.tagnamenss.value = Tabelle.jexcel.selectedContainer[0];
  	Formular.tagnamensz.value = Tabelle.jexcel.selectedContainer[1];
	Formular.tagnamenes.value = Tabelle.jexcel.selectedContainer[2];
	Formular.tagnamenez.value = Tabelle.jexcel.selectedContainer[3];
}

function Funktion_Werte_schreiben() {
	var Formular = document.getElementById("WerteDialog");
	Spalte = parseInt(Formular.as.value);
	Zeile = parseInt(Formular.Zeile.value);
	Tabelle.jexcel.updateSelectionFromCoords(Spalte,Zeile,Spalte,Zeile);
	var Start = 0;
	var Ende = 0;
	var Einheit = "0";
	var Zeitstempel = "0";
	var Richtung = "0";
	var Spalte = 0;
	var Zeile = 0;
	var vt_interpol = "0";
	var vt = "0";
	var unixzeit = "0";
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	if (Formular.vt.checked == true){vt = "1";}
	if (Formular.vt_interpol.checked == true){vt_interpol = "1";}
	if (Formular.unixzeit.checked == true){unixzeit = "1";}
	if (Formular.tagnamenss.value == Formular.tagnamenes.value) {
		Start = parseInt(Formular.tagnamensz.value);
		Ende = parseInt(Formular.tagnamenez.value);
		Bewegung = "senkrecht";
	} else {
		Start = parseInt(Formular.tagnamenss.value);
		Ende = parseInt(Formular.tagnamenes.value);
		Bewegung = "waagerecht";
	}
	if (Bewegung == "waagerecht"){
		Zeile = parseInt(Formular.Zeile.value);
	} else {
		Spalte = parseInt(Formular.as.value);
	}
	for (i=Start; i <= Ende; i++ ) {
		if (Bewegung == "waagerecht"){
			Spalte = parseInt(Formular.as.value) - Start + i;
			Tabelle.jexcel.setWidth(Spalte,130);
			Tabelle.jexcel.updateSelectionFromCoords(Spalte,Zeile,Spalte,Zeile);
			if (Formular.funktion.value == "aktueller Wert") {
				Tabelle.jexcel.setValueFromCoords(Spalte, Zeile, "=akt_Wert('" + Tabelle.jexcel.getHeader(parseInt(Formular.tagnamenss.value) - Start + i) + (parseInt(Formular.tagnamensz.value) + 1).toString() + "'," + Zeitstempel + "," + Einheit + ",'" + Formular.richtung.value + "')");	
			} else {
				Tabelle.jexcel.setValueFromCoords(Spalte, Zeile, "=Archivwerte('" + Tabelle.jexcel.getHeader(parseInt(Formular.tagnamenss.value) - Start + i) + (parseInt(Formular.tagnamensz.value) + 1).toString() + "'," + Zeitstempel + ",'" + Formular.von.value + "','" + Formular.bis.value + "'," + Einheit + "," + vt + "," + vt + "," + unixzeit + ",'" + Formular.typ.value + "','" + Formular.richtung.value + "')");
			}
			
		} else {
			Zeile = parseInt(Formular.Zeile.value) - Start + i;
			Tabelle.jexcel.updateSelectionFromCoords(Spalte,Zeile,Spalte,Zeile);
			if (Formular.funktion.value == "aktueller Wert") {
				Tabelle.jexcel.setValueFromCoords(Spalte, Zeile, "=akt_Wert('" + Tabelle.jexcel.getHeader(parseInt(Formular.tagnamenss.value)) + (parseInt(Formular.tagnamensz.value) - Start + i + 1).toString() + "'," + Zeitstempel + "," + Einheit + ",'" + Formular.richtung.value + "')");
			} else {
				Tabelle.jexcel.setValueFromCoords(Spalte, Zeile, "=Archivwerte('" + Tabelle.jexcel.getHeader(parseInt(Formular.tagnamenss.value)) + (parseInt(Formular.tagnamensz.value) - Start + i + 1).toString() + "'," + Zeitstempel + ",'" + Formular.von.value + "','" + Formular.bis.value + "'," + Einheit + "," + vt + "," + vt_interpol + "," + unixzeit + ",'" + Formular.typ.value + "','" + Formular.richtung.value + "')");
			}
		}
		Tabelle.jexcel.updateSelectionFromCoords(Spalte,Zeile,Spalte,Zeile);
	}
}

function neu_berechnen() {
	jsPanel.create({
 		position:    'center-top 0 200 down',
 		contentSize: '250 100',
 		content:     '<br><br>... die Berechnung läuft.',
		theme:       'success filled',
  		headerTitle: 'Bitte haben Sie etwas Geduld, ...'
	});
		
		
	Z = 0;
	var Zeilen = Tabelle.jexcel.options.data[Z].length; 
	while (Z < Zeilen) {
		S = 0;
		while (S <= Tabelle.jexcel.options.data[Z].length) {
			try{
				if (Tabelle.jexcel.options.data[Z][S].length > 0){
					if (Tabelle.jexcel.options.data[Z][S].substr(0,1) == "=") {
						Spalte = S;
						Zeile = Z;
						Tabelle.jexcel.updateCell(S,Z,Tabelle.jexcel.getValueFromCoords(S,Z));
					}
				}
			} catch (err) {}
			S = S + 1;
		}
		Z = Z + 1;
	}
	try{jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).close();} catch (err) {}
}


document.addEventListener('DOMContentLoaded', function() {
	jexcel(document.getElementById('spreadsheet'), {
		data:Blatt,
      rowResize:true,
      columnDrag:true,
		minDimensions:[30,30],
      onbeforechange: beforeChange,
      onselection: selectionActive,
		toolbar:[
			{ type:'i', content:'undo', onclick:function() { Tabelle.jexcel.undo(); } },
        	{ type:'i', content:'redo', onclick:function() { Tabelle.jexcel.redo(); } },
        	{ type:'i', content:'save', onclick:function () { Tabelle.jexcel.download(); } },
        	{ type:'select', k:'font-family', v:['Arial','Verdana'] },
        	{ type:'select', k:'font-size', v:['9px','10px','11px','12px','13px','14px','15px','16px','17px','18px','19px','20px'] },
        	{ type:'i', content:'format_align_left', k:'text-align', v:'left' },
        	{ type:'i', content:'format_align_center', k:'text-align', v:'center' },
        	{ type:'i', content:'format_align_right', k:'text-align', v:'right' },
        	{ type:'i', content:'format_bold', k:'font-weight', v:'bold' },
        	{ type:'color', content:'format_color_text', k:'color' },
        	{ type:'color', content:'format_color_fill', k:'background-color' },
    	],
    	 style: Aussehen,
    	 columns: Spaltenkonfig,
	});
});

var Tabelle = document.getElementById('spreadsheet');

</script>
</body>
</html>