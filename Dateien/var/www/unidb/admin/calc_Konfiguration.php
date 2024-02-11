<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../jquery-ui.css">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script src="../jquery-3.3.1.min.js"></script>

<?php
	session_start();
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	include('../conf_DH_schreiben.php');
	header("X-XSS-Protection: 1");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("calc_Konfiguration.php");
	echo "<title>".$Text[0]."</title>";
	echo "</head>";
	echo "<body class='allgemein'>";
	echo "<form action='calc_Konfiguration.php' method='post' target='_self' id='phpform' name='Phpform'>";
	echo "<table class='Text_einfach' style='position: absolute; top: 10px; left: 10px;' cellpadding='5px'>";
	echo "<tr><td class='Text_fett'>".$Text[1]."</td></tr>";
	if($Speichern == $Text[2]) {
		if($Pointname_orig != $Pointname) {
			$query = "UPDATE `Tagtable` SET `Tagname` = '".$Pointname."' WHERE `Point_ID` = ".$Point_ID." AND `Tagname` = '".$Pointname_orig."';";
			Kol_schreiben($query);
		}
		$query = "UPDATE `Points` SET `Intervall` = '".$Intervall."' `Path` = '".$Path."', `Pointname` = '".$Pointname."', `Description` = '".$Beschreibung."', `Info` = '".$Formel."', `EUDESC` = '".$Einheit."' WHERE `Point_ID` = ".$Point_ID.";";
		Kol_schreiben($query);
		//Meldung ausgeben
		$query = "SELECT `Interface` FROM `Points` WHERE `Point_ID` = ".$Point_ID.";";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Rechner = Schnittstelle_Rechner($line["Interface"]);
		Meldung_schreiben($line["Interface"], "Points einlesen", $Rechner);
	}
	if ($Point_ID > 0) {
		$query="SELECT `Point_ID`, `Pointname`, `Path`, `Info`, `Description`, `EUDESC`, `Intervall` FROM `Points` WHERE `Point_ID` = ".$Point_ID.";";
	   $stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
   	mysqli_stmt_close($stmt);
   } else {
   	if ($Path > "" and $Pointname > "") {
			$query="SELECT `Point_ID`, `Pointname`, `Path`, `Info`, `Description`, `EUDESC`, `Intervall` FROM `Points` WHERE `Pointname` = '".$Pointname."' AND `Path` = '".$Path."';";
   		$stmt = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
   		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			mysqli_stmt_close($stmt);
   	}
   }
	echo "<tr><td class='Text_einfach' colspan='3'><textarea id='formel' name='Formel' rows='10' cols='62'>".$line["Info"]."</textarea></td></tr>\n";
	echo "<tr><td align='right'><input class='Schalter_Element' id='berechnen' name='Berechnen' value='".$Text[3]."' type='button' style='border-left: 0px;' onclick='Formel_berechnen();'></td><td class='Text_fett' align='right'>".$Text[4]."</td><td><input class='Text_Element' name='Ergebnis' id='ergebnis' value='' type='Text' style='width:220px'></td></tr>\n";
	if($line["Path"] == "") {
		$Pfad = "%";
	} else {
		$Pfad = $line["Path"];
	}
	echo "<tr><td></td><td class='Text_fett' align='right'>".$Text[5]."</td><td><input class='Text_Element' name='Path' id='path' value='".$Pfad."' type='Text' style='width:220px'></td></tr>\n";
	if($line["Pointname"] == "") {
		$Punktname = "%";
	} else {
		$Punktname = $line["Pointname"];
	}	
	echo "<tr><td align='right'><input class='Schalter_Element' id='suchen' name='Suchen' value='".$Text[6]."' type='button' style='border-left: 0px;' onclick='Point_suchen_dialog();'></td><td class='Text_fett' align='right'>".$Text[7]."</td><td><input class='Text_Element' name='Pointname' id='pointname' value='".$Punktname."' type='Text' style='width:220px'></td></tr>\n";
	echo "<tr><td><font size='-2'><div id='Point_ID_Text'>Point_ID: ".$line["Point_ID"]."</div></font></td><td class='Text_fett' align='right'>".$Text[8]."</td><td><input class='Text_Element' name='Beschreibung' id='beschreibung' value='".$line["Description"]."' type='Text' style='width:220px'></td></tr>\n";
	echo "<tr><td></td><td class='Text_fett' align='right'>".$Text[11]."</td><td><input class='Text_Element' name='Einheit' id='einheit' value='".$line["Intervall"]."' type='Text' style='width:50px'></td></tr>\n";
 	echo "<tr><td align='right'><input class='Schalter_Element' id='speichern' name='Speichern' value='".$Text[9]."' type='submit' style='border-left: 0px;'></td><td class='Text_fett' align='right'>".$Text[10]."</td><td><input class='Text_Element' name='Einheit' id='einheit' value='".$line["EUDESC"]."' type='Text' style='width:50px'></td></tr>\n";
	echo "</table>\n";
	echo "<input name='Point_ID' id='point_ID' value='".$line["Point_ID"]."' type='hidden'>\n";
	echo "<input name='Pointname_orig' id='pointname_orig' value='".$line["Pointname"]."' type='hidden'>\n";
?>

</form>
</body>
<script type='text/javascript'>
var panel;
var strReturn = "";
function Point_suchen_dialog() {
	try {
		panel.close('pointsuche');
	} catch (err) {}
	jQuery.ajax({
		url: "./Point_suchen.php?Pointname=" + document.Phpform.elements["pointname"].value + "&Pfad=" + document.Phpform.elements["path"].value + "&Schnittstelle=calc",
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
		contentSize: '550 360',
		content: strReturn,
		contentOverflow: 'hidden',
		callback: function (panel) {
			jsPanel.pointerup.forEach(function (item) {
           	panel.footer.querySelector('#btn-close').addEventListener(item, function () {
					panel.close('pointsuche');
	         });
			});
	  	}
	});
}

function uebertragen() {
	try {
		var Ergebnis = document.Point_finden.Ergebnis.value.split(" - ");
		document.getElementById("Point_ID_Text").innerHTML = "Point_ID: " + Ergebnis[0];
		document.Phpform.elements["point_ID"].value = Ergebnis[0];
		document.Phpform.elements["path"].value = Ergebnis[1];
		document.Phpform.elements["pointname"].value = Ergebnis[2];
		document.Phpform.submit();
	} catch (err) {}
}

function Formel_berechnen() {
	document.Phpform.elements["ergebnis"].value = "";
	jQuery.ajax({
		url: "./DH_berechnen.php?Ausdruck=" + document.Phpform.elements["formel"].value.replace(/\+/g,"@@@"),
		success: function (html) {
   		document.Phpform.elements["ergebnis"].value = html;
		},
  		async: false
  	});
}
</script>
</html>
