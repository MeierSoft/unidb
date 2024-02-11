$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	try {document.getElementById('detailbereich').style.left = (document.getElementById('auswahlbereich').offsetWidth + 20).toString() + "px";} catch (err) {}
	try {document.getElementById('kopftabelle').style.left = (document.getElementById('auswahlbereich').offsetWidth + 20).toString() + "px";} catch (err) {}
	try {document.getElementById('feldschalter').style.top = (document.getElementById('dynfelddetail').offsetTop + document.getElementById('dynfelddetail').offsetHeight + 120).toString() + "px";} catch (err) {}
});

function escapeHtml(str) {
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};
	return str.replace(/[&<>"']/g, function(m) {return map[m];});
}

function decodeHtml(str) {
	var map = {
		'&amp;': '&',
		'&lt;': '<',
		'&gt;': '>',
		'&quot;': '"',
		'&#039;': "'"
	};
	return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

function auswahl_historie(Baum_ID,Server_ID) {
	if (document.getElementById("version").value == "aktuelle Version") {
		document.getElementById("hist_id_hidden").value = "";
		document.getElementById("tabelle").value = "Baum";
	} else {
		document.getElementById("hist_id_hidden").value = document.getElementById("version").value;
		document.getElementById("tabelle").value = "Baumhistorie";
	}
	document.getElementById("server_id").value = Server_ID;
	document.getElementById("baum_id").value = Baum_ID;
	document.Formular.submit();
}

function Feld_auswaehlen() {
	document.getElementById("dynfelddetail").value = decodeHtml(document.getElementById("feld_" + document.getElementById("dynFeldliste").value).value);
	try {document.getElementById('dynfelddetail').style.left = (document.getElementById('versionsliste').offsetWidth + 20).toString() + "px";} catch (err) {}
}

function dyn_Feld_entfernen(Tabelle,Hist_ID,Baum_ID,Server_ID) {
	var dyn_Feld = document.getElementById("feldname_" + document.getElementById("dynFeldliste").value).value;
	if (Tabelle == "Baum") {
		var SQL = "UPDATE `Baum` SET `Inhalt` = COLUMN_DELETE(`Inhalt`, '" + dyn_Feld + "') WHERE `Baum_ID`=" + Baum_ID + " AND `Server_ID`=" + Server_ID + ";";
	} else {
		var SQL = "UPDATE `Baumhistorie` SET `Inhalt` = COLUMN_DELETE(`Inhalt`, '" + dyn_Feld + "') WHERE `Hist_ID`=" + Hist_ID + ";";
	}
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=unidb",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.forms["Formular"].submit();
}

function dyn_Feld_einfuegen(Tabelle,Hist_ID,Baum_ID,Server_ID) {
	result = prompt(T_Text[14]);
	if (result == null || result == "") {return;}	
	if (Tabelle == "Baum") {
		var SQL = "UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'" + result + "', '') WHERE `Baum_ID`=" + Baum_ID + " AND `Server_ID`=" + Server_ID + ";";
	} else {
		var SQL = "UPDATE `Baumhistorie` SET `Inhalt` = COLUMN_ADD(`Inhalt`,'" + result + "', '') WHERE `Hist_ID`=" + Hist_ID + ";";
	}
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=unidb",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.forms["Formular"].submit();
}

function gel_umschalten() {
	if (document.getElementById("geloeschte").checked == true) {
		document.getElementById("geloeschte_wert").value = 1;
	} else {
		document.getElementById("geloeschte_wert").value = 0;
	}
	document.forms["Formular"].submit();
}

function uebertragen() {
	document.getElementById('feld_' + document.getElementById('dynFeldliste').value).value = decodeHtml(document.getElementById('dynfelddetail').value);
}

function anpassen() {
	try {document.getElementById('feldschalter').style.top = (document.getElementById('dynfelddetail').offsetTop + document.getElementById('dynfelddetail').offsetHeight + 120).toString() + "px";} catch (err) {}
}

function satz_loeschen(Tabelle,Hist_ID,Baum_ID,Server_ID) {
	result = confirm("Datensatz wirklich löschen?");
	if (result == true) {
		if (Tabelle == "Baum") {
			SQL = "DELETE FROM `Baum` WHERE `Baum_ID` = " + Baum_ID + " AND `Server_ID` = " + Server_ID + ";";
		} else {
			SQL = "DELETE FROM `Baumhistorie` WHERE `Hist_ID` = " + Hist_ID + ";";
		}
		jQuery.ajax({
			url: "./SQL_ausfuehren.php?DB=unidb",
			type: 'POST',
			data: {SQL_Text: SQL},
			async: false
		});
	}
}

function satz_speichern(Tabelle,Hist_ID,Baum_ID,Server_ID) {
	SQL = "UPDATE `"+ Tabelle + "` SET ";
	for(x = 0; x < parseInt(document.getElementById('anzahl_dyn_felder').value); x++) {
		SQL = SQL + "`Inhalt`=COLUMN_ADD(Inhalt,'" + document.getElementById("feldname_" + x.toString()).value + "', '" + document.getElementById("feld_" + x.toString()).value + "'), ";
	}
	SQL = SQL.substr(0,SQL.length - 2);
	if (Tabelle == "Baum") {
		SQL = SQL + " WHERE `Baum_ID` = " + Baum_ID + " AND `Server_ID` = " + Server_ID + ";";
	} else {
		SQL = SQL + " WHERE `Hist_ID` = " + Hist_ID + ";";
	}
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=unidb",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
}
