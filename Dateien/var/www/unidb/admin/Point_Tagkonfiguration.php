<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="../../Fenster/dist/jspanel.min.js"></script>
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script type="text/javascript" src="../jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../tabulator/dist/css/tabulator.css" />
<script type="text/javascript" src="../tabulator/dist/js/tabulator.min.js"></script>

<?php
	session_start();
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	include('../conf_DH_schreiben.php');
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("Point_Tagkonfiguration.php");
	echo "<title>".$Text[0]."</title>";
	echo "</head>";
	echo "<body class='allgemein'>";

echo "<table class='table' cellspacing = '7'>";
	echo "<tr>";
echo "<td class='Text_fett'>".$Text[2].":</td>";
?>
<td>
	<select class="Auswahl_Liste_Element" id="filter-field">
<option></option>
<option value="Point_ID">Point_ID</option>
<option value="Path">Path</option>
<option value="Pointname">Pointname</option>
<option value="Description">Description</option>
<option value="EUDESC">EUDESC</option>
<option value="scan">scan</option>
<option value="Interface">Interface</option>
<option value="archive">archive</option>
<option value="compression">compression</option>
<option value="minarch">minarch</option>
<option value="Info">Info</option>
<option value="Property_1">Property_1</option>
<option value="Property_2">Property_2</option>
<option value="Property_3">Property_3</option>
<option value="Property_4">Property_4</option>
<option value="Property_5">Property_5</option>
<option value="Point_Type">Point_Type</option>
<option value="Dezimalstellen">Dezimalstellen</option>
<option value="Scale_min">Scale_min</option>
<option value="Scale_max">Scale_max</option>
<option value="Intervall">Intervall</option>
<option value="Mittelwerte">Mittelwerte</option>
<option value="Changedate">Changedate</option>
<option value="first_value">first_value</option>
<option value="Point_owner">Point_owner</option>
	</select>
</td>
<td>
<select class="Auswahl_Liste_Element" id="filter-type">
	<option value="=">=</option>
	<option value="<"><</option>
	<option value="<="><=</option>
	<option value=">">></option>
	<option value=">=">>=</option>
	<option value="!=">!=</option>
	<option value="like">like</option>
</select>
</td>
<td>
<?php
	echo '<input class="Text_Element" id="filter-value" type="text" placeholder="'.$Text[1].'">';
echo '</td>';
echo '<td>';
	echo '<button class="Schalter_Element" id="filter-clear">'.$Text[3].'</button>';
?>
</td>
	</tr>
	</table>
	<br>
	<div id="Tabelle"></div>
	<br>
	<table>
<tr>
	<td valign="top">
<table class="table">
	<tr>
<td></td>
<td class="Text_fett">
	<?php echo $Text[4];?>
</td>
	</tr>
	<tr>
<td class="Text_fett" align='right'>Point_ID</td><td>
<?php
	if($line["Point_ID"] == "") {
		$Point_ID = "%";
	} else {
		$Point_ID = $line["Point_ID"];
	}
	echo "<input class='Text_Element' name='Point_ID' id='point_id' value='".$Point_ID."' type='Text' style='width:220px'></td></tr>";
	echo '</tr><tr><td class="Text_fett" align="right">'.$Text[5].'</td><td colspan="2">';
	//Schnittstellen_ID ermitteln
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Schnittstellen' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Schnittstellen_ID = $line['Einstellung_ID'];
	echo "<input name='Schnittstellen_ID' id='schnittstellen_id' value='".$Schnittstellen_ID."' type='hidden'>";
	mysqli_stmt_close($stmt);
	//weitere Suchfelder vorbelegen
	if($line["Path"] == "") {
		$Pfad = "%";
	} else {
		$Pfad = $line["Path"];
	}
	echo "<input class='Text_Element' name='Path' id='path' value='".$Pfad."' type='Text' style='width:220px'></td></tr>";
	if($line["Pointname"] == "") {
		$Punktname = "%";
	} else {
		$Punktname = $line["Pointname"];
	}
	echo "<tr><td class='Text_fett' align='right'>".$Text[39]."</td><td colspan='2'><input class='Text_Element' name='Pointname' id='pointname' value='".$Punktname."' type='Text' style='width:220px'></td></tr>";
	if($line["Interface"] == "") {
		$Schnittstelle = "%";
	} else {
		$Schnittstelle = $line["Interface"];
	}
	echo "<tr><td class='Text_fett' align='right'>".$Text[6]."</td><td colspan='2'><input class='Text_Element' name='Schnittstelle' id='schnittstelle' value='".$Schnittstelle."' type='Text' style='width:220px'></td></tr>";
 	echo "<tr><td></td><td><input class='Schalter_Element' id='suchen' name='Suchen' value='".$Text[7]."' type='button' style='border-left: 0px;' onclick='Point_suchen_dialog();'></td>";
	 //echo "<td><input class='Schalter_Element' name='neu_aufbauen' value='neu aufbauen' type='submit' style='border-left: 0px;'></td></tr>";
	echo "<td><button class='Schalter_Element' id='ajax-trigger'>".$Text[8]."</button></td>";
	echo "</table>";
	echo "<form action='Point_Tagkonfiguration.php' method='post' target='_self' id='phpform' name='Phpform'>";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
	echo "<input name='Pointliste' id='pointliste' value='".$Pointliste."' type='hidden'>";
?>
	</form>
</td>
<td width="30">
</td>
<td valign="top">
<table class="table">
	<tr>
<?php
echo "<td colspan = '3' align='center' class='Text_fett'>".$Text[9]."</td>";
	echo "</tr>";
	echo "<tr class='Tabellenzeile'>";
echo "<td><button class='Schalter_Element' id='clear'>".$Text[10]."</button></td>";
echo "<td><button class='Schalter_Element' id='history-undo'>".$Text[11]."</button></td>";
echo "<td><button class='Schalter_Element' id='history-redo'>".$Text[12]."</button></td>";
	echo "</tr>";
echo "</table>";
	echo "</td>";
	echo "<td width='30' valign='top'>";
	echo "</td>";
	echo "<td valign='top'>";
echo "<table class='table'>";
	echo "<tr>";
echo "<td colspan = '4' align='center' class='Text_fett'>".$Text[13]."</td>";
	echo "</tr>";
	echo "<tr class='Tabellenzeile'>";
echo "<td><button class='Schalter_Element' id='points_kopieren' onclick='Points_kopieren();'>".$Text[14]."</button></td>";
echo "<td><button class='Schalter_Element' id='points_speichern' onclick='Points_speichern();'>".$Text[15]."</button></td>";
echo "<td><button class='Schalter_Element' id='neue_Zeile'>".$Text[16]."</button></td>";
echo "<td><button class='Schalter_Element' id='markierte_loeschen' onclick='loeschen();'>".$Text[17]."</button></td>";
	echo "</tr>";
echo "</table>";
	echo "</td>";
echo "</tr>";
	echo "</table>";
echo "</td>";
	echo "</tr>";
echo "</table>";
echo "<br><br><hr><br>";
echo "<div class='Text_fett'>".$Text[18]."</div>";
echo "<br>";
echo "<div id='Tag_Tabelle'></div>";
echo "<br><br>";
echo "<table class='table'>";
	echo "<tr>";
echo "<td colspan = '3' align='center' class='Text_fett'>".$Text[19]."</td>";
	echo "</tr>";
	echo "<tr class='Tabellenzeile'>";
echo "<td><button class='Schalter_Element' id='tags_speichern' onclick='Tags_speichern();'>".$Text[15]."</button></td>";
echo "<td><button class='Schalter_Element' id='neuer_tag'>".$Text[20]."</button></td>";
echo "<td><button class='Schalter_Element' id='markierte_tags_loeschen' onclick='Tags_loeschen();'>".$Text[17]."</button></td>";
	echo "</tr>";
echo "</table>";
?>
<script type="text/javascript">
//Define variables for input elements
var fieldEl = document.getElementById("filter-field");
var typeEl = document.getElementById("filter-type");
var valueEl = document.getElementById("filter-value");
var Point_ID = 1;
var Schnittstellen = [];
var T_Text = new Array;
T_Text = JSON.parse(document.getElementById("translation").value);

//Trigger setFilter function with correct parameters
function updateFilter(){
  var filterVal = fieldEl.options[fieldEl.selectedIndex].value;
  var typeVal = typeEl.options[typeEl.selectedIndex].value;
  var filter = filterVal == "function" ? customFilter : filterVal;
  if(filterVal == "function" ){
    typeEl.disabled = true;
    valueEl.disabled = true;
  }else{
    typeEl.disabled = false;
    valueEl.disabled = false;
  }
  if(filterVal){
    table.setFilter(filter,typeVal, valueEl.value);
  }
}

//Update filters on value change
document.getElementById("filter-field").addEventListener("change", updateFilter);
document.getElementById("filter-type").addEventListener("change", updateFilter);
document.getElementById("filter-value").addEventListener("keyup", updateFilter);

//Clear filters on "Clear Filters" button click
document.getElementById("filter-clear").addEventListener("click", function(){
	fieldEl.value = "";
	typeEl.value = "=";
	valueEl.value = "";
	table.clearFilter();
});

var tagtable = new Tabulator("#Tag_Tabelle", {
	layout:"fitDataTable",
	data:'[{"Tag_ID":"","Point_ID":"","Tagname":"","Path":"","Tag_owner":"","id":0}]',
	placeholder:"No Data Set",
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
  			cell.getRow().toggleSelect();
		}},
		{title:"id", field:"id", sorter:"number"},
		{title:"Tag_ID", field:"Tag_ID", sorter:"number"},
		{title:T_Text[5], field:"Path", sorter:"string", editor:"input", validator:"required"},
		{title:T_Text[21], field:"Tagname", sorter:"string", editor:"input", validator:"required"},
		{title:"Point_ID", field:"Point_ID", sorter:"number"},
		{title:"Tag_owner", field:"Tag_owner", editor:"input", sorter:"number"},
	],
	validationFailed:function(cell, value, validators) {
		if(value == "") {alert(T_Text[22]);}
	},
	cellEdited:function (cell) {
		if (cell._cell.value != cell._cell.initialValue) {
			cell._cell.element.style.backgroundColor="#E9F2A3";
		} else {
			cell._cell.element.style.backgroundColor="";
		}	
	},
});

var table = new Tabulator("#Tabelle", {
	history:true,
	layout:"fitDataFill",
	pagination:"remote",
	ajaxParams:{Points:document.getElementById("pointliste").value,page:1,size:10,sorters:"Point_ID"},
	paginationSize:10,
	paginationSizeSelector:[5, 10, 15, 20, 30, 100, 1000],
	movableRows:true,
	ajaxURL: './Points_lesen.php',
	placeholder:T_Text[23],
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
        cell.getRow().toggleSelect();
      }},
		{title:"id", field:"id", sorter:"number"},
		{title:T_Text[37], field:"Timestamp", sorter:"date"},
		{title:T_Text[38], field:"Value", sorter:"number"},
		{title:"Point_ID", field:"Point_ID", sorter:"number"},
		{title:T_Text[5], field:"Path", sorter:"string", editor:"input", validator:"required"},
		{title:T_Text[39], field:"Pointname", sorter:"string", editor:"input", validator:"required"},
		{title:T_Text[36], field:"Description", editor:"input", sorter:"string"},
		{title:T_Text[35], field:"EUDESC", editor:"input", sorter:"string"},
		{title:"scan", field:"scan", editor:true, formatter:"tickCross", validator:"required"},
		{title:T_Text[34], field:"Interface", editor:"input", sorter:"string", validator:"required"},
		{title:T_Text[40], field:"archive", editor:true, formatter:"tickCross", validator:"required"},
		{title:"step", field:"step", editor:true, formatter:"tickCross", validator:"required"},
		{title:T_Text[31], field:"compression", editor:"input", sorter:"number", validator:"required"},
		{title:"minarch", field:"minarch", editor:"input", sorter:"number", validator:"required"},
		{title:"Info", field:"Info", editor:"input", sorter:"string"},
		{title:T_Text[28] + "_1", field:"Property_1", editor:"input", sorter:"string"},
		{title:T_Text[28] + "_2", field:"Property_2", editor:"input", sorter:"string"},
		{title:T_Text[28] + "_3", field:"Property_3", editor:"input", sorter:"string"},
		{title:T_Text[28] + "_4", field:"Property_4", editor:"input", sorter:"string"},
		{title:T_Text[28] + "_5", field:"Property_5", editor:"input", sorter:"string"},
		{title:T_Text[27], field:"Point_Type", editor:"input", sorter:"string", validator:"required"},
		{title:T_Text[33], field:"Dezimalstellen", editor:"input", sorter:"number"},
		{title:T_Text[30], field:"Scale_min", editor:"input", sorter:"number"},
		{title:T_Text[29], field:"Scale_max", editor:"input", sorter:"number"},
		{title:T_Text[26], field:"Intervall", editor:"input", sorter:"number"},
		{title:T_Text[32], field:"Mittelwerte", editor:true, formatter:"tickCross", validator:"required"},
		{title:T_Text[24], field:"Changedate", sorter:"date"},
		{title:T_Text[25], field:"first_value", sorter:"date"},
		{title:"Point_owner", field:"Point_owner", editor:"input", sorter:"number"},
	],
	validationFailed:function(cell, value, validators){
		if(value == ""){alert(T_Text[22]);}
	},
	cellEdited:function (cell) {
		if (cell._cell.value != cell._cell.initialValue) {
			cell._cell.element.style.backgroundColor="#E9F2A3";
		} else {
			cell._cell.element.style.backgroundColor="";
		}
	},
	cellClick:function(e, row) {
		Point_ID = row.getData().Point_ID;
		jQuery.ajax({
			url: "./Tags_lesen.php?Point_ID=" + Point_ID,
			success: function (html) {
				Antwort = html;
			},
			async: false
		});
		tagtable.setData(Antwort);
	},
});

//Clear table on "Empty the table" button click
document.getElementById("clear").addEventListener("click", function(){
	document.getElementById("pointliste").value = "";
	document.Phpform.submit();
});

//undo button
document.getElementById("history-undo").addEventListener("click", function(){
	table.undo();
	if (table.modules.history.history[table.modules.history.index + 1].data.oldValue !=table.modules.history.history[table.modules.history.index + 1].component.initialValue) {
		table.modules.history.history[table.modules.history.index + 1].component.element.style.backgroundColor="#E9F2A3";
	} else {
		table.modules.history.history[table.modules.history.index + 1].component.element.style.backgroundColor="";
	}
});

//redo button
document.getElementById("history-redo").addEventListener("click", function(){
	table.redo();
	if (table.modules.history.history[table.modules.history.index - 1].data.oldValue !=table.modules.history.history[table.modules.history.index - 1].component.initialValue) {
		table.modules.history.history[table.modules.history.index - 1].component.element.style.backgroundColor="#E9F2A3";
	} else {
		table.modules.history.history[table.modules.history.index - 1].component.element.style.backgroundColor="";
	}
});

//trigger AJAX load on "Load Data via AJAX" button click
document.getElementById("ajax-trigger").addEventListener("click", function(){
	table.setData("./Points_lesen.php");
});


function Points_kopieren() {
	Reihen = table.rowManager.rows.length;
	for (z = 0;z < table.getSelectedRows().length; z++) {
		Daten1 = JSON.stringify(table.getSelectedRows()[z]._row.data);
		Daten2 = JSON.parse(Daten1);
		Daten2.id = Reihen;
		Daten2.Point_ID = Point_ID = "";
		Daten2.first_value = first_value = "";
		Daten2.Changedate = Changedate = "";
		table.addRow(Daten2);
		Reihen = Reihen + 1;
	}
	table.setSort("id","asc")
}

function Points_speichern() {
	for (z = 0;z < table.rowManager.rows.length; z++) {
		Meldung = 0;
		if (table.rowManager.rows[z].cells[4].value == undefined || table.rowManager.rows[z].cells[4].value == "") {
			jQuery.ajax({
				url: "./Point_ID_lesen.php?Pointname=" + table.rowManager.rows[z].data.Pointname + "&Path=" + table.rowManager.rows[z].data.Path,
				success: function (html) {
					Point_ID = html;
				},
				async: false
			});
			if (Point_ID > "") {
				alert(T_Text[41] + table.rowManager.rows[z].data.Path + table.rowManager.rows[z].data.Pointname + T_Text[42]);
				SQLs = "";
			} else {
				SQL = "INSERT INTO `Points` (";
				SQL1 = " VALUES (";
				for (s=5; s < table.columnManager.columns.length; s++) {
					SQL += "`" + table.columnManager.columns[s].field + "`, ";
					if (table.columnManager.columns[s].field == "Changedate" || table.columnManager.columns[s].field == "first_value" ) {
						SQL1 += "CURRENT_TIMESTAMP, ";
					} else {
						Wert = table.rowManager.rows[z].cells[s].value;
						if (Wert == undefined) {Wert = "";}
						if (Wert == true && table.columnManager.columns[s].definition.formatter == "tickCross") {Wert = 1;}
						if (Wert == false && table.columnManager.columns[s].definition.formatter == "tickCross") {Wert = 0;}
						SQL1 += "'" + Wert + "', ";	
					}
					table.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
				}
				SQL = SQL.substr(0, SQL.length-2);
				SQL += ")";
				SQL1 = SQL1.substr(0, SQL1.length-2);
				SQL1 += ");";
				SQLs = SQL + SQL1;
				SQL = "";
				SQL1 = "";
				jQuery.ajax({
					url: './SQL_ausfuehren.php?DB=DH',
					type: 'POST',
					data: {SQL_Text: SQLs},
					async: false
				});
				//Point_ID des neuen Points ermitteln
				jQuery.ajax({
					url: "./Point_ID_lesen.php?Pointname=" + table.rowManager.rows[z].data.Pointname + "&Path=" + table.rowManager.rows[z].data.Path,
					success: function (html) {
						Point_ID = html;
					},
					async: false
				});
				table.rowManager.rows[z].data.Point_ID = Point_ID;
				table.redraw(z);
				if (document.getElementById("pointliste").value.length > 0) {
					document.getElementById("pointliste").value = document.getElementById("pointliste").value + "," + Point_ID;
				} else {
					document.getElementById("pointliste").value = Point_ID;
				}
				SQLs = "INSERT INTO `akt` (`Point_ID`, `Value`, `Timestamp`) VALUES (" + Point_ID + ", 0, CURRENT_TIMESTAMP)";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=DH",
					type: 'POST',
					data: {SQL_Text: SQLs},
					async: false
				});
				Owner = table.rowManager.rows[z].data.Point_owner;
				if (Owner == undefined) {Owner = "0";}
				SQLs = "INSERT INTO `Tagtable` (`Point_ID`, `Path`, `Tagname`, `Tag_owner`) VALUES (" + Point_ID + ", '" + table.rowManager.rows[z].data.Path + "', '" + table.rowManager.rows[z].data.Pointname + "', " + Owner + ");";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=DH",
					type: 'POST',
					data: {SQL_Text: SQLs},
					async: false
				});
				Meldung = 1;
			}
		} else {
			SQL = "UPDATE `Points` SET ";
			for (s = 5;s < table.columnManager.columns.length; s++) {
				if (table.rowManager.rows[z].cells[s].element.style.backgroundColor != "") {
					Wert = table.rowManager.rows[z].cells[s].value;
					if (Wert == true) {Wert = 1;}
					if (Wert == false) {Wert = 0;}
					SQL = SQL + "`" + table.columnManager.columns[s].field + "` = '" + Wert + "', ";
					table.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
				}
			}
			if (SQL != "UPDATE `Points` SET ") {
				Meldung = 1;
				SQL = SQL.substr(0,SQL.length - 2) + " WHERE `Point_ID` = " + table.rowManager.rows[z].cells[4].value + ";";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=DH",
					type: 'POST',
					data: {SQL_Text: SQL},
					async: false
				});
			}
		}
		if (Meldung == 1) {
			Schnittstelle = {"Rechner": "xxx", "Schnittstelle":"","Meldung":"Points einlesen"};
			Schnittstelle.Schnittstelle = table.rowManager.rows[z].data.Interface;
			Schnittstellen.push(Schnittstelle);
		}
	}
	Meldungen_absetzen(Schnittstellen);
}

function Tags_speichern() {
	for (z = 0;z < tagtable.rowManager.rows.length; z++) {
		Meldung = 0;
		if (tagtable.rowManager.rows[z].cells[2].value == "") {
			jQuery.ajax({
				url: "./Tag_ID_lesen.php?Tagname=" + tagtable.rowManager.rows[z].data.Tagname + "&Path=" + tagtable.rowManager.rows[z].data.Path,
				success: function (html) {
					Tag_ID = html;
				},
				async: false
			});
			if (Tag_ID > "") {
				alert(T_Text[43] + tagtable.rowManager.rows[z].data.Path + tagtable.rowManager.rows[z].data.Tagname + T_Text[42]);
				SQLs = "";
			} else {
				SQL = "INSERT INTO `Tagtable` (";
				SQL1 = " VALUES (";
				for (s=3; s < tagtable.columnManager.columns.length; s++) {
					SQL += "`" + tagtable.columnManager.columns[s].field + "`, ";
					SQL1 += "'" + tagtable.rowManager.rows[z].cells[s].value + "', ";	
					tagtable.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
				}
				SQL = SQL.substr(0, SQL.length-2);
				SQL += ")";
				SQL1 = SQL1.substr(0, SQL1.length-2);
				SQL1 += ");";
				SQLs = SQL + SQL1;
				SQL = "";
				SQL1 = "";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=DH",
					type: 'POST',
					data: {SQL_Text: SQLs},
					async: false
				});
				//Tag_ID des neuen Tags ermitteln
				jQuery.ajax({
					url: "./Tag_ID_lesen.php?Tagname=" + tagtable.rowManager.rows[z].data.Tagname + "&Path=" + tagtable.rowManager.rows[z].data.Path,
					success: function (html) {
						Tag_ID = html;
					},
					async: false
				});
				tagtable.rowManager.rows[z].data.Tag_ID = Tag_ID;
				tagtable.redraw(z);
			}
		} else {
			SQL = "UPDATE `Tags` SET ";
			for (s = 3;s < tagtable.columnManager.columns.length; s++) {
				if (tagtable.rowManager.rows[z].cells[s].element.style.backgroundColor != "") {
					SQL = SQL + "`" + tagtable.columnManager.columns[s].field + "` = '" + tagtable.rowManager.rows[z].cells[s].value + "', ";
					tagtable.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
				}
			}
			if (SQL != "UPDATE `Tags` SET ") {
				SQL = SQL.substr(0,SQL.length - 2) + " WHERE `Tag_ID` = " + tagtable.rowManager.rows[z].cells[2].value + ";";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=DH",
					type: 'POST',
					data: {SQL_Text: SQL},
					async: false
				});
			}
		}
	}
}

document.getElementById("neue_Zeile").addEventListener("click", function(){
    table.addRow({});
});

document.getElementById("neuer_tag").addEventListener("click", function(){
	Reihen = tagtable.rowManager.rows.length;
	if (Reihen > 0) {
		Daten1 = JSON.stringify(tagtable.getRows()[0]._row.data);
		Daten2 = JSON.parse(Daten1);
		Daten2.id = Reihen;
		Daten2.Tag_ID = "";
		tagtable.addRow(Daten2);
	} else {
		tagtable.setData("[{\"Tag_ID\":0,\"Point_ID\":" + table.getSelectedRows()[0]._row.data.Point_ID + ",\"Tagname\":\"" + table.getSelectedRows()[0]._row.data.Pointname + "\",\"Path\":\"" + table.getSelectedRows()[0]._row.data.Pfad + "\",\"Tag_owner\":0,\"id\":0}]");
	}
});

function Tags_loeschen() {
	for (z = 0; z < tagtable.rowManager.rows.length; z++) {
		if (tagtable.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			SQL = "DELETE FROM `Tagtable` WHERE `Tag_ID` = " + tagtable.rowManager.rows[z].data.Tag_ID + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=DH",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
			tagtable.deleteRow(z);
		}
	}
}

function loeschen() {
	Tabellen = [];
	Tabellen[0] = "Points";
	Tabellen[1] = "Tagtable";
	Tabellen[2] = "akt";
	for (z = 0; z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			jQuery.ajax({
				url: "./Anz_Werte_Archiv.php?Point_ID=" + table.rowManager.rows[z].data.Point_ID,
				success: function (html) {
					strResult = html;
				},
				async: false
			});
			if (strResult > 0) {
				alert(T_Text[41] + table.rowManager.rows[z].data.Path + table.rowManager.rows[z].data.Pointname + T_Text[44] + strResult + T_Text[45])
			} else {
				for (i = 0; i < 3; i++) {
					SQL = "DELETE FROM `" + Tabellen[i] + "` WHERE `Point_ID` = " + table.rowManager.rows[z].data.Point_ID + ";";
					jQuery.ajax({
						url: "./SQL_ausfuehren.php?DB=DH",
						type: 'POST',
						data: {SQL_Text: SQL},
						async: false
					});
				}
				table.deleteRow(z);
			}
		}
	}
}

function Point_suchen_dialog() {
	var strReturn = "";
	try {
		panel.close('pointsuche');
	} catch (err) {}
	jQuery.ajax({
		url: "./Points_suchen.php?Pointname=" + document.getElementById("pointname").value + "&Pfad=" + document.getElementById("path").value + "&Schnittstelle=" + document.getElementById("schnittstelle").value + "&Point_ID=" + document.getElementById("point_id").value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'pointsuche',
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '550 420',
		content: strReturn,
  		contentOverflow: 'hidden'
	});
}

function uebertragen() {
	try {
		var Ergebnis = document.getElementById("Liste").selectedOptions;
		var i = 0;
		if (Ergebnis.length > 0) {
			if (document.getElementById("pointliste").value.length == 0) {
				document.getElementById("pointliste").value = Ergebnis[0].value;
				i = 1;
			}
			while (i < Ergebnis.length) {
				document.getElementById("pointliste").value = document.getElementById("pointliste").value + "," + Ergebnis[i].value;
				i = i +1;
			}
		}
		document.Phpform.submit();
	} catch (err) {}
}

function Meldungen_absetzen(Schnittstellen) {
	//comp bekommt immer Bescheid
	Schnittstelle={"Rechner": "Server", "Schnittstelle":"comp","Meldung":"Points einlesen"};
	Schnittstellen.push(Schnittstelle);
	for (x=0; x<Schnittstellen.length; x++) {
		for (i=x+1; i<Schnittstellen.length; i++) {
			if (Schnittstellen[i]["Schnittstelle"] == Schnittstellen[x]["Schnittstelle"]) {
				Schnittstellen.splice(i,1);
				i = i - 1;
			}
		}
	}
	for (i=0; i<Schnittstellen.length; i++) {
		if (Schnittstellen[i].Rechner == "xxx") {
			jQuery.ajax({
				url: "./Schnittstelle_Rechner.php?Schnittstellen_ID=" + document.getElementById("schnittstellen_id").value + "&Schnittstelle=" + Schnittstellen[i].Schnittstelle,
				success: function (html) {
					Schnittstellen[i].Rechner = html;
				},
				async: false
			});
		}
		if (Schnittstellen[i].Rechner > "") {
			jQuery.ajax({
				url: "./Meldung_schreiben.php?Schnittstelle=" + Schnittstellen[i].Schnittstelle + "&Meldung=" + Schnittstellen[i].Meldung + "&Rechner=" + Schnittstellen[i].Rechner,
				async: false
			});
		}
	}
	Schnittstellen = [];
}
</script>

</body>
</html>
