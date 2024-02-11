var T_Text = new Array;
var panel;
var akt_Zelle_A;
var akt_Zelle_C;
var akt_Ber_A = []
var akt_Blatt;
var Blatt = [];
var Aussehen = [];
var Spaltenkonfig = [];
var Tabs = [];
var mZellen = [];
var Kommentare = [];

try{Blatt = JSON.parse(document.getElementById("savestr").innerHTML);} catch (err) {
	document.getElementById("savestr").innerHTML = document.getElementById("savestr").innerHTML.replace(/\n/g, "");
	try{Blatt = JSON.parse(document.getElementById("savestr").innerHTML);} catch (err) {}
}
for (b = 0; b < Blatt.length; b++) {
	for (z = 0; z < Blatt[b].length; z++) {
		for (s = 0; s < Blatt[b][z].length; s++) {
			if (Blatt[b][z][s] > "") {
				Blatt[b][z][s] = Blatt[b][z][s].replace(/@@@/g, "'");
				Blatt[b][z][s] = Blatt[b][z][s].replace(/µµµ/g, '"');
			}
		}
	}
}
try{Aussehen = JSON.parse(document.getElementById("config").innerHTML);} catch (err) {}
try{mZellen = JSON.parse(document.getElementById("mzellen").innerHTML);} catch (err) {}
try{Kommentare = JSON.parse(document.getElementById("kommentare").innerHTML);} catch (err) {}
for (k = 0; k < Kommentare.length; k++) {
	for (z = 0; z < Kommentare[k].length; z++) {
		for (s = 0; s < Kommentare[k][z].length; s++) {
			if (Kommentare[k][z][s] > "") {
				Kommentare[k][z][s] = Kommentare[k][z][s].replace(/@@@/g, "'");
				Kommentare[k][z][s] = Kommentare[k][z][s].replace(/µµµ/g, '"');
			}
		}
	}
}
try{Spaltenkonfig = JSON.parse(document.getElementById("Spalten").innerHTML);} catch (err) {}
try{Tabs = JSON.parse(document.getElementById("tabs").innerHTML);} catch (err) {}
var Hoehe = window.innerHeight - 120;
if (document.getElementById("mobil").value == "1") {Hoehe = window.innerHeight;}

$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	try {
		Tab1 = document.getElementsByClassName("jexcel_tab jexcel_container")[0].parentElement;
		Mama = Tab1.parentElement;
		Tab2 = Mama.firstChild;
		Mama.removeChild(Tab1);
		Mama.removeChild(Tab2);
		Mama.appendChild(Tab1);
		Mama.appendChild(Tab2);
	} catch (err) {}
	try {document.getElementById("timestamp1").value = document.getElementById("timestamp").value;} catch (err) {}
	for (i = 0; i < Tabelle.jexcel.length; i++) {
		akt_Blatt = i;
		Tabelle.jexcel[akt_Blatt].filter.style.display = "none";
		Tabelle.jexcel[akt_Blatt].versteckt = [];
		neu_berechnen();
	}
	if (document.getElementById("mobil").value == "1") {	
		x = document.getElementsByClassName("bereich_mobil");
		for (i = 0; i < x.length; i++) {
			x[i].style.width = window.innerWidth.toString() + "px";
		}
		document.getElementById("nav_mobil").style.width = window.innerWidth.toString() + "px";
	}
	var Toolbars = document.getElementsByClassName("jexcel_toolbar");
	for (i = 0; i < Toolbars.length; i++) {
		Toolbars[i].childNodes[0].setAttribute("title",T_Text[162]);
		Toolbars[i].childNodes[1].setAttribute("title",T_Text[163]);
		Toolbars[i].childNodes[2].setAttribute("title",T_Text[114]);
		Toolbars[i].childNodes[3].setAttribute("title",T_Text[148]);
		Toolbars[i].childNodes[4].setAttribute("title",T_Text[147]);
		Toolbars[i].childNodes[5].setAttribute("title",T_Text[115]);
		Toolbars[i].childNodes[6].setAttribute("title",T_Text[20]);
		Toolbars[i].childNodes[7].setAttribute("title",T_Text[158]);
		Toolbars[i].childNodes[8].setAttribute("title",T_Text[159]);
		Toolbars[i].childNodes[9].setAttribute("title",T_Text[2]);
		Toolbars[i].childNodes[10].setAttribute("title",T_Text[164]);
		Toolbars[i].childNodes[11].setAttribute("title",T_Text[165]);
		Toolbars[i].childNodes[12].setAttribute("title",T_Text[39]);
		Toolbars[i].childNodes[13].setAttribute("title",T_Text[161]);
	}
});

function speichern() {
	Tabls = Tabelle.jexcel;
	Daten = [];
	Conf = [];
	Sp = [];
	Tabs = [];
	MergeZellen = [];
	Kommentare = [];
	for (i = 0; i < Tabls.length; i++) {
		Daten[i] = Tabelle.jexcel[i].getData();
		for (z = 0; z < Daten[i].length; z++) {
			for (s = 0; s < Daten[i][z].length; s++) {
				if (Daten[i][z][s] > "") {
					Daten[i][z][s] = Daten[i][z][s].replace(/"/g, 'µµµ');
				}
			}
		}
		Conf[i] = Tabelle.jexcel[i].getConfig().style;
		Sp[i] = Tabelle.jexcel[i].getConfig().columns;
		Kommentare[i] = Tabelle.jexcel[i].getConfig().comments;
		for (z = 0; z < Kommentare[i].length; z++) {
			for (s = 0; s < Kommentare[i][z].length; s++) {
				if (Kommentare[i][z][s] > "") {
					Kommentare[i][z][s] = Kommentare[i][z][s].replace(/"/g, 'µµµ');
				}
			}
		}
		MergeZellen[i] = Tabelle.jexcel[i].getConfig().mergeCells;
		Tabs[i] = Tabelle.lastChild.childNodes[i].innerHTML;
	}
	$('#savestr').val(JSON.stringify(Daten));
	document.getElementById('config').value = JSON.stringify(Conf);
	document.getElementById('Spalten').value = JSON.stringify(Sp);
	document.getElementById('mzellen').value = JSON.stringify(MergeZellen);
	document.getElementById('kommentare').value = JSON.stringify(Kommentare);
	document.getElementById('tabs').value = JSON.stringify(Tabs);
	document.Formular.submit();
}

var add = function() {
    var sheets = [];
    sheets.push({
		sheetName: prompt('Create a new tab', 'Tab ' + (document.getElementById('spreadsheet').jexcel.length + 1).toString()),
		minDimensions:[40,40],
		tableOverflow:true,
		tableHeight: Hoehe,
		tableWidth: window.innerWidth - 20,
		defaultColWidth: 80,
		filters: true,
		onselection: Auswahl,
		allowComments:true,
		about: false,
		toolbar:[
			{ type:'i', content:'undo', onclick:function() {Tabelle.jexcel[akt_Blatt].undo();}},
			{ type:'i', content:'redo', onclick:function() { Tabelle.jexcel[akt_Blatt].redo();}},
			{ type:'i', content:'insert_drive_file', onclick:function() {Menu(T_Text[114]);}},
			{ type:'i', content:'table_rows', onclick:function() {Menu(T_Text[148]);}},
			{ type:'i', content:'view_column', onclick:function() {Menu(T_Text[147]);}},
			{ type:'i', content:'text_format', onclick:function() {Menu(T_Text[115]);}},
			{ type:'i', content:'border_all', onclick:function() {Menu(T_Text[20]);}},
			{ type:'i', content:'comment', onclick:function() {kommentar();}},
			{ type:'i', content:'filter_list', onclick:function() {filterschaltung();}},
			{ type:'i', content:'functions', onclick:function() {neu_berechnen();}},
			{ type:'i', content:'swap_horiz', onclick:function() {freeze(0);}},
			{ type:'i', content:'swap_vert', onclick:function() {freeze(1);}},
			{ type:'i', content:'help_outline', onclick:function() {Hilfe_Fenster("46");}},
			{ type:'i', content:'edit', onclick:function() {editieren();}},
		],
    });
    jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
};

var Auswahl = function () {
	try {
		Kontainer = document.getElementsByClassName("jexcel_tab jexcel_container");
		for (i = 0; i < Kontainer.length; i++ ) {
			if (Kontainer[i].style["display"] == "block") {
				akt_Blatt = i;
				akt_Zelle_A = Tabelle.jexcel[i].getHeader(document.getElementById('spreadsheet').jexcel[i].selectedCell[0]) + (parseInt(Tabelle.jexcel[i].selectedCell[1]) + 1).toString();
				akt_Zelle_C = Tabelle.jexcel[i].selectedCell[0].toString() + "," + Tabelle.jexcel[i].selectedCell[1].toString();
				akt_Ber_C = Tabelle.jexcel[i].selectedContainer;
//				akt_Ber_A = Nummern_zu_Zellenbez(akt_Ber_C);
			}
		}
	} catch (err) {}
}

if (Blatt.length > 0) {
	var sheets = [];
	for (i = 0; i < Blatt.length; i++) {
		if (Aussehen[i] == undefined) {Aussehen[i] = [];}
		if (Spaltenkonfig[i] == undefined) {Spaltenkonfig[i] = [];}
		if (mZellen[i] == undefined) {mZellen[i] = [];}
		if (Kommentare[i] == undefined) {Kommentare[i] = [];}
		if (Tabs[i] == undefined) {Tabs[i] = "Tab " + (i + 1).toString();}
		sheets.push([{
			data:Blatt[i],
			sheetName: Tabs[i],
			minDimensions:[40,40],
			tableOverflow:true,
			tableHeight: Hoehe,
			tableWidth: window.innerWidth - 20,
			defaultColWidth: 80,
			onselection: Auswahl,
			filters: true,
			allowComments:true,
			about: false,
			style: Aussehen[i],
			columns: Spaltenkonfig[i],
			mergeCells: mZellen[i],
			comments: Kommentare[i],
			toolbar:[
				{ type:'i', content:'undo', onclick:function() {Tabelle.jexcel[akt_Blatt].undo();}},
				{ type:'i', content:'redo', onclick:function() { Tabelle.jexcel[akt_Blatt].redo();}},
				{ type:'i', content:'insert_drive_file', onclick:function() {Menu(T_Text[114]);}},
				{ type:'i', content:'table_rows', onclick:function() {Menu(T_Text[148]);}},
				{ type:'i', content:'view_column', onclick:function() {Menu(T_Text[147]);}},
				{ type:'i', content:'text_format', onclick:function() {Menu(T_Text[115]);}},
				{ type:'i', content:'border_all', onclick:function() {Menu(T_Text[20]);}},
				{ type:'i', content:'comment', onclick:function() {kommentar();}},
				{ type:'i', content:'filter_list', onclick:function() {filterschaltung();}},
				{ type:'i', content:'functions', onclick:function() {neu_berechnen();}},
				{ type:'i', content:'swap_horiz', onclick:function() {freeze(0);}},
				{ type:'i', content:'swap_vert', onclick:function() {freeze(1);}},
				{ type:'i', content:'help_outline', onclick:function() {Hilfe_Fenster("46");}},
				{ type:'i', content:'edit', onclick:function() {editieren();}},
				],
		}]);
	}
	fertig = 0;
	for (i = 0; i < sheets.length; i++) {
		if (Array.isArray(sheets[i]) == true) {
			temp = sheets[i][0];
			sheets.splice(i,1,temp);
		}
	}
//jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
} else {
	sheets = [{
		sheetName: 'Tab 1',
		minDimensions:[40,40],
		tableOverflow:true,
		filters: true,
		tableHeight: Hoehe,
		tableWidth: window.innerWidth - 80,
		defaultColWidth: 80,
		onselection: Auswahl,
		allowComments:true,
		about: false,
		toolbar:[
			{ type:'i', content:'undo', onclick:function() {Tabelle.jexcel[akt_Blatt].undo();}},
			{ type:'i', content:'redo', onclick:function() { Tabelle.jexcel[akt_Blatt].redo();}},
			{ type:'i', content:'insert_drive_file', onclick:function() {Menu(T_Text[114]);}},
			{ type:'i', content:'table_rows', onclick:function() {Menu(T_Text[148]);}},
			{ type:'i', content:'view_column', onclick:function() {Menu(T_Text[147]);}},
			{ type:'i', content:'text_format', onclick:function() {Menu(T_Text[115]);}},
			{ type:'i', content:'border_all', onclick:function() {Menu(T_Text[20]);}},
			{ type:'i', content:'comment', onclick:function() {kommentar();}},
			{ type:'i', content:'filter_list', onclick:function() {filterschaltung();}},
			{ type:'i', content:'functions', onclick:function() {neu_berechnen();}},
			{ type:'i', content:'swap_horiz', onclick:function() {freeze(0);}},
			{ type:'i', content:'swap_vert', onclick:function() {freeze(1);}},
			{ type:'i', content:'help_outline', onclick:function() {Hilfe_Fenster("46");}},
			{ type:'i', content:'edit', onclick:function() {editieren();}},
		],
	}];
//	jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
}

function TEXT(Zahl) {
	if (ISNUMBER(Zahl) == false) {Zahl = Tabelle.jexcel[akt_Blatt].getValue(Zahl);}
	return Zahl.toString();
}

function AKT_ZEIT() {
	var Zeitpunkt = new Date();
	return ZEIT_FORMAT(Zeitpunkt);
}

function ZEIT_FORMAT(Zeitpunkt) {
	Zeitstempel = Zeitpunkt.getFullYear() + "-";
	var tmp = (Zeitpunkt.getMonth() + 1).toString();
	if (tmp.length == 1) {tmp = "0" + tmp;}
	Zeitstempel = Zeitstempel + tmp;
	tmp = (Zeitpunkt.getDate()).toString();
	if (tmp.length == 1) {tmp = "0" + tmp;}
	Zeitstempel = Zeitstempel + "-" + tmp + " ";
	tmp = (Zeitpunkt.getHours()).toString();
	if (tmp.length == 1) {tmp = "0" + tmp;}
	Zeitstempel = Zeitstempel + tmp + ":";
	tmp = (Zeitpunkt.getMinutes()).toString();
	if (tmp.length == 1) {tmp = "0" + tmp;}
	Zeitstempel = Zeitstempel + tmp + ":";
	tmp = (Zeitpunkt.getSeconds()).toString();
	if (tmp.length == 1) {tmp = "0" + tmp;}
	Zeitstempel = Zeitstempel + tmp;
	return Zeitstempel;
}

function ZEIT_ADD(Zeitstempel,Operator,Sekunden) {
	Zeitpunkt = Date.parse(Zeitstempel);
	if (Operator == "+") {
		Zeitpunkt = Zeitpunkt + Sekunden * 1000;
	}
	if (Operator == "-") {
		Zeitpunkt = Zeitpunkt - Sekunden * 1000;
	}
	return ZEIT_FORMAT(new Date(Zeitpunkt));
}

function AKT_WERT(TagZelle,Zeitstempel,Einheit,Richtung) {
	if (Tabelle == undefined) {return;}
	var strReturn;
	if (TagZelle.substr(0,1) == "/") {
		Tagname = TagZelle;
	} else {
		Tagname = Tabelle.jexcel[akt_Blatt].getCell(TagZelle).innerHTML;
	}
   Zelle = Zellenbez_zu_Nummern(CELL());
   Zeile = Zelle[0] - 1;
   Spalte = Zelle[1];
	jQuery.ajax({
		url: "akt_Wert.php?Tagname=" + Tagname,
		success: function (html) {
			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = T_Text[48];}
  	Ergebnis = strReturn.split(",");
	if (Zeitstempel == 1) {Attribute_Schreiben(Ergebnis[1]);}
  	if (Einheit == 1) {Attribute_Schreiben(Ergebnis[2]);}
	return Ergebnis[0];
	function Attribute_Schreiben(Wert) {
		if (Richtung == T_Text[49].toUpperCase() || Richtung == T_Text[49].toLowerCase() || Richtung == "senkrecht" || Richtung == "SENKRECHT") {Zeile = Zeile + 1;}
		if (Richtung == T_Text[48].toUpperCase() || Richtung == T_Text[48].toLowerCase() || Richtung == "waagerecht" || Richtung == "WAAGERECHT") {Spalte = Spalte + 1;}
		if (Zeile > Tabelle.jexcel[akt_Blatt].rows.length - 1) {Tabelle.jexcel[akt_Blatt].insertRow();}
		if (Spalte > Tabelle.jexcel[akt_Blatt].colgroup.length - 1) {Tabelle.jexcel[akt_Blatt].insertColumn();}
		try {
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(Spalte, Zeile, Wert, 1);
		} catch (err) {}
	}
}

function Funktion_aktWert_schreiben(Zelle) {
	var Formular = document.getElementById("aktuellerWertDialog");
	var Einheit = "0";
	var Zeitstempel = "0";
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	Kontainer = document.getElementsByClassName("jexcel_tab jexcel_container");
	TagZelle = Formular.tagnamenbereich.value;
	Tabelle.jexcel[akt_Blatt].setValue(Zelle,'=AKT_WERT("' + TagZelle + '",' + Zeitstempel + ',' + Einheit + ',"' + Formular.richtung.value + '")');
	try {aktWertDialog.close();} catch (err) {}
}

function aktueller_Wert_Dialog(){
	try {aktWertDialog.close();} catch (err) {}
	var Zelle = akt_Zelle_A;
	var Tagnamenbereich = "";
	var Zeitstempel = "";
	var Einheit = "";
	var Richtung = "";
	var ZelleWert = Tabelle.jexcel[akt_Blatt].getValue(Zelle);
	if (ZelleWert.substr(0,9) == "=AKT_WERT") {
		Tagnamenbereich = ZelleWert.substr(11,1);
		i = 12;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Tagnamenbereich = Tagnamenbereich + ZelleWert.substr(i,1);
			i = i + 1;
		}
		while (ZelleWert.substr(i,1) != ",") {
			i = i + 1;
		}
		i = i + 1;
		Zeitstempel = ZelleWert.substr(i,1);
		if (Zeitstempel == "1") {
			Zeitstempel = "checked=\"checked\"";
		} else {
			Zeitstempel = "";
		}
		i = i + 2;
		Einheit = ZelleWert.substr(i,1);
		if (Einheit == "1") {
			Einheit = "checked=\"checked\"";
		} else {
			Einheit = "";
		}
		i = i + 3;
		Richtung = ZelleWert.substr(i,1);
		i = i + 1;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Richtung = Richtung + ZelleWert.substr(i,1);
			i = i + 1;
		}
	}
  	var Panel_Inhalt = '<form  id="aktuellerWertDialog" name="aktWertDialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="15" value="' + Tagnamenbereich + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.aktuellerWertDialog.tagnamenbereich, \'aktuellerWertDialog\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[47] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected">' + T_Text[48] + '</option><option>' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option>' + T_Text[48] + '</option><option selected="selected">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[50] + '</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" ' + Zeitstempel + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[51] + '</td><td><input name="einheit" value="einheit" type="checkbox" ' + Einheit + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_aktWert_schreiben(\'' + Zelle.toUpperCase() + '\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'aktWertDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '440 220',
		headerTitle: T_Text[104],
		content:  Panel_Inhalt,
	});
}

function Bereich_auswaehlen(Feld, Formularname) {
	var Formular = document.getElementById(Formularname);
	if (Feld.name == "von") {
		try {Formular.von.value = akt_Zelle_A;} catch (err) {} 
	}
	if (Feld.name == "bis") {
		try {Formular.bis.value = akt_Zelle_A;} catch (err) {} 
	}
	if (Feld.name == "Zeitpunkt") {
		try {Formular.Zeitpunkt.value = akt_Zelle_A;} catch (err) {} 
	}
	if (Feld.name == "tagnamenbereich") {
		try {Formular.tagnamenbereich.value = akt_Zelle_A;} catch (err) {} 
	}
	if (Feld.name == "ausgabebereich") {
		try {Formular.ausgabebereich.value = akt_Zelle_A;} catch (err) {}
	}
}

function Zellenbez_zu_Nummern(Bereich) {
	var istBereich = Bereich.indexOf(":");
	var Bereich_Array = {};
	var i = 0;
	while (Bereich.charCodeAt(i) < 48 || Bereich.charCodeAt(i) > 57) { 	
		i = i + 1;
	}
	Spaltenname = Bereich.substr(0,i);
	if (istBereich > -1) {
		Bereich_Array[0] = parseInt(Bereich.substr(i,istBereich - 1));
	} else {
		Bereich_Array[0] = parseInt(Bereich.substr(i,Bereich.length - i));
	}
	Bereich_Array[1] = COLUMN() - 1;
	
	if (istBereich > -1) {
		Bereich = Bereich.substr(istBereich + 1,Bereich.length);
		i = 0;
		while (Bereich.charCodeAt(i) < 48 || Bereich.charCodeAt(i) > 57) { 	
			i = i + 1;
		}
		Spaltenname = Bereich.substr(0,i);
		Bereich_Array[2] = parseInt(Bereich.substr(i,Bereich.length - i));
		Bereich_Array[3] = COLUMN() - 1;
	}
	return Bereich_Array;
}

function Nummern_zu_Zellenbez(Bereich) {
	Alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	for (i = 0; i < 3; i = i + 2) {
		Name = "";
		Spalten = Bereich[i];
		Rest = Spalten - parseInt(Spalten / 26) * 26;
		Name = Alphabet[Rest] + Name;
		if (Spalten > Rest) {
			Spalten = Spalten - Rest;
		} else {
			Spalten = -1;
		}
		if (Spalten >= 676) {
			Rest = parseInt(Spalten / 26) - 26;
			if (Rest == 0) {
				Buchst = 25;
			} else {
				Buchst = Rest - 1;
			}
			Name = Alphabet[Buchst] + Name;
			if (Spalten / 676 == parseInt(Spalten / 676)) {Spalten = Spalten - 1;}
			Spalten = Spalten - 676 - Rest * 26;
		}
		if (Spalten >= 26) {
			Rest = parseInt(Spalten / 26);
			if (Rest == 0) {
				Buchst = 25;
			} else {
				Buchst = Rest - 1;
			}
			Name = Alphabet[Rest - 1] + Name;
			if (Spalten / 26 == parseInt(Spalten / 26)) {Spalten = Spalten - 1;}
			Spalten = Spalten - Rest * 26;
		}
		if (Spalten > -1) {Name = Alphabet[Spalten] + Name;}
		akt_Ber_A[i] = Name;
		akt_Ber_A[i + 1] = Bereich[i + 1];
	}
	return akt_Ber_A;
}

function Tag_suchen_Dialog() {
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'tag_suchen_Dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '540 112',
		headerTitle: T_Text[34],
		content:  '<div style="background: #FCEDD9;"><div style="margin-top: 5px;margin-left: 5px;" class="Text_einfach"><u><b>' + T_Text[94] + ':</u></b>&nbsp;' + T_Text[95] + '<br><br><form name="Tag_suchen_Form">' + T_Text[96] + ':&nbsp;<input class="Text_Element" name="Tagname" size="40" type="text">&nbsp;&nbsp;&nbsp;<input class="Schalter_Element" name="suchen" value="' + T_Text[97] + '" type="button" onclick="Eingabefeld_sichtbar_dialog()"></form><br></div></div>',
	});
}
	
function uebertragen() {
	try {tag_suchen_Dialog.close();} catch (err) {}
	try {
		var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
		Tabelle.jexcel[akt_Blatt].setValue(akt_Zelle_A,Ergebnis[0] + Ergebnis[1]);
	}
	catch (err) {}
	try {Tagsuche.close();} catch (err) {}
}	
function Eingabefeld_sichtbar_dialog() {
	jQuery.ajax({
		url: "Test_Tag_suchen.php?Suchtext=" + document.Tag_suchen_Form.Tagname.value,
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
	try {tag_suchen_Dialog.close();} catch (err) {}
}

function Archivwert_Dialog(){
	try {ArchivwDialog.close();} catch (err) {}
	var Richtung = T_Text[49];
	var Zelle = akt_Zelle_A;
	var Tagnamenbereich = "";
	var Zeitpunkt = "";
	var interpoliert = "";
	var typ = "";
	var Zeitstempel = "";
	var Einheit = "";
	var Richtung = "";
	var ZelleWert = Tabelle.jexcel[akt_Blatt].getValue(Zelle);
	if (ZelleWert.substr(0,4) == "=AW(") {
		Tagnamenbereich = ZelleWert.substr(5,1);
		i = 6;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Tagnamenbereich = Tagnamenbereich + ZelleWert.substr(i,1);
			i = i + 1;
		}
		while (ZelleWert.substr(i,1) != ",") {
			i = i + 1;
		}
		i = i + 2;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Zeitpunkt = Zeitpunkt + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			interpoliert = interpoliert + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 2;
		Zeitstempel = ZelleWert.substr(i,1);
		if (Zeitstempel == "1") {
			Zeitstempel = "checked=\"checked\"";
		} else {
			Zeitstempel = "";
		}
		i = i + 2;
		Einheit = ZelleWert.substr(i,1);
		if (Einheit == "1") {
			Einheit = "checked=\"checked\"";
		} else {
			Einheit = "";
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			typ = typ + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 3;
		Richtung = ZelleWert.substr(i,1);
		i = i + 1;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Richtung = Richtung + ZelleWert.substr(i,1);
			i = i + 1;
		}
	}
  	var Panel_Inhalt = '<form  id="ArchivwertDialog" name="Archivwert_Dialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[53] + '</td><td><select name="typ" size="1" value="' + typ + '">';
	if (typ == "ROHWERT") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Rohwert" selected>' + T_Text[54] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Rohwert">' + T_Text[54] + '</option>';
	}
	if (typ == "STUNDENMITTELWERT") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Stundenmittelwert" selected>' + T_Text[55] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Stundenmittelwert">' + T_Text[55] + '</option>';
	}
	if (typ == "TAGESMITTELWERT") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Tagesmittelwert" selected>' + T_Text[56] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Tagesmittelwert">' + T_Text[56] + '</option>';
	}
	if (typ == "MIN-WERT STUNDE") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Wert Stunde" selected>' + T_Text[57] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Wert Stunde">' + T_Text[57] + '</option>';
	}
	if (typ == "MAX-WERT STUNDE") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Wert Stunde" selected>' + T_Text[58] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Wert Stunde">' + T_Text[58] + '</option>';
	}
	if (typ == "MIN-WERT TAG") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Wert Tag" selected>' + T_Text[59] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Wert Tag">' + T_Text[59] + '</option>';
	}
	if (typ == "MAX-WERT TAG") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Wert Tag" selected>' + T_Text[60] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Wert Tag">' + T_Text[60] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[61] + '</td><td><input name="Zeitpunkt" size="15" value="' + Zeitpunkt + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.ArchivwertDialog.Zeitpunkt, \'ArchivwertDialog\');"><image src="./images/Zelle_ubern.png"></a></td>';
	Panel_Inhalt = Panel_Inhalt + '<td><select name="interpoliert" size="1">';
	if (interpoliert == "WERT DAVOR") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Wert davor" selected>' + T_Text[62] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Wert davor">' + T_Text[62] + '</option>';
	}
	if (interpoliert == "INTERPOLIERT") {
		Panel_Inhalt = Panel_Inhalt + '<option value="interpoliert" selected>' + T_Text[63] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="interpoliert">' + T_Text[63] + '</option>';
	}
	if (interpoliert == "WERT DANACH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Wert danach" selected>' + T_Text[64] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Wert danach">' + T_Text[64] + '</option>';
	}
	
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="15" value="' + Tagnamenbereich + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.ArchivwertDialog.tagnamenbereich, \'ArchivwertDialog\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[69] + '</td><td><select name="richtung" size = "1" value="' + interpoliert + '">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected" value="waagerecht">' + T_Text[48] + '</option><option value="senkrecht">' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht">' + T_Text[48] + '</option><option selected="selected" value="senkrecht">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[70] + '</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" ' + Zeitstempel + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[71] + '</td><td><input name="einheit" value="einheit" type="checkbox" ' + Einheit + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Archivwert_schreiben(\'' + Zelle.toUpperCase() + '\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'ArchivwDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '460 270',
		headerTitle: T_Text[100],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_Archivwert_schreiben(Zelle) {
	var Formular = document.getElementById("ArchivwertDialog");
	var interpoliert = Formular.interpoliert.value;
	var Zeitpunkt = Formular.Zeitpunkt.value;
	var typ = Zellenbez_zu_Nummern(Formular.typ.value);
	var Einheit = "0";
	var Zeitstempel = "0";
	if (Formular.einheit.checked == true) {Einheit = "1";}
	if (Formular.zeitstempel.checked == true) {Zeitstempel = "1";}
	Tabelle.jexcel[akt_Blatt].setValue(Zelle,'=AW("' + Formular.tagnamenbereich.value + '","' + Zeitpunkt + '","' + interpoliert + '",' + Zeitstempel + ',' + Einheit + ',"' + Formular.typ.value + '", "' + Formular.richtung.value + '")');
	try {ArchivwDialog.close();} catch (err) {}
}

function AW(TagZelle,Zeitpunkt,interpoliert,Zeitstempel,Einheit,typ,Richtung) {
	if (Tabelle == undefined) {return;}
	var strReturn;
	if (TagZelle.substr(0,1) == "/") {
		Tagname = TagZelle;
	} else {
		Tagname = Tabelle.jexcel[akt_Blatt].getCell(TagZelle).innerHTML;
	}
	if (isNaN(parseInt(Zeitpunkt.substr(0,1))) == true) {Zeitpunkt = Tabelle.jexcel[akt_Blatt].getCell(Zeitpunkt).innerHTML;}
	Zelle = Zellenbez_zu_Nummern(CELL());
   Zeile = Zelle[0] - 1;
   Spalte = Zelle[1];
	jQuery.ajax({
		url: "DH_AW.php?Tagname=" + Tagname + "&Zeitpunkt=" + Zeitpunkt + "&typ=" + typ + "&interpoliert=" + interpoliert,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = T_Text[49].toUpperCase();}
  	Ergebnis = strReturn.split(",");
	if (Zeitstempel == 1){Attribute_Schreiben(Ergebnis[1]);}
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[2]);}
	return Ergebnis[0];
   
	function Attribute_Schreiben(Wert) {
		if (Richtung == T_Text[49].toUpperCase() || Richtung == T_Text[49].toLowerCase() || Richtung == "senkrecht" || Richtung == "SENKRECHT") {Zeile = Zeile + 1;}
		if (Richtung == T_Text[48].toUpperCase() || Richtung == T_Text[48].toLowerCase() || Richtung == "waagerecht" || Richtung == "WAAGERECHT") {Spalte = Spalte + 1;}
		if (Zeile > Tabelle.jexcel[akt_Blatt].rows.length - 1) {Tabelle.jexcel[akt_Blatt].insertRow();}
		if (Spalte > Tabelle.jexcel[akt_Blatt].colgroup.length - 1) {Tabelle.jexcel[akt_Blatt].insertColumn();}
		try {
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(Spalte, Zeile, Wert, 1);
		}
		catch (err) {}
	}
}

function Mittelwert_Dialog(){
	try {MWDialog.close();} catch (err) {}
	var Zelle = akt_Zelle_A;
	var Tagnamenbereich = "";
	var von = "";
	var bis = "";
	var typ = "";
	var step = "";
	var Einheit = "";
	var Richtung = "";
	var ZelleWert = Tabelle.jexcel[akt_Blatt].getValue(Zelle);
	if (ZelleWert.substr(0,4) == "=MW(") {
		Tagnamenbereich = ZelleWert.substr(5,1);
		i = 6;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Tagnamenbereich = Tagnamenbereich + ZelleWert.substr(i,1);
			i = i + 1;
		}
		while (ZelleWert.substr(i,1) != ",") {
			i = i + 1;
		}
		i = i + 2;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			von = von + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			bis = bis + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 2;
		Einheit = ZelleWert.substr(i,1);
		if (Einheit == "1") {
			Einheit = "checked=\"checked\"";
		} else {
			Einheit = "";
		}
		i = i + 2;
		step = ZelleWert.substr(i,1);
		if (step == "1") {
			step = "checked=\"checked\"";
		} else {
			step = "";
		}
		i = i + 3;
		Richtung = ZelleWert.substr(i,1);
		i = i + 1;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Richtung = Richtung + ZelleWert.substr(i,1);
			i = i + 1;
		}
	}
  	var Panel_Inhalt = '<form  id="MittelwertDialog" name="MW_Dialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="15" value="' + Tagnamenbereich + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.MittelwertDialog.tagnamenbereich, \'MittelwertDialog\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[72] + '</td><td><input name="von" size="15" value="' + von + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.MittelwertDialog.von, \'MittelwertDialog\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[73] + '</td><td><input name="bis" size="15" value="' + bis + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.MittelwertDialog.bis, \'MittelwertDialog\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[87] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected">' + T_Text[48] + '</option><option>' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option>' + T_Text[48] + '</option><option selected="selected">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[71] + '</td><td><input name="einheit" value="einheit" type="checkbox" ' + Einheit + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[92] + '</td><td><input name="step" value="step" type="checkbox" ' + step + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Mittelwert_schreiben(\'' + Zelle.toUpperCase() + '\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'MWDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '350 270',
		headerTitle: T_Text[98],
		content:  Panel_Inhalt,
	});
}

function Funktion_Mittelwert_schreiben(Zelle) {
	var Formular = document.getElementById("MittelwertDialog");
	var von = Formular.von.value;
	var bis = Formular.bis.value;
	var Einheit = "0";
	var step = "0";
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.step.checked == true){step = "1";}
	Tabelle.jexcel[akt_Blatt].setValue(Zelle,'=MW("' + Formular.tagnamenbereich.value + '","' + von + '","' + bis + '",' + Einheit + ',' + step + ',"' + Formular.richtung.value + '")');
	try {MWDialog.close();} catch (err) {}
}

function MW(TagZelle,von,bis,Einheit,step,Richtung) {
	if (Tabelle == undefined) {return;}
	var strReturn;
	if (TagZelle.substr(0,1) == "/") {
		Tagname = TagZelle;
	} else {
		Tagname = Tabelle.jexcel[akt_Blatt].getCell(TagZelle).innerHTML;
	}
	if (isNaN(parseInt(von.substr(0,1))) == true) {von = Tabelle.jexcel[akt_Blatt].getCell(von).innerHTML;}
	if (isNaN(parseInt(bis.substr(0,1))) == true) {bis = Tabelle.jexcel[akt_Blatt].getCell(bis).innerHTML;}
	Zelle = Zellenbez_zu_Nummern(CELL());
   Zeile = Zelle[0] - 1;
   Spalte = Zelle[1];
	jQuery.ajax({
		url: "DH_MW.php?Tagname=" + Tagname + "&von=" + von + "&bis=" + bis + "&step=" + step,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = T_Text[48].toUpperCase();}
  	Ergebnis = strReturn.split(",");
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[1]);}
	return Ergebnis[0];
   
	function Attribute_Schreiben(Wert) {
		if (Richtung == T_Text[49].toUpperCase() || Richtung == T_Text[49].toLowerCase() || Richtung == "senkrecht" || Richtung == "SENKRECHT") {Zeile = Zeile + 1;}
		if (Richtung == T_Text[48].toUpperCase() || Richtung == T_Text[48].toLowerCase() || Richtung == "waagerecht" || Richtung == "WAAGERECHT") {Spalte = Spalte + 1;}
		if (Zeile > Tabelle.jexcel[akt_Blatt].rows.length - 1) {Tabelle.jexcel[akt_Blatt].insertRow();}
		if (Spalte > Tabelle.jexcel[akt_Blatt].colgroup.length - 1) {Tabelle.jexcel[akt_Blatt].insertColumn();}
		try {
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(Spalte, Zeile, Wert, 1);
		}
		catch (err) {}
	}
}

function Archivwerte_Werte_Dialog(){
	try {ArchivwerteDialog.close();} catch (err) {}
	var Zelle = akt_Zelle_A;
	var Tagnamenbereich = "";
	var von = "";
	var bis = "";
	var Zeitstempel = "";
	var Unixzeit = "";
	var vt = "";
	var vt_interpol = "";
	var typ = "";
	var Richtung = "";
	var ZelleWert = Tabelle.jexcel[akt_Blatt].getValue(Zelle);
	if (ZelleWert.substr(0,8) == "=AWERTE(") {
		Tagnamenbereich = ZelleWert.substr(9,1);
		i = 10;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Tagnamenbereich = Tagnamenbereich + ZelleWert.substr(i,1);
			i = i + 1;
		}
		while (ZelleWert.substr(i,1) != ",") {
			i = i + 1;
		}
		i = i + 2;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			von = von + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			bis = bis + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 2;
		Zeitstempel = ZelleWert.substr(i,1);
		if (Zeitstempel == "1") {
			Zeitstempel = "checked=\"checked\"";
		} else {
			Zeitstempel = "";
		}
		i = i + 2;
		vt = ZelleWert.substr(i,1);
		if (vt == "1") {
			vt = "checked=\"checked\"";
		} else {
			vt = "";
		}
		i = i + 2;
		vt_interpol = ZelleWert.substr(i,1);
		if (vt_interpol == "1") {
			vt_interpol = "checked=\"checked\"";
		} else {
			vt_interpol = "";
		}
		i = i + 2;
		Unixzeit = ZelleWert.substr(i,1);
		if (Unixzeit == "1") {
			Unixzeit = "checked=\"checked\"";
		} else {
			Unixzeit = "";
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			typ = typ + ZelleWert.substr(i,1);
			i = i + 1;
		}
		i = i + 3;
		while (ZelleWert.substr(i,1) != '"' && ZelleWert.substr(i,1) != "'") {
			Richtung = Richtung + ZelleWert.substr(i,1);
			i = i + 1;
		}
	}
  	var Panel_Inhalt = '<form  id="ArchivwerteDialogInhalt" name="Archivwerte_Dialog_Inhalt">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[72] + '</td><td><input name="von" size="15" value="' + von + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.ArchivwerteDialogInhalt.von, \'ArchivwerteDialogInhalt\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[73] + '</td><td><input name="bis" size="15" value="' + bis + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.ArchivwerteDialogInhalt.bis, \'ArchivwerteDialogInhalt\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[74] + '</td><td colspan="2"><select name="typ" size="1" value="' + typ + '">';
		if (typ == "ROHWERTE") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Rohwerte" selected>' + T_Text[75] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Rohwerte">' + T_Text[75] + '</option>';
	}
	if (typ == "STUNDENMITTELWERTE") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Stundenmittelwerte" selected>' + T_Text[76] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Stundenmittelwerte">' + T_Text[76] + '</option>';
	}
	if (typ == "TAGESMITTELWERTE") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Tagesmittelwerte" selected>' + T_Text[77] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Tagesmittelwerte">' + T_Text[77] + '</option>';
	}
	if (typ == "MIN-WERTE STÜNDLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Werte stündlich" selected>' + T_Text[78] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Werte stündlich">' + T_Text[78] + '</option>';
	}
	if (typ == "MAX-WERTE STÜNDLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Werte stündlich" selected>' + T_Text[79] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Werte stündlich">' + T_Text[79] + '</option>';
	}
	if (typ == "MIN-MAX-WERTE STÜNDLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Max-Werte stündlich" selected>' + T_Text[80] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Max-Werte stündlich">' + T_Text[80] + '</option>';
	}
	if (typ == "MIN-WERTE TÄGLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Werte täglich" selected>' + T_Text[81] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Werte täglich">' + T_Text[81] + '</option>';
	}
	if (typ == "MAX-WERTE TÄGLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Werte täglich" selected>' + T_Text[82] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Max-Werte täglich">' + T_Text[82] + '</option>';
	}
	if (typ == "MIN-MAX-WERTE TÄGLICH") {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Max-Werte täglich" selected>' + T_Text[83] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="Min-Max-Werte täglich">' + T_Text[83] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="15" value="' + Tagnamenbereich + '" type="text"></td><td><a href="javascript:void(0);" onclick="Bereich_auswaehlen(forms.ArchivwerteDialogInhalt.tagnamenbereich, \'ArchivwerteDialogInhalt\');"><image src="./images/Zelle_ubern.png"></a></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[87] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht" selected>' + T_Text[48] + '</option><option value="senkrecht">' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht">' + T_Text[48] + '</option><option value="senkrecht" selected>' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[88] + '</td><td><input name="zeitstempel" value="" type="checkbox" ' + Zeitstempel + '></td><td style="text-align: right">' + T_Text[89] + '</td><td><input name="vt" value="vt" type="checkbox" ' + vt + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[90] + '</td><td><input name="unixzeit" value="" type="checkbox" ' + Unixzeit + '></td><td style="text-align: right">' + T_Text[91] + '</td><td><input name="vt_interpol" value="vt_interpol" type="checkbox" ' + vt_interpol + '></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Archivwerte_schreiben(\'' + Zelle.toUpperCase() + '\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'ArchivwerteDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '540 300',
		headerTitle: T_Text[99],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_Archivwerte_schreiben(Zelle) {
	var Formular = document.getElementById("ArchivwerteDialogInhalt");
	var Zeitstempel = "0";
	var Richtung = T_Text[49];
	var vt_interpol = "0";
	var vt = "0";
	var unixzeit = "0";
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	if (Formular.vt.checked == true){vt = "1";}
	if (Formular.vt_interpol.checked == true){vt_interpol = "1";}
	if (Formular.unixzeit.checked == true){unixzeit = "1";}
	Tabelle.jexcel[akt_Blatt].setValue(Zelle,'=AWERTE("' + Formular.tagnamenbereich.value + '","' + Formular.von.value + '","' + Formular.bis.value + '",' + Zeitstempel + ',' + vt + ',' + vt_interpol + ',' + unixzeit + ',"' + Formular.typ.value + '","' + Formular.richtung.value + '")');
	try {ArchivwerteDialog.close();} catch (err) {}
}

function AWERTE(TagZelle,von,bis,Zeitstempel,vt,vt_interpol,unixzeit,typ,Richtung) {
	if (Tabelle == undefined) {return;}
	var strReturn;
	if (TagZelle.substr(0,1) == "/") {
		Tagname = TagZelle;
	} else {
		Tagname = Tabelle.jexcel[akt_Blatt].getCell(TagZelle).innerHTML;
	}
	if (isNaN(parseInt(von.substr(0,1))) == true) {von = Tabelle.jexcel[akt_Blatt].getCell(von).innerHTML;}
	if (isNaN(parseInt(bis.substr(0,1))) == true) {bis = Tabelle.jexcel[akt_Blatt].getCell(bis).innerHTML;}
	jQuery.ajax({
		url: "DH_Archivwerte.php?Tagname=" + Tagname + "&von=" + von + "&bis=" + bis + "&typ=" + typ,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	var WertePaar;
	Zelle = Zellenbez_zu_Nummern(CELL());
   Zeile = Zelle[0] - 1;
   Spalte = Zelle[1];
	var S = Spalte;
	var Z = Zeile;
	var S1 = S;
	var Z1 = Z;
	if (Richtung == T_Text[49].toUpperCase() || Richtung == T_Text[49].toLowerCase() || Richtung == "senkrecht" || Richtung == "SENKRECHT") {
		Richtung = "senkrecht";
	} else {
		Richtung = "waagekrecht";
	}
	if (Zeitstempel == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, "Zeitstempel", 1);
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, "Zeitstempel", 1);
		}
	}
	if (vt == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, "vt", 1);
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, "vt", 1);
		}
	}
	if (vt_interpol == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, "vt interpoliert", 1);
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, "vt interpoliert", 1);
		}
	}
	if (unixzeit == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, "Unix Zeitstempel", 1);
		} else {
			Z1 = Z1 + 1;
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, "Unix Zeitstempel", 1);
		}
	}
  	Ergebnis = strReturn.split(";");
	Einheit_Ergebnis = Ergebnis[0];
	var Saetze = Ergebnis.length;
	for (Teil=1; Teil < Saetze; Teil++ ) {
  		if (Richtung == "senkrecht") {
  			Z = Z + 1;
  		} else {
			S = S + 1;
		}
		if (Z > Tabelle.jexcel[akt_Blatt].rows.length - 1) {Tabelle.jexcel[akt_Blatt].insertRow();}
		if (S > Tabelle.jexcel[akt_Blatt].colgroup.length - 1) {Tabelle.jexcel[akt_Blatt].insertColumn();}
		S1 = S;
		Z1 = Z;
		try {
			WertePaar = Ergebnis[Teil].split(",");
			Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z, WertePaar[1], 1);
			if (Zeitstempel == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, WertePaar[0], 1);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, WertePaar[0], 1);
				}
			}
			if (vt == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, WertePaar[4], 1);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, WertePaar[4], 1);
				}
			}
			if (vt_interpol == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, WertePaar[3], 1);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, WertePaar[3], 1);
				}
			}
			if (unixzeit == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S1, Z, WertePaar[2], 1);
				} else {
					Z1 = Z1 + 1;
					Tabelle.jexcel[akt_Blatt].setValueFromCoords(S, Z1, WertePaar[2], 1);
				}
			}
		}
		catch (err) {}
	}
	return typ + " " + T_Text[93] + " " + Einheit_Ergebnis;
}

function editieren() {
	try {editieren_Dialog.close();} catch (err) {}
	Zelle = Tabelle.jexcel[akt_Blatt].getValue(akt_Zelle_A);
	if (Zelle.substr(0,9) == "=AKT_WERT") {
		aktueller_Wert_Dialog();
	} else {
		if (Zelle.substr(0,4) == "=AW(") {
			Archivwert_Dialog();
		} else {
			if (Zelle.substr(0,3) == "=MW") {
				Mittelwert_Dialog();
			} else {
				if (Zelle.substr(0,7) == "=AWERTE") {
					Archivwerte_Werte_Dialog();
				} else {
					Inhalt = '<div><textarea id="inhalt_textarea" style="position: absolute; left: 10px; width: 580px; height: 65px;">' + Zelle + '</textarea></div><div><input type="button" onclick="editieren_uebernehmen();" value="übernehmen" id="editieren_fertig" style="position: absolute; left: 10px; top: 75px; height: 20px;"></div>';
					jsPanel.create({
						id: 'editieren_Dialog',
						theme: 'info',
						headerControls: {
							size: 'xs'
						},
						contentSize: '600 100',
						headerTitle: T_Text[157],
						position: 'left-top 50 100',
						content: Inhalt,
					});
				}
			}
		}
	}
}

function editieren_uebernehmen() {
	Tabelle.jexcel[akt_Blatt].setValue(akt_Zelle_A, document.getElementById("inhalt_textarea").value,1);
	try {editieren_Dialog.close();} catch (err) {}
}

function Tab_entfernen() {
	if (confirm('Wollen Sie das Tabellenblatt wirklich löschen?') ==  true) {
		if (document.getElementsByClassName("jexcel_tab_link").length == 1) {
			alert("Eine Tabelle muss mindestens vorhanden sein.");
			return 1;
		}
		document.getElementsByClassName("jexcel_tab_link selected")[0].parentElement.removeChild(document.getElementsByClassName("jexcel_tab_link selected")[0]);
		Tabs = document.getElementsByClassName("jexcel_tab_link");
		for (i = 0; i < Tabs.length; i++) {
			Tabs[i].setAttribute("data-spreadsheet", i);
		}
		Tabs[0].class = "jexcel_tab_link selected";
		Tabellenblaetter = document.getElementsByClassName("jexcel_tab jexcel_container");
		gel = 0;
		for (i = 0; i < Tabellenblaetter.length; i++) {
			if (Tabellenblaetter[i].style.display == "block") {
				Tabellenblaetter[i].parentElement.removeChild(Tabellenblaetter[i]);
				gel = 1;
			} else {
				Tabellenblaetter[i].setAttribute("tabindex", (i + gel).toString());
			}
		}
		Tabellenblaetter[0].style.display = "block";
	}
}

function neu_berechnen() {
	Z = 0;
	var Zeilen = Tabelle.jexcel[akt_Blatt].options.data[Z].length; 
	while (Z < Zeilen) {
		S = 0;
		while (S <= Tabelle.jexcel[akt_Blatt].options.data[Z].length) {
			try{
				if (Tabelle.jexcel[akt_Blatt].options.data[Z][S].length > 0){
					if (Tabelle.jexcel[akt_Blatt].options.data[Z][S].substr(0,1) == "=") {
						Spalte = S;
						Zeile = Z;
						Tabelle.jexcel[akt_Blatt].updateCell(S,Z,Tabelle.jexcel[akt_Blatt].getValueFromCoords(S,Z));
					}
				}
			} catch (err) {}
			S = S + 1;
		}
		Z = Z + 1;
	}
}

function html_speichern(){
	var htmlInhalt = '<html><style type="text/css">td {border: 1px;border-style: dotted;border-color: #000000;}</style>' + Tabelle.jexcel[akt_Blatt].el.childNodes[1].firstChild.outerHTML + '</html>';
//	htmlInhalt = htmlInhalt.replace(/<tr.*?>/g, "<tr>")
//	htmlInhalt = htmlInhalt.replace(/<td.*?style/g, "<td style")
	var pom = document.createElement('a');
	var blob = new Blob([htmlInhalt], {type: 'text/html;charset=ISO-8859-1;'});
  	var url = URL.createObjectURL(blob);
	pom.href = url;
	pom.setAttribute('download', 'Kalk.html');
	document.body.appendChild(pom);
	pom.click();
	pom.parentNode.removeChild(pom);
}

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["Formular"].aktion.value = "edit_Tabkalk_speichern";
	} else {
		document.forms["Formular"].aktion.value = "Hist_löschen";
	}
	document.forms["Formular"].submit();
	document.getElementById("timestamp").value = "";
	document.getElementById("timestamp1").value = "";
	document.forms["Formular"].aktion.value = "";
	document.forms["Formular"].submit();
}

function Vers_zeigen() {
	document.getElementById("timestamp").value = document.getElementById("timestamp1").value;
	document.forms["Formular"].action = "Tabellenkalk.php";
	document.forms["Formular"].target = "Hauptrahmen";
	document.forms["Formular"].submit();
}

function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("dokument_nav").style.display == "block") {
			document.getElementById("dokument_nav").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("dokument_nav").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("dokument_nav").style.display = "none";
		document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 2) {
		if (document.getElementById("spalte_zeilen_nav").style.display == "block") {
			document.getElementById("spalte_zeilen_nav").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("spalte_zeilen_nav").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("spalte_zeilen_nav").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 3) {
		if (document.getElementById("format_nav").style.display == "block") {
			document.getElementById("format_nav").style.display = "none"
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("format_nav").style.display = "block"
			document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("format_nav").style.display = "none";
		document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 4) {
		if (document.getElementById("dh_funktionen_nav").style.display == "block") {
			document.getElementById("dh_funktionen_nav").style.display = "none"
			document.getElementById("schaltfl_4").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("dh_funktionen_nav").style.display = "block"
			document.getElementById("schaltfl_4").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("dh_funktionen_nav").style.display = "none";
		document.getElementById("schaltfl_4").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 5) {
		if (document.getElementById("sonstiges_nav").style.display == "block") {
			document.getElementById("sonstiges_nav").style.display = "none"
			document.getElementById("schaltfl_5").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("sonstiges_nav").style.display = "block"
			document.getElementById("schaltfl_5").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("sonstiges_nav").style.display = "none";
		document.getElementById("schaltfl_5").style.backgroundColor = "#FCEDD9";
	}
}

function Spalte_einfuegen(Richtung) {
	if (Tabelle.jexcel[akt_Blatt].colgroup.length < 1000) {Tabelle.jexcel[akt_Blatt].insertColumn(1, parseInt(akt_Zelle_C.substr(0, akt_Zelle_C.indexOf(","))), Richtung, null);}
}

function Spalte_entfernen() {
	Tabelle.jexcel[akt_Blatt].deleteColumn(parseInt(akt_Zelle_C.substr(0, akt_Zelle_C.indexOf(","))),1);
}

function Zeile_einfuegen(Richtung) {
	Tabelle.jexcel[akt_Blatt].insertRow(1, parseInt(akt_Zelle_C.substr(akt_Zelle_C.indexOf(",") + 1, akt_Zelle_C.length)), Richtung);
}

function Zeile_entfernen() {
	Tabelle.jexcel[akt_Blatt].deleteRow(parseInt(akt_Zelle_C.substr(akt_Zelle_C.indexOf(",") + 1, akt_Zelle_C.length)), 1);
}

function Menu(M) {
	try {MenuDialog.close();} catch (err) {}
	rahmenbereich = "Zelle";
	var Inhalt = "<div style='position: absolute; top: 10px;left: 10px;' font-family: arial, helvetica, sans-serif; font-size: 12px;><table><tr style='height: 30px;'>";
	if (M == T_Text[114]) {
		Groesse = "176 200";
		Inhalt = Inhalt + '<td><input type="button" style="width: 150px;" value="' + T_Text[110] + '" onclick="add();"></td></tr>';
		Inhalt = Inhalt + '<tr style="height: 30px;"><td><input type="button" style="width: 150px;" value="' + T_Text[113] + '" onclick="Tab_entfernen();"></td></tr>';
		Inhalt = Inhalt + '<tr style="height: 30px;"><td><input type="button" style="width: 150px;" value="' + T_Text[109] + '" onclick="Tabelle.jexcel[akt_Blatt].download();"></td></tr>';
		Inhalt = Inhalt + '<tr style="height: 30px;"><td><input type="button" style="width: 150px;" value="' + T_Text[111] + '" onclick="html_speichern();"></td></tr>';
		Inhalt = Inhalt + '<tr style="height: 30px;"><td><input type="button" style="width: 150px;" name="berechnen" value="' + T_Text[2] + '" onclick="neu_berechnen();"</td></tr>';
	}
	if (M == T_Text[147]) {
		Inhalt = Inhalt + "<td><input type='button' style='width: 150px;' onclick='Spalte_einfuegen(0);'  value='" + T_Text[8] + "'></td>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' onclick='Spalte_einfuegen(1);' value='" + T_Text[9] + "'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' onclick='Spalte_entfernen();' value='" + T_Text[10] + "'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[154] + "' onclick='Spalte_verbergen();'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[155] + "' onclick='Spalte_zeigen();'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[165] + "' onclick='freeze(0);'></td></tr>";
		Groesse = "176 220";
	}
	if (M == T_Text[148]) {
		Inhalt = Inhalt + "<td><input type='button' style='width: 150px;' onclick='Zeile_einfuegen(1);' value='" + T_Text[11] + "'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' onclick='Zeile_einfuegen(0);' value='" + T_Text[12] + "'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' onclick='Zeile_entfernen();' value='" + T_Text[13] + "'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[160] + "' onclick='Zeile_verbergen();'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[156] + "' onclick='Zeile_zeigen();'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td><input type='button' style='width: 150px;' value='" + T_Text[164] + "' onclick='freeze(1);'></td></tr>";
		Groesse = "176 220";
	}
	if (M == T_Text[115]) {
		Schriftliste = "<option value='arial, helvetica, sans-serif' selected>" + T_Text[126] + "</option>";
		Schriftliste = Schriftliste + "<option value='roman, \"times new roman\", times, serif'>" + T_Text[127] + "</option>";
		Schriftliste = Schriftliste + "<option value='courier, fixed, monospace'>" + T_Text[128] + "</option>";
		Schriftliste = Schriftliste + "<option value='western, fantasy'>" + T_Text[129] + "</option>";
		Schriftliste = Schriftliste + "<option value='Zapf-Chancery, cursive'>" + T_Text[130] + "</option>";
		Schriftliste = Schriftliste + "<option value='serif'>" + T_Text[131] + "</option>";
		Schriftliste = Schriftliste + "<option value='sans-serif'>" + T_Text[132] + "</option>";
		Schriftliste = Schriftliste + "<option value='cursive'>" + T_Text[133] + "</option>";
		Schriftliste = Schriftliste + "<option value='fantasy'>" + T_Text[134] + "</option>";
		Schriftliste = Schriftliste + "<option value='monospace'>" + T_Text[135] + "</option>";
		Schriftbreite = "<option value='' selected></option>";
		Schriftbreite = Schriftbreite + "<option value='normal'>" + T_Text[136] + "</option>";
		Schriftbreite = Schriftbreite + "<option value='bold'>" + T_Text[137] + "</option>";
		Schriftbreite = Schriftbreite + "<option value='bolder'>" + T_Text[138] + "</option>";
		Schriftbreite = Schriftbreite + "<option value='lighter'>" + T_Text[139] + "</option>";
		Ausrichtung = "<option value='' selected></option>";
		Ausrichtung = Ausrichtung + "<option value='left'>" + T_Text[143] + "</option>";
		Ausrichtung = Ausrichtung + "<option value='center'>" + T_Text[144] + "</option>";
		Ausrichtung = Ausrichtung + "<option value='right'>" + T_Text[145] + "</option>";
		Inhalt = Inhalt + "<td align='right'>" + T_Text[14] + ":</td><td align='left'><input id='shintergrundfarbe' type='color' style='height: 24px;' value='#FFFFFF' style='width:40px;' onchange='zeichen_formatieren(\"shintergrundfarbe\");'></td></tr>";
  		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[15] + ":</td><td align='left'><input id='sschriftfarbe' type='color' style='height: 24px;' value='#000000' style='width:40px;' onchange='zeichen_formatieren(\"sschriftfarbe\");'></td></tr>";
  		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right' style='padding: 5px; height: 24px:'>" + T_Text[18] + "</td><td><input id='skursiv' class='Text_Element' type='checkbox' onchange='zeichen_formatieren(\"skursiv\");'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[140] + "</td><td><select id='sschriftart' style='height: 22px;' onchange='zeichen_formatieren(\"sschriftart\");'>" + Schriftliste + "</select></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[141] + "</td><td><select id='sschriftbreite' style='height: 22px;' onchange='zeichen_formatieren(\"sschriftbreite\");'>" + Schriftbreite + "</select></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right' style='padding: 5px; height: 22px;'>" + T_Text[142] + "</td><td><input id='sschriftgroesse' class='Text_Element' type='number' style='width: 48px; height: 22px;' value='14' onchange='zeichen_formatieren(\"sschriftgroesse\");'> px</td>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[146] + "</td><td><select id='sausrichtung' onchange='zeichen_formatieren(\"sausrichtung\");'>" + Ausrichtung + "</select></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 50px; vertical-align: bottom;'><td align='center'><input type='button' class='Schalter_fett_Element' style='width: 120px; text-align: center;' value='" + T_Text[116] + "' onclick='zeichen_formatieren();'></td>";
		Inhalt = Inhalt + "<td align='center'><input class='Schalter_fett_Element' style='width: 120px; text-align: center;' type='button' value='" + T_Text[107] + "' onclick='MenuDialog.close();'></td></tr>";
	  	Groesse = "395 300";
  		rahmenbereich = "Zelle";
	}
	if (M == T_Text[20]) {
		Groesse = "515 220";
		Rahmenliste = "<option value='solid' selected>" + T_Text[118] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='dotted'>" + T_Text[119] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='dashed'>" + T_Text[120] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='double'>" + T_Text[121] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='groove'>" + T_Text[122] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='ridge'>" + T_Text[123] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='inset'>" + T_Text[124] + "</option>";
		Rahmenliste = Rahmenliste + "<option value='outset'>" + T_Text[125] + "</option>";

		rahmenbereich = "Zelle";
		Inhalt = Inhalt + "<td align='right'>" + T_Text[22] + "</td><td><input id='ikomplett' type='checkbox'></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='ibkomplett' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='irkomplett' size='1'>" + Rahmenliste + "</select></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifkomplett' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[23] + "</td><td><input id='ioben' type='checkbox'></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='iboben' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='iroben' size='1'>" + Rahmenliste + "</select></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifoben' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[24] + "</td><td><input id='irechts' type='checkbox'></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='ibrechts' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='irrechts' size='1'>" + Rahmenliste + "</select></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifrechts' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[25] + "</td><td><input id='iunten' type='checkbox'></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='ibunten' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='irunten' size='1'>" + Rahmenliste + "</select></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifunten' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[26] + "</td><td><input id='ilinks' type='checkbox'></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='iblinks' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='irlinks' size='1'>" + Rahmenliste + "</select></td>";
		Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='iflinks' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		if (akt_Ber_C[0] !== akt_Ber_C[2] || akt_Ber_C[1] != akt_Ber_C[3]) {
			//Bereich markiert
			Groesse = "515 290";
			rahmenbereich = "Bereich";
			Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[27] + "</td><td><input id='iinnen' type='checkbox'></td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='ibinnen' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='irinnen' size='1'>" + Rahmenliste + "</select></td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifinnen' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
			Inhalt = Inhalt + "<tr style='height: 30px;'><td align='right'>" + T_Text[28] + "</td><td><input id='iaussen' type='checkbox'></td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[32] + "</td><td><input id='ibaussen' class='Text_Element' type='number' style='width: 48px;' value='1'> px</td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[117] + "</td><td><select class='Text_Element' style='height: 22px;' id='iraussen' size='1'>" + Rahmenliste + "</select></td>";
			Inhalt = Inhalt + "<td align='right' style='padding: 5px;'>" + T_Text[31] + "</td><td><input id='ifaussen' type='color' style='height: 24px' name='Farbauswahl_Rahmen' value='#000000'></td></tr>";
		}
		Inhalt = Inhalt + "<tr style='height: 40px;'><td align='center' colspan='3'><input class='Schalter_fett_Element' style='width: 120px; text-align: center;' type='button' value='" + T_Text[152] + "' onclick='rahmen_entf();'></td>";
		Inhalt = Inhalt + "<td align='center' colspan='3'><input type='button' class='Schalter_fett_Element' style='width: 120px; text-align: center;' value='" + T_Text[116] + "' onclick='formatieren();'></td>";
		Inhalt = Inhalt + "<td align='center' colspan='3'><input class='Schalter_fett_Element' style='width: 120px; text-align: center;' type='button' value='" + T_Text[107] + "' onclick='MenuDialog.close();'></td></tr>";
	}
	Inhalt = Inhalt + '</table></div>';
	Inhalt = Inhalt + "<input id='rahmenbereich' type='hidden' value='" + rahmenbereich + "'>";
	jsPanel.create({
		id: 'MenuDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: Groesse,
		position: 'left-top 50 100',
		headerTitle: 'Menu ' + M,
		content:  Inhalt,
	});
}

function rahmen_entf() {
	document.getElementById('ikomplett').checked = false;
	document.getElementById('ioben').checked = false;
	document.getElementById('irechts').checked = false;
	document.getElementById('iunten').checked = false;
	document.getElementById('ilinks').checked = false;
	try {document.getElementById('iinnen').checked = false;} catch (err) {}
	try {document.getElementById('iaussen').checked = false;} catch (err) {}
	formatieren();
}

function formatieren() {
	Bereich = document.getElementById('rahmenbereich').value;
	if (Bereich == "Zelle") {
		Koord = akt_Zelle_C.split(",");
		Spalte = Koord[0];
		Zeile = (parseInt(Koord[1]) + 1).toString();
		Spalte1 = (parseInt(Spalte) + 1).toString();
		Zeile1 = (parseInt(Zeile) - 1).toString();
		Tabelle.jexcel[akt_Blatt].tbody.rows[Zeile1].cells[Spalte1].style.borderStyle = "";
		Tabelle.jexcel[akt_Blatt].tbody.rows[Zeile1].cells[Spalte1].style.borderWidth = "";
		Tabelle.jexcel[akt_Blatt].tbody.rows[Zeile1].cells[Spalte1].style.borderColor = "";
		if (document.getElementById('ikomplett').checked == true) {
			if (document.getElementById('ibkomplett').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border",document.getElementById('ibkomplett').value + "px " + document.getElementById('irkomplett').value + " " + document.getElementById('ifkomplett').value);
			}
		}
		if (document.getElementById('ioben').checked == true) {
			if (document.getElementById('iboben').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-top",document.getElementById('iboben').value + "px " + document.getElementById('iroben').value + " " + document.getElementById('ifoben').value);
			}
		}
		if (document.getElementById('irechts').checked == true) {
			if (document.getElementById('ibrechts').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-right",document.getElementById('ibrechts').value + "px " + document.getElementById('irrechts').value + " " + document.getElementById('ifrechts').value);
			}
		}
		if (document.getElementById('iunten').checked == true) {
			if (document.getElementById('ibunten').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-bottom",document.getElementById('ibunten').value + "px " + document.getElementById('irunten').value + " " + document.getElementById('ifunten').value);
			}
		}
		if (document.getElementById('ilinks').checked == true) {
			if (document.getElementById('iblinks').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-left",document.getElementById('iblinks').value + "px " + document.getElementById('irlinks').value + " " + document.getElementById('iflinks').value);
			}	
		}
	} else {
		Spalte = akt_Ber_C[0];
		Zeile = (parseInt(akt_Ber_C[1]) + 1).toString();
		Spalte2 = akt_Ber_C[2];
		Zeile2 = (parseInt(akt_Ber_C[3]) + 1).toString();
		//leeren
		for (z = parseInt(Zeile) - 1; z <= parseInt(Zeile2) - 1; z++) {
			for (i = parseInt(Spalte) + 1; i <= parseInt(Spalte2) + 1; i++) {
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderBottomStyle = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderBottomWidth = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderBottomColor = ""

				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderTopStyle = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderTopWidth = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderTopColor = ""

				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderRightStyle = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderRightWidth = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderRightColor = ""

				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderLeftStyle = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderLeftWidth = ""
				Tabelle.jexcel[akt_Blatt].tbody.rows[z].cells[i].style.borderLeftColor = ""
			}
		}
		//Ende leeren
		if (document.getElementById('ikomplett').checked == true) {
			document.getElementById('iraussen').value = document.getElementById('irkomplett').value;
			document.getElementById('ibaussen').value = document.getElementById('ibkomplett').value;
			document.getElementById('ifaussen').value = document.getElementById('ifkomplett').value;
			faussen();
			document.getElementById('irinnen').value = document.getElementById('irkomplett').value;
			document.getElementById('ibinnen').value = document.getElementById('ibkomplett').value;
			document.getElementById('ifinnen').value = document.getElementById('ifkomplett').value;
			finnen();
		}
		if (document.getElementById('iinnen').checked == true) {
			finnen();
		}
		if (document.getElementById('iaussen').checked == true) {
			faussen();
		}
		if (document.getElementById('iunten').checked == true) {
			funten();
		}
		if (document.getElementById('ioben').checked == true) {
			foben();
		}
		if (document.getElementById('ilinks').checked == true) {
			flinks();
		}
		if (document.getElementById('irechts').checked == true) {
			frechts();
		}		

		function faussen() {
			if (document.getElementById('ibaussen').value != "0") {
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-top",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"border-left",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte2) + Zeile2.toString(),"border-bottom",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte2) + Zeile2.toString(),"border-right",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte2) + Zeile.toString(),"border-top",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte2) + Zeile.toString(),"border-right",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile2.toString(),"border-bottom",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile2.toString(),"border-left",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				for (i = parseInt(Zeile) + 1; i < parseInt(Zeile2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + i.toString(),"border-left",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte2) + i.toString(),"border-right",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				}
				for (i = parseInt(Spalte) + 1; i < parseInt(Spalte2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + Zeile.toString(),"border-top",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + Zeile2.toString(),"border-bottom",document.getElementById('ibaussen').value + "px " + document.getElementById('iraussen').value + " " + document.getElementById('ifaussen').value);
				}
			}
		}
		
		function finnen() {
			for (z = parseInt(Zeile); z <= parseInt(Zeile2); z++) {
				for (i = parseInt(Spalte); i <= parseInt(Spalte2); i++) {
					if (z < parseInt(Zeile2)) {
						Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-bottom",document.getElementById('ibinnen').value + "px " + document.getElementById('irinnen').value + " " + document.getElementById('ifinnen').value);
					}
					if (i < parseInt(Spalte2)) {
						Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-right",document.getElementById('ibinnen').value + "px " + document.getElementById('irinnen').value + " " + document.getElementById('ifinnen').value);
					}
				}
			}
		}
		
		function funten() {
			for (z = parseInt(Zeile); z <= parseInt(Zeile2); z++) {
				for (i = parseInt(Spalte); i <= parseInt(Spalte2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-bottom",document.getElementById('ibunten').value + "px " + document.getElementById('irunten').value + " " + document.getElementById('ifunten').value);
				}
			}
		}
		
		function foben() {
			for (z = parseInt(Zeile); z <= parseInt(Zeile2); z++) {
				for (i = parseInt(Spalte); i <= parseInt(Spalte2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-top",document.getElementById('iboben').value + "px " + document.getElementById('iroben').value + " " + document.getElementById('ifoben').value);
				}
			}
		}
		
		function flinks() {
			for (z = parseInt(Zeile); z <= parseInt(Zeile2); z++) {
				for (i = parseInt(Spalte); i <= parseInt(Spalte2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-left",document.getElementById('iblinks').value + "px " + document.getElementById('irlinks').value + " " + document.getElementById('iflinks').value);
				}
			}
		}
		
		function frechts() {
			for (z = parseInt(Zeile); z <= parseInt(Zeile2); z++) {
				for (i = parseInt(Spalte); i <= parseInt(Spalte2); i++) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(i) + z.toString(),"border-right",document.getElementById('ibrechts').value + "px " + document.getElementById('irrechts').value + " " + document.getElementById('ifrechts').value);
				}
			}
		}
	}
	 
//	Tabelle.jexcel[akt_Blatt].setStyle(akt_Zelle_A, 'border', 'solid 1px grey');
	//Tabelle.jexcel[akt_Blatt].tbody.rows[2].cells[3].style   -->  Auf der Konsole testen.
}

function zeichen_formatieren(Feld) {
	Bereich = "Bereich";
	if (akt_Ber_C[0] == akt_Ber_C[2] && akt_Ber_C[1] == akt_Ber_C[3]) {Bereich = "Zelle";}
	Startspalte = akt_Ber_C[0];
	Spalte = Startspalte;
	Endespalte = akt_Ber_C[2];
	Startzeile = akt_Ber_C[1] + 1;
	Zeile = Startzeile;
	Endezeile = akt_Ber_C[3] + 1;
	while (Spalte <= Endespalte) {
		while (Zeile <= Endezeile) {
			if (Feld == undefined || Feld == 'shintergrundfarbe') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"background-color",document.getElementById('shintergrundfarbe').value);} 
			if (Feld == undefined || Feld == 'sschriftfarbe') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"color",document.getElementById('sschriftfarbe').value);}
			if (Feld == undefined || Feld == 'skursiv') {
				if (document.getElementById('skursiv').checked == true) {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"font-style","italic");
				} else {
					Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"font-style","");
				}
			}
			if (Feld == undefined || Feld == 'sschriftart') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"font-family",document.getElementById('sschriftart').value);}
			if (Feld == undefined || Feld == 'sschriftbreite') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"font-weight",document.getElementById('sschriftbreite').value);}
			if (Feld == undefined || Feld == 'sschriftgroesse') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"font-size",document.getElementById('sschriftgroesse').value);}
			if (Feld == undefined || Feld == 'sausrichtung') {Tabelle.jexcel[akt_Blatt].setStyle(Tabelle.jexcel[akt_Blatt].getHeader(Spalte) + Zeile.toString(),"text-align",document.getElementById('sausrichtung').value);}
			Zeile = Zeile + 1;
		}
		Zeile = Startzeile;
		Spalte = Spalte + 1;
	}
}

function Zellen_verbinden() {
	Spalten = akt_Ber_C[2] - akt_Ber_C[0] + 1;
	Zeilen = akt_Ber_C[3] - akt_Ber_C[1] + 1;
	Tabelle.jexcel[akt_Blatt].setMerge(akt_Zelle_A, Spalten, Zeilen)	
}

function Zellverbindung_aufheben() {
	Tabelle.jexcel[akt_Blatt].removeMerge(akt_Zelle_A);	
}

function Spalte_verbergen() {
	Start = akt_Ber_C[0];
	Ende = akt_Ber_C[2];
	for (i = Start; i <= Ende; i++) {Tabelle.jexcel[akt_Blatt].hideColumn(i);} 
}

function Spalte_zeigen() {
	Start = akt_Ber_C[0];
	Ende = akt_Ber_C[2];
	if (Ende < Tabelle.jexcel[akt_Blatt].colgroup.length) {
		if (Tabelle.jexcel[akt_Blatt].colgroup[Ende + 1].style.display == "none") {Ende = Ende + 1;}
	}
	if (Start > 0) {
		if (Tabelle.jexcel[akt_Blatt].colgroup[Start - 1].style.display == "none") {Start = Start - 1;}
	}
	for (i = Start; i <= Ende; i++) {Tabelle.jexcel[akt_Blatt].showColumn(i);} 
}

function Zeile_verbergen() {
	Start = akt_Ber_C[1];
	Ende = akt_Ber_C[3];
	for (i = Start; i <= Ende; i++) {Tabelle.jexcel[akt_Blatt].hideRow(i);} 
}

function Zeile_zeigen() {
	Start = akt_Ber_C[1];
	Ende = akt_Ber_C[3];
	if (Ende < Tabelle.jexcel[akt_Blatt].rows.length) {
		if (Tabelle.jexcel[akt_Blatt].rows[Ende + 1].style.display == "none") {Ende = Ende + 1;}
	}
	if (Start > 0) {
		if (Tabelle.jexcel[akt_Blatt].rows[Start - 1].style.display == "none") {Start = Start - 1;}
	}
	for (i = Start; i <= Ende; i++) {Tabelle.jexcel[akt_Blatt].showRow(i);} 
}

function kommentar() {
	try {kommentar_Dialog.close();} catch (err) {}
	Inhalt = '<div><textarea id="inhalt_textarea" style="position: relative; top: 10px; left: 10px; width: 580px; height: 65px;">' + Tabelle.jexcel[akt_Blatt].getComments(akt_Zelle_A) + '</textarea></div>';
	Inhalt = Inhalt + '<div style="position: relative; left: 10px; top: 15px;"><table width="100%"><tr><td align="center"><input type="button" onclick="kommentar_uebernehmen(0);" value="' + T_Text[116] + '" id="editieren_fertig" style="height: 20px;"></td>';
	Inhalt = Inhalt + '<td align="center"><input type="button" onclick="kommentar_uebernehmen(1);" value="' + T_Text[21] + '" id="editieren_fertig" style="height: 20px;"></td>';
	Inhalt = Inhalt + '<td align="center" ><input type="button" onclick="kommentar_Dialog.close();" value="' + T_Text[107] + '" id="editieren_fertig" style="height: 20px;"></td></tr></table></div>';
	jsPanel.create({
		id: 'kommentar_Dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '600 125',
		headerTitle: T_Text[158],
		position: 'left-top 50 100',
		content: Inhalt,
	});
}

function kommentar_uebernehmen(Modus) {
	if (Modus == 0) {
		Tabelle.jexcel[akt_Blatt].setComments(akt_Zelle_A, document.getElementById("inhalt_textarea").value);
	} else {
		Tabelle.jexcel[akt_Blatt].setComments(akt_Zelle_A, "");
	}
	try {kommentar_Dialog.close();} catch (err) {}
}

function filterschaltung() {
	if (Tabelle.jexcel[akt_Blatt].filter.style.display == "contents" || Tabelle.jexcel[akt_Blatt].filter.style.display == "") {
		Tabelle.jexcel[akt_Blatt].resetFilters();
		Tabelle.jexcel[akt_Blatt].filter.style.display = "none";
	} else {
		Tabelle.jexcel[akt_Blatt].filter.style.display = "contents";
	}
}

jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
var Tabelle = document.getElementById('spreadsheet');
for (i = 0; i < Blatt.length; i++) {
	if (Object.keys(Kommentare[i]).length > 0) {
		for (x = 0; x < Object.keys(Kommentare[i]).length; x++) {
			Tabelle.jexcel[i].setComments(Object.keys(Kommentare[i])[x],Object.values(Kommentare[i])[x]);
		}
	}
}

function freeze(vh) {
	SZ = akt_Zelle_C.split(",");
	var obj = Tabelle.jexcel[akt_Blatt];
	for (var i = 0; i < obj.colgroup.length; i++) {
		obj.headers[i].classList.remove('jexcel_freezed');
		obj.headers[i].style.left = '';
		obj.rows[i].classList.remove('jexcel_freezed');
		obj.rows[i].style.top = '';
		for (var j = 0; j < obj.rows.length; j++) {
			if (obj.records[i][j]) {
				obj.records[i][j].classList.remove('jexcel_freezed');
				obj.records[i][j].style.left = '';
				obj.records[i][j].style.top = '';
			}
		}
	}
	Tabelle.jexcel[akt_Blatt].options.freezeColumns = 0;
	Tabelle.jexcel[akt_Blatt].options.freezeRows = 0;
	if (vh == 1) { 
		Tabelle.jexcel[akt_Blatt].options.freezeRows = SZ[1];
	} else {
		Tabelle.jexcel[akt_Blatt].options.freezeColumns = SZ[0];
	}
}





