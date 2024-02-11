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
include('./DH_Admin_func.php');
$Text = Translate("Puffer.php");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<table style='position: absolute; top: 10px; left: 10px;' cellpadding='3px'>\n";
echo "<tr class='Tabelle_Ueberschrift'><td>".$Text[1]."</td><td>".$Text[2]."</td></tr>\n";
//Kollektiv_ID ermitteln
$Rechner = array();
$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE (`Parameter` = 'Kollektiv') AND `Eltern_ID` = 0;";
$req = mysqli_query($db,$query);
$i = 0;
$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
$Eltern_ID = $line["Einstellung_ID"];
$query = "SELECT `Einstellung_ID`, `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID.";";
$req1 = mysqli_query($db,$query);
$i = 0;
while($line = mysqli_fetch_array($req1, MYSQLI_ASSOC)) {
	$data["Rechnername"] = $line["Parameter"];
	$data["Einstellung_ID"] = $line["Einstellung_ID"];
	$query = "SELECT `Parameter`, `Wert` FROM `Einstellungen` WHERE `Eltern_ID` = ".$line["Einstellung_ID"].";";
	$req2 = mysqli_query($db,$query);
	while($line1 = mysqli_fetch_array($req2, MYSQLI_ASSOC)) {
		$data[$line1["Parameter"]] = $line1["Wert"];
	}
	$Rechner[$i] = $data;
	$i = $i + 1;
}
$x = 0;
while($x < count($Rechner)) {
	$db = mysqli_connect($Rechner[$x]["IP"],$Rechner[$x]["User"],$Rechner[$x]["Password"],$Rechner[$x]["Database"]);
	if($db != false) {
		$query = "SELECT count(`Puffer_ID`) AS `Eintraege` FROM `Puffer`;";
		$req3 = mysqli_query($db,$query);
		$line3 = mysqli_fetch_array($req3, MYSQLI_ASSOC);
		echo "<tr class='Tabellenzeile'><td><button class='Schalter_Element' onclick='einlesen(".$Rechner[$x]["Einstellung_ID"].")'>".$Rechner[$x]["Rechnername"]."</button></td><td id='Anzahl_".$Rechner[$x]["Einstellung_ID"]."'>".$line3["Eintraege"]."</td></tr>\n";
		mysqli_close($db);
	} else {
		echo "<tr class='Tabellenzeile'><td>".$Rechner[$x]["Rechnername"]."</td><td>".$Text[3]."</td></tr>\n";
	}
	$x = $x + 1;
}
?>
</table>
</div>
<div id="filterform" style="position: absolute; top:30px; left: 200px; display: none;">
	<table class='table' cellspacing = '7'>
		<tr>
			<td class='Text_fett'>Filter:</td>
			<td>
				<select class="Auswahl_Liste_Element" id="filter-field">
					<option></option>
					<option value="Server">Server</option>
					<option value="SQL_Text">SQL_Text</option>
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
				<input class="Text_Element" id="filter-value" type="text">
			</td>
			<td>
				<button class="Schalter_Element" id="filter-clear">zurücksetzen</button>
			</td>
		</tr>
	</table>
</div>
<div id="steuerung" style="position: absolute; top: 80px; left: 200px; display:none;">
	<div id="Tabelle"></div>
	<div style='position:relative; left:0px; top:20px;'>
<button class='Schalter_Element' id='markierte_loeschen' onclick='loeschen();'><?php echo $Text[4]; ?></button>
	</div>
</div>

<script type="text/javascript">
var table="";
var ausgewaehlt = 0;

//Define variables for input elements
var fieldEl = document.getElementById("filter-field");
var typeEl = document.getElementById("filter-type");
var valueEl = document.getElementById("filter-value");

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


function einlesen(Rechner){
	document.getElementById("filterform").style.display = "block";
	ausgewaehlt = Rechner;
	table = new Tabulator("#Tabelle", {
		layout:"fitDataTable",
		pagination:"remote",
		ajaxParams:{ID:Rechner,page:1,size:10},
		paginationSize:10,
		paginationSizeSelector:[5, 10, 15, 20, 30, 100, 1000],
		ajaxURL:"./Puffer_lesen_unidb.php",
		placeholder:<?php echo "'".$Text[5]."'";?>,
		columns:[
			{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
      		cell.getRow().toggleSelect();
   		}},
			{title:"id", field:"id", sorter:"number"},
			{title:"Puffer_ID", field:"Puffer_ID", sorter:"number"},
			{title:"Timestamp", field:"Timestamp", sorter:"date"},
			{title:"Server", field:"Server", sorter:"string"},
			{title:"SQL_Text", field:"SQL_Text", sorter:"string"},
		],
	});
	document.getElementById("steuerung").style.display="block";
}

function loeschen() {
	for (z = 0;z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			SQL_Text = "DELETE FROM `Puffer` WHERE `Puffer_ID` = " + table.rowManager.rows[z].cells[2].value + ";";
			jQuery.ajax({
				url: "./Puffer_loeschen_unidb.php",
				type: 'POST',
				data: {SQL: SQL_Text, ID: ausgewaehlt},
				async: false
			});
			table.deleteRow(z);
			document.getElementById("Anzahl_" + ausgewaehlt).innerHTML = parseInt(document.getElementById("Anzahl_" + ausgewaehlt).innerHTML) - 1;
		}
	}
}
</script>
</body>
</html>
