var T_Text = new Array;
var gelesene_Filter = new Array;
var Filterzeilen = 10;
var Felder = "";
var Elemente = "";
var Bedingungen = {};
var editnr = 0;
function Start() {
	try {
		T_Text = JSON.parse(document.getElementById("translation").value);
		if (document.getElementById("unterformulare").value > "") {
			Unterformulare = document.getElementById("unterformulare").value.split(",");
			Unterform_Feld_IDs = document.getElementById("Unterform_Feld_IDs").value.split(",");
			for (i = 0; i < Unterformulare.length; i++) {
				document.cookie = "Filter_" + Unterformulare[i] + " = " + document.getElementById("verkn_feld_" + Unterformulare[i]).value + " = " + document.getElementById("verkn_wert_" + Unterformulare[i]).value + ";sameSite = strict;";
				document.getElementById("" + Unterform_Feld_IDs[i]).contentDocument.forms.Einstellungen.submit();
			}
		}
	} catch (err) {}
	//bedingte Formatierung einrichten
	try {Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
		i = 1;
		while(Bedingungen[i] != undefined) {
			Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
			i = i + 1;
		}	
	} catch (err) {}
	//Groesse anpassen
	try {
		Ausgabe_Spalten = document.getElementById("datentabelle").childNodes[1].firstChild.children;
		for (Spalte = 0; Spalte < Ausgabe_Spalten.length; Spalte++) {
			Ausgabe_Spalten[Spalte].style.width = (parseInt(Ausgabe_Spalten[Spalte].style.width) + 10).toString() + "px";
		}
		if (document.getElementById("neu_laden").value !=1) {
			document.getElementById("neu_laden").value = 1;
			//Nav_Anfang();
		} else {
			document.getElementById("neu_laden").value = 0;
		}
	} catch (err) {}
	if (document.getElementById("navigationsbereich").value == 1) {
		if (document.forms.Einstellungen.Darstellung.value == T_Text[9]) {document.addEventListener('keydown', Taste_gedrueckt);}
		document.getElementById("navi").style.display="block";
	}
	//ggf Elemente ausblenden
	try {
		var Bereich = document.getElementById("db_Bereich");
		for (i = 0; i < Bereich.childNodes.length; i++) {
			try {
				if (Bereich.childNodes[i].hasAttribute("display1") == true ) {Bereich.childNodes[i].style.display = Bereich.childNodes[i].attributes.display1.value;}
			} catch (err) {}
		}
	} catch (err) {}
	try {Satz_lesen();} catch (err) {}
	//try {Felder_fuellen();} catch (err) {}
}

//document.addEventListener('jspanelbeforeclose', handler, false);

$("#datentabelle").colResizable({
	liveDrag:true, 
	draggingClass:"dragging", 
	resizeMode:'overflow', 
});

function Nav_Anfang(Modus){
	document.getElementById("nav_Satz").value = 1;
	if (Modus == "Satz") {speichern();}
	Satz_lesen();
}
	
function Nav_zurueck(Modus){
	if (Modus == "Satz") {
		speichern();
		if (document.getElementById("nav_Satz").value > 1) {
			document.getElementById("nav_Satz").value = parseInt(document.getElementById("nav_Satz").value) - 1;
			Satz_lesen();
		}
	} else {
		if (parseInt(document.getElementById("nav_Satz").value) / parseInt(document.getElementById("form_tabellenzeilen").value) > 1) {document.getElementById("nav_Satz").value = parseInt(document.getElementById("nav_Satz").value) - parseInt(document.getElementById("form_tabellenzeilen").value);}
	}
}

function Nav_weiter(Modus){
	if (Modus == "Satz") {
		speichern();
		if (parseInt(document.getElementById("nav_Satz").value) < parseInt(document.getElementById("Saetze").value)) {document.getElementById("nav_Satz").value = parseInt(document.getElementById("nav_Satz").value) + 1;}
		Satz_lesen();
	} else {
		if (parseInt(document.getElementById("nav_Satz").value) / parseInt(document.getElementById("form_tabellenzeilen").value) < parseInt(document.getElementById("Saetze").value) / parseInt(document.getElementById("form_tabellenzeilen").value)) {document.getElementById("nav_Satz").value = parseInt(document.getElementById("nav_Satz").value) + parseInt(document.getElementById("form_tabellenzeilen").value);}
	}
}

function Nav_Ende(Modus){
	if (Modus == "Satz") {
		speichern();
		document.getElementById("nav_Satz").value = document.getElementById("Saetze").value;
		Satz_lesen();
	} else {
		Seiten = parseInt(parseInt(document.getElementById("Saetze").value / parseInt(document.getElementById("form_tabellenzeilen").value)));
		if (Seiten * parseInt(document.getElementById("form_tabellenzeilen").value) + 1 > parseInt(document.getElementById("Saetze").value)) {Seiten = Seiten -1;}
		document.getElementById("nav_Satz").value = Seiten * parseInt(document.getElementById("form_tabellenzeilen").value) + 1;
	}
}

function Satz_entfernen() {
	if (document.forms.Einstellungen.Ansicht.value != T_Text[9]) {document.getElementById("indexwert").value = document.getElementById("Zeile" + document.getElementById("nav_Satz").value).attributes.indexwert.value;}
	if (confirm('Wollen Sie den Datensatz wirklich löschen?') == true) {
		document.getElementById("aktion").value = "loeschen";
		document.forms["Einstellungen"].submit();
	} 
}

function neuer_Satz() {
	speichern();
	document.getElementById("aktion").value = "neuer Datensatz";
	document.forms["Einstellungen"].submit();
}

function speichern() {
	abschicken = 0;
	var SQL_Text = "UPDATE `" + document.getElementById("tabellenname").value + "` SET ";
	for (i = 0; i < Felder.length; i++) {
		try{
			if(Felder[i].type == "checkbox") {
				if (Felder[i].checked == true) {
					Felder[i].value = 1;
				} else {
					Felder[i].value = 0;
				}
			}
			if (Felder[i].attributes["feldtyp"].value == "Optionsgruppe") {
				orig_Wert = htmlspecialchars_decode(document.getElementById("schatten_" + Felder[i].attributes.feld.value).value);
				Optionsgruppe = document.getElementsByName(Felder[i].attributes.name.value);
				for (x = 0; x < Optionsgruppe.length; x++) {
					if (Optionsgruppe[x].localName == "input" && Optionsgruppe[x].type == "radio") {
						if (Optionsgruppe[x].value != orig_Wert && Optionsgruppe[x].checked == true) {
							SQL_Text = SQL_Text + "`" + Felder[i].attributes.feld.value + "` = '" + Optionsgruppe[x].value + "', ";
							abschicken = 1;
						}
					}
				}
			} else {
				if (Felder[i].attributes["feldtyp"].value == "Textarea" || Felder[i].attributes["feldtyp"].value == "Feld_anzeigen") {
					if (htmlspecialchars(Felder[i].innerHTML) != document.getElementById("schatten_" + Felder[i].attributes.feld.value).value) {
						if (Felder[i].attributes["feldtyp"].value == "Textarea") {
							if (Felder[i].innerHTML.substr(0,3) == "<p>") {Felder[i].innerHTML = Felder[i].innerHTML.substr(3,Felder[i].innerHTML.length-7);}
							while (Felder[i].innerHTML.substr(Felder[i].innerHTML.length-4,4) == "<br>") {
								Felder[i].innerHTML = Felder[i].innerHTML.substr(0,Felder[i].innerHTML.length-4);
							}
						}
						SQL_Text = SQL_Text + "`" + Felder[i].attributes.feld.value + "` = '" + htmlspecialchars(Felder[i].innerHTML) + "', ";
						abschicken = 1;
					}
				} else {
					if (htmlspecialchars(Felder[i].value) != document.getElementById("schatten_" + Felder[i].attributes.feld.value).value) {
						SQL_Text = SQL_Text + "`" + Felder[i].attributes.feld.value + "` = '" + htmlspecialchars(Felder[i].value) + "', ";
						abschicken = 1;
					}
				}
			}
		} catch (err) {}
	}
	SQL_Text = SQL_Text.substr(0, SQL_Text.length - 2);
	SQL_Text = SQL_Text + " WHERE `" + document.getElementById("indexfeld").value + "` = " + document.getElementById("indexwert").value + ";";
	
	if (abschicken == 1) {
  		jQuery.ajax({
			url: "SQL_ausfuehren.php",
			success: function (html) {
   			strReturn = html;
			},
			type: 'POST',
			data: {SQL: SQL_Text, Replikation: document.getElementById("replikation").value},
  			async: false
  		});
  		if (strReturn.length > 0) {alert("Die Verbindung zu einem der Server ist unterbrochen, daher können momentan keine Daten hinzugefügt, gelöscht oder geändert werden.");}
	}
}

function Satz_lesen() {
	var indexfeld = document.getElementById("indexfeld").value;
	if (document.getElementById("neueste_ID").value > 0) {
		SQL_Text = "SELECT * FROM `" + document.getElementById("tabellenname").value + "` WHERE `" + document.getElementById("indexfeld").value + "` = " + document.getElementById("neueste_ID").value.toString() + ";";
		document.getElementById("neueste_ID").value = 0;
	} else {
		SQL_Text = document.getElementById("datenquelle_akt").value;
		var TempSQL = SQL_Text.substr(0,SQL_Text.toLocaleLowerCase().indexOf("from"));
		if (TempSQL.toLocaleLowerCase().indexOf(indexfeld.toLocaleLowerCase()) == -1 && TempSQL.indexOf("*") == -1) {
			if (TempSQL.substr(-1) == " ") {TempSQL = TempSQL.substr(0,TempSQL.length-1);}
			TempSQL = TempSQL + ", `" + indexfeld + "` ";
			SQL_Text = TempSQL + SQL_Text.substr(SQL_Text.toLocaleLowerCase().indexOf("from"), SQL_Text.lenght);
		}
		if(SQL_Text.substr(-1,1) == ";") {SQL_Text = SQL_Text.substr(0,SQL_Text.length-1);}
		SQL_Text = SQL_Text + " LIMIT " + (parseInt(document.getElementById("nav_Satz").value) - 1).toString() + ",1;";
	}
	jQuery.ajax({
		url: "Datensatz_lesen.php",
		success: function (html) {
   		strReturn = html;
		},
		type: 'POST',
		data: {SQL: SQL_Text},
		async: false
	});
	Werte = JSON.parse(strReturn);
	for (i = 0; i < Werte.length; i++) {
		try {document.getElementById("schatten_" + Werte[i].Feld).value = Werte[i].Wert;} catch (err) {}
	}
	Felder_fuellen();
}

function Sortierung_Dialog_oeffnen() {
	var Feldnamen = document.getElementById("datenfeldnamen").value;
	if (Feldnamen.substr(Feldnamen.length - 1) == ",") {Feldnamen = Feldnamen.substr(0,Feldnamen.length - 1);}
	Feldnamen = Feldnamen.split(",");
	var Feldliste = "<option></option>";
	for (i = 0; i < Feldnamen.length; i++) {
		Feldliste = Feldliste + "<option>" + Feldnamen[i] + "</option>";
	}
	var vor_Sortierung = "";
	if (document.getElementById("sortierungen").value > "") {vor_Sortierung = document.getElementById("sortierungen").value.substr(10);}
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px'><table class='table' cellpadding = '3' width='300px'>\n";
	Inhalt = Inhalt + "<tr class='Tabelle_Ueberschrift' style='height: 30px;' valign = 'bottom'><td>" + T_Text[26] + "</td><td>" + T_Text[27] + "</td></tr>\n";
	for (i = 1; i < Filterzeilen + 1; i++) {
		Inhalt = Inhalt + "<td><select class='Auswahl_Liste_Element' id='feld_" + i + "' name='Feld_" + i + "' value=''>" + Feldliste + "</select></td>\n";
		Inhalt = Inhalt + "<td><select class='Auswahl_Liste_Element' value='' id='sort_" + i + "' name='Sort' value=''><option>ASC</option><option>DESC</option></select></td></tr>\n";
	}
	Inhalt = Inhalt + "</table><br><input class='Schalter_Element' type ='button' id='sortierung_uebernehmen' name='Sortierung_uebernehmen' value='" + T_Text[28] + "' onclick='Sortierung_uebernehmen();'></div>\n";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Sortierung_Dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '320 380',
		headerTitle: T_Text[29],
		content:  Inhalt,
	});
	Zeile = 1;
	while (vor_Sortierung.length > 0) {
		Sort = "";
		Feldname = "";
		if (vor_Sortierung.length > 0) {
			while (vor_Sortierung.substr(0,1) == " " || vor_Sortierung.substr(0,1) == "`"){
				vor_Sortierung = vor_Sortierung.substr(1);
			}
			while (vor_Sortierung.substr(0,1) != "`"){
				Feldname = Feldname + vor_Sortierung.substr(0,1);
				vor_Sortierung = vor_Sortierung.substr(1);
			}
			document.getElementById("feld_" + Zeile).value = Feldname;
			while (vor_Sortierung.substr(0,1) == " " || vor_Sortierung.substr(0,1) == "`"){
				vor_Sortierung = vor_Sortierung.substr(1);
			}
			if (vor_Sortierung.substr(0,3) == "ASC") {
				Sort = "ASC";
				vor_Sortierung = vor_Sortierung.substr(4);
			} else {
				Sort = "DESC";
				vor_Sortierung = vor_Sortierung.substr(5);
			}
			document.getElementById("sort_" + Zeile).value = Sort;
			Zeile = Zeile + 1;
		}
	}
}

function Filter_Dialog_oeffnen() {
	var op = "<option>=</option><option><</option><option><=</option><option>></option><option>>=</option><option>like</option><option>not like</option>";
	var Feldnamen = document.getElementById("datenfeldnamen").value;
	if (Feldnamen.substr(Feldnamen.length - 1) == ",") {Feldnamen = Feldnamen.substr(0,Feldnamen.length - 1);}
	Feldnamen = Feldnamen.split(",");
	var Feldliste = "<option></option>";
	for (i = 0; i < Feldnamen.length; i++) {
		Feldliste = Feldliste + "<option>" + Feldnamen[i] + "</option>";
	}
	Filterliste = Filterliste_einlesen();
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px'><table class='table' cellpadding = '3' width='525px'>\n";
	Filtertext = "";
	if (gelesene_Filter.length > 0) {Filtertext = gelesene_Filter[0]["Filtertext"];}
	Inhalt = Inhalt + "<tr><td colspan='6'><textarea class='Text_einfach' id='filtertext' name='Filtertext' style='height: 80px; width: 100%;' ondblclick='Filterfelder_aktualisieren();' onfocusout='Filterfelder_aktualisieren();'>" + Filtertext + "</textarea></td></tr>\n";
	Inhalt = Inhalt + "<tr><td colspan='6'><span id='meldung' style='display: none;'><b><font color='#ff0000'>" + T_Text[30] + "</font></b></span></td></tr>\n";
	Inhalt = Inhalt + "<tr class='Tabelle_Ueberschrift' style='height: 50px;' valign = 'bottom'><td align = 'center'>(</td><td align = 'center'>" + T_Text[26] + "</td><td align = 'center'>" + T_Text[31] + "</td><td align = 'center'>)</td><td align = 'center'></td></tr>\n";
	for (i = 1; i < Filterzeilen + 1; i++) {
		Inhalt = Inhalt + "<tr><td><input class='Text_Element' id='klammer_auf_" + i + "' name='Klammer_auf_" + i + "' size='3' value='' type='Text' onfocusout='Filtertext_aktualisieren();'></td>\n";
		Inhalt = Inhalt + "<td><select class='Auswahl_Liste_Element' id='feld_" + i + "' name='Feld_" + i + "' onfocusout='Filtertext_aktualisieren();'>" + Feldliste + "</select></td>\n";
		Inhalt = Inhalt + "<td><select class='Auswahl_Liste_Element' value='' id='oper_" + i + "' name='Oper' onfocusout='Filtertext_aktualisieren();'>" + op + "</select></td>\n";
		Inhalt = Inhalt + "<td><input class='Text_Element' id='wert_" + i + "' name='Wert_" + i + "' value='' type='Text' onfocusout='Filtertext_aktualisieren();'></td>\n";
		Inhalt = Inhalt + "<td><input class='Text_Element' id='klammer_zu_" + i + "' name='Klammer_zu_" + i + "' size='3' value='' type='Text' onfocusout='Filtertext_aktualisieren();'></td>\n";
		if (i < Filterzeilen) {
			Inhalt = Inhalt + "<td><select class='Auswahl_Liste_Element' value='' id='verknuepfung_" + i + "' name='Verknuepfung_" + i + "' onfocusout='Filtertext_aktualisieren();'><option>AND</option><option>OR</option></select></td></tr>\n";
		} else {
			Inhalt = Inhalt + "<td></td></tr>\n";
		}
	}
	Inhalt = Inhalt + "<tr><td colspan='6'><table width='100%'><tr>\n";
	Inhalt = Inhalt + "<tr><td><input class='Schalter_Element' type ='button' id='filter_uebernehmen' name='Filter_uebernehmen' value='" + T_Text[32] + "' onclick='Filtertext_uebernehmen();'></td>\n";
	Inhalt = Inhalt + "<td><input class='Schalter_Element' type ='button' id='filter_speichern' name='Filter_speichern' value='" + T_Text[33] + "' onclick='Filtertext_speichern();'></td>\n";
	Inhalt = Inhalt + "<td><input class='Schalter_Element' type ='button' id='filter_loeschen' name='Filter_loeschen' value='" + T_Text[2] + "' onclick='Filter_loeschen();'></td>\n";
	Inhalt = Inhalt + "<td align='right'>" + T_Text[34] + "</td><td><select class='Auswahl_Liste_Element' id='filter_laden' name='Filter_laden' value='" + T_Text[34] + "' onchange='Filter_laden();'>" + Filterliste + "</td></tr></table></td></tr>\n";
	Inhalt = Inhalt + "</table></div>\n";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Filter_Dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '545 530',
		headerTitle: T_Text[35],
		content:  Inhalt,
	});
}

function Filtertext_aktualisieren() {
	fertig = 0;
	i = 1;
	Klammern = 0;
	document.getElementById("filtertext").value = "";
	while(i <= Filterzeilen && fertig == 0) {
		akzeptiert = "";
		while (document.getElementById("klammer_auf_" + i).value.length > 0){
			if (document.getElementById("klammer_auf_" + i).value.substr(0,1) == "(") {
				akzeptiert = akzeptiert + "(";
				Klammern = Klammern + 1;
			}
			document.getElementById("klammer_auf_" + i).value = document.getElementById("klammer_auf_" + i).value.substr(1);
		}
		document.getElementById("klammer_auf_" + i).value = akzeptiert;
		akzeptiert = "";
		while (document.getElementById("klammer_zu_" + i).value.length > 0){
			if (document.getElementById("klammer_zu_" + i).value.substr(0,1) == ")") {
				akzeptiert = akzeptiert + ")";
				Klammern = Klammern - 1;
			}
			document.getElementById("klammer_zu_" + i).value = document.getElementById("klammer_zu_" + i).value.substr(1);
		}
		document.getElementById("klammer_zu_" + i).value = akzeptiert;
		if (Klammern != 0) {
			document.getElementById("meldung").style.display = "block";
		} else {
			document.getElementById("meldung").style.display = "none";
		}
		document.getElementById("wert_" + i).value = document.getElementById("wert_" + i).value.replace(/\"/g,"'");
		document.getElementById("filtertext").value = document.getElementById("filtertext").value + document.getElementById("klammer_auf_" + i).value + "`" + document.getElementById("feld_" + i).value + "` " + document.getElementById("oper_" + i).value + " " + document.getElementById("wert_" + i).value + document.getElementById("klammer_zu_" + i).value;
		if (i < Filterzeilen) {
			if (document.getElementById("feld_" + (i + 1).toString()).value > "") {
				document.getElementById("filtertext").value = document.getElementById("filtertext").value + " " + document.getElementById("verknuepfung_" + i).value + " ";
			} else {
				fertig = 1;
			}
		}
		i = i + 1;
	}
}

function Filtertext_speichern() {
	try {
		var Filtername = prompt(T_Text[36],document.getElementById('filtername').value);
	} catch (err) {
		var Filtername = prompt(T_Text[36]);
	}
	var Filtertext = document.getElementById('filtertext').value;
	Filtertext = Filtertext.replace(/'/g,'"');
	SQL_Text = "INSERT INTO `Userdef_Filter` (`Server_ID`, `Baum_ID`, `User_ID`, `Filtername`, `Filtertext`) VALUES (" + document.getElementById("Server_ID").value + "," + document.getElementById("Baum_ID").value + "," + document.getElementById("benutzer").value + ",'" + Filtername + "','" +Filtertext + "');";
	jQuery.ajax({
		url: "SQL_ausfuehren.php",
		type: 'POST',
		data: {SQL: SQL_Text, DB: 'unidb', Replikation: document.getElementById("replikation").value},
		async: false
	});
	document.getElementById("filter_laden").innerHTML = Filterliste_einlesen();
  	document.getElementById("filter_laden").value = Filtername;
}

function Filter_loeschen() {
	SQL_Text = "DELETE FROM `Userdef_Filter` WHERE `Baum_ID` = " + document.getElementById("Baum_ID").value + " AND `User_ID` = " + document.getElementById("benutzer").value + " AND `Filtername` = '" + document.getElementById("filter_laden").value + "' AND `Server_ID` = " + document.getElementById("Server_ID").value + ";";
	jQuery.ajax({
		url: "SQL_ausfuehren.php",
		type: 'POST',
		data: {SQL: SQL_Text, DB: 'unidb', Replikation: document.getElementById("replikation").value},
		async: false
	});
	document.getElementById("filter_laden").innerHTML = Filterliste_einlesen();
}

function Filter_laden() {
	for (i = 0; i < gelesene_Filter.length; i++) {
		if (gelesene_Filter[i]["Filtername"] == document.getElementById("filter_laden").value) {
			document.getElementById("filtertext").value = gelesene_Filter[i]["Filtertext"];
			i = gelesene_Filter.length;
		}
	}
	Filterfelder_aktualisieren();
}

function Filtertext_uebernehmen() {
	var Filtername = prompt(T_Text[36], document.getElementById('filter_laden').value);
	var Filtertext = document.getElementById('filtertext').value;
	Filtertext = Filtertext.replace(/'/g,'"');
	SQL_Text = "UPDATE `Userdef_Filter` SET `Filtertext` = '" + Filtertext + "', `Filtername` = '" + Filtername + "' WHERE `Baum_ID` = " + document.getElementById("Baum_ID").value + " AND `User_ID` = " + document.getElementById("benutzer").value + " AND `Filtername` = '" + document.getElementById("filter_laden").value + "' AND `Server_ID` = " + document.getElementById("Server_ID").value + ";";
	jQuery.ajax({
		url: "SQL_ausfuehren.php",
		type: 'POST',
		data: {SQL: SQL_Text, DB: 'unidb', Replikation: document.getElementById("replikation").value},
		async: false
	});
	document.getElementById("filter_laden").innerHTML = Filterliste_einlesen();
}

function Filterfelder_aktualisieren() {
	var Text = document.getElementById("filtertext").value;
	Zeile = 1;
	for (i = 1; i < Filterzeilen + 1; i++) {
		document.getElementById("klammer_auf_" + i).value = "";
		document.getElementById("feld_" + i).value = "";
		document.getElementById("oper_" + i).value = "";
		document.getElementById("wert_" + i).value = "";
		document.getElementById("klammer_zu_" + i).value = "";
		if (i < Filterzeilen) {document.getElementById("verknuepfung_" + i).value = "AND";}
	}
	while (Text.length > 0) {
		gewaehlt = "";
		while (Text.substr(0,1) == "(") {
			gewaehlt = gewaehlt + Text.substr(0,1);
			Text = Text.substr(1);
		}
		document.getElementById("klammer_auf_" + Zeile).value = gewaehlt;
		gewaehlt = "";
		while (Text.substr(0,1) == " " || Text.substr(0,1) == "`"){
			Text = Text.substr(1);
		}
		while (Text.substr(0,1) != "`" && Text.length > 0) {
			gewaehlt = gewaehlt + Text.substr(0,1);
			Text = Text.substr(1);
		}
		document.getElementById("feld_" + Zeile).value = gewaehlt;
		gewaehlt = "";
		while ((Text.substr(0,1) == " " || Text.substr(0,1) == "`") && Text.length > 0){
			Text = Text.substr(1);
		}
		while (Text.substr(0,1) != " " && Text.length > 0) {
			gewaehlt = gewaehlt + Text.substr(0,1);
			Text = Text.substr(1);
		}
		document.getElementById("oper_" + Zeile).value = gewaehlt;
		gewaehlt = "";
		while (Text.substr(0,1) == " ") {
			Text = Text.substr(1);
		}
		while (Text.substr(0,1) != ")" && Text.substr(0,4) != " AND" && Text.substr(0,3) != " OR" && Text.length > 0){
			gewaehlt = gewaehlt + Text.substr(0,1);
			Text = Text.substr(1);
		}
		document.getElementById("wert_" + Zeile).value = gewaehlt;
		if (Text.length > 0) {
			gewaehlt = "";
			while (Text.substr(0,1) == ")") {
				gewaehlt = gewaehlt + Text.substr(0,1);
				Text = Text.substr(1);
			}
			document.getElementById("klammer_zu_" + Zeile).value = gewaehlt;
		}
		if (Text.length > 0) {
			if (Text.substr(0,4) == " AND") {
				document.getElementById("verknuepfung_" + Zeile).value = "AND";
				Text = Text.substr(5);
			}
			if (Text.substr(0,3) == " OR") {
				document.getElementById("verknuepfung_" + Zeile).value = "OR";
				Text = Text.substr(4);
			}
		}
		Zeile = Zeile + 1;
	}
}

function Filterliste_einlesen() {
	jQuery.ajax({
		url: "./Filter_lesen.php?Baum_ID=" + document.getElementById("Baum_ID").value + "&Server_ID=" + document.getElementById("Server_ID").value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
  	gelesene_Filter = JSON.parse(strReturn);
  	var Filterliste = ""
  	for (i = 0; i < gelesene_Filter.length; i++) {
  		Filterliste = Filterliste + "<option>" + gelesene_Filter[i]["Filtername"] + "</option>\n";
  	}
  	return Filterliste;
}

function Sortierung_uebernehmen() {
	var Sortierung = "ORDER BY ";
	for (i = 1; i < Filterzeilen + 1; i++) {
		if (document.getElementById("feld_" + i).value > "") {Sortierung = Sortierung + "`" + document.getElementById("feld_" + i).value + "` " + document.getElementById("sort_" + i).value + ", ";}
	}
	if (Sortierung != "ORDER BY ") {
 		Sortierung = Sortierung.substr(0, Sortierung.length - 2);
	} else {
		Sortierung = "";
	}
	document.getElementById("sortierungen").value = Sortierung;
	document.forms.Einstellungen.submit();
}

function Zeile_markieren(Zeile) {
	document.getElementById("Zeile" + document.getElementById("nav_Satz").value).setAttribute("class", "");
	document.getElementById("Zeile" + Zeile).setAttribute("class", "Tabellenzeile");
	document.getElementById("nav_Satz").value = Zeile;
}

function zur_Formularansicht(Zeile) {
	document.forms.Einstellungen.Ansicht.value = T_Text[16];
	document.forms.Einstellungen.Darstellung.value = T_Text[16];
	document.forms.Einstellungen.submit();
}

function Taste_gedrueckt(event) {
	//event.preventDefault();
	if (event.key == "ArrowDown") {
		if (parseInt(document.getElementById("nav_Satz").value) < parseInt(document.getElementById("saetze").innerHTML)) {Zeile_markieren(parseInt(document.getElementById("nav_Satz").value) + 1);}
	} else {
		if (event.key == "ArrowUp") {
			if (document.getElementById("nav_Satz").value > 1) {Zeile_markieren(parseInt(document.getElementById("nav_Satz").value) - 1);}
		} else {
			if (event.key == "PageDown") {
				if (parseInt(document.getElementById("nav_Satz").value) + 10 < parseInt(document.getElementById("saetze").innerHTML)) {
					Zeile_markieren(parseInt(document.getElementById("nav_Satz").value) + 10);
				} else {
					if (parseInt(document.getElementById("nav_Satz").value) < parseInt(document.getElementById("saetze").innerHTML)) {Zeile_markieren(parseInt(document.getElementById("saetze").innerHTML));}
				}
			} else {
				if (event.key == "PageUp") {
					if (document.getElementById("nav_Satz").value > 10) {
						Zeile_markieren(parseInt(document.getElementById("nav_Satz").value) - 10);
					} else {
						if (parseInt(document.getElementById("nav_Satz").value) > 1) {Zeile_markieren(1);}
					}
				}
			}
		}
	}
}

function Ansicht_umschalten() {
	if (document.forms.Einstellungen.Ansicht.value == T_Text[9]) {
		document.forms.Einstellungen.Darstellung.value = T_Text[9];
		document.forms.Einstellungen.Ansicht.value = T_Text[16];
	} else {
		document.forms.Einstellungen.Darstellung.value = T_Text[16];
		document.forms.Einstellungen.Ansicht.value = T_Text[9];
	}
	document.forms.Einstellungen.submit();
}
		
function Text_bearbeiten(id) {
	Dialog = jsPanel.create({
		onbeforeclose: function() {
			try {document.getElementById(id).innerHTML = document.getElementsByClassName("tox-edit-area")[0].firstChild.contentDocument.getElementById("tinymce").innerHTML;} catch (err) {}
			x = document.getElementById('edit_dialog');
			x.parentElement.removeChild(x);
		},
		dragit: {
        	snap: true
        },
		id: 'edit_dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '700 400',
		headerTitle: T_Text[3],
		content: '<textarea id="editor' + editnr + '">' + document.getElementById(id).innerHTML + '</textarea>'
   });
	tinymce.init({
		selector: 'textarea#editor' + editnr,
		language: 'de',
		plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link codesample table charmap nonbreaking anchor insertdatetime advlist lists help charmap quickbars',
		menubar: 'edit view insert format tools table help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap | fullscreen  preview print | insertfile image link anchor codesample',
		toolbar_sticky: true,
		quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		toolbar_mode: 'sliding',
		contextmenu: 'link image table'
	});
	editnr = editnr + 1;
}

function Registerkarte_wechseln(id, Stelle, Eltern_ID) {
	if (typeof Eltern_ID === 'object') {Eltern_ID = Eltern_ID.id;}
	if (typeof id === 'object') {id = id.id;}	
	var Tabellenzeile = document.getElementById("kopftabelle_" + Eltern_ID).firstChild.firstChild;
	for (x = 0; x < document.getElementById(Eltern_ID).childNodes.length; x++) {
		if (document.getElementById(Eltern_ID).childNodes[x].id.substr(0,12) != "kopftabelle_") {
			document.getElementById(Eltern_ID).childNodes[x].style.display = "none";
		} else {
			var Tabpos = x;
		}
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
}

function Felder_fuellen() {
	x = 0;
	var Eltern = [];
	Felder = [];
	F = -1;
	Unterformulare = [];
	y = 0;
	var Bereich = document.getElementById("db_Bereich");
	var indexfeld = document.getElementById("indexfeld").value;
	if (Bereich != null) {
		for (i = 0; i < Bereich.childNodes.length; i++) {
			try {
				if (Bereich.childNodes[i].attributes["feldtyp"].value == "Unterformular") {
					Unterformulare[y] = Bereich.childNodes[i].id;
					y = y +1;
				}
			} catch (err) {}
			try {
				if (Bereich.childNodes[i].attributes["feldtyp"].value == "Listenfeld" || Bereich.childNodes[i].attributes["feldtyp"].value == "Kombinationsfeld") {
					SQL = "";
					Werteliste = "";
					try {
						SQL = Bereich.childNodes[i].attributes.sql.value;
					} catch (err) {
						Werteliste = Bereich.childNodes[i].attributes.werteliste.value;
					}
					if (SQL.length > 0) {
						jQuery.ajax({
							url: "Select_einlesen.php",
								success: function (html) {
			   				strReturn = html;
							},
							type: 'POST',
							data: {query: SQL},
							async: false
						});
						Bereich.childNodes[i].innerHTML = strReturn;
					}
					if (Werteliste.length > 0) {
						Liste = Werteliste.split(";");
						Werteliste = "";
						for (Z = 0; Z < Liste.length; Z++) {
							Werteliste = Werteliste + "<option>" + Liste[Z] + "</option>";
						}
						Bereich.childNodes[i].innerHTML = Werteliste;
					}
					if (Bereich.childNodes[i].attributes.feldtyp.value == "Kombinationsfeld") {$('#' + Bereich.childNodes[i].id).editableSelect();}
				}
				} catch (err) {}
			try {
				if (Bereich.childNodes[i].hasAttribute("feldtyp") == true) {
					if (Bereich.childNodes[i].attributes.feldtyp.value == "Option") {
						if(htmlspecialchars_decode(document.getElementById("schatten_" + Bereich.attributes.feld.value).value) == Bereich.childNodes[i].value) {
							Bereich.childNodes[i].checked = true;
						} else {
							Bereich.childNodes[i].checked = false;
						}
					} else {
						if (Bereich.childNodes[i].attributes.feldtyp.value == "Checkbox") {
							if(document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value == 1) {
								Bereich.childNodes[i].checked = true;
							} else {
								Bereich.childNodes[i].checked = false;
							}
							Felder[F] = Bereich.childNodes[i];
						} else {
							if (Bereich.childNodes[i].hasAttribute("feld") == true) {
								F = F + 1;
								Felder[F] = Bereich.childNodes[i];
								if (Bereich.childNodes[i].attributes["feldtyp"].value != "Feld_anzeigen" && Bereich.childNodes[i].attributes["feldtyp"].value != "Option" && Bereich.childNodes[i].attributes["feldtyp"].value != "Checkbox" && Bereich.childNodes[i].attributes["feldtyp"].value != "Textarea") {
									if (Bereich.childNodes[i].attributes["feldtyp"].value == "Optionsgruppe") {
										Bereich.childNodes[i].attributes.value.value = document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value;
										Bereich.childNodes[i].value = document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value;
									} else {
										Bereich.childNodes[i].value = htmlspecialchars_decode(document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value);
									}
								} else {
									if(Bereich.childNodes[i].attributes["feldtyp"].value == "Textarea" || Bereich.childNodes[i].attributes["feldtyp"].value == "Feld_anzeigen") {Bereich.childNodes[i].innerHTML = htmlspecialchars_decode(document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value);}
									if(Bereich.childNodes[i].attributes["feldtyp"].value == "Checkbox" && document.getElementById("schatten_" + Bereich.childNodes[i].attributes.feld.value).value == "1") {Bereich.childNodes[i].checked = true;}
								}
							}
						}
					}
				}
				if (Bereich.childNodes[i].childNodes.length > 0) {
					Eltern[x] = Bereich.childNodes[i];
					x = x + 1;
				}
			} catch (err) {}
		}
	}
	while (Eltern.length > 0) {
		for (i = 0; i < Eltern[0].childNodes.length; i++) {
			try {
				if (Eltern[0].childNodes[i].attributes["feldtyp"].value == "Unterformular") {
					Unterformulare[y] = Eltern[0].childNodes[i].id;
					y = y +1;
				}
			} catch (err) {}
			try {
				if (Eltern[0].childNodes[i].attributes["feldtyp"].value == "Listenfeld" || Eltern[0].childNodes[i].attributes["feldtyp"].value == "Kombinationsfeld") {
					SQL = "";
					Werteliste = "";
					try {
						SQL = Eltern[0].childNodes[i].attributes.sql.value;
					} catch (err) {
						Werteliste = Eltern[0].childNodes[i].attributes.werteliste.value;
					}
					if (SQL.length > 0) {
						jQuery.ajax({
							url: "Select_einlesen.php",
							success: function (html) {
			   				strReturn = html;
							},
							type: 'POST',
							data: {query: SQL},
							async: false
						});
						Eltern[0].innerHTML = strReturn;
					}
					if (Werteliste.length > 0) {
						Liste = Werteliste.split(";");
						Werteliste = "";
						for (Z = 0; Z < Liste.length; Z++) {
							Werteliste = Werteliste + "<option>" + Liste[Z] + "</option>";
						}
						Eltern[0].childNodes[i].innerHTML = Werteliste;
					}
					if (Eltern[0].childNodes[i].attributes.feldtyp.value == "Kombinationsfeld") {$('#' + Eltern[0].childNodes[i].id).editableSelect();}
				}
			} catch (err) {}
			try {
				if (Eltern[0].childNodes[i].hasAttribute("feldtyp") == true) {
					if (Eltern[0].childNodes[i].attributes.feldtyp.value == "Option") {
						if(htmlspecialchars_decode((document.getElementById("schatten_" + Eltern[0].attributes.feld.value).value)) == Eltern[0].childNodes[i].value) {
							Eltern[0].childNodes[i].checked = true;
						} else {
							Eltern[0].childNodes[i].checked = false;
						}
					} else {
						if (Eltern[0].childNodes[i].hasAttribute("feld") == true) {
							F = F + 1;
							Felder[F] = Eltern[0].childNodes[i];
							if (Eltern[0].childNodes[i].attributes["feldtyp"].value != "Feld_anzeigen" && Eltern[0].childNodes[i].attributes["feldtyp"].value != "Option" && Eltern[0].childNodes[i].attributes["feldtyp"].value != "Checkbox" && Eltern[0].childNodes[i].attributes["feldtyp"].value != "Textarea") {
								if (Eltern[0].childNodes[i].attributes["feldtyp"].value == "Optionsgruppe") {
									Eltern[0].childNodes[i].attributes.value.value = htmlspecialchars_decode(document.getElementById("schatten_" + Eltern[0].childNodes[i].attributes.feld.value).value);
									Eltern[0].childNodes[i].value = htmlspecialchars_decode(document.getElementById("schatten_" + Eltern[0].childNodes[i].attributes.feld.value).value);
								} else {
									Eltern[0].childNodes[i].value = htmlspecialchars_decode(document.getElementById("schatten_" + Eltern[0].childNodes[i].attributes.feld.value).value);
								}
							} else {
								if(Eltern[0].childNodes[i].attributes["feldtyp"].value == "Textarea" || Eltern[0].childNodes[i].attributes["feldtyp"].value == "Feld_anzeigen") {Eltern[0].childNodes[i].innerHTML = htmlspecialchars_decode(document.getElementById("schatten_" + Eltern[0].childNodes[i].attributes.feld.value).value);}
								if(Eltern[0].childNodes[i].attributes["feldtyp"].value == "Checkbox" && document.getElementById("schatten_" + Eltern[0].childNodes[i].attributes.feld.value).value == "1") {Eltern[0].childNodes[i].checked = true;}
							}
						}
					}
				}
			} catch (err) {}
			if (Eltern[0].childNodes[i].childNodes.length > 0) {
				Eltern[Eltern.length] = Eltern[0].childNodes[i];
			}
		}
		Eltern.splice(0,1);
	}
	try {document.getElementById("indexwert").value = htmlspecialchars_decode(document.getElementById("schatten_" + indexfeld).value);} catch (err) {}
	//berechnete Felder berechnen
	for (i = 0; i < Bereich.childNodes.length; i++) {
		try {
			if (Bereich.childNodes[i].attributes.feldtyp.value == "berechnet") {
				Wert = Function('return ' + Bereich.childNodes[i].attributes.formel.value)();
				//Wert = eval(Bereich.childNodes[i].attributes.formel.value);
				//if (Wert != NaN) {Bereich.childNodes[i].innerHTML = Wert;}
				Bereich.childNodes[i].innerHTML = Wert;
			}
		} catch (err) {}
	}
	//Ende Berechnungen
	bed_Formatierung();
	current();
	for (i = 0; i < Unterformulare.length; i++) {
		Filter = document.getElementById(Unterformulare[i]).attributes.verkn_feld_unterform.value;
		Filterfeld = document.getElementById(Unterformulare[i]).attributes.verkn_feld.value;
		//Zusatzfelder im Unterformular füllen
		try {
			UForm = document.getElementById(Unterformulare[i]).contentDocument;
			UForm.getElementById("verkn_feld_Wert").value = htmlspecialchars_decode(document.getElementById("schatten_" + Filterfeld).value);
			UForm.getElementById("verkn_feld").value = Filter;
		} catch (err) {}
		//Ende Zusatzfelder
		UForm = document.getElementById(Unterformulare[i]).contentWindow;
		Datenquelle = UForm.document.getElementById("datenquelle").value.replace(/\"/g,"'");
		if (Datenquelle.substr(-1,1) == ";") {Datenquelle = Datenquelle.substr(0,Datenquelle.length - 1);}
		pos1 = Datenquelle.toLocaleLowerCase().indexOf("where");
		if (pos1 == -1) {
			pos1 = Datenquelle.toLocaleLowerCase().indexOf("group");
			if (pos1 == -1) {pos1 = Datenquelle.toLocaleLowerCase().indexOf("order");}
			if (pos1 == -1) {pos1 = Datenquelle.toLocaleLowerCase().indexOf("limit");}
			if (pos1 == -1) {pos1 = Datenquelle.length;}
		}
		links = Datenquelle.substr(0, pos1);
		pos2 = Datenquelle.toLocaleLowerCase().indexOf("group");
		if (pos2 == -1) {pos2 = Datenquelle.toLocaleLowerCase().indexOf("order");}
		if (pos2 == -1) {pos2 = Datenquelle.toLocaleLowerCase().indexOf("limit");}
		if (pos2 == -1) {pos2 = Datenquelle.length;}
		Mitte = "";
		if (pos2 - pos1 > 6) {Mitte = Datenquelle.substr(pos1 + 6,pos2 - pos1 - 7);}
		if (Mitte.length > 0) {
			pos3 = Mitte.toLocaleLowerCase().indexOf(Filter.toLocaleLowerCase());
			if (pos3 > -1) {
				mlinks = Mitte.substr(0,pos3);
				if (mlinks.substr(-1,1) == "`") {mlinks = mlinks.substr(0,mlinks.length - 1);}
				mrechts = Mitte.substr(mlinks.length + Filter.length,Mitte.length);
				if (mrechts.substr(0,1) == "`") {mrechts = mrechts.substr(1,mrechts.length);}
				while (mrechts.substr(0,1) == " ") {
					mrechts = mrechts.substr(1,mrechts.length);
				}
				while (mrechts.toLocaleLowerCase().substr(0,5) != " and " && mrechts.toLocaleLowerCase().substr(0,4) != " or " && mrechts.toLocaleLowerCase().substr(0,6) != " order" && mrechts.toLocaleLowerCase().substr(0,6) != " group" && mrechts.toLocaleLowerCase().substr(0,6) != " limit" && mrechts.length > 0) {
					mrechts = mrechts.substr(1,mrechts.length);
				}
				if (mrechts.toLocaleLowerCase().indexOf(" limit") > -1) {mrechts = mrechts.substr(0,mrechts.toLocaleLowerCase().indexOf(" limit"));}
				Mitte = mlinks + mrechts;
				if (Mitte.length > 0) {Mitte = " AND " + Mitte;}
			}
		}
		rechts = " " + Datenquelle.substr(pos2,Datenquelle.length);
		if (rechts.toLocaleLowerCase().indexOf(" limit") > -1) {rechts = rechts.substr(0,rechts.toLocaleLowerCase().indexOf(" limit"));}
		Filterwert = htmlspecialchars_decode(document.getElementById("schatten_" + Filterfeld).value);
		Datenquelle = links + "WHERE `" + Filter + "` = '" + Filterwert + "' AND " + Mitte + rechts + ";";
		Datenquelle1 = "SELECT COUNT(`" + Filter + "`) AS `Anzahl` " + Datenquelle.substr(Datenquelle.toLocaleLowerCase().indexOf("from"),Datenquelle.length);
		//Anzahl der Datensaetze im Unterformular ermitteln und eintragen
		jQuery.ajax({
			url: "Datensatz_lesen.php",
			success: function (html) {
   		strReturn = html;
			},
			type: 'POST',
			data: {SQL: Datenquelle1},
			async: false
		});
		if (strReturn.length > 2) {
			Werte = JSON.parse(strReturn);
			UForm.document.getElementById("saetze").innerHTML = Werte[0].Wert;
			UForm.document.getElementById("nav_Satz").value = "1";
		} else {
			UForm.document.getElementById("saetze").innerHTML = "0";
			UForm.document.getElementById("nav_Satz").value = "0";
		}
		Datenquelle = Datenquelle.replace(/AND  AND/g,'\AND');
		Datenquelle = Datenquelle.replace(/AND AND/g,'\AND');
		Datenquelle = Datenquelle.replace(/AND  AND/g,'\AND');
		UForm.document.getElementById("datenquelle").value = Datenquelle.replace(/'/g,'\"');
		UForm.document.forms.Einstellungen.submit();
		
	}
}

function htmlspecialchars(str) {
	var map = {
		"&": "&amp;",
		"<": "&lt;",
		">": "&gt;",
		"\"": "&quot;",
		"'": "&#39;" // ' -> &apos; for XML only
	};
	return str.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function htmlspecialchars_decode(str) {
	var map = {
		"&amp;": "&",
		"&lt;": "<",
		"&gt;": ">",
		"&quot;": "\"",
		"&#39;": "'"
	};
	return str.replace(/(&amp;|&lt;|&gt;|&quot;|&#39;)/g, function(m) { return map[m]; });
}

function Elem(Elementname) {
	Bereich = document.getElementById("db_Bereich");
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
			Wert = parseFloat(document.getElementById(Elem(Elementname)).innerHTML);
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
		Wert = Function('return ' + Bedingungen[i].Bedingung)();
		if (Wert == true) {
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

function menue_sichtbar(menue) {
	if (document.getElementById(menue).style.display == "block") {
		document.getElementById(menue).style.display = "none";
		document.getElementById("datei").style.fontWeight = "normal";
		document.getElementById("datei").style.borderStyle = "outset";
		
	} else {
		document.getElementById(menue).style.display = "block";
		document.getElementById("datei").style.fontWeight = "bold";
		document.getElementById("datei").style.borderStyle = "inset";
	}
}

function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("navigation").style.display == "block") {
			document.getElementById("navigation").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("navigation").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("navigation").style.display = "none";
		document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 2) {
		if (document.getElementById("filter_sortierung").style.display == "block") {
			document.getElementById("filter_sortierung").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("filter_sortierung").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("filter_sortierung").style.display = "none";
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
