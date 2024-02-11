<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="../jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../tabulator/dist/css/tabulator.css" />
<script type="text/javascript" src="../tabulator/dist/js/tabulator.min.js"></script>
<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
$Text = Translate("Meldungen.php");
echo "<title>".$Text[0]."</title>\n";
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<table class='table' cellspacing = '7'>\n";
	echo "<tr>\n";
echo "<td class='Text_fett'>".$Text[1].":&nbsp;</td>\n";
?>
<td>
	<select class="Auswahl_Liste_Element" id="filter-field">
		<option></option>
		<option value="Timestamp">Timestamp</option>
		<option value="Schnittstelle">Schnittstelle</option>
		<option value="Meldung">Meldung</option>
		<option value="Rechner">Rechner</option>
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
<button class="Schalter_Element" id="markierte_loeschen" onclick="loeschen();"><?php echo $Text[3]; ?></button>
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
	layout:"fitDataTable",
	pagination:"remote",
	ajaxParams:{page:1,size:10},
	paginationSize:10,
	paginationSizeSelector:[5, 10, 15, 20, 30, 100, 1000],
	ajaxURL:"./Meldungen_lesen.php",
	placeholder:<?php echo "'".$Text[4]."'"; ?>,
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
			cell.getRow().toggleSelect();
      }},
		{title:"id", field:"id", formatter:"number", sorter:"number"},
		{title:"Meldungen_ID", field:"Meldungen_ID", sorter:"number"},
		{title:"Timestamp", field:"Timestamp", sorter:"string"},
		{title:<?php echo "'".$Text[5]."'"; ?>, field:"Schnittstelle", sorter:"string"},
		{title:<?php echo "'".$Text[6]."'"; ?>, field:"Meldung", sorter:"string"},
		{title:<?php echo "'".$Text[7]."'"; ?>, field:"Rechner", sorter:"string"},
	],
});

function loeschen() {
	var entfernt = 0;
	for (z = 0;z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			entfernt = 1;
			SQL = "DELETE FROM `Meldungen` WHERE `Meldungen_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=DH",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
		}
	}
	if (entfernt == 1) {table.setData("./Meldungen_lesen.php");}
}
</script>
</body>
</html>
