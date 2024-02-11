<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<link href="./css/ip.grid.css" rel="stylesheet" />
<link href="Navigation.css" rel="stylesheet">
<script src="./jquery.js"></script>
<script src="./scripts/jquery-ui-1.9.2.custom.min.js"></script>
<script src="./ipgrid/scripts/ip.grid.js"></script>
<script src="./scripts/jscolor.js"></script>
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="../Fenster/extensions/hint/jspanel.hint.js"></script>
<script src="./ip_Grid.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<style>
	body {font-family: Arial;}
	.gridContainer {
		position:relative;
		width:1200px;
		height:700px;
	}
	#Tab {
		width:100%;
		height:100%;
	}

	@media screen and (max-width: 600px) { 
	.schaltfl_mobil {
		font-size: 90%;
	}
	.Text_Element {
		font-size: 85%;
	}
	.Schalter_fett_Element {
		font-size: 85%;
	}
	.Textbox {
		Bereich_mobi: 100%;
	}
}
</style>
</head>
<body style="background-color: #d6d6d6">
<div id="search_result_container" class="jsPanel-depth-3" style="display: none;">
	<p style="margin: .4rem .4rem;text-align: center;"></p>
	<table id="search_result" class="table"><tbody></tbody></table>
</div>

<?php
	include('Sitzung.php');
	$Text = Translate("IP_Grid.php");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include 'mobil.php';
	if ($Baum_ID > 0){
		if ($Timestamp > ""){
			$query = "SELECT `Timestamp`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Spaltenformate' as Char) as `Spaltenformate`, column_get(`Inhalt`, 'Blatt' as Char) as `Blatt`, column_get(`Inhalt`, 'Zeilen' as Char) as `Zeilen`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
			$Abfrage = "Baumhistorie";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
		} else {
			$query = "SELECT `Timestamp`, `geloescht`, `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Spaltenformate' as Char) as `Spaltenformate`, column_get(`Inhalt`, 'Blatt' as Char) as `Blatt`, column_get(`Inhalt`, 'Zeilen' as Char) as `Zeilen`, column_get(`Inhalt`, 'Spalten' as Char) as `Spalten` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
			$Abfrage = "Baum";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
		}
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$geloescht = $line["geloescht"];
		if($Abfrage == "Baum") {
			$Timestamp = "";
		} else {
			$Timestamp = $line["Timestamp"];
		}
		echo "<title>".$line["Bezeichnung"]."</title>\n";
		echo "</head>\n";
		echo "<body class='allgemein'>\n";
		echo "<form id='Formular' action='./Baum2.php' method='post' target='Baum' name='Formular'>";
		echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
		echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
		echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
		echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
		echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
		echo "<textarea id='savestr' style='display:none;' name ='Blatt'>".$line["Blatt"]."</textarea>";
		echo "<input type= 'hidden' name='Baum_ID' value='".$Baum_ID."'>";
		echo "<input id= 'Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
		echo "<input type= 'hidden' name='Eltern_ID' value='".$Eltern_ID."'>";
		echo "<input type= 'hidden' name='Zeilen' value='".$line["Zeilen"]."'>";
		echo "<input type= 'hidden' name='Spalten' value='".$line["Spalten"]."'>";
		echo "<textarea id='spaltenformate' style='display:none;' name ='Spaltenformate'>".$line["Spaltenformate"]."</textarea>";
		mysqli_stmt_close($stmt);
	}
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[105];
		exit;
	}

if($mobil == 1) {
	include ("./IP_Grid_nav_mobil.php");
} else {
	include ("./IP_Grid_nav.php");
}
?>

</form>

<div class="gridContainer">
	<div id="Tab"></div>
</div>
</body>
</html>