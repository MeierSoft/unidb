<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="../jquery-3.3.1.min.js"></script>
<script src="./db_edit.js"></script>

<?php
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include '../conf_unidb.php';
	$Text = Translate("db_edit.php");
	$Datenbank = "unidb";
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>";
	echo "</head><body class='allgemein'>";
	echo "<form action='db_edit.php' method='post' id='formular' name='Formular'>";
	if($Tabelle == "") {$Tabelle = "Baum";}
	echo "<input id='tabelle' name='Tabelle' value='".$Tabelle."' type='hidden'>";
	echo "<input id='hist_id_hidden' name='Hist_ID' value='".$Hist_ID."' type='hidden'>";
	echo "<input id='server_id' name='Server_ID' value='".$Server_ID."' type='hidden'>";
	echo "<input id='baum_id' name='Baum_ID' value='".$Baum_ID."' type='hidden'>";
	echo "<input id='geloeschte_wert' name='geloeschte' type='hidden' value='".$geloeschte."'>";
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
	$geloeschte_checked = "";
	if($geloeschte == "1") {$geloeschte_checked = " checked";}
	echo "<table border='1' cellpadding='5px'><tr><td align='center'>".$Text[1].":&nbsp;<input id='geloeschte' type='checkbox' onchange='gel_umschalten();'".$geloeschte_checked."></td></tr></table>";
	//Baumstruktur aufbauen
	if($_SESSION["Thema"] == "dark") {
		echo "<link rel='StyleSheet' href='../dtree_dark.css' type='text/css' />";
	} else {
		echo "<link rel='StyleSheet' href='../dtree.css' type='text/css' />";
	}
	echo "<script type='text/javascript' src='../dtree.js'></script>";
	echo "<div style='position:absolute; left:10px; top:60px; width:250px;'>";
	echo "<div class='dtree'>\n";
	echo "<script type='text/javascript'>\n";
	echo "d = new dTree('d');\n";
	echo "d.add('1_0','-1','<font color=\'black\'><font size=\'3\'>".$Text[2]."</font color></font size>','','','');\n";

	if($geloeschte != "1") {
		$query = "SELECT `Baum_ID`,`Server_ID`,`Bezeichnung`,`geloescht`, `Eltern_ID` FROM `Baum` WHERE `geloescht` = 0 ORDER BY `Bezeichnung` ASC;";
	} else {
		$query = "SELECT `Baum_ID`,`Server_ID`,`Bezeichnung`,`geloescht`, `Eltern_ID` FROM `Baum` ORDER BY `Bezeichnung` ASC;";
	}

	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
		$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
		if($line["geloescht"] == 1) {$Bezeichnung = "<font color=\"#909090\">".$Bezeichnung."</font>";}
		echo "d.add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','db_edit.php?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."&geloeschte=".$geloeschte."','','');\n";
	}
	echo "document.write(d);\n</script>\n</div></div>";
	mysqli_stmt_close($stmt);
	echo "<div style='position:absolute; left:270px; top:10px;'><div>";
	//Versionsliste
	$Versionsliste = "<select id='version'	name='Version' onchange='auswahl_historie(".$Baum_ID.",".$Server_ID.");' value='".$Version."'><option value='".$Text[13]."'>aktuelle Version</option>";
	$query = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Server_ID` = ".$Server_ID." AND `Baum_ID` = ".$Baum_ID." ORDER BY `Timestamp` DESC;";
	$result = mysqli_query($db, $query);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if($Version == $line["Hist_ID"]) {
			$Versionsliste = $Versionsliste."<option value='".$line["Hist_ID"]."' selected>".$line["Timestamp"]."</option>";
		} else {
			$Versionsliste = $Versionsliste."<option value='".$line["Hist_ID"]."'>".$line["Timestamp"]."</option>";
		}
	}
	$Versionsliste = $Versionsliste."</select>";

	echo "<div id='kopftabelle' style='position:absolute; top: 0px;'><table border='1' cellpadding='5px'><tr><td align='center'>".$Text[3]."</td><td align='center'>".$Text[4]."</td><td align='center'>Hist_ID</td><td align='center'>Server_ID</td><td>Baum_ID</td><td align='center'>Eltern_ID</td><td align='center'>Path</td><td align='center'>owner</td><td align='center'>".$Text[5]."</td><td align='center'>".$Text[6]."</td><td align='center'>Timestamp</td><td align='center'>".$Text[2]."</td><td align='center'>".$Text[7]."</td><td align='center'>".$Text[8]."</td></tr>";
	$query = "SELECT * FROM `".$Tabelle."` WHERE `Server_ID` = ".$Server_ID." AND `Baum_ID` = ".$Baum_ID.";";
	$result = mysqli_query($db, $query);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<tr>";
	echo "<td>".$Versionsliste."</td>";
	echo "<td>".$line["geloescht"]."</td>";
	echo "<td>".$line["Hist_ID"]."</td>";
	echo "<td>".$line["Server_ID"]."</td>";
	echo "<td>".$line["Baum_ID"]."</td>";
	echo "<td>".$line["Eltern_ID"]."</td>";
	echo "<td>".$line["Path"]."</td>";
	echo "<td>".$line["owner"]."</td>";
	echo "<td>".$line["Bezeichnung"]."</td>";
	echo "<td>".$line["Vorlage"]."</td>";
	echo "<td>".$line["Timestamp"]."</td>";
	try {
		echo "<td><select id='dynFeldliste' onchange='Feld_auswaehlen();'>";
	
		//Spaltennamen aus dem dynamischen Feld lesen
		$query="SELECT `Inhalt`, column_list(`Inhalt`) FROM `".$Tabelle."` WHERE `Baum_ID` = ".$Baum_ID." AND `Server_ID` = ".$Server_ID.";";
		$req = mysqli_query($db,$query);
		$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
		$Textinh = $line["column_list(`Inhalt`)"];
		$dynFeld = explode("`,`", $Textinh);
		for($x = 0; $x < count($dynFeld); $x++) {
			$dynFeld[$x] = str_replace("`", "", $dynFeld[$x]);
		}
		//maximale Laenge des Blob Feldes feststellen
		$query = "SELECT * FROM `".$Tabelle."` WHERE `Baum_ID` = ".$Baum_ID." AND `Server_ID` = ".$Server_ID.";";
		$req = mysqli_query($db,$query);
		$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
		$Laengenarray = mysqli_fetch_lengths($req);
		$Laenge = 0;
		for($x = 0; $x < count($Laengenarray); $x++) {
			if($Laenge < $Laengenarray[$x]) {
				$Laenge = $Laengenarray[$x];
			}
		}
		//kompletter Datensatz lesen
		//zuerst die Abfrage zusammenbasteln
		$query = "select ";
		for($i = 0; $i < count($dynFeld); $i++) {
			if($dynFeld[$i] == "") {
				$query = $query."`".$dynFeld[$i]."`, ";
			} else {
				$query = $query."column_get(`Inhalt`, '".$dynFeld[$i]."' as CHAR(".$Laenge.")) as `".$dynFeld[$i]."`, ";
			}
		}
		if($Tabelle == "Baum") {
			$query = $query."FROM `Baum` WHERE `Baum_ID` = ".$Baum_ID." AND `Server_ID` = ".$Server_ID.";";
		} else {
			$query = $query."FROM `Baumhistorie` WHERE `Hist_ID` = ".$Hist_ID.";";
		}
		$query = str_replace(", FROM", " FROM", $query);
	
		$req = mysqli_query($db,$query);
		$line = mysqli_fetch_row($req);
		for($i = 0; $i < count($dynFeld); $i++) {
			echo "<option value='".$i."'>".$dynFeld[$i]."</option>";
		}
		echo "</select></td>";
	} catch (Throwable $t) {echo "</td>";}
	if($Hist_ID == NULL) {$Hist_ID = -1;}
	echo "<td><input type='button' onclick='satz_loeschen(\"".$Tabelle."\",".$Hist_ID.",".$Baum_ID.",".$Server_ID.");' value='".$Text[9]."'></td><td><input type='button' onclick='satz_speichern(\"".$Tabelle."\",".$Hist_ID.",".$Baum_ID.",".$Server_ID.");' value='".$Text[10]."'></td></tr></table></div>";
	for($i = 0; $i < count($dynFeld); $i++) {
		echo "<input id='feld_".$i."' value='".htmlspecialchars($line[$i],ENT_HTML401)."' type='hidden'>";
		echo "<input id='feldname_".$i."' value='".$dynFeld[$i]."' type='hidden'>";
	}
	echo "<input id='anzahl_dyn_felder' value='".count($dynFeld)."' type='hidden'>";
	echo "<div id='detailbereich' style='position:absolute; top:100px;'><textarea id='dynfelddetail' style='height:400px; width: 800px;' onclick='anpassen();' onchange='uebertragen();'>".$line[0]."</textarea>";
	echo "</div><div id='feldschalter' style='position:relative; top:20px;'><input type='button' value='".$Text[11]."' onclick='dyn_Feld_einfuegen(\"".$Tabelle."\",".$Hist_ID.",".$Baum_ID.",".$Server_ID.");'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='".$Text[12]."' onclick='dyn_Feld_entfernen(\"".$Tabelle."\",".$Hist_ID.",".$Baum_ID.",".$Server_ID.");'></div>";
?>
</form>
</body>
</html>
