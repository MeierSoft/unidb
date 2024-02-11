var Hilfeposition = 0;
var HListe = new Array;
var Fenster = "";
var Hilfetext = new Array;
var T_Textarray = new Array;

// setup event handler function
let handler = function(event) {
	InhaltBreite = parseInt(document.getElementById('if_inhalt').style.width)
	document.getElementById('rahmen_haupt').style.width = (document.getElementsByClassName("jsPanel-content")[0].clientWidth - InhaltBreite ).toString() + "px";
}

// assign handler to event
document.addEventListener('jspanelresizestop', handler, false);

function suchen() {
	jQuery.ajax({
		url: "./Hilfesuchen.php?Suchtext=" + document.getElementById('suchen_text').value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	Fenster.content.childNodes[15].childNodes[1].contentDocument.getElementById("Suchergebnis").innerHTML = "<b><u>" + Hilfetext[2] + ":</b></u><br><br>" + strReturn + "<br><hr><br>";
	document.getElementById('rahmen_inhalt').style.width = "300px";
	document.getElementById('if_inhalt').style.width = "300px";
	document.getElementById('rahmen_haupt').style.left = "305px";
}

function Hilfeliste_einlesen() {
	jQuery.ajax({
		url: "./Hilfeliste.php?Modus=lesen",
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
  	HListe = strReturn.split(",");
  	if (Hilfeposition == 0) {Hilfeposition = HListe.length;}
}

function Inhaltsverz() {
	document.getElementById('rechts').removeAttribute('disabled');
	document.getElementById('links').removeAttribute('disabled');
	if (document.getElementById('rahmen_inhalt').style.width == "0px") {
		document.getElementById('rahmen_inhalt').style.width = "300px";
		document.getElementById('if_inhalt').style.width = "300px";
		document.getElementById('rahmen_haupt').style.left = "305px";
	} else {
		document.getElementById('rahmen_inhalt').style.width = "0px";
		document.getElementById('if_inhalt').style.width = "0px";
		document.getElementById('rahmen_haupt').style.left = "5px";
	}
	InhaltBreite = parseInt(document.getElementById('if_inhalt').style.width)
	document.getElementById('rahmen_haupt').style.width = (document.getElementsByClassName("jsPanel-content")[0].clientWidth - InhaltBreite ).toString() + "px";
}

function schieben(Richtung) {
	Hilfeliste_einlesen();
	if (Richtung == "links") {
		Hilfeposition = Hilfeposition - 1;
		if (Hilfeposition == 0) {Hilfeposition = 1;}
	} else {
		Hilfeposition = Hilfeposition + 1;
		if (Hilfeposition >= HListe.length) {Hilfeposition = HListe.length;}
	}
	
	if (Hilfeposition < HListe.length)	{
		document.getElementById('rechts').removeAttribute('disabled');
	} else {
		document.getElementById('rechts').setAttribute("disabled","disabled");
	}
	if (Hilfeposition == 1)	{
		document.getElementById('links').setAttribute("disabled","disabled");
	} else {
		document.getElementById('links').removeAttribute('disabled');
	}
	if (HListe.length < 2)	{
		document.getElementById('links').setAttribute("disabled","disabled");
		document.getElementById('rechts').setAttribute("disabled","disabled");
	}
	document.getElementById("if_haupt").attributes.src.value = "Hilfe3.php?Hilfe_ID=" + HListe[Hilfeposition].toString() + "&nreg=1";
}

function Hilfe_Fenster(Hilfe_ID) {
	if (document.getElementById("mobil").value == "1") {
		window.open("./Hilfe.php?Hilfe_ID=" + Hilfe_ID, '_blank');
	} else {
		try {HilfeFenster.close();} catch (err) {}
		jQuery.ajax({
			url: "./Sprache.php",
			success: function (html) {
  				Sprache = html;
			},
			async: false
		});
		SQL = "SELECT `Nummer`, `Text_" + Sprache + "` AS `Text` FROM `Translation` WHERE `Dokument` = 'Hilfe.php' ORDER BY `Nummer` ASC;";
		jQuery.ajax({
			url: "Daten_lesen.php",
			success: function (html) {
  				strReturn = html;
			},
			type: 'POST',
			data: {query: SQL, user: "1"},
			async: false
		});
		T_Textarray = JSON.parse(strReturn);
		Hilfetext[1] = T_Textarray[0].Text;
		Hilfetext[2] = T_Textarray[4].Text;
		jQuery.ajax({
			url: "./Hilfe.php?Hilfe_ID=" + Hilfe_ID,
			success: function (html) {
  				strReturn = html;
			},
			async: false
 		});
		Fenster = jsPanel.create({
			dragit: {snap: true},
			id: 'HilfeFenster',
			theme: 'info',
			headerControls: {size: 'xs'},
			contentSize: '600 600',
			headerTitle: Hilfetext[1],
			content:  strReturn,
		});
	}
}