<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<!-- jsPanel css -->
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/sort-list.js"></script>
<style type="text/css">
#Schrift {
  font-family: Helvetica,Arial,sans-serif;
}
</style>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
//Daten lesen und aufbereiten
if ($Timestamp > ""){
	$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'von' as CHAR(1024)) as `Von`, column_get(`Inhalt`, 'bis' as CHAR(1024)) as `Bis`, column_get(`Inhalt`, 'Tags' as CHAR(1024)) as `Tags`, column_get(`Inhalt`, 'Art' as CHAR(1024)) as `Art`, column_get(`Inhalt`, 'uTime' as CHAR(1024)) as `uTime`, column_get(`Inhalt`, 'vt' as CHAR(1024)) as `vt`, column_get(`Inhalt`, 'vt_interpol' as CHAR(1024)) as `vt_interpol`, column_get(`Inhalt`, 'Verw' as CHAR(1024)) as `Verw`, column_get(`Inhalt`, 'verteilen' as CHAR(1024)) as `Verteilen`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR(1024)) as `Tags_Pfad` FROM `Baumhistorie` WHERE Baum_ID = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "iis", $Baum_ID, $Server_ID, $Timestamp);
} else {
	$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'von' as CHAR(1024)) as `Von`, column_get(`Inhalt`, 'bis' as CHAR(1024)) as `Bis`, column_get(`Inhalt`, 'Tags' as CHAR(1024)) as `Tags`, column_get(`Inhalt`, 'Art' as CHAR(1024)) as `Art`, column_get(`Inhalt`, 'uTime' as CHAR(1024)) as `uTime`, column_get(`Inhalt`, 'vt' as CHAR(1024)) as `vt`, column_get(`Inhalt`, 'vt_interpol' as CHAR(1024)) as `vt_interpol`, column_get(`Inhalt`, 'Verw' as CHAR(1024)) as `Verw`, column_get(`Inhalt`, 'verteilen' as CHAR(1024)) as `Verteilen`, column_get(`Inhalt`, 'Tags_Pfad' as CHAR(1024)) as `Tags_Pfad` FROM Baum WHERE Baum_ID= ? AND `Server_ID` = ?;";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line_Gruppe = mysqli_fetch_array($result, MYSQLI_ASSOC);

$Tagliste = explode(",", $line_Gruppe["Tags"]);
$Artliste = explode(",", $line_Gruppe["Art"]);
$Verwliste = explode(",", $line_Gruppe["Verw"]);
$uTimeliste = explode(",", $line_Gruppe["uTime"]);
$vtliste = explode(",", $line_Gruppe["vt"]);
$vt_interpolliste = explode(",", $line_Gruppe["vt_interpol"]);
$Verteilen = "";
if($line_Gruppe["Verteilen"] == 1) {$Verteilen = " checked";}
$Bezeichnung = html_entity_decode($line_Gruppe["Bezeichnung"]);
mysqli_stmt_close($stmt);
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("Export.php");
echo "<title>".$Bezeichnung."</title>";
echo "</head>";
echo "<body class='allgemein'>";
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[15];
	exit;
}
include './mobil.php';
include './conf_DH.php';

if($Aktion == "löschen") {
	$query = "DELETE FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? AND `Timestamp` = ?;";
	uKol_schreiben(1,$query, "iis", [$Baum_ID, $Server_ID, $Timestamp]);
	$Timestamp = "";
}
	
//Menue
echo "<form id='phpform' name='phpform' action='Export.php' method='post' target='_self'>";
$Ausgabe = "<table><tr>";
if($anzeigen == 1) {
	if ($Timestamp == null) {
		if ($line["geloescht"] != 1) {
			$Ausgabe = $Ausgabe."<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[26]."</a></td>";
			$Ausgabe = $Ausgabe."<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[27]."</a></td>";
			$Ausgabe = $Ausgabe."<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[25]."</a></td>";
			if($line["geloescht"] == 1) {$Ausgabe = $Ausgabe."<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";}
		} else {
			$Ausgabe = $Ausgabe."<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";
		}
	} else {
		$Ausgabe = $Ausgabe."<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>Aktuelle Version durch diese hier ersetzen.</a></td>";
		$Ausgabe = $Ausgabe."<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>Diese Version löschen.</a></td>";
	}
}
if($anzeigen == 1) {
	if($line["geloescht"] != 1) {
		$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		$erweitern = 0;
		if(mysqli_num_rows($result1) > 0) {
			$erweitern = 1;
			$Ausgabe = $Ausgabe."<td>Version</td><td><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
			while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				if($line1["Timestamp"] == $Timestamp) {
					$Ausgabe = $Ausgabe."<option value='".$line1["Timestamp"]."' selected>".$line1["Timestamp"]."</option>";
				} else {
					$Ausgabe = $Ausgabe."<option value='".$line1["Timestamp"]."'>".$line1["Timestamp"]."</option>";
				}
			}
			$Ausgabe = $Ausgabe."</select></td>";
		}
		mysqli_stmt_close($stmt1);
	}
}
$Ausgabe = "</tr></table>".$Ausgabe;
echo $Ausgabe;
echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
echo "<input type='hidden' id='gel' name='geloescht' value = '".$geloescht."'>\n";
echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
echo "<table cellpadding='3px' style='background: #d6d6d6;'>\n";
echo "</form><br><br>";

echo "<form action='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='Baum' name='Exportformular'>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input id='inhalt_tagsuche_dialog' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
echo "<input type='hidden' id='tagliste' name='Tagliste' value = '".$line_Gruppe["Tags"]."'>";
echo "<input type='hidden' id='artliste' name='Artliste' value = '".$line_Gruppe["Art"]."'>";
echo "<input type='hidden' id='verwliste' name='Verwliste' value = '".$line_Gruppe["Verw"]."'>";
echo "<input type='hidden' id='uTimeliste' name='uTimeliste' value = '".$line_Gruppe["uTime"]."'>";
echo "<input type='hidden' id='vtliste' name='vtliste' value = '".$line_Gruppe["vt"]."'>";
echo "<input type='hidden' id='vt_interpolliste' name='vt_interpolliste' value = '".$line_Gruppe["vt_interpol"]."'>";
echo "<table id='tagtabelle'><tr><td align='right'>".$Text[1]."</td><td colspan='3'><input class='Text_Element' value='".$Bezeichnung."' type='text' id='bezeichnung' name='Bezeichnung' size='60'></td></tr>";
echo "<tr><td>".$Text[2]."</td><td colspan='3'><input class='Text_Element' value='".$line_Gruppe["Tags_Pfad"]."' type='text' name='Tags_Pfad' size='60'></td></tr></table>";
//Zeitraum einstellen
echo "<br><br><table id='Schrift'; style='font-size: 14px';>";
echo "<tr style='font-weight:bold'><td></td><td align='center'>".$Text[20]."</td><td align='center'>".$Text[21]."</td></tr>";
echo "<tr style='font-weight:bold'><td>".$Text[19]."</td><td align='center' style='width: 140px;'><input style='width: 120px;' class='Text_Element' value='".$line_Gruppe["Von"]."' type='text' id='von' name='Von'></td><td align='center' style='width: 140px;'><input style='width: 120px;' class='Text_Element' value='".$line_Gruppe["Bis"]."' type='text' id='bis' name='Bis'></td></tr><tr style='height: 30px;'></tr></table></div>";
echo "<input id='inhalt_tagsuche_dialog1' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>";
echo "<br><br><table id='Schrift'; style='width: 720px;' font-size: 14px';>";
echo "<tr style='font-weight:bold'><td width='120px'>".$Text[6]."</td><td width='200px'>".$Text[7]."</td><td align='center' width='100px'>".$Text[18]."</td><td width='70px' align='center'>unixTime</td><td width='30px' align='center'>vt</td><td width='80px' align='center'>vt interpol</td><td width='90px' align='center'>".$Text[22]."</td></tr></table>";
echo "<span id='Schrift'; style='font-weight:normal; font-size: 14px'>";
echo "<ul id='sortlist'>";
$x = 0;
for($i = 0;$i < count($Tagliste); $i++) {
	if(strlen($Tagliste[$i]) > 0) {
		$query = "SELECT `Tagname`, `Dezimalstellen`, `Description`, `EUDESC`, `Mittelwerte` FROM `Tags` WHERE `Tag_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Tagliste[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Tagname = html_entity_decode($line_Tag["Tagname"]);
		$Description = html_entity_decode($line_Tag["Description"]);
		echo "<li><table><tr class='tagtabellenzeile' bgcolor='#E5E5E5'><td id='tag_id_".$i."' style='display: none;'>".$Tagliste[$i]."</td><td width='120px'>".$Tagname."</td><td width='200px'>".$Description."</td>";
		if($line_Tag["Mittelwerte"] == 0) {
			echo "<td width='100px' align='center'>".$Text[17]."</td>";
		} else {
			echo "<td width='100px' align='center'><select id='art_".$i."' value='".$Artliste[$i]."' size='1' onchange='Art_waehlen(".$i.");'><option selected>".$Text[17]."</option>";
			if ($Artliste[$i] == "hMinMax"){
					echo "<option selected>hMinMax</option>";
			} else {
				echo "<option>hMinMax</option>";
			}
			if ($Artliste[$i] == "dMinMax"){
				echo "<option selected>dMinMax</option>";
			} else {
				echo "<option>dMinMax</option>";
			}
			if ($Artliste[$i] == "hMW"){
				echo "<option selected>hMW</option>";
			} else {
				echo "<option>hMW</option>";
			}
			if ($Artliste[$i] == "hMin"){
				echo "<option selected>hMin</option>";
			} else {
				echo "<option>hMin</option>";
			}
			if ($Artliste[$i] == "hMax"){
				echo "<option selected>hMax</option>";
			} else {
				echo "<option>hMax</option>";
			}
			if ($Artliste[$i] == "dMW"){
				echo "<option selected>dMW</option>";
			} else {
				echo "<option>dMW</option>";
			}
			if ($Artliste[$i] == "dMin"){
				echo "<option selected>dMin</option>";
			} else {
				echo "<option>dMin</option>";
			}
			if ($Artliste[$i] == "dMax"){
				echo "<option selected>dMax</option>";
			} else {
				echo "<option>dMax</option>";
			}
			echo "</select><div style='display: none;' id='art_text_".$i."'>".$Text[17]."</div></td>";
		}
		if($Verwliste[$i] == 1) {
			$checkverw = " checked";
		} else {
			$checkverw = "";
		}
		if($uTimeliste[$i] == 1) {
			$checkuTime = " checked";
		} else {
			$checkuTime = "";
		}
		if($vtliste[$i] == 1) {
			$checkvt = " checked";
		} else {
			$checkvt = "";
		}
		if($vt_interpolliste[$i] == 1) {
			$checkvt_interpol = " checked";
		} else {
			$checkvt_interpol = "";
		}
		echo '<td width="70px" align="center"><input class="Text_Element" id="uTime_'.$i.'" type="checkbox"'.$checkuTime.' value="'.$uTimeliste[$i].'" onchange="checken(\''.$i.'\',\'uTime_\');"></td>';
		if($line_Tag["Mittelwerte"] == 0) {
			echo "<td width='30px'></td>";
			echo "<td width='80px'></td>";
		} else {	
			echo '<td width="30px" align="center"><input class="Text_Element" id="vt_'.$i.'" type="checkbox"'.$checkvt.' value="'.$vtliste[$i].'" onchange="checken(\''.$i.'\',\'vt_\');"></td>';
			echo '<td width="80px" align="center"><input class="Text_Element" id="vt_interpol_'.$i.'" type="checkbox"'.$checkvt_interpol.' value="'.$vt_interpolliste[$i].'" onchange="checken(\''.$i.'\',\'vt_interpol_\');"></td>';
		}
		echo '<td width="90px" align="center"><input class="Text_Element" id="verw_'.$i.'" type="checkbox"'.$checkverw.' value="'.$Verwliste[$i].'" onchange="checken(\''.$i.'\',\'verw_\');"></td>';
		echo "<td width = 70px align='center'><input class='Schalter_Element' type='button' name='Tag_suchen_Schalter' value='".$Text[10]."' onclick='zeile_entf(".$i.")'></td>";
		echo "</tr></table></li>";
		$x++;
	}
}
echo "</ul><br>";
echo "<span id='Schrift'; style='font-weight:normal; font-size: 14px'>";
echo "<table><tr><td>".$Text[11]."</td><td colspan='4'><input class='Text_Element' id='Tag' name='Tag' type='text' size='60' maxlength='255'></td></tr>";
echo "<tr style='line-height: 3';><td>";
echo "</td><td><input class='Schalter_Element' value='".$Text[12]."' type='button' onclick='hinzufuegen();'></td><td align='center'><input class='Schalter_Element' type='button' name='Tag_suchen_Schalter' value='".$Text[13]."' onclick='Eingabefeld_sichtbar(1)'></td>";
echo "<input id='inhalt_tagsuche_dialog2' name='Inhalt_Tagsuche' type='hidden' value='".$Inhalt_Tagsuche."'>";
echo "<td align='right'><input class='Schalter_Element' name='Pfade_editieren' value='".$Text[14]."' type='button' onclick='Pfade_Dialog_oeffnen();'></td></tr></table>";
echo "</span>";
mysqli_close($db);
mysqli_close($dbDH);
?>
<br><br><br>
<table><tr style="font-size: 18px;"><td style="width: 180px;" align="right"><?php echo $Text[23]; ?>:</td><td width='10px'></td><td width='40px'><a id="link" href="javascript:void(0);" onclick="csv_exportieren();">csv</a></td><td width='50px'><a id="link" href="javascript:void(0);" onclick="html_exportieren();">html</a></td></tr></table>
<br><br><table><tr style="height: 90px;"><td>
<?php echo "</td><td><input class='Schalter_Element' value='".$Text[4]."' type='submit' onclick='speichern();') name='Aktion'></td><td style='width: 20px;'></td><td><input class='Schalter_Element' id=\"Hilfe\" value='".$Text[5]."' type='button' name='Hilfe' onclick=\"Hilfe_Fenster('45');\">";?>
</td></tr></table>
</form>

<form style="display: none;" name="ex" method="post" action="./Tabelle_exp_csv.php" target="_blank">
<input type="text" name="exStart">
<input type="text" name="exEnde">
<input type="text" name="exTitel">
<input type="text" name="exParameterfeld">
<input type="text" name="exverteilen">
</form>
<script type="text/javascript">
jsPanel.defaults.resizeit.minWidth = 30;
jsPanel.defaults.resizeit.minHeight = 52;
var T_Text = new Array;

let tagsuche_geschlossen = function(event) {
	document.getElementById("inhalt_tagsuche_dialog").value = "";
	document.getElementById("inhalt_tagsuche_dialog1").value = "";
	document.getElementById("inhalt_tagsuche_dialog2").value = "";
}
document.addEventListener('jspanelclosed', tagsuche_geschlossen, false);

window.addEventListener("DOMContentLoaded", () => {
	slist(document.getElementById("sortlist"));
});

$(window).on('load',function() {;
	T_Text = JSON.parse(document.getElementById("translation").value);
	if (document.getElementById("inhalt_tagsuche_dialog").value + document.getElementById("inhalt_tagsuche_dialog1").value + document.getElementById("inhalt_tagsuche_dialog2").value > "") {Eingabefeld_sichtbar(0);}
		
});

function Art_waehlen(Tag) {
	Selectfeld = document.getElementById("art_" + Tag.toString());
	for (z = 0; z < Selectfeld.childElementCount; z++) {
		if (Selectfeld.childNodes[z].selected == true) {Selectfeld.value = Selectfeld.childNodes[z].innerHTML;}
	}
}

function speichern() {
	Tabelle = document.getElementsByClassName('tagtabellenzeile');
	TListe = "";
	AListe = "";
	VListe = "";
	uListe = "";
	vtListe = "";
	vtinteriste = "";
	for (z = 0; z < Tabelle.length; z++) {
		TListe = TListe + Tabelle[z].firstChild.innerHTML + ",";
		AListe = AListe + Tabelle[z].childNodes[3].firstChild.value + ",";
		uListe = uListe + Tabelle[z].childNodes[4].firstChild.value + ",";
		try {
			vtListe = vtListe + Tabelle[z].childNodes[5].firstChild.value + ",";
			vtinteriste = vtinteriste + Tabelle[z].childNodes[6].firstChild.value + ",";	
		} catch (err) {
			vtListe = vtListe + "0,";
			vtinteriste = vtinteriste + "0,";
		}
		VListe = VListe + Tabelle[z].childNodes[7].firstChild.value + ",";
	}
	TListe = TListe.substr(0,TListe.length - 1);
	document.getElementById("tagliste").value = TListe;
	AListe = AListe.substr(0,AListe.length - 1);
	document.getElementById("artliste").value = AListe;
	VListe = VListe.substr(0,VListe.length - 1);
	document.getElementById("verwliste").value = VListe;
	uListe = uListe.substr(0,uListe.length - 1);
	document.getElementById("uTimeliste").value = uListe;
	vtListe = vtListe.substr(0,vtListe.length - 1);
	document.getElementById("vtliste").value = vtListe;
	vtinteriste = vtinteriste.substr(0,vtinteriste.length - 1);
	document.getElementById("vt_interpolliste").value = vtinteriste;
}

function checken(Zeile, Feld) {
	Kaestchen = document.getElementById(Feld + Zeile);
	if (Kaestchen.checked == false) {
		Kaestchen.value = 0;
	} else {
		Kaestchen.value = 1;
	}
}

function zeile_entf(Zeile) {
	Tabelle = document.getElementById("sortlist");
	Tabelle.removeChild(Tabelle.childNodes[Zeile]);
	tab_sortieren();
}

function tab_sortieren() {
	Tabelle = document.getElementById("sortlist");
	for (z = 0; z < Tabelle.childNodes.length; z++) {
		Tabelle.childNodes[z].firstChild.firstChild.firstChild.firstChild.id = "tag_id_" + z.toString();
		Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[3].firstChild.id = "art_" + z.toString();
		Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[4].firstChild.id = "verw_" + z.toString();
		Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[4].firstChild.setAttribute("onchange", "checken('" + z + "');");
		Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[5].firstChild.setAttribute("onclick", "zeile_entf(" + z + ");");
	}
}

function hinzufuegen() {
	Tabelle = document.getElementById("sortlist");
	Eingabe = document.getElementById("Tag").value;
	if(Eingabe.length > 0) {
		Pfad = "%";
		if(Eingabe.substr(0,1) == "/") {
			pos = Eingabe.indexOf("/",0);
			Pfad = Eingabe.substr(0, pos + 1);
			Eingabe2 = Eingabe.substr(pos + 1, Eingabe.length);
		}
		jQuery.ajax({
			url: "DH_Tag_Tagliste.php?Tagname=" + Eingabe,
			success: function (html) {
   			strReturn = html;
			},
	  		async: false
  		});
  		Eigenschaften = JSON.parse(strReturn)[0];
  		Zeile = document.createElement("li");
  		Zeile.setAttribute("draggable", "true");
  		Inhalt = '<table><tbody><tr class="tagtabellenzeile" bgcolor="#E5E5E5"><td id="tag_id_' + Tabelle.childNodes.length + '" style="display: none;">' + Eigenschaften.Tag_ID + '</td>';
  		Inhalt = Inhalt + '<td width="120px">' + Eigenschaften.Tagname + '</td><td width="200px">' + Eigenschaften.Description + '</td><td width="100px" align="center">';
  		Inhalt = Inhalt + '<select id="art_' + Tabelle.childNodes.length + '" value="' + T_Text[17] + '" size="1" onchange="Art_waehlen(' + Tabelle.childNodes.length + ');"><option selected>' + T_Text[17] + '</option><option>hMinMax</option><option>dMinMax</option><option>hMW</option><option>hMin</option><option>hMax</option><option>dMW</option><option>dMin</option><option>dMax</option></select><div style="display: none;" id="art_text_' + Tabelle.childNodes.length + '">' + T_Text[17] + '</div></td>';
		Inhalt = Inhalt + '<td width="70px" align="center"><input class="Text_Element" id="uTime_' + Tabelle.childNodes.length + '" type="checkbox" value="0" onchange="checken("' + Tabelle.childNodes.length + '","uTime_");"></td>';
		Inhalt = Inhalt + '<td width="30px" align="center"><input class="Text_Element" id="vt_' + Tabelle.childNodes.length + '" type="checkbox" value="0" onchange="checken("' + Tabelle.childNodes.length + '","vt_");"></td>';
		Inhalt = Inhalt + '<td width="80px" align="center"><input class="Text_Element" id="vt_interpol_' + Tabelle.childNodes.length + '" type="checkbox" value="0" onchange="checken("' + Tabelle.childNodes.length + '","vt_interpol_");"></td>';
  		Inhalt = Inhalt + '<td width="90px" align="center"><input class="Text_Element" id="verw_' + Tabelle.childNodes.length + '" type="checkbox" checked="" value="1" onchange="checken("' + Tabelle.childNodes.length + '","verw_");"></td><td width="70px" align="center"><input class="Schalter_Element" type="button" name="Tag_suchen_Schalter" value="entfernen" onclick="zeile_entf(' + Tabelle.childNodes.length + ')"></td></tr></tbody></table>';
  		Zeile.innerHTML = Inhalt;
  		Tabelle.appendChild(Zeile);
  		//Den EventListener aktualisieren.
  		slist(document.getElementById("sortlist"))
	}
}

function uebertragen() {
	var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
	document.forms.Exportformular.Tag.value = Ergebnis[0] + Ergebnis[1];
	jsPanel.activePanels.getPanel("Dialog").close();
}

function Eingabefeld_sichtbar(neu) {
	if (neu == 0) {
		if (document.getElementById("inhalt_tagsuche_dialog").value > "") {
			var strReturn = document.getElementById("inhalt_tagsuche_dialog").value;
		} else {
			if (document.getElementById("inhalt_tagsuche_dialog1").value > "") {
				var strReturn = document.getElementById("inhalt_tagsuche_dialog1").value;
			} else {
				if (document.getElementById("inhalt_tagsuche_dialog2").value > "") {
					var strReturn = document.getElementById("inhalt_tagsuche_dialog2").value;
				}
			}
		}
	} else {
		var strReturn = "";
		jQuery.ajax({
			url: "Test_Tag_suchen.php?Suchtext=" + document.forms.Exportformular.Tag.value,
			success: function (html) {
   			strReturn = html;
			},
			async: false
		});
	}
	try {Tagsuche.close();} catch (err) {}
	var panel = jsPanel.create({
		id: 'Tagsuche',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '500 275',
		content: strReturn,
  		contentOverflow: 'hidden',
  		callback: function (panel) {
  			$(this).on('mouseleave', function () {
				var Pos = jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).content[0].children[0].Ergebnis.value.search(" - ");
				var Tagname = jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).content[0].children[0].Ergebnis.value.substring(0, Pos);  
				document.forms.Exportformular.Tag.value=Tagname;
				//panel.close();
			});
	  	}
	});
	document.getElementById("inhalt_tagsuche_dialog").value = document.getElementById("Tagsuche").content.innerHTML;
	document.getElementById("inhalt_tagsuche_dialog1").value = document.getElementById("Tagsuche").content.innerHTML;
	document.getElementById("inhalt_tagsuche_dialog2").value = document.getElementById("Tagsuche").content.innerHTML;
}

function Pfade_Dialog_oeffnen(){
	try {PfadeDialog.close();} catch (err) {}
	jQuery.ajax({
		url: "DH_Tagliste_ermitteln.php?Tagliste=" + document.forms.Exportformular.Tagliste.value + "&Pfad=" + document.forms.Exportformular.Tags_Pfad.value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'PfadeDialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '600 275',
		content: strReturn,
	});
}

function Pfadaenderung_uebernehmen() {
	i = 0;
	Feld = document.Dialog_Tagaustausch[0];
	try{	
		while (Feld.type != "button"){
			if (Feld.checked == true) {
				Tag_alt_neu = Feld.value.split(",");
				document.forms.Exportformularn.Tagliste1.value = document.forms.Exportformularn.Tagliste1.value.replace(Tag_alt_neu[0], Tag_alt_neu[1]);
			}
			i = i + 1;
			Feld = document.Dialog_Tagaustausch[i];
		}
	} catch (err) {}
	try {PfadeDialog.close();} catch (err) {}
}

function export_allgemein() {
	document.forms.ex.exParameterfeld.value = Parameter_festlegen();
	document.forms.ex.exStart.value = document.getElementById("von").value;
	document.forms.ex.exEnde.value = document.getElementById("bis").value;
	document.forms.ex.exTitel.value = document.getElementById("bezeichnung").value;
	document.forms.ex.submit();
}
function csv_exportieren() {
	document.forms.ex.action = "./Tabelle_exp_csv.php";
	export_allgemein();
}

function html_exportieren() {
	document.forms.ex.action = "./Tabelle_exp_html.php";
	export_allgemein();
}
	
function Parameter_festlegen() {
	var Parameterfeld = [];
	Tabelle = document.getElementById("sortlist");
	i = 0;
	for (z = 0; z < Tabelle.childNodes.length; z++) {
		if (document.getElementById("verw_" + z.toString()).checked == true) {
			vuTime = 0;
			try {
				if (document.getElementById("uTime_" + z.toString()).checked == true) {vuTime = 1;}
				vvt = 0;
				if (document.getElementById("vt_" + z.toString()).checked == true) {vvt = 1;}
				vvt_interpol = 0;
				if (document.getElementById("vt_interpol_" + z.toString()).checked == true) {vvt_interpol = 1;}
			} catch (err) {
				vvt = 0;
				vvt_interpol = 0;
			}
			if (Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[3].firstChild.value != undefined) {
				Arttext = Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[3].firstChild.value;
			} else {
				Arttext = Tabelle.childNodes[z].firstChild.firstChild.firstChild.childNodes[3].firstChild.data;
			}
			Parameterfeld[i] = {
				Tag_ID: document.getElementById("tag_id_" + z.toString()).innerHTML,
				Art: Arttext,
				uTime: vuTime,
				vt: vvt,
				vt_interpol: vvt_interpol
			}
			i++;
		}
	}
	return JSON.stringify(Parameterfeld);
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
</table>
</body>
</html>