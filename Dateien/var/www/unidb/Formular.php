<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">
<script src="./scripts/jquery-3.6.0.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./colresizable/colResizable-1.6.min.js"></script>
<script src="./Formular.js"></script>
<link rel='StyleSheet' href='dtree.css' type='text/css' />
<script type='text/javascript' src='dtree.js'></script>
<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css">
<script src="./scripts/jquery-ui.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<script type="text/javascript" src="./scripts/jquery.js"></script>
<script src="./tinymce/tinymce.min.js"></script>
<script src="./tinymce/langs/de.js"></script>
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$_SESSION['V_Daten'] = NULL;
include("Formular_func.inc.php");
$Text = Translate("Formular.php");
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[38];
	exit;
}
$neueste_ID = 0;
if($Navigation_Satz < 1) {$Navigation_Satz = 1;}
//Formular einlesen
$query = "SELECT `Server_ID`, `Baum_ID`, `Eltern_ID`, `Bezeichnung`, `Vorlage`, column_get(`Inhalt`, 'Darstellung' as CHAR) as `Darstellung`, column_get(`Inhalt`, 'Datenbank' as CHAR) as `Datenbank`, column_get(`Inhalt`, 'Datenquelle' as CHAR) as `Datenquelle`, column_get(`Inhalt`, 'JS' as CHAR) as `JS`, column_get(`Inhalt`, 'Tabellenzeilen' as CHAR) as `Tabellenzeilen`, column_get(`Inhalt`, 'Headererweiterung' as CHAR) as `Headererweiterung`, column_get(`Inhalt`, 'Hintergrundfarbe' as CHAR) as `Hintergrundfarbe`, column_get(`Inhalt`, 'Navigationsbereich' as CHAR) as `Navigationsbereich`, column_get(`Inhalt`, 'Bei_Start' as CHAR) as `Bei_Start`, column_get(`Inhalt`, 'DB_Bereich' AS CHAR) AS `DB_Bereich`, column_get(`Inhalt`, 'bed_Format' as CHAR) as `bed_Format`, column_get(`Inhalt`, 'current' as CHAR) as `current`, column_get(`Inhalt`, 'Replikation' as CHAR) as `Replikation` FROM `Baum` WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$query);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_stmt_close($stmt);

if(strlen($Darstellung) == 0) {$Darstellung = $line["Darstellung"];}
$JS = html_entity_decode($line["JS"]);
$current = html_entity_decode($line["current"]);
$Headererweiterung = html_entity_decode($line["Headererweiterung"]);
$Replikation = $line["Replikation"];
$Hintergrundfarbe = $line["Hintergrundfarbe"];
$Navigationsbereich = $line["Navigationsbereich"];
$bed_Format = html_entity_decode($line["bed_Format"]);
$Bei_Start = html_entity_decode($line["Bei_Start"]);
$Bei_Start = str_replace(" class=\"\"", "", $Bei_Start);
if($UForm == null) {
	$Datenquelle = html_entity_decode($line["Datenquelle"]);
} else {
	$Datenquelle = str_replace("'", '"', $Datenquelle);
	if($Datenquelle == null) {$Datenquelle = html_entity_decode($line["Datenquelle"]);}
}
$Datenquelle = str_replace(" AND  ORDER", " ORDER", $Datenquelle);
$Datenquelle = str_replace(" AND ORDER", " ORDER", $Datenquelle);
$Datenquelle = str_replace(" AND  GROUP", " GROUP", $Datenquelle);
$Datenquelle = str_replace(" AND GROUP", " GROUP", $Datenquelle);
$Datenquelle = str_replace(" AND  LIMIT", " LIMIT", $Datenquelle);
$Datenquelle = str_replace(" AND LIMIT", " LIMIT", $Datenquelle);
$Datenquelle = str_replace(" AND  order", " order", $Datenquelle);
$Datenquelle = str_replace(" AND order", " order", $Datenquelle);
$Datenquelle = str_replace(" AND  group", " group", $Datenquelle);
$Datenquelle = str_replace(" AND group", " group", $Datenquelle);
$Datenquelle = str_replace(" AND  limit", " limit", $Datenquelle);
$Datenquelle = str_replace(" AND limit", " limit", $Datenquelle);
$Datenquelle1 = $Datenquelle;
$DB_Bereich = html_entity_decode($line["DB_Bereich"]);
if(strlen($Datenquelle) == 0 and $Navigationsbereich == 1) {header("location: ./Formular_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID);}
$Tabellenzeilen = $line["Tabellenzeilen"];
if(strlen($Tabellenzeilen) == 0) {$Tabellenzeilen = 15;}

echo $Headererweiterung;
echo "\n<title>".html_entity_decode($line["Bezeichnung"])."</title>\n";
echo "\n</head>\n";
if(strlen($Hintergrundfarbe) > 0) {
	echo "<body class='allgemein' style='background-color: ".$Hintergrundfarbe.";'>\n";
} else {
	echo "<body class='allgemein'>\n";
}
echo '<style type="text/css">.Unterstrich {border-bottom-style: dotted; border-bottom-color: #000000;	border-bottom-width: 1px;}</style>';
include 'mobil.php';

if($Navigationsbereich == 1) {
	if($Form_Filter_laden > "") {
		$query = "SELECT `Filtertext` FROM `Userdef_Filter` WHERE `Baum_ID` = ? AND `User_ID` = ? AND `Filtername` = ? AND `Server_ID` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "iisi", $Baum_ID, $_SESSION["User_ID"], $Form_Filter_laden, $Server_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while($linedb = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$Filtertext = str_replace("'", '"', $linedb["Filtertext"]);
		}
		mysqli_free_result($result);
		mysqli_stmt_close($stmt);
		if(strpos(strtolower($Datenquelle), "where") > 0) {
			$start = strpos(strtolower($Datenquelle), "where") + 6;
			$Filtertext = "(".$Filtertext.") AND ";
		} else {
			if(strpos(strtolower($Datenquelle), "group") > 0) {
				$start = strpos(strtolower($Datenquelle), "group");
			} else {
				if(strpos(strtolower($Datenquelle), "order") > 0) {
					$start = strpos(strtolower($Datenquelle), "order");
				} else {
					if(strpos(strtolower($Datenquelle), "limit") > 0) {
						$start = strpos(strtolower($Datenquelle), "limit");
					} else {
						$start = strlen($Datenquelle);
						if(substr($Datenquelle,-1) == ";") {$Datenquelle = substr($Datenquelle,0, -1)." ";}
					}
				}
			}
			$Filtertext = "WHERE (".$Filtertext.") ";
		}
		$Datenquelle = substr($Datenquelle, 0, $start).$Filtertext.substr($Datenquelle, $start);
		if(substr($Datenquelle,-1) != ";") {$Datenquelle = $Datenquelle.";";}
	}
	if($Sortierungen > "") {
		if(strpos(strtolower($Datenquelle), "order by") > 0) {
			$start = strpos(strtolower($Datenquelle), "order by");
			$Rest = substr($Datenquelle, $start);
			$Ende = strpos(strtolower($Rest), "limit");
			if($Ende == 0) {$Ende = strpos($Rest, ";", $start);}
			if($Ende == 0) {
				$Datenquelle = substr($Datenquelle, 0, $start).$Sortierungen.";";
			} else {
				$Datenquelle = substr($Datenquelle, 0, $start).$Sortierungen.substr($Rest, $Ende);
			}
		} else {
			$Ende = strpos(strtolower($Datenquelle), "limit");
			if($Ende == 0) {$Ende = strpos(strtolower($Rest), ";");}
			if($Ende == 0) {
				if(substr($Datenquelle, -1) == ";") {$Datenquelle = substr($Datenquelle, 0, strlen($Datenquelle) - 1);}
				$Datenquelle = $Datenquelle." ".$Sortierungen.";";
			} else {
				$Datenquelle = substr($Datenquelle, 0, $Ende).$Sortierungen." ".substr($Datenquelle, $Ende);
			}
		}
	}
	$_SESSION['DB_Server'] = $sqlhostname;
	$_SESSION['Form_Datenquelle'] = $Datenquelle;
	$_SESSION['Datenbank'] = $line["Datenbank"];
	$Datenbank = $line["Datenbank"];
	$_SESSION['Form_DB'] = $Datenbank;
	//Verbindung zur Datenbank herstellen
	$sqlhostname = "localhost";
	$db_Satz = mysqli_connect($sqlhostname,$_SESSION['DB_User'],$_SESSION['DB_pwd'],$Datenbank);
	mysqli_query($db_Satz, 'set character set utf8;');
	//Datensatz loeschen
	if($Aktion == "loeschen") {
		$query = "DELETE FROM `".$Tabellenname."` WHERE `".$Indexfeld."` = ".$Indexwert.";";
		if($Replikation == 1) {
			if(FKol_schreiben($query,$Datenbank) == 1) {
				echo "Die Verbindung zu einem der Server ist unterbrochen, daher können momentan keine Daten hinzugefügt, gelöscht oder geändert werden.";
				exit;
			}
		} else {
			$stmt = mysqli_prepare($db_Satz,$query);
			mysqli_stmt_execute($stmt);
		}
		$Aktion = "";
	}
	//neuer Datensatz
	if($Aktion == "neuer Datensatz") {
		if($UForm == 1) {
			$query = "INSERT INTO `".$Tabellenname."` (`".$Indexfeld."`, `".$verkn_feld."`) VALUES (NULL, '".$verkn_feld_Wert."');";
		} else {
			$query = "INSERT INTO `".$Tabellenname."` (`".$Indexfeld."`) VALUES (NULL);";
		}
		if($Replikation == 1) {
			if(FKol_schreiben($query,$Datenbank) == 1) {
				echo "Die Verbindung zu einem der Server ist unterbrochen, daher können momentan keine Daten hinzugefügt, gelöscht oder geändert werden.";
				exit;
			}
		} else {
			$stmt = mysqli_prepare($db_Satz,$query);
			mysqli_stmt_execute($stmt);
		}
		$query = "SELECT LAST_INSERT_ID();";
		$stmt = mysqli_prepare($db_Satz,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_row($result);
		mysqli_free_result($result);
		$neueste_ID = $row[0];
		$Darstellung = "Formular";
		$Aktion = "";
	}

	//Primaerindex ermitteln
	if(strlen($Indexfeld) < 1) {
		$DQ = strtolower($Datenquelle);
		$DQ = substr($DQ, strpos($DQ, "from") + 4);
		$Tabellenname = "";
		$Zeichen = substr($DQ,0,1);
		while($Zeichen == "`" or $Zeichen == "," or $Zeichen == " ") {
			$DQ = substr($DQ, 1);
			$Zeichen = substr($DQ,0,1);
		}
		while($Zeichen != "`" and $Zeichen != "," and $Zeichen != " ") {
			$Tabellenname = $Tabellenname.$Zeichen;
			$DQ = substr($DQ, 1);
			$Zeichen = substr($DQ,0,1);
		}
		$Tabellenname = substr($Datenquelle, strpos(strtolower($Datenquelle), $Tabellenname), strlen($Tabellenname));
		$query = "SHOW INDEX FROM `".$Tabellenname."` WHERE Key_name = 'PRIMARY';";
		$stmt = mysqli_prepare($db_Satz,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_row($result);
		mysqli_free_result($result);
		$Indexfeld = $row[4];
	}
}
if($Navigationsbereich == 1) {
	//Anzahl der Datensaetze feststellen
	$stmt1 = mysqli_prepare($db_Satz,$Datenquelle);
	mysqli_stmt_execute($stmt1);
	$result = mysqli_stmt_get_result($stmt1);
	$Saetze = mysqli_num_rows($result);
	mysqli_free_result($result);
	if($Navigation_Satz == "") {$Navigation_Satz = 1;}
	if($Saetze < $Navigation_Satz) {$Navigation_Satz = $Saetze;}
	mysqli_stmt_close($stmt1);
	//Datensatz einlesen
	if(substr($Datenquelle,-1,1) == ";") {
		$query = substr($Datenquelle,0,strlen($Datenquelle)-1);
	} else {
		$query = $Datenquelle;
	}
	$query = $query." LIMIT ".strval(intval($Navigation_Satz) - 1).",1;";
	$stmt1 = mysqli_prepare($db_Satz,$query);
	mysqli_stmt_execute($stmt1);
	$result = mysqli_stmt_get_result($stmt1);
	$line_Satz = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	mysqli_stmt_close($stmt1);
}

echo "<form id='Einstellungen' name='Einstellungen' action='./Formular.php' method='post' target='_self'>\n";
echo "<input id='neueste_ID' name='neueste_ID' type='hidden' value='".$neueste_ID."'>\n";
echo "<input id='verkn_feld' name='verkn_feld' type='hidden' value='".$verkn_feld."'>\n";
echo "<input id='verkn_feld_Wert' name='verkn_feld_Wert' type='hidden' value='".$verkn_feld_Wert."'>\n";
echo "<input id='benutzer' name='Benutzer' type='hidden' value='".$_SESSION['User_ID']."'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
echo "<input id='datenquelle' name='Datenquelle' type='hidden' value='".$Datenquelle1."'>\n";
echo "<input id='datenquelle_akt' name='Datenquelle_akt' type='hidden' value='".$Datenquelle."'>\n";
echo "<input id='replikation' name='Replikation' type='hidden' value='".$Replikation."'>\n";
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
echo "<input id='datenbank' name='Datenbank' type='hidden' value='".$Datenbank."'>\n";
//echo "<table style='background: #FCEDD9; height: 30px;'><tr>\n";

if($Navigationsbereich == 1) {
	$query = "SELECT * FROM `Userdef_Filter` WHERE `Baum_ID` = ? AND `User_ID` = ? AND `Server_ID` = ? ORDER BY `Filtername` ASC;";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "iii", $Baum_ID, $_SESSION["User_ID"], $Server_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$gelesene_Filter = array();
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$gelesene_Filter[] = $line;
	}
	mysqli_free_result($result);
	mysqli_stmt_close($stmt);
	$Form_Filterliste = "<option></option>";
	$i = 0;
	while($i < count($gelesene_Filter)) {
		if($gelesene_Filter[$i]["Filtername"] == $Form_Filter_laden) {
			$Form_Filterliste = $Form_Filterliste."<option selected>".$gelesene_Filter[$i]["Filtername"]."</option>";
		} else {
			$Form_Filterliste = $Form_Filterliste."<option>".$gelesene_Filter[$i]["Filtername"]."</option>";
		}
		$i = $i + 1;
	}
}
echo "<input name='Baum_ID' id='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "<input name='Form_Filterliste' id='Form_Filterliste' value='".$Form_Filterliste."' type='hidden'>\n";
echo "<input id='js_code' name='JS' type='hidden' value='".$JS."'>\n";
echo "<input id='current' name='current' type='hidden' value='".$current."'>\n";
echo "<input id='darstellung' name='Darstellung' type='hidden' value='".$Darstellung."'>\n";
echo "<input id='form_tabellenzeilen' name='Tabellenzeilen' type='hidden' value='".$Tabellenzeilen."'>\n";
echo "<input id='navigationsbereich' name='Navigationsbereich' type='hidden' value='".$Navigationsbereich."'>\n";
echo "<input id='aktion' name='Aktion' type='hidden' value=''>\n";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";

if($mobil == 1) {
	include ("./Formular_navi_mobil.php");
} else {
	include ("./Formular_navi.php");
}

if(substr($Datenquelle,-1,1) == ";") {$Datenquelle = substr($Datenquelle,0,strlen($Datenquelle)-1);}
$query = $Datenquelle;
$DB_Bereich = str_replace("§§§","'", $DB_Bereich);
$DB_Bereich = str_replace("@@@",'"', $DB_Bereich);
if($Darstellung == "Formular" or $Darstellung == $Text[16]) {
	$DB_Bereich = str_replace(' onclick="auswaehlen(this);"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' ontouchend="auswaehlen(this);"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' class="context-menu-two context-menu-active"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' class="context-menu-two"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' draggable="true"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' ondragstart="drag(event)"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' class="context-menu-one"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' onclick1', ' onclick', $DB_Bereich);
	$DB_Bereich = str_replace(' ondrop="drop(event);"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' ondragover="allowDrop(event);"', '', $DB_Bereich);
	$DB_Bereich = str_replace(' onclick="auswaehlen(this);"', '', $DB_Bereich);
//	mysqli_stmt_close($stmt);
	$query = $query." LIMIT 1;";
	$stmt = mysqli_prepare($db_Satz,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$finfo = mysqli_fetch_fields($result);
	$Datenfeldnamen = "";
	foreach ($finfo as $val) {
		$Datenfeldnamen = $Datenfeldnamen.$val->name.",";
	}
} else {
	echo "<br><table id='datentabelle' class='Text_einfach' style='background: #FCEDD9;' cellpadding = '4'>\n";
	$query = $query." LIMIT ".strval(intval($Navigation_Satz) - 1).",".$Tabellenzeilen.";";
	$stmt = mysqli_prepare($db_Satz,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Ergebnis = "<tr class='Tabelle_Ueberschrift'>";
	$finfo = mysqli_fetch_fields($result);
	$Datenfeldnamen = "";
	$i = 0;
	foreach ($finfo as $val) {
		$Ergebnis = $Ergebnis."<td style='border-bottom-style: solid; border-bottom-color: #000000;	border-bottom-width: 1px;'>".$val->name."</td>";
		if($val->name == $Indexfeld) {$iSpalte = $i;}
		$i = $i + 1;
		$Datenfeldnamen = $Datenfeldnamen.$val->name.",";
	}
	$Ergebnis = $Ergebnis."</tr>";
	$Zeile = $Navigation_Satz;
	while ($row = mysqli_fetch_row($result)) {
		$i = 0;
		$Markierung = "";
		if($Zeile == $Navigation_Satz) {$Markierung = " class='Tabellenzeile'";}
		$Ergebnis = $Ergebnis."<tr indexwert='".$row[$iSpalte]."' id='Zeile".$Zeile."' onkeydown='Taste_gedrueckt();' onclick='Zeile_markieren(\"".$Zeile."\");' ondblclick='zur_Formularansicht(\"".$Zeile."\");'".$Markierung.">";
		while ($i < count($row)) {
			$Ergebnis = $Ergebnis."<td class='Unterstrich'>".html_entity_decode($row[$i])."</td>";
			$i = $i + 1;
		}
		$Ergebnis = $Ergebnis."</tr>";
		$Zeile = $Zeile + 1;
	}
	echo $Ergebnis;
	mysqli_stmt_close($stmt);
	echo "</table>";
}
if(substr($Datenfeldnamen, -1, 1) == ",") {$Datenfeldnamen = substr($Datenfeldnamen, 0, strlen($Datenfeldnamen) - 1);}
echo "<input type ='hidden' id='datenfeldnamen' name='Datenfeldnamen' value='".$Datenfeldnamen."'>";
if($Darstellung == "Formular" or $Darstellung == $Text[16]) {
	$Datenfeldnamen = explode(",",$Datenfeldnamen);
	$i = 0;
	while($i < count($Datenfeldnamen)) {
		echo "<input type ='hidden' id='schatten_".$Datenfeldnamen[$i]."' name='Schatten_".$Datenfeldnamen[$i]."' value='".htmlentities($line_Satz[$Datenfeldnamen[$i]])."'>";
		$i = $i + 1;
	}
}
echo "</form>\n";
if($Darstellung == "Formular" or $Darstellung == $Text[16]) {
	if($mobil == 1) {
		$verschieben = "";
	} else {
		$verschieben = " top:-30px;";
	}
	echo "<div style='position: relative;".$verschieben."' id='db_Bereich'>".$DB_Bereich."</div>";
}
mysqli_close($db);
mysqli_close($db_Satz);
?>
</div>
<script type="text/javascript">
$(window).on('load',function() {
	Start();
<?php
	$Bei_Start = str_replace("§§§","'", $Bei_Start);
	$Bei_Start = str_replace("@@@",'"', $Bei_Start);
	echo $Bei_Start."\n";
	echo "});\n\n";
	$JS = str_replace("§§§","'", $JS);
	$JS = str_replace("@@@",'"', $JS);
	echo $JS."\n\n";
	echo "\nfunction current() {\n";
	$current = str_replace("§§§","'", $current);
	$current = str_replace("@@@",'"', $current);
	echo $current;
	echo "\n}\n";
?>
tinymce.init({
  selector: 'textarea',
  language: 'de',
	plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link codesample table charmap nonbreaking anchor insertdatetime advlist lists help charmap quickbars',
  menubar: 'edit view insert format tools table help',
  toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap | fullscreen  preview print | insertfile image link anchor codesample',
  toolbar_sticky: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  toolbar_mode: 'sliding',
  contextmenu: 'link image table'
});

</script>
</body>
</html>