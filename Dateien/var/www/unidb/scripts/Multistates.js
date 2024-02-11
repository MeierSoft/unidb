var T_Text = new Array;

$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
});

function Gruppe_einlesen() {
	document.getElementById("phpform").submit();
}
function neues_Multistate() {
	var Bezeichnung = prompt(T_Text[14], "");
	if (Bezeichnung != null && Bezeichnung != "") {
		document.getElementById("gruppe_bezeichnung").value = Bezeichnung;
		document.getElementById("phpform").submit();
	}
}
function Bedingung_entfernen(Multistate_Detail_ID) {
	document.getElementById("multistate_detail_id").value = Multistate_Detail_ID;
	document.getElementById("phpform").submit();
}

function Bedingungeditieren(Gruppe_Liste, Multistate_detail_ID, Operant, Wert, Bild) {
	var Inhalt = "<div style='position: absolute; left: 10px; top: 10px;'><form id='form_edit' name='form_edit' action='Multistates.php' method='post' target='_self'>";
	Inhalt = Inhalt + "<input type='hidden' id='multistate_detail_id1' name='Multistate_Detail_ID1' value = '" + Multistate_detail_ID + "'>";
	Inhalt = Inhalt + "<input type='hidden' id='gruppe_liste' name='Gruppe_Liste' value = '" + Gruppe_Liste + "'>";
	Inhalt = Inhalt + "<table><tr><td>" + T_Text[10] + "</td><td><select class='Auswahl_Liste_Element' size='1' id='operant' name='Operant'>";
	if (Operant == "=") {
		Inhalt = Inhalt + "<option selected>=</option>";
	} else {
		Inhalt = Inhalt + "<option>=</option>";
	}
	if (Operant == "!=") {
		Inhalt = Inhalt + "<option selected>!=</option>";
	} else {
		Inhalt = Inhalt + "<option>!=</option>";
	}
	if (Operant == ">=") {
		Inhalt = Inhalt + "<option selected>>=</option>";
	} else {
		Inhalt = Inhalt + "<option>>=</option>";
	}
	if (Operant == ">") {
		Inhalt = Inhalt + "<option selected>></option>";
	} else {
		Inhalt = Inhalt + "<option>></option>";
	}
	if (Operant == "<=") {
		Inhalt = Inhalt + "<option selected><=</option>";
	} else {
		Inhalt = Inhalt + "<option><=</option>";
	}
	if (Operant == "<") {
		Inhalt = Inhalt + "<option selected><</option>";
	} else {
		Inhalt = Inhalt + "<option><</option>";
	}
	Inhalt = Inhalt + "</select></td></tr>";
	Inhalt = Inhalt + "<tr><td>" + T_Text[11] + "</td><td><input class='Text_Element' type='text' id='wert' name='Wert' value = '" + Wert + "'></td></tr>";
	Inhalt = Inhalt + "<tr><td>" + T_Text[12] + "</td><td><select class='Auswahl_Liste_Element' size='1' id='bild' name='Bild' onchange='Bild_hier_zeigen();'>";
	Dateien = document.getElementById("dateiliste").value.split(";");
	for (x = 0; x < Dateien.length; x++) {
		if (Dateien[x] !="." && Dateien[x] !="..") {
			Inhalt = Inhalt + "<option";
			if (Dateien[x] == Bild) {
				Inhalt = Inhalt + " selected";
			}
			Inhalt = Inhalt + ">" + Dateien[x] + "</option>";
		}
	}
	Inhalt = Inhalt + "</select></td></tr>";
	Inhalt = Inhalt + "<tr height='40px' valign = 'bottom'><td><input class='Schalter_Element' type='submit' value='" + T_Text[2] + "' name='Bedingung_editieren'></td><td><input class='Schalter_Element' type='button' value='" + T_Text[15] + "' name='abbrechen' onclick='Fenster_edit_schliessen();'></td></tr>";
	Inhalt = Inhalt + "</table></form></div>";
	Inhalt = Inhalt + "<div style='position: absolute; top: 10px; left: 270px;' id = 'bild_zeigen'></div>\n";

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Fenster_edit',
		theme: 'info',
		contentSize: '500 150',
		headerTitle: T_Text[16],
		position: 'left-top 100 30',
		content: Inhalt
	});
	Bild_hier_zeigen();
}

function Fenster_edit_schliessen() {
	try {Fenster_edit.close();} catch (err) {}
}

function Bild_zeigen(Datei) {
  	jsPanel.create({
		dragit: {
			snap: true
      },
		id: 'Vorschau',
		theme: 'info',
		contentSize: '200 200',
		headerTitle: T_Text[17],
		position: 'left-top 100 30',
		content:  "<img id='bild_angezeigt' src='" + Datei + "'>",
	});
	//nur wegen dem async, denn sonnst ist das Bild nicht komplett da wenn die nächsten Zeilen bearbeitet werden
	jQuery.ajax({
		url: Datei,
		success: function (html) {
   		Bild = html;
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

function Bild_hier_zeigen() {
	document.getElementById("bild_zeigen").innerHTML = "<img id='bild_angezeigt' src='" + "./" + document.getElementById("upload_folder").value + document.getElementById("bild").value + "'>";
	//nur wegen dem async, denn sonnst ist das Bild nicht komplett da wenn die nächsten Zeilen bearbeitet werden
	jQuery.ajax({
		url: "./" + document.getElementById("upload_folder").value + document.getElementById("bild").value,
		success: function (html) {
   		Bild = html;
		},
 		async: false
 	});
	//Ende des JS Wahnsinns
	if (document.getElementById("bild_angezeigt").width > 230) {
		document.getElementById("Fenster_edit").style.width = (document.getElementById("bild_angezeigt").width + 290).toString() + "px";
	} else {
		document.getElementById("Fenster_edit").style.width = "500px";
	}
	if (document.getElementById("bild_angezeigt").height > 140) {
		document.getElementById("Fenster_edit").style.height = (document.getElementById("bild_angezeigt").height + 50).toString() + "px";
	} else {
		document.getElementById("Fenster_edit").style.height = "180px";
	}
}

function Hilfe_Fenster(Hilfe_ID) {
	jQuery.ajax({
		url: "./Hilfe.php?Hilfe_ID=" + Hilfe_ID,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Hilfe_Fenster',
		theme: 'info',
		contentSize: '600 600',
		headerTitle: T_Text[9],
		content:  "<div style = 'position: relative; top: 20px; left: 20px'>" + strReturn + "</div>",
	});
}