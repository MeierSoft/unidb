var Spalten = 0;
var links = 10;
var Foben = 65;
var Verb = new Array;
var Tab = new Array;
Verb_Objekt = null;
var Ebene = 0;
var Kriterienzeilen = 2;
var AuswVersionen = -1;
var Ergebnisausgaben = 0;
var BTeile = new Array;
var T_Text = new Array;

$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	Hoehe = window.innerHeight - 328;
	Spalten = parseInt((window.innerWidth - 70) / 80);
	Spaltenbreite = (window.innerWidth - 70) / Spalten;
	Spalten = Spalten - 1;
	//Tabellen_fuellen();
	Auswahltabelle_bauen();
});

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["phpform"].aktion.value = T_Text[6];
	} else {
		document.forms["phpform"].action = "Baum2.php";
		document.forms["phpform"].target = "Baum";
		document.forms["phpform"].aktion.value = "Hist_löschen";
	}
	abspeichern();
	document.forms["phpform"].submit();
	window.reload();
}

function Vers_zeigen() {
	document.forms["phpform"].action = "Abfrage.php";
	document.forms["phpform"].target = "Hauptrahmen";
	document.forms["phpform"].submit();
	document.forms["phpform"].action = "Baum2.php";
	document.forms["phpform"].target = "Baum";
	window.reload();
}
	
function Auswahltabelle_bauen(SQL_nicht_einlesen) {
	if (SQL_nicht_einlesen == undefined || SQL_nicht_einlesen == "") {SQL_nicht_einlesen = 0;}
	if (document.getElementById("abfragetyp").value == undefined || document.getElementById("abfragetyp").value == "") {
		Abfragetyp = "SELECT";
	} else {
		Abfragetyp = document.getElementById("abfragetyp").value;
	}
	try {auswahlbereich.close();} catch (err) {}
	AuswVersionen += 1;
	Inhalt = "<div class='context-menu-one' id='updatePanel' style='position: relative; top: 5px; left: 5px;'><table class='Text_einfach' id='kriterientabelle" + AuswVersionen.toString() + "' width='100%'>\n";
	Inhalt += "<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'></td>";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><table width='100%'><tr><td class='Text_fett' align='center'><input class='Schalter_fett_Element' type='button' value = ' - ' id='entf" + i + "' onclick='Spalte_entfernen(" + i + ");'></td><td class='Text_fett' align='center'><input class='Schalter_fett_Element' value = ' + ' type='button' id='einf" + i + "' onclick='Spalte_einfuegen(" + i + ");'></td></tr></table></td>\n";
	}	
	
	Inhalt += "<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[28] + "</td>";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' type='Text' id='feld" + i + "' style='width: 100%;' ondrop='auswahl_drop(event)' ondragover='allowDrop(event)'></td>\n";
	}
	if (Abfragetyp == "SELECT") {
		unsichtbar = "";
	} else {
		unsichtbar = "display:none; ";
	}
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[19] + "</td>";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' type='Text' id='funktion" + i + "' style='" + unsichtbar + "width: 100%;' onblur='Feld_u_Tabelle_leeren();'></td>\n";
	}	
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[20] + "</td>\n";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' type='Text' id='alias" + i + "' style='" + unsichtbar + "width: 100%;'></td>\n";
	}
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[21] + "</td>\n";
	if (document.getElementById("tabellenliste") == null) {
		Liste = "";
	} else {
		Liste = document.getElementById("tabellenliste").cloneNode(true);
	}
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><select class='Auswahl_Liste_Element' id='tabelle" + i + "' style='width: 100%;' size='1' onchange='auswahl_Tab_change(" + i + ")'>" + Liste.innerHTML + "</select></td>\n";
	}
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[22] + "</td>\n";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><select class='Auswahl_Liste_Element' id='sortierung" + i + "' style='" + unsichtbar + "width: 100%;' size='1'><option selected></option><option>aufwärts</option><option>abwärts</option></select></td>\n";
	}
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[23] + "</td>\n";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' id='gruppieren" + i + "' style='" + unsichtbar + "width: 100%;' size='1' type='checkbox'></td>\n";
	}
	Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[24] + "</td>\n";
	for (i = 0; i < Spalten; i++) {
		Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' id='anzeigen" + i + "' style='" + unsichtbar + "width: 100%;' size='1' type='checkbox'></td>\n";
	}
	if (Abfragetyp == "UPDATE" || Abfragetyp == "INSERT") {
		Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[25] + "</td>\n";
		for (i = 0; i < Spalten; i++) {
			Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' id='schreiben" + i + "' style='width: 100%;' type='Text'></td>\n";
		}
	}
	Inhalt += "</tr>\n<tr><td colspan='" + (Spalten + 1).toString() + "'><hr></td>\n";
	for (x = 0; x < Kriterienzeilen; x++) {
		Inhalt += "</tr>\n<tr class='Tabellenzeile'><td class='Text_einfach' align='right' width='65px'>" + T_Text[26] + "</td>\n";
		for (i = 0; i < Spalten; i++) {
			Inhalt += "<td width='" + Spaltenbreite + "'><input class='Text_Element' type='Text' id='kriterium" + i.toString() + "_" + x.toString() + "' style='width: 100%;' onchange='Tabelle_erweitern();'></td>\n";
		}
		Inhalt += "</tr>\n";
	}
	Inhalt += "</table></div>\n";
	jsPanel.create({
		id: 'auswahlbereich',
		theme: 'info',
		contentSize: window.innerWidth + ' 300',
		headerTitle: T_Text[27],
		headerControls: {
			size: 'xs'
		},
		position: 'left-top 0 ' + Hoehe,
		content:  Inhalt,
		contentOverflow: 'scroll scroll',
	});
	if (SQL_nicht_einlesen == 0) {SQL_einlesen();}
	$("#kriterientabelle" + AuswVersionen.toString()).colResizable({
		liveDrag:true, 
		draggingClass:"dragging", 
		resizeMode:'overflow', 
	}); 
}

function Spalte_entfernen(Spalte) {
	for (i = Spalte; i < Spalten - 1; i++) {
		try {document.getElementById("feld" + i.toString()).value = document.getElementById("feld" + (i + 1).toString()).value;} catch (err) {}
		try {document.getElementById("funktion" + i.toString()).value = document.getElementById("funktion" + (i + 1).toString()).value;} catch (err) {}
		try {document.getElementById("alias" + i.toString()).value = document.getElementById("alias" + (i + 1).toString()).value;} catch (err) {}
		try {document.getElementById("tabelle" + i.toString()).value = document.getElementById("tabelle" + (i + 1).toString()).value;} catch (err) {}
		try {document.getElementById("sortierung" + i.toString()).value = document.getElementById("sortierung" + (i + 1).toString()).value;} catch (err) {}
		try {
			if (document.getElementById("gruppieren" + (i + 1).toString()).checked == true) {
				document.getElementById("gruppieren" + i.toString()).checked = true;
			} else {
				document.getElementById("gruppieren" + i.toString()).checked = false;
			}
		} catch (err) {}
		try {
			if (document.getElementById("anzeigen" + (i + 1).toString()).checked == true) {
				document.getElementById("anzeigen" + i.toString()).checked = true;
			} else {
				document.getElementById("anzeigen" + i.toString()).checked = false;
			}
		} catch (err) {}
		try {document.getElementById("schreiben" + i.toString()).value = document.getElementById("schreiben" + (i + 1).toString()).value;} catch (err) {}
		for (x = 0; x < Kriterienzeilen; x++) {
			try {document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value = document.getElementById("kriterium" + (i + 1).toString() + "_" + x.toString()).value;} catch (err) {}
		}
	}
	try {document.getElementById("feld" + (Spalten - 1).toString()).value = "";} catch (err) {}
	try {document.getElementById("funktion" + (Spalten - 1).toString()).value = "";} catch (err) {}
	try {document.getElementById("alias" + (Spalten - 1).toString()).value = "";} catch (err) {}
	try {document.getElementById("tabelle" + (Spalten - 1).toString()).value = "";} catch (err) {}
	try {document.getElementById("sortierung" + (Spalten - 1).toString()).value = "";} catch (err) {}
	try {document.getElementById("gruppieren" + (Spalten - 1).toString()).checked = false;} catch (err) {}
	try {document.getElementById("anzeigen" + (Spalten - 1).toString()).checked = false;} catch (err) {}
	try {document.getElementById("schreiben" + (Spalten - 1).toString()).value = "";} catch (err) {}
	for (x = 0; x < Kriterienzeilen; x++) {
		try {document.getElementById("kriterium" + (Spalten - 1).toString() + "_" + x.toString()).value = "";} catch (err) {}
	}	
}

function Spalte_einfuegen(Spalte) {
	x = (Spalten - 1).toString();
	if (document.getElementById("feld" + x).value != "" || document.getElementById("funktion" + x).value != "" || document.getElementById("alias" + x).value != "") {
		Spalten +=1;
		Auswahltabelle_bauen(0);
	}
	for (i = Spalten - 1; i > Spalte; i--) {
		try {document.getElementById("feld" + i.toString()).value = document.getElementById("feld" + (i - 1).toString()).value;} catch (err) {}
		try {document.getElementById("funktion" + i.toString()).value = document.getElementById("funktion" + (i - 1).toString()).value;} catch (err) {}
		try {document.getElementById("alias" + i.toString()).value = document.getElementById("alias" + (i - 1).toString()).value;} catch (err) {}
		try {document.getElementById("tabelle" + i.toString()).value = document.getElementById("tabelle" + (i - 1).toString()).value;} catch (err) {}
		try {document.getElementById("sortierung" + i.toString()).value = document.getElementById("sortierung" + (i - 1).toString()).value;} catch (err) {}
		try {
			if (document.getElementById("gruppieren" + (i - 1).toString()).checked == true) {
				document.getElementById("gruppieren" + i.toString()).checked = true;
			} else {
				document.getElementById("gruppieren" + i.toString()).checked = false;
			}
		} catch (err) {}
		try {
			if (document.getElementById("anzeigen" + (i - 1).toString()).checked == true) {
				document.getElementById("anzeigen" + i.toString()).checked = true;
			} else {
				document.getElementById("anzeigen" + i.toString()).checked = false;
			}
		} catch (err) {}
		try {document.getElementById("schreiben" + i.toString()).value = document.getElementById("schreiben" + (i - 1).toString()).value;} catch (err) {}
		for (x = 0; x < Kriterienzeilen; x++) {
			try {document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value = document.getElementById("kriterium" + (i - 1).toString() + "_" + x.toString()).value;} catch (err) {}
		}
	}
	try {document.getElementById("feld" + (Spalte).toString()).value = "";} catch (err) {}
	try {document.getElementById("funktion" + (Spalte).toString()).value = "";} catch (err) {}
	try {document.getElementById("alias" + (Spalte).toString()).value = "";} catch (err) {}
	try {document.getElementById("tabelle" + (Spalte).toString()).value = "";} catch (err) {}
	try {document.getElementById("sortierung" + (Spalte).toString()).value = "";} catch (err) {}
	try {document.getElementById("gruppieren" + (Spalte).toString()).checked = false;} catch (err) {}
	try {document.getElementById("anzeigen" + (Spalte).toString()).checked = false;} catch (err) {}
	try {document.getElementById("schreiben" + (Spalte).toString()).value = "";} catch (err) {}
	for (x = 0; x < Kriterienzeilen; x++) {
		try {document.getElementById("kriterium" + (Spalte).toString() + "_" + x.toString()).value = "";} catch (err) {}
	}	
}

var Tabelle_entfernen = function (event) {
	Tabellenname = event.detail.substr(0,event.detail.length - 8);
	for (i = 0; i < Tab.length; i++) {
		if (Tab[i].Tabelle == Tabellenname) {Tab.splice(i,1);}
	}
	for (i = 0; i < Verb.length; i++) {
		if (Verb[i].Starttabelle == Tabellenname || Verb[i].Zieltabelle == Tabellenname) {
			loeschen = document.getElementById("div_" + Verb[i].id);
			loeschen.parentElement.removeChild(loeschen);
			Verb.splice(i,1);
			i = 0;
		}
	}
}

document.addEventListener('jspanelclosed', Tabelle_entfernen, false);

$(function(){
	$.contextMenu({
		selector: '.context-menu-one',
		zIndex: 200,
		callback: function(key, options) {
			if (key == "Eigenschaften") {Verbindung_Dialog_oeffnen(options.$trigger[0].id);}
			if (key == "entfernen") {Verbindung_entfernen(options.$trigger[0].id);}
		},
		items: {
			"Eigenschaften": {"name": "Eigenschaften", icon: "edit"},
			"entfernen": {"name": "entfernen", icon: "delete"}
		}
	});
});

function Tabelle_erweitern() {
	SQL_erzeugen();
	erneuern = 0;
	temp = event.currentTarget.id;
	Zeile = "";
	while (temp.substr(-1) != "_"){
		Zeile += temp.substr(-1);
		temp = temp.substr(0,temp.length - 1);
	}
	temp = temp.substr(0,temp.length - 1);
	Zeile = parseInt(Zeile) + 1;
	//neue Zeile einfügen?
	if (Zeile == Kriterienzeilen) {
		Kriterienzeilen +=1;
		erneuern = 1;
	}
	//neue Spalte einfügen?
	Spalte = parseInt(temp.substr(9));
	if (Spalte == Spalten -1) {
		Spalten +=1;
		erneuern = 1;
	}
	if (erneuern == 1) {
		Auswahltabelle_bauen(0);
	}
}

function Verbindung_entfernen(Element) {
	for (i = 0; i < Verb.length; i++) {
		if ("div_" + Verb[i].id == Element) {Verb.splice(i,1);}
	}
	loeschen = document.getElementById(Element);
	loeschen.parentElement.removeChild(loeschen);
}

function Verbindung_Dialog_oeffnen(Element) {
	try {Verbindungseinstellung.close();} catch (err) {}
	Auswahl = document.getElementById(Element);
	for (i = 0; i < Verb.length; i++) {
		if (Verb[i].id == Element.substr(4)) {Verb_Objekt = Verb[i];}
	}
	Inhalt = "<div style = 'position: relative; top: 20px; left: 20px; width: 470px;'><table class='Text_einfach' style='background: #FCEDD9;' cellpadding = '3'>\n";
	Inhalt += "<tr class='Tabellenzeile'><td width='30px' align='center'><input type='radio' id='inner_Join' value='inner Join'";
	if (Verb_Objekt.Verbindung == "beide" || Verb_Objekt.Verbindung == null) {Inhalt += " checked";}
	Inhalt += " onchange='Verb_Einst_wechseln(1);'></td><td>" + T_Text[29] + Verb_Objekt.Startfeld + T_Text[30] + Verb_Objekt.Zielfeld + T_Text[31] + "</td></tr>\n";
	Inhalt += "<tr class='Tabellenzeile'><td width='30px' align='center'><input type='radio' id='left_Join' value='left Join'";
	if (Verb_Objekt.Verbindung == "rechts") {Inhalt += " checked";}
	Inhalt += " onchange='Verb_Einst_wechseln(3);'></td><td>" + T_Text[32] + Verb_Objekt.Starttabelle + T_Text[33] + Verb_Objekt.Zieltabelle + T_Text[34] + Verb_Objekt.Zielfeld + T_Text[35] + Verb_Objekt.Startfeld + T_Text[36] + "</td></tr>\n";	Inhalt += "<tr class='Tabellenzeile'><td width='30px' align='center'><input type='radio' id='right_Join' value='right Join'";
	if (Verb_Objekt.Verbindung == "links") {Inhalt += " checked";}
	Inhalt += " onchange='Verb_Einst_wechseln(2);'></td><td>" + T_Text[37] + Verb_Objekt.Zieltabelle + T_Text[38] + Verb_Objekt.Starttabelle + T_Text[39] + Verb_Objekt.Startfeld + T_Text[35] + Verb_Objekt.Zielfeld + T_Text[36] + "</td></tr>\n";
	Inhalt += "</table></div";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Verbindungseinstellung',
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '500 230',
		headerTitle: T_Text[40],
		content: Inhalt,
	});
}

function Verb_Einst_wechseln(Option) {
	try {
		document.getElementById("svg_" + Verb_Objekt.id).removeChild(document.getElementById("Pfeil1_" + Verb_Objekt.id));
		document.getElementById("svg_" + Verb_Objekt.id).removeChild(document.getElementById("Pfeil2_" + Verb_Objekt.id));
	} catch (err) {}
	if (Option == 1) {
		Verb_Objekt.Verbindung = "beide";
		document.getElementById("inner_Join").checked = true;
		document.getElementById("left_Join").checked = false;
		document.getElementById("right_Join").checked = false;
	}
	if (Option == 2) {
		Verb_Objekt.Verbindung = "links";
		document.getElementById("inner_Join").checked = false;
		document.getElementById("left_Join").checked = true;
		document.getElementById("right_Join").checked = false;
		var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
		newlinie.setAttribute('id','Pfeil1_' + Verb_Objekt.id);
		newlinie.setAttribute("stroke", "rgb(255,0,0)");
		newlinie.setAttribute('stroke-width',"2");
		document.getElementById("svg_" + Verb_Objekt.id).appendChild(newlinie);
		var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
		newlinie.setAttribute('id','Pfeil2_' + Verb_Objekt.id);
		newlinie.setAttribute("stroke", "rgb(255,0,0)");
		newlinie.setAttribute('stroke-width',"2");
		document.getElementById("svg_" + Verb_Objekt.id).appendChild(newlinie);
	}
	if (Option == 3) {
		Verb_Objekt.Verbindung = "rechts";
		document.getElementById("inner_Join").checked = false;
		document.getElementById("left_Join").checked = false;
		document.getElementById("right_Join").checked = true;
		var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
		newlinie.setAttribute('id','Pfeil1_' + Verb_Objekt.id);
		newlinie.setAttribute("stroke", "rgb(255,0,0)");
		newlinie.setAttribute('stroke-width',"2");
		document.getElementById("svg_" + Verb_Objekt.id).appendChild(newlinie);
		var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
		newlinie.setAttribute('id','Pfeil2_' + Verb_Objekt.id);
		newlinie.setAttribute("stroke", "rgb(255,0,0)");
		newlinie.setAttribute('stroke-width',"2");
		document.getElementById("svg_" + Verb_Objekt.id).appendChild(newlinie);
	}
	neu_verbinden(Verb_Objekt.Starttabelle);
	try {Verbindungseinstellung.close();} catch (err) {}
}

//function Verbindungstyp_uebernehmen() {
//	ausgewaehlt = document.getElementById("Verbindungsart").value;
//	alert(ausgewaehlt);
//	try {Verbindungseinstellung.close();} catch (err) {}
//}

var Fenster_Groesse = function (event) {
 	Tabelle = event.detail;
	Tabelle = Tabelle.substr(0, Tabelle.length - 8);
	neu_verbinden(Tabelle);
	Breite = (parseInt(document.getElementById(event.detail).style.width) - 10).toString() + "px";
	Inhalt = document.getElementById(event.detail).content.firstChild.firstChild;
	for (i = 0; i < Inhalt.childNodes.length; i++) {
		Inhalt.childNodes[i].firstChild.firstChild.style.width = Breite;
	}
}

var Fenster_bewegt = function (event) {
	Tabelle = event.detail;
	Tabelle = Tabelle.substr(0, Tabelle.length - 8);
	Fenster = document.getElementById(event.detail)
	if (parseInt(Fenster.style.top) < 0) {Fenster.style.top = "0px";}
	if (window.innerWidth < parseInt(Fenster.style.left) + parseInt(Fenster.style.width)) {Fenster.style.left = (window.innerWidth - parseInt(Fenster.style.width)).toString() + "px";}
	if (parseInt(Fenster.style.left) < 0) {Fenster.style.left = "0px";}
	if (window.innerHeight < parseInt(Fenster.style.top) + parseInt(Fenster.content.style.height) - 34) {Fenster.style.top = (window.innerHeight - parseInt(Fenster.content.style.height) - 34).toString() + "px";}
	neu_verbinden(Tabelle);
}

//Handler für Panel
document.addEventListener('jspaneldragstop', Fenster_bewegt, false);
document.addEventListener('jspanelresizestop', Fenster_Groesse, false);

function neu_verbinden(Tabelle) {
	for (i = 0; i < Verb.length; i++) {
		if (Verb[i].Starttabelle == Tabelle || Verb[i].Zieltabelle == Tabelle) {
			Verb_Koordinaten(i);
			id = Verb[i].Starttabelle + "@@@" + Verb[i].Startfeld + "µµµ" + Verb[i].Zieltabelle + "@@@" + Verb[i].Zielfeld;
			div = document.getElementById("div_" + id);
			div.style.left = Verb[i].links + "px";
			div.style.top = (Verb[i].oben - 10).toString() + "px";
			div.style.height = Verb[i].Hoehe + "px";
			div.style.width = Verb[i].Breite + "px";
			svg = document.getElementById("svg_" + id);
			svg.setAttribute("viewBox","0 0 " + Verb[i].Breite + " " + Verb[i].Hoehe);
			if (Verb[i].x1 < Verb[i].x2) {
				Zuschlag = 20;
			} else {
				Zuschlag = -20;
			}
			linie = document.getElementById("linie1_" + id);
			linie.setAttribute('x1',Verb[i].x1);
			linie.setAttribute('y1',Verb[i].y1);
			linie.setAttribute('x2',(Verb[i].x1 + Zuschlag).toString());
			linie.setAttribute('y2',Verb[i].y1);
			linie = document.getElementById("linie2_" + id);
			linie.setAttribute('x1',(Verb[i].x2 - Zuschlag).toString());
			linie.setAttribute('y1',Verb[i].y2);
			linie.setAttribute('x2',Verb[i].x2);
			linie.setAttribute('y2',Verb[i].y2);
			linie = document.getElementById("linie3_" + id);
			y = (Verb[i].y2 - Verb[i].y1) / 2 + 5;
			if (y < 0) {y = y * -1;}
			x = (Verb[i].x2 - Verb[i].x1) / 2;
			rechts = 0;
			if (x < 0) {
				x = x * -1;
				rechts = 1;
			}
			x = x - 10;
			linie.setAttribute('x1',x);
			linie.setAttribute('y1',y);
			linie.setAttribute('x2',x + 20);
			linie.setAttribute('y2',y);
			linie = document.getElementById("linie4_" + id);
			if (rechts == 0) {
				linie.setAttribute('x1',x);
				linie.setAttribute('x2',(Verb[i].x1 + Zuschlag).toString());
				linie.setAttribute('y2',Verb[i].y1);
				linie.setAttribute('y1',y);
			} else {
				linie.setAttribute('x1',(Verb[i].x1 + Zuschlag).toString());
				linie.setAttribute('x2',x + 20);
				linie.setAttribute('y1',Verb[i].y1);
				linie.setAttribute('y2',y);
			}
			linie = document.getElementById("linie5_" + id);
			if (rechts == 0) {
				linie.setAttribute('x1',(Verb[i].x2 - Zuschlag).toString());
				linie.setAttribute('x2',x + 20);
				linie.setAttribute('y2',y);
				linie.setAttribute('y1',Verb[i].y2);
			} else {
				linie.setAttribute('x2',(Verb[i].x2 - Zuschlag).toString());
				linie.setAttribute('x1',x);
				linie.setAttribute('y1',y);
				linie.setAttribute('y2',Verb[i].y2);
			}
			try {
				if (Verb[i].Verbindung == "links") {
					if (Verb[i].Start_left < Verb[i].Ziel_left) {
						linie = document.getElementById("Pfeil1_" + id);
						linie.setAttribute('x1',x);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 15);
						linie.setAttribute('y2',y + 7);
						linie = document.getElementById("Pfeil2_" + id);
						linie.setAttribute('x1',x);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 15);
						linie.setAttribute('y2',y - 7);
					} else {
						linie = document.getElementById("Pfeil1_" + id);
						linie.setAttribute('x1',x + 20);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 10);
						linie.setAttribute('y2',y + 7);
						linie = document.getElementById("Pfeil2_" + id);
						linie.setAttribute('x1',x + 20);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 10);
						linie.setAttribute('y2',y - 7);
						}
					}
				if (Verb[i].Verbindung == "rechts") {
					if (Verb[i].Start_left > Verb[i].Ziel_left) {
						linie = document.getElementById("Pfeil1_" + id);
						linie.setAttribute('x1',x);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 15);
						linie.setAttribute('y2',y + 7);
						linie = document.getElementById("Pfeil2_" + id);
						linie.setAttribute('x1',x);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 15);
						linie.setAttribute('y2',y - 7);
					} else {
						linie = document.getElementById("Pfeil1_" + id);
						linie.setAttribute('x1',x + 20);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 10);
						linie.setAttribute('y2',y + 7);
						linie = document.getElementById("Pfeil2_" + id);
						linie.setAttribute('x1',x + 20);
						linie.setAttribute('y1',y);
						linie.setAttribute('x2',x + 10);
						linie.setAttribute('y2',y - 7);
					}
				}
			} catch (err) {}
		}
	}
}

function Tabelle_anzeigen() {
	if (document.getElementById("tabellenliste").value != "") {
		i = Tab.length;
		Tab[i] = [];
		Tab[i].Tabelle = document.getElementById("tabellenliste").value;
		jQuery.ajax({
			url: "./Tabellenfelder_einlesen.php?Tabelle=" + Tab[i].Tabelle + "&Datenbank=" + document.getElementById("datenbank").value,
			success: function (html) {
	   		strReturn = html;
			},
  			async: false
	  	});
  		var Inhalt = "<table>";
	  	var oben = -15;
  		Tab[i].Felder = strReturn.split("@@@");
  		for (x = 0; x < Tab[i].Felder.length; x++) {
	  		Feld = JSON.parse(Tab[i].Felder[x]);
  			Tab[i].Felder[x] = Feld;
			oben = oben + 20;
			Inhalt += "<tr><td><div id=\"" + Tab[i].Tabelle + "@@@" + Feld["Field"] + "\" style=\"border: 1px black dotted; position: absolute; top: " + oben.toString() + "px; left:5px; height:16px; width:120px; touch-action: none; cursor: move;\" draggable=\"true\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\">" + Feld["Field"] + "</div></td></tr>";
		}
		Inhalt += "</table>";
		if (links > window.innerWidth - 130) {
			links = 10;
			Foben = Foben + 200;
		}
		if (window.innerHeight - 403 - Foben < oben) {oben = window.innerHeight - 403 - Foben - 55;}
		try {
			x = document.getElementById(Tab[i].Tabelle + '_Fenster').childNodes.length;
		} catch (err) {
			jsPanel.create({
				id: Tab[i].Tabelle + '_Fenster',
				theme: 'info',
				contentSize: '130 ' + (oben + 25).toString(),
				headerTitle: Tab[i].Tabelle,
				headerControls: 'closeonly xs',
				position: 'left-top ' + links.toString() + ' ' + Foben.toString(),
				zIndex: 0,
				content: Inhalt
			});
			links = links + 180;
		}
	}
}

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
	ev.preventDefault();
	i = Verb.length;
	var data = ev.dataTransfer.getData("text");
	Verb[i] = [];
	Verb[i].Starttabelle = data.split("@@@")[0];
	Verb[i].Startfeld = data.split("@@@")[1];
	Verb[i].Zieltabelle = ev.target.id.split("@@@")[0];
	Verb[i].Zielfeld = ev.target.id.split("@@@")[1];
	Verb[i].Verbindung = "beide";
	Verb_Koordinaten(i);
	Verb_erstellen(i);
}

function Verb_erstellen(i) {
	var id = Verb[i].Starttabelle + "@@@" + Verb[i].Startfeld + "µµµ" + Verb[i].Zieltabelle + "@@@" + Verb[i].Zielfeld;
	Verb[i].id = id;
	var newdiv = document.createElement("div");
	newdiv.className = 'context-menu-one';
	newdiv.style.position = "absolute";
	newdiv.style.left = Verb[i].links + "px";
	newdiv.style.top = (Verb[i].oben - 10).toString() + "px";
	newdiv.style.height = Verb[i].Hoehe + "px";
	newdiv.style.width = Verb[i].Breite + "px";
	newdiv.style.zIndex = 0;
	newdiv.id = "div_" + id;
	document.body.appendChild(newdiv);
	var newsvg = document.createElementNS('http://www.w3.org/2000/svg','svg');
	newsvg.setAttribute('id',"svg_" + id);
	newsvg.setAttribute("viewBox","0 0 " + Verb[i].Breite + " " + Verb[i].Hoehe);
	newsvg.setAttribute('xmlns',"http://www.w3.org/2000/svg");
	newsvg.setAttribute('xmlns:xlink',"http://www.w3.org/1999/xlink");
	document.getElementById("div_" + id).appendChild(newsvg);
	var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
	newlinie.setAttribute('id','linie1_' + id);
	newlinie.setAttribute("stroke", "rgb(255,0,0)");
	newlinie.setAttribute('stroke-width',"1");
	document.getElementById("svg_" + id).appendChild(newlinie);
	var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
	newlinie.setAttribute('id','linie2_' + id);
	newlinie.setAttribute("stroke", "rgb(255,0,0)");
	newlinie.setAttribute('stroke-width',"1");
	document.getElementById("svg_" + id).appendChild(newlinie);
	var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
	newlinie.setAttribute('id','linie3_' + id);
	newlinie.setAttribute("stroke", "rgb(255,0,0)");
	newlinie.setAttribute('stroke-width',"1");
	document.getElementById("svg_" + id).appendChild(newlinie);
	var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
	newlinie.setAttribute('id','linie4_' + id);
	newlinie.setAttribute("stroke", "rgb(255,0,0)");
	newlinie.setAttribute('stroke-width',"1");
	document.getElementById("svg_" + id).appendChild(newlinie);
	var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
	newlinie.setAttribute('id','linie5_' + id);
	newlinie.setAttribute("stroke", "rgb(255,0,0)");
	newlinie.setAttribute('stroke-width',"1");
	document.getElementById("svg_" + id).appendChild(newlinie);
	neu_verbinden(Verb[i].Starttabelle);
}

function auswahl_drop(ev) {
	ev.preventDefault();
	data = ev.dataTransfer.getData("text");
	Feld = data.split("@@@")[1];
	document.getElementById(ev.target.id).value = Feld;
	Spalte = ev.target.id.substr(4);
	Tabelle = data.split("@@@")[0];
	document.getElementById('tabelle' + Spalte).value = Tabelle;
	auswahl_Tab_change(Spalte);
	document.getElementById('anzeigen' + Spalte).checked = true;
}

function auswahl_Tab_change(Spalte) {
	durchgelaufen = 0;
	Tabellenname = document.getElementById("tabelle" + Spalte).value;
	Feldname = document.getElementById("feld" + Spalte).value;
	Inhalt = "<select class='Auswahl_Liste_Element' id='feld" + Spalte + "' style='width: 100%;' size='1' ondrop='auswahl_drop(event)' ondragover='allowDrop(event)'>"
	Inhalt += "<option></option>";
	Tabelle = -1;
	for (i = 0; i < Tab.length; i++) {
		if (Tab[i].Tabelle == Tabellenname) {Tabelle = i;}
	}
	if (Tabelle == -1) {
		document.getElementById("tabellenliste").value = Tabellenname;
		Tabelle_anzeigen();
		auswahl_Tab_change(Spalte);
	}
	for (i = 0; i < Tab[Tabelle].Felder.length; i++) {
		if (Tab[Tabelle].Felder[i].Field == Feldname) {
			Inhalt += "<option selected>" + Tab[Tabelle].Felder[i].Field + "</option>";
		} else {
			Inhalt += "<option>" + Tab[Tabelle].Felder[i].Field + "</option>";
		}
	}
	Inhalt += "</select>\n";
	if (durchgelaufen == 0) {document.getElementById("feld" + Spalte).parentElement.innerHTML = Inhalt;}
	durchgelaufen = 1;			
}

function Verb_Koordinaten(i) {
	Verb[i].Ziel_Breite = parseInt(document.getElementById(Verb[i].Zieltabelle + "_Fenster").style.width);
	Verb[i].Start_Breite = parseInt(document.getElementById(Verb[i].Starttabelle + "_Fenster").style.width);
	Ziel_oben = parseInt(document.getElementById(Verb[i].Zieltabelle + "_Fenster").style.top);
	if (isNaN(Ziel_oben)) {
		Ziel_oben = parseInt(document.getElementById(Verb[i].Zieltabelle + "_Fenster").style.top.substr(5));
	}
	Ziel_links = parseInt(document.getElementById(Verb[i].Zieltabelle + "_Fenster").style.left);
	if (isNaN(Ziel_links)) {
		Ziel_links = parseInt(document.getElementById(Verb[i].Zieltabelle + "_Fenster").style.left.substr(5));
	}
	Start_oben = parseInt(document.getElementById(Verb[i].Starttabelle + "_Fenster").style.top);
	if (isNaN(Start_oben)) {
		Start_oben = parseInt(document.getElementById(Verb[i].Starttabelle + "_Fenster").style.top.substr(5));
	}
	Start_links = parseInt(document.getElementById(Verb[i].Starttabelle + "_Fenster").style.left);
	if (isNaN(Start_links)) {
		Start_links = parseInt(document.getElementById(Verb[i].Starttabelle + "_Fenster").style.left.substr(5));
	}
	Verb[i].Ziel_top = Ziel_oben + parseInt(document.getElementById(Verb[i].Zieltabelle + "@@@" + Verb[i].Zielfeld).style.top) + 40;
	Verb[i].Ziel_left = Ziel_links + parseInt(document.getElementById(Verb[i].Zieltabelle + "@@@" + Verb[i].Zielfeld).style.left);
	Verb[i].Start_top = Start_oben + parseInt(document.getElementById(Verb[i].Starttabelle + "@@@" + Verb[i].Startfeld).style.top) + 40;
	Verb[i].Start_left = Start_links + parseInt(document.getElementById(Verb[i].Starttabelle + "@@@" + Verb[i].Startfeld).style.left);
	Tab1 = Verb[i].Start_left;
	Tab2 = Verb[i].Ziel_left;
	if (Tab1 > Tab2) {
		Tab3 = Tab1;
		Tab1 = Tab2 + Verb[i].Ziel_Breite;
		Tab2 = Tab3;
	} else {
		Tab1 = Tab1 + Verb[i].Start_Breite;
	}
	Verb[i].Breite = Tab2 - Tab1;
	Verb[i].Hoehe = Verb[i].Ziel_top - Verb[i].Start_top + 15;
	if (Verb[i].Ziel_top - Verb[i].Start_top < 0) {Verb[i].Hoehe = Verb[i].Start_top - Verb[i].Ziel_top + 15;}
	if (Verb[i].Ziel_top < Verb[i].Start_top) {
		Verb[i].oben = Verb[i].Ziel_top;
	} else {
		Verb[i].oben = Verb[i].Start_top;
	}
	Verb[i].links = Tab1;
	if (Verb[i].Ziel_top > Verb[i].Start_top) {
		Verb[i].y1 = 5;
		Verb[i].y2 = Verb[i].Ziel_top - Verb[i].Start_top + 5;
	} else {
		Verb[i].y1 = Verb[i].Start_top - Verb[i].Ziel_top + 5;
		Verb[i].y2 = 5;
	}
	if (Verb[i].Ziel_left > Verb[i].Start_left) {
		Verb[i].x1 = 0;
		Verb[i].x2 = Verb[i].Breite;
	} else {
		Verb[i].x1 = Verb[i].Breite;
		Verb[i].x2 = 0;
	}
}

function SQL_einlesen() {
	//allgemeine Dinge am Anfang
	leeren();
	SQL = document.getElementById("SQL").value;
	if (SQL == "") {return;}
	//SQL = "SELECT `Point_ID`, `Path` FROM `Tagtable` WHERE (((`Point_ID` >100)) AND ((`Path` like '/')) OR (`Point_ID` >200 AND `Path` like '/Ober%') OR (`Point_ID` <400 AND `Path` like '/Ober%'));"
	SQL = HTML_decode(SQL);
	//SQL von Zeilenumbrüchen befreien und dafür sorgen, daß hinter jedem Komma ein Leerzeichen ist
	SQL1 = "";
	for (i = 0; i < SQL.length; i++) {
		if (SQL.charCodeAt(i) != 10 && SQL.charCodeAt(i) != 13) {
			if (SQL.substr(i,1) == ",") {
				SQL1 += ", ";
			} else {
				SQL1 += SQL.substr(i,1);
			}
		} else {
			SQL1 += " ";
		}
	}
	SQL = SQL1;
	while (SQL.indexOf("  ") > -1){
		SQL = SQL.replace("  "," ");
	}
	//Abstand vor Klammern
	SQL = SQL.replace(/\`\(/g,"` (");
	SQL = SQL.replace(/\"\(/g,'" (');
	SQL = SQL.replace(/\'\(/g,"' (");
	//jetzt ist der SQL Text sauber
	document.getElementById("SQL").value = SQL;
	Teile = SQL.split(" ");
	//Alle Schlüsselwörter in uppercase umformen
	for (i = 0; i < Teile.length; i++) {
		if (Teile[i].toUpperCase() == "OR" || Teile[i].toUpperCase() == "AND" || Teile[i].toUpperCase() == "INNER" || Teile[i].toUpperCase() == "ON" || Teile[i].toUpperCase() == "SELECT" || Teile[i].toUpperCase() == "UPDATE" || Teile[i].toUpperCase() == "INSERT" || Teile[i].toUpperCase() == "DELETE" || Teile[i].toUpperCase() == "FROM" || Teile[i].toUpperCase() == "AS" || Teile[i].toUpperCase() == "WHERE" || Teile[i].toUpperCase() == "GROUP" || Teile[i].toUpperCase() == "ORDER" || Teile[i].toUpperCase() == "BY" || Teile[i].toUpperCase() == "LIMIT" || Teile[i].toUpperCase() == "ASC" || Teile[i].toUpperCase() == "DESC" || Teile[i].toUpperCase() == "RIGHT" || Teile[i].toUpperCase() == "LEFT" || Teile[i].toUpperCase() == "JOIN") {
			Teile[i] = Teile[i].toUpperCase();
		}
	}
	Felder = new Array;
	Felder = [];
	Tabellen = new Array;
	Tabellen = [];
	Spalte = 0;
	//DISTINCT verarbeiten
	document.getElementById("duplikate").removeAttribute("checked");
	TeilePos = 0;
	fertig = 0;
	while (TeilePos < Teile.length && fertig == 0) {
		if (Teile[TeilePos] == "DISTINCT") {
			document.getElementById("duplikate").setAttribute("checked","");
			Teile.splice(TeilePos,1);
			fertig = 1;
		}
		TeilePos += 1;
	}
	TeilePos = 1;
	if (Teile[0] == "SELECT" || Teile[0] == "UPDATE" || Teile[0] == "INSERT" || Teile[0] == "DELETE") {document.getElementById("abfragetyp").value = Teile[0];}
	Auswahltabelle_bauen(1);
	if (Teile[0] == "SELECT") {
		//Felder einlesen die angezeigt werden
		Felder = new Array;
		Felder = [];
		Tabellen = new Array;
		Tabellen = [];
		Spalte = 0;
		while (TeilePos < Teile.length && Teile[TeilePos] != "FROM"){
			Felder[Spalte] = new Array;
			Felder[Spalte] = [];
			Tabellen[Spalte] = "";
			if (Teile[TeilePos].indexOf(".") > -1 && Teile[TeilePos].indexOf("(") == -1) {
				Tab_Feld = Tab_Feld_ermitteln(Teile[TeilePos])
				Felder[Spalte]["Feld"] = Tab_Feld["Feld"];
				Tabellen[Spalte] = Tab_Feld["Tabelle"];
			} else {
				//Ist es keine Funktion?
				if (Teile[TeilePos].indexOf("(") == -1) {
					Tab_Feld = Tab_Feld_ermitteln(Teile[TeilePos])
					Felder[Spalte]["Feld"] = Tab_Feld["Feld"];
					Tabellen[Spalte] = Tab_Feld["Tabelle"];
				} else {
					//Es ist eine Funktion
					Funktion = "";
					Felder[Spalte]["Feld"] = "";
					while (Teile[TeilePos].indexOf(",") == -1 && Teile[TeilePos].indexOf("FROM") == -1 && Teile[TeilePos].indexOf("AS") == -1) {
						Funktion += Teile[TeilePos];
						TeilePos += 1;
					}
					if (Teile[TeilePos].indexOf(",") > -1) {
						Funktion += Teile[TeilePos];
						Teile[TeilePos] = ""; 
						TeilePos += 1;
					}
					TeilePos -= 1;
					//Komma und Leerstellen am Ende entfernen
					while (Funktion.substr(-1) == "," || Funktion.substr(-1) == " ") {
						Funktion = Funktion.substr(0, Funktion.length -1);
					}
					document.getElementById("funktion" + Spalte.toString()).value = Funktion;
				}
			}
			
			if (Tabellen[Spalte].length > 0) {
				Felder[Spalte]["Tabelle"] = Tabellen[Spalte];
			} else {
				Felder[Spalte]["Tabelle"] = "";
			}
			TeilePos += 1;
			//Haben wir hier noch einen Alias?
			if (Teile[TeilePos].indexOf("AS") > -1) {
				TeilePos += 1;
				Alias = Teile[TeilePos];
				while (Teile[TeilePos].indexOf(",") == -1 && Teile[TeilePos].indexOf("FROM") == -1){
					if (Teile[TeilePos].indexOf(",") > -1) {Alias += Teile[TeilePos];}
					TeilePos += 1;
				}
				if (Teile[TeilePos].indexOf("FROM") == -1) {TeilePos += 1;}
				while (Alias.substr(-1) == "," || Alias.substr(-1) == " ") {
					Alias = Alias.substr(0, Alias.length -1);
				}
				document.getElementById("alias" + Spalte.toString()).value = Alias;
			}
			Spalte += 1;
		}
	}
	//UPDATE?
	if (Teile[0] == "UPDATE") {
		Felder = new Array;
		Felder = [];
		Tabellen = new Array;
		Tabellen = [];
		Spalte = 0;
		while (Teile[TeilePos] != "SET") {
			Teil = Teile[TeilePos];
			Tabellen[Spalte] = "";
			while (Teil.substr(0, 1) == "`" || Teil.substr(0, 1) == "'" || Teil.substr(0, 1) == '"'){
				Teil = Teil.substr(1);
			}
			while (Teil.substr(0, 1) != "`" && Teil.substr(0, 1) != "'" && Teil.substr(0, 1) != '"' && Teil.substr(0, 1) != "," && Teil.substr(0, 1) != "." && Teil.length >0){
				Tabellen[Spalte] += Teil.substr(0, 1);
				Teil = Teil.substr(1);
			}
			TeilePos += 1;
			Spalte += 1;
		}
		TeilePos += 1;
		Spalte = 0;
		// Die Tabelle haben wir. Jetzt kommt der Teil hinter SET
		while (TeilePos < Teile.length && Teile[TeilePos] != "FROM" && Teile[TeilePos] != "WHERE" && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";"){
			Felder[Spalte] = new Array;
			Felder[Spalte] = [];
			Felder[Spalte]["Feld"] = "";
			Teil = Teile[TeilePos];
			Tab_Feld = Tab_Feld_ermitteln(Teile[TeilePos]);
			Felder[Spalte]["Feld"] = Tab_Feld ["Feld"];
			TeilePos += 1;
			Teil = "";
			while (TeilePos < Teile.length && Teile[TeilePos].substr(-1) != "," && Teile[TeilePos] != "WHERE" && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";"){
				Teil += Teile[TeilePos];
				TeilePos += 1;
			}
			while ((Teil.substr(0,1) == " " || Teil.substr(0,1) == "=") && Teil.length > 0){
				Teil = Teil.substr(1);
			}
			Felder[Spalte]["Wert"] = Teil;
			document.getElementById("tabelle" + Spalte.toString()).value = Tabellen[Spalte];
			document.getElementById("feld" + Spalte.toString()).value = Felder[Spalte]["Feld"];
			document.getElementById("schreiben" + Spalte.toString()).value = Felder[Spalte]["Wert"];
			Spalte += 1;
		}
		TeilePos += -1;
	}
		
	//INSERT?
	if (Teile[0] == "INSERT") {
		TeilePos = 2;
		Felder = new Array;
		Felder = [];
		Tabellen = new Array;
		Tabellen = [];
		Spalte = 0;
		Tabellen[Spalte] = "";
		Teil = Teile[2];
		while (Teil.substr(0, 1) == "`" || Teil.substr(0, 1) == "'" || Teil.substr(0, 1) == '"'){
			Teil = Teil.substr(1);
		}
		while (Teil.substr(0, 1) != "`" && Teil.substr(0, 1) != "'" && Teil.substr(0, 1) != '"' && Teil.substr(0, 1) != "." && Teil.length >0){
			Tabellen[Spalte] += Teil.substr(0, 1);
			Teil = Teil.substr(1);
		}
		TeilePos += 1;
		if (Teil[TeilePos] == "(") {TeilePos += 1;}
		// Die Tabelle haben wir. Jetzt kommen die Feldnamen
		while (TeilePos < Teile.length && Teile[TeilePos] != "VALUES" && Teil[TeilePos] != ")"){
			Tabellen[Spalte] = Tabellen[0];
			Teil = Teile[TeilePos];
			while ((Teil.substr(0, 1) == "(" || Teil.substr(0, 1) == "`" || Teil.substr(0, 1) == "'" || Teil.substr(0, 1) == '"') && Teil.length > 0){
				Teil = Teil.substr(1);
			}
			Felder[Spalte] = new Array;
			Felder[Spalte] = [];
			Felder[Spalte]["Wert"] = "";
			Felder[Spalte]["Feld"] = "";
			while (Teil.substr(0, 1) != "," && Teil.substr(0, 1) != "`" && Teil.substr(0, 1) != "'" && Teil.substr(0, 1) != '"' && Teil.substr(0, 1) != ")" && Teil.length >0){
				Felder[Spalte]["Feld"] += Teil.substr(0, 1);
				Teil = Teil.substr(1);
			}
			TeilePos += 1;
			Spalte += 1;
		}
		//Anfang der Werte suchen
		while (TeilePos < Teile.length && Teile[TeilePos] != "VALUES"){
			TeilePos += 1;
		}
		TeilePos += 1;
		if (Teile[TeilePos] == "(") {TeilePos += 1;}
		//Werte der Felder einlesen
		Spalte = 0;
		while (TeilePos < Teile.length && Teile[TeilePos] != "WHERE" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";" && Teil[TeilePos] != ")"){
			Teil = Teile[TeilePos];
			while (Teil.substr(0, 1) == "(" && Teil.length > 0){
				Teil = Teil.substr(1);
			}
			while (Teil.substr(0, 1) != "," && Teil.substr(0, 1) != ")" && Teil.length >0){
				Felder[Spalte]["Wert"] += Teil.substr(0, 1);
				Teil = Teil.substr(1);
			}
			while ((Teil.substr(-1) == " " || Teil.substr(-1) == "," || Teil.substr(-1) == "`" || Teil.substr(-1) == "'" || Teil.substr(-1) == '"') && Teil.length > 0){
				Teil = Teil.substr(0,Teil.length - 1);
			}
			Spalte += 1;
			TeilePos += 1;
		}
	}
	//FROM Teil
	//Tabellen einlesen
	TeilePos += 1;
	From_Text_Start = TeilePos;
	From_Text_Start_orig = TeilePos;
	while (TeilePos < Teile.length && Teile[TeilePos] != "WHERE" && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";"){
		Tabelle = Teile[TeilePos];
		Tabelle = Tabelle.replace(",","");
		//Prüfung auf einen JOIN
		if (Tabelle.substr(0,1) == "`" && Tabelle.indexOf("`.`") == -1) {
			From_Text_Start = From_Text_Start + 1;
			for (x = 0; x < 2; x++) {
				Tabelle = Tabelle.replace("`","");
			}
			Tabelle = Tabelle.replace(";","");
			Treffer = 0;
			for (x = 0; x < Tabellen.length; x++) {
				if (Tabellen[x] == Tabelle) {Treffer = 1;}
			}
			if (Treffer == 0) {
				Tabellen[Tabellen.length] = Tabelle;
			}
		}
		if (Teile[TeilePos] == "JOIN") {From_Text_Start = From_Text_Start - 1;}
		TeilePos += 1;
	}
	if (From_Text_Start_orig < From_Text_Start) {From_Text_Start = From_Text_Start - 1;}
	From_Text_Ende = TeilePos - 1;
	//leere Einträge aus dem Tabellenarray entfernen
	tempTabellen = Tabellen;
	for (x = 0; x < tempTabellen.length; x++) {
		if (tempTabellen[x] == "") {
			tempTabellen.splice(x,1);
			x = -1;
		}
	}
	//mehrfache Einträge aus dem Tabellenarray entfernen
	abgearbeitet = 0;
	while (abgearbeitet < tempTabellen.length){
		for (x = abgearbeitet + 1; x < tempTabellen.length; x++) {
			if (tempTabellen[x] == tempTabellen[abgearbeitet]) {
				tempTabellen.splice(x,1);
			}
		}
		abgearbeitet += 1;
	}
	//Tabellen sichtbar machen
	for (Tab_Zaehler = 0; Tab_Zaehler < tempTabellen.length; Tab_Zaehler++) {
		document.getElementById("tabellenliste").value = tempTabellen[Tab_Zaehler];
		Tabelle_anzeigen();
	}	
	//JOINS verarbeiten
	Pos = From_Text_Start;
	while (Teile[Pos] != "JOIN" && Pos <= From_Text_Ende) {
		Pos += 1;
	}
	while (Pos < From_Text_Ende) {
		Pos = From_Text_Start + 1;
		i = Verb.length;
		Verb[i] = [];
		if (Teile[Pos] == "LEFT") {Verb[i].Verbindung = "links";}
		if (Teile[Pos] == "RIGHT") {Verb[i].Verbindung = "rechts";}
		if (Teile[Pos] == "INNER") {Verb[i].Verbindung = "beide";}
		Verb_Objekt = Verb[i];
		Pos += 4;
		Teile[Pos] = Teile[Pos].substr(1);
		Tab_Feld = Tab_Feld_ermitteln(Teile[Pos]);
		Verb[i].Startfeld = Tab_Feld["Feld"];
		Verb[i].Starttabelle = Tab_Feld["Tabelle"];
		Pos += 2;
		Tab_Feld = Tab_Feld_ermitteln(Teile[Pos]);
		Verb[i].Zielfeld = Tab_Feld["Feld"];
		Verb[i].Zieltabelle = Tab_Feld["Tabelle"];
		Verb_Koordinaten(i);
		Verb_erstellen(i);
		//Pfeile erstellen
		for (x = 1; x < 3; x++) {
			var newlinie = document.createElementNS('http://www.w3.org/2000/svg','line');
			newlinie.setAttribute('id','Pfeil' + x.toString() + "_" + Verb_Objekt.id);
			newlinie.setAttribute("stroke", "rgb(255,0,0)");
			newlinie.setAttribute('stroke-width',"2");
			document.getElementById("svg_" + Verb_Objekt.id).appendChild(newlinie);
		}
		neu_verbinden(Verb_Objekt.Starttabelle);
		From_Text_Start = Pos;
	}
	//sichtbare Felder anzeigen
	Spalte = 0;
	for (Feld = 0; Feld < Felder.length; Feld++) {
		if (Felder[Feld]["Tabelle"] == "") {Felder[Feld]["Tabelle"] = Tabelle_zum_Feld(Felder[Feld]["Feld"]);}
		document.getElementById("tabelle" + Spalte.toString()).value = Felder[Feld]["Tabelle"];
		document.getElementById("feld" + Spalte.toString()).value = Felder[Feld]["Feld"];
		document.getElementById("anzeigen" + Spalte.toString()).setAttribute("checked","");
		if (Teile[0] == "UPDATE" || Teile[0] == "INSERT") {document.getElementById("schreiben" + Spalte.toString()).value = Felder[Spalte]["Wert"];}
		Spalte += 1;
	}
	//Bedingungen einlesen
	if (TeilePos < Teile.length && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";"){TeilePos += 1;}
	Bedingungen = "";
	while (TeilePos < Teile.length && Teile[TeilePos] != "GROUP" && Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos] != ";"){
		Bedingungen += Teile[TeilePos] + " ";
		TeilePos += 1;
	}
	Anfang = 0;
	while (Bedingungen.length != Anfang && Bedingungen.length > 0){
		Anfang = Bedingungen.length;
		Bedingungen = Bed_zerlegen(Bedingungen);
	}
	//Den Text OR in Ebenen aufteilen
	if (Bedingungen.length > 0) {
		if (Bedingungen.indexOf("(") > -1) {
			tempBedingungen = Bedingungen.split(") OR (");
		}else {
			tempBedingungen = Bedingungen.split(" OR ");
		}
		for (Zeile = 0; Zeile < tempBedingungen.length; Zeile++) {
			Klammern_auf = 0;
			for (Zeichen = 0; Zeichen < tempBedingungen[Zeile].length; Zeichen++) {
				if (tempBedingungen[Zeile].substr(Zeichen,1) == "(") {Klammern_auf += 1;}
				if (tempBedingungen[Zeile].substr(Zeichen,1) == ")") {Klammern_auf += -1;}
			}
			if (Klammern_auf != 0) {
				if (tempBedingungen[Zeile].substr(0,1) != "(" && tempBedingungen[Zeile].substr(-1) == ")") {tempBedingungen[Zeile] = "(" + tempBedingungen[Zeile];}
				if (tempBedingungen[Zeile].substr(-1) != ")" && tempBedingungen[Zeile].substr(0,1) == "(") {tempBedingungen[Zeile] = tempBedingungen[Zeile] + ")";}
			}
			while (tempBedingungen[Zeile].indexOf("( ") > -1) {tempBedingungen[Zeile] = tempBedingungen[Zeile].replace(/\( /g,"(");}
			while (tempBedingungen[Zeile].indexOf(" )") > -1) {tempBedingungen[Zeile] = tempBedingungen[Zeile].replace(/ \)/g,")");}
			tempBedingungen[Zeile] = Bed_zerlegen(tempBedingungen[Zeile]);
			BTeile = BTeile_erzeugen(tempBedingungen[Zeile]);
			//Test auf Klammern
			i = 0;
			erledigt = 0;
			x = -1;
			while (x < BTeile.length - 1) {
				x += 1;
				if (BTeile[x].indexOf("(") > -1) {
					erledigt = 1;
					if (x > 0) {i += 1;}
				}
			}
			if (erledigt == 0) {i = -1;}
			//Wennn es keine Klammer in den Bedingungen gibt, dann ist i = -1, ansonsten ist BTeile[i] die tiefste Klammer
			//Wenn i = 0 ist, dann befindet sich die Klammer am Anfang, ansonsten schauen wir was sich vor der Klammer befindet
			if (i > -1) {
				Anfang = -1;
				x = i;
				Klammer = 0;
				Start = -1;
				Ende = -1;
				Klammer1 = 0;
				Klammer1_Start = -1;
				Klammer1_Ende = -1;
				Klammer2 = 0;
				Klammer2_Start = -1;
				Klammer2_Ende = -1;
				if (i > 0) {
					if (BTeile[x - 1] != "(") {
						x += -1;
						if (BTeile[x - 1] == ")") {
							Klammer1 = 1;
							Klammer1_Ende = x;
							x += -1;
							while (BTeile[x] != "("){
								x += -1;
							}
							Klammer1_Start = x;
							Start = Klammer1_Start + 1;
						}
						Anfang = x - 1;
					} else {
						Anfang = x - 2;
					}
				} else {
					Start = 0;
					Anfang = 0;
					Klammer1 = 1;
					Klammer1_Start = 0;
					x = 1;
					while (BTeile[x] != ")"){
						x += 1;
					}
					Klammer1_Ende = x;
					Ende = x + 2;
				}
				Runden = 0;
				while (Runden < i) {
					while (x < BTeile.length && BTeile[x] != "(") {
						x += 1;
					}
					Runden += 1;
					x += 1;
				}
				if (i > 0) {x += -1;}
				i += -1;
				if (x < BTeile.length) {
					Klammer2_Start = x
					while (BTeile[x] != ")") {
						x += 1;
					}
					Klammer2_Ende = x;
					Ende = x -1;
				}
				if (Klammer1_Start == -1) {
					if (BTeile[Klammer2_Ende + 1] == ")" || BTeile[Klammer2_Ende + 1] == "(") {
						Ende = Klammer2_Ende;
					} else {
						Ende = Klammer2_Ende + 2;
					}
				}
				if (Start < Klammer2_Start) {
					//Es steht keine weitere Klammer vor dem geklammerten Ausdruck
					//Falls keine Bedingung vor der Klammer steht, dann nehmen wir die Bedingung nach der Klammer, ansonsten die davor
					if (Anfang == 0) {
						Klammer1 = 1;
						Klammer1_Start = Klammer2_Start - 1;
						while (BTeile[Klammer1_Start] !="("){
							Klammer1_Start += -1;
						}
						Start = Klammer1_Start + 1;
						x = Anfang;
						while (BTeile[x] != ")" && x < BTeile.length) {
							x += 1;
						}
						Klammer1_Ende = x;
						//Kommt nach der Klammer eine weitere Klammer,nur eine Bedingung, oder ist es das Ende aller Bedingungen?
						if (x + 2 < BTeile.length) {
							if (BTeile[x + 2] == "(") {
								Anfang = x + 3;
								Klammer2 = 1;
								Klammer2_Start = Anfang - 1;
								x = Anfang;
								while (BTeile[x] != ")" && x < BTeile.length) {
									x += 1;
								}
								Klammer2_Ende = x;
								Ende = Klammer2_Ende - 1;
							} else {
								//Es kommt nur noch eine Bedingung
								Ende = x + 2;
							}
						} else {
							//Es ist das Ende aller Bedingungen
							Ende = x;
						}
					} else {
						//Es steht eine Bedingung vor der Klammer
						Start = Klammer2_Start - 2;
					}
				}
				//Klammer(n) auflösen
				temp_Bedingung = "";
				//Fall 1: Bedingung + Klammer
				if (Klammer1_Start == -1) {
					x = Start + 3;
					while (x < Klammer2_Ende) {
						temp_Bedingung += BTeile[Start] + " " + BTeile[Start + 1] + " " + BTeile[x] + " ";
						x += 1;
						if (BTeile[x] == "AND" || BTeile[x] == "OR") {
							temp_Bedingung += BTeile[x] + " ";
							x += 1;
						}
					}
				} else {
					//Fall 2: Klammer + Bedingung
					if (Klammer1 > 0 && Klammer2 == 0) {
						x = Klammer1_Start + 1;
						while (x < Klammer1_Ende) {
							if (BTeile[Ende].substr(-1) == ";") {BTeile[Ende] = BTeile[Ende].substr(0, BTeile[Ende].length - 1);}
							temp_Bedingung += BTeile[x] + " " + BTeile[Ende - 1] + " " + BTeile[Ende] + " ";
							x += 1;
							if (BTeile[x] == "AND" || BTeile[x] == "OR") {
								temp_Bedingung += BTeile[x] + " ";
								x += 1;
							}
						}
					} else {
						//Fall 3: Klammer + Klammer
						Start = Klammer1_Start + 1;
						x = Start;
						Verkn = BTeile[Klammer1_Ende + 1];
						while (x < Klammer1_Ende) {
							i = Klammer2_Start + 1;
							if (BTeile[x] == "AND" || BTeile[x] == "OR") {
								temp_Bedingung += BTeile[x] + " ";
								x += 1;
							}
							while (i < Klammer2_Ende) {
								if (BTeile[x] == "AND" || BTeile[x] == "OR") {
									temp_Bedingung += BTeile[x];
								} else {
									temp_Bedingung += BTeile[x] + " " + Verkn + " " + BTeile[i] + " ";
								}
								i += 1;
								if (BTeile[i] == "AND" || BTeile[i] == "OR") {
									temp_Bedingung += BTeile[i] + " ";
									i += 1;
								}
							}
							x += 1;
						}
					}
				}
				//temp_Bedingungen in die ursprünglichen Bedingungen einbauen und die Klammern entfernen
				i = 0;
				temp_Bedingung2 = "";
				while (i < Klammer1_Start) {
					temp_Bedingung2 += BTeile[i] + " ";
					i += 1;
				}
				temp_Bedingung2 += " " + temp_Bedingung + " ";
				if (BTeile[Ende + 1] != ")" && BTeile[Ende + 2] == ")") {Ende += 1;}
				for (i = Ende - 1; i < BTeile.length; i++) {
					temp_Bedingung2 += BTeile[i] + " ";
				}
				temp_Bedingung2 = temp_Bedingung2.replace(/  /g," ");
				temp_Bedingung2 = temp_Bedingung2.replace(/\( /g,"(");
				temp_Bedingung2 = temp_Bedingung2.replace(/ \)/g,")");
				temp_Bedingung2 = temp_Bedingung2.replace(/ ;/g,";");
				temp_Bedingung2 = temp_Bedingung2.replace(/; /g,";");
				if (temp_Bedingung2.substr(0,1) == " ") {temp_Bedingung2 = temp_Bedingung2.substr(1);}
				if (temp_Bedingung2.substr(-1) == " ") {temp_Bedingung2 = temp_Bedingung2.substr(0,temp_Bedingung2.length - 1);}
				//Noch Klammern drin? Wenn ja, dann drehen wir noch eine Runde
				if (temp_Bedingung2.indexOf("(") > -1) {
					Anfang = -1;
				} else {
					Anfang = 0;
				}
				if (temp_Bedingung2.length > Bedingungen.length) {Bedingungen = temp_Bedingung2;}
				if (temp_Bedingung2.length > 0) {tempBedingungen = temp_Bedingung2.split(") OR (");}
			}
		}
		//Kriterien in die Tabelle eintragen
		Ebene = -1;
		for (Zeile = 0; Zeile < tempBedingungen.length; Zeile++) {
			Ebene += 1;
			Bedingung_schreiben(tempBedingungen[Zeile]);
		}
	}
	
	//Gruppierung einbauen
	if (Teile[TeilePos] == "GROUP") {
		TeilePos += 2;
		while (Teile[TeilePos] != "ORDER" && Teile[TeilePos] != "LIMIT" && Teile[TeilePos].indexOf(";") == -1 && TeilePos < Teile.length) {
			Gruppierung = Tab_Feld_ermitteln(Teile[TeilePos]);
			Spalte = 0;
			erledigt = 0;
			while (Spalte <= Spalten && erledigt == 0){
				if (document.getElementById("tabelle" + Spalte.toString()).value == "") {
					document.getElementById("tabelle" + Spalte.toString()).value = Tabelle_zum_Feld(document.getElementById("feld" + Spalte.toString()).value);
				}
				if ((document.getElementById("tabelle" + Spalte.toString()).value == Gruppierung["Tabelle"] || Gruppierung["Tabelle"] =="") && (document.getElementById("feld" + Spalte.toString()).value == Gruppierung["Feld"] || document.getElementById("alias" + Spalte.toString()).value == Gruppierung["Feld"])) {
					document.getElementById("gruppieren" + Spalte.toString()).checked = true;
					Spalte = Spalten + 1;
					erledigt = 1;
				}
				if (Spalte < Spalten && document.getElementById("tabelle" + Spalte.toString()).value == "" && document.getElementById("feld" + Spalte.toString()).value == "" && document.getElementById("alias" + Spalte.toString()).value == "" && document.getElementById("funktion" + Spalte.toString()).value == "") {
					document.getElementById("tabelle" + Spalte.toString()).value = Gruppierung["Tabelle"];
					gefunden = 0;
					for (x = 0; x < Tab.length; x++) {
						if (Tab[x]["Tabelle"] == Gruppierung["Tabelle"]) {
							for (i = 0; i < Tab[x]["Felder"].length; I++) {
								if (Tab[x]["Felder"][I]["Field"] == Gruppierung["Feld"]) {gefunden = 1;}
							}
						}
					}
					if (gefunden == 1) {
						document.getElementById("feld" + Spalte.toString()).value = Gruppierung["Feld"];
					} else {
						document.getElementById("alias" + Spalte.toString()).value = Gruppierung["Feld"];
					}
					document.getElementById("gruppieren" + Spalte.toString()).checked = true;
					Spalte = Spalten + 1;
					erledigt = 1;
				}
				Spalte += 1;
			}
			TeilePos += 1;
		}
	}
	//Sortierung einbauen
	if (Teile[TeilePos] == "ORDER") {
		TeilePos += 2;
		while (TeilePos < Teile.length && Teile[TeilePos] != "LIMIT" && Teile[TeilePos].indexOf(";") == -1) {
			Sortierung = Tab_Feld_ermitteln(Teile[TeilePos]);
			Spalte = 0;
			erledigt = 0;
			while (Spalte <= Spalten && erledigt == 0){
				if (document.getElementById("tabelle" + Spalte.toString()).value == "") {
					document.getElementById("tabelle" + Spalte.toString()).value = Tabelle_zum_Feld(document.getElementById("feld" + Spalte.toString()).value);
				}
				if ((document.getElementById("tabelle" + Spalte.toString()).value == Sortierung["Tabelle"] || Sortierung["Tabelle"] =="") && (document.getElementById("feld" + Spalte.toString()).value == Sortierung["Feld"] || document.getElementById("alias" + Spalte.toString()).value == Sortierung["Feld"] || document.getElementById("funktion" + Spalte.toString()).value == Sortierung["Feld"])) {
					if (Teile[TeilePos + 1] == "DESC" || Teile[TeilePos + 1] == "DESC;") {
						document.getElementById("sortierung" + Spalte.toString()).value = "abwärts";
					} else {
						document.getElementById("sortierung" + Spalte.toString()).value = "aufwärts";
					}
					Spalte = Spalten + 1;
					erledigt = 1;
				}
				if (Spalte < Spalten && document.getElementById("tabelle" + Spalte.toString()).value == "" && document.getElementById("feld" + Spalte.toString()).value == "" && document.getElementById("alias" + Spalte.toString()).value == "" && document.getElementById("funktion" + Spalte.toString()).value == "") {
					document.getElementById("tabelle" + Spalte.toString()).value = Sortierung["Tabelle"];
					gefunden = 0;
					for (x = 0; x < Tab.length; x++) {
						if (Tab[x]["Tabelle"] == Sortierung["Tabelle"]) {
							for (i = 0; i < Tab[x]["Felder"].length; i++) {
								if (Tab[x]["Felder"][i]["Field"] == Sortierung["Feld"]) {gefunden = 1;}
							}
						}
					}
					if (gefunden == 1) {
						document.getElementById("feld" + Spalte.toString()).value = Sortierung["Feld"];
					} else {
						document.getElementById("alias" + Spalte.toString()).value = Sortierung["Feld"];
					}
					if (Teile[TeilePos + 1] == "DESC" || Teile[TeilePos + 1] == "DESC;") {
						document.getElementById("sortierung" + Spalte.toString()).value = "abwärts";
					} else {
						document.getElementById("sortierung" + Spalte.toString()).value = "aufwärts";
					}
					Spalte = Spalten + 1;
					erledigt = 1;
				}
				Spalte += 1;
			}
			TeilePos += 2;
		}
	}
	//LIMIT verarbeiten
	Begrenzung = "";
	von = "0";
	while (TeilePos < Teile.length) {
		if (Teile[TeilePos] == "LIMIT") {
			TeilePos += 1;
			while (TeilePos < Teile.length){
				Begrenzung += Teile[TeilePos];
				TeilePos += 1;
			}
		}
		TeilePos += 1;
	}
	if (Begrenzung != "") {
		if (Begrenzung.indexOf(",") > -1) {
			von = Begrenzung.substr(0, Begrenzung.indexOf(","));
			Begrenzung = Begrenzung.substr(Begrenzung.indexOf(",")+1);
		}
		while (Begrenzung.substr(-1) == ";" || Begrenzung.substr(-1) == "'" ||Begrenzung.substr(-1) == '"') {
			Begrenzung = Begrenzung.substr(0,Begrenzung.length - 1);
		}
	}
	if (Begrenzung == "") {Begrenzung = "100";}
	document.getElementById("begrenzung").value = Begrenzung;
	document.getElementById("ab_satz").value = von;
}

function Tab_Feld_ermitteln(Teil) {
	Tabelle = "";
	Feld = "";
	while (Teil.substr(0, 1) == "`" || Teil.substr(0, 1) == "'" || Teil.substr(0, 1) == '"'){
		Teil = Teil.substr(1);
	}
	if (Teil.indexOf(".") > -1) {
		while (Teil.substr(0, 1) != "`" && Teil.substr(0, 1) != "'" && Teil.substr(0, 1) != '"' && Teil.substr(0, 1) != "." && Teil.length >0){
			Tabelle += Teil.substr(0, 1);
			Teil = Teil.substr(1);
		}
		while ((Teil.substr(0, 1) == "." || Teil.substr(0, 1) == "`" || Teil.substr(0, 1) == "'" || Teil.substr(0, 1) == '"') && Teil.length >0){
			Teil = Teil.substr(1);
		}
	}
	while (Teil.substr(0, 1) != "`" && Teil.substr(0, 1) != "'" && Teil.substr(0, 1) != '"' && Teil.substr(0, 1) != "," && Teil.substr(0, 1) != ";" && Teil.length >0){
		Feld += Teil.substr(0, 1);
		Teil = Teil.substr(1);
	}
	Ergebnis = new Array;
	Ergebnis = [];
	Ergebnis["Tabelle"] = Tabelle;
	Ergebnis["Feld"] = Feld;
	return(Ergebnis);
}

function Bedingung_schreiben(Auswertungstext) {
	while (Auswertungstext.length > 2){
		//Feld und ggf Tabelle ermitteln
		while (Auswertungstext.substr(0, 1) == "`" || Auswertungstext.substr(0, 1) == '"' || Auswertungstext.substr(0, 1) == "." || Auswertungstext.substr(0, 1) == "'" || Auswertungstext.substr(0, 1) == "=" || Auswertungstext.substr(0, 1) == " ") {
			Auswertungstext = Auswertungstext.substr(1);
		}
		Text = "";
		Tabelle = "";
		Feld = "";
		while (Auswertungstext.length > 0 && Auswertungstext.substr(0, 1) != "`" && Auswertungstext.substr(0, 1) != '"' && Auswertungstext.substr(0, 1) != "." && Auswertungstext.substr(0, 1) != "'" && Auswertungstext.substr(0, 1) != "=" && Auswertungstext.substr(0, 1) != " ") {
			Text += Auswertungstext.substr(0,1);
			Auswertungstext = Auswertungstext.substr(1);
		}
		while (Auswertungstext.substr(0, 1) == "`" || Auswertungstext.substr(0, 1) == '"' || Auswertungstext.substr(0, 1) == "'" || Auswertungstext.substr(0, 1) == "=" || Auswertungstext.substr(0, 1) == " ") {
			Auswertungstext = Auswertungstext.substr(1);
		}
		if (Auswertungstext.substr(0,1) == ".") {
			Tabelle = Text;
			Auswertungstext = Auswertungstext.substr(1);
			while (Auswertungstext.substr(0, 1) == "`" || Auswertungstext.substr(0, 1) == '"' || Auswertungstext.substr(0, 1) == "'" || Auswertungstext.substr(0, 1) == "=" || Auswertungstext.substr(0, 1) == " ") {
				Auswertungstext = Auswertungstext.substr(1);
			}
			while (Auswertungstext.length > 0 && Auswertungstext.substr(0, 1) != "`" && Auswertungstext.substr(0, 1) != '"' && Auswertungstext.substr(0, 1) != "." && Auswertungstext.substr(0, 1) != "'" && Auswertungstext.substr(0, 1) != "=" && Auswertungstext.substr(0, 1) != " ") {
				Feld += Auswertungstext.substr(0,1);
				Auswertungstext = Auswertungstext.substr(1);
			}
			Auswertungstext = Auswertungstext.substr(1);
		} else {
			Feld = Text;
			while (Auswertungstext.substr(0, 1) == "`" || Auswertungstext.substr(0, 1) == '"' || Auswertungstext.substr(0, 1) == "'" || Auswertungstext.substr(0, 1) == "=" || Auswertungstext.substr(0, 1) == " ") {
				Auswertungstext = Auswertungstext.substr(1);
			}
			Tabelle = Tabelle_zum_Feld(Feld);
		}
		//Kriterium aus dem Auswertungstext extrahieren
		oder = Auswertungstext.indexOf("OR ");
		und = Auswertungstext.indexOf("AND ");
		Ebene_schalten = 0;
		if (und > -1 && ((oder == -1 && und > -1) || (oder > -1 && und < oder))) {
			Kriterium = Auswertungstext.substr(0, Auswertungstext.indexOf("AND "));
			Auswertungstext = Auswertungstext.substr(Auswertungstext.indexOf("AND ") + 5);
		} else {
			if (oder > -1 && ((oder > -1 && und == -1) || (und > -1 && oder < und))) {
				Kriterium = Auswertungstext.substr(0, Auswertungstext.indexOf("OR "));
				Auswertungstext = Auswertungstext.substr(Auswertungstext.indexOf("OR ") + 4);
				Ebene_schalten = 1;
			} else {
				Kriterium = Auswertungstext;
				Auswertungstext = "";
			}
		}
		while (Kriterium.substr(0, 1) == " "){
			Kriterium = Kriterium.substr(1);
		}
		while (Kriterium.substr(0, 1) == "`"){
			Kriterium = Kriterium.substr(1);
		}
		while (Kriterium.substr(-1, 1) == " " || Kriterium.substr(-1, 1) == ";"){
			Kriterium = Kriterium.substr(0, Kriterium.length-1);
		}
		//leere Spalte, oder eine vorhandene Spalte mit gleichem Tabellennamen finden
		Zielspalte = -1;
		for (Spalte = 0; Spalte < Spalten; Spalte++) {
			if (document.getElementById("feld" + Spalte.toString()).value == "" && document.getElementById("funktion" + Spalte.toString()).value == "" && Zielspalte == -1) {
				Zielspalte = Spalte;
			} else {
				if ((document.getElementById("feld" + Spalte.toString()).value == Feld || (document.getElementById("feld" + Spalte.toString()).value == "" && document.getElementById("funktion" + Spalte.toString()).value == "")) && (document.getElementById("kriterium" + Spalte.toString() + "_" + Ebene.toString()).value == "" || document.getElementById("kriterium" + Spalte.toString() + "_" + Ebene.toString()).value == Kriterium) && document.getElementById("tabelle" + Spalte.toString()).value == Tabelle && Zielspalte == -1) {
					Zielspalte = Spalte;
				}
			}
		}
		//Sind genug Spalten und Zeilen da? Wenn nein, dann eine Spalte oder Zeile anfügen
		erneuern = 0;
		if (Zielspalte == Spalten - 1) {
			Spalten += 1;
			erneuern = 1;
		}
		if (Ebene == Kriterienzeilen - 1) {
			Kriterienzeilen += 1;
			erneuern = 1;
		}
		if (erneuern == 1) {Auswahltabelle_bauen();}
		//Spalte füllen
		if (document.getElementById("feld" + Zielspalte.toString()).value == "") {
			document.getElementById("tabelle" + Zielspalte.toString()).value = Tabelle;
			document.getElementById("feld" + Zielspalte.toString()).value = Feld;
		}
		document.getElementById("kriterium" + Zielspalte.toString() + "_" + Ebene.toString()).value = Kriterium;
		if (Ebene_schalten == 1) {Ebene += 1;}
	}
}

function Tabelle_zum_Feld(Feld) {
	gefunden = 0;
	for (i = 0; i < Tab.length; i++) {
		for (x = 0; x < Tab[i]["Felder"].length; x++) {
			if (Tab[i]["Felder"][x]["Field"] == Feld) {
				gefunden += 1;
				Tabelle = Tab[i].Tabelle;
			}
		}
	}
	if (gefunden != 1) {Tabelle = "";}
	//if (gefunden > 1) {alert("Das Feld " + Feld + " kommt in mehreren Tabellen vor.");}
	//if (gefunden == 0) {alert("Das Feld " + Feld + " kommt in keiner Tabelle vor.");}
	return Tabelle;
}

function SQL_zeigen() {
	try {sql_ausgabe.close();} catch (err) {}
	SQL_erzeugen();
	jsPanel.create({
		id: 'sql_ausgabe',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: window.innerWidth + ' 50',
		headerTitle: 'SQL',
		headerControls: {
			size: 'xs'
		},
		position: 'left-top 0 ' + (window.innerHeight - 403).toString(),
		content: '<textarea style="height: 100%; width: 100%;" id="sql_text" name="SQL_Text" onblur="SQL_zu_PHP_Feld();">' + SQL_erzeugen() + '</textarea>',
	});
}

function SQL_zu_PHP_Feld() {
	document.getElementById("SQL").value = document.getElementById("sql_ausgabe").content.firstChild.value;
}

function SQL_erzeugen() {
	Abfragetyp = document.getElementById("abfragetyp").value;
	SQL = Abfragetyp + " ";
	if (document.getElementById("duplikate").checked == true) {SQL += "DISTINCT ";}
	//temp Tabellenliste bauen
	tempTabellenliste = [];
	for (i = 0; i < Tab.length; i++) {
		tempTabellenliste[i] = Tab[i].Tabelle;
	}
	//Alle Tabellen aus der tempTabellenliste entfernen, die irgend eine rechte oder linke Verbindung haben
	for (x = 0; x < Verb.length; x++) {
		for (i = 0; i < tempTabellenliste.length; i++) {
			if (tempTabellenliste[i] == Verb[x].Starttabelle || tempTabellenliste[i] == Verb[x].Zieltabelle) {
				try {
					tempTabellenliste.splice(i,1);
					i = -1;
				} catch (err) {}
			}
		}
	}
	//Felder, Funktionen und Aliase aus dem Auswahlbereich hinzufügen
	if (Abfragetyp == "SELECT") {
		for (i = 0; i < Spalten; i++) {
			if (((document.getElementById("feld" + i).value > "" && document.getElementById("tabelle" + i).value > "") || document.getElementById("funktion" + i).value > "") && document.getElementById("anzeigen" + i).checked == true) {
				if (document.getElementById("feld" + i).value == "*") {
					Feldname = "*";
					if (Tab.length > 1) {Feldname = "`" + document.getElementById("tabelle" + i).value + "`." +  Feldname;}
				} else {
					if (document.getElementById("funktion" + i).value > "") {
						Feldname = document.getElementById("funktion" + i).value;
					} else {
						Feldname = "`" + document.getElementById("feld" + i).value + "`";
						if (Tab.length > 1) {Feldname = "`" + document.getElementById("tabelle" + i).value + "`." +  Feldname;}
					}
				}
				if (document.getElementById("alias" + i).value > "") {Feldname = Feldname + " AS " + document.getElementById("alias" + i).value;}
				SQL += Feldname + ", ";
			}
		}
		if (SQL.substr(-2) == ", ") {SQL = SQL.substr(0,SQL.length - 2);}
		
		//Tabellen hinzu
		SQL += " FROM ";
		for (i = 0; i < tempTabellenliste.length; i++) {
			SQL += "`" + tempTabellenliste[i] + "`, ";
		}
	}
	if (Abfragetyp == "UPDATE") {
		//Tabellen hinzu
		for (i = 0; i < tempTabellenliste.length; i++) {
			SQL += "`" + tempTabellenliste[i] + "`, ";
		}
		if (SQL.substr(-2) == ", ") {SQL = SQL.substr(0,SQL.length - 2);}
		//Den SET Teil bauen
		setzen = " SET ";
		for (i = 0; i < Spalten; i++) {
			if (document.getElementById("feld" + i).value > "" && document.getElementById("tabelle" + i).value > "" && document.getElementById("schreiben" + i).value > "") {
				if (document.getElementById("feld" + i).value != "*") {
					Feldname = "`" + document.getElementById("feld" + i).value + "`";
					if (Tab.length > 1) {Feldname = "`" + document.getElementById("tabelle" + i).value + "`." +  Feldname;}
					setzen += Feldname + " = '" + document.getElementById("schreiben" + i).value + "', ";
				}
			}
		}
		if (setzen.substr(-2) == ", ") {setzen = setzen.substr(0,setzen.length - 2);}
	}
	
	if (Abfragetyp == "DELETE") {
		SQL = "DELETE FROM " + "`" + Tab[0]["Tabelle"] + "` ";
	}
		
	if (Abfragetyp == "INSERT") {
		SQL = "INSERT INTO " + "`" + document.getElementById("tabelle0").value + "` (";
		for (i = 0; i < Spalten; i++) {
			if (document.getElementById("feld" + i.toString()).value != "") {
				SQL += "`" + document.getElementById("feld" + i.toString()).value + "`, ";
			}
		}
		if (SQL.substr(SQL.length - 2) == ", ") {SQL = SQL.substr(0, SQL.length - 2);}
		SQL += ") VALUES (";
		for (i = 0; i < Spalten; i++) {
			if (document.getElementById("schreiben" + i.toString()).value != "") {
				SQL += "'" + document.getElementById("schreiben" + i.toString()).value + "', ";
			}
		}
		if (SQL.substr(SQL.length - 2) == ", ") {SQL = SQL.substr(0, SQL.length - 2);}
		SQL += ")";
	} else {
		//JOINS einbauen
		Tabelle = "";
		for (i = 0; i < Verb.length; i++) {
			if (Verb[i].Verbindung == "links") {Typ = "LEFT";}
			if (Verb[i].Verbindung == "rechts") {Typ = "RIGHT";}
			if (Verb[i].Verbindung == "beide") {Typ = "INNER";}
			if (Tabelle == ""){
				Tabelle = Verb[i].Starttabelle;
				SQL += "`" + Verb[i].Starttabelle + "` ";
			} 
			SQL += Typ + " JOIN `" + Verb[i].Zieltabelle + "` ON `" + Verb[i].Starttabelle + "`.`" + Verb[i].Startfeld + "` = `" + Verb[i].Zieltabelle + "`.`" + Verb[i].Zielfeld + "` ";
		}
		if (SQL.substr(-2) == ", ") {SQL = SQL.substr(0, SQL.length - 2) + " ";}
		if (Abfragetyp == "UPDATE") {SQL += setzen + " ";}
		SQL += "WHERE ";

		//Bedingungen dazu nehmen
		for (x = 0; x < Kriterienzeilen; x++) {
			und = "("; 
			for (i = 0; i < Spalten; i++) {
				if (document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value > "" && document.getElementById("feld" + i).value != "*") {
					if (Tab.length > 1) {
						und += "`" + document.getElementById("tabelle" + i).value + "`.`" + document.getElementById("feld" + i).value + "` " + document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value + " AND ";
					} else {
						und += "`" + document.getElementById("feld" + i).value + "` " + document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value + " AND ";
					}
				}
			}
			if (und == "(") {
				und = "";
			} else {
				und = und.substr(0,und.length - 5) + ")"
			}
			SQL += und + " OR ";	
		}
		while (SQL.substr(SQL.length - 4) == " OR "){
			SQL = SQL.substr(0, SQL.length - 4);
		}
		while (SQL.substr(SQL.length - 5) == " AND "){
			SQL = SQL.substr(0, SQL.length - 5);
		}
		while (SQL.substr(SQL.length - 1) == " "){
			SQL = SQL.substr(0, SQL.length - 1);
		}
		if (SQL.substr(SQL.length - 6) == " WHERE") {
			SQL = SQL.substr(0, SQL.length - 6)
		}
		//GROUP BY einbauen
		Gruppierung = " GROUP BY ";
		for (Spalte = 0; Spalte < Spalten; Spalte++) {
			if (document.getElementById("gruppieren" + Spalte.toString()).checked == true) {
				if (document.getElementById("alias" + Spalte.toString()).value != "") {
					Gruppierung += "`" + document.getElementById("alias" + Spalte.toString()).value + "`, ";
				} else {
					Gruppierung += "`" + document.getElementById("tabelle" + Spalte.toString()).value + "`.`" + document.getElementById("feld" + Spalte.toString()).value + "`, ";
				}
			}
		}
		if (Gruppierung != " GROUP BY ") {
			Gruppierung = Gruppierung.substr(0, Gruppierung.length - 2);
			SQL += Gruppierung;
		}
		//ORDER einbauen
		Order = " ORDER BY ";
		for (i = 0; i < Spalten; i++) {
			if (document.getElementById("feld" + i.toString()).value > "" || document.getElementById("funktion" + i.toString()).value > "" || document.getElementById("alias" + i.toString()).value > "") {
				Richtung = "";
				if (document.getElementById("feld" + i.toString()).value > "") {
					OrderText = document.getElementById("feld" + i.toString()).value;
				} else {
					if (document.getElementById("alias" + i.toString()).value > "") {
						OrderText = document.getElementById("alias" + i.toString()).value;
					} else {OrderText = document.getElementById("funktion" + i.toString()).value;}
				}
				if (document.getElementById("tabelle" + i.toString()).value > "") {
					OrderText = "`" + document.getElementById("tabelle" + i.toString()).value + "`.`" + OrderText + "`";
				} else {
					OrderText = "`" + OrderText + "`";
				}				
				if (document.getElementById("sortierung" + i.toString()).value =="abwärts") {Richtung = " DESC";}
				if (document.getElementById("sortierung" + i.toString()).value =="aufwärts") {Richtung = " ASC";}
				if (Richtung > "") { Order += OrderText + Richtung + ", ";}
			}
		}
		if (Order != " ORDER BY ") {
			SQL += Order.substr(0,Order.length-2);
		}
		if (document.getElementById("abfragetyp").value == "SELECT") {
			//Limit setzen
			Limit = " LIMIT ";
			einbauen = 0;
			if (document.getElementById("ab_satz").value != "") {Limit += document.getElementById("ab_satz").value + ",";}
			if (document.getElementById("begrenzung").value > 0) {
				Limit += document.getElementById("begrenzung").value;
				einbauen = 1;
			}
			if (einbauen == 1) {SQL += Limit;}
		}
	}
	document.getElementById("SQL").value =  SQL + ";";
	return SQL + ";";
}

function abspeichern() {
	document.getElementById("SQL").value = SQL_erzeugen();
}

function HTML_decode(input){
  var e = document.createElement('textarea');
  e.innerHTML = input;
  // handle case of empty input
  return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

function leeren() {
	for (i = 0; i < Spalten; i++) {
		try {
			document.getElementById("feld" + i.toString()).value = "";
			document.getElementById("funktion" + i.toString()).value = "";
			document.getElementById("alias" + i.toString()).value = "";
			document.getElementById("tabelle" + i.toString()).value = "";
			document.getElementById("sortierung" + i.toString()).value = "";
			try {
				document.getElementById("sortierung" + i.toString()).removeAttribute("checked");
				document.getElementById("sortierung" + i.toString()).value = "";
			} catch (err) {}
			for (x = 0; x < Kriterienzeilen; x++) {
				try {document.getElementById("kriterium" + i.toString() + "_" + x.toString()).value = "";} catch (err) {}
			}
		} catch (err) {}
	}
	for (Tab_Zaehler = 0; Tab_Zaehler <= Tab.length; Tab_Zaehler++) {
		try {
			document.getElementById(Tab[Tab_Zaehler].Tabelle + '_Fenster').close();
			Tab_Zaehler = -1;
		} catch (err) {}
	}
	Tab = [];
	Verb = [];
	Ebene = 0;
	links = 10;
	Foben = 65;
	Verb_Objekt = null;
}
		
function Abfrage_ausfuehren() {
	try {ausgabe_abfrage.close();} catch (err) {}
	SQL_erzeugen();
	Inhalt = "<div id='Abfrageergebnis' style = 'position: relative; top: 5px; left: 5px;'><table id='Ergebnistabelle" + Ergebnisausgaben.toString() + "' class='Text_einfach' style='background: #FCEDD9;' cellspaceing = '4'>\n";
	jQuery.ajax({
		url: "./Abfrage_ausfuehren.php?Datenbank=" + document.getElementById("datenbank").value,
		type: 'POST',
		data: {SQL_Text: document.getElementById("SQL").value},
		success: function (html) {
	  		strReturn = html;
		},
  		async: false
	});
	Inhalt += strReturn + "</table></div>";
	jsPanel.create({
		id: 'ausgabe_abfrage',
		theme: 'info',
		contentSize: window.innerWidth - 20 + ' 300',
		headerTitle: T_Text[41],
		headerControls: {
			size: 'xs'
		},
		position: 'left-top 0 65',
		content: Inhalt,
		contentOverflow: 'scroll scroll'
	});
	$("#Ergebnistabelle" + Ergebnisausgaben.toString()).colResizable({
		liveDrag:true, 
		draggingClass:"dragging", 
		resizeMode:'overflow', 
	});
	//Groesse anpassen
	Ausgabe_Spalten = document.getElementById("Ergebnistabelle" + Ergebnisausgaben.toString()).childNodes[1].firstChild.children;
	for (Spalte = 0; Spalte < Ausgabe_Spalten.length; Spalte++) {
		Ausgabe_Spalten[Spalte].style.width = (parseInt(Ausgabe_Spalten[Spalte].style.width) + 10).toString() + "px";
	}
	Breite = document.getElementById("Ergebnistabelle" + Ergebnisausgaben.toString()).clientWidth;
	if (Breite < 200) {Breite = 200;}
	ausgabe_abfrage.style.width = (Breite + 20).toString() + "px";
	Ergebnisausgaben +=1;
}

function Feld_u_Tabelle_leeren() {
	for (Spalte = 0; Spalte < Spalten; Spalte++) {
		if (document.getElementById("funktion" + Spalte.toString()).value != "") {
			document.getElementById("feld" + Spalte.toString()).value = "";
			document.getElementById("tabelle" + Spalte.toString()).value = "";
		}
	}
}
	
function Tabellen_fuellen() {
	jQuery.ajax({
		url: "./Tabellen_einlesen.php?Datenbank=" + document.getElementById("datenbank").value,
		success: function (html) {
   		document.getElementById("tabellenliste").innerHTML = html;
		},
 			async: false
  	});
  	document.getElementById("SQL").value = "";
  	Auswahltabelle_bauen(0);
}

function Bed_zerlegen(Bedingungen) {
	BTeile = BTeile_erzeugen(Bedingungen);
	Saetze = new Array;
	Saetze = [];
	Satz = 0;
	Saetze[Satz] = "";
	Klammer_auf = 0;
	for (i = 0; i < BTeile.length; i++) {
		if (BTeile[i] == "(") {Klammer_auf += 1;}
		if (BTeile[i] == ")") {Klammer_auf += -1;}
		if (BTeile[i] == "OR" && Klammer_auf == 0) {
			Satz += 1;
			Saetze[Satz] = "";
		} else {Saetze[Satz] += BTeile[i] + " ";}
	}
	//kleine Korrekturen wegen Leerzeichen
	for (i = 0; i < Saetze.length; i++) {
		Saetze[i] = Saetze[i].replace(/\( \(/g,"((");
		Saetze[i] = Saetze[i].replace(/\) \)/g,"))");
		Saetze[i] = Saetze[i].replace(/  /g," ");
		Saetze[i] = Saetze[i].replace(/\(\s/g,"(")
		Saetze[i] = Saetze[i].replace(/\s\)/g,")")
		if (Saetze[i].substr(-1) == " ") {Saetze[i] = Saetze[i].substr(0,Saetze[i].length - 1);}
	}
	//Haben wir es mit einem, oder mehreren JOINs zu tun? Wenn ja, dann entsprechend behandeln
	for (Satz = 0; Satz < Saetze.length; Satz++) {
		tempText = "";
		for (i = 0; i < Saetze[Satz].length; i++) {
			tempZeichen = Saetze[Satz].substr(i,1);
			if (tempZeichen == "." || tempZeichen == "=") {tempText += tempZeichen;}
			if (Saetze[Satz].substr(i,3) == "AND" || Saetze[Satz].substr(i,2) == "OR") {i = Saetze[Satz].length;}
		}
		if (tempText == ".=.") {
			posAND = Saetze[Satz].indexOf("AND");
			posOR = Saetze[Satz].indexOf("OR");
			if (posOR < posAND) {
				pos = posOR + 4;
			} else {
				pos = posAND + 5;
			}
			Kriterium = Saetze[Satz].substr(0, pos);
			if (Kriterium.indexOf(" AND ") > -1){
				Kriterium = Kriterium.substr(0,Kriterium.indexOf(" AND "));
				Saetze[Satz] = Saetze[Satz].substr(0,Saetze[Satz].indexOf(Kriterium)) + Saetze[Satz].substr(Saetze[Satz].indexOf(Kriterium) + Kriterium.length + 5, Saetze[Satz].length);
			} else {
				Saetze[Satz] = Saetze[Satz].substr(0,Saetze[Satz].indexOf(Kriterium)) + Saetze[Satz].substr(Saetze[Satz].indexOf(Kriterium) + Kriterium.length, Saetze[Satz].length);
			}
			while (Kriterium.substr(0, 1) == "`" || Kriterium.substr(0, 1) == "'" || Kriterium.substr(0, 1) == '"' || Kriterium.substr(0, 1) == " "){
				Kriterium = Kriterium.substr(1);
			}
			Tabelle1 = "";
			while (Kriterium.length > 0 && Kriterium.substr(0, 1) != "`" && Kriterium.substr(0, 1) != "." && Kriterium.substr(0, 1) != '"' && Kriterium.substr(0, 1) != "'" && Kriterium.substr(0, 1) != "=" && Kriterium.substr(0, 1) != " "){
				Tabelle1 += Kriterium.substr(0, 1);
				Kriterium = Kriterium.substr(1);
			}
			while (Kriterium.substr(0, 1) == "`" || Kriterium.substr(0, 1) == "." || Kriterium.substr(0, 1) == "'" || Kriterium.substr(0, 1) == "=" || Kriterium.substr(0, 1) == '"' || Kriterium.substr(0, 1) == " "){
				Kriterium = Kriterium.substr(1);
			}
			Feld1 = "";
			while (Kriterium.length > 0 && Kriterium.substr(0, 1) != "`" && Kriterium.substr(0, 1) != '"' && Kriterium.substr(0, 1) != "." && Kriterium.substr(0, 1) != "'" && Kriterium.substr(0, 1) != "=" && Kriterium.substr(0, 1) != " "){
				Feld1 += Kriterium.substr(0, 1);
				Kriterium = Kriterium.substr(1);
			}
		
			while (Kriterium.substr(0, 1) == "`" || Kriterium.substr(0, 1) == "'" || Kriterium.substr(0, 1) == "=" || Kriterium.substr(0, 1) == '"' || Kriterium.substr(0, 1) == " "){
				Kriterium = Kriterium.substr(1);
			}
			Tabelle2 = "";
			while (Kriterium.length > 0 && Kriterium.substr(0, 1) != "`" && Kriterium.substr(0, 1) != '"' && Kriterium.substr(0, 1) != "." && Kriterium.substr(0, 1) != "'" && Kriterium.substr(0, 1) != "=" && Kriterium.substr(0, 1) != " "){
				Tabelle2 += Kriterium.substr(0, 1);
				Kriterium = Kriterium.substr(1);
			}
			while (Kriterium.substr(0, 1) == "`" || Kriterium.substr(0, 1) == "." || Kriterium.substr(0, 1) == "'" || Kriterium.substr(0, 1) == "=" || Kriterium.substr(0, 1) == '"' || Kriterium.substr(0, 1) == " "){
				Kriterium = Kriterium.substr(1);
			}
			Feld2 = "";
			while (Kriterium.length > 0 && Kriterium.substr(0, 1) != "`" && Kriterium.substr(0, 1) != '"' && Kriterium.substr(0, 1) != "." && Kriterium.substr(0, 1) != "'" && Kriterium.substr(0, 1) != "=" && Kriterium.substr(0, 1) != " "){
				Feld2 += Kriterium.substr(0, 1);
				Kriterium = Kriterium.substr(1);
			}
			i = Verb.length;
			Verb[i] = [];
			Verb[i].Starttabelle = Tabelle1;
			Verb[i].Startfeld = Feld1;
			Verb[i].Zieltabelle = Tabelle2;
			Verb[i].Zielfeld = Feld2;
			Verb[i].Verbindung = "beide";
			Verb_Koordinaten(i);
			Verb_erstellen(i);
		}
	}
	Bedingungen = "";
	for (x = 0; x < Saetze.length; x++) {
		Bedingungen += Saetze[x] + " OR ";
	}

	return Bedingungen.substr(0, Bedingungen.length - 4)
}

function BTeile_erzeugen(Bedingungen) {
	BTeile = [];
	pos = 0;
	i = 0;
	BTeile[i] = "";
	while (pos < Bedingungen.length) {
		if (BTeile[i] == null) {BTeile[i]  = "";}
		if (Bedingungen[pos] == ")") {
			i += 1;
			BTeile[i] = ")";
			i += 1;
		} else {
			if (Bedingungen[pos] == "(") {
				if (pos > 0) {
					Zeichencode = Bedingungen[pos - 1].charCodeAt(0);
					if ((Zeichencode > 64 && Zeichencode < 91) || (Zeichencode > 96 && Zeichencode < 123)) {
						BTeile[i] += Bedingungen[pos];
					}
				} else {
					i += 1;
					BTeile[i] = "(";
					i += 1;
				}
			} else {
				if (Bedingungen.substr(pos,4) == " OR ") {
					i += 1;
					BTeile[i] = "OR";
					pos += 3;
					i += 1;
				} else {
					if (Bedingungen.substr(pos,5) == " AND ") {
						i += 1;
						BTeile[i] = "AND";
						pos += 4;
						i += 1;
					} else {
						BTeile[i] += Bedingungen[pos];
					}
				}
			}
		}
		pos += 1;
	}
	//leere Teile entfernen
	for (i = 0; i < BTeile.length; i++) {
		if (BTeile[i] == "" || BTeile[i] == " ") {
			BTeile.splice(i,1);
			i += -1;
		}
	}
	//Leerstellen am Anfang und Ende der Teile entfernen
	for (i = 0; i < BTeile.length; i++) {
		while (BTeile[i][0] == " "){
			BTeile[i] = BTeile[i].substr(1);
		}
		while (BTeile[i].substr(-1) == " "){
			BTeile[i] = BTeile[i].substr(0, BTeile[i].length - 1);
		}
	}
	//überflüssige Klammer am Anfang und Ende entfernen
	fertig = 0;
	Bedingungen_temp = Bedingungen;
	//Leerstellen am Anfang und Ende von Bedingungen_temp entfernen
	while (Bedingungen_temp[0] == " ") {
		Bedingungen_temp = Bedingungen_temp.substr(1,Bedingungen_temp.length);
	}
	while (Bedingungen_temp[Bedingungen_temp.length - 1] == " ") {
		Bedingungen_temp = Bedingungen_temp.substr(0,Bedingungen_temp.length - 1);
	}	

	while (fertig == 0) {
		if (Bedingungen_temp[0] == "(" && Bedingungen_temp[Bedingungen_temp.length - 1] == ")") {
			Klammer = "";
			for (i = 1; i < Bedingungen_temp.length - 1; i++) {
				if (Bedingungen_temp[i] == "(" || Bedingungen_temp[i] == ")") {Klammer += Bedingungen_temp[i];} 
			}
			while (Klammer.indexOf("()") > -1) {
				Klammer = Klammer.replace(/\(\)/g,"");
			}
			if (Klammer == "") {
				BTeile.splice(BTeile.length - 1,1);
				BTeile.splice(0,1);
				Bedingungen_temp = Bedingungen_temp.substring(1,Bedingungen_temp.length-1);
			} else {fertig = 1;}
		} else {fertig = 1;}
	}
	//Klammern von einzeln eingeklammerten Bedingungen entfernen
//	for (i = 0; i < BTeile.length; i++) {
//		if (BTeile[i] == "(" && BTeile[i + 2] == ")") {
//			BTeile.splice(i + 2,1);
//			BTeile.splice(i,1);
//			i = 0;
//		}
//	}
	if (BTeile[BTeile.length - 1] == ";") {BTeile.splice(BTeile.length - 1,1);}
	//doppelte Klammern durch einzelne Klammern ersetzen
	erledigt = 0;
	while (erledigt == 0) {
		for (i = 0; i < BTeile.length; i++) {
			erledigt = 1;
			if (BTeile[i] == "(" && BTeile[i + 1] == "(") {
				x = i;
				while (BTeile[x] != ")") {
					x += 1;
				}
				if (BTeile[x + 1] == ")") {
					erledigt = 0;
					BTeile.splice(x,1);
					BTeile.splice(i,1);
				}
			}
		}
	}
	return BTeile;
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
		if (document.getElementById("abfrage_nav").style.display == "block") {
			document.getElementById("abfrage_nav").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("abfrage_nav").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("abfrage_nav").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 3) {
		if (document.getElementById("datenbank_nav").style.display == "block") {
			document.getElementById("datenbank_nav").style.display = "none"
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("datenbank_nav").style.display = "block"
			document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("datenbank_nav").style.display = "none";
		document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 4) {
		if (document.getElementById("sonstiges_nav").style.display == "block") {
			document.getElementById("sonstiges_nav").style.display = "none"
			document.getElementById("schaltfl_4").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("sonstiges_nav").style.display = "block"
			document.getElementById("schaltfl_4").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("sonstiges_nav").style.display = "none";
		document.getElementById("schaltfl_4").style.backgroundColor = "#FCEDD9";
	}
}
	