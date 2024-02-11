var panel;
var T_Text = new Array;

$(window).on('load',function() {;
	T_Text = JSON.parse(document.getElementById("translation").value);
});

function uebertragen() {
	var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
	document.getElementById("tagname1").value = Ergebnis[0] + Ergebnis[1];
	jsPanel.activePanels.getPanel("Tagsuche").close();
}

function Eingabefeld_sichtbar() {
	var strReturn = "";
	jQuery.ajax({
		url: "./Test_Tag_suchen.php?Suchtext=" + document.getElementById("tagname1").value,
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
  		callback: function (panel) {
			$(this).on('mouseleave', function () {
				try {
					panel.close('Tagsuche');
				} catch (err) {}
			});
	  	}
	});
}
	
function Tagdetails(Point_ID) {
	try {
		panel.close('tagdetails');
	} catch (err) {}
	jQuery.ajax({
		url: "./DH_Tagdetails.php?Point_ID=" + Point_ID,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	panel = jsPanel.create({
		dragit: {
     		snap: true
     	},
		id: 'tagdetails',
		position: 'left-top 10 10',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '600 600',
		headerTitle: T_Text[17],
		content: strReturn,
	});
}

function Tag_zuordnen(var_nummer, var_name, Geraetenummer) {
	try {
		panel.close('tag_zuordnen_dialog');
	} catch (err) {}
	var Inhalt = "<form action='./Geraete_Einstellungen.php' name='Formular_Tag_zuordnen' id='formular_tag_zuordnen' method='post' target='_self'><div style = 'position: relative; top: 10px; left: 10px'><table><tr><td>" + T_Text[18] + ":<font size = '-2'><br><i>" + T_Text[19] + "</i></font></td></tr>";
	Inhalt += "<tr><td><input class='Text_Element' id='tagname1' name='Tagname1' type='text' value='' style = 'width: 300px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='Schalter_Element' value='" + T_Text[35] + "' type='button' name='Tag_suchen' onclick='Eingabefeld_sichtbar()'></td></tr>";
	Inhalt += "<tr><td colspan = '2' style='height: 30px;'><hr></td></tr>";
	Inhalt += "<tr><td>" + T_Text[20] + ":</td></tr>";
	Inhalt += "<tr><td><table><tr><td class='Text_einfach' align='right'>" + T_Text[21] + ":</td><td colspan = '3' valign='top'><input class='Text_Element' id='tagname2' name='Tagname2' type='text' value='' style = 'width: 300px;'></td></tr>";
	Inhalt += "<tr><td class='Text_einfach' align='right'>" + T_Text[22] + ":</td><td colspan = '3'><input class='Text_Element' id='pfad' name='Pfad' type='text' value='' style = 'width: 300px;'></td></tr>";	
	Inhalt += "<tr><td class='Text_einfach' align='right'>" + T_Text[23] + ":</td><td colspan = '3'><input class='Text_Element' id='beschreibung' name='Beschreibung' type='text' value='' style = 'width: 300px;'></td></tr>";
	Inhalt += "<tr><td class='Text_einfach' align='right'>" + T_Text[24] + ":</td><td style='width: 40px;'><input class='Text_Element' id='einheit' name='Einheit' type='text' value='' size='5'></td><td class='Text_einfach' align='right' style='width: 40px;'>" + T_Text[25] + ":</td><td><input class='Text_Element' id='step' name='Step' type='checkbox' value=''></td></tr>";
	Inhalt += "</table></td></tr>";
	Inhalt += "<tr><td style='height: 40px;'><input class='Schalter_Element' value='" + T_Text[26] + "' type='button' name='speichern' onclick='Eingabe_validieren();'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick=\"Hilfe_Fenster('28');\">" + T_Text[10] + "</a></td></tr></table></div>";
	Inhalt += "<input name='Var_Nummer' id='var_nummer' type='hidden' value='" + var_nummer + "'>";
	Inhalt += "<input name='Var_Name' id='var_name' type='hidden' value='" + var_name + "'>";
	Inhalt += "<input name='Step_Feld' id='step_feld' type='hidden' value=''>";
	Inhalt += "<input name='Interface' id='interface' type='hidden' value='KS_" + Geraetenummer.toString() + "'>";
	Inhalt += "<input id='geraete_id' name='Geraete_ID' type='hidden' value='" + document.getElementById("geraete_id").value + "'>";
	Inhalt += "<input name='speichern1' id='speichern1' type='hidden' value='x'>";
	panel = jsPanel.create({
		dragit: {
     		snap: true
     	},
		id: 'tag_zuordnen_dialog',
		position: 'left-top 10 10',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '425 310',
		headerTitle: T_Text[27],
		content: Inhalt,
	});
}

function Eingabe_validieren() {
	Fehler = 0;
	if (document.getElementById("step").checked == true) {
		document.getElementById("step_feld").value='1';
	} else {
		document.getElementById("step_feld").value='0';
	}
	if (document.getElementById("tagname2").value > "") {
		if (document.getElementById("tagname2").value.indexOf(" ") > -1) {
			alert(T_Text[28]);
			Fehler = 1;
		}
		if (document.getElementById("pfad").value.indexOf("/") == -1) {
			alert(T_Text[29]);
			Fehler = 1;
		}
		if (document.getElementById("pfad").value.substr(0,1) != "/" || document.getElementById("pfad").value.substr(document.getElementById("pfad").value.length - 1,1) != "/") {
			alert(T_Text[30]);
			Fehler = 1;
		}
		if (document.getElementById("beschreibung").value.length == 0) {
			alert(T_Text[31]);
			Fehler = 1;
		}
		jQuery.ajax({
			url: "admin/Point_ID_lesen.php?Pointname=" + document.getElementById("tagname2").value + "&Path=" + document.getElementById("pfad").value,
			success: function (html) {
				Point_ID = html;
			},
		async: false
		});
		if (Point_ID > "") {
			alert(T_Text[32] + document.getElementById("pfad").value + document.getElementById("tagname2").value + T_Text[33]);
			Fehler = 1;
		}
	}
	if (Fehler == 0) {
		document.getElementById("speichern1").value = T_Text[34];
		document.getElementById("formular_tag_zuordnen").submit();
	}
}