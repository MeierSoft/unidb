jsPanel.defaults.resizeit.minWidth = 20;
jsPanel.defaults.resizeit.minHeight = 16;
var markierte_elem = [];
DH_Elemente=[];
var T_Text = new Array;
var ausgewaehlt = 0; 
var Elternelement = null;
var MausStartX = 0;
var MausStartY = 0;
var nichtentfernen = 0;
var zuverschiebendesElement = "";
var db_Bereich_top = 0;
var Rahmenbreite = 0;
var Rahmenhoehe = 0;
var vergr_Faktor = -1;
var Formhoehe = 0;
var Formbreite = 0;
var Formlinks = 0;
var Formoben = 0;
var Groesse = "4";
var Seiteneigenschaften = "";
var Hoehe = 0;
var Breite = 0;
var akt_Bereich = "db_Bereich";
var Bedingungen = {};
var Gruppen = 0;
var id_alt = "";
//Konstanten
var DIN_A = [];
DIN_A[0] = [];
DIN_A[1] = [];
DIN_A[2] = [];
DIN_A[3] = [];
DIN_A[4] = [];
DIN_A[5] = [];
DIN_A[6] = [];
DIN_A[0]["Hoehe"] = "1189";
DIN_A[0]["Breite"] = "841";
DIN_A[1]["Hoehe"] = "841";
DIN_A[1]["Breite"] = "594"; 
DIN_A[2]["Hoehe"] = "594";
DIN_A[2]["Breite"] = "420"; 
DIN_A[3]["Hoehe"] = "420";
DIN_A[3]["Breite"] = "297"; 
DIN_A[4]["Hoehe"] = "297";
DIN_A[4]["Breite"] = "210"; 
DIN_A[5]["Hoehe"] = "210";
DIN_A[5]["Breite"] = "148";
DIN_A[6]["Hoehe"] = "148";
DIN_A[6]["Breite"] = "105";
var Format = "";
$(window).on('load',function() {;
	T_Text = JSON.parse(document.getElementById("translation").value);
	Format = T_Text[96];
	initDraw(document.getElementById('db_Bereich'));
	db_Bereich_top = parseInt(document.getElementById("db_Bereich").style.top);
	vergr_Faktor = document.getElementById("faktor").value;
	document.getElementById("vergr").value = vergr_Faktor / 0.03635 / Math.round(vergr_Faktor / 0.03635) * vergr_Faktor / 0.03635;
	Format_einstellen(1);
	document.getElementById("vergr").value = parseInt(document.getElementById("vergr").value) + 1;
	Format_einstellen(1);
	for (i = 0; i < document.getElementById("arbeitsbereich").childNodes.length; i++) {
	 	document.getElementById("arbeitsbereich").childNodes[i].setAttribute("onclick","Markierungen_entfernen(this)");
		document.getElementById("arbeitsbereich").childNodes[i].className = "context-menu-three";
	}
	document.getElementById("deckblattbereich").setAttribute("onclick","Markierungen_entfernen(this)");
	//bedingte Formatierung einrichten
	try {
		Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
		i = 1;
		while(Bedingungen[i] != undefined) {
			Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
			i = i + 1;
		}	
	} catch (err) {}
	//Testen ob alle Elemente eine eindeutige id haben und ob diese mit dem Elementnamen übereinstimmt. Wenn nicht, dann korrigieren.
	var Baum = [];
	var Zweig = {};
	var akt_Element = document.getElementById("db_Bereich");
	Zweig = {};
	Zweig.erledigt = 0;
	Zweig.ID = "db_Bereich";
	Baum[0] = Zweig;
	Zweig = {};
	x = 1;
	var alles_erledigt = 0;
	while (alles_erledigt == 0) {
		alles_erledigt = 1;
		for (z = 0; z < Baum.length; z++) {
			if (Baum[z].erledigt == 0) {
				Baum[z].erledigt = 1;
				akt_Element = document.getElementById(Baum[z].ID);
				alles_erledigt = 0;
				break;
			}
		}
		if (alles_erledigt == 0) {
			for (i = 0; i < akt_Element.childNodes.length; i++) {
				if (akt_Element.childNodes[i].id != undefined && akt_Element.childNodes[i].id != "" && akt_Element.childNodes[i].id.substr(0,11) != "kopftabelle") {
					Zweig.ID = akt_Element.childNodes[i].id;
					if (akt_Element.childNodes[i].childNodes.length > 0) {
						Zweig.erledigt = 0;
					} else {
						Zweig.erledigt = 1;
					}
					Baum[x] = Zweig;
					Zweig = {};
					x = x + 1;
				}
			}
		}
	}
	for (i = 0; i < Baum.length; i++) {
		Baum[i].erledigt = 0;
	}
	alles_erledigt = 0;
	while (alles_erledigt == 0) {
		alles_erledigt = 1;
		for (z = 0; z < Baum.length; z++) {
			if (Baum[z].erledigt == 0) {
				Baum[z].erledigt = 1;
				for (i = z+1; i < Baum.length; i++) {
					if (Baum[z].ID == Baum[i].ID) {
						Baum[i].ID = Baum[z].ID + "1";
						document.getElementById(Baum[z].ID).id = Baum[i].ID;
						try {document.getElementById(Baum[z].ID).attributes.elementname.value = Baum[z].ID;} catch (err) {}
						try {document.getElementById(Baum[z].ID).attributes.bezeichnung.value = Baum[z].ID;} catch (err) {}
					}
				}
				try {if (Baum[z].ID != document.getElementById(Baum[z].ID).attributes.Elementname.value) {document.getElementById(Baum[z].ID).attributes.Elementname.value = Baum[z].ID;}} catch (err) {}
				alles_erledigt = 0;
			}
		}
	}
	document.getElementById("Berichtskopf").setAttribute("kann_eltern","1");
	document.getElementById("Detailkopf").setAttribute("kann_eltern","1");
	document.getElementById("Detailbereich").setAttribute("kann_eltern","1");
	document.getElementById("Detailfuss").setAttribute("kann_eltern","1");
	document.getElementById("Berichtsfuss").setAttribute("kann_eltern","1");
});

document.addEventListener('jspanelresizestop', function (event) {
    if (event.detail === 'ausgewaehlt') {
    	Groesse_anpassen();
	}
});

//setup event handler function
var handler = function (event) {
try {document.getElementById("Element_Dialog").top.value = document.getElementById("ausgewaehlt").style.top;} catch (err) {}
try {document.getElementById("Element_Dialog").left.value = document.getElementById("ausgewaehlt").style.left;} catch (err) {}
}

//assign handler to event
document.addEventListener('jspaneldragstop', handler, false);

$(function(){
	T_Text = JSON.parse(document.getElementById("translation").value);
	$.contextMenu({
		selector: '.context-menu-one', 
		callback: function(key, options) {
			if (markierte_elem.length > 0) {
				ausrichten(key);
			}
		},
		items: {
			"gruppieren": {"name": T_Text[13], "icon": "edit"},
			"fold1": {
				"name": T_Text[14], 
				"items": {
					"oben": {"name": T_Text[15]},
					"unten": {"name": T_Text[16]},
					"rechts": {"name": T_Text[17]},
					"links": {"name": T_Text[18]}
				}
			},
			"fold2": {
				"name": T_Text[19], 
				"items": {
					"breitesten": {"name": T_Text[20]},
					"schmalsten": {"name": T_Text[21]},
					"höchsten": {"name": T_Text[22]},
					"niedrigsten": {"name": T_Text[23]}
					}
			},
		}
	});
	$.contextMenu({
		selector: '.context-menu-two', 
		callback: function(key, options) {
			if (key == "Eigenschaften") {Element_Dialog_oeffnen();}
			if (key == "Gruppierung aufheben") {Gruppierung_aufheben(this[0]);}
			if (key == "entfernen") {Element_entfernen();}
		},
		items: {
			"Gruppierung aufheben": {"name": T_Text[24], "icon": "quit"},
			"Eigenschaften": {"name": T_Text[25], "icon": "edit"},
			"sep1": "---------",
			"entfernen": {"name": T_Text[26], "icon": "delete"}
		}
	});
	$.contextMenu({
		selector: '.context-menu-three', 
		callback: function(key, options) {
			if (key == "Eigenschaften") {Kopf_Dialog_oeffnen(this[0].id);}
			if (key == "ausblenden") {ein_ausblenden(this[0].id);}
			if (key == "Höhe ändern") {Hoehe_aendern(this[0]);}
		},
		items: {
			"ausblenden": {"name": T_Text[79], "icon": "quit"},
			"Eigenschaften": {"name":T_Text[8], "icon": "edit"},
			"Höhe ändern": {"name": T_Text[80], "icon": "delete"}
		}
	});
});

function Gruppierung_aufheben(Auswahl) {
	if (Auswahl == null) {Auswahl = Element_aus_Fenster();}
	Auswahl_beenden();
	if (Auswahl.id.substr(0,6) == "Gruppe") {
		rlinks = Auswahl.parentElement.parentElement.offsetLeft;
		roben = Auswahl.parentElement.parentElement.offsetTop;
		while (Auswahl.childNodes.length > 0) {
			Auswahl.childNodes[0].style.top = (parseInt(Auswahl.childNodes[0].style.top) + parseInt(Auswahl.style.top)) + "px";
			Auswahl.childNodes[0].style.left = (parseInt(Auswahl.childNodes[0].style.left) + parseInt(Auswahl.style.left)) + "px";
			Auswahl.childNodes[0].className = "";
			Auswahl.childNodes[0].style.border = "";
			if (document.Einstellungen.mobil.value=="1") {
				Auswahl.childNodes[0].setAttribute('ontouchend', 'auswaehlen(this);');
			} else {
				Auswahl.childNodes[0].setAttribute('onclick', 'auswaehlen(this);');
			}
			Auswahl.parentElement.appendChild(Auswahl.childNodes[0]);
		}
		Auswahl.parentElement.removeChild(Auswahl);
		markierte_elem = [];
	}
	ausgewaehlt = 0;
}

function Kopf_Dialog_oeffnen(Bereich) {
	try {Auswahl_beenden();} catch (err) {}
	Linieoben = "";
	Linieunten = "";
	Hintergrundfarbe = "#FFFFFF";
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table>";
	Elem = document.getElementById(Bereich);
	if (Bereich == "Seitenkopf" || Bereich == "Seitenfuss") {
		Element = Elem.firstChild.firstChild.firstChild;
		Datum = "";
		Datum_Uhrzeit = "";
		Seite = "";
		Seite_Seiten = "";
		Berichtsname = "";
		Freitext = "";
		Ausrichtung = [];
		for (i = 0; i < Element.childNodes.length; i++) {
			if (Element.childNodes[i].innerHTML == T_Text[81]) {
				Datum = " checked";
				Ausrichtung[i] = "Datum";
			} else {
				if (Element.childNodes[i].innerHTML == T_Text[82]) {
					Datum_Uhrzeit = " checked";
					Ausrichtung[i] = "Datum";
				} else {
					if (Element.childNodes[i].innerHTML == T_Text[83]) {
						Seite = " checked";
						Ausrichtung[i] = "Seite";
					} else {
						if (Element.childNodes[i].innerHTML == T_Text[84]) {
							Seite_Seiten = " checked";
							Ausrichtung[i] = "Seite";
						} else {
							if (Element.childNodes[i].innerHTML == document.getElementById("Bezeichnung").value) {
								Berichtsname = " checked";
								Freitext = "";
								Ausrichtung[i] = "Text";
							} else {
								if (Element.childNodes[i].innerHTML != document.getElementById("Bezeichnung").value && Element.childNodes[i].innerHTML.substr(0,5) !="Datum" && Element.childNodes[i].innerHTML.substr(0,5) !="Seite") {Freitext = Element.childNodes[i].innerHTML;}
							}
						}
					}
				}
			}
		}
		Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[85] + '</td><td><input class="Text_Element" id="Bereich_Name" type="checkbox" onchange="Bereich_Text_umschalten(\'Berichtsname\');"' + Berichtsname + '></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[86] + '</td><td colspan="2"><input class="Text_Element" id="Bereich_Freitext" type="text" style="width: 250px;" onchange="Bereich_Text_umschalten(\'Freitext\');" value="' + Freitext + '");"></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[87] + '</td><td><select class="Auswahl_Liste_Element" id="Bereich_Ausrichtung_Text" value="">\n';
		for (i = 0; i < Element.childNodes.length; i++) {
			if (Ausrichtung[i] == "Text") {Option = i;}
		}
		Zaehler = 0;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[18] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[18] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[89] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[89] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[17] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[17] + '</option>';
		}
		Inhalt = Inhalt + '</select></td><td><input class="Schalter_Element" value="' + T_Text[88] + '" type="button" onclick="Bereich_Formatierung(\'' + Bereich + '\',\'Bereich_Ausrichtung_Text\');"></td></tr>\n';
		Inhalt = Inhalt + '<tr><td colspan="3"><hr></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[81] + '</td><td><input class="Text_Element" id="Bereich_Datum" type="checkbox" onchange="Bereich_Datum_umschalten(\'Datum\');"' + Datum + '></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[82] + '</td><td><input class="Text_Element" id="Bereich_Datum_Uhrzeit" type="checkbox" onchange="Bereich_Datum_umschalten(\'Datum_Zeit\');"' + Datum_Uhrzeit + '></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[87] + '</td><td><select class="Auswahl_Liste_Element" id="Bereich_Ausrichtung_Datum" value="">\n';
		for (i = 0; i < Element.childNodes.length; i++) {
			if (Ausrichtung[i] == "Datum") {Option = i;}
		}
		Zaehler = 0;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[18] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[18] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[89] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[89] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[17] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[17] + '</option>';
		}
		Inhalt = Inhalt + '</select></td><td><input class="Schalter_Element" value="' + T_Text[88] + '" type="button" onclick="Bereich_Formatierung(\'' + Bereich + '\',\'Bereich_Ausrichtung_Datum\');"></td></tr>\n';
		Inhalt = Inhalt + '<tr><td colspan="3"><hr></td></tr>\n';
		Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[84] + '</td><td><input class="Text_Element" id="Bereich_Seiten" type="checkbox" onchange="Bereich_Seiten_umschalten(\'Seiten\');"' + Seite_Seiten + '></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[83] + '</td><td><input class="Text_Element" id="Bereich_Seite" type="checkbox" onchange="Bereich_Seiten_umschalten(\'Seite\');"' + Seite + '></td></tr>\n';
		Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[87] + '</td><td><select class="Auswahl_Liste_Element" id="Bereich_Ausrichtung_Seite" value="">\n';
		for (i = 0; i < Element.childNodes.length; i++) {
			if (Ausrichtung[i] == "Seite") {Option = i;}
		}
		Zaehler = 0;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[18] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[18] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[89] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[89] + '</option>';
		}
		Zaehler = Zaehler + 1;
		if (Option == Zaehler) {
			Inhalt = Inhalt + '<option selected>' + T_Text[17] + '</option>';
		} else {
			Inhalt = Inhalt + '<option>' + T_Text[17] + '</option>';
		}
		Inhalt = Inhalt + '</select></td><td><input class="Schalter_Element" value="' + T_Text[88] + '" type="button" onclick="Bereich_Formatierung(\'' + Bereich + '\',\'Bereich_Ausrichtung_Seite\');"></td></tr>\n';
		Inhalt = Inhalt + '<tr><td colspan="3"><hr></td></tr>\n';
	}
	if (Elem.style.borderBottom.indexOf("solid") > -1) {Linieunten = " checked";}
	if (Elem.style.borderTop.indexOf("solid") > -1) {Linieoben = " checked";}
	if (Elem.style.backgroundColor > "") {Hintergrundfarbe = rgbToHex(Elem.style.backgroundColor);}
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[90] + '</td><td><input class="Text_Element" id="Bereich_Trennlinie_oben" type="checkbox"' + Linieoben + '></td></tr>\n';
	Inhalt = Inhalt + '<tr height="20px"><td class="Text_einfach" style="text-align: right">' + T_Text[91] + '</td><td><input class="Text_Element" id="Bereich_Trennlinie_unten" type="checkbox"' + Linieunten + '></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[54] + '</td><td><input class="Text_Element" id="Bereich_Hintergrundfarbe" style="width: 40px;" type="text" value="' + Hintergrundfarbe + '">&nbsp;&nbsp;&nbsp;&nbsp;<input class="Text_Element" id="Bereich_Hintergrundfarbe_h" name="Bereich_Hintergrundfarbe_h" value="' + Hintergrundfarbe + '" type="color" onchange="document.getElementById(\"Bereich_Hintergrundfarbe\").value = document.getElementById(\"Bereich_Hintergrundfarbe_h\").value;"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="50px"><td></td><td><input class="Schalter_Element" value="' + T_Text[37] + '" type="button" onclick="Bereich_bearbeiten(\'' + Bereich + '\');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="Schalter_Element" value="' + T_Text[93] + '" type="button" onclick="Bereich_bearbeiten_schliessen();"></td></tr></table>\n';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'kopf_fusszeile',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '400 450',
		headerTitle: Bereich,
		position: 'left-top 20 150',
		content: Inhalt
	});
}

function Bereich_Formatierung(Bereich,Zelleninhalt) {
	try {document.getElementById('Bereich_Formatierung').close();} catch (err) {}
	Zellen = document.getElementById(Bereich).firstChild.firstChild.firstChild;
	if (document.getElementById(Zelleninhalt).value == "links") {
		Zelle = Zellen.childNodes[0];
		Zellennr = 0;
	} else {
		 if (document.getElementById(Zelleninhalt).value == "mittig") {
			Zelle = Zellen.childNodes[1];
			Zellennr = 1;
		} else {
			Zelle = Zellen.childNodes[2];
			Zellennr = 2;
		}
	}
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table>";
	Inhalt += '<tr><td align="right">' + T_Text[72] + '</td><td colspan="2"><select class="Auswahl_Liste_Element" id="font-family" name="font-family" value=""><option value="arial, helvetica, sans-serif">arial, helvetica, sans-serif</option><option value="roman, times new roman, times, serif">roman, times new roman, times, serif</option><option value="courier, fixed, monospace">courier, fixed, monospace</option><option value="western, fantasy">western, fantasy</option><option value="Zapf-Chancery, cursive">Zapf-Chancery, cursive</option><option value="serif">serif</option><option value="sans-serif">sans-serif</option><option value="cursive">cursive</option><option value="fantasy">fantasy</option><option value="monospace">monospace</option><option value=""></option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[53] + '</td><td><select class="Auswahl_Liste_Element" id="font-style" name="font-style" value=""><option value="" selected></option><option value="normal">normal</option><option value="italic">kursiv</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[73] + '</td><td><input class="Text_Element" title="null" id="color" name="color" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[55] + '</td><td><select class="Auswahl_Liste_Element" id="font-weight" name="font-weight" value=""><option value=""></option><option value="normal">normal</option><option value="bold">fett</option><option value="bolder">fetter</option><option value="lighter">dünner</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[74] + '</td><td><input style="width: 40px;" class="Text_Element" title="null" id="font-size" name="font-size" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[56] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" title="' + T_Text[57] + '" id="class" name="class" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 50px;"><td align="right"><input class="Schalter_Element" type="button" name="uebernehmen" value="' + T_Text[37] + '" onclick="Bereich_Formatierung_uebernehmen(' + Bereich + ',' + Zellennr + ');"></td>';
	Inhalt += '<td align="left"><input class="Schalter_Element" type="button" name="cssdialog" value="' + T_Text[58] + '" onclick="CSS_Dialog_oeffnen();"></td></tr>';
	Inhalt += '</table></div>';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Bereich_Formatierung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '320 220',
		headerTitle: Bereich + ' - ' + T_Text[88],
		position: 'left-top 430 150',
		content: Inhalt
	});
	document.getElementById("font-family").value = Zelle.style.fontFamily;
	document.getElementById("font-style").value = Zelle.style.fontStyle;
	document.getElementById("color").value = rgbToHex(Zelle.style.color);
	document.getElementById("font-size").value = Zelle.style.fontSize;
	document.getElementById("font-weight").value = Zelle.style.fontWeight;
	document.getElementById("class").value = Zelle.class;
}

function Bereich_bearbeiten_schliessen() {
	try {document.getElementById('Bereich_Formatierung').close();} catch (err) {}
	kopf_fusszeile.close();
}

function Bereich_Formatierung_uebernehmen(Bereich, Zellennr) {
	Zelle = Bereich.firstChild.firstChild.firstChild.childNodes[Zellennr];
	Zelle.style.fontFamily = document.getElementById('font-family').value;
	Zelle.style.fontStyle = document.getElementById('font-style').value;
	Zelle.style.color = document.getElementById('color').value;
	Zelle.style.fontWeight = document.getElementById('font-weight').value;
	Zelle.style.fontSize = document.getElementById('font-size').value;
	Zelle.style.class = document.getElementById('class').value;
	document.getElementById('Bereich_Formatierung').close();
}

function Bereich_bearbeiten(Bereich) {
	Elem = document.getElementById(Bereich);
	if (Bereich == "Seitenkopf" || Bereich == "Seitenfuss") {
		//erstmal alles leer machen
		Zellen = document.getElementById(Bereich).firstChild.firstChild.firstChild;
		for (i = 0; i < Zellen.childNodes.length; i++) {
			Zellen.childNodes[i].innerHTML = "";
		}
		//Datum (Zeit) anzeigen
		pos = document.getElementById("Bereich_Ausrichtung_Datum").value;
		if (pos == T_Text[18]) {pos = 0;}
		if (pos == T_Text[89]) {pos = 1;}
		if (pos == T_Text[17]) {pos = 2;}
		Zelle = document.getElementById(Bereich).firstChild.firstChild.firstChild.childNodes[pos];
		Zelle.style.width = "25%";
		if (document.getElementById("Bereich_Datum").checked == true) {Zelle.innerHTML = T_Text[81];}
		if (document.getElementById("Bereich_Datum_Uhrzeit").checked == true) {Zelle.innerHTML = T_Text[82];}
		//Seitennummer anzeigen
		pos = document.getElementById("Bereich_Ausrichtung_Seite").value;
		if (pos == T_Text[18]) {pos = 0;}
		if (pos == T_Text[89]) {pos = 1;}
		if (pos == T_Text[17]) {pos = 2;}
		Zelle = document.getElementById(Bereich).firstChild.firstChild.firstChild.childNodes[pos];
		Zelle.style.width = "25%";
		if (document.getElementById("Bereich_Seite").checked == true) {Zelle.innerHTML = T_Text[83];}
		if (document.getElementById("Bereich_Seiten").checked == true) {Zelle.innerHTML = T_Text[84];}
		//Text anzeigen
		pos = document.getElementById("Bereich_Ausrichtung_Text").value;
		if (pos == T_Text[18]) {pos = 0;}
		if (pos == T_Text[89]) {pos = 1;}
		if (pos == T_Text[17]) {pos = 2;}
		Zelle = document.getElementById(Bereich).firstChild.firstChild.firstChild.childNodes[pos];
		Zelle.style.width = "50%";
		if (document.getElementById("Bereich_Freitext").value != "") {Zelle.innerHTML = document.getElementById("Bereich_Freitext").value;}
		if (document.getElementById("Bereich_Name").checked == true) {Zelle.innerHTML = document.getElementById("Bezeichnung").value;}
	}
	//Trennlinien anzeigen
	document.getElementById(Bereich).style.border = "";
	if (document.getElementById("Bereich_Trennlinie_oben").checked == true) {document.getElementById(Bereich).style.borderTop = "1px solid";}
	if (document.getElementById("Bereich_Trennlinie_unten").checked == true) {document.getElementById(Bereich).style.borderBottom = "1px solid";}
	if (document.getElementById(Bereich).style.borderTop == "" && document.getElementById(Bereich).style.borderBottom == "") {document.getElementById(Bereich).style.border = "1px dotted";}
	//Hintergrundfarbe einstellen
	if ( document.getElementById("Bereich_Hintergrundfarbe").value > "") {document.getElementById(Bereich).style.backgroundColor = document.getElementById("Bereich_Hintergrundfarbe").value;}
}

function Bereich_Text_umschalten(Checkbox) {
	if (Checkbox == "Berichtsname" && document.getElementById("Bereich_Freitext").value != "" && document.getElementById("Bereich_Name").checked == true) {
		document.getElementById("Bereich_Freitext").value = "";
	} else {
		if (Checkbox == "Freitext" && document.getElementById("Bereich_Freitext").value != "" && document.getElementById("Bereich_Name").checked == true) {document.getElementById("Bereich_Name").checked = false;}
	}
}

function Bereich_Datum_umschalten(Checkbox) {
	if (Checkbox == "Datum" && document.getElementById("Bereich_Datum").checked == true && document.getElementById("Bereich_Datum_Uhrzeit").checked == true) {
		document.getElementById("Bereich_Datum_Uhrzeit").checked = false;
	} else {
		if (Checkbox == "Datum_Zeit" && document.getElementById("Bereich_Datum_Uhrzeit").checked == true && document.getElementById("Bereich_Datum").checked == true) {document.getElementById("Bereich_Datum").checked = false;}
	}
}

function Bereich_Seiten_umschalten(Checkbox) {
	if (Checkbox == "Seite" && document.getElementById("Bereich_Seite").checked == true && document.getElementById("Bereich_Seiten").checked == true) {
		document.getElementById("Bereich_Seiten").checked = false;
	} else {
		if (Checkbox == "Seiten" && document.getElementById("Bereich_Seiten").checked == true && document.getElementById("Bereich_Seite").checked == true) {document.getElementById("Bereich_Seite").checked = false;}
	}
}

function Hoehe_aendern(Objekt) {
	jsPanel.create({
		header: false,
		addCloseControl: 1,
		id: 'Groesse_panel',
		container: document.getElementById("arbeitsbereich"),
		dragit: {
			disable: true
		},
		resizeit: {
			minWidth: parseInt(Objekt.style.width),
			maxWidth: parseInt(Objekt.style.width)
		},
		content: Objekt.innerHTML,
		contentSize: parseInt(Objekt.style.width) + ' ' + parseInt(Objekt.style.height),
		position: 'left-top ' + parseInt(Objekt.style.left) + ' ' + parseInt(Objekt.style.top),
		onbeforeclose: function(panel) {
			Objekt.style.height = panel.style.height;
			Format_einstellen(1);
			return true;
		}
	});
}

function ausrichten(key) {
	if (key == "gruppieren") {
		//Die Grenzen ausloten
		var rrechts = -1000000;
		var runten = -1000000;
		var roben = 1000000;
		var rlinks = 1000000;
		var rvertikal = 1;
		var tempoben = 0;
		var templinks = 0;
		for (i = 0; i < markierte_elem.length; i++) {
			tempdiv = document.getElementById(markierte_elem[i]);
			tempoben = 0;
			templinks = 0;
			while (tempdiv.id != "db_Bereich") {
				if (tempdiv.style.top != "") {tempoben = tempoben + parseInt(tempdiv.style.top);}
				if (tempdiv.style.left != "") {templinks = templinks + parseInt(tempdiv.style.left);}
				tempdiv = tempdiv.parentElement;
			}
			tempdiv = document.getElementById(markierte_elem[i]);
			if (parseInt(tempdiv.style.zIndex) > rvertikal){rvertikal = parseInt(tempdiv.style.zIndex);}
			if (tempoben + parseInt(tempdiv.style.height) > runten){runten = tempoben + parseInt(tempdiv.style.height);}
			if (templinks + parseInt(tempdiv.style.width) > rrechts){rrechts = templinks + parseInt(tempdiv.style.width);}
			if (tempoben < roben){roben = tempoben;}
			if (templinks < rlinks){rlinks = templinks;}
		}
		//neues DIV erzeugen und die ausgewählten DIVs unterordnen
		newdiv = document.createElement('div');
		var jetzt = new Date();
		var id = jetzt.getTime();
		newdiv.setAttribute('draggable',"true");
		newdiv.setAttribute('ondragstart',"drag(event);");
		newdiv.setAttribute('id',"Gruppe" + id);
		newdiv.style.left = rlinks + 'px';
		newdiv.style.top = roben + 'px';
		newdiv.style.width = (rrechts - rlinks) + 'px';
		newdiv.style.height = (runten - roben) + 'px';
		newdiv.style.zIndex = rvertikal + 1;
		newdiv.style.position="absolute";
		if (document.Einstellungen.mobil.value=="1") {
			newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
		} else {
			newdiv.setAttribute('onclick', 'auswaehlen(this);');
		}
		for (i = 0; i < markierte_elem.length; i++) {
			Element = document.getElementById(markierte_elem[i]);
			tempElement = Element;
			tempoben = 0;
			templinks = 0;
			while (tempElement.id != "db_Bereich") {
				if (tempElement.style.top != "") {tempoben = tempoben + parseInt(tempElement.style.top);}
				if (tempElement.style.left != "") {templinks = templinks + parseInt(tempElement.style.left);}
				tempElement = tempElement.parentElement;
			}
			Element.style.top = (tempoben - roben) + "px";
			Element.style.left = (templinks - rlinks) + "px";
			Element.removeAttribute("ontouchend");
			Element.removeAttribute("onclick");
			//Element.className = 'context-menu-two';
			newdiv.appendChild(Element);
			for (x = 0; x < Element.childNodes.length; x++) {
				try {
					Element.childNodes[x].removeAttribute("ontouchend");
					Element.childNodes[x].removeAttribute("onclick");
				} catch (err) {}
			}
		}
		if (Elternelement == null) {Elternelement = document.getElementById("db_Bereich");}
		if (Elternelement.id != "db_Bereich") {
			newdiv.style.top = (parseInt(newdiv.style.top) - parseInt(Elternelement.style.top)) + "px";
			newdiv.style.left = (parseInt(newdiv.style.left) - parseInt(Elternelement.style.left)) + "px";
		}
		Elternelement.appendChild(newdiv);
		return 0;
	}
	if (key == "oben" || key == "links" || key == "schmalsten" || key == "niedrigsten") {
		var minmax = 1000000;
	} else {
		var minmax = -1000000;
	}
	for (i = 0; i < markierte_elem.length; i++) {
		if (key == "breitesten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.width) > minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.width);}
		}
		if (key == "schmalsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.width) < minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.width);}
		}
		if (key == "höchsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.height) > minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.height);}
		}
		if (key == "niedrigsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.height) < minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.height);}
		}
		if (key == "oben") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) < minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.top);}
		}
		if (key == "unten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height) > minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height);}
		}
		if (key == "links") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) < minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.left);}
		}
		if (key == "rechts") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width) > minmax) {minmax = parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width);}
		}
	}
	for (i = 0; i < markierte_elem.length; i++) {
		if (key == "breitesten") {document.getElementById(markierte_elem[i]).style.width = minmax.toString() + "px";}
		if (key == "schmalsten") {document.getElementById(markierte_elem[i]).style.width = minmax.toString() + "px";}
		if (key == "höchsten") {document.getElementById(markierte_elem[i]).style.height = minmax.toString() + "px";}
		if (key == "niedrigsten") {document.getElementById(markierte_elem[i]).style.height = minmax.toString() + "px";}
		if (key == "oben") {	document.getElementById(markierte_elem[i]).style.top = minmax.toString() + "px";}
		if (key == "unten") {document.getElementById(markierte_elem[i]).style.top = (minmax - parseInt(document.getElementById(markierte_elem[i]).style.height)).toString() + "px";}
		if (key == "links") {document.getElementById(markierte_elem[i]).style.left = minmax.toString() + "px";}
		if (key == "rechts") {document.getElementById(markierte_elem[i]).style.left = (minmax - parseInt(document.getElementById(markierte_elem[i]).style.width)).toString() + "px";}
	}
}

function initDraw(Bereich) {
	function setMousePosition(e) {
		var ev = e || window.event; //Moz || IE
		if (ev.pageX) { //Moz
			mouse.x = ev.pageX + window.pageXOffset;
			mouse.y = ev.pageY + window.pageYOffset;
		} else if (ev.clientX) { //IE
			mouse.x = ev.clientX + document.body.scrollLeft;
			mouse.y = ev.clientY + document.body.scrollTop;
		}
	};
	var mouse = {
		x: 0,
		y: 0,
		startX: 0,
		startY: 0
	};
	var element = null;
	Bereich.onmousemove = function (e) {
		setMousePosition(e);
		if (element !== null) {
			element.style.width = Math.abs(mouse.x - mouse.startX) + 'px';
			element.style.height = Math.abs(mouse.y - mouse.startY) + 'px';
			element.style.left = (mouse.x - mouse.startX < 0) ? mouse.x + 'px' : mouse.startX + 'px';
			element.style.top = (mouse.y - mouse.startY < 0) ? mouse.y + 'px' : mouse.startY + 'px';
		}
	}
	Bereich.onmouseup = function (e) {
	//Falls die STRG - Taste gedrückt wurde, dann den Rest ignorieren.
		if (event.ctrlKey == false) {		
			if (element !== null) {
				markierte_elem = [];
				Bereich_oben = parseInt(document.getElementById("db_Bereich").style.top) + parseInt(document.getElementById("arbeitsbereich").style.top);
				try {Bereich_oben = Bereich_oben + parseInt(e.target.style.top);} catch (err) {}
				Bereich_links = parseInt(document.getElementById("db_Bereich").style.left) + parseInt(document.getElementById("arbeitsbereich").style.left);
				try {Bereich_links = Bereich_links + parseInt(e.target.style.left);} catch (err) {}
				for (i=0; i < e.target.childNodes.length; i++) {
					try {
						if (parseInt(e.target.childNodes[i].style.top) + Bereich_oben > parseInt(element.style.top)) {
							if (parseInt(e.target.childNodes[i].style.top) + Bereich_oben + parseInt(e.target.childNodes[i].style.height) < parseInt(element.style.top) + parseInt(element.style.height)) {
								if (parseInt(e.target.childNodes[i].style.left) + Bereich_links > parseInt(element.style.left)) {
									if (parseInt(e.target.childNodes[i].style.left) + Bereich_links + parseInt(e.target.childNodes[i].style.width) < parseInt(element.style.left) + parseInt(element.style.width)) {
										e.target.childNodes[i].style.opacity = 0.5;
										e.target.childNodes[i].className = 'context-menu-one';
										markierte_elem.push(e.target.childNodes[i].id);
									}
								}
							}
						}
					} catch (err) {}
				}
				try {e.target.removeChild(element);} catch (err) {}
				element = null;
				e.target.style.cursor = "default";
				nichtentfernen = 1;
			} else {
				Auswahl_beenden();
				mouse.startX = mouse.x;
				mouse.startY = mouse.y;
				element = document.createElement('div');
				element.className = 'rectangle'
				element.style.left = mouse.x + 'px';
				element.style.top = mouse.y + 'px';
				e.target.appendChild(element)
				e.target.style.cursor = "crosshair";
			}
		}
	}
}

function Elem_aus_Liste_markieren(id) {
	try {Auswahl_beenden();} catch (err) {}
	if (id_alt != id) {
		auswaehlen(id);
		id_alt = id;
	} else {
		id_alt = "";
	}
}

function abspeichern() {
	//Gruppierungen in der Reihenfolge nach unten stellen
	Bereich = document.getElementById("Detailbereich");
	Dok = document.getElementById("db_Bereich");
	for (i = 0; i < Bereich.childNodes.length - 1; i++) {
		if (Bereich.childNodes[i].hasAttribute("verkn_feld") == true) {
			id = Bereich.childNodes[i].id;
			Dok.appendChild(Bereich.childNodes[i]);
			Bereich.appendChild(document.getElementById(id));
		}
	}
	document.getElementById("deckblattbereich").style.display = "none";
	document.getElementById("arbeitsbereich").style.display = "block";
	reinigen();
	try {Gruppierung_aufheben();} catch (err) {}
	try {Auswahl_beenden();} catch (err) {}
	for (i=0; i < document.getElementById("arbeitsbereich").childNodes.length; i++) {
		try {document.getElementById("arbeitsbereich").childNodes[i].removeAttribute("onclick");} catch (err) {}
	}
	for (i=0; i < document.getElementById("deckblattbereich").childNodes.length; i++) {
		try {document.getElementById("deckblattbereich").childNodes[i].removeAttribute("onclick");} catch (err) {}
	}
	Format_einstellen(1);
	document.getElementById("db_bereich").value = document.getElementById("db_Bereich").innerHTML;
}

function auswaehlen(id){
	try {akt_Bereich = id.id;} catch (err) {akt_Bereich = id;}
	if (akt_Bereich == null || akt_Bereich == undefined) {akt_Bereich = id;}
	if (ausgewaehlt == 0) {
		akt_Bereich = document.getElementById(akt_Bereich).parentElement.id;
		var Auswahl = Element_aus_Fenster();
		if (Auswahl != 1) {
			try {Auswahl_beenden();} catch (err) {}
		}
		// Feld neu fuellen
		try {
			if (id == null) {
				var id = arguments[0].attributes['id'].value;
				var Auswahl=document.getElementById(id);
			} else {
				var Auswahl = id;
			}
		} catch (err) {
			var Auswahl = id;
		}
		//Falls es sich um ein neues Element handelt, dann die Markierung entfernen.
		if (Auswahl.hasAttribute('neu') == true) {
			Auswahl.removeAttribute('neu');
			if (Auswahl.hasAttribute("hintergrundfarbe_orig") == true) {
				Auswahl.style.backgroundColor = Auswahl.attributes.hintergrundfarbe_orig.value;
			} else {
				Auswahl.style.backgroundColor = "#FFFFFF";
			}
		}
		//Falls die STRG - Taste gedrückt wurde, dann das Element nur zur Auswahl hinzufügen.
		if (event != undefined && event.ctrlKey == true) {
			Auswahl.style.opacity = 0.5;
			Auswahl.className = 'context-menu-one';
			markierte_elem.push(Auswahl.id);
			nichtentfernen = 1;
		} else {
			Elternelement = Auswahl.parentElement;
			Auswahl.className = 'context-menu-two';
			try {
				var tempoben = parseInt(Auswahl.style.top);
				var templinks = parseInt(Auswahl.style.left);
				Auswahl.style.removeProperty("position");
				Auswahl.style.removeProperty("left");
				Auswahl.style.removeProperty("top");
				Auswahl.removeAttribute("onclick");
				Auswahl.removeAttribute("draggable","true");
 				Auswahl.removeAttribute("ondragstart","drag(event)");
				jsPanel.create({
					container: Elternelement,
					id: 'ausgewaehlt',
					header: false,
					//addCloseControl: 1,
					position: "left-top " + templinks + " " + tempoben,
					content: Auswahl,
   				contentSize: Auswahl.style.width + " " + Auswahl.style.height,
   				contentOverflow: 'hidden',
			   	theme: 'info',
	   			dragit: {handles: '.jsPanel-content', grid: [parseFloat(document.Einstellungen.Raster.value)], containment: 0},
   				onbeforeclose: function(panel) {
						Auswahl_beenden();
						return true;
					}
				});
				ausgewaehlt = 1;
			} catch (err) {}
		}
	}
}

function Elementtyp_aussuchen(){
	try {Auswahl_beenden();} catch (err) {}
	var Inhalt = "<div style='position: absolute; top: 10px; left: 100px;'>" + T_Text[27] + ":<br><br><select class='Auswahl_Liste_Element' id='Typauswahlliste' name='Typauswahlliste' size='10'>";
	var Liste = document.getElementById("Typauswahl").value.split(";");
	for (i=0; i < Liste.length; i++) {
		Liste_Name = Liste[i].split(",");
		Inhalt=Inhalt + "<option>" + Liste_Name[0] + "</option>";
	}
	Inhalt=Inhalt + "</select><br><br><input name='schliessen' value='" + T_Text[28] + "' type='button' onclick='Element_bauen();' style='width: 75px;border-left: 0px;'></div>";
	//e.preventDefault();
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'neues_Element',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '340 340',
		headerTitle: T_Text[29],
		position: 'left-top 300 30',
		content: Inhalt
	});
}

function Element_bauen(){
	var Sprache = document.getElementById("sprache").value;
	Stil = "";
	ElementTyp = document.getElementById('Typauswahlliste').value;
	ElementTyp = DBQ("unidb","","DE","Elementvorlagen","`" + Sprache + "` = '" + ElementTyp + "'")
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		var Elemente_ID = Auswahl.attributes["elemente_id"].value;
		var Eltern_ID = Auswahl.id;
	} else {
		var Elemente_ID = 0;
		var Eltern_ID = akt_Bereich;
	}
	var Eigenschaften;
	try {
		document.getElementById('neues_Element').close();
	} catch (err) {}
  	jQuery.ajax({
		url: "./Elementvorlage_einlesen.php?alle=1&Bezeichnung=" + ElementTyp,
		success: function (html) {
	   	Eigenschaften = html;
		},
   	async: false
   });
   Eigenschaften = Eigenschaften.split("@@@");
	if (Eigenschaften != undefined && Eigenschaften != null) {
		Eigenschaften2 = JSON.parse(Eigenschaften[0]);
		var jetzt = new Date();
		Bez = Eigenschaften2.Vorlage + jetzt.getTime().toString();
		if (Eigenschaften2.Vorlage == "berechnet" || Eigenschaften2.Vorlage == "Feld_anzeigen" || Eigenschaften2.Vorlage == "Textarea" || Eigenschaften2.Vorlage == "Text") {
			var newdiv = document.createElement('div');
		} else {
			if (Eigenschaften2.Vorlage == "Grafik") {
				var newdiv = document.createElement('img');
			} else {
				if (Eigenschaften2.Vorlage == "Gruppierung") {
					Gruppen = Gruppen + 1;
					if (Eltern_ID.indexOf("Detailbereich") == -1) {Eltern_ID = "Detailbereich";}
					var newdiv = document.createElement('div');
					newdiv.setAttribute("kann_eltern","1");
					Stil += "top: 0px; left: 0px; height: 75px; width: " + document.getElementById("arbeitsbereich").style.width + " ";
					newdiv.innerHTML = "Gruppierung";
				} else {
					var newdiv = document.createElement('input');
					newdiv.setAttribute('value', "");
					if (Eigenschaften2.Vorlage == "Textfeld") {newdiv.setAttribute('type', "Text");}
				}
			}
		}
		newdiv.setAttribute('id',Bez);
//		newdiv.setAttribute('name',Bez);
		Stil += "position : absolute; ";
		for (x=0; x < Eigenschaften.length; x++) {
			Eigenschaften2 = JSON.parse(Eigenschaften[x]);
			if (Eigenschaften2.Attributtyp == "style") {
				Stil += Eigenschaften2.orig_Name + ":" + Eigenschaften2.Standardwert + "; ";
			} else {
				if (Eigenschaften2.Eigenschaft == "feldtyp" || Eigenschaften2.Eigenschaft == "Feldtyp") {
					newdiv.setAttribute(Eigenschaften2.orig_Name, Eigenschaften2.Vorlage) + "; ";
					Eigenschaften2.Wert = Eigenschaften2.Vorlage;
				} else {
					if (Eigenschaften2.Eigenschaft != "disabled") {
						newdiv.setAttribute(Eigenschaften2.orig_Name, Eigenschaften2.Standardwert) + "; ";
						Eigenschaften2.Wert = Eigenschaften2.Standardwert;
					}
				}
				if (Eigenschaften2.Eigenschaft == "Feld" || Eigenschaften2.Eigenschaft == "feld") {
					feldliste = document.getElementById("feldliste").value;
					if (feldliste == "") {
						feldliste = Bez;
					} else {
						feldliste = feldliste + "@@@" + Bez;
					}
					document.getElementById("feldliste").value = feldliste;
				}
			}
		}
		newdiv.setAttribute("style", Stil);
		newdiv.setAttribute('Bezeichnung', Bez);
		if (document.Einstellungen.mobil.value == "1") {
			newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
		} else {
			newdiv.setAttribute('onclick', 'auswaehlen(this);');
		}
		newdiv.setAttribute('draggable', true);
		newdiv.setAttribute('ondragstart','drag(event)');
		//neues Element markieren
		newdiv.setAttribute('neu','1');
		newdiv.style.zIndex = 1000;
		newdiv.style.backgroundColor = "#FF0000";
		if (ElementTyp == "Textarea") {newdiv.setAttribute('onclick1','Text_bearbeiten("' + Bez + '");');}
		document.getElementById(Eltern_ID).appendChild(newdiv);
		newdiv.style.top = "0px";
		if (parseFloat(newdiv.style.height) > parseFloat(document.getElementById(Eltern_ID).style.height)) {
			document.getElementById(Eltern_ID).style.height = parseFloat(newdiv.style.height).toString() + "px";
		}
	}
}

function Element_Dialog_uebernehmen() {
	var Sprache = document.getElementById("sprache").value;
	var Auswahl = Element_aus_Fenster();
	var id = Auswahl.id;
	Eigenschaften2_temp = [];
	Eigenschaften3_temp = [];
	Formular = document.getElementById("Element_Dialog");
	Stil = "";
	Eigenschaften = document.getElementById(id).attributes;
	jQuery.ajax({
		url: "./Elementvorlage_einlesen.php?alle=0&Bezeichnung=" + Formular.Feldtyp.value,
		success: function (html) {
			Eigenschaften_temp = html;
		},
  		async: false
  	});
	Eigenschaften_temp = Eigenschaften_temp.split("@@@");
	for (x=0; x < Eigenschaften_temp.length; x++) {
		tempa = JSON.parse(Eigenschaften_temp[x]);
		Eigenschaften2_temp[tempa["Eigenschaft"]] = tempa;
		Eigenschaften3_temp[tempa["orig_Name"]] = Eigenschaften2_temp["Eigenschaft"];
	}
	for (i=1; i < Formular.elements.length; i++) {
		try {
			if (Formular[i].type == "checkbox") {
				if (Formular[i].checked == true) {
					Formular[i].value = 1;
				} else {
					Formular[i].value = 0;
				}
			}
		} catch (err) {}
		if ((Formular[i].id == "border-color" && Formular[i].value == "") || (Formular[i].id == "color" && Formular[i].value == "") || (Formular[i].id == "background-color" && Formular[i].value == "")) {
			try {
				if (Formular[i].id == "border-color" && Formular.class.value > "") {Auswahl.removeAttribute("border-color");}
			} catch (err) {}
			try {
				if (Formular[i].id == "color" && Formular.class.value > "") {Auswahl.removeAttribute("color");}
			} catch (err) {}
			try {
				if (Formular[i].id == "background-color" && Formular.class.value > "") {Auswahl.removeAttribute("background-color");}
			} catch (err) {}
		}
		try {
			Eigenschaft = Eigenschaften2_temp[Formular[i].id];
			try {
				Auswahl.setAttribute("kann_eltern",Eigenschaft.kann_Eltern);
			} catch (err) {}	
			if (Eigenschaft == undefined) {Eigenschaft = Eigenschaften3_temp[Formular[i].id];}
			if (Eigenschaft.Attributtyp == "style") {
				if (Eigenschaft["orig_Name"] != "top" && Eigenschaft["orig_Name"] !="left") {
					if (Formular.elements[Formular[i].id].value > "") {Stil += Eigenschaft["orig_Name"] + ":" + Formular.elements[Formular[i].id].value + "; ";}
				}
			} else {
				if (Formular[i].id != "onclick") {
					if (Formular[i].id == "css_Stil" || Formular[i].id == "css_stil" || Formular[i].id == "class") {
						if (Formular.elements[Formular[i].id].value > "") {
							Auswahl.setAttribute("class1", Formular[i].value);
						} else {
							delete Auswahl.removeAttribute("class1");
						}
					}
					if (Formular.elements[Formular[i].id].value > "") {
						Auswahl.setAttribute(Formular[i].id, Formular[i].value);
					} else {
						delete Auswahl.removeAttribute(Formular[i].id);
					}
				} else {
					Auswahl.setAttribute("onclick1", Formular[i].value);
				}
			}
		} catch (err) {}
	}
	if (Formular.Feldtyp.value == "Text") {
		Auswahl.innerHTML = Formular.Textinhalt.value;
	}
	Auswahl.setAttribute("style", Stil);
	Auswahl.parentElement.style.height = Auswahl.style.height;
	Auswahl.parentElement.style.width = Auswahl.style.width;
	try {Elementeinstellungen.close();} catch (err) {}
}

function Element_Dialog_oeffnen() {
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		var id = Auswahl.attributes["id"].value;
		var Sprache = document.getElementById("sprache").value;
		var DialogHoehe=120;
		var Zeilen = {};
		Zeilen[1] = 0;
		Zeilen[2] = 0;
		Zeilen[3] = 0;
		var Inhalt = "<div style=\"position: absolute; top: 10px; left: 10px;\">\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[62] + "' type='button' onclick='Tab_umschalten(1);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[63] + "' type='button' onclick='Tab_umschalten(2);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[64] + "' type='button' onclick='Tab_umschalten(3);'><br><br>\n";
		Inhalt = Inhalt + "<div id='Tabs'>\n<form id='Element_Dialog' name='Element_Dialog'>\n";
		var Inhalt1 = "<div id='Tab1' style='display: block;'>\n";
		Inhalt1 = Inhalt1 + "<table cellpadding='2px'>\n";
		Inhalt1 += "<input id='elemente_id' name='Elemente_ID' value='" + id + "' type='hidden'>\n";
		var Inhalt2 = "<div id='Tab2' style='display: none;'><table cellpadding='2px'>\n";
		var Inhalt3 = "<div id='Tab3' style='display: none;'><table cellpadding='2px'>\n";
		
		var Eigenschaften = "";
		Eigenschaften = document.getElementById(id).attributes;
		Inhalt1 += "<input id='Feldtyp' value='" + Eigenschaften["feldtyp"].value + "' type='hidden'>\n";
		jQuery.ajax({
			url: "./Elementvorlage_einlesen.php?alle=0&Bezeichnung=" + Eigenschaften["feldtyp"].value,
			success: function (html) {
	   		Eigenschaften1 = html;
			},
   		async: false
   	});
   	Eigenschaften1 = Eigenschaften1.split("@@@");
		if (Eigenschaften1 != undefined && Eigenschaften1 != null) {
			for (x=0; x < Eigenschaften1.length; x++) {
				Eigenschaften2 = JSON.parse(Eigenschaften1[x]);
				if (Eigenschaften2.Eigenschaft != 'feldtyp' && Eigenschaften2.Eigenschaft != 'Feldtyp') {
					Eigenschaften2.Elemente_ID = id;
					if (Eigenschaften2.Tab == 'allgemein') {
						var Inhalt_temp = Inhalt1;
						var Karte = 1;
						Zeilen[1] = Zeilen[1] + 1;
					} else {
						if (Eigenschaften2.Tab == 'Scripte') {
							var Inhalt_temp = Inhalt2;
							var Karte = 2;
							Zeilen[2] = Zeilen[2] + 1;
						} else {
							if (Eigenschaften2.Tab == 'Format') {
								var Inhalt_temp = Inhalt3;
								var Karte = 3;
								Zeilen[3] = Zeilen[3] + 1;
							}
						}
					} 
					Inhalt_temp += "<tr><td align='right'>" + Eigenschaften2[Sprache] + "</td><td>";
					if (Eigenschaften2.Darstellung_Dialog == "Textarea") {
						Position = Eigenschaften2.Wert;
						if (Position == null) {Position = Eigenschaften2.Standardwert;}
						Inhalt_temp += "<Textarea class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "'>" + Position + "</Textarea>";
					}
					if (Eigenschaften2.Darstellung_Dialog == "Textfeld") {
						Zusatz = "";
						if (Auswahl.attributes[Eigenschaften2.orig_Name] == undefined) {
							if (Eigenschaften2.Attributtyp == "style") {
								if (Auswahl.style[Eigenschaften2.orig_Name] == undefined) {
									Position = Eigenschaften2.Standardwert;
								} else {
									Position = Auswahl.style[Eigenschaften2.orig_Name];
								}
							} else {Position = Eigenschaften2.Standardwert;}
						} else {
							Position = Auswahl.attributes[Eigenschaften2.orig_Name].value;
						}
						if (Eigenschaften2.orig_Name.indexOf("color") > -1) {
							try {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							} catch (err) {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							}
						} else {
							if (Eigenschaften2.Eigenschaft == "Elementname" && Position == "") {Position = id;} 
							if (Position == null) {Position = Eigenschaften2.Standardwert;}
							if (Eigenschaften2.Eigenschaft == "oben" || Eigenschaften2.Eigenschaft == "links") {
								if (Eigenschaften2.Eigenschaft == "links") {Position = Auswahl.parentElement.parentElement.offsetLeft.toString() + "px";}
								if (Eigenschaften2.Eigenschaft == "oben") {Position = Auswahl.parentElement.parentElement.offsetTop.toString() + "px";}
								Zusatz = " onchange='Pos_sync();'";
							}
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Position + "' type='Text'" + Zusatz + ">";
						}
					}
					if (Eigenschaften2.Darstellung_Dialog == "Auswahl") {
						if (Eigenschaften2.Eigenschaft == "Feld") {
							SelectListe = Eigenschaften2.Eigenschaft.toLocaleLowerCase() + "liste";
							SelectListe = document.getElementById(SelectListe).value.split("@@@");
							Inhalt_temp += "<select class='Auswahl_Liste_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "'>";
							for (i = 0; i < SelectListe.length; i++) {
								if(Eigenschaften.feld.value == SelectListe[i]) {
									Inhalt_temp += "<option selected>";
								} else {
									Inhalt_temp += "<option>";
								}
								Inhalt_temp += SelectListe[i] + "</option>\n";
							}
							Inhalt_temp += "</select>";
						} else {
							jQuery.ajax({
								url: "./Optionen_lesen.php?Vorlageneigenschaften_ID=" + Eigenschaften2.Vorlageneigenschaften_ID,
								success: function (html) {
	   						Optionen = html;
							},
   						async: false
	   					});
	   					if (Eigenschaften2.Wert == undefined) {
	   						links = Optionen.indexOf("<option value=''></option>");
	   						rechts = Optionen.length - links -26;
	   						Optionen = Optionen.substr(0,links) + "<option value='' selected></option>" + Optionen.substr(Optionen.length - rechts);
	   					}
							Inhalt_temp += "<select class='Auswahl_Liste_Element' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "'>";
							Inhalt_temp += Optionen;
							Inhalt_temp += "</select>";
						}
					}
					if (Eigenschaften2.Darstellung_Dialog == "Checkbox") {
						if (Eigenschaften2.Wert == "1") {
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "' type='checkbox' checked>";
						} else {
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "' type='checkbox'>";
						}
					}
					Inhalt_temp += "</td></tr>\n";
					if (Karte == 1) {
						Inhalt1 = Inhalt_temp;
					} else {
						if (Karte == 2) {
							Inhalt2 = Inhalt_temp;
						} else {
							if (Karte == 3) {Inhalt3 = Inhalt_temp;}
						}
					}
					Inhalt_temp = "";
				}
			}
		}
		Inhalt1 += "</table></div>\n";
		Inhalt2 += "</table></div>\n";
		Inhalt3 += "</table></div>\n";
		Inhalt += Inhalt1 + Inhalt2 + Inhalt3;
		Inhalt += "</form><br><input class='Text_Element' type='button' name='uebernehmen' value='" + T_Text[37] + "' onclick='Element_Dialog_uebernehmen();'>\n";
		Inhalt += "&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' type='button' name='cssdialog' value='" + T_Text[58] + "' onclick='CSS_Dialog_oeffnen();'>\n";
		Inhalt += "</div></div>\n";
		var Multiplikator = 0;
		if (Zeilen[1] > Multiplikator) {Multiplikator = Zeilen[1];}
		if (Zeilen[2] > Multiplikator) {Multiplikator = Zeilen[2];}
		if (Zeilen[3] > Multiplikator) {Multiplikator = Zeilen[3];}
		DialogHoehe = DialogHoehe + Multiplikator * 27;
		jsPanel.create({
			onclosed: [
				function() {
					try {CSSauswahl.close();} catch (err) {}
					try {Dialog_CSSRegel.close();} catch (err) {}
				}
			],
			dragit: {
      		 snap: true
	       },
			id: 'Elementeinstellungen',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '370 ' + DialogHoehe.toString(),
			headerTitle: T_Text[65],
			position: 'left-top 65 5',
			contentOverflow: 'scroll scroll',
			content: Inhalt
		});
		Formular = document.getElementById("Element_Dialog");
		Eigenschaften3 = [];
		for (x=0; x < Eigenschaften1.length; x++) {
			Eigenschaften1[x] = JSON.parse(Eigenschaften1[x]);
			Eigenschaften3[Eigenschaften1[x]["Eigenschaft"].toLocaleLowerCase()] = Eigenschaften1[x];
		}
		try {
			Eigenschaften["class"].value = Eigenschaften["class1"].value;
			delete Eigenschaften["class"];
		} catch (err) {}
		for (x=0; x < Eigenschaften.length; x++) {
			try{
				if (Eigenschaften[x].name != "style") {
					if (Eigenschaften3[Eigenschaften[x].name].Darstellung_Dialog == "Checkbox") {
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value == "1") {Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].checked = true;}
					}
					if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value == undefined) {
						Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften3[Eigenschaften[x].name].Standardwert;
					} else {
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value == "context-menu-two context-menu-active") {Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = "";}
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].name == "onclick") {
							Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften["onclick1"].value;
						} else {
							Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften[Eigenschaften3[Eigenschaften[x].name].orig_Name].value;
						}
					}
				}
			} catch (err) {}
		}
		Stile = Eigenschaften.style.value.split(";");
		for (x=0; x < Stile.length; x++) {
			if (Stile[x] != "" && Stile[x] != " ") {
				Stil = Stile[x].split(":");
				if (Stil[0][0] == " ") {Stil[0] = Stil[0].substr(1);}
				if (Stil[0] == "height") {Stil[0] = "höhe";}
				if (Stil[0] == "width") {Stil[0] = "breite";}
				if (Stil[1][0] == " ") {Stil[1] = Stil[1].substr(1);}
				if (Stil[1].substr(0,4) == "rgb(") {Stil[1] = rgbToHex(Stil[1]);}
				try{
					document.getElementById(Stil[0]).value = Stil[1];
				} catch (err) {}
			}
		}
	}
}

function Bericht_Dialog_oeffnen() {
	var Inhalt = '<div style="background: #FCEDD9; width: 100%; height: 1200px;"><div style="position: absolute; top: 10px; left: 10px"><form name="Bericht_Dialog"><font size="+1"><b>' + T_Text[62] + '</b></font><br><br><table>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[30] + '</td><td colspan="2"><input class="Text_Element" name="Berichtname" size="30" value="' + document.Einstellungen.Bezeichnung.value + '" type="text"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[54] + '</td><td colspan="2"><input class="Text_Element" name="Hintergrundfarbe" id="Hintergrundfarbe" style="width: 40px;" value="' + document.Einstellungen.Hintergrundfarbe.value + '" type="text">&nbsp;&nbsp;&nbsp;&nbsp;<input class="Text_Element" id="Hintergrundfarbe_h" name="Hintergrundfarbe_h" value="' + document.Einstellungen.Hintergrundfarbe.value + '" type="color" onchange="document.getElementById(\'Hintergrundfarbe\').value = document.getElementById(\'Hintergrundfarbe_h\').value;"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[34] + '</td><td colspan="2"><input class="Text_Element" name="Datenbank" size="30" value="' + document.Einstellungen.Datenbank.value + '" type="text"></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[35] + '</td><td colspan="3"><textarea class="Text_Element" name="Datenquelle" style="height: 60px; width: 560px;">' + document.Einstellungen.Datenquelle.value + '</textarea></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[68] + '</td><td colspan="3"><textarea class="Text_Element" name="Headererweiterung_Dialog" style="height: 60px; width: 560px;">' + document.Einstellungen.headererweiterung.value + '</textarea></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">Js - onload</td><td colspan="3"><textarea class="Text_Element" name="Bei_Start_Dialog" style="height: 60px; width: 560px;">' + document.Einstellungen.Bei_Start.value + '</textarea></td></tr></table>\n';
	Inhalt = Inhalt + '<hr><br><font size="+1"><b>' + T_Text[94] + '</b></font><br>\n';
	Inhalt = Inhalt + '<table><tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[95] + '</td><td><select class="Auswahl_Liste_Element" name="Groesse" value="' + Seiteneigenschaften["Groesse"] + '">\n';
	if (Seiteneigenschaften["Groesse"] == "DIN A0") {
		Inhalt = Inhalt + '<option selected>DIN A0</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A0</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A1") {
		Inhalt = Inhalt + '<option selected>DIN A1</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A1</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A2") {
		Inhalt = Inhalt + '<option selected>DIN A2</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A2</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A3") {
		Inhalt = Inhalt + '<option selected>DIN A3</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A3</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A4") {
		Inhalt = Inhalt + '<option selected>DIN A4</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A4</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A5") {
		Inhalt = Inhalt + '<option selected>DIN A5</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A5</option>';
	}
	if (Seiteneigenschaften["Groesse"] == "DIN A6") {
		Inhalt = Inhalt + '<option selected>DIN A6</option>';
	} else {
		Inhalt = Inhalt + '<option>DIN A6</option>';
	}
	Inhalt = Inhalt + '</select></td>\n';
	Inhalt = Inhalt + '<td class="Text_einfach" style="text-align: right"></td><td>Format</td><td colspan="2"><select class="Auswahl_Liste_Element" name="Format" value="' + Seiteneigenschaften["Format"]+ '">\n';
	if (Seiteneigenschaften["Format"] == T_Text[96]) {
		Inhalt = Inhalt + '<option selected>' + T_Text[96] + '</option>';
	} else {
		Inhalt = Inhalt + '<option>' + T_Text[96] + '</option>';
	}
	if (Seiteneigenschaften["Format"] == T_Text[97]) {
		Inhalt = Inhalt + '<option selected>' + T_Text[97] + '</option>';
	} else {
		Inhalt = Inhalt + '<option>' + T_Text[97] + '</option>';
	}
	angekreuzt = "";
	if (Seiteneigenschaften["beidseitig"] == 1) {angekreuzt = " checked";}
	Inhalt = Inhalt + '</select></td><td colspan="3" align="right">' + T_Text[98] + '</td><td><input class="Text_Element" name="beidseitig" type="checkbox"' + angekreuzt + '></td> </tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td colspan="3" class="Text_einfach" style="text-align: left">' + T_Text[99] + '</td>\n';
	Inhalt = Inhalt + '<td class="Text_einfach" style="text-align: right">' + T_Text[18] + '</td><td><input class="Text_Element" name="linker_Rand" value="' + Seiteneigenschaften["linker_Rand"] + '" type="text" size="5"></td>\n';
	Inhalt = Inhalt + '<td class="Text_einfach" style="text-align: right">' + T_Text[17] + '</td><td><input class="Text_Element" name="rechter_Rand" value="' + Seiteneigenschaften["rechter_Rand"] + '" type="text" size="5"></td>\n';
	Inhalt = Inhalt + '<td class="Text_einfach" style="text-align: right">' + T_Text[15] + '</td><td><input class="Text_Element" name="oberer_Rand" value="' + Seiteneigenschaften["oberer_Rand"] + '" type="text" size="5"></td>\n';
	Inhalt = Inhalt + '<td class="Text_einfach" style="text-align: right">' + T_Text[16] + '</td><td><input class="Text_Element" name="unterer_Rand" value="' + Seiteneigenschaften["unterer_Rand"] + '" type="text" size="5"></td></tr>\n';
	Inhalt = Inhalt + '</table><br><hr><br><font size="+1"><b>' + T_Text[100] + '</b></font><br>\n';
	angekreuzt = "";
	if (document.getElementById("Seitenkopf").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<table><tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[107] + '</td><td><input class="Text_Element" name="seitenkopf_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Seitenkopf\');"></td>\n';
	angekreuzt = "";
	if (document.getElementById("Berichtskopf").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<td width="30px"></td><td class="Text_einfach" style="text-align: right">' + T_Text[102] + '</td><td><input class="Text_Element" name="berichtskopf_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Berichtskopf\');"></td>\n';
	angekreuzt = "";
	if (document.getElementById("Detailkopf").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<td width="30px"></td><td class="Text_einfach" style="text-align: right">' + T_Text[105] + '</td><td><input class="Text_Element" name="detailkopf_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Detailkopf\');"></td>\n';
	angekreuzt = "";
	if (document.getElementById("Seitenfuss").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<tr height="25px"><td class="Text_einfach" style="text-align: right">' + T_Text[106] + '</td><td><input class="Text_Element" name="seitenfuss_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Seitenfuss\');"></td>\n';
	angekreuzt = "";
	if (document.getElementById("Berichtsfuss").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<td width="30px"></td><td class="Text_einfach" style="text-align: right">' + T_Text[101] + '</td><td><input class="Text_Element" name="berichtsfuss_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Berichtsfuss\');"></td>\n';
	angekreuzt = "";
	if (document.getElementById("Detailfuss").style.height == "0px") {angekreuzt = " checked";}
	Inhalt = Inhalt + '<td width="30px"></td><td class="Text_einfach" style="text-align: right">' + T_Text[104] + '</td><td><input class="Text_Element" name="detailfuss_zeigen" type="checkbox"' + angekreuzt + ' onchange="ein_ausblenden(\'Detailfuss\');"></td></tr>\n';
	Inhalt = Inhalt + '</table><br><hr><br>' + T_Text[36] + '&nbsp;&nbsp;&nbsp;<input class="Schalter_Element" name="übernehmen" value="' + T_Text[37] + '" type="button" onclick="Berichteinstellungen_uebernehmen()">\n';
	Inhalt = Inhalt + '</form></div></div>\n';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Berichteinstellungen',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '680 700',
		headerTitle: T_Text[38],
		position: 'left-top 30 30',
		contentOverflow: 'hidden',
		content: Inhalt,
	});
}

function ein_ausblenden(Bereich) {
	if (Bereich != "Detailbereich") {
		if (document.getElementById(Bereich).style.height == "0px") {
			document.getElementById(Bereich).style.height = document.getElementById(Bereich).attributes.orig_hoehe.value;
			document.getElementById(Bereich).style.display = "block";
		} else {
			document.getElementById(Bereich).attributes.orig_hoehe.value = document.getElementById(Bereich).style.height;
			document.getElementById(Bereich).style.height = "0px";
			document.getElementById(Bereich).style.display = "none";
		}
		Format_einstellen(1);
	}
}

function Berichteinstellungen_uebernehmen() {
	document.Einstellungen.Bezeichnung.value = document.Bericht_Dialog.Berichtname.value;
	document.Einstellungen.Hintergrundfarbe.value = document.Bericht_Dialog.Hintergrundfarbe.value;
	document.Einstellungen.Datenbank.value = document.Bericht_Dialog.Datenbank.value;
	document.Einstellungen.Datenquelle.value = document.Bericht_Dialog.Datenquelle.value;
	Seiteneigenschaften = "Groesse:" + document.Bericht_Dialog.Groesse.value;
	
	Seiteneigenschaften = Seiteneigenschaften + ",Format:" + document.Bericht_Dialog.Format.value;
	Seiteneigenschaften = Seiteneigenschaften + ",linker_Rand:" + document.Bericht_Dialog.linker_Rand.value;
	Seiteneigenschaften = Seiteneigenschaften + ",rechter_Rand:" + document.Bericht_Dialog.rechter_Rand.value;	
	Seiteneigenschaften = Seiteneigenschaften + ",oberer_Rand:" + document.Bericht_Dialog.oberer_Rand.value;
	Seiteneigenschaften = Seiteneigenschaften + ",unterer_Rand:" + document.Bericht_Dialog.unterer_Rand.value;
	angekreuzt = "0";
	if (document.Bericht_Dialog.beidseitig.checked == true) {angekreuzt = "1";}
	Seiteneigenschaften = Seiteneigenschaften + ",beidseitig:" + angekreuzt;
	document.Einstellungen.Seiteneinstellungen.value = Seiteneigenschaften;
	Seiteneigenschaften = Seiteneigenschaften.split(",");
	for (z = 0; z < Seiteneigenschaften.length; z++) {
		x = Seiteneigenschaften[z].split(":");
		Seiteneigenschaften[x[0]] = x[1];
	}
	temp_Text = document.Bericht_Dialog.Headererweiterung_Dialog.value.replace(/'/g,"\"");
	document.Einstellungen.Headererweiterung.value = temp_Text;
	temp_Text = document.Bericht_Dialog.Bei_Start_Dialog.value.replace(/'/g,"\"");
	document.Einstellungen.Bei_Start.value = temp_Text;
	Format = document.Bericht_Dialog.Format.value;
	Groesse = Seiteneigenschaften["Groesse"].substr(-1);
	try {Berichteinstellungen.close();} catch (err) {}
	Format_einstellen(0);
	document.getElementById("vergr").value = parseInt(document.getElementById("vergr").value);
	Format_einstellen(1);
}

function Format_einstellen(manuell) {
	Auswahl_beenden();
	try {Groesse_panel.close();} catch (err) {}
	if (Seiteneigenschaften == undefined || Seiteneigenschaften == "") {
		Seiteneigenschaften = document.getElementById("seiteneinstellungen").value.split(",");
		for (z = 0; z < Seiteneigenschaften.length; z++) {
			x = Seiteneigenschaften[z].split(":");
			Seiteneigenschaften[x[0]] = x[1];
		}
	}
	vergr_Faktor_alt = vergr_Faktor;
	Format = Seiteneigenschaften["Format"];
	Rahmenbreite = document.documentElement.clientWidth;
	Rahmenhoehe = document.documentElement.clientHeight - db_Bereich_top - 10;
	Hoehe1 = parseFloat(DIN_A[Groesse]["Hoehe"]);
	Breite1 = parseFloat(DIN_A[Groesse]["Breite"]);
	if (Format == T_Text[97]) {
		Breite1 = DIN_A[Groesse]["Hoehe"];
		Hoehe1 = DIN_A[Groesse]["Breite"];
	}
	if (manuell == 1) {
		vergr_Faktor = parseFloat(document.getElementById("vergr").value) / 100 * 3.635;
	} else {
		if (Rahmenbreite / Breite1 < Rahmenhoehe / Hoehe1) {
			vergr_Faktor = Rahmenbreite / Breite1;
		} else {
			vergr_Faktor = Rahmenhoehe / Hoehe1;
		}
		document.getElementById("vergr").value = vergr_Faktor / 0.03635;
	}
	document.getElementById("faktor").value = vergr_Faktor.toString();
	if (vergr_Faktor_alt == -1) {
		vergr = 1;
	} else {
		vergr = vergr_Faktor / vergr_Faktor_alt;
	}
	document.getElementById("db_Bereich").style.width = (vergr_Faktor * Breite1).toString() + "px";
	document.getElementById("db_Bereich").style.height = (vergr_Faktor * Hoehe1).toString() + "px";
	Formhoehe = document.getElementById("db_Bereich").style.height;
	Formbreite = document.getElementById("db_Bereich").style.width;
	Formlinks = "0px";
	Formoben = "0px";
	//Arbeitsbereich
	document.getElementById("arbeitsbereich").style.top = (parseFloat(Formoben) + parseFloat(Seiteneigenschaften["oberer_Rand"] * vergr_Faktor)).toString() + "px";
	document.getElementById("arbeitsbereich").style.left = (parseFloat(Formlinks) + parseFloat(Seiteneigenschaften["linker_Rand"] * vergr_Faktor)).toString() + "px";
	document.getElementById("arbeitsbereich").style.height = parseFloat(parseFloat(Formhoehe) - Seiteneigenschaften["oberer_Rand"] * vergr_Faktor - Seiteneigenschaften["unterer_Rand"] * vergr_Faktor).toString() + "px";
	document.getElementById("arbeitsbereich").style.width = parseFloat(parseFloat(Formbreite) - Seiteneigenschaften["linker_Rand"] * vergr_Faktor - Seiteneigenschaften["rechter_Rand"] * vergr_Faktor).toString() + "px";
	Hoehe = parseFloat(document.getElementById("arbeitsbereich").style.height);
	Breite = parseFloat(document.getElementById("arbeitsbereich").style.width);
	
	//Bereiche oben
	document.getElementById("Seitenkopf").style.top = "0px";
	document.getElementById("Berichtskopf").style.top = (parseFloat(document.getElementById("Seitenkopf").style.top) + parseFloat(document.getElementById("Seitenkopf").style.height) + 2).toString() + "px";
	document.getElementById("Detailkopf").style.top = (parseFloat(document.getElementById("Berichtskopf").style.top) + parseFloat(document.getElementById("Berichtskopf").style.height) + 3).toString() + "px";
	document.getElementById("Detailbereich").style.top = (parseFloat(document.getElementById("Detailkopf").style.top) + parseFloat(document.getElementById("Detailkopf").style.height) + 4).toString() + "px";
	//Detailbereich Höhe
	Gesamthoehe = 0;
	for (z = 0; z < document.getElementById("arbeitsbereich").childNodes.length; z++) {
		if (document.getElementById("arbeitsbereich").childNodes[z].id != "Groesse_panel") {
			Gesamthoehe = Gesamthoehe + parseFloat(document.getElementById("arbeitsbereich").childNodes[z].style.height);
			if (document.getElementById("arbeitsbereich").childNodes[z].hasAttribute("orig_hoehe") == true && document.getElementById("arbeitsbereich").childNodes[z].style.height != "0px") {
				document.getElementById("arbeitsbereich").childNodes[z].attributes.orig_hoehe.value = document.getElementById("arbeitsbereich").childNodes[z].style.height;
			} else {
				if (document.getElementById("arbeitsbereich").childNodes[z].style.height != "0px") {document.getElementById("arbeitsbereich").childNodes[z].setAttribute("orig_hoehe",document.getElementById("arbeitsbereich").childNodes[z].style.height);}
			}
		}
	}
	if (Gesamthoehe < Hoehe) {
		Gesamthoehe = Hoehe;
	} else {
		document.getElementById("arbeitsbereich").style.height = Gesamthoehe.toString() + "px";
		Formhoehe = parseFloat(Seiteneigenschaften["oberer_Rand"] * vergr) + parseFloat(Seiteneigenschaften["unterer_Rand"] * vergr) + Gesamthoehe;
		document.getElementById("db_Bereich").style.height = Formhoehe.toString() + "px";
	}
	document.getElementById("Seitenfuss").style.top = (Gesamthoehe - parseFloat(document.getElementById("Seitenfuss").style.height)).toString() + "px";
	document.getElementById("Berichtsfuss").style.top = (parseFloat(document.getElementById("Seitenfuss").style.top) - parseFloat(document.getElementById("Berichtsfuss").style.height) - 3).toString() + "px";
	document.getElementById("Detailfuss").style.top = (parseFloat(document.getElementById("Berichtsfuss").style.top) - parseFloat(document.getElementById("Detailfuss").style.height) - 2).toString() + "px";
	//Größe der Elemente anpassen
	if (vergr != 1) {
		var erledigt = 0;
		var Knotenliste = [];
		var Knoten = [];
		Knoten["erledigt"] = 0;
		Knoten["id"] = "arbeitsbereich";
		Knotenliste[0] = Knoten;
		var Knoten = [];
		Knoten["erledigt"] = 0;
		Knoten["id"] = "deckblattbereich";
		Knotenliste[1] = Knoten;
		var Knoten = [];
		x = 2;
		while (erledigt == 0) {
			erledigt = 1;
			for (z = 0; z < Knotenliste.length; z++) {
				if (Knotenliste[z]["erledigt"] == 0) {
					Knotenliste[z]["erledigt"] = 1;
					if (Knotenliste[z].id > "" && Knotenliste[z].id != undefined) {
						temp = document.getElementById(Knotenliste[z].id);
						z = Knotenliste.length
					}
				}
			}
			if (temp != null) {
				for (i = 0; i < temp.childNodes.length; i++) {
					Knoten["id"] = temp.childNodes[i].id;
					try {
						if (Knoten["id"] > "" && Knoten["id"] != undefined) {
							try {temp.childNodes[i].style.top = parseFloat(parseFloat(temp.childNodes[i].style.top) * vergr).toString() + "px";} catch (err) {}
							try {temp.childNodes[i].style.left = parseFloat(parseFloat(temp.childNodes[i].style.left) * vergr).toString() + "px";} catch (err) {}
							try {temp.childNodes[i].style.height = parseFloat(parseFloat(temp.childNodes[i].style.height) * vergr).toString() + "px";} catch (err) {}
							try {temp.childNodes[i].style.width = parseFloat(parseFloat(temp.childNodes[i].style.width) * vergr).toString() + "px";} catch (err) {}
							try {temp.childNodes[i].style.fontSize = parseFloat(parseFloat(temp.childNodes[i].style.fontSize) * vergr).toString() + "px";} catch (err) {}
						}
					} catch (err) {}
					if (temp.childNodes[i].childNodes.length > 0) {
						Knoten["erledigt"] = 0;
					} else {
						Knoten["erledigt"] = 1;
					}
					if (Knoten.id != "" && Knoten.id != undefined) {
						Knotenliste.push(x);
						Knotenliste[x] = Knoten;
					}
					Knoten = [];
					x = x + 1;
				}
				try {
					for (z = 0; z < Knotenliste.length; z++) {
						if (Knotenliste[z]["erledigt"] == 0) {
							erledigt = 0;
							z = Knotenliste.length
						}
					}
				} catch (err) {}
			}
		}
	}
	//Bereiche
	Breite_text = Breite.toString() + "px";
	for (z = 0; z < document.getElementById("arbeitsbereich").childNodes.length; z++) {
		document.getElementById("arbeitsbereich").childNodes[z].style.left = "0px";
		document.getElementById("arbeitsbereich").childNodes[z].style.width = Breite_text;
	}
	//Deckblatt anpassen
	document.getElementById("deckblattbereich").style.top = document.getElementById("arbeitsbereich").style.top;
	document.getElementById("deckblattbereich").style.left = document.getElementById("arbeitsbereich").style.left;
	document.getElementById("deckblattbereich").style.height = document.getElementById("arbeitsbereich").style.height;
	document.getElementById("deckblattbereich").style.width = document.getElementById("arbeitsbereich").style.width;
	document.getElementById("deckblattbereich").style.borderWidth = document.getElementById("arbeitsbereich").style.borderWidth;
	document.getElementById("deckblattbereich").style.borderStyle = document.getElementById("arbeitsbereich").style.borderStyle;
}

function Element_entfernen() {
	var Auswahl = Element_aus_Fenster();
	var Fenster = Auswahl.parentElement.parentElement;
	Auswahl.parentElement.removeChild(Auswahl);
	Fenster.close();
	ausgewaehlt = 0;
}

function Markierungen_entfernen(Bereich) {
	if (Bereich.id != "db_Bereich") { akt_Bereich1 = Bereich.id;}
	try {Groesse_panel.close();} catch (err) {}
	if (nichtentfernen == 1) {
		nichtentfernen = 0;
		return;
	}
	for (i=0; i < Bereich.childNodes.length; i++) {
		if (Bereich.childNodes[i].id != "ausgewaehlt") {
			try{Bereich.childNodes[i].style.opacity = "";} catch (err) {}
		}
	}
}

function Auswahl_beenden() {
	Elternelement = document.getElementById(akt_Bereich);
	//Sobald sich ein Element in einem Kopf- oder Fußbereich befindet, die Standardbeschriftung entfernen.
	if (Elternelement.childNodes.length > 0) {
		while (Elternelement.innerHTML.substr(0,1) != "<" && Elternelement.innerHTML.length > 0) {
			Elternelement.innerHTML = Elternelement.innerHTML.substr(1,Elternelement.innerHTML.length);
		}
	}
	if (Elternelement == null) {Elternelement = document.getElementById("db_Bereich");}
	for (i=0; i < Elternelement.childNodes.length; i++) {
		try {Elternelement.childNodes[i].style.opacity = "";} catch (err) {}
	}
 	try{
 		Auswahl = Element_aus_Fenster();
 		var Fenster = Auswahl.parentElement.parentElement;
 		try {Auswahl.removeAttribute("class");} catch (err) {}
 		try {Auswahl.setAttribute("class", Auswahl.attributes.class1.value);} catch (err) {}
 		Auswahl.setAttribute("draggable","true");
 		Auswahl.setAttribute("ondragstart","drag(event)");
		Auswahl.setAttribute("onclick", "auswaehlen(this);");
 		Auswahl.style.top = Fenster.offsetTop.toString() + "px";
 		Auswahl.style.left = Fenster.offsetLeft.toString() + "px";
 		Auswahl.style.height = Fenster.offsetHeight.toString() + "px";
 		Auswahl.style.width = Fenster.offsetWidth.toString() + "px";
 		Auswahl.style.position = "absolute";
		Elternelement.appendChild(Auswahl);
	} catch (err) {}
	try {Fenster.close();} catch (err) {}
	try {Elementeinstellungen.close();} catch (err) {}
	ausgewaehlt = 0;
	Elternelement = null;
	reinigen();
}

function Groesse_anpassen() {
	try{
		var Auswahl = Element_aus_Fenster();
		if (parseFloat(Auswahl.parentElement.parentElement.style.width) > parseFloat(document.getElementById("arbeitsbereich").style.width)) {Auswahl.parentElement.parentElement.style.width = document.getElementById("arbeitsbereich").style.width;}
		Auswahl.style.width = Auswahl.parentElement.parentElement.style.width;
		Auswahl.style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		if (Auswahl.firstChild.localName != "label") {
			Auswahl.firstChild.style.width = Auswahl.parentElement.parentElement.style.width;
			Auswahl.firstChild.style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		} else {
			Auswahl.firstChild.style.width = (parseInt(Auswahl.style.width) - parseInt(Auswahl.attributes["feldbreite"].value)).toString() + "px";
			Auswahl.childNodes[1].style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		}
		Auswahl = "undefinied";
	} catch (err) {}
}

function Element_aus_Fenster() {
	var Auswahl = 1;
	try{
		var panels = jsPanel.getPanels(function () {
			if (this.id == "ausgewaehlt") {return this;}
		}).map(function (panel) {
   		if (panel.id == "ausgewaehlt"){
  	 			try {
  	 				Auswahl = panel.content.childNodes[0];
	  	 		} catch (err) {
	  	 			try {
  		 				Auswahl = panel.childNodes[3].firstChild;
  		 			} catch (err) {Auswahl = 1;}
  	 			}
			}
		});
	} catch (err) {}
	if (Auswahl == null) {Auswahl = 1;}
	try {
		if (Auswahl.id == "") {Auswahl = 1;}
	} catch (err) {Auswahl = 1;}
	return Auswahl;
}

function Pos_sync(){
document.getElementById("ausgewaehlt").style.top = document.getElementById("Element_Dialog").oben.value;
document.getElementById("ausgewaehlt").style.left = document.getElementById("Element_Dialog").links.value;
}

function rgbToHex(Wert) {
	Wert = Wert.substr(4,Wert.length - 5)
	Wert = Wert.split(", ");
	return "#" + ((1 << 24) + (parseInt(Wert[0]) << 16) + (parseInt(Wert[1]) << 8) + parseInt(Wert[2])).toString(16).slice(1);
}

function hexToRgb(Wert){
	if (Wert.substr(0,1) == "#") {Wert = Wert.substr(1,Wert.length);}
	var r = parseInt((Wert).substring(0,2),16);
	var g = parseInt((Wert).substring(2,4), 16);
	var b = parseInt((Wert).substring(4,6),16);
	return "rgb(" + r.toString() + "," + g.toString() + "," + b.toString() + ")";
}

function Code_Dialog(){
	var Inhalt = "<textarea id='code_text' style='width: 100%;'>" + document.getElementById("js_code").value + "</textarea><input class='Schalter_Element' name='Code_übernehmen' value='" + T_Text[37] + "' type='button' onclick='Code_uebernehmen()'>";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'js_code_dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '1000 600',
		headerTitle: 'Code',
		position: 'left-top 300 30',
		content: Inhalt,
	});
	document.getElementById("code_text").style.height = (parseInt(document.getElementById("js_code_dialog").content.style.height) - 30) + "px";
}

function Code_uebernehmen(){
	document.getElementById("js_code").value = document.getElementById("code_text").value;
	document.getElementById("js_code_dialog").close();
}

function Tab_umschalten(zeigen) {
	for (i=1; i < 4; i++) {
		if (i == zeigen) {
			document.getElementById("Tab" + i).style.display="block";
		} else {
			document.getElementById("Tab" + i).style.display="none";
		}
	}

}

function CSS_Dialog_oeffnen() {
	var Inhalt = '<div style="position: absolute; top: 10px; left: 10px;"><form name="CSS_Dialog">\n';
	Inhalt = Inhalt + "<div class='dtree' id='css_Baum'></div>";
	dcss = new dTree('dcss');
	dcss.add(0,-1,'Style Sheets');
	for (i = 0; i < document.styleSheets.length; i++) {
		Dateiname = document.styleSheets[i].href;
		if (Dateiname != null) {
			while (Dateiname.indexOf("/") > -1) {
				Dateiname = Dateiname.substr(Dateiname.indexOf("/") + 1,Dateiname.length);
			}
			dcss.add(i+1,0,Dateiname);
			for (x = 0; x < document.styleSheets[i].cssRules.length; x++) {
				dcss.add((i+1).toString() + "_" + x.toString(),(i+1).toString(),document.styleSheets[i].cssRules[x].selectorText,"javascript: style_zeigen(" + i + "," + x + ");");
			}
		}
	}
	Inhalt = Inhalt + "</form></div>\n";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'CSSauswahl',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '610 345',
		headerTitle: T_Text[60],
		position: 'left-top 30 30',
		content: Inhalt,
	});
	document.getElementById("css_Baum").innerHTML= dcss.toString();
}

function style_zeigen(i,x) {
	var tempText = document.styleSheets[i].cssRules[x].cssText;
	var Text = "<div style='position: absolute; top: 10px; left: 10px;'><table cellpadding = '5' cellspaceing = '10'>";
	var Stil = tempText.substr(0,tempText.indexOf("{"));
	while (Stil.substr(0,1) == " " || Stil.substr(0,1) == ".") {
		Stil = Stil.substr(1, Stil.length);
	}
	while (Stil.substr(Stil.length - 1,Stil.length) == " ") {
		Stil = Stil.substr(0, Stil.length - 1);
	}
	tempText = tempText.substr(tempText.indexOf("{") + 1,tempText.length);
	while (tempText.length > 5) {
		tempText2 = tempText.substr(0, tempText.indexOf(";") + 1);
		tempText2 = tempText2.split(":");
		Text = Text + "<tr class='Tabellenzeile'><td>" + tempText2[0] + "</td><td>" + tempText2[1] + "</td></tr>"; 
		tempText = tempText.substr(tempText.indexOf(";") + 1,tempText.length)
	}
	Text = Text + "</table><br><br><input type='button' value='" + T_Text[37] + "' class='Schalter_Element' onclick='Stil_hinzufuegen(\"" + Stil + "\");'></div>";
	try {document.getElementById("Dialog_CSSRegel").close();} catch (err) {}

	jsPanel.create({
		dragit: {
     		snap: true
     	},
		id: 'Dialog_CSSRegel',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '330 345',
		headerTitle: T_Text[61],
		position: 'left-top 640 30',
		content: Text,
	});
}

function Stil_hinzufuegen(Stil) {
	if (document.forms.Element_Dialog.class.value.length > 0) {document.forms.Element_Dialog.class.value = document.forms.Element_Dialog.class.value + " ";}
	if(Stil.substr(-1) == " ") {Stil = Stil.substr(0,Stil.length - 1);}
	document.forms.Element_Dialog.class.value = document.forms.Element_Dialog.class.value + Stil;
}

function allowDrop(ev) {
	if (ausgewaehlt == 0) {ev.preventDefault();}
}

function drag(ev) {
	zuverschiebendesElement = ev.target.id;
	ev.dataTransfer.setData("text", ev.target.id);
	var tempdiv = document.getElementById(ev.target.id);
	var tempoben = 0;
	var templinks = 0;
	while (tempdiv.id != "db_Bereich") {
		if (tempdiv.style.top != "") {tempoben = tempoben + parseInt(tempdiv.style.top);}
		if (tempdiv.style.left != "") {templinks = templinks + parseInt(tempdiv.style.left);}
		tempdiv = tempdiv.parentElement;
	}
	MausStartY = ev.clientY - tempoben;
	MausStartX = ev.clientX - templinks;
}

function drop(ev) {
	ev.preventDefault();
	try {
		var data = zuverschiebendesElement;
	} catch (err) {var data = ev.target.id;}
	if (ev.target.id == "db_Bereich") {
		ev.target.appendChild(document.getElementById(data));
		document.getElementById(data).elternelement = 0;
		document.getElementById(data).style.top = (ev.clientY - MausStartY).toString() + "px";
		document.getElementById(data).style.left = (ev.clientX - MausStartX).toString() + "px";
	} else {
		if (ev.target.hasAttribute("kann_eltern") == true) {
			if (ev.target.attributes.kann_eltern == "0") {return;}
		} else {return;}
		tempdiv = ev.target;
		oben = 0;
		links = 0;
		while (tempdiv.id != "db_Bereich") {
			oben = oben + parseInt(tempdiv.style.top);
			links = links + parseInt(tempdiv.style.left);
			tempdiv = tempdiv.parentElement;
		}
		ev.target.appendChild(document.getElementById(data));
		oben = ev.clientY - oben - MausStartY;
		if (oben < 0) {oben = oben * -1;}
		document.getElementById(data).style.top = oben.toString() + "px";
		links = ev.clientX - links - MausStartX;
		if (links < 0) {links = links * -1;}
		document.getElementById(data).style.left = links.toString() + "px";
	}
	try {
		if (document.getElementById(data).attributes.feldtyp.value == "Option" && ev.target.id != "db_Bereich") {
			document.getElementById(data).name = document.getElementById(ev.target.id).firstChild.attributes.name.value;
		}
	} catch (err) {}
	reinigen();
}

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["Einstellungen"].aktion.value = T_Text[1];
	} else {
		document.forms["Einstellungen"].aktion.value = "löschen";
	}
	abspeichern();
	document.forms["Einstellungen"].submit();
}
	
function Deckblatt_umschalten(Teil) {
	if (Teil == "Deckblatt") {
		document.getElementById("deckblatt_zeigen").style.display = "none";
		document.getElementById("bericht_zeigen").style.display = "block";
		document.getElementById("deckblattbereich").style.display = "block";
		document.getElementById("arbeitsbereich").style.display = "none";
	}else {
		document.getElementById("deckblatt_zeigen").style.display = "block";
		document.getElementById("bericht_zeigen").style.display = "none";
		document.getElementById("deckblattbereich").style.display = "none";
		document.getElementById("arbeitsbereich").style.display = "block";
	}
}

function reinigen() {
	Elemente = document.getElementsByClassName("rectangle");
	while (Elemente.length > 0) {
		Elemente[0].parentElement.removeChild(Elemente[0]);
	}
	var ausgew = document.getElementById("ausgewaehlt");
	try {ausgew.parentElement.removeChild(document.getElementById("ausgewaehlt"));} catch (err) {}
}

function bed_Format_Dialog() {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table cellspan='3px' cellpadding='2px' id='bed_Tab'>";
	Inhalt += '<tr class="Tabellenzeile"><td class="Tabelle_Ueberschrift">' + T_Text[114] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[109] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[110] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[112] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[113] + '</td><td></td><td align="right"><input type="button" name="Hilfe" class="Schalter_Element" value="' + T_Text[12] + '" onclick="Hilfe_Fenster(\'41\');"></td></tr>';
	i = 1;
	while (Bedingungen[i] != undefined) {
		Inhalt += '<tr class="Tabellenzeile"><td>' + i.toString() + '</td><td>' + Bedingungen[i].Bedingung + '</td><td>' + Bedingungen[i].Element + '</td><td>' + Bedingungen[i].Kommentar + '</td><td><div style="' + Bedingungen[i].Stil + '" class="' + Bedingungen[i].class + '">' + T_Text[115] + '</div></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[47] + '" onclick="bed_Dialog(\'' + i.toString() + '\');"></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[10] + '" onclick="bed_entfernen(\'' + i.toString() + '\');"></td></tr>';
		i = i + 1;
	}
	Inhalt += '</table><table><tr style="height: 50px;"><td colspan="2"><input class="Schalter_Element" type="button" name="uebernehmen" value="' + T_Text[37] + '" onclick="Bed_Format_uebernehmen();"></td>';
	Inhalt += '<td colspan="2"><input class="Schalter_Element" type="button" name="neu" value="' + T_Text[48] + '" onclick="bed_Dialog(\'neu\');"></td></tr>';
	Inhalt += '</table></div>';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Bedingte_Formatierung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '480 360',
		headerTitle: T_Text[49],
		position: 'left-top 10 60',
		content: Inhalt
	});
}

function bed_Dialog(BedNr) {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table>";
	Inhalt += '<tr><td align="right">' + T_Text[109] + '</td><td colspan="2"><textarea style="width: 210px; height: 16px;" class="Text_Element" id="bedingung_feld" name="Bedingung_Feld"></textarea></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[110] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="element_feld" name="Element_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[112] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="kommentar_feld" name="Kommentar_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td colspan="3"><hr></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[71] + '</td><td><select class="Auswahl_Liste_Element" id="sichtbar_feld" name="sichtbar_Feld" value=""><option value="" selected></option><option value="none">none</option><option value="block">block</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[50] + '</td><td><select class="Auswahl_Liste_Element" id="border-style_feld" name="border-style_Feld" value="undefined"><option value="solid">durchgezogen</option><option value="dotted">gepunktet</option><option value="dashed">gestrichelt</option><option value="" selected=""></option><option value="double">doppelt</option><option value="groove">Rille</option><option value="ridge">Grat</option><option value="inset">innen gesetzt</option><option value="outset">außen gesetzt</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[51] + '</td><td><input class="Text_Element" id="rahmenfarbe_feld" name="Rahmenfarbe_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[52] + '</td><td><input style="width: 40px;" class="Text_Element" id="rahmenbreite_feld" name="Rahmenbreite_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[72] + '</td><td colspan="2"><select class="Auswahl_Liste_Element" id="font-family_feld" name="font-family_Feld" value=""><option value="arial, helvetica, sans-serif">arial, helvetica, sans-serif</option><option value="roman, times new roman, times, serif">roman, times new roman, times, serif</option><option value="courier, fixed, monospace">courier, fixed, monospace</option><option value="western, fantasy">western, fantasy</option><option value="Zapf-Chancery, cursive">Zapf-Chancery, cursive</option><option value="serif">serif</option><option value="sans-serif">sans-serif</option><option value="cursive">cursive</option><option value="fantasy">fantasy</option><option value="monospace">monospace</option><option value=""></option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[53] + '</td><td><select class="Auswahl_Liste_Element" id="font-style_feld" name="font-style_Feld" value=""><option value="" selected></option><option value="normal">normal</option><option value="italic">kursiv</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[73] + '</td><td><input class="Text_Element" id="color_feld" name="color_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[54] + '</td><td><input class="Text_Element" id="background-color_feld" name="background-color_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[55] + '</td><td><select class="Auswahl_Liste_Element" id="font-weight_feld" name="font-weight_Feld" value=""><option value=""></option><option value="normal">normal</option><option value="bold">fett</option><option value="bolder">fetter</option><option value="lighter">dünner</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[74] + '</td><td><input style="width: 40px;" class="Text_Element" id="font-size_feld" name="font-size_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[56] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" title="' + T_Text[57] + '" id="class_feld" name="class_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 50px;"><td align="right"><input class="Schalter_Element" type="button" name="uebernehmen_Feld" value="' + T_Text[37] + '" onclick="Bedingung_uebernehmen(\'' + BedNr.toString() + '\');"></td>';
	Inhalt += '<td align="left"><input class="Schalter_Element" type="button" name="cssdialog_Feld" value="' + T_Text[58] + '" onclick="CSS_Dialog_oeffnen();"></td></tr>';
	Inhalt += '</table></div>';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'bed_Format_Einstellung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '340 440',
		headerTitle: T_Text[59],
		position: 'left-top 340 60',
		content: Inhalt
	});
	if (BedNr != "neu") {
		Zeile = document.getElementById("bed_Tab").firstChild.childNodes[BedNr];
		document.getElementById("bedingung_feld").innerHTML = Bedingungen[BedNr]["Bedingung"];
		document.getElementById("element_feld").value = Bedingungen[BedNr]["Element"];
		document.getElementById("kommentar_feld").value = Bedingungen[BedNr]["Kommentar"];
		document.getElementById("font-family_feld").value = Bedingungen[BedNr]["font-family"];
		document.getElementById("font-style_feld").value = Bedingungen[BedNr]["font-style"];
		document.getElementById("color_feld").value = Bedingungen[BedNr]["color"];
		document.getElementById("font-size_feld").value = Bedingungen[BedNr]["font-size"];
		document.getElementById("font-weight_feld").value = Bedingungen[BedNr]["font-weight"];
		document.getElementById("class_feld").value = Bedingungen[BedNr]["class"];
		document.getElementById("sichtbar_feld").value = Bedingungen[BedNr]["sichtbar"];
		document.getElementById("rahmenfarbe_feld").value = Bedingungen[BedNr]["Rahmenfarbe"];
		document.getElementById("border-style_feld").value = Bedingungen[BedNr]["border-style"];
		document.getElementById("rahmenbreite_feld").value = Bedingungen[BedNr]["Rahmenbreite"];
		document.getElementById("background-color_feld").value = Bedingungen[BedNr]["background-color"];
	}
}

function Bedingung_uebernehmen(BedNr) {
	BedNr_orig = BedNr;
	if (BedNr == "neu") {
		Zeile = document.createElement("tr");
		Zeile.setAttribute("class","Tabellenzeile");
		BedNr = document.getElementById("bed_Tab").firstChild.childNodes.length;
	} else {
		Zeile = document.getElementById("bed_Tab").firstChild.childNodes[BedNr];
	}
	Bedingungen[BedNr] = {};
	Bedingungen[BedNr].orig_Stil = "";
	Bedingungen[BedNr].orig_class = "";
	Zellen = "<td>" + BedNr.toString() + "</td>";
	Zellen = Zellen + '<td>' + document.getElementById("bedingung_feld").value + '</td>';
	Bedingungen[BedNr]["Bedingung"] = document.getElementById("bedingung_feld").value;
	Zellen = Zellen + '<td>' + document.getElementById("element_feld").value + '</td>';
	Bedingungen[BedNr]["Element"] = document.getElementById("element_feld").value;
	Zellen = Zellen + '<td>' + document.getElementById("kommentar_feld").value + '</td>';
	Bedingungen[BedNr]["Kommentar"] = document.getElementById("kommentar_feld").value;
	Bedingungen[BedNr]["sichtbar"] = document.getElementById("sichtbar_feld").value;
	Bedingungen[BedNr]["font-family"] = document.getElementById("font-family_feld").value;
	Bedingungen[BedNr]["font-style"] = document.getElementById("font-style_feld").value;
	Bedingungen[BedNr]["color"] = document.getElementById("color_feld").value;
	Bedingungen[BedNr]["font-size"] = document.getElementById("font-size_feld").value;
	Bedingungen[BedNr]["font-weight"] = document.getElementById("font-weight_feld").value;
	Bedingungen[BedNr]["class"] = document.getElementById("class_feld").value;
	Bedingungen[BedNr]["Rahmenfarbe"] = document.getElementById("rahmenfarbe_feld").value;
	Bedingungen[BedNr]["border-style"] = document.getElementById("border-style_feld").value;
	Bedingungen[BedNr]["Rahmenbreite"] = document.getElementById("rahmenbreite_feld").value;
	Bedingungen[BedNr]["background-color"] = document.getElementById("background-color_feld").value;
	Stil = "";
	if (Bedingungen[BedNr]["sichtbar"] == "none") {Stil = Stil + "display: none; ";}
	if (Bedingungen[BedNr]["font-family"] != "") {Stil = Stil + "font-family: " + Bedingungen[BedNr]["font-family"] + "; ";}
	if (Bedingungen[BedNr]["font-style"] != "") {Stil = Stil + "font-style: " + Bedingungen[BedNr]["font-style"] + "; ";}
	if (Bedingungen[BedNr]["color"] != "") {Stil = Stil + "color: " + hexToRgb(Bedingungen[BedNr]["color"]) + "; ";}
	if (Bedingungen[BedNr]["font-size"] != "") {Stil = Stil + "font-size: " + Bedingungen[BedNr]["font-size"] + "; ";}
	if (Bedingungen[BedNr]["font-weight"] != "") {Stil = Stil + "font-weight: " + Bedingungen[BedNr]["font-weight"] + "; ";}
	if (Bedingungen[BedNr]["Rahmenfarbe"] != "") {Stil = Stil + "border-color: " + hexToRgb(Bedingungen[BedNr]["Rahmenfarbe"]) + "; ";}
	if (Bedingungen[BedNr]["border-style"] != "") {Stil = Stil + "border-style: " + Bedingungen[BedNr]["border-style"] + "; ";}
	if (Bedingungen[BedNr]["Rahmenbreite"] != "") {Stil = Stil + "border-width: " + Bedingungen[BedNr]["Rahmenbreite"] + "; ";}
	if (Bedingungen[BedNr]["background-color"] != "") {Stil = Stil + "background-color: " + hexToRgb(Bedingungen[BedNr]["background-color"]) + "; ";}
	Klasse = "";
	if (Bedingungen[BedNr]["class"] != "") {Klasse = "class='" + Bedingungen[BedNr]["class"] + "' ";}
	Bedingungen[BedNr]["Stil"] = Stil;
	Zellen = Zellen + '<td><div ' + Klasse + 'style="' + Stil + '">' + T_Text[46] + '</div></td>';
	Zellen = Zellen + '<td><input class="Schalter_Element" type="button" value="' + T_Text[47] + '" onclick="bed_Dialog(\'' + BedNr.toString() + '\');"></td>';
	Zellen = Zellen + '<td><input class="Schalter_Element" type="button" value="' + T_Text[10] + '" onclick="bed_entfernen(\'' + BedNr.toString() + '\');"></td>';
	Zeile.innerHTML = Zellen;

	if (BedNr_orig == "neu") {
		document.getElementById("bed_Tab").firstChild.appendChild(Zeile);
	}
	
	bed_Format_Einstellung.close();
}

function bed_entfernen(BedNr) {
	document.getElementById("bed_Tab").firstChild.removeChild(document.getElementById("bed_Tab").firstChild.childNodes[BedNr]);
	delete Bedingungen[BedNr];
}

function Bed_Format_uebernehmen() {
	i = 1;
	while(Bedingungen[i] != undefined) {
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\'/g,"µµµ");
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\"/g,"µµµ");
		i = i + 1;
	}
	document.getElementById("bed_Format").value = JSON.stringify(Bedingungen);
	Bedingte_Formatierung.close();
	try {bed_Format_Einstellung.close();} catch (err) {}
}

function Elem(Elementname) {
	for (i = 0; i < Bereich.childNodes.length; i++) {
		try {
			if (Bereich.childNodes[i].attributes.elementname.value == Elementname) {
				try {return Bereich.childNodes[i].innerHTML;} catch (err) {}
				return Bereich.childNodes[i].value;
			}
		} catch (err) {}
	}
	return "Error";
}

function Element_kopieren(Auswahl) {
	var Auswahl = Element_aus_Fenster();
	var cln = Auswahl.cloneNode(true);
	if (document.getElementById(akt_Bereich) == "") {
		Bereich = db_Bereich;
	} else {
		Bereich = document.getElementById(akt_Bereich);
	}
	Bereich.appendChild(cln);
	var jetzt = new Date();
	var Zeitpunkt = jetzt.getTime();
	var id = Auswahl.attributes.feldtyp.value + Zeitpunkt.toString();
	cln.setAttribute('id',id);
	if (Bereich.id == "db_Bereich") {
		cln.style.top = "60px";
	} else {
		cln.style.top = "0px";
	}
	try {cln.removeAttribute("class");} catch (err) {}
	try {cln.setAttribute("class", cln.attributes.class1.value);} catch (err) {}
	Auswahl_beenden();
	auswaehlen(cln);
	Auswahl_beenden();
	//neues Element markieren
	cln.setAttribute('neu','1');
	cln.setAttribute('hintergrundfarbe_orig',cln.style.backgroundColor);
	cln.style.backgroundColor = "#FF0000";
	Auswahl.className = 'context-menu-one';
	var erledigt = 0;
	var temp = cln;
	var Kopieliste = [];
	var Kopie = [];
	Kopie["erledigt"] = 0;
	Kopie["id"] = temp.id;
	Kopie["orig_id"] = Auswahl.id;
	Kopieliste[0] = Kopie;
	var Kopie = [];
	x = 1;
	while (erledigt == 0) {
		erledigt = 1;
		temp = null;
		for (z = 0; z < Kopieliste.length; z++) {
			if (Kopieliste[z]["erledigt"] == 0) {
				Kopieliste[z]["erledigt"] = 1;
				if (Kopieliste[z].id > "" && Kopieliste[z].id != undefined) {
					temp = document.getElementById(Kopieliste[z].id);
					z = Kopieliste.length
				}
			}
		}
		if (temp != null) {
			for (i = 0; i < temp.childNodes.length; i++) {
				Kopie["orig_id"] = temp.childNodes[i].id;
				if(temp.childNodes[i].id > "" && temp.childNodes[i].id != undefined) {
					try {temp.childNodes[i].id = temp.childNodes[i].attributes.feldtyp.value + Zeitpunkt.toString();} catch (err) {}
				} else {
					temp.childNodes[i].id = "";
				}
				if (temp.childNodes[i].childNodes.length > 0) {
					Kopie["erledigt"] = 0;
				} else {
					Kopie["erledigt"] = 1;
				}
				Kopie["id"] = temp.childNodes[i].id;
				Kopieliste.push(x);
				Kopieliste[x] = Kopie;
				Kopie = [];
				x = x + 1;
			}
			for (z = 0; z < Kopieliste.length; z++) {
				if (Kopieliste[z]["erledigt"] == 0) {
					erledigt = 0;
					z = Kopieliste.length
				}
			}
		}
	}
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

function Navi_Dialog() {
	try {navigationsdialog.close();} catch (err) {}
	var Baum = [];
	var Zweig = {};
	var akt_Element = document.getElementById("db_Bereich");
	Zweig.ID = "db_Bereich";
	Zweig.erledigt = 0;
	Zweig.eltern_id = "db_Bereich";
	Baum[0] = Zweig;
	Zweig = {};
	x = 1;
	var alles_erledigt = 0;
	while (alles_erledigt == 0) {
		alles_erledigt = 1;
		for (z = 0; z < Baum.length; z++) {
			if (Baum[z].erledigt == 0) {
				Baum[z].erledigt = 1;
				akt_Element = document.getElementById(Baum[z].ID);
				alles_erledigt = 0;
				break;
			}
		}
		if (alles_erledigt == 0) {
			for (i = 0; i < akt_Element.childNodes.length; i++) {
				if (akt_Element.childNodes[i].id != undefined && akt_Element.childNodes[i].id != "" && akt_Element.childNodes[i].id.substr(0,11) != "kopftabelle") {
					Zweig.ID = akt_Element.childNodes[i].id;
					Zweig.eltern_id = akt_Element.id;
					if (akt_Element.childNodes[i].childNodes.length > 0) {
						Zweig.erledigt = 0;
					} else {
						Zweig.erledigt = 1;
					}
					Baum[x] = Zweig;
					Zweig = {};
					x = x + 1;
				}
			}
		}
	}

	var Inhalt = '<div style="position: absolute; top: 40px; left: 10px;">\n';
	Inhalt = Inhalt + "<div class='dtree' id='Tbaum'></div>";
	d = new dTree('d');
	d.add('db_Bereich',-1,T_Text[116],"");
	for (i = 1; i < Baum.length; i++) {
		d.add(Baum[i].ID,Baum[i].eltern_id,Baum[i].ID,"javascript: Elem_aus_Liste_markieren(" + Baum[i].ID + ");");
	}
	Inhalt = Inhalt + "</div>\n";
	Inhalt = Inhalt + '<div style="position: absolute; top: 5px; left: 10px;"><input class="Schalter_Element" type="button" value="' + T_Text[118] + '" onclick="Element_Dialog_oeffnen();"></div>\n';
	jsPanel.create({
		dragit: {
			snap: true
		},
		id: 'navigationsdialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '220 400',
		headerTitle: T_Text[117],
		position: 'left-top ' + (frameElement.clientWidth - 220).toString() + ' 44',
		content: Inhalt
	});
	document.getElementById("Tbaum").innerHTML= d.toString();
}
