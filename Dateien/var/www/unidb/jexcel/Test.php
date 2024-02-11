<html>

<script src="../scripts/jquery-3.6.0.js"></script>
<script src="./jexcel.js"></script>
<link rel="stylesheet" href="./jexcel.css" type="text/css" />
<script src="./jsuites.js"></script>
<link rel="stylesheet" href="./jsuites.css" type="text/css" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<!-- <script src="../../Fenster/extensions/hint/jspanel.hint.js"></script> -->
<body style="background-color: #ececec;">
<div id="spreadsheet" style="position: absolute; top: 50px; height: 800px;"></div>
<input type="button" value="Add new tab" onclick="add();" style="width:150px;">
<input type="button" value="Download selected tab" onclick="download();" style="width:150px;">
<input type="button" value="akt Wert" onclick="aktueller_Wert_Dialog();" style="width:150px;">
<?php 
	//include('../funktionen.inc.php');
	include('../Sitzung.php');
	$Text = Translate("IP_Grid.php");
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
?>
<script>
var add = function() {
    var sheets = [];
    sheets.push({
        sheetName: prompt('Create a new tab', 'New tab ' + document.getElementById('spreadsheet').jexcel.length),
	minDimensions:[20,30],
	tableOverflow:true,
	tableHeight: "800px",
	tableWidth: "1200px",
	defaultColWidth: 100,
	columns: [
	    {
		type:'calendar',
		title:'Last visit',
		options: {format:'YYYY-MM-DD hh:mm',time:1},
		width:'150',
	    },
	    {
		type: 'checkbox',
		width:200,
	    }
	],
	filters: true,
	allowComments:true,
    });
    jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
};

var download = function() {
    // Get selected tab
    var worksheet = document.getElementById('spreadsheet').children[0].querySelector('.selected').getAttribute('data-spreadsheet');
    // Download
    document.getElementById('spreadsheet').jexcel[worksheet].download();
};

var TEST = function(v) {
    if (v == 1) {
	v = 'funktioniert';
    } else {
	v = 'funktioniert auch';
    }
    return v;
}

var sheets = [
    {
        sheetName: 'Countries',
        minDimensions:[20,30],
	tableOverflow:true,
	tableHeight: "800px",
	tableWidth: "1200px",
	defaultColWidth: 100,
    },
    {
        sheetName: 'Cities',
        minDimensions:[20,30],
	tableOverflow:true,
	tableHeight: "800px",
	tableWidth: "1200px",
	defaultColWidth: 100,
    }
];

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
jspreadsheet.tabs(document.getElementById('spreadsheet'), sheets);
</script>
</body>
</html>
