jsPanel.defaults.resizeit.minWidth = 20;
jsPanel.defaults.resizeit.minHeight = 10;
var markierte_elem = [];
var Eigenschaften_Objekt = {};
DH_Elemente=[];
var T_Text = new Array;
var ausgewaehlt = 0; 
var Elternelement = null;
var Registerkarten = [];
var MausStartX = 0;
var MausStartY = 0;
var nichtauswaehlen = 0;
var zuverschiebendesElement = "";
var Bedingungen = {};
var id_alt = "";

$(window).on('load',function() {;
	initDraw(document.getElementById('db_Bereich'));
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
			"Eigenschaften": {"name": T_Text[8], "icon": "edit"},
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
});

function Gruppierung_aufheben(Auswahl) {
	Auswahl = Element_aus_Fenster();
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

function mehrere_bearbeiten() {
	jQuery.ajax({
		url: "Daten_lesen.php",
		success: function (html) {
  			strReturn = html;
		},
		type: 'POST',
		data: {query: 'SELECT * FROM `Vorlageneigenschaften`;'},
		async: false
	});
	Werte = JSON.parse(strReturn);
	for (x = 0; x < Werte.length; x++) {
		vorhanden = 0;
		for (i = 0; i < markierte_elem.length; i++) {
			if (document.getElementById(markierte_elem[i]).style[Werte[x]["orig_Name"]] == undefined) {
				if (document.getElementById(markierte_elem[i]).hasAttribute(Werte[x]["orig_Name"]) == true) {vorhanden = vorhanden + 1;}
			} else {
				vorhanden = vorhanden + 1;
			}
		}
		if (vorhanden < markierte_elem.length || Werte[x].Eigenschaft == "Feld" || Werte[x].Eigenschaft == "Feldtyp" || Werte[x].Eigenschaft == "Elementname" || Werte[x].Eigenschaft == "font-style") {
			Werte.splice(x,1);
			x = x - 1;
		}
	}
	//Dialog aufbauen
	try {mehrere_Elementeinstellungen.close();} catch (err) {}
	try {Elementeinstellungen.close();} catch (err) {}
	var Sprache = document.getElementById("sprache").value;
	var DialogHoehe=145;
	var Zeilen = {};
	Zeilen[1] = 0;
	Zeilen[2] = 0;
	Zeilen[3] = 0;
	var Inhalt = "<div style=\"position: absolute; top: 10px; left: 10px;\">\n";
	Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[69] + "' type='button' onclick='Tab_umschalten(1);'>\n";
	Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[70] + "' type='button' onclick='Tab_umschalten(2);'>\n";
	Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[71] + "' type='button' onclick='Tab_umschalten(3);'><br><br>\n";
	Inhalt = Inhalt + "<div id='Tabs'>\n<form id='Element_Dialog' name='Element_Dialog'>\n";
	var Inhalt1 = "<div id='Tab1' style='display: block;'>\n";
	Inhalt1 = Inhalt1 + "<table cellpadding='2px'>\n<tr><td></td><td>" + T_Text[83] + "</td><td></td></tr>\n";
	var Inhalt2 = "<div id='Tab2' style='display: none;'><table cellpadding='2px'>\n<tr><td></td><td>" + T_Text[83] + "</td><td></td></tr>\n";
	var Inhalt3 = "<div id='Tab3' style='display: none;'><table cellpadding='2px'>\n<tr><td></td><td>" + T_Text[83] + "</td><td></td></tr>\n";

	for (x = 0; x < Werte.length; x++) {
		if (Werte[x].Attributtyp == "style") {
			Stil = '1';
		} else {
			Stil = '0';
		}
		if (Werte[x].Tab == 'allgemein') {
			var Inhalt_temp = Inhalt1;
			var Karte = 1;
			Zeilen[1] = Zeilen[1] + 1;
		} else {
			if (Werte[x].Tab == 'Scripte') {
				var Inhalt_temp = Inhalt2;
				var Karte = 2;
				Zeilen[2] = Zeilen[2] + 1;
			} else {
				if (Werte[x].Tab == 'Format') {
					var Inhalt_temp = Inhalt3;
					var Karte = 3;
					Zeilen[3] = Zeilen[3] + 1;
				}
			}
		}
		Inhalt_temp += "<tr><td align='right'>" + Werte[x][Sprache] + "</td><td align='center'><input class='Text_Element' id='" + Werte[x].orig_Name + "editieren' name='" + Werte[x].orig_Name + "editieren' value='0' type='checkbox' onclick='Feld_sichtbar(\"" + Werte[x].orig_Name + "\");'></td><td>";
		if (Werte[x].Darstellung_Dialog == "Textarea") {
			Inhalt_temp += "<Textarea class='Text_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' style='display: none;'></Textarea>";
		}
		if (Werte[x].Darstellung_Dialog == "Schalter") {
			Zusatz = "";
			if (Werte[x].Vorlage == 'Register') {Zusatz = " onclick='Dialog_Registerkarten_oeffnen(" + id + ");'";}
			Inhalt_temp += "<input class='Schalter_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='bearbeiten' type='button'" + Zusatz + " style='display: none;'>";
		}
		if (Werte[x].Darstellung_Dialog == "Textfeld") {
			Zusatz = "";
			Inhalt_temp += "<input class='Text_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='' type='Text'" + Zusatz + " style='display: none;'>";
		}
		if (Werte[x].orig_Name.indexOf("color") > -1) {
			Inhalt_temp += "<input class='Text_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='' type='color' style='display: none;'>";
		}
		if (Werte[x].Darstellung_Dialog == "Auswahl") {
			if (Werte[x].Eigenschaft == "Feld") {
				SelectListe = Werte[x].Eigenschaft.toLocaleLowerCase() + "liste";
				SelectListe = document.getElementById(SelectListe).value.split("@@@");
				Inhalt_temp += "<select class='Auswahl_Liste_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='' style='display: none;'>";
			} else {
				jQuery.ajax({
					url: "./Optionen_lesen.php?Vorlageneigenschaften_ID=" + Werte[x].Vorlageneigenschaften_ID,
					success: function (html) {
 							Optionen = html;
					},
						async: false
 					});
 					if (Werte[x].Wert == undefined) {
 						links = Optionen.indexOf("<option value=''></option>");
 						rechts = Optionen.length - links -26;
 						Optionen = Optionen.substr(0,links) + "<option value='' selected></option>" + Optionen.substr(Optionen.length - rechts);
 					}
				Inhalt_temp += "<select class='Auswahl_Liste_Element' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='" + Werte[x].Wert + "' style='display: none;'>";
				Inhalt_temp += Optionen;
				Inhalt_temp += "</select>";
			}
		}
		if (Werte[x].Darstellung_Dialog == "Checkbox") {
				Inhalt_temp += "<input class='Text_Element' title='" + Werte[x]["Hinweis_" + Sprache] + "' id='" + Werte[x].orig_Name + "' stil = '"+ Stil + "' name='" + Werte[x].orig_Name + "' value='' type='checkbox' style='display: none;'>";
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

	Inhalt1 += "</table></div>\n";
	Inhalt2 += "</table></div>\n";
	Inhalt3 += "</table></div>\n";
	Inhalt += Inhalt1 + Inhalt2 + Inhalt3;
	Inhalt += "</form><br><input class='Text_Element' type='button' name='uebernehmen' value='" + T_Text[37] + "' onclick='mehrere_Element_Dialog_uebernehmen();'>\n";
	Inhalt += "&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' type='button' name='cssdialog' value='" + T_Text[65] + "' onclick='CSS_Dialog_oeffnen();'>\n";
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
		id: 'mehrere_Elementeinstellungen',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '420 ' + DialogHoehe.toString(),
		headerTitle: T_Text[72],
		position: 'left-top 65 5',
		contentOverflow: 'scroll scroll',
		content: Inhalt
	});
}
function Feld_sichtbar(Feldname) {
	if (document.getElementById(Feldname + "editieren").checked == true) {
		document.getElementById(Feldname).style.display = "block";
	} else {
		document.getElementById(Feldname).style.display = "none";	
	}
}

function mehrere_Element_Dialog_uebernehmen() {
	var Sprache = document.getElementById("sprache").value;
	Formular = document.getElementById("Element_Dialog").elements;
	for (x=0; x < Formular.length; x++) {
		if (Formular[x].style.display == "block") {
			try {
				if (Formular[x].type == "checkbox") {
					if (Formular[x].checked == true) {
						Formular[x].value = 1;
					} else {
						Formular[x].value = 0;
					}
				}
			} catch (err) {}
			try {
				if (Formular[x].id != "onclick") {
					if (Formular[x].id == "css_Stil" || Formular[x].id == "css_stil" || Formular[x].id == "class") {
						if (Formular[Formular[x].id].value > "") {
							for (i = 0; i < markierte_elem.length; i++) {
								document.getElementById(markierte_elem[i]).setAttribute("class1", Formular[x].value);
							}
						} else {
							for (i = 0; i < markierte_elem.length; i++) {
								delete document.getElementById(markierte_elem[i]).removeAttribute("class1");
							}
						}
					}
					if (Formular[Formular[x].id].value > "") {
						for (i = 0; i < markierte_elem.length; i++) {
							if (Formular[x].attributes["stil"].value == '0') {
								document.getElementById(markierte_elem[i]).setAttribute(Formular[x].id, Formular[x].value);
							} else {
								if (Formular[x].id == "display") {
									if (Formular[x].value == "none") {document.getElementById(markierte_elem[i]).style[Formular[x].id] = "block";}
									document.getElementById(markierte_elem[i]).setAttribute("display1",Formular[x].value);
								} else {
									document.getElementById(markierte_elem[i]).style[Formular[x].id] = Formular[x].value;
								}
							}
						}
					} else {
						for (i = 0; i < markierte_elem.length; i++) {
							if (Formular[x].attributes["stil"].value == '0') {
								document.getElementById(markierte_elem[i]).removeAttribute(Formular[x].id);
							} else {
								document.getElementById(markierte_elem[i]).style[Formular[x].id] = "";
							}
						}
					}
				} else {
					for (i = 0; i < markierte_elem.length; i++) {
						document.getElementById(markierte_elem[i]).setAttribute("onclick1", Formular[x].value);
					}
				}
			} catch (err) {}
		}
	}
	try {mehrere_Elementeinstellungen.close();} catch (err) {}
}

function ausrichten(key) {
	if (key == "Eigenschaften") {mehrere_bearbeiten();}
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
		//newdiv.setAttribute('class',"context-menu-two context-menu-active");
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
			Element.className = 'context-menu-two';
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

function initDraw(Elternelement) {
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
	Elternelement.onmousemove = function (e) {
		setMousePosition(e);
		if (element !== null) {
			element.style.width = Math.abs(mouse.x - mouse.startX) + 'px';
			element.style.height = Math.abs(mouse.y - mouse.startY) + 'px';
			element.style.left = (mouse.x - mouse.startX < 0) ? mouse.x + 'px' : mouse.startX + 'px';
			element.style.top = (mouse.y - mouse.startY < 0) ? mouse.y + 'px' : mouse.startY + 'px';
		}
	}
	Elternelement.onmouseup = function (e) {
	//Falls die STRG - Taste gedrückt wurde, dann den Rest ignorieren.
		if (event.ctrlKey == false) {
			for (i=0; i < markierte_elem.length; i++) {
				try {document.getElementById(markierte_elem[i]).style.opacity = "";} catch (err) {}
			}
			if (element !== null) {
				markierte_elem = [];
				for (i=0; i < Elternelement.childNodes.length; i++) {
					try {
						if (parseInt(Elternelement.childNodes[i].style.top) > parseInt(element.style.top)) {
							if (parseInt(Elternelement.childNodes[i].style.top) + parseInt(Elternelement.childNodes[i].style.height) < parseInt(element.style.top) + parseInt(element.style.height)) {
								if (parseInt(Elternelement.childNodes[i].style.left) > parseInt(element.style.left)) {
									if (parseInt(Elternelement.childNodes[i].style.left) + parseInt(Elternelement.childNodes[i].style.width) < parseInt(element.style.left) + parseInt(element.style.width)) {
										Elternelement.childNodes[i].style.opacity = 0.5;
										Elternelement.childNodes[i].className = 'context-menu-one';
										markierte_elem.push(Elternelement.childNodes[i].id);
									}
								}
							}
						}
					} catch (err) {}
				}
				try {Elternelement.removeChild(element);} catch (err) {}
				element = null;
				Elternelement.style.cursor = "default";
			} else {
				Auswahl_beenden();
				mouse.startX = mouse.x;
				mouse.startY = mouse.y;
				element = document.createElement('div');
				element.className = 'rectangle'
				element.style.left = mouse.x + 'px';
				element.style.top = mouse.y + 'px';
				Elternelement.appendChild(element)
				Elternelement.style.cursor = "crosshair";
			}
		}
	}
}

function abspeichern() {
	try {Gruppierung_aufheben();} catch (err) {}
	try {Auswahl_beenden();} catch (err) {}
	for (i=0; i < document.getElementById("db_Bereich").childNodes.length; i++) {
		try {
			if (document.getElementById("db_Bereich").childNodes[i].attributes['class'].value == "rectangle") {
				document.getElementById("db_Bereich").removeChild(document.getElementById("db_Bereich").childNodes[i]);
				i = i - 1;
			}
		} catch (err) {}
	}
	document.getElementById("db_bereich").value = document.getElementById("db_Bereich").innerHTML;
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

function auswaehlen(id){
	if (nichtauswaehlen == 1) {
		nichtauswaehlen = 0;
		return;
	}
	if (ausgewaehlt == 0) {
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
				if (typeof(Auswahl) != "object") {Auswahl = document.getElementById(id);}
			}
		} catch (err) {
			var Auswahl = id;
		}
		//Falls es sich um ein neues Element handelt, dann die Markierung entfernen.
		try {
			if (Auswahl.hasAttribute('neu') == true) {
				Auswahl.removeAttribute('neu');
				if (Auswahl.hasAttribute("hintergrundfarbe_orig") == true) {
					Auswahl.style.backgroundColor = Auswahl.attributes.hintergrundfarbe_orig.value;
				} else {
					Auswahl.style.backgroundColor = "#FFFFFF";
				}
			}
		} catch (err) {}
		//Falls die STRG - Taste gedrückt wurde, dann das Element nur zur Auswahl hinzufügen.
		try {
			if (event.ctrlKey == true) {
				Auswahl.style.opacity = 0.5;
				Auswahl.className = 'context-menu-one';
				markierte_elem.push(Auswahl.id);
				nichtauswaehlen = 1;
			}
		} catch (err) {}
		if (nichtauswaehlen != 1) {
			try {if (Auswahl.attributes.feldtyp.value == "Unterformular") {Auswahl.style.border = "solid 15px";}} catch (err) {}
			Elternelement = Auswahl.parentElement;
			Auswahl.className = 'context-menu-two';
			try {
				var tempoben = parseInt(Auswahl.style.top);
				var templinks = parseInt(Auswahl.style.left);
				if (Elternelement.id != "db_Bereich") {
 					var oberstesElement = Auswahl;
	 				var tempoben = 0;
 					var templinks = 0;
 					while (oberstesElement.id != "db_Bereich") {
 						try {
 							if (oberstesElement.style.left.length > 0) {templinks = templinks + parseInt(oberstesElement.style.left);}
							if (oberstesElement.style.top.length > 0) {tempoben = tempoben + parseInt(oberstesElement.style.top);}
						} catch (err) {}
 						oberstesElement = oberstesElement.parentElement;
 					}
 				}
				Auswahl.style.removeProperty("position");
				Auswahl.style.removeProperty("left");
				Auswahl.style.removeProperty("top");
				if (Auswahl.id.substr(0,6) !="Gruppe") {
					Auswahl.setAttribute("ondrop","drop(event);");
					Auswahl.setAttribute("ondragover","allowDrop(event);");
				}
				jsPanel.create({
					id: 'ausgewaehlt',
					header: false,
					footerToolbar: '<span id="btn-close"><b>X</b></span>',
					position: "left-top " + templinks + " " + tempoben,
					content: Auswahl,
   				contentSize: Auswahl.style.width + " " + Auswahl.style.height,
   				contentOverflow: 'hidden',
			   	theme: 'info',
			   	headerControls: {
						size: 'xs'
					},
	   			dragit: {handles: '.jsPanel-ftr', grid: [parseFloat(document.Einstellungen.Raster.value)]},
	   			callback: function (panel) {
   					panel.footer.style.background = '#0398E2';
						jsPanel.pointerup.forEach(function (item) {
							panel.footer.querySelector('#btn-close').addEventListener(item, function () {
								Auswahl_beenden();
      	   		   });
			        });
   				}
				});
				if (Auswahl.id.substr(0,6) =="Gruppe") {
					document.getElementById("ausgewaehlt").setAttribute("ondrop","drop(event);");
					document.getElementById("ausgewaehlt").setAttribute("ondragover","allowDrop(event);");
					document.getElementById("ausgewaehlt").setAttribute('ondragstart','drag(event)');
				}
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

function Element_bauen(ElementTyp){
	var Sprache = document.getElementById("sprache").value;
	if (ElementTyp != "Registerkarte") {
		ElementTyp = document.getElementById('Typauswahlliste').value;
		ElementTyp = DBQ("unidb","","DE","Elementvorlagen","`" + Sprache + "` = '" + ElementTyp + "'");
	}
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		if (Auswahl.attributes.feldtyp.value == "Register") {
			var Elemente_ID = 0;
		} else {
			var Elemente_ID = Auswahl.attributes["elemente_id"].value;
		}
		var Eltern_ID = Auswahl.id;
	} else {
		var Elemente_ID = 0;
		var Eltern_ID = "db_Bereich";
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
		if (Eigenschaften2.Vorlage == "berechnet" || Eigenschaften2.Vorlage == "Feld_anzeigen" || Eigenschaften2.Vorlage == "Optionsgruppe" || Eigenschaften2.Vorlage == "Textarea" || Eigenschaften2.Vorlage == "Text" || Eigenschaften2.Vorlage == "Register" || Eigenschaften2.Vorlage == "Registerkarte") {
			var newdiv = document.createElement('div');
			if (ElementTyp != "Register") {newdiv.innerHTML = Eigenschaften2.Vorlage;}
		} else {
			if (Eigenschaften2.Vorlage == "Grafik") {
				var newdiv = document.createElement('img');
			} else {
				if (Eigenschaften2.Vorlage == "Unterformular") {
					var newdiv = document.createElement('iframe');
				} else {
					if (Eigenschaften2.Vorlage == "Listenfeld" || Eigenschaften2.Vorlage == "Kombinationsfeld") {
						var newdiv = document.createElement('select');
					} else {
						var newdiv = document.createElement('input');
						newdiv.setAttribute('value', "");
						if (Eigenschaften2.Vorlage == "Schalter") {newdiv.setAttribute('type', "button");}
						if (Eigenschaften2.Vorlage == "Textfeld") {newdiv.setAttribute('type', "Text");}
						if (Eigenschaften2.Vorlage == "Checkbox") {newdiv.setAttribute('type', "checkbox");}
					}
				}
			}
		}
		newdiv.setAttribute('id',Bez);
		newdiv.setAttribute('name',Bez);
		newdiv.setAttribute('neu','1');
		newdiv.style.backgroundColor = "#FF0000";
		if (ElementTyp == "Option") {newdiv.setAttribute('type','radio');}
		Stil = "position : absolute; ";
		for (x=0; x < Eigenschaften.length; x++) {
			Eigenschaften2 = JSON.parse(Eigenschaften[x]);
			if (Eigenschaften2.Attributtyp == "style") {
				if (ElementTyp == "Registerkarte") {
					if (Eigenschaften2.orig_Name == "top" || Eigenschaften2.orig_Name == "left" || Eigenschaften2.orig_Name == "height" || Eigenschaften2.orig_Name == "width") {
						if (Eigenschaften2.orig_Name == "top") {
							Stil += Eigenschaften2.orig_Name + ":21px; ";
							Eigenschaften2.Wert = "21px";
						} else {
							if (Eigenschaften2.orig_Name == "left") {
								Stil += Eigenschaften2.orig_Name + ":0px; ";
								Eigenschaften2.Wert = "0px";
							} else {
								if (Eigenschaften2.orig_Name == "height") {
									Stil += Eigenschaften2.orig_Name + ":" + (parseInt(document.getElementById(Eltern_ID).style.height) - 21).toString() + "px; ";
									Eigenschaften2.Wert = (parseInt(document.getElementById(Eltern_ID).style.height) - 21).toString() + "px";
								} else {
									if (Eigenschaften2.orig_Name == "width") {
										Stil += Eigenschaften2.orig_Name + ":" + (parseInt(document.getElementById(Eltern_ID).style.width) - 5).toString() + "px; ";
										Eigenschaften2.Wert = (parseInt(document.getElementById(Eltern_ID).style.width) - 5).toString() + "px";
									}
								}
							}
						}
					} else {Stil += Eigenschaften2.orig_Name + ":" + Eigenschaften2.Standardwert + "; ";}
				} else {Stil += Eigenschaften2.orig_Name + ":" + Eigenschaften2.Standardwert + "; ";}
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
		if (ElementTyp == "Textarea") {newdiv.setAttribute('onclick1','Text_bearbeiten("' + Bez + '");');}
		if (ElementTyp == "Registerkarte") {
			Registerkarte = {};
			Registerkarte["id"] = Bez;
			Registerkarte["Beschriftung"] = "neue Karte";
			Registerkarten[Registerkarten.length] = Registerkarte;
			newdiv.className = 'Register Registerkarte';
			newdiv.Beschriftung = "neue Karte";
			if (document.getElementById("kopftabelle_" + Eltern_ID) == null) {
				var newtab = document.createElement('table');
				newtab.id = "kopftabelle_" + Eltern_ID;
				newtab.style.height = "21px";
				newtab.style.position = "absolute";
				newtab.innerHTML = "<tr id='kopftabelle_" + Bez + "' class='Text_einfach' height='21px'><td class='Registerkarte_Kopf Registerkarte_Kopf_abgew' id='Zelle_" + Bez + "' onclick='Registerkarte_wechseln(\"" + Bez + "\",1,\"" + Bez + "\");'>neue Karte</td></tr>";
				document.getElementById(Eltern_ID).appendChild(newtab);
			}
		}
		//neues Element markieren
		newdiv.style.zIndex = 1000;
		newdiv.setAttribute('neu','1');
		newdiv.style.backgroundColor = "#FF0000";
		document.getElementById(Eltern_ID).appendChild(newdiv);
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
						Auswahl.setAttribute(Eigenschaft.orig_Name, Formular[i].value);
					} else {
						delete Auswahl.removeAttribute(Eigenschaft.orig_Name);
					}
				} else {
					Auswahl.setAttribute("onclick1", Formular[i].value);
				}
			}
		} catch (err) {}
	}
	if (Formular.Feldtyp.value == "Text") {Auswahl.innerHTML = Formular.Textinhalt.value;}
	Auswahl.setAttribute("style", Stil);
	if (Auswahl.style.display == "none") {
		Auswahl.setAttribute("display1","none");
		Auswahl.style.display = "block";
	}
	if (Formular.Feldtyp.value == "Textarea") {Auswahl.style.overflow = "scroll";}
	Auswahl.parentElement.style.height = Auswahl.style.height;
	Auswahl.parentElement.style.width = Auswahl.style.width;
	kann_Eltern = DBQ('unidb', '','kann_Eltern','Elementvorlagen','Elementvorlage_ID = ' + tempa.Elementvorlage_ID);
	Auswahl.setAttribute("kann_eltern",kann_Eltern);
	try {Elementeinstellungen.close();} catch (err) {}
}

function Element_Dialog_oeffnen() {
	try {
		Elementeinstellungen.close();
		mehrere_Elementeinstellungen.close();
		return;
	} catch (err) {}
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
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[69] + "' type='button' onclick='Tab_umschalten(1);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[70] + "' type='button' onclick='Tab_umschalten(2);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[71] + "' type='button' onclick='Tab_umschalten(3);'><br><br>\n";
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
						Position = Auswahl.innerHTML;
						if (Position == null) {Position = Eigenschaften2.Standardwert;}
						Inhalt_temp += "<Textarea class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "'>" + Position + "</Textarea>";
					}
					if (Eigenschaften2.Darstellung_Dialog == "Schalter") {
						Zusatz = "";
						if (Eigenschaften2.Vorlage == 'Register') {Zusatz = " onclick='Dialog_Registerkarten_oeffnen(" + id + ");'";}
						Inhalt_temp += "<input class='Schalter_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='bearbeiten' type='button'" + Zusatz + ">";
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
							if (Position == "context-menu-two context-menu-active") {Position = "";}
						}
						if (Eigenschaften2.orig_Name.indexOf("color") > -1) {
							try {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							} catch (err) {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							}
						} else {
							if (Eigenschaften2.Eigenschaft == "Elementname") {Position = id;} 
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
							try {
								for (i = 0; i < SelectListe.length; i++) {
									if(Eigenschaften.hasOwnProperty("feld") == false) {Eigenschaften.feld = "";}
									if(Eigenschaften.feld.value == SelectListe[i]) {
										Inhalt_temp += "<option selected>";
									} else {
										Inhalt_temp += "<option>";
									}
									Inhalt_temp += SelectListe[i] + "</option>\n";
								}
								Inhalt_temp += "</select>";
							} catch (err) {}
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
		Inhalt += "&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' type='button' name='cssdialog' value='" + T_Text[65] + "' onclick='CSS_Dialog_oeffnen();'>\n";
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
			headerTitle: T_Text[72],
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
	if (Auswahl.hasAttribute("display1") == true) {Element_Dialog.elements.sichtbar.value = Auswahl.attributes["display1"].value;}
}

function Element_kopieren(Auswahl) {
	var Auswahl = Element_aus_Fenster();
	var cln = Auswahl.cloneNode(true);
	db_Bereich.appendChild(cln);
	var jetzt = new Date();
	var Zeitpunkt = jetzt.getTime();
	var id =  Auswahl.attributes.feldtyp.value + Zeitpunkt.toString();
	cln.setAttribute('id',id);
	cln.setAttribute('name',id);
	cln.style.top = "60px";
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
					temp.childNodes[i].id = temp.childNodes[i].id + Zeitpunkt.toString();
				} else {
					temp.childNodes[i].id = "";
				}
				try {
					if (Kopie["orig_id"] > "" && Kopie["orig_id"] != undefined) {
						if (temp.childNodes[i].name != undefined) {temp.childNodes[i].setAttribute('name',temp.childNodes[i].name + Zeitpunkt.toString());}
						if (temp.childNodes[i].elemente_id != undefined) {temp.childNodes[i].setAttribute('elemente_id',temp.childNodes[i].elemente_id + Zeitpunkt.toString());}
					}
				} catch (err) {}
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

function Formular_Dialog_oeffnen() {
	var Inhalt = '<div style="background: #FCEDD9; width: 100%; height: 1200px;"><div style="position: absolute; top: 10px; left: 10px"><form name="Formular_Dialog"><table>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[30] + '</td><td colspan="2"><input class="Text_Element" name="Formularname" size="30" value="' + document.Einstellungen.Bezeichnung.value + '" type="text"></td></tr>\n';
	//Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[61] + '</td><td colspan="2"><input class="Text_Element" name="Hintergrundfarbe" size="30" value="' + document.Einstellungen.Hintergrundfarbe.value + '" type="color"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[61] + '</td><td colspan="2"><input class="Text_Element" name="Hintergrundfarbe" id="Hintergrundfarbe" style="width: 40px;" value="' + document.Einstellungen.Hintergrundfarbe.value + '" type="text">&nbsp;&nbsp;&nbsp;&nbsp;<input class="Text_Element" id="Hintergrundfarbe_h" name="Hintergrundfarbe_h" value="' + document.Einstellungen.Hintergrundfarbe.value + '" type="color" onchange="document.getElementById(\'Hintergrundfarbe\').value = document.getElementById(\'Hintergrundfarbe_h\').value;"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[31] + '</td><td><select class="Auswahl_Liste_Element" name="Darstellung" value="' + document.Einstellungen.Darstellung.value + '">\n';
	if (document.Einstellungen.Darstellung.value == T_Text[33]) {
		Inhalt = Inhalt + '<option>' + T_Text[32] + '</option>';
		Inhalt = Inhalt + '<option selected>' + T_Text[33] + '</option>';
	} else {
		Inhalt = Inhalt + '<option selected>' + T_Text[32] + '</option>';
		Inhalt = Inhalt + '<option>' + T_Text[33] + '</option>';
	}
	Inhalt = Inhalt + '</select>\n';
	Inhalt = Inhalt + '&nbsp;&nbsp;&nbsp;&nbsp;' + T_Text[73] + ':&nbsp;<input class="Text_Element" id="dialog_tabellenzeilen" name="Dialog_Tabellenzeilen" value="' + document.Einstellungen.Tabellenzeilen.value + '" type="text" size="3">\n';
	var angekreuzt = "";
	if (document.Einstellungen.Navigationsbereich.value == 1) {angekreuzt = " checked";}
	Inhalt = Inhalt + '&nbsp;&nbsp;&nbsp;&nbsp;' + T_Text[74] + ':&nbsp;<input class="Text_Element" id="dialog_Navigationsbereich" name="Dialog_Navigationsbereich" type="checkbox"' + angekreuzt + '></td></tr>\n';
	Inhalt = Inhalt + '<tr height="30px"><td class="Text_einfach" style="text-align: right">' + T_Text[34] + '</td><td colspan="2"><input class="Text_Element" name="Datenbank" size="30" value="' + document.Einstellungen.Datenbank.value + '" type="text"></td></tr>\n';
	angekreuzt = "";
	if (document.Einstellungen.Replikation.value == 1) {angekreuzt = " checked";}
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[84] + ':</td><td><input class="Text_Element" id="dialog_Replikation" name="Dialog_Replikation" type="checkbox"' + angekreuzt + '></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[35] + '</td><td colspan="3"><textarea class="Text_Element" name="Datenquelle" style="height: 60px; width: 560px;">' + document.Einstellungen.Datenquelle.value + '</textarea></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[75] + '</td><td colspan="3"><textarea class="Text_Element" name="Headererweiterung_Dialog" style="height: 60px; width: 560px;">' + document.Einstellungen.headererweiterung.value + '</textarea></td></tr>\n';
	var js = document.Einstellungen.Bei_Start.value.replace(/§§§/g,"'");
	js = js.replace(/@@@/g,'"');
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">Js - onload</td><td colspan="3"><textarea class="Text_Element" name="Bei_Start_Dialog" style="height: 60px; width: 560px;">' + js + '</textarea></td></tr>\n';
	js = document.Einstellungen.current.value.replace(/§§§/g,"'");
	js = js.replace(/@@@/g,'"');
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">current</td><td colspan="3"><textarea class="Text_Element" name="current_Dialog" style="height: 60px; width: 560px;">' + js + '</textarea></td></tr>\n';
	Inhalt = Inhalt + '<tr style="height: 40px;"><td class="Text_einfach" style="text-align: right">' + T_Text[36] + '</td><td><input class="Schalter_Element" name="übernehmen" value="' + T_Text[37] + '" type="button" onclick="Formulareinstellungen_uebernehmen()"></td><td></td></tr>\n';
	Inhalt = Inhalt + '</table></form></div></div>\n';
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Formulareinstellungen',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '680 500',
		headerTitle: T_Text[38],
		position: 'left-top 30 30',
		contentOverflow: 'hidden',
		content: Inhalt,
	});
}

function Formulareinstellungen_uebernehmen() {
	document.Einstellungen.Bezeichnung.value = document.Formular_Dialog.Formularname.value;
	document.Einstellungen.Hintergrundfarbe.value = document.Formular_Dialog.Hintergrundfarbe.value;
	document.Einstellungen.Datenbank.value = document.Formular_Dialog.Datenbank.value;
	document.Einstellungen.Datenquelle.value = document.Formular_Dialog.Datenquelle.value;
	document.Einstellungen.Darstellung.value = document.Formular_Dialog.Darstellung.value;
	document.Einstellungen.Tabellenzeilen.value = document.Formular_Dialog.Dialog_Tabellenzeilen.value;
	if (document.Formular_Dialog.Dialog_Navigationsbereich.checked == true) {
		document.Einstellungen.Navigationsbereich.value = 1;
	} else {
		document.Einstellungen.Navigationsbereich.value = 0;
	}
	if (document.Formular_Dialog.Dialog_Replikation.checked == true) {
		document.Einstellungen.Replikation.value = 1;
	} else {
		document.Einstellungen.Replikation.value = 0;
	}
	temp_Text = document.Formular_Dialog.Headererweiterung_Dialog.value.replace(/'/g,"\"");
	document.Einstellungen.Headererweiterung.value = temp_Text;
	temp_Text = document.Formular_Dialog.Bei_Start_Dialog.value.replace(/'/g,"§§§");
	temp_Text = temp_Text.replace(/"/g,"@@@");
	document.Einstellungen.Bei_Start.value = temp_Text;
	temp_Text = document.Formular_Dialog.current_Dialog.value.replace(/'/g,"§§§");
	temp_Text = temp_Text.replace(/"/g,"@@@");
	document.Einstellungen.current.value = temp_Text;
	try {Formulareinstellungen.close();} catch (err) {}
}

function Element_entfernen() {
	var Auswahl = Element_aus_Fenster();
	var Fenster = Auswahl.parentElement.parentElement;
	Auswahl.parentElement.removeChild(Auswahl);
	Fenster.close();
	ausgewaehlt = 0;
}

function Auswahl_beenden() {
	if (Elternelement == null) {Elternelement = document.getElementById("db_Bereich");}
 	try{
 		Auswahl = Element_aus_Fenster();
 		try {Auswahl.style.opacity = "";} catch (err) {}
		try {if (Auswahl.attributes.feldtyp.value == "Unterformular") {Auswahl.style.border = "";}} catch (err) {}
 		for (i=0; i < Auswahl.childNodes.length; i++) {
			try {Auswahl.childNodes[i].style.opacity = "";} catch (err) {}
		}
 		var Fenster = Auswahl.parentElement.parentElement;
 		try {Auswahl.removeAttribute("class");} catch (err) {}
 		try {Auswahl.setAttribute("class", Auswahl.attributes.class1.value);} catch (err) {}
 		Auswahl.style.position = "absolute";
 		if (Elternelement.id == "db_Bereich") {
 			var tempoben = 0;
			var templinks = 0;
 		} else {
			var oberstesElement = Elternelement;
			var tempoben = 0;
			var templinks = 0;
			while (oberstesElement.id != "db_Bereich") {
				try {
					if (oberstesElement.style.left.length > 0) {templinks = templinks + parseInt(oberstesElement.style.left);}
					if (oberstesElement.style.top.length > 0) {tempoben = tempoben + parseInt(oberstesElement.style.top);}
				} catch (err) {}
 				oberstesElement = oberstesElement.parentElement;
 			}
 		}
 		//tempoben = tempoben + parseInt(oberstesElement.style.top);
		//templinks = templinks + parseInt(oberstesElement.style.left);
 		if ((Auswahl.parentElement.parentElement.offsetTop - tempoben) < 0) {
 			Auswahl.style.top = "0px";
 		} else {
			Auswahl.style.top = (Auswahl.parentElement.parentElement.offsetTop - tempoben).toString() + "px";
		}
		if ((Auswahl.parentElement.parentElement.offsetLeft - templinks) < 0) {
 			Auswahl.style.left = "0px";
 		} else {
			Auswahl.style.left = (Auswahl.parentElement.parentElement.offsetLeft - templinks).toString() + "px";
		}
		try {
			if (Auswahl.attributes["feldtyp"].value == "Register") {
				for (x=0; x < Auswahl.childNodes.length; x++) {
					if (Auswahl.childNodes[x].id.substr(0,12) != "kopftabelle_") {
						try {Auswahl.childNodes[x].style.height = (parseInt(Auswahl.style.height) - 21).toString() + "px";} catch (err) {}
					}
				}
			}
		} catch (err) {}
		Elternelement.appendChild(Auswahl);
	} catch (err) {}
	try {Fenster.close();} catch (err) {}
	try {Elementeinstellungen.close();} catch (err) {}
	ausgewaehlt = 0;
	Elternelement = null;
	try {
		navigationsdialog.close();
		Navi_Dialog();
	} catch (err) {}
}

function Groesse_anpassen() {
	try{
		var Auswahl = Element_aus_Fenster();
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
  	 		Auswahl = panel.content.childNodes[0];
		}
		});
	} catch (err) {}	
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

function Formular_zeigen(Datei) {
  	jsPanel.create({
		dragit: {
			snap: true
      },
		id: 'Vorschau',
		theme: 'info',
		contentSize: '200 200',
		headerTitle: T_Text[40],
		headerControls: {
			size: 'xs'
		},
		position: 'left-top 100 30',
		content:  "<img id='bild_angezeigt' src='" + Datei + "'>",
	});
	//nur wegen dem async, denn sonnst ist das Formular nicht komplett da, wenn die nächsten Zeilen bearbeitet werden
	jQuery.ajax({
		url: Datei,
		success: function (html) {
   		Formular = html;
		},
 		async: false
 	});
	//Ende des JS Wahnsinns
	if (document.getElementById("bild_angezeigt").width > 150) {
		document.getElementById("Vorschau").style.width = (document.getElementById("bild_angezeigt").width).toString() + "px";
	} else {
		document.getElementById("Vorschau").style.width = "150px";
	}
	if (document.getElementById("bild_angezeigt").height > 140) {
		document.getElementById("Vorschau").style.height = (document.getElementById("bild_angezeigt").height + 50).toString() + "px";
	} else {
		document.getElementById("Vorschau").style.height = "150px";
	}
}

function Code_Dialog(){
	var js = document.getElementById("js_code").value.replace(/§§§/g,"'");
	js = js.replace(/@@@/g,'"');
	var Inhalt = "<textarea id='code_text' style='width: 100%;'>" + js + "</textarea><input class='Schalter_Element' name='Code_übernehmen' value='" + T_Text[37] + "' type='button' onclick='Code_uebernehmen()'>";
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
	document.getElementById("js_code").value = document.getElementById("js_code").value.replace(/'/g,"§§§");
	document.getElementById("js_code").value = document.getElementById("js_code").value.replace(/"/g,'@@@');
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
		headerTitle: T_Text[67],
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
		headerTitle: T_Text[68],
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
	ev.preventDefault();
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
	try {
		navigationsdialog.close();
		Navi_Dialog();
	} catch (err) {}
}

function Dialog_Registerkarten_oeffnen(Elemente_ID) {
	if (typeof Elemente_ID === 'object') {Elemente_ID = Elemente_ID.id;}
	Registerkarten_einlesen(Elemente_ID);
	var Inhalt = Registerkarten_auflisten(Elemente_ID);
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Registerkarten_bearbeiten',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '800 170',
		headerTitle: T_Text[76],
		position: 'left-top 30 30',
		content: Inhalt,
	});
}

function Karte_bearbeiten(Aufgabe,id,Karte) {
	if (Aufgabe == "Beschriftung") {
		if (Registerkarten[Karte]["id"].substr(0,6) == "Zelle_") {Registerkarten[Karte]["id"].substr(6);}
		Registerkarten[Karte]["Beschriftung"] = document.getElementById("Bezeichnung_" + Karte).value;
	}
	if (Aufgabe == "rechts") {
		if (Karte + 1 < Registerkarten.length) {
			var Registerkarte = Registerkarten[Karte];
			Registerkarten.splice(Karte,1);
			Registerkarten.splice(Karte + 1,0,Registerkarte);
		}
	}
	if (Aufgabe == "links") {
		if (Karte > 0) {
			var Registerkarte = Registerkarten[Karte];
			Registerkarten.splice(Karte,1);
			Registerkarten.splice(Karte - 1,0,Registerkarte);
		}
	}
	if (Aufgabe == "entfernen") {
		try {
			if (Registerkarten[Karte].id.substr(0,6) == "Zelle_") {Registerkarten[Karte].id = Registerkarten[Karte].id.substr(6);}
			document.getElementById("Element_" + Registerkarten[Karte].id).value = "loeschen";
		} catch (err) {}
		document.getElementById(id).removeChild(document.getElementById(Registerkarten[Karte].id));
		Registerkarten.splice(Karte,1);
		document.getElementById("kopftabelle_" + id).removeChild(document.getElementById("kopftabelle_" + id).cells[Karte])
		Registerkarten_einlesen(id);
	}
	if (Aufgabe == "neu") {
		Element_bauen("Registerkarte");
		//Registerkarten_sortieren(id);
	}
	document.getElementById("Registerkarten_bearbeiten").content.innerHTML = Registerkarten_auflisten(id);
	Registerkarten_sortieren(id);
}

function Registerkarten_auflisten(Elemente_ID) {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table><tr>";
	for (x = 0; x < Registerkarten.length; x++) {
		Inhalt += "<td><table><tr class='Tabellenzeile'><td colspan='3'><input id='Bezeichnung_" + x.toString() + "' class='Text_Element' type ='Text' value='" + Registerkarten[x]["Beschriftung"] + "' onfocusout=\"Karte_bearbeiten('Beschriftung','" + Elemente_ID + "'," + x.toString() + ");\"></td></tr>";
		Inhalt += "<tr class='Tabellenzeile'><td><input class='Schalter_fett_Element' type ='button' value='<' onclick=\"Karte_bearbeiten('links','" + Elemente_ID + "'," + x.toString() + ");\"></td>";
		Inhalt += "<td align='center'><input class='Schalter_fett_Element' type ='button' value='X' onclick=\"Karte_bearbeiten('entfernen','" + Elemente_ID + "'," + x.toString() + ");\"></td>";
		Inhalt += "<td align='right'><input class='Schalter_fett_Element' type ='button' value='>' onclick=\"Karte_bearbeiten('rechts','" + Elemente_ID + "'," + x.toString() + ");\"></tr></table></td><td width='25px'></td>";
	}
	Inhalt += "</tr><tr valign='bottom' height='50px'><td colspan='3'><input id='neue_Karte' class='Schalter_Element' type ='button' value='" + T_Text[77] + "' onclick=\"Karte_bearbeiten('neu','" + Elemente_ID + "'," + x.toString() + ");\"></td></tr></table></div>";
	return(Inhalt);
}

function Registerkarten_einlesen(Elemente_ID) {
	Registerkarten = [];
	if (document.getElementById("kopftabelle_" + Elemente_ID) != null) {
		Karten = "";
		for (x = 0; x < document.getElementById("kopftabelle_" + Elemente_ID).firstChild.firstChild.cells.length; x++) {
			Registerkarte = {};
			Registerkarte["id"] = document.getElementById("kopftabelle_" + Elemente_ID).firstChild.firstChild.cells[x].id;
			if (Registerkarte["id"].substr(0,6) == "Zelle_") {Registerkarte["id"] = Registerkarte["id"].substr(6);}
			Karten += "," + Registerkarte["id"];
			Registerkarte["Beschriftung"] = document.getElementById("kopftabelle_" + Elemente_ID).firstChild.firstChild.cells[x].innerText;
			Registerkarten[x] = Registerkarte;
		}
	}
}

function Registerkarten_sortieren(id) {
	if (document.getElementById("kopftabelle_" + id) != null) {
		document.getElementById("kopftabelle_" + id).innerHTML = "<table style='zIndex: 3;'><tr></tr></table>";
		var Karten = "";
		for (x = 0; x < Registerkarten.length; x++) {
			if (Registerkarten[x].id.substr(0,6) == "Zelle_") {Registerkarten[x].id = Registerkarten[x].id.substr(6);}
			gew = "abgew";
			if (x == 0) {gew = "ausgew";}
			document.getElementById("kopftabelle_" + id).firstChild.firstChild.innerHTML += "<td class='Registerkarte_Kopf Registerkarte_Kopf_" + gew + "' id='Zelle_" + Registerkarten[x]["id"] + "' onclick='Registerkarte_wechseln(\"" + Registerkarten[x]["id"] + "\"," + x.toString() + ",\"" + id + "\");'>" + Registerkarten[x]["Beschriftung"] + "</td>";
			Karten += "," + Registerkarten[x]["id"];
		}
		Karten = Karten.substr(1);
		var Elternelement = document.getElementById(id).parentElement;
		try {
			Elternelement.attributes.karten.value = Karten;
		} catch (err) {
			if (Elternelement.hasAttribute("feldtyp") == true) {Elternelement.setAttribute("karten", Karten)}
		}
	}
}

function Registerkarte_wechseln(id, Stelle, Eltern_ID) {
	if (typeof Eltern_ID === 'object') {Eltern_ID = Eltern_ID.id;}
	if (typeof id === 'object') {id = id.id;}	
	var Tabellenzeile = document.getElementById("kopftabelle_" + Eltern_ID).firstChild.firstChild;
	for (x = 0; x < document.getElementById(Eltern_ID).childNodes.length; x++) {
		if (document.getElementById(Eltern_ID).childNodes[x].id.substr(0,12) != "kopftabelle_") {
			document.getElementById(Eltern_ID).childNodes[x].style.display = "none";
			document.getElementById(Eltern_ID).childNodes[x].style.top = "21px";
			document.getElementById(Eltern_ID).childNodes[x].style.left = "0px";
			document.getElementById(Eltern_ID).childNodes[x].style.height = (parseInt(document.getElementById(Eltern_ID).style.height) - 21).toString() + "px";
			document.getElementById(Eltern_ID).childNodes[x].style.width = (parseInt(document.getElementById(Eltern_ID).style.width) - 5).toString() + "px";
		} else {
			var Tabpos = x;
		}
	}
	if (document.getElementById(Eltern_ID).childNodes.length - 1 != Tabpos) {
		tempdiv = document.createElement('div');
		tempdiv.style.display = "none";
		tempdiv.appendChild(document.getElementById(Eltern_ID).childNodes[Tabpos]);
		document.getElementById(Eltern_ID).appendChild(tempdiv.firstChild);
		tempdiv.delete;
	}
	for (x = 0; x < document.getElementById("kopftabelle_" + Eltern_ID).firstChild.firstChild.cells.length; x++) {
		if (x != Stelle) {
			document.getElementById("kopftabelle_" + Eltern_ID).firstChild.firstChild.cells[x].className = "Registerkarte_Kopf Registerkarte_Kopf_abgew";
		} else {
			document.getElementById("kopftabelle_" + Eltern_ID).firstChild.firstChild.cells[x].className = "Registerkarte_Kopf Registerkarte_Kopf_ausgew";
			document.getElementById(id).style.display="block"
		}
	}
	try {
		document.getElementById(id).className = "Register Registerkarte";
		document.getElementById(id).style.zIndex = parseInt(document.getElementById(Eltern_ID).parentElement.style.zIndex) + 1;
	} catch (err) {}
	nichtauswaehlen = 1;
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

function bed_Format_Dialog() {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table cellspan='3px' cellpadding='2px' id='bed_Tab'>";
	Inhalt += '<tr class="Tabellenzeile"><td class="Tabelle_Ueberschrift">' + T_Text[47] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[42] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[43] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[45] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[46] + '</td><td></td><td align="right"><input type="button" name="Hilfe" class="Schalter_Element" value="' + T_Text[12] + '" onclick="Hilfe_Fenster(\'41\');"></td></tr>';
	i = 1;
	while (Bedingungen[i] != undefined) {
		Inhalt += '<tr class="Tabellenzeile"><td>' + i.toString() + '</td><td>' + Bedingungen[i].Bedingung + '</td><td>' + Bedingungen[i].Element + '</td><td>' + Bedingungen[i].Kommentar + '</td><td><div style="' + Bedingungen[i].Stil + '" class="' + Bedingungen[i].class + '">' + T_Text[53] + '</div></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[54] + '" onclick="bed_Dialog(\'' + i.toString() + '\');"></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[26] + '" onclick="bed_entfernen(\'' + i.toString() + '\');"></td></tr>';
		i = i + 1;
	}
	Inhalt += '</table><table><tr style="height: 50px;"><td colspan="2"><input class="Schalter_Element" type="button" name="uebernehmen" value="' + T_Text[37] + '" onclick="Bed_Format_uebernehmen();"></td>';
	Inhalt += '<td colspan="2"><input class="Schalter_Element" type="button" name="neu" value="' + T_Text[44] + '" onclick="bed_Dialog(\'neu\');"></td></tr>';
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
		headerTitle: T_Text[56],
		position: 'left-top 10 60',
		content: Inhalt
	});
}

function bed_Dialog(BedNr) {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table>";
	Inhalt += '<tr><td align="right">' + T_Text[42] + '</td><td colspan="2"><textarea style="width: 210px; height: 16px;" class="Text_Element" id="bedingung_feld" name="Bedingung_Feld"></textarea></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[43] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="element_feld" name="Element_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[45] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="kommentar_feld" name="Kommentar_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td colspan="3"><hr></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[78] + '</td><td><select class="Auswahl_Liste_Element" id="sichtbar_feld" name="sichtbar_Feld" value=""><option value="" selected></option><option value="none">none</option><option value="block">block</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[57] + '</td><td><select class="Auswahl_Liste_Element" id="border-style_feld" name="border-style_Feld" value="undefined"><option value="solid">durchgezogen</option><option value="dotted">gepunktet</option><option value="dashed">gestrichelt</option><option value="" selected=""></option><option value="double">doppelt</option><option value="groove">Rille</option><option value="ridge">Grat</option><option value="inset">innen gesetzt</option><option value="outset">außen gesetzt</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[58] + '</td><td><input class="Text_Element" id="rahmenfarbe_feld" name="Rahmenfarbe_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[59] + '</td><td><input style="width: 40px;" class="Text_Element" id="rahmenbreite_feld" name="Rahmenbreite_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[79] + '</td><td colspan="2"><select class="Auswahl_Liste_Element" id="font-family_feld" name="font-family_Feld" value=""><option value="arial, helvetica, sans-serif">arial, helvetica, sans-serif</option><option value="roman, times new roman, times, serif">roman, times new roman, times, serif</option><option value="courier, fixed, monospace">courier, fixed, monospace</option><option value="western, fantasy">western, fantasy</option><option value="Zapf-Chancery, cursive">Zapf-Chancery, cursive</option><option value="serif">serif</option><option value="sans-serif">sans-serif</option><option value="cursive">cursive</option><option value="fantasy">fantasy</option><option value="monospace">monospace</option><option value=""></option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[60] + '</td><td><select class="Auswahl_Liste_Element" id="font-style_feld" name="font-style_Feld" value=""><option value="" selected></option><option value="normal">normal</option><option value="italic">kursiv</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[80] + '</td><td><input class="Text_Element" id="color_feld" name="color_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[61] + '</td><td><input class="Text_Element" id="background-color_feld" name="background-color_Feld" value="#FFFFFF" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[62] + '</td><td><select class="Auswahl_Liste_Element" id="font-weight_feld" name="font-weight_Feld" value=""><option value=""></option><option value="normal">normal</option><option value="bold">fett</option><option value="bolder">fetter</option><option value="lighter">dünner</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[81] + '</td><td><input style="width: 40px;" class="Text_Element" id="font-size_feld" name="font-size_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[63] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" title="' + T_Text[64] + '" id="class_feld" name="class_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 50px;"><td align="right"><input class="Schalter_Element" type="button" name="uebernehmen_Feld" value="' + T_Text[37] + '" onclick="Bedingung_uebernehmen(\'' + BedNr.toString() + '\');"></td>';
	Inhalt += '<td align="left"><input class="Schalter_Element" type="button" name="cssdialog_Feld" value="' + T_Text[65] + '" onclick="CSS_Dialog_oeffnen();"></td></tr>';
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
		headerTitle: T_Text[66],
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
	Zellen = Zellen + '<td><div ' + Klasse + 'style="' + Stil + '">' + T_Text[53] + '</div></td>';
	Zellen = Zellen + '<td><input class="Schalter_Element" type="button" value="' + T_Text[54] + '" onclick="bed_Dialog(\'' + BedNr.toString() + '\');"></td>';
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

function DBQ(DB, Funktion,Feld,Tabelle,Bedingung) {
	strReturn = "Error";
	jQuery.ajax({
		url: "DBQ.php",
		success: function (html) {
   		strReturn = html;
		},
		type: 'POST',
		data: {DB: DB, Fun: Funktion,Fel: Feld, Tab: Tabelle, Bed: Bedingung},
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
	d.add('db_Bereich',-1,T_Text[87],"");
	for (i = 1; i < Baum.length; i++) {
		d.add(Baum[i].ID,Baum[i].eltern_id,Baum[i].ID,"javascript: Elem_aus_Liste_markieren(" + Baum[i].ID + ");");
	}
	Inhalt = Inhalt + "</div>\n";
	Inhalt = Inhalt + '<div style="position: absolute; top: 5px; left: 10px;"><input class="Schalter_Element" type="button" value="' + T_Text[86] + '" onclick="Element_Dialog_oeffnen();"></div>\n';
	try {
		Fensterpos = (frameElement.clientWidth - 220).toString() + ' 44';
	} catch (err) {
		Fensterpos = "10 100";
	}
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
		headerTitle: T_Text[85],
		position: 'left-top ' + Fensterpos,
		content: Inhalt
	});
	document.getElementById("Tbaum").innerHTML= d.toString();
}

function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("formular").style.display == "block") {
			document.getElementById("formular").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("formular").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("formular").style.display = "none";
		document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 2) {
		if (document.getElementById("elemente").style.display == "block") {
			document.getElementById("elemente").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("elemente").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("elemente").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
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
