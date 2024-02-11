var T_Text = new Array;
var db_Bereich_top = 0;
var Rahmenbreite = 0;
var Rahmenhoehe = 0;
var vergr_Faktor = 1;
var Seiteneigenschaften = "";
var Seiten = [];
var akt_Seite = "";
var anz_Seiten = "";
var Deckblatt = 0;
var Bedingungen = {};
var Ber_id = "";
var Ebene = 1;
var E = [];
var EaS = [];
var EB = []; 
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

function Start() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	//bedingte Formatierung einrichten
	try {Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
		i = 1;
		while(Bedingungen[i] != undefined) {
			Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
			i = i + 1;
		}	
	} catch (err) {}
	db_Bereich_top = parseInt(document.getElementById("db_Bereich").style.top);
	vergr_Faktor = document.getElementById("faktor").value;
	document.getElementById("vergr").value = vergr_Faktor / 0.03635 / Math.round(vergr_Faktor / 0.03635) * vergr_Faktor / 0.03635;
	Format_einstellen();
	Daten_lesen();
	document.getElementById("vergr").value = parseInt(document.getElementById("vergr").value);
	Format_einstellen(1);
	Seiten_Kopf_Fuss_einstellen();
}

function Format_einstellen(manuell) {
	if (Seiteneigenschaften == undefined || Seiteneigenschaften == "") {
		Seiteneigenschaften = document.getElementById("seiteneinstellungen").value.split(",");
		for (z = 0; z < Seiteneigenschaften.length; z++) {
			x = Seiteneigenschaften[z].split(":");
			Seiteneigenschaften[x[0]] = x[1];
		}
	}
	vergr_Faktor_alt = vergr_Faktor;
	Rahmenbreite = document.documentElement.clientWidth;
	Rahmenhoehe = document.documentElement.clientHeight - db_Bereich_top - 10;
	Groesse = Seiteneigenschaften["Groesse"].substr(-1);
	Hoehe1 = parseFloat(DIN_A[Groesse]["Hoehe"]);
	Breite1 = parseFloat(DIN_A[Groesse]["Breite"]);
	if (Seiteneigenschaften["Format"] == T_Text[13]) {
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
	//Größe der Elemente anpassen
	Typ = [];
	Typ[0] = "div";
	Typ[1] = "img";
	for (i = 0; i < Typ.length; i++) {
		Elemente = document.getElementsByTagName(Typ[i]);
		for (z = 0; z < Elemente.length; z++) {
			if (Elemente[z].id !="db_Bereich") {
				Elemente[z].style.top = (parseFloat(Elemente[z].style.top) * vergr).toString() + "px";
				Elemente[z].style.left = (parseFloat(Elemente[z].style.left) * vergr).toString() + "px";
				Elemente[z].style.width = (parseFloat(Elemente[z].style.width) * vergr).toString() + "px";
				Elemente[z].style.height = (parseFloat(Elemente[z].style.height) * vergr).toString() + "px";
				if (Elemente[z].style.fontSize == "") {Elemente[z].style.fontSize = (9 / vergr).toString() + "px";}
				Elemente[z].style.fontSize = (parseFloat(Elemente[z].style.fontSize) * vergr).toString() + "px";
			}
		}
	}
}

function neue_Seite(Snr) {
	Seiten[Snr] = document.getElementById('arbeitsbereich').cloneNode();
	//Beidseitiger Druck berücksichtigen
	if (Seiteneigenschaften["Format"] == T_Text[12] && Seiteneigenschaften["beidseitig"] == 1) {
		Deckbl = 0;
		if (document.getElementById('deckblattbereich').childNodes.length > 0) {Deckbl = 1;}
		if ((Snr - Deckbl) / 2 != parseInt(Snr / 2)) {
			Seiten[Snr].style.left = (Seiteneigenschaften["rechter_Rand"] * vergr_Faktor).toString() + "px";
		}
	}
	Seiten[Snr].id = "Seite_" + (Snr + 1).toString();
	document.getElementById("db_Bereich").appendChild(Seiten[Snr]);
	newdiv = document.getElementById("Seitenkopf").cloneNode();
	newdiv.id = "Seitenkopf" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Seitenkopf").innerHTML;
	}
	newdiv = document.getElementById("Seitenfuss").cloneNode();
	newdiv.id = "Seitenfuss" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Seitenfuss").innerHTML;
	}
	//berechnete Felder berechnen
	for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
		try {
			if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
				document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung(document.getElementById(newdiv.id));
	return parseFloat(document.getElementById("Seitenfuss").style.top) - parseFloat(document.getElementById("Seitenkopf").style.height);
}

function Daten_lesen() {
	//Erstmal die Ränder und Beschriftungen der Bereiche entfernen.
	document.getElementById("deckblattbereich").style.border="";
	document.getElementById("arbeitsbereich").style.border="";
	if (document.getElementById("Seitenkopf").style.border == "1px dotted") {document.getElementById("Seitenkopf").style.border = "";}
	if (document.getElementById("Berichtskopf").style.border == "1px dotted") {document.getElementById("Berichtskopf").style.border="";}
	if (document.getElementById("Detailkopf").style.border == "1px dotted") {document.getElementById("Detailkopf").style.border="";}
	if (document.getElementById("Detailfuss").style.border == "1px dotted") {document.getElementById("Detailfuss").style.border="";}
	if (document.getElementById("Berichtsfuss").style.border == "1px dotted") {document.getElementById("Berichtsfuss").style.border="";}
	if (document.getElementById("Seitenfuss").style.border == "1px dotted") {document.getElementById("Seitenfuss").style.border="";}
	document.getElementById("Seitenkopf").innerHTML =  document.getElementById("Seitenkopf").innerHTML.replace('Seitenkopf','');
	document.getElementById("Berichtskopf").innerHTML =  document.getElementById("Berichtskopf").innerHTML.replace('Berichtskopf','');
	document.getElementById("Detailkopf").innerHTML =  document.getElementById("Detailkopf").innerHTML.replace('Detailkopf','');
	document.getElementById("Detailfuss").innerHTML =  document.getElementById("Detailfuss").innerHTML.replace('Detailfuß','');
	document.getElementById("Berichtsfuss").innerHTML =  document.getElementById("Berichtsfuss").innerHTML.replace('Berichtsfuß','');
	document.getElementById("Seitenfuss").innerHTML =  document.getElementById("Seitenfuss").innerHTML.replace('Seitenfuß','');
	Snr = 0;
	if (document.getElementById("deckblattbereich").childNodes.length > 0) {
		Deckblatt = 1;
		Seiten[Snr] = document.getElementById('deckblattbereich').cloneNode();
		Seiten[Snr].id = "Seite_" + (Snr + 1).toString();
		document.getElementById("db_Bereich").appendChild(Seiten[Snr]);
		Seiten[Snr].innerHTML = document.getElementById('deckblattbereich').innerHTML;
		document.getElementById('deckblattbereich').style.display = "none";
		Snr = Snr + 1;
	}
	maxHoehe = neue_Seite(Snr);
	Gesamthoehe = 0;
	newdiv = document.getElementById("Berichtskopf").cloneNode();
	newdiv.id = "Berichtskopf" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Berichtskopf").innerHTML;
	}
	//berechnete Felder berechnen
	for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
		try {
			if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
				document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung(document.getElementById(newdiv.id));
	
	newdiv = document.getElementById("Detailkopf").cloneNode();
	newdiv.id = "Detailkopf" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Detailkopf").innerHTML;
	}
	//berechnete Felder berechnen
	for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
		try {
			if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
				document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung(document.getElementById(newdiv.id));
	Gesamthoehe = 0;
	maxHoehe = parseFloat(document.getElementById("Seitenfuss").style.top) - parseFloat(document.getElementById("Detailkopf").style.top) - parseFloat(document.getElementById("Detailkopf").style.height);
	
	Daten_einlesen();

	newdiv = document.getElementById("Detailfuss").cloneNode();
	newdiv.id = "Detailfuss" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Detailfuss").innerHTML;
	}
	//berechnete Felder berechnen
	for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
		try {
			if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
				document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung(document.getElementById(newdiv.id));
	document.getElementById("Detailfuss" + Snr.toString()).style.top = (Gesamthoehe + parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.height)).toString() + "px";
	Gesamthoehe = parseFloat(document.getElementById("Detailfuss" + Snr.toString()).style.top) + parseFloat(document.getElementById("Detailfuss" + Snr.toString()).style.height);
	if (Gesamthoehe >= maxHoehe) {
		Snr = Snr + 1;
		maxHoehe = neue_Seite(Snr);
		Gesamthoehe = 0;
		Seiten[Snr].appendChild(document.getElementById(newdiv.id));
	}
	newdiv = document.getElementById("Berichtsfuss").cloneNode();
	newdiv.id = "Berichtsfuss" + Snr.toString();
	Seiten[Snr].appendChild(newdiv.cloneNode());
	if (newdiv.style.height== "0px") {
		document.getElementById(newdiv.id).innerHTML = "";
	} else {
		document.getElementById(newdiv.id).innerHTML = document.getElementById("Berichtsfuss").innerHTML;
	}
	//berechnete Felder berechnen
	for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
		try {
			if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
				document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung(document.getElementById(newdiv.id));
	document.getElementById("Berichtsfuss" + Snr.toString()).style.top = Gesamthoehe.toString() + "px";
	Gesamthoehe = Gesamthoehe + parseFloat(document.getElementById("Berichtsfuss" + Snr.toString()).style.height);
	if (Gesamthoehe >= maxHoehe) {
		Snr = Snr + 1;
		maxHoehe = neue_Seite(Snr);
		Gesamthoehe = 0;
		Seiten[Snr].appendChild(document.getElementById(newdiv.id));
	}
	document.getElementById("arbeitsbereich").style.display = "none";
	document.getElementById("anz_seiten").innerHTML = Seiten.length.toString();
	Seite_waehlen("erste");
}

function Seite_waehlen(Modus) {
	if (Modus == " ") {zeigen = parseInt(document.getElementById("seite_akt").value);}
	if (Modus == "erste") {zeigen = 0;}
	if (Modus == "letzte") {zeigen = Seiten.length;}
	if (Modus == "-") {zeigen = parseInt(document.getElementById("seite_akt").value) - 1;}
	if (Modus == "+") {zeigen = parseInt(document.getElementById("seite_akt").value) + 1;}
	if (zeigen < 1) {zeigen = 1;}
	if (zeigen > Seiten.length) {zeigen = Seiten.length;}
	document.getElementById("seite_akt").value = zeigen;
	zeigen = zeigen - 1;
	for (i = 0; i < Seiten.length; i++) {
		if (i == zeigen) {
			Seiten[i].style.display = "block";
		} else {
			Seiten[i].style.display = "none";
		}
	}
}

function Seiten_Kopf_Fuss_einstellen() {
	Bereich = [];
	Bereich[0] = "Seitenkopf";
	Bereich[1] = "Seitenfuss";
	Zusatz = 0;
	if (Deckblatt == 0) {Zusatz = 1;}
	for (z = 0; z < Bereich.length; z++) {
		if (document.getElementById(Bereich[z]).style.height != "0px") {
			Zellen = document.getElementById(Bereich[z]).firstChild.firstChild.firstChild;
			for (i = 0; i < Zellen.childNodes.length; i++) {
				if (Zellen.childNodes[i].innerHTML.substr(0,5) == "Datum" || Zellen.childNodes[i].innerHTML.substr(0,4) == "Date" || Zellen.childNodes[i].innerHTML.substr(0,4) == "date") {
					Zeitp = new Date();
					h = Zeitp.getHours();
					if(h < 10){h = '0' + h;} 
					m = Zeitp.getMinutes();
					if(m < 10){m = '0' + m;}
					d = Zeitp.getDate();
					if(d < 10){d = '0' + d;}
					M = Zeitp.getMonth() + 1;
					if(M < 10){M = '0' + M;}
					J = Zeitp.getFullYear();
					if (Zellen.childNodes[i].innerHTML.substr(0,5) == "Datum" || Zellen.childNodes[i].innerHTML.substr(0,4) == "Date" || Zellen.childNodes[i].innerHTML.substr(0,4) == "date") {Zeitpunkt = d + "." + M + "." + J;}
					if (Zellen.childNodes[i].innerHTML == "Datum_Uhrzeit" || Zellen.childNodes[i].innerHTML == "Date / time" || Zellen.childNodes[i].innerHTML == "Datum Tijd" || Zellen.childNodes[i].innerHTML == "Date / heure") {Zeitpunkt = d + "." + M + "." + J + " " + h + ":" + m;}
					for (x = 0; x < Seiten.length; x++) {
						try {document.getElementById(Bereich[z] + x.toString()).firstChild.firstChild.firstChild.childNodes[i].innerHTML = Zeitpunkt;} catch (err) {}
					}
				}
				if (Zellen.childNodes[i].innerHTML.substr(0,5) == "Seite" || Zellen.childNodes[i].innerHTML.substr(0,4) == "page" || Zellen.childNodes[i].innerHTML.substr(0,4) == "Page" || Zellen.childNodes[i].innerHTML.substr(0,9) == "Bladzijde") {
					if (Zellen.childNodes[i].innerHTML == "Seite " || Zellen.childNodes[i].innerHTML == "Page " || Zellen.childNodes[i].innerHTML == "Bladzijde " || Zellen.childNodes[i].innerHTML == "page ") {
						for (x = 0; x < Seiten.length; x++) {
							try {document.getElementById(Bereich[z] + x.toString()).firstChild.firstChild.firstChild.childNodes[i].innerHTML = x.toString();} catch (err) {}
						}
					} else {
						for (x = 0; x < Seiten.length; x++) {
							try {document.getElementById(Bereich[z] + x.toString()).firstChild.firstChild.firstChild.childNodes[i].innerHTML = T_Text[11] + " " + (x + Zusatz).toString() + " / " + (Seiten.length - Deckblatt).toString();} catch (err) {}
						}
					}
				}
			}
			for (x = 0; x < Seiten.length; x++) {
				try {document.getElementById(Bereich[z] + x.toString()).setAttribute("textinhalt",document.getElementById(Bereich[z] + x.toString()).innerHTML);} catch (err) {}
			}
		}
	}
	//Beidseitiger Druck berücksichtigen
	if (Seiteneigenschaften["Format"] == T_Text[12] && Seiteneigenschaften["beidseitig"] == 1) {
		x = 0;
		if (document.getElementById("deckblattbereich").childNodes.length > 0) {x = 1;}
		Bereich = [];
		Bereich[0] = "Seitenkopf";
		Bereich[1] = "Seitenfuss";
		for (S = x; S < Seiten.length; S++) {
			if ((x == 1 && S/2 == parseInt(S/2)) ||(x == 0 && S/2 != parseInt(S/2))) {
				for (i = 0; i < Bereich.length; i++) {
					Zeile = document.getElementById(Bereich[i] + S.toString()).firstChild.firstChild.firstChild;
					temp = Zeile.childNodes[0].innerHTML;
					Zeile.childNodes[0].innerHTML = Zeile.childNodes[2].innerHTML;
					Zeile.childNodes[2].innerHTML = temp;
					Zeile.parentElement.parentElement.parentElement.attributes.textinhalt.value = Zeile.parentElement.parentElement.parentElement.innerHTML;
				}
			}
		}
	}
}

function pdf_erstellen() {
	gesInhalt = "";
	ElemTyp = [];
	ElemTyp[0] = "div";
	ElemTyp[1] = "img";
	for (x = 0; x < Seiten.length; x++) {
		Inhalt = "";
		Seite = document.getElementById(Seiten[x].id);
		for (t = 0; t < ElemTyp.length; t++) {
			Elemente = Seite.getElementsByTagName(ElemTyp[t]);
			for (i = 0; i < Elemente.length; i++) {
				if (Elemente[i].style.display != "none") {
					if (Elemente[i].style.border == "medium none") {Elemente[i].style.border = "";}
					xZusatz = 0;
					yZusatz = 0; 
					temp_Elem = Elemente[i].parentNode;
					while (temp_Elem.id != "db_Bereich"){
						xZusatz = xZusatz + parseFloat(temp_Elem.style.left) / vergr_Faktor;
						yZusatz = yZusatz + parseFloat(temp_Elem.style.top) / vergr_Faktor;
						temp_Elem = temp_Elem.parentNode;
					}
					Elem = {};
					if (Elemente[i].style.top != "") {
						Elem["o"] = parseFloat(Elemente[i].style.top) / vergr_Faktor + yZusatz;
					} else {
						Elem["o"] = "";
					}	
					if (Elemente[i].style.left != "") {
						Elem["l"] = parseFloat(Elemente[i].style.left) / vergr_Faktor + xZusatz;
					} else {
						Elem["l"] = "";
					}
					if (Elemente[i].style.height != "") {
						Elem["h"] = parseFloat(Elemente[i].style.height) / vergr_Faktor;
					} else {
						Elem["h"] = "";
					}
					if (Elemente[i].style.width != "") {
						Elem["b"] = parseFloat(Elemente[i].style.width) / vergr_Faktor;
					} else {
						Elem["b"] = "";
					}
					if (Elemente[i].style.fontSize != "") {
						Elem["SG"] = parseFloat(Elemente[i].style.fontSize) / vergr_Faktor / 25.2 * 72;
					} else {
						Elem["SG"] = 0;
					}
					if (Elemente[i].style.fontWeight != "") {
						if (Elemente[i].style.fontWeight.substr(0,4) == "bold") {
							Elem["SB"] = "B";
						} else {
							if (Elemente[i].style.fontWeight.substr(0,4) == "ital") {
								Elem["SB"] = "I";
							} else {
								Elem["SB"] = "";
							}
						}
					} else {
						Elem["SB"] = "";
					}
					if (Elemente[i].style.color != "") {
						pos = 4;
							Farbe = "";
						while (Elemente[i].style.color.substr(pos,1) != "," && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.color.substr(pos,1);
							pos = pos + 1;
						}
						Elem["SF1"] = Farbe;
						while ((Elemente[i].style.color.substr(pos,1) == "," || Elemente[i].style.color.substr(pos,1) == " ") && pos < Elemente[i].style.backgroundColor.length) {
							pos = pos + 1;
						}
						Farbe = "";
						while (Elemente[i].style.color.substr(pos,1) != "," && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.color.substr(pos,1);
							pos = pos + 1;
						}
						Elem["SF2"] = Farbe;
						while ((Elemente[i].style.color.substr(pos,1) == "," || Elemente[i].style.color.substr(pos,1) == " ") && pos < Elemente[i].style.backgroundColor.length) {
							pos = pos + 1;
						}
						Farbe = "";
						while (Elemente[i].style.color.substr(pos,1) != ")" && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.color.substr(pos,1);
							pos = pos + 1;
						}
						Elem["SF3"] = Farbe;
					} else {
						Elem["SF1"] = 0;
						Elem["SF2"] = 0;
						Elem["SF3"] = 0;
					}
					if (Elemente[i].style.backgroundColor != "") {
						pos = 4;
						Farbe = "";
						while (Elemente[i].style.backgroundColor.substr(pos,1) != "," && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.backgroundColor.substr(pos,1);
							pos = pos + 1;
						}
						Elem["HF1"] = Farbe;
						while ((Elemente[i].style.backgroundColor.substr(pos,1) == "," || Elemente[i].style.backgroundColor.substr(pos,1) == " ") && pos < Elemente[i].style.backgroundColor.length) {
							pos = pos + 1;
						}
						Farbe = "";
						while (Elemente[i].style.backgroundColor.substr(pos,1) != "," && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.backgroundColor.substr(pos,1);
							pos = pos + 1;
						}
						Elem["HF2"] = Farbe;
						while ((Elemente[i].style.backgroundColor.substr(pos,1) == "," || Elemente[i].style.backgroundColor.substr(pos,1) == " ") && pos < Elemente[i].style.backgroundColor.length) {
							pos = pos + 1;
						}
						Farbe = "";
						while (Elemente[i].style.backgroundColor.substr(pos,1) != ")" && pos < Elemente[i].style.backgroundColor.length) {
							Farbe = Farbe + Elemente[i].style.backgroundColor.substr(pos,1);
							pos = pos + 1;
						}
						Elem["HF3"] = Farbe;
					} else {
						Elem["HF1"] = 255;
						Elem["HF2"] = 255;
						Elem["HF3"] = 255;
					}
					Elem["Ausr"] = "L";
					if (Elemente[i].hasAttribute("align") == true) {
						if (Elemente[i].attributes["align"].value == "right") {Elem["Ausr"] = "R";}
						if (Elemente[i].attributes["align"].value == "center") {Elem["Ausr"] = "C";}
					}
					Elem["Rand"] = "";
					if (Elemente[i].style.borderBottom != "") {Elem["Rand"] = "B";}
					if (Elemente[i].style.borderTop != "") {Elem["Rand"] = Elem["Rand"] + "T";}
					if (Elemente[i].style.borderWidth != "" && Elem["Rand"] == "") {
						Elem["Rand"] = 1;
					} else {
						if (Elem["Rand"] == "") {Elem["Rand"] = 0;}
					}
					Elem["Link"] = "";
					try {Elem["Link"] = Elemente[i].attributes.link.value;} catch (err) {}
					Elem["T"] = "";
					if (Elemente[i].id.substr(0,4) != "Satz") {
						try {
							Elem["T"] = Elemente[i].attributes.textinhalt.value;
						} catch (err) {
							if (Elemente[i].childNodes.length == 1) {
								try {
									Elem["T"] = Elemente[i].firstChild.innerHTML;
									if (Elem["T"] == undefined) {Elem["T"] = Elemente[i].innerHTML;}
								} catch (err) {
									Elem["T"] = Elemente[i].innerHTML;
								}
							}
						}
					}
					Elem["Bild_Adresse"] = "";
					Elem["Bild_Typ"] = "";
					if (ElemTyp[t] == "img") {
						try {
							Elem["Bild_Adresse"] = Elemente[i].attributes.src.value;
							Elem["Bild_Typ"] = Elemente[i].attributes.src.value.substr(-3);
						} catch (err) {}
					}
					if (Inhalt != "") {Inhalt = Inhalt + "@@@";}
					Inhalt = Inhalt + JSON.stringify(Elem);
				}
			}
		}
		if (gesInhalt != "") {gesInhalt = gesInhalt + "|||";}
		gesInhalt = gesInhalt + Inhalt;
	}
	PDF_anfordern.Seit_Eig.value = JSON.stringify(Seiteneigenschaften);
	PDF_anfordern.PDF_Inhalt.value = gesInhalt;
	PDF_anfordern.submit();
}

function bed_Formatierung(Bereich) {
	bednr = 1;
	while (Bedingungen[bednr] != undefined) {
		Ziel = document.getElementById(Elem(Bedingungen[bednr].Element,Bereich.id));
		if (Ziel != null) {
			Wert = Function('return ' + Bedingungen[bednr].Bedingung)();
			if (Wert == true) {
				Ziel.className = Bedingungen[bednr].class;
				Ziel.style.fontFamily = Bedingungen[bednr]["font-family"];
				Ziel.style.fontStyle = Bedingungen[bednr]["font-style"];
				Ziel.style.color = Bedingungen[bednr]["color"];
				Ziel.style.fontSize = Bedingungen[bednr]["font-size"];
				Ziel.style.fontWeight = Bedingungen[bednr]["font-weight"];
				Ziel.style.display = Bedingungen[bednr]["sichtbar"];
				Ziel.style.borderColor = Bedingungen[bednr]["Rahmenfarbe"];
				Ziel.style.borderStyle = Bedingungen[bednr]["border-style"];
				Ziel.style.borderWidth = Bedingungen[bednr]["Rahmenbreite"];
				Ziel.style.backgroundColor = Bedingungen[bednr]["background-color"];
			}
		}
		bednr = bednr + 1;
	}
}

function Elem(Elementname,Bereichid) {
	if (Bereichid == undefined) {Bereichid = Ber_id;}
	Bereich = document.getElementById(Bereichid);
	for (z = 0; z < Bereich.childNodes.length; z++) {
		try {
			if (Bereich.childNodes[z].attributes.elementname.value == Elementname) {return Bereich.childNodes[z].id;}
		} catch (err) {}
	}
	return "Error";
}

function Elem_Wert(Elementname,Bereichid) {
	if (Bereichid == undefined) {Bereichid = Ber_id;}
	try {
		Wert = parseFloat(document.getElementById(Elem(Elementname,Bereichid)).value);
		if (isNaN(Wert) == true) {Wert = document.getElementById(Elem(Elementname,Bereichid)).value;}
		if (Wert == undefined) {
			Wert = parseFloat(document.getElementById(Elem(Elementname,Bereichid)).innerHTML);
			if (isNaN(Wert) == true) {Wert = document.getElementById(Elem(Elementname,Bereichid)).innerHTML;}
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

function Daten_holen(Gruppierungselement,Kriterium) {
	if (Ebene == 1) {
		SQL = document.getElementById("datenquelle").value;
	} else {
		SQL = Gruppierungselement.attributes.sql.value;
		verkn = Gruppierungselement.attributes.verkn_feld.value;
		if (SQL.toLocaleLowerCase().indexOf("where") == -1) {
			pos = SQL.toLocaleLowerCase().indexOf("from") + 5;
			while (SQL.substr(pos,1) != " " && pos < SQL.length) {
				pos = pos + 1;
			}
			l = SQL.substr(0,pos);
			r = SQL.substr(pos + 1, SQL.lenght);
			SQL = l + " WHERE `" + verkn + "` = '" + Kriterium + "' " + r;
		} else {
			pos = SQL.toLocaleLowerCase().indexOf("where") + 6;
			l = SQL.substr(0,pos);
			r = SQL.substr(pos + 1, SQL.lenght);
			SQL = l + "`" + verkn + "` = '" + verkn + "' = '" + Kriterium + "' AND " + r;
		}
	}
	jQuery.ajax({
		url: "Daten_lesen.php",
		success: function (html) {
   		strReturn = html;
		},
		type: 'POST',
		data: {query: SQL, user:'Bericht'},
		async: false
	});
	return JSON.parse(strReturn);
}

function Daten_einlesen() {
	Bearbeitungbereich = document.getElementById("Detailbereich");
	EB[1] = Bearbeitungbereich
	Ebene_geschaltet = 0;
	Satz = [];
	Nr = [];
	Feld = [];
	Felder = [];
	Gesamthoehe = 0;
	Detailber1 = Bearbeitungbereich.cloneNode();
	Detailber1.id = "Detailbereich" + Snr.toString();
	try {oben = parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.height);} catch (err) {}
	try {oben = parseFloat(document.getElementById("Berichtskopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Berichtskopf" + Snr.toString()).style.height) + 1;} catch (err) {}
	try {oben = parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.height) + 5;} catch (err) {}
	maxHoehe = parseInt(document.getElementById("Seitenfuss").style.top) - oben;
	Gesamthoehe = oben;
	Detailber1.innerHTML = "";
	while (Ebene > 0) {
		Bearbeitungbereich = EB[Ebene];
		Bearbeitungbereich.style.border = "none";
		x = 0;
		if (Satz[Ebene] == undefined || Kriterium_alt != Kriterium) {
			Feld[Ebene] = [];
			Nr[Ebene] = [];
			Tiefpunkt = 0;
			for (i = 0; i < Bearbeitungbereich.childNodes.length; i++) {
				try {
					Bearbeitungbereich.childNodes[i].removeAttribute("textinhalt");
					if (Bearbeitungbereich.childNodes[i].hasAttribute("feld") == true || Bearbeitungbereich.childNodes[i].hasAttribute("verkn_feld") == true) {
						if (Bearbeitungbereich.childNodes[i].hasAttribute("feld") == true) {
							Feld[Ebene][x] = Bearbeitungbereich.childNodes[i].attributes["feld"].value;
							Bearbeitungbereich.childNodes[i].setAttribute("textinhalt",Feld[Ebene][x]);
						}
						Nr[Ebene][x] = i;
						x = x + 1;
					}
					if (parseFloat(Bearbeitungbereich.childNodes[i].style.top) + parseFloat(Bearbeitungbereich.childNodes[i].style.height) > Tiefpunkt) {Tiefpunkt = parseFloat(Bearbeitungbereich.childNodes[i].style.top) + parseFloat(Bearbeitungbereich.childNodes[i].style.height);}
				} catch (err) {}
			}
			Felder[Ebene] = x;
			Satz[Ebene] = [];
		
			if (Ebene == 1) {Kriterium = "";}
			EaS[Ebene] = 0;
			Satz[Ebene] = [];
			E[Ebene] = Daten_holen(Bearbeitungbereich,Kriterium);
			Kriterium_alt = Kriterium;
			EaS[Ebene] = 0;
		}
		unterer_Rand = parseFloat(Bearbeitungbereich.style.height) - Tiefpunkt;
		for (i = EaS[Ebene]; i < E[Ebene].length; i++) {
			Satz[Ebene][i] = Bearbeitungbereich.cloneNode();
			Satz[Ebene][i].id = "Satz" + Ebene.toString() + "_" + i.toString();
			Satz[Ebene][i].style.top = Gesamthoehe.toString() + "px";
			Satz[Ebene][i].innerHTML = Bearbeitungbereich.innerHTML;
			for (x = 0; x < Felder[Ebene]; x++) {
				try {
					EaS[Ebene] = i;
					Satz[Ebene][i].childNodes[Nr[Ebene][x]].id = Satz[Ebene][i].childNodes[Nr[Ebene][x]].id + i.toString();
					if (Satz[Ebene][i].childNodes[Nr[Ebene][x]].hasAttribute("verkn_feld") == true) {
						Ebene_geschaltet = 1;
						Bearbeitungbereich = Satz[Ebene][i].childNodes[Nr[Ebene][x]];
						EB[Ebene + 1] = Bearbeitungbereich;
						for (su = 0; su < Satz[Ebene][i].childNodes.length; su++) {
							try {if (Satz[Ebene][i].childNodes[su].attributes.feld.value == Satz[Ebene][i].childNodes[Nr[Ebene][x]].attributes.verkn_feld.value) {Kriterium = Satz[Ebene][i].childNodes[su].attributes.textinhalt.value;}} catch (err) {}
						}
						Satz[Ebene][i].removeChild(Satz[Ebene][i].childNodes[Nr[Ebene][x]]);
						Satz[Ebene][i].style.height = Satz[Ebene][i].scrollHeight.toString() + "px"; 
					} else {
						if (E[Ebene][i][Feld[Ebene][x]] == undefined) {
							Satz[Ebene][i].childNodes[Nr[Ebene][x]].innerHTML = E[Ebene][i][Feld[Ebene]];
							Satz[Ebene][i].childNodes[Nr[Ebene][x]].setAttribute("textinhalt",E[Ebene][i][Feld[Ebene]]);
						} else {
							Satz[Ebene][i].childNodes[Nr[Ebene][x]].innerHTML = E[Ebene][i][Feld[Ebene][x]];
							Satz[Ebene][i].childNodes[Nr[Ebene][x]].setAttribute("textinhalt",E[Ebene][i][Feld[Ebene][x]]);
						}
					}
				} catch (err) {}
			}
			Seiten[Snr].appendChild(Satz[Ebene][i]);
			//berechnete Felder berechnen
			for (b = 0; b < Satz[Ebene][i].childNodes.length; b++) {
				try {
					if (Satz[Ebene][i].childNodes[b].attributes.feldtyp.value == "berechnet") {
						Wert = Function('return ' + Satz[Ebene][i].childNodes[b].attributes.formel.value)();
						Satz[Ebene][i].childNodes[b].innerHTML = Wert;
					}
				} catch (err) {}
			}
			//Ende Berechnungen
			Ber_id = Satz[Ebene][i].id;
			bed_Formatierung(Satz[Ebene][i]);
			for (x = 0; x < Felder[Ebene]; x++) {
				try {
					if (Satz[Ebene][i].childNodes[Nr[Ebene][x]].scrollHeight > parseFloat(Satz[Ebene][i].childNodes[Nr[Ebene][x]].style.height)) {
						Differenz = Satz[Ebene][i].childNodes[Nr[Ebene][x]].scrollHeight - parseFloat(Satz[Ebene][i].childNodes[Nr[Ebene][x]].style.height);
						Satz[Ebene][i].childNodes[Nr[Ebene][x]].style.height = Satz[Ebene][i].childNodes[Nr[Ebene][x]].scrollHeight.toString() + "px";
						tempdiv = Satz[Ebene][i].childNodes[Nr[Ebene][x]].parentElement;
						while (tempdiv.id.indexOf("etailber") == -1 && tempdiv.id.indexOf("Seite") == -1) {
							tempdiv.style.height = (Differenz + parseFloat(tempdiv.style.height)).toString() + "px";
							tempdiv = tempdiv.parentElement;
						}
					}
				} catch (err) {}
			}
			gefunden = 1;
			fertig = 1;
			while (fertig == 1) {
				fertig = 0;
				gefunden = 1;
				while (gefunden == 1) {
					gefunden = 0;
					for (x = 0; x < Satz[Ebene][0].childNodes.length; x++) {
						for (y = 0; y < Satz[Ebene][0].childNodes.length; y++) {
							if (y != x) {
								try {
									if (parseFloat(Satz[Ebene][i].childNodes[x].style.top) > parseFloat(Satz[Ebene][i].childNodes[y].style.top)) {
										if (parseFloat(Satz[Ebene][i].childNodes[y].style.top) + parseFloat(Satz[Ebene][i].childNodes[y].style.height) > parseFloat(Satz[Ebene][i].childNodes[x].style.top)) {
											if (parseFloat(Satz[Ebene][i].childNodes[x].style.left) + parseFloat(Satz[Ebene][i].childNodes[x].style.width) > parseFloat(Satz[Ebene][i].childNodes[y].style.left)) {
												orig_Abstand = parseFloat(Bearbeitungbereich.childNodes[x].style.top) - parseFloat(Bearbeitungbereich.childNodes[y].style.top) - parseFloat(Bearbeitungbereich.childNodes[y].style.height);
												if (parseInt(parseFloat(Satz[Ebene][i].childNodes[x].style.top) * 100) != parseInt((parseFloat(Satz[Ebene][i].childNodes[y].style.top) + parseFloat(Satz[Ebene][i].childNodes[y].style.height) + orig_Abstand) * 100)) {
													Satz[Ebene][i].childNodes[x].style.top = (parseFloat(Satz[Ebene][i].childNodes[y].style.top) + parseFloat(Satz[Ebene][i].childNodes[y].style.height) + orig_Abstand).toString() + "px";
													gefunden = 1;
													fertig = 1;
												}
											}
										}
									}
									if (parseFloat(Satz[Ebene][i].childNodes[y].style.top) > parseFloat(Satz[Ebene][i].childNodes[x].style.top)) {
										if (parseFloat(Satz[Ebene][i].childNodes[x].style.top) + parseFloat(Satz[Ebene][i].childNodes[x].style.height) > parseFloat(Satz[Ebene][i].childNodes[y].style.top)) {
											if (parseFloat(Satz[Ebene][i].childNodes[y].style.left) + parseFloat(Satz[Ebene][i].childNodes[y].style.width) > parseFloat(Satz[Ebene][i].childNodes[x].style.left)) {
												orig_Abstand = parseFloat(Bearbeitungbereich.childNodes[y].style.top) - parseFloat(Bearbeitungbereich.childNodes[x].style.top) - parseFloat(Bearbeitungbereich.childNodes[x].style.height);
												if (parseInt(parseFloat(Satz[Ebene][i].childNodes[y].style.top) * 100) != parseInt((parseFloat(Satz[Ebene][i].childNodes[x].style.top) + parseFloat(Satz[Ebene][i].childNodes[x].style.height) + orig_Abstand) * 100)) {
													Satz[Ebene][i].childNodes[y].style.top = (parseFloat(Satz[Ebene][i].childNodes[x].style.top) + parseFloat(Satz[Ebene][i].childNodes[x].style.height) + orig_Abstand).toString() + "px";
													gefunden = 1;
													fertig = 1;
												}
											}
										}
									}
								} catch (err) {}
							}
						}
					}	
				}
			}
			Erhoehung = 0;
			if (Satz[Ebene][i].scrollHeight - parseInt(Bearbeitungbereich.style.height) > 0) {
				Erhoehung = unterer_Rand;
				Satz[Ebene][i].style.height = (Satz[Ebene][i].scrollHeight + Erhoehung).toString() + "px";
			}
			Gesamthoehe = Gesamthoehe + Satz[Ebene][i].scrollHeight + Erhoehung;
			if (Gesamthoehe > maxHoehe) {
				Seiten[Snr].removeChild(Satz[Ebene][i]);
				Snr = Snr + 1;
				maxHoehe = neue_Seite(Snr);
				newdiv = document.getElementById("Detailkopf").cloneNode();
				newdiv.id = "Detailkopf" + Snr.toString();
				Seiten[Snr].appendChild(newdiv.cloneNode());
				if (newdiv.style.height== "0px") {
					document.getElementById(newdiv.id).innerHTML = "";
				} else {
					document.getElementById(newdiv.id).innerHTML = document.getElementById("Detailkopf").innerHTML;
				}
				//berechnete Felder berechnen
				for (b = 0; b < document.getElementById(newdiv.id).childNodes.length; b++) {
					try {
						if (document.getElementById(newdiv.id).childNodes[b].attributes.feldtyp.value == "berechnet") {
							Wert = Function('return ' + document.getElementById(newdiv.id).childNodes[b].attributes.formel.value)();
							document.getElementById(newdiv.id).childNodes[b].innerHTML = Wert;
						}
					} catch (err) {}
				}
				//Ende Berechnungen
				bed_Formatierung(document.getElementById(newdiv.id));
				document.getElementById(newdiv.id).style.top = (parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.height)).toString() + "px"; 
				Gesamthoehe = 0;
				maxHoehe = parseFloat(document.getElementById("Seitenfuss").style.top) - parseFloat(document.getElementById("Detailkopf").style.top) - parseFloat(document.getElementById("Detailkopf").style.height);
				try {oben = parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Seitenkopf" + Snr.toString()).style.height);} catch (err) {}
				try {oben = parseFloat(document.getElementById("Berichtskopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Berichtskopf" + Snr.toString()).style.height) + 1;} catch (err) {}
				try {oben = parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.top) + parseFloat(document.getElementById("Detailkopf" + Snr.toString()).style.height) + 5;} catch (err) {}
				Gesamthoehe = oben;
				Satz[Ebene][i].style.top = oben.toString() + "px";
				Seiten[Snr].appendChild(Satz[Ebene][i]);
				Erhoehung = 0;
				if (Satz[Ebene][i].scrollHeight -  parseInt(Bearbeitungbereich.style.height) > 0) {Erhoehung = unterer_Rand;}
				Gesamthoehe = Gesamthoehe + Satz[Ebene][i].scrollHeight + Erhoehung;
				maxHoehe = parseInt(document.getElementById("Seitenfuss").style.top) - oben;
			}
			if (Ebene_geschaltet == 1) {
				Ebene_geschaltet = 0;
				EaS[Ebene] = i + 1;
				i = 0;
				Ebene = Ebene + 2;
				break
			}
		}
		Ebene = Ebene - 1;
	}
}



