var T_Text = new Array;
var panel;
$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	var Zeilen = document.Formular.Zeilen.value;
	if (Zeilen == "") {Zeilen = 25;}
	Zeilen = parseInt(Zeilen); 
	var Spalten = document.Formular.Spalten.value;
	if (Spalten == "") {Spalten = 26;}
	Spalten = parseInt(Spalten); 
	$('#Tab').ip_Grid({ rows: Zeilen,  cols: Spalten });
	var Zelle=[];
	var Zellen=[];
	if (document.Formular.Blatt.value.length > 0) {
		Zellen=document.Formular.Blatt.value.split("</Zelle><Zelle>");
		for (z = 0; z < Zellen.length; z++) {
			Zelle[z]= Zellen[z].split("<Prop>");
		}
		for (z = 0; z < Zellen.length; z++) {
			if (Zelle[z][2] !="undefined") {$('#Tab').ip_FormatCell({style:Zelle[z][2], row: Zelle[z][0], col: Zelle[z][1]});}
			if (Zelle[z][4] !="undefined") {$('#Tab').ip_CellInput({valueRAW: Zelle[z][4], row: Zelle[z][0], col: Zelle[z][1] });}
			if (Zelle[z][3] !="undefined") {$('#Tab').ip_CellInput({valueRAW: Zelle[z][3], row: Zelle[z][0], col: Zelle[z][1]});}
			if (Zelle[z][5] !="undefined") {ip_GridProps["Tab"].rowData[Zelle[z][0]].cells[Zelle[z][1]].mask = Zelle[z][5];}
		}
	}
	ip_GridProps["Tab"].colData = JSON.parse(document.getElementById("spaltenformate").innerHTML);
	ip_ResizeGrid("Tab");
});

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["Formular"].aktion.value = T_Text[7];
	} else {
		document.forms["Formular"].aktion.value = "Hist_löschen";
	}
	schreiben();
	document.forms["Formular"].submit();
}

function Vers_zeigen() {
	document.forms["Formular"].action = "IP_Grid.php";
	document.forms["Formular"].target = "Hauptrahmen";
	document.forms["Formular"].submit();
	document.forms["phpform"].action = "Baum2.php";
	document.forms["phpform"].target = "Baum";
}

function neu_berechnen(){
	ip_ReCalculateFormulas('Tab',{range: Array({startRow: 0, startCol: 0, endRow: ip_GridProps.Tab.rows - 1, endCol: ip_GridProps.Tab.cols - 1})});
}

function neue_Spalte() {
	$('#Tab').ip_AddCol({count: 1});
	Spalten_benennen();
}

function Spalten_benennen() {
	var Spalten = ip_GridProps.Tab.cols;
	if (Spalten > 26){
		var Wiederholungen = parseInt((Spalten - 1)/26);
		var Ind = (Spalten - 1 - (Wiederholungen * 26)).toString();
		Ind = ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Ind];
		Wiederholungen = ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Wiederholungen - 1];
		ip_GridProps.Tab.indexedData.colSymbols.colSymbols.push(Wiederholungen.toString() + Ind);
		ip_GridProps.Tab.indexedData.colSymbols.symbolCols[Wiederholungen.toString() + Ind] = Spalten - 1;
	}
}

function schreiben() {
	var Text="";
	for (z = 0; z < ip_GridProps["Tab"].rowData.length; z++) {
		for (s = 0; s < ip_GridProps["Tab"].rowData[z].cells.length; s++) {
			if (ip_GridProps["Tab"].rowData[z].cells[s].display != "") {
				Text+= "<Zelle>" + z + "<Prop>" + s + "<Prop>" + ip_GridProps["Tab"].rowData[z].cells[s].style;
				Text+="<Prop>" + ip_GridProps["Tab"].rowData[z].cells[s].formula;
				Text+="<Prop>" + ip_GridProps["Tab"].rowData[z].cells[s].display;
				Text+="<Prop>" + ip_GridProps["Tab"].rowData[z].cells[s].mask;
				Text+="</Zelle>";
			}					
		}
	}
	document.Formular.Blatt.value = Text.substr(7,Text.length-15);
	document.Formular.Zeilen.value = ip_GridProps["Tab"].rows;
	document.Formular.Spalten.value = ip_GridProps["Tab"].cols;
	document.Formular.spaltenformate.value = JSON.stringify(ip_GridProps["Tab"].colData);
}

function Spalte_einfuegen(Richtung) {
	var Optionen = {};
	var Spalte = ip_GridProps.Tab.selectedColumn[0];
	if (Spalte == undefined){
		Spalte = ip_GridProps.Tab.selectedCell.col;
	}
	Optionen.col = Spalte;
	Optionen.appendTo = Richtung;
	Optionen.count = 1;
	$('#Tab').ip_InsertCol(Optionen);
	Spalten_benennen();
}

function Spalte_entfernen() {
	var Optionen = {};
	var Spalte = ip_GridProps.Tab.selectedColumn[0];
	if (Spalte == "undefined"){
		Spalte = ip_GridProps.Tab.selectedCell.col;
	}
	Optionen.col = Spalte;
	Optionen.mode = 'destroy';
	Optionen.count = 1;
	$('#Tab').ip_RemoveCol(Optionen);
}

function Zeile_einfuegen(Richtung) {
	var Optionen = {};
	var Zeile = ip_GridProps.Tab.selectedRow[0];
	if (Zeile == undefined){
		Zeile = ip_GridProps.Tab.selectedCell.row;
	}
	Optionen.row = Zeile;
	Optionen.appendTo = Richtung;
	Optionen.count = 1;
	$('#Tab').ip_InsertRow(Optionen);
}

function Zeile_entfernen() {
	var Optionen = {}; 
	var Zeile = ip_GridProps.Tab.selectedRow[0];
	if (Zeile == undefined){
		Zeile = ip_GridProps.Tab.selectedCell.row;
	}
	Optionen.row = Zeile;
	Optionen.mode = 'destroy';
	Optionen.count = 1;
	$('#Tab').ip_RemoveRow(Optionen);
}

function Zelle_Hintergrund_Farbe() {
	$('#Tab').ip_FormatCell({style:"background-color:#" + document.Formular.Farbauswahl_Hintergrund.value + ";"})
}

function Zelle_Schriftfarbe() {
	$('#Tab').ip_FormatCell({style:"color:#" + document.Formular.Farbauswahl_Schrift.value + ";"})
}

function formatieren(Eigenschaft,Art) {
	var Optionen = {};
	Optionen.style = Eigenschaft + Art;
	$('#Tab').ip_FormatCell(Optionen);
}

function Rahmen(Eigenschaft,Art) {
	var Optionen = {};
	if (Eigenschaft == "borderPlacement") {Optionen.borderPlacement =  Art;
	} else {
		Optionen.borderPlacement =  "all";
	}
	if (Eigenschaft == "borderColor") {Optionen.borderColor =  Art;}
	if (Eigenschaft == "borderSize") {Optionen.borderSize =  Art;}
	$('#Tab').ip_Border(Optionen);
}

function akt_Wert(Grid_ID,Zeile,Spalte,TagZelle,Zeitstempel,Einheit,Richtung) {
	var strReturn;
	//var Tagname = ip_CellData("Tab",TagZelle.startRow,TagZelle.startCol).value;
	var Tagname = TagZelle;
	jQuery.ajax({
		url: "akt_Wert.php?Tagname=" + Tagname,
		success: function (html) {
			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = T_Text[48];}
  	Ergebnis = strReturn.split(",");
	if (Zeitstempel == 1){Attribute_Schreiben(Ergebnis[1]);}
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[2]);}
  	ip_ResizeGrid("Tab");
	return Ergebnis[0];
   
	function Attribute_Schreiben(Wert) {
		if (Richtung == T_Text[49]) {Zeile = Zeile + 1;}
		if (Richtung == T_Text[48]) {Spalte = Spalte + 1;}
		if (Zeile >= ip_GridProps.Tab.rows) {$('#Tab').ip_AddRow({count: 1});}
		if (Spalte >= ip_GridProps.Tab.cols) {neue_Spalte();}
		try {
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataTypeName="string"
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataType="string"
			ip_SetValue("Tab",Zeile,Spalte,Wert);
		}
		catch (err) {}
	}
}

function Archivwerte(Grid_ID,Zeile,Spalte,TagZelle,von,bis,Zeitstempel,vt,vt_interpol,unixzeit,typ,Richtung) {
	var strReturn;
	//var Tagname = ip_CellData("Tab",TagZelle.startRow,TagZelle.startCol).value;
	var Tagname = TagZelle;
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
			ip_SetValue("Tab",Z,S1,"Zeitstempel");
		} else {
			Z1 = Z1 + 1;
			ip_SetValue("Tab",Z1,S,"Zeitstempel");
		}
	}
	if (vt == 1){
		if (Richtung == "senkrecht") {		
			S1 = S1 + 1;
			ip_SetValue("Tab",Z,S1,"vt");
		} else {
			Z1 = Z1 + 1;
			ip_SetValue("Tab",Z1,S,"vt");
		}
	}
	if (vt_interpol == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			ip_SetValue("Tab",Z,S1,"vt interpoliert");
		} else {
			Z1 = Z1 + 1;
			ip_SetValue("Tab",Z1,S,"vt interpoliert");
		}
	}
	if (unixzeit == 1){
		if (Richtung == "senkrecht") {
			S1 = S1 + 1;
			ip_SetValue("Tab",Z,S1,"Unix Zeitstempel");
		} else {
			Z1 = Z1 + 1;
			ip_SetValue("Tab",Z1,S,"Unix Zeitstempel");
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
		if (Z >= ip_GridProps.Tab.rows) {$('#Tab').ip_AddRow({count: 1});}
		if (S >= ip_GridProps.Tab.cols) {neue_Spalte();}
		try {
			WertePaar = Ergebnis[Teil].split(",");
			ip_SetValue("Tab",Z,S,WertePaar[1]);
			if (Zeitstempel == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					ip_GridProps.Tab.rowData[Z].cells[S1].dataType.dataTypeName="string"
					ip_GridProps.Tab.rowData[Z].cells[S1].dataType.dataType="string"
					ip_SetValue("Tab",Z,S1,WertePaar[0]);
				} else {
					Z1 = Z1 + 1;
					ip_SetValue("Tab",Z1,S,"");
					ip_GridProps.Tab.rowData[Z1].cells[S].dataType.dataTypeName="string"
					ip_GridProps.Tab.rowData[Z1].cells[S].dataType.dataType="string"
					ip_SetValue("Tab",Z1,S,WertePaar[0].toString());
				}
			}
			if (vt == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					ip_SetValue("Tab",Z,S1,WertePaar[3]);
				} else {
					Z1 = Z1 + 1;
					ip_SetValue("Tab",Z1,S,WertePaar[3]);
				}
			}
			if (vt_interpol == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					ip_SetValue("Tab",Z,S1,WertePaar[4]);
				} else {
					Z1 = Z1 + 1;
					ip_SetValue("Tab",Z1,S,WertePaar[4]);
				}
			}
			if (unixzeit == 1){
				if (Richtung == "senkrecht") {
					S1 = S1 + 1;
					ip_SetValue("Tab",Z,S1,WertePaar[2]);
				} else {
					Z1 = Z1 + 1;
					ip_SetValue("Tab",Z1,S,WertePaar[2]);
				}
			}
		}
		catch (err) {}
	}
	ip_ResizeGrid("Tab");
	return typ + T_Text[93] + Einheit_Ergebnis;
}

function Archivwert(Grid_ID,Zeile,Spalte,TagZelle,Zeitpunkt,interpoliert,Zeitstempel,Einheit,typ,Richtung) {
	var strReturn;
	//var Tagname = ip_CellData("Tab",TagZelle.startRow,TagZelle.startCol).value;
	var Tagname = TagZelle;
	jQuery.ajax({
		url: "DH_AW.php?Tagname=" + Tagname + "&Zeitpunkt=" + Zeitpunkt + "&typ=" + typ + "&interpoliert=" + interpoliert,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = "waagerecht";}
  	Ergebnis = strReturn.split(",");
	if (Zeitstempel == 1){Attribute_Schreiben(Ergebnis[1]);}
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[2]);}
  	ip_ResizeGrid("Tab");
	return Ergebnis[0];
   
	function Attribute_Schreiben(Wert) {
		if (Richtung == "senkrecht") {Zeile = Zeile + 1;}
		if (Richtung == "waagerecht") {Spalte = Spalte + 1;}
		if (Zeile >= ip_GridProps.Tab.rows) {$('#Tab').ip_AddRow({count: 1});}
		if (Spalte >= ip_GridProps.Tab.cols) {neue_Spalte();}
		try {
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataTypeName="string"
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataType="string"
			ip_SetValue("Tab",Zeile,Spalte,Wert);
		}
		catch (err) {}
	}
}

function Mittelwert(Grid_ID,Zeile,Spalte,TagZelle,von,bis,Einheit,step,Richtung) {
	var strReturn;
	//var Tagname = ip_CellData("Tab",TagZelle.startRow,TagZelle.startCol).value;
	var Tagname = TagZelle;
	jQuery.ajax({
		url: "DH_MW.php?Tagname=" + Tagname + "&von=" + von + "&bis=" + bis + "&step=" + step,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	if (Richtung == "undefined") {Richtung = T_Text[48];}
  	Ergebnis = strReturn.split(",");
  	if (Einheit == 1){Attribute_Schreiben(Ergebnis[1]);}
  	ip_ResizeGrid("Tab");
	return Ergebnis[0];
   
	function Attribute_Schreiben(Wert) {
		if (Richtung == T_Text[49]) {Zeile = Zeile + 1;}
		if (Richtung == T_Text[48]) {Spalte = Spalte + 1;}
		if (Zeile >= ip_GridProps.Tab.rows) {$('#Tab').ip_AddRow({count: 1});}
		if (Spalte >= ip_GridProps.Tab.cols) {neue_Spalte();}
		try {
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataTypeName="string"
			ip_GridProps.Tab.rowData[Zeile].cells[Spalte].dataType.dataType="string"
			ip_SetValue("Tab",Zeile,Spalte,Wert);
		}
		catch (err) {}
	}
}

function Bereich_auswaehlen(Feld, Formularname) {
	var Formular = document.getElementById(Formularname);
	var Zeile = ip_GridProps.Tab.selectedRange[0][0][0];
	var Spalte = ip_GridProps.Tab.selectedRange[0][0][1];
	if (Feld.name == "tagnamenbereich") {
		try {Formular.tagnamenbereich.value = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][Spalte] + Zeile.toString();} catch (err) {} 
	}
	if (Feld.name == "ausgabebereich") {
		try {Formular.ausgabebereich.value = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][Spalte] + Zeile.toString();} catch (err) {}
	}
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
		contentSize: '540 85',
		headerTitle: T_Text[34],
		position: 'left-top 10 60',
		content:  '<div style="background: #FCEDD9;"><div style="margin-top: 5px;margin-left: 5px;" class="Text_einfach"><u><b>' + T_Text[94] + ':</u></b>&nbsp;' + T_Text[95] + '<br><br><form name="Tag_suchen_Form">' + T_Text[96] + ':&nbsp;<input class="Text_Element" name="Tagname" size="40" type="text">&nbsp;&nbsp;&nbsp;<input class="Schalter_Element" name="suchen" value="' + T_Text[97] + '" type="button" onclick="Eingabefeld_sichtbar_dialog()"></form><br></div></div>',
	});
}
	
function uebertragen() {
	try {tag_suchen_Dialog.close();} catch (err) {}
	try {
		var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
		var ZellObjekt = {};
		ZellObjekt["col"] = ip_GridProps.Tab.selectedRange[0][0][1];
		ZellObjekt["row"] = ip_GridProps.Tab.selectedRange[0][0][0];
		ZellObjekt["valueRAW"] = Ergebnis[0] + Ergebnis[1];
		$('#Tab').ip_CellInput(ZellObjekt);
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

function aktueller_Wert_Dialog(){
	var Richtung = T_Text[49];
	var Ausgabebereich = "";
	var Tagnamenbereich = "";
  	try {Bereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	try {Ausgabebereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	var Panel_Inhalt = '<form  id="aktuellerWertDialog" name="aktWertDialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[43] + '</td><td><input name="ausgabebereich" size="5" value="' + Ausgabebereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[44] + '" type="button" onclick="Bereich_auswaehlen(ausgabebereich, \'aktuellerWertDialog\');"></td></tr>'; 
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[45] + '</td><td><input name="tagnamenbereich" size="5" value="' + Tagnamenbereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[46] + '" type="button" onclick="Bereich_auswaehlen(tagnamenbereich, \'aktuellerWertDialog\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[47] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected">' + T_Text[48] + '</option><option>' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option>' + T_Text[48] + '</option><option selected="selected">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[50] + '</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" checked="checked"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[51] + '</td><td><input name="einheit" value="einheit" type="checkbox" checked="checked"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_aktWert_schreiben();"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'aktWertDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '500 350',
		headerTitle: T_Text[104],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_aktWert_schreiben() {
	var Formular = document.getElementById("aktuellerWertDialog");
	var Bereich = Zellenbez_zu_Nummern(Formular.tagnamenbereich.value);
	var Zeile = Bereich[0];
	var Spalte = Bereich[1];
	var Bereich = Zellenbez_zu_Nummern(Formular.ausgabebereich.value);
	var AusgabeZeile = Bereich[0];
	var AusgabeSpalte = Bereich[1];
	var ZellenObjekt = {};
	ZellenObjekt["row"] = AusgabeZeile;
	ZellenObjekt["col"] = AusgabeSpalte;
	$('#Tab').ip_SelectCell(ZellenObjekt);
	var Einheit = "0";
	var Zeitstempel = "0";
	var Richtung = T_Text[49];
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	var ZellObjekt = {};
	ZellObjekt["col"] = AusgabeSpalte;
	ZellObjekt["row"] = AusgabeZeile;
	ZellObjekt["valueRAW"] = "=akt_Wert(" + ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Spalte] + Zeile.toString() + "," + Zeitstempel + "," + Einheit + ",'" + Formular.richtung.value + "')";
	$('#Tab').ip_CellInput(ZellObjekt);
}

function Archivwert_Dialog(){
	var Richtung = T_Text[49];
	var Ausgabebereich = "";
	var Tagnamenbereich = "";
  	try {Bereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	try {Ausgabebereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	var Panel_Inhalt = '<form  id="ArchivwertDialog" name="Archivwert_Dialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[53] + '</td><td><select name="typ" size="1"><option value="Rohwert">' + T_Text[54] + '</option><option value="Stundenmittelwert">' + T_Text[55] + '</option><option value="Tagesmittelwert">' + T_Text[56] + '</option><option value="Min-Wert Stunde">' + T_Text[57] + '</option><option value="Max-Wert Stunde">' + T_Text[58] + '</option><option value="Min-Wert Tag">' + T_Text[59] + '</option><option value="Max-Wert Tag">' + T_Text[60] + '</option></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[61] + '</td><td><input name="Zeitpunkt" size="15" value="" type="text"></td><td><select name="interpoliert" size="1"><option value="Wert davor">' + T_Text[62] + '</option><option value="interpoliert">' + T_Text[63] + '</option><option value="Wert danach">' + T_Text[64] + '</option></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[65] + '</td><td><input name="ausgabebereich" size="5" value="' + Ausgabebereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[66] + '" type="button" onclick="Bereich_auswaehlen(ausgabebereich, \'ArchivwertDialog\');"></td></tr>'; 
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[67] + '</td><td><input name="tagnamenbereich" size="5" value="' + Tagnamenbereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[68] + '" type="button" onclick="Bereich_auswaehlen(tagnamenbereich, \'ArchivwertDialog\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[69] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected" value="waagerecht">' + T_Text[48] + '</option><option value="senkrecht">' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht">' + T_Text[48] + '</option><option selected="selected" value="senkrecht">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[70] + '</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" checked="checked"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[71] + '</td><td><input name="einheit" value="einheit" type="checkbox" checked="checked"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Archivwert_schreiben();"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'aktWertDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '530 370',
		headerTitle: T_Text[100],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_Archivwert_schreiben() {
	var Formular = document.getElementById("ArchivwertDialog");
	var interpoliert = Formular.interpoliert.value;
	var Zeitpunkt = Formular.Zeitpunkt.value;
	var typ = Zellenbez_zu_Nummern(Formular.typ.value);
	var Bereich = Zellenbez_zu_Nummern(Formular.tagnamenbereich.value);
	var Zeile = Bereich[0];
	var Spalte = Bereich[1];
	var Bereich = Zellenbez_zu_Nummern(Formular.ausgabebereich.value);
	var AusgabeZeile = Bereich[0];
	var AusgabeSpalte = Bereich[1];
	var ZellenObjekt = {};
	ZellenObjekt["row"] = AusgabeZeile;
	ZellenObjekt["col"] = AusgabeSpalte;
	$('#Tab').ip_SelectCell(ZellenObjekt);
	var Einheit = "0";
	var Zeitstempel = "0";
	var Richtung = T_Text[49];
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	var ZellObjekt = {};
	ZellObjekt["col"] = AusgabeSpalte;
	ZellObjekt["row"] = AusgabeZeile;
	ZellObjekt["valueRAW"] = "=AW(" + ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Spalte] + Zeile.toString() + ",'" + Zeitpunkt + "','" + interpoliert + "'," + Zeitstempel + "," + Einheit + ",'" + Formular.typ.value + "', '" + Formular.richtung.value + "')";
	$('#Tab').ip_CellInput(ZellObjekt);
}

function Archivwerte_Werte_Dialog(){
	var Richtung = T_Text[49];
	var Ausgabebereich = "";
	var Tagnamenbereich = "";
  	try {Bereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	try {Ausgabebereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	var Panel_Inhalt = '<form  id="ArchivwerteDialogInhalt" name="Archivwerte_Dialog_Inhalt">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td></td><td><div id="1">' + T_Text[72] + '<br><input name="von" size="15" value="" type="text"></div></td><td><div id="2">' + T_Text[73] + '<br><input name="bis" size="15" value="" type="text"></div></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right"><div id="3">' + T_Text[74] + '</div></td><td><div id="4"><select name="typ" size="1"><option value="Rohwerte">' + T_Text[75] + '</option><option value="Stundenmittelwerte">' + T_Text[76] + '</option><option value="Tagesmittelwerte">' + T_Text[77] + '</option><option value="Min-Werte stündlich">' + T_Text[78] + '</option><option value="Max-Werte stündlich">' + T_Text[79] + '</option><option value="Min-Max-Werte stündlich">' + T_Text[80] + '</option><option value="Min-Werte täglich">' + T_Text[81] + '</option><option value="Max-Werte täglich">' + T_Text[82] + '</option><option value="Min-Max-Werte täglich">' + T_Text[83] + '</option></div></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[84] + '</td><td><input name="ausgabebereich" size="5" value="' + Ausgabebereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[85] + '" type="button" onclick="Bereich_auswaehlen(ausgabebereich, \'ArchivwerteDialogInhalt\');"></td></tr>'; 
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="5" value="' + Tagnamenbereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[85] + '" type="button" onclick="Bereich_auswaehlen(tagnamenbereich, \'ArchivwerteDialogInhalt\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[87] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht" selected="selected">' + T_Text[48] + '</option><option value="senkrecht">' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option value="waagerecht">' + T_Text[48] + '</option><option value="senkrecht" selected="selected">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
  	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[88] + '</td><td><input name="zeitstempel" value="zeitstempel" type="checkbox" checked="checked"></td><td style="text-align: right">' + T_Text[89] + '</td><td><input name="vt" value="vt" type="checkbox"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[90] + '</td><td><input name="unixzeit" value="unixzeit" type="checkbox"></td><td style="text-align: right">' + T_Text[91] + '</td><td><input name="vt_interpol" value="vt interpol" type="checkbox"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Archivwerte_schreiben();"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'ArchivwerteDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '620 370',
		headerTitle: T_Text[99],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_Archivwerte_schreiben() {
	var Formular = document.getElementById("ArchivwerteDialogInhalt");
	var Bereich = Zellenbez_zu_Nummern(Formular.tagnamenbereich.value);
	var Zeile = Bereich[0];
	var Spalte = Bereich[1];
	var Bereich = Zellenbez_zu_Nummern(Formular.ausgabebereich.value);
	var AusgabeZeile = Bereich[0];
	var AusgabeSpalte = Bereich[1];
	var ZellenObjekt = {};
	ZellenObjekt["row"] = AusgabeZeile;
	ZellenObjekt["col"] = AusgabeSpalte;
	$('#Tab').ip_SelectCell(ZellenObjekt);
	var Zeitstempel = "0";
	var Richtung = T_Text[49];
	var vt_interpol = "0";
	var vt = "0";
	var unixzeit = "0";
	if (Formular.zeitstempel.checked == true){Zeitstempel = "1";}
	if (Formular.vt.checked == true){vt = "1";}
	if (Formular.vt_interpol.checked == true){vt_interpol = "1";}
	if (Formular.unixzeit.checked == true){unixzeit = "1";}
	var ZellObjekt = {};
	ZellObjekt["col"] = AusgabeSpalte;
	ZellObjekt["row"] = AusgabeZeile;
	ZellObjekt["valueRAW"] = "=AWerte(" + ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Spalte] + Zeile.toString() + ",'" + Formular.von.value + "','" + Formular.bis.value + "'," + Zeitstempel + "," + vt + "," + vt + "," + unixzeit + ",'" + Formular.typ.value + "','" + Formular.richtung.value + "')";
	$('#Tab').ip_CellInput(ZellObjekt);
}

function Mittelwert_Dialog(){
	var Richtung = T_Text[49];
	var Ausgabebereich = "";
	var Tagnamenbereich = "";
  	try {Bereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	try {Ausgabebereich = ip_GridProps.Tab.indexedData.colSymbols["colSymbols"][ip_GridProps.Tab.selectedCell.col] + ip_GridProps.Tab.selectedCell.row.toString();} catch (err) {}
  	var Panel_Inhalt = '<form  id="MittelwertDialog" name="MW_Dialog">';
	Panel_Inhalt = Panel_Inhalt + '<table cellspacing="10">';
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[84] + '</td><td><input name="ausgabebereich" size="5" value="' + Ausgabebereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[85] + '" type="button" onclick="Bereich_auswaehlen(ausgabebereich, \'MittelwertDialog\');"></td></tr>'; 
	Panel_Inhalt = Panel_Inhalt + '<tr><td align="right">' + T_Text[86] + '</td><td><input name="tagnamenbereich" size="5" value="' + Tagnamenbereich + '" type="text"></td><td><input name="bereich_aussuchen" value="' + T_Text[85] + '" type="button" onclick="Bereich_auswaehlen(tagnamenbereich, \'MittelwertDialog\');"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td></td><td><div id="1">' + T_Text[72] + '<br><input name="von" size="15" value="" type="text"></div></td><td><div id="2">' + T_Text[73] + '<br><input name="bis" size="15" value="" type="text"></div></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[87] + '</td><td><select name="richtung" size = "1">';
	if (Richtung == T_Text[48]){
		Panel_Inhalt = Panel_Inhalt + '<option selected="selected">' + T_Text[48] + '</option><option>' + T_Text[49] + '</option>';
	} else {
		Panel_Inhalt = Panel_Inhalt + '<option>' + T_Text[48] + '</option><option selected="selected">' + T_Text[49] + '</option>';
	}
	Panel_Inhalt = Panel_Inhalt + '</select></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[71] + '</td><td><input name="einheit" value="einheit" type="checkbox" checked="checked"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr><td style="text-align: right">' + T_Text[92] + '</td><td><input name="step" value="step" type="checkbox"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '<tr style="height: 45px;"><td></td><td><input name="ok" value="' + T_Text[52] + '" type="button" onclick="Funktion_Mittelwert_schreiben();"></td></tr>';
	Panel_Inhalt = Panel_Inhalt + '</table></form>';

	jsPanel.create({
		id: 'MWDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '500 350',
		headerTitle: T_Text[98],
		position: 'left-top 100 100',
		content:  Panel_Inhalt,
	});
}

function Funktion_Mittelwert_schreiben() {
	var Formular = document.getElementById("MittelwertDialog");
	var Bereich = Zellenbez_zu_Nummern(Formular.tagnamenbereich.value);
	var Zeile = Bereich[0];
	var Spalte = Bereich[1];
	var von = Formular.von.value;
	var bis = Formular.bis.value;
	var Bereich = Zellenbez_zu_Nummern(Formular.ausgabebereich.value);
	var AusgabeZeile = Bereich[0];
	var AusgabeSpalte = Bereich[1];
	var ZellenObjekt = {};
	ZellenObjekt["row"] = AusgabeZeile;
	ZellenObjekt["col"] = AusgabeSpalte;
	$('#Tab').ip_SelectCell(ZellenObjekt);
	var Einheit = "0";
	var step = "0";
	var Richtung = T_Text[49];
	if (Formular.einheit.checked == true){Einheit = "1";}
	if (Formular.step.checked == true){step = "1";}
	var ZellObjekt = {};
	ZellObjekt["col"] = AusgabeSpalte;
	ZellObjekt["row"] = AusgabeZeile;
	ZellObjekt["valueRAW"] = "=MW(" + ip_GridProps.Tab.indexedData.colSymbols.colSymbols[Spalte] + Zeile.toString() + ",'" + von + "','" + bis + "'," + Einheit + "," + step + ",'" + Formular.richtung.value + "')";
	$('#Tab').ip_CellInput(ZellObjekt);
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
	Bereich_Array[1] = ip_GridProps.Tab.indexedData.colSymbols.symbolCols[Spaltenname];
	
	if (istBereich > -1) {
		Bereich = Bereich.substr(istBereich + 1,Bereich.length);
		i = 0;
		while (Bereich.charCodeAt(i) < 48 || Bereich.charCodeAt(i) > 57) { 	
			i = i + 1;
		}
		Spaltenname = Bereich.substr(0,i);
		Bereich_Array[2] = parseInt(Bereich.substr(i,Bereich.length - i));
		Bereich_Array[3] = ip_GridProps.Tab.indexedData.colSymbols.symbolCols[Spaltenname];
	}
	return Bereich_Array;
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
