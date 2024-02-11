<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'/>
<meta name="viewport" content="width=device-width, initial-scale = 0.5, maximum-scale=5.0"/>
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="../jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../tabulator/dist/css/tabulator.css" />
<script type="text/javascript" src="../tabulator/dist/js/tabulator.min.js"></script>

<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
header("X-XSS-Protection: 1");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Benutzerverwaltung.php");
echo "<title>".$Text[0]."</title>";
echo "</head>";
echo "<body class='allgemein'>";
echo '<table class="table" cellspacing = "7">';
	echo '<tr>';
echo '<td class="Text_fett">'.$Text[1].':&nbsp;</td>';
?>
<td>
	<select class="Auswahl_Liste_Element" id="filter-field">
		<option></option>
		<option value="User_ID">User_ID</option>
		<option value="UserName">UserName</option>
		<option value="mistrials">mistrials</option>
		<option value="Full_Name">Full_Name</option>
		<option value="eMail">eMail</option>
		<option value="Admin">Admin</option>
		<option value="Admin">Zeitstempel</option>
		<option value="Admin">Sprache </option>
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
	<input class="Text_Element" id="filter-value" type="text" placeholder="value to filter">
</td>
<td>	
	<button class="Schalter_Element" id="filter-clear"><?php echo $Text[2]; ?></button>
</td>
	</tr>
</table>
<br>
<div id="Tabelle"></div>
<br><br>
<table>
	<tr>
<td>
	<table class="table">
<tr>
	<td></td>
<?php
	echo '<td colspan = "2" align="center" class="Text_fett">'.$Text[3].'</td>';
	echo '</tr>';
	echo '<tr class="Tabellenzeile">';
	echo '<td><button class="Schalter_Element" id="ajax-trigger">'.$Text[4].'</button></td>';
	echo '<td><button class="Schalter_Element" id="history-undo">'.$Text[5].'</button></td>';
	echo '<td><button class="Schalter_Element" id="history-redo">'.$Text[6].'</button></td>';
	echo '</tr>';
	echo '</table>';
	echo '</td>';
	echo '<td width="30">';
	echo '</td>';
	echo '<td>';
	echo '<table class="table">';
	echo '<tr>';
	echo '<td colspan = "3" align="center" class="Text_fett">'.$Text[7].'</td>';
	echo '</tr>';
	echo '<tr class="Tabellenzeile">';
	echo '<td><button class="Schalter_Element" id="speichern" onclick="speichern();">'.$Text[8].'</button></td>';
	echo '<td><button class="Schalter_Element" id="markierte_loeschen" onclick="loeschen();">'.$Text[9].'</button></td>';
	echo '<td><button class="Schalter_Element" id="neue_Zeile">'.$Text[10].'</button></td>';
?>
</tr>
	</td>
</tr>
	</table>
</table>
<br><br><div id="Pfadtabelle"></div>
<br><br>
<table class='table'>
	<tr>
<?php
echo "<td colspan = '3' align='center' class='Text_fett'>".$Text[15]."</td>";
echo "</tr>";
echo "<tr class='Tabellenzeile'>";
echo "<td><button class='Schalter_Element' id='pfade_speichern' onclick='Pfade_speichern();'>".$Text[8]."</button></td>";
echo "<td><button class='Schalter_Element' id='neuer_pfad' onclick='Pfad_neu();'>".$Text[16]."</button></td>";
echo "<td><button class='Schalter_Element' id='markierte_pfade_loeschen' onclick='Pfade_loeschen();'>".$Text[9]."</button></td>";
?>
	</tr>
</table>

<script type="text/javascript">
//Define variables for input elements
var fieldEl = document.getElementById("filter-field");
var typeEl = document.getElementById("filter-type");
var valueEl = document.getElementById("filter-value");

//Custom filter example
function customFilter(data){
    return data.car && data.rating < 3;
}

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
var table = new Tabulator("#Tabelle", {
	history:true,
	layout:"fitDataTable",
	pagination:"remote",
	ajaxParams:{page:1,size:10,sorters:"UserName"},
	paginationSize:10,
	paginationSizeSelector:[5, 10, 15, 20, 30, 100, 1000],
	movableColumns:true,
	movableRows:true,
	//autoColumns:true,
	ajaxURL:"./Benutzer_lesen.php",
	placeholder:<?php echo "'".$Text[11]."'"; ?>,
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
			cell.getRow().toggleSelect();
		}},
		{title:"id", field:"id", formatter:"number", sorter:"number"},
		{title:"User_ID", field:"User_ID", formatter:"number", sorter:"number"},
		{title:"UserName", field:"UserName", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"]},
		{title:"Password", field:"Password", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"]},
		{title:"last_active", field:"last_active", formatter:"date", sorter:"date"},
		{title:"mistrials", field:"mistrials", formatter:"number", editor:"input", sorter:"number", validator:["required", "integer"]},
		{title:"Full_Name", field:"Full_Name", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"]},
		{title:"eMail", field:"eMail", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"]},
		{title:"activated", field:"activated", formatter:"number", editor:"input", sorter:"number", validator:["required", "integer"]},
		{title:"Admin", field:"Admin", formatter:"number", editor:"input", sorter:"number", validator:["required", "integer"]},
		{title:"Zeitstempel", field:"Zeitstempel", formatter:"date", sorter:"date"},
		{title:"Sprache", field:"Sprache", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"]},
	],
	validationFailed:function(cell, value, validators){
		if(value == ""){alert(<?php echo "'".$Text[12]."'"; ?>);}
		if(validators[0].type == "string" && typeof(cell._cell.value) != "string"){alert(<?php echo "'".$Text[13]."'"; ?>);}
		if(validators[0].type == "integer" && typeof(cell._cell.value) != "integer"){alert(<?php echo "'".$Text[14]."'"; ?>);}
	},
	cellEdited:function (cell) {
		if (cell._cell.value != cell._cell.initialValue) {
			cell._cell.element.style.backgroundColor="#E9F2A3";
		} else {
			cell._cell.element.style.backgroundColor="";
		}
	},
	cellClick:function(e, row) {
		User_ID = row.getData().User_ID;
		jQuery.ajax({
			url: "./Pfade_lesen.php?User_ID=" + User_ID,
			success: function (html) {
				Antwort = html;
			},
			async: false
		});
		pathtable.setData(Antwort);
	},
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
	table.setData("./Benutzer_lesen.php");
});

function speichern() {
	var neu = 0;
	for (z = 0;z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[2].value == undefined) {
			SQL = "INSERT INTO `User` (`UserName`, `Password`, `mistrials`, `Full_Name`, `eMail`, `Admin`, `Zeitstempel`, `Sprache`) VALUES (";
			SQL = SQL + "'" + table.rowManager.rows[z].cells[3].value + "', '" + table.rowManager.rows[z].cells[4].value + "', 0, '" + table.rowManager.rows[z].cells[7].value + "', '" + table.rowManager.rows[z].cells[8].value + "', " + table.rowManager.rows[z].cells[9].value + ", CURRENT_TIMESTAMP, '" + table.rowManager.rows[z].cells[11].value + "');"
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
			neu = 1;
		} else {
			SQL = "UPDATE `User` SET ";
			for (s = 3;s < table.columnManager.columns.length; s++) {
				if (table.rowManager.rows[z].cells[s].element.style.backgroundColor != "") {
					SQL = SQL + "`" + table.columnManager.columns[s].field + "` = '" + table.rowManager.rows[z].cells[s].value + "', ";
					table.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
				}
			}
			if (SQL != "UPDATE `User` SET ") {
				SQL = SQL.substr(0,SQL.length - 2) + " WHERE `User_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
				jQuery.ajax({
					url: "./SQL_ausfuehren.php?DB=unidb",
					type: 'POST',
					data: {SQL_Text: SQL},
					async: false
				});
			}
		}
	}
	if (neu == 1) {table.setData("./Benutzer_lesen.php");}
}

document.getElementById("neue_Zeile").addEventListener("click", function(){
    table.addRow({});
});

function loeschen() {
	var entfernt = 0;
	for (z = 0;z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			entfernt = 1;
			SQL = "DELETE FROM `User_Path` WHERE `User_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
			SQL = "DELETE FROM `Userdef_Filter` WHERE `User_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
			SQL = "DELETE FROM `User` WHERE `User_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
		}
	}
	if (entfernt == 1) {table.setData("./Benutzer_lesen.php");}
}

var pathtable = new Tabulator("#Pfadtabelle", {
	history:true,
	layout:"fitDataTable",
	pagination:"remote",
	ajaxParams:{Modus:"Pfad",User_ID:2,page:1,size:10,sorters:"Path"},
	paginationSize:10,
	paginationSizeSelector:[5, 10, 15, 20, 30],
	movableColumns:true,
	movableRows:true,
	//autoColumns:true,
	ajaxURL:"./Benutzer_lesen.php",
	placeholder:<?php echo "'".$Text[11]."'"; ?>,
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
			cell.getRow().toggleSelect();
		}},
		{title:"id", field:"id", formatter:"number", sorter:"number"},
		{title:"User_Path_ID", field:"User_Path_ID", formatter:"number", sorter:"number"},
		{title:"User_ID", field:"User_ID", formatter:"number", sorter:"number"},
		{title:"Path", field:"Path", formatter:"string", editor:"input", sorter:"string", validator:["required", "string"],minWidth:260},
		{title:"root", field:"root", formatter:"number", editor:"input", sorter:"number", validator:["required", "number"]},
	],
	validationFailed:function(cell, value, validators){
		if(value == ""){alert(<?php echo "'".$Text[12]."'"; ?>);}
		if(validators[0].type == "string" && typeof(cell._cell.value) != "string"){alert(<?php echo "'".$Text[13]."'"; ?>);}
		if(validators[0].type == "integer" && typeof(cell._cell.value) != "integer"){alert(<?php echo "'".$Text[14]."'"; ?>);}
	},
	cellEdited:function (cell) {
		if (cell._cell.value != cell._cell.initialValue) {
			cell._cell.element.style.backgroundColor="#E9F2A3";
		} else {
			cell._cell.element.style.backgroundColor="";
		}
	},
});

function Pfade_speichern() {
	for (z = 0;z < pathtable.rowManager.rows.length; z++) {
		Meldung = 0;
		if (pathtable.rowManager.rows[z].cells[2].value == "") {
			SQL = "INSERT INTO `User_Path` (";
			SQL1 = " VALUES (";
			for (s=3; s < pathtable.columnManager.columns.length; s++) {
				SQL += "`" + pathtable.columnManager.columns[s].field + "`, ";
				SQL1 += "'" + pathtable.rowManager.rows[z].cells[s].value + "', ";	
				pathtable.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
			}
			SQL = SQL.substr(0, SQL.length-2);
			SQL += ")";
			SQL1 = SQL1.substr(0, SQL1.length-2);
			SQL1 += ");";
			SQLs = SQL + SQL1;
			SQL = "";
			SQL1 = "";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQLs},
				async: false
			});
		}
		SQL = "UPDATE `User_Path` SET ";
		for (s = 3;s < pathtable.columnManager.columns.length; s++) {
			if (pathtable.rowManager.rows[z].cells[s].element.style.backgroundColor != "") {
				SQL = SQL + "`" + pathtable.columnManager.columns[s].field + "` = '" + pathtable.rowManager.rows[z].cells[s].value + "', ";
				pathtable.rowManager.rows[z].cells[s].element.style.backgroundColor = "";
			}
		}
		if (SQL != "UPDATE `User_Path` SET ") {
			SQL = SQL.substr(0,SQL.length - 2) + " WHERE `User_Path_ID` = " + pathtable.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
		}
	}
}

function Pfad_neu() {
	Reihen = pathtable.rowManager.rows.length;
	if (Reihen > 0) {
		Daten1 = JSON.stringify(pathtable.getRows()[0]._row.data);
		Daten2 = JSON.parse(Daten1);
		Daten2.id = Reihen;
		Daten2.User_Path_ID = "";
		pathtable.addRow(Daten2);
	} else {
		pathtable.setData("[{\"User_Path_ID\":\"\",\"User_ID\":" + table.getSelectedRows()[0]._row.data.User_ID + ",\"rw\":\"r\",\"Path\":\"\",\"root\":0,\"id\":0}]");
	}
};

function Pfade_loeschen() {
	for (z = 0; z < pathtable.rowManager.rows.length; z++) {
		if (pathtable.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			SQL = "DELETE FROM `User_Path` WHERE `User_Path_ID` = " + pathtable.rowManager.rows[z].data.User_Path_ID + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=unidb",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
			pathtable.deleteRow(z);
		}
	}
}
</script>

</body>
</html>