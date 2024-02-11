<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./scripts/jquery-3.6.0.js"></script>
<script src="./jexcel/jexcel.js"></script>
<link rel="stylesheet" href="./jexcel/jexcel.css" type="text/css" />
<script src="./jexcel/jsuites.js"></script>
<link rel="stylesheet" href="./jexcel/jsuites.css" type="text/css" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<link rel="stylesheet" type="text/css" href="./JS_Tab/dist/icons.css?family=Open+Sans|Roboto|Dosis|Material+Icons">
<?php
	include('./Sitzung.php');
	include 'mobil.php';
	$Text = Translate("IP_Grid.php");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Zeitstempel = $Timestamp;
	if ($Baum_ID > 0){
		if ($Timestamp > ""){
			$query = "SELECT `Baum_ID`, `Server_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'data' as Char) as `data`, column_get(`Inhalt`, 'config' as Char) as `config`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten`, column_get(`Inhalt`, 'Tabs' as Char) as `Tabs`, column_get(`Inhalt`, 'mZellen' as Char) as `mZellen`, column_get(`Inhalt`, 'Kommentare' as Char) as `Kommentare` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
			$Abfrage = "Baumhistorie";
		} else {
			$query = "SELECT `geloescht`, `Baum_ID`, `Server_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'data' as Char) as `data`, column_get(`Inhalt`, 'config' as Char) as `config`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten`, column_get(`Inhalt`, 'Tabs' as Char) as `Tabs`, column_get(`Inhalt`, 'mZellen' as Char) as `mZellen`, column_get(`Inhalt`, 'Kommentare' as Char) as `Kommentare` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
			$Abfrage = "Baum";
		}
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo "<title>".$line["Bezeichnung"]."</title>\n";
		echo '</head>';
		echo '<body class="allgemein" onresize="doresize();" style="background-color: #ececec;">';
		echo "<form id='Formular' action='./Baum2.php' method='post' target='Baum' name='Formular'>";
		echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
		echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
		$geloescht = $line["geloescht"];
		if($Abfrage == "Baum") {
			$Timestamp = "";
		} else {
			$Timestamp = $line["Timestamp"];
		}
		mysqli_stmt_close($stmt);
	}
	echo "<input type= 'hidden' name='Baum_ID' value='".$Baum_ID."'>";
	echo "<input type= 'hidden' name='Server_ID' value='".$line["Server_ID"]."'>";
	echo "<input type= 'hidden' name='Eltern_ID' value='".$line["Eltern_ID"]."'>";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
//	$line["data"] = str_replace("@@@", "'", $line["data"]);
	echo "<textarea id='savestr' style='display:none;' name ='data'>".$line["data"]."</textarea>";
	echo "<textarea id='config' style='display:none;' name ='config'>".$line["config"]."</textarea>";
	echo "<textarea id='Spalten' style='display:none;' name ='Spalten'>".$line["Spalten"]."</textarea>";
	echo "<textarea id='kommentare' style='display:none;' name ='Kommentare'>".$line["Kommentare"]."</textarea>";
//	$line["Kommentare"] = str_replace("@@@", "'", $line["Kommentare"]);
	echo "<textarea id='mzellen' style='display:none;' name ='mZellen'>".$line["mZellen"]."</textarea>";
	echo "<textarea id='tabs' style='display:none;' name ='Tabs'>".$line["Tabs"]."</textarea>";
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[105];
		exit;
	}
	if($mobil == 1) {
		include ("./Tabellenkalk_nav_mobil.php");
	} else {
		include ("./Tabellenkalk_nav.php");
	}
?>
<input type="hidden" id='aktion' name="Aktion", value="edit_Tabkalk_speichern">
</form>

<div id="spreadsheet" style="top: 50px;"></div>

<script src="./Tabellenkalk.js"></script>
</body>
</html>
