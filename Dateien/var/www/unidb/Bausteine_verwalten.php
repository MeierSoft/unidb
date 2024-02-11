<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>
<head>

<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<link href="./editor/jHtmlArea.css" rel="stylesheet">
<link href="./editor/jHtmlArea.ColorPickerMenu.css" rel="stylesheet">
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<script src="./editor/jHtmlArea-0.8.js"></script>
<script src="./editor/jHtmlArea.ColorPickerMenu-0.8.min.js"></script>
<script type="text/javascript" src="./Bilder_bauen.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/gauge.js"></script>
<style type="text/css">
#DH_Bereich {
    width: 100%;
    height: 800px;
}
.rectangle {
    border: 1px dotted #000000;
    position: absolute;
}
</style>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	$Text = Translate("Bausteine_verwalten.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<font size='2'>\n";



	if ($_SESSION['Zeitpunkt']=="") {
		$Zeitpunkt="jetzt";
	} else {
		$Zeitpunkt=$_SESSION['Zeitpunkt'];
	}
   include '../mobil.php';
	header("X-XSS-Protection: 1");
	
	if($speichern == $Text[1]) {
		$Bezeichnung = htmlentities(mysqli_real_escape_string($db, $Bezeichnung));
		$Inhalt = htmlentities(mysqli_real_escape_string($db, $Inhalt));
		$query = "INSERT INTO Bausteine (User_ID, Bezeichnung, Inhalt) VALUES (?, ?, COLUMN_CREATE('Inhalt', ?));";
		uKol_schreiben(1,$query, "iss", [$_SESSION['User_ID'], $Bezeichnung, $Inhalt]);
	}
	if($speichern == $Text[2]) {
		$Inhalt=str_replace(" ontouchend=\"auswaehlen(this);\"","",$Inhalt);
		$Inhalt=str_replace(" onclick=\"auswaehlen(this);\"","",$Inhalt);
		$Inhalt=str_replace("\r\n\r\n","",$Inhalt);
		$Inhalt=str_replace("'","\'",$Inhalt);
		$Inhalt = htmlentities($Inhalt);
		$Bezeichnung = htmlentities($Bezeichnung);
		$query="UPDATE `Bausteine` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`,'Inhalt','".$Inhalt."') WHERE `Baustein_ID` = ?;";
		uKol_schreiben(1,$query, "si", [$Bezeichnung, $Baustein_Liste]);
	}
	if($speichern == $Text[3]) {
		$query="DELETE FROM `Bausteine` WHERE `Baustein_ID` = ?;";
		uKol_schreiben(1,$query, "i", [$Baustein_Liste]);
	}
	//Bausteine einlesen
	$query = "select `Baustein_ID`, `Bezeichnung` FROM `Bausteine` WHERE User_ID = 0 or User_ID = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "i", $_SESSION['User_ID']);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	echo "<form action='Bausteine_verwalten.php' method='post' target='_self' id='phpform' name='phpform'>";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	echo "<table class='Text_einfach' style='background: #FCEDD9; height: 50px;'><tr><td>".$Text[4]."</td><td>".$Text[5]."</td><td></td><td></td><td></td><td></td><td>".$Text[6]."</td><td></td><td>".$Text[7]."</td><td></td><td></td><td></td><td>".$Text[8]."</td></tr>\n";
	echo "<tr><td><select class='Auswahl_Liste_Element' id='baustein_liste' name='Baustein_Liste' size='1' onchange = 'Baustein_einlesen();'>";
	echo "<option value = '0'></option>\n";
	if($Baustein_Liste == null) {
		$Liste = 0;
	} else {
		$Liste = intval($Baustein_Liste);
	}
	$Baustein_Name = "";
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		if($line["Baustein_ID"] == $Liste) {
			echo "<option value = '".$line["Baustein_ID"]."' selected>".$line["Bezeichnung"]."</option>\n";
			$Baustein_Name = $line["Bezeichnung"];
		} else {
			echo "<option value = '".$line["Baustein_ID"]."'>".$line["Bezeichnung"]."</option>\n";
		}
	}
	echo "</select></td>\n";
	mysqli_stmt_close($stmt);
	echo "<td><input class='Text_Element' name='Bezeichnung' value='".$Baustein_Name."' type='Text'></td>\n";
	echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[2]."' type='submit' onclick='Baustein_speichern();'></td>\n";
	echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[1]."' type='submit' onclick='neuer_Baustein();'></td>\n";
	echo "<td><input class='Schalter_Element' name='speichern' value='".$Text[3]."' type='submit'></td>\n";
	echo "<td width='20px'></td>\n";
	echo "<td><input class='Text_Element' name='Raster' value='10' type='Text' size='3'></td>\n";
	echo "<td width = '20px'></td>\n";
	echo "<td><input class='Schalter_Element' name='erstellen' value='".$Text[9]."' type='button' onclick='Elementtyp_aussuchen();'></td>\n";
	echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[10]."' type='button' onclick='Element_entfernen();'></td>\n";
	echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[11]."' type='button' onclick='Element_Dialog_oeffnen();'></td>\n";
	echo "<td width = '20px'></td>\n";
	echo "<td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('25');\"></td>\n";
	echo "</tr></table>\n";
	echo "</font>";
	//Alle Elementvorlagen finden und auflisten
	$Typauswahl = "";
	$query="SELECT `Vorlage_ID`, `Bezeichnung` FROM `Vorlagen` WHERE `Typ` = 'Element';";
   $stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$Typauswahl=$Typauswahl.$line['Bezeichnung'].";";
	}
	mysqli_stmt_close($stmt);
	
	if($Baustein_Liste > 0) {
		$query = "select `Baustein_ID`, `Bezeichnung`, column_get(`Inhalt`, 'Inhalt' as CHAR) as `Inhalt` FROM `Bausteine` WHERE Baustein_ID = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "i", $Baustein_Liste);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Text = html_entity_decode($line["Inhalt"]);
		$Text = str_replace("'", "&quot;", $Text);
		$Text = str_replace(" class=\"\"", "", $Text);
		$Text = str_replace(" class=\"context-menu-two\"", "", $Text);
		$Text = str_replace(" onclick=\"auswaehlen(this);\"", "", $Text);
		$Text = str_replace(" ontouchend=\"auswaehlen(this);\"", "", $Text);
		$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
	} else {
		$Text = "";
		$Bezeichnung = "";
	}
	if ($mobil==1){
		echo "<input name='mobil' value='1' type='hidden'>\n";
	} else {
		echo "<input name='mobil' value='0' type='hidden'>\n";
	}
	echo "<input id ='Inhalt' name='Inhalt' value='".$Text."' type='hidden'>\n";
	echo "<input id ='Typauswahl' name='Typauswahl' value='".$Typauswahl."' type='hidden'>\n";
	echo "</div>\n";	
	echo "<div id='DH_Bereich' style='font-size: 12px;'>\n";
	if ($mobil==1){
		$Text = str_replace("<div", "<div ontouchend=\"auswaehlen(this);\"", $Text);
	}else{
		$Text = str_replace("<div", "<div onclick=\"auswaehlen(this);\"", $Text);
	}
	echo $Text;
	echo "</div>\n";
	
	echo "<script type='text/javascript'>\n";
	mysqli_close($db);
?>
	initDraw(document.getElementById('DH_Bereich'));
	DH_Elemente=[];
	$(window).on('load',function() {;
		einrichten();
		lesen();
		T_Text = JSON.parse(document.getElementById("translation").value);
		var refreshId = setInterval(function() {lesen();}, 60000);
	});
	</script>

<script type="text/javascript">
document.addEventListener('jspanelresizestop', function (event) {
    if (event.detail === 'ausgewaehlt') {
    	Groesse_anpassen();
	}
});

function Baustein_einlesen() {
	document.getElementById("phpform").submit();
}

function neuer_Baustein() {
	var Bezeichnung = prompt(T_Text[13], "");
	if (Bezeichnung != null && Bezeichnung != "") {
		document.phpform.Bezeichnung.value = Bezeichnung;
		document.getElementById("DH_Bereich").innerHTML="";
		abspeichern();
	}
}

function Baustein_speichern() {
	var Auswahl = document.getElementById("DH_Bereich");
	var oben = 10000000;
	var links = 10000000;
	for (i=0; i < Auswahl.childNodes.length; i++) {
		Element = Auswahl.childNodes[i];
		try {
			if (oben > parseInt(Element.style.top)){oben = parseInt(Element.style.top);}
			if (links > parseInt(Element.style.left)){links = parseInt(Element.style.left);}
		} catch (err) {}
	}
	oben = oben - 60;
	for (i=0; i < Auswahl.childNodes.length; i++) {
		Element = Auswahl.childNodes[i];
		try {
			Element.style.top = (parseInt(Element.style.top) - oben).toString() + "px";
			Element.style.left = (parseInt(Element.style.left) - links).toString() + "px";
		} catch (err) {}
	}
	abspeichern();
}
</script>
</body>
</html>