<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="refresh" content="60">
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript">
	var panel;
	var T_Text = new Array;

	$(window).on('load',function() {;
		T_Text = JSON.parse(document.getElementById("translation").value);
	});
	
	function Tagdetails(Tag_ID) {
		var strReturn = "";
		try {
			panel.close('tagdetails');
		} catch (err) {}
		jQuery.ajax({
			url: "./DH_Tagdetails.php?Tag_ID=" + Tag_ID,
			success: function (html) {
   			strReturn = html;
			},
  			async: false
  		});
		panel = jsPanel.create({
			dragit: {
        		snap: true
        	},
			id: 'tagdetails',
			position: 'left-top 10 10',
			theme: 'primary',
			contentSize: '600 600',
			headerTitle: T_Text[19],
			content: strReturn,
		});
	}
	function Regler_schieben() {
		var Zeitspanne = parseFloat(document.getElementById("zeitspanne").value);
		var pos_alt = parseFloat(document.getElementById("pos_Regler_alt").value);
		var alter_Zeitpunkt = document.getElementById("zeitstpl").value;
		if (alter_Zeitpunkt == T_Text[1]){
			var Zeitobjekt = new Date();
		} else {
			var Zeitobjekt = new Date(alter_Zeitpunkt);
		}
		alter_Zeitpunkt = Zeitobjekt.getTime() / 1000;
		var neuer_Zeitpunkt = alter_Zeitpunkt - Zeitspanne / 100 * (pos_alt - parseFloat(document.getElementById("schieberegler").value));
		var Zeitstempel = new Date(neuer_Zeitpunkt * 1000);
   	var Jahr = Zeitstempel.getFullYear(Zeitstempel).toString();
	   var Monat = (Zeitstempel.getMonth(Zeitstempel) + 1).toString();
   	if (Monat.length == 1){Monat = "0" + Monat;}
	   var Tag = Zeitstempel.getDate(Zeitstempel).toString();
   	if (Tag.length == 1){Tag = "0" + Tag;}
   	document.getElementById("zeitstpl").value = Jahr + "-" + Monat + "-" + Tag + " " + Zeitstempel.toLocaleTimeString('de-DE');
   	document.getElementById("pos_Regler_alt").value = document.getElementById("schieberegler").value;
   	document.forms["Einstellungen"].submit();
	}
	
	function Vers_wiederherstellen(Variante) {
		if (Variante == "wiederherstellen") {
			document.forms["phpform"].aktion.value = "speichern";
		} else {
			document.forms["phpform"].aktion.value = "löschen";
		}
		document.forms["phpform"].submit();
	}
</script>
<?php
	include('./Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include ('./conf_DH.php');
	include('./Trend_funktionen.php');
	$Text = Translate("Gruppe.php");
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[20];
		exit;
	}
	include ('./mobil.php');
	if($Aktion == "speichern") {
		$Bezeichnung = strip_tags($Bezeichnung);
		$query = "UPDATE `Baum` SET `Bezeichnung` = ?, `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(1,$query, "ssii", [$Bezeichnung, $Tags, $Baum_ID, $Server_ID]);
		$query="UPDATE `Baum` SET `Inhalt` = COLUMN_ADD(`Inhalt`, 'Tags_Pfad', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		uKol_schreiben(0,$query, "sii", [$Tags_Pfad, $Baum_ID, $Server_ID]);		
	}
	if($Aktion == "löschen") {
		$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
		$Timestamp = "";
	}

if(isset($_POST['Intervall'])) {$_SESSION['Intervall']=$_POST['Intervall'];};
if (!isset($_SESSION['Intervall'])){
   $_SESSION['Intervall'] = 60;
   $Intervall=60;
} else {
	$Intervall=$_SESSION['Intervall'];
}

if (!isset($_SESSION['Zeitstempel'])){
   $_SESSION['Zeitstempel'] = $Text[1];
   $Zeitstempel=$_SESSION['Zeitstempel'];
}
if ($Zeitstempel==""){$Zeitstempel = $Text[1];}

if ($Spanne<1){$Spanne=86400;}
if ($Zeitstempel==$Text[1] or $Zeitstempel==""){
	$Zeitpunkt=time();
}else{
	$Zeitpunkt=strtotime($Zeitstempel);
}

if ($Spanne<1){$Spanne=1;}
If ($Spanne=='1 h'){
	$Spanne=3600;
}
If ($Spanne=='4 h'){
	$Spanne=14400;
}
If ($Spanne=='8 h'){
	$Spanne=28800;
}
If ($Spanne==$Text[2]){
	$Spanne=86400;
}
If ($Spanne==$Text[3]){
	$Spanne=172800;
}
If ($Spanne==$Text[4]){
	$Spanne=604800;
}
If ($Spanne==$Text[5]){
	$Spanne=1209600;
}
If ($Spanne==$Text[6]){
	$Spanne=2592000;
}
If ($Spanne==$Text[7]){
	$Spanne=31536000;
}
If ($schieben=='<'){
	$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt-$Spanne));
}
If ($schieben=='>'){
	$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt+$Spanne));	
}
If ($schieben==$Text[1]){
	$Zeitstempel=$Text[1];
}
if ($Baum_ID > 0){
	if ($Timestamp > ""){
		$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Tags' as CHAR) as `Tags` FROM `Baumhistorie` WHERE Baum_ID = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
	} else {
		$query = "SELECT `Timestamp`, `geloescht`, `Bezeichnung`, column_get(`Inhalt`, 'Tags' as CHAR(1024)) as `Tags` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Tagliste=explode(",", $line["Tags"]);
	mysqli_stmt_close($stmt);
	$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
	echo "<title>".$Bezeichnung."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<form id='phpform' name='phpform' action='Gruppe.php' method='post' target='_self'>";
	echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
	echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
	echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
	echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
	echo "<input type='hidden' id='bezeichnung' name='Bezeichnung' value = '".$line["Bezeichnung"]."'>\n";
	echo "<input type='hidden' id='tags' name='Tags' value = '".$line["Tags"]."'>\n";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
}
//if ($mobil==1){echo "&nbsp;<a href='./Baum2.php' target='_self'>".$Text[12]."</a><br>";}
if($statisch !== "1") {
	if($mobil == 1) {
		include ("./Gruppe_navi_mobil.php");
	} else {
		include ("./Gruppe_navi.php");
	}


	echo "<input type = 'hidden' name = 'Intervall' value= '".$Intervall."'>\n";
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	echo "<input type = 'hidden' id = 'zeitspanne' value= '".$Spanne."'>\n";
	if ($Pos_Regler_alt == ""){$Pos_Regler_alt = "100";}
	echo "<input type = 'hidden' id = 'pos_Regler_alt' name = 'Pos_Regler_alt' value= '".$Pos_Regler_alt."'>\n";
	echo "</form>\n";
}
echo "<span style='font-family:Arial; font-size:12px'><table cellpadding='5px'><br>\n";
for($i=0;$i < count($Tagliste); $i++) {
	//Point_ID für den Tag finden
	$Point_ID = Point_ID_finden($Tagliste[$i], $dbDH);
	if ($Zeitstempel == $Text[1]){	
		$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID =? ORDER BY Timestamp DESC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Point_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Wert = round($line_Value['Value'],$line_Tag["Dezimalstellen"]);
		$Zeitpunkt = $line_Value['Timestamp'];
	} else {
		$Werte = lesen("rV", $Point_ID, $Zeitstempel, $Zeitstempel,1 ,0, 0, 0, 0);
		$Wert = round($Werte[1][0],$line_Tag["Dezimalstellen"]);
		$Zeitpunkt = $Werte[0][0];
	}
	$query= "SELECT Tagname, Dezimalstellen, Description, EUDESC FROM Tags WHERE Tag_ID = ?;";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_bind_param($stmt, "i", $Tagliste[$i]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	
	$Tagname = html_entity_decode($line_Tag["Tagname"]);
	$Description = html_entity_decode($line_Tag["Description"]);
	if($mobil == 1) {
		echo"<tr bgcolor='#EFEAEA'><td><a href='./DH_Tagdetails.php?Tag_ID=".$Tagliste[$i]."' target = '_blank'>";
	} else {	
		echo"<tr bgcolor='#EFEAEA'><td><a href='javascript:void(0);' onclick='Tagdetails($Tagliste[$i]);'>";
	}
	echo $Tagname."</a></td><td>".$Description."</td><td width=135pt>".$Zeitpunkt."</td><td width=60pt>".$Wert."</td><td width=60pt>".$line_Tag["EUDESC"]."</td><td><a href='./Trend.php?Tag_ID=".$Tagliste[$i]."&Zeitpunkt=".$Zeitpunkt."&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Breite=600' name='Trend' target='Hauptrahmen_Gruppe'>".$Text[18]."</a></td></tr>";
}

echo "</tbody></table></span>";
// schliessen der Verbindung
mysqli_close($dbDH);
mysqli_close($db);
?>
</div></div>
<script type="text/javascript" >
function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("zeit_einstellen").style.display == "block") {
			document.getElementById("zeit_einstellen").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("zeit_einstellen").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("zeit_einstellen").style.display = "none";
		document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 2) {
		if (document.getElementById("zeit_schieben").style.display == "block") {
			document.getElementById("zeit_schieben").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("zeit_schieben").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("zeit_schieben").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 3) {
		if (document.getElementById("links").style.display == "block") {
			document.getElementById("links").style.display = "none"
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("links").style.display = "block"
			document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("links").style.display = "none";
		document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
	}
}
</script>
</body>
</html>