<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script type="text/javascript" src="./jquery.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript">
function Tag_suchen_dialog() {
	var strReturn = "";
	try {
		jsPanel.getPanels()[0].close();
	} catch (err) {}
	jQuery.ajax({
		url: "admin/Tags_suchen.php?Tagname=" + document.editieren.elements["tagname"].value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'tagsuche',
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '550 420',
		content: strReturn,
  		contentOverflow: 'hidden',
  		callback: function (panel) {
  			jsPanel.pointerup.forEach(function (item) {
           	panel.footer.querySelector('#btn-close').addEventListener(item, function () {
           		panel.close('tagsuche');
	         });
        });
	  	}
	});
}

function uebertragen() {
	try {
		document.editieren.elements["tagname"].value = document.getElementById("Liste").selectedOptions[0].value;
		jsPanel.getPanels()[0].close();
	} catch (err) {}
}
</script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
$Text = Translate("DH_Handeingabe.php");
echo "<title>".$Text[0]."</title>\n";
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>\n";
echo "<body class='allgemein'>\n";
include ('./mobil.php');
include ('./conf_DH.php');

echo "<span class='Text_fett' id='Schrift'; style='font-size: 20px'>".$Text[0]."</span><br><br>";
if ($Aktion==$Text[2]){
	$query = "SELECT `Point_ID` FROM `Tags` WHERE CONCAT(`Path`,`Tagname`) = ?;";
  	$stmt = mysqli_prepare($dbDH, $query);
  	mysqli_stmt_bind_param($stmt, "s", $Tag);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line_Punkt = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if($Zeitstempel==$Text[1]) {
		$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',(time()));
		Kol_schreiben("INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES ('".$line_Punkt['Point_ID']."', '".$Zeitstempel."', '".$Wert."');");
	}else {
		//Die Zeitzahl wird nur berechnet um zu kontrollieren, ob ein gültiges Datum eingegeben wurde
		$Zeitzahl = strtotime($Zeitstempel);
	 	Kol_schreiben("INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES ('".$line_Punkt['Point_ID']."', '".$Zeitstempel."', '".$Wert."');");
	}
}
echo "<form action='DH_Handeingabe.php' method='post' target='_self' id='editieren' name='editieren'>";
echo "<table cellpadding = '5px'>";
echo "<tr><td></td><td class='Text_fett' style='color: red;' colspan='2'>Tag mit Pfad eingeben!</td></tr>";
echo "<tr><td class='Text_einfach' align='right'>Tagname</td><td><input class='Text_Element' id='tagname' name='Tag' type='text' size='20' maxlength='255'></td><td><input class='Schalter_Element' type='button' name='Tag_suchen_Schalter' value='".$Text[3]."' onclick='Tag_suchen_dialog()'></td></tr>";
echo "<tr><td class='Text_einfach' align='right'>".$Text[4]."</td><td><input class='Text_Element' name='Zeitstempel' type='text' size='20' maxlength='20'></td><td class='Text_einfach'>".$Text[6]." = <b>".$Text[1]."</b></td></tr>";
echo "<tr><td class='Text_einfach' align='right'>".$Text[5]."</td><td><input class='Text_Element' name='Wert' type='text' size='20' maxlength='20'></td></tr>";
echo "<tr><td></td><td><input class='Schalter_Element' value='".$Text[2]."' type='submit' name='Aktion'></td></tr>";
echo "</form>";
echo "</table>";

// schliessen der Verbindung
mysqli_close($db);
?>
</div></div>
</body>
</html>
