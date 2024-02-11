<link rel="stylesheet" type="text/css" href="./jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="./jquery.ganttView.css" />
<script type="text/javascript" src="./jquery-1.11.2.min.js"></script>
<?php
include('./Sitzung.php');
header("X-XSS-Protection: 1");
//Verbindung zur Datenbank herstellen
$_SESSION['DB_Server'] = "localhost";
$db_Satz = mysqli_connect($_SESSION['DB_Server'],$_SESSION['DB_User'],$_SESSION['DB_pwd'],$_SESSION['Form_DB']);
mysqli_query($db_Satz, 'set character set utf8;');
$query = "SELECT `Projekte_ID` AS `id`, `Projektname` AS `name` FROM `Projekte` WHERE `Status` != 0 ORDER BY `Projekte_ID` ASC;";
$stmt = mysqli_prepare($db_Satz,$query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ganttData = array();
while($line_Projekte = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$query = "SELECT `Aufgabenname` AS `name`, `Start` AS `start`, `Ende` AS `end`, `Beschreibung` AS `beschreibung` FROM `Aufgaben` WHERE `Projekte_ID` = ".$line_Projekte["id"]." AND `erledigt` < 1 AND `aktiv` != 0 ORDER BY `Start` ASC, `Ende` ASC;";
	$stmt1 = mysqli_prepare($db_Satz,$query);
	mysqli_stmt_execute($stmt1);
	$result1 = mysqli_stmt_get_result($stmt1);
	$series = array();
	while($line_Aufgaben = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
		$Felder = array();
		$Felder["name"] = $line_Aufgaben["name"];
		$Felder["start"] = substr($line_Aufgaben["start"],0,4).",".substr($line_Aufgaben["start"],5,2).",".substr($line_Aufgaben["start"],8,2);
		$Felder["end"] = substr($line_Aufgaben["end"],0,4).",".substr($line_Aufgaben["end"],5,2).",".substr($line_Aufgaben["end"],8,2);
		$Felder["beschreibung"] = html_entity_decode($line_Aufgaben["beschreibung"]);
		$series[] = $Felder;
	}
	$line_Projekte["series"] = $series;
	$ganttData[] = $line_Projekte;
}
$ganttData = json_encode($ganttData);
echo "<input id='ganttData' name='ganttData' type='hidden' value='".$ganttData."'>\n";
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt1);
mysqli_close($db_Satz);
?>
<script type="text/javascript">
	$(window).on('load',function() {
		document.getElementsByClassName("ganttview-slide-container")[0].style.height = (document.getElementsByClassName("ganttview-slide-container")[0].scrollHeight + 10).toString() + "px";
		document.getElementsByClassName("ganttview-slide-container")[0].style.width = frameElement.clientWidth;
	});
</script>
<div id="ganttChart"></div>
