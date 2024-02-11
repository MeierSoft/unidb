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
$Text = Translate("log.php");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";

echo "<table class='table'>\n";
	echo "<tr>\n";
echo "<td></td>\n";
echo "<td class='Text_fett' height='30'>\n";
	echo $Text[1]."\n";
echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
echo "<td class='Text_fett' align='right'>\n";
	echo $Text[2]."\n";
echo "</td>\n";
echo "<td>\n";
	echo "<input class='Text_Element' id='von' value='' type='Text' style='width:130px'>\n";
echo "</td>\n";
echo "<td class='Text_fett' align='right'>\n";
	echo $Text[3]."\n";
echo "</td>\n";
echo "<td>\n";
	echo "<input class='Text_Element' id='bis' value='' type='Text' style='width:130px'>\n";
echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
echo "<td class='Text_fett' align='right'>\n";
	echo $Text[4]."\n";
echo "</td>\n";
echo "<td>\n";
	echo "<input class='Text_Element' id='source' value='' type='Text' style='width:130px'>\n";
echo "</td>\n";
echo "<td class='Text_fett' align='right'>\n";
	echo $Text[5]."\n";
echo "</td>\n";
echo "<td>\n";
	echo "<input class='Text_Element' id='text' value='' type='Text' style='width:270px'>\n";
echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
echo "<td>\n";
echo "</td>\n";
echo "<td height='30'>\n";
	echo "<button class='Schalter_Element' type='submit' id='filtern' onclick='filtern();'>".$Text[6]."</button>\n";
echo "</td>\n";
	echo "</tr>\n";
echo "</table>\n";
echo "<br><br>\n";
echo "<div id='Tabelle'></div>\n";
echo "<br><br>\n";
echo "<button class='Schalter_Element' id='markierte_loeschen' onclick='loeschen();'>".$Text[8]."</button>\n";
?>
<script type="text/javascript">
var table = new Tabulator("#Tabelle", {
	layout:"fitDataTable",
	pagination:"remote",
	ajaxParams:{page:1,size:20},
	paginationSize:20,
	paginationSizeSelector:[5, 10, 15, 20, 30, 100, 1000],
	ajaxURL:"./Log_lesen.php",
	placeholder:<?php echo "'".$Text[7]."'";?>,
	columns:[
		{formatter:"rowSelection", titleFormatter:"rowSelection", hozAlign:"center", headerSort:false, cellClick:function(e, cell){
        cell.getRow().toggleSelect();
      }},
		{title:"id", field:"id", formatter:"number", sorter:"number"},
		{title:"Timestamp", field:"Timestamp", sorter:"string"},
		{title:"Source", field:"Source", sorter:"string"},
		{title:"Text", field:"Text", sorter:"string"},
	],
});

function loeschen() {
	var entfernt = 0;
	for (z = 0;z < table.rowManager.rows.length; z++) {
		if (table.rowManager.rows[z].cells[0].element.firstChild.checked == true) {
			entfernt = 1;
			SQL = "DELETE FROM `Log` WHERE `Timestamp` = '" + table.rowManager.rows[z].data.Timestamp + "' AND `Source` = '" + table.rowManager.rows[z].data.Source + "' AND `Text` = '" + table.rowManager.rows[z].data.Text + "';";
			jQuery.ajax({
				url: "./SQL_ausfuehren.php?DB=DH",
				type: 'POST',
				data: {SQL_Text: SQL},
				async: false
			});
		}
	}
	if (entfernt == 1) {table.setData("./Log_lesen.php");}
}

function filtern() {
	table.setData("./Log_lesen.php?von=" + document.getElementById("von").value + "&bis=" + document.getElementById("bis").value + "&source=" + document.getElementById("source").value + "&text=" + document.getElementById("text").value + "&size=" + table.options.paginationSize + "&page=1");
}
</script>
</body>
</html>
