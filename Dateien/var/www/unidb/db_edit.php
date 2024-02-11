<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<title>MariaDB Tabellen editieren</title>
<script type="text/javascript">
	function dyn_Feld_entfernen(Tabelle,dyn_Feld,Feld,ID_Feld,ID) {
		var SQL = "UPDATE `" + Tabelle + "` SET `" + dyn_Feld + "` = COLUMN_DELETE(`" + dyn_Feld + "`, '" + Feld + "') WHERE `" + ID_Feld + "` = " + ID + ";";
		document.forms.formular.abfrage.value = SQL;
		
	}

	function dyn_Feld_einfuegen(Tabelle,Feld,ID,ID_Feld,Methode) {
		var Feldinhalt=document.forms.formular.elements.namedItem("neuesfeldinhalt" + Feld).value;
		Feldinhalt=Feldinhalt.replace(/'/g, "\\'");
		if (Methode=="add") {
			var SQL = "UPDATE `" + Tabelle + "` SET `" + Feld + "` = COLUMN_ADD(`" + Feld + "`, '" + document.forms.formular.elements.namedItem("neuesfeldname" + Feld).value + "', '" + Feldinhalt + "') WHERE `" + ID_Feld + "` = " + ID + ";";
		} else {
			var SQL = "UPDATE `" + Tabelle + "` SET `" + Feld + "` = COLUMN_CREATE('" + document.forms.formular.elements.namedItem("neuesfeldname" + Feld).value + "', '" + Feldinhalt + "') WHERE `" + ID_Feld + "` = " + ID + ";";
		}
		document.forms.formular.abfrage.value = SQL;
	}
	
	function editieren(Tabelle,Feldname,ID_Feld,ID,dyn_Feld,dynamisch){
		if (dynamisch!="ja") {
			Feldinhalt=document.forms.formular.elements.namedItem(Feldname).value;
			Feldinhalt=Feldinhalt.replace(/'/g, "\\'");
			var SQL = "UPDATE `" + Tabelle + "` SET `" + Feldname + "` = '" + Feldinhalt + "' WHERE `" + ID_Feld + "` = " + ID + ";";
		} else {
			var zusammengesetzt = Feldname + dyn_Feld;
			var Feldinhalt= document.forms.formular.elements.namedItem(zusammengesetzt).value
			Feldinhalt=Feldinhalt.replace(/'/g, "\\'");
			var SQL = "UPDATE `" + Tabelle + "` SET `" + Feldname + "` = COLUMN_ADD(`" + Feldname + "`,'" + dyn_Feld + "','" + Feldinhalt + "') WHERE `" + ID_Feld + "` = " + ID + ";";
		}
		document.forms.formular.abfrage.value = SQL;
	}
	
	function loeschen() {
		if (confirm("Wirklich löschen?")==false) {
			document.getElementById("satz_entfernen").value="";
		} 
	}
</script>
<?php
	session_start();
	header("X-XSS-Protection: 1");
	include('Sitzung.php');
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head><body class='allgemein'>";
	header("X-XSS-Protection: 1");
	include './conf_unidb.php';
	$Datenbank="unidb";
	//SQL ausfuehren
	if($Aktion=="ausführen") {
		$result = mysqli_query($db, $Abfrage);
	}

	if($satz_entfernen=="löschen") {
		$result = mysqli_query($db, "DELETE FROM `".$Tabelle."` WHERE `".$Primaerschluesselfeld."` = ".$ID.";");
		$ID=0;
	}	
	
	if($von<1) {
		$von=0;
	}
	//Anzahl legt fest, wieviele Datensaetze gleichzeitig angezeigt werden
	$Anzahl_Datensaetze_zeigen=10;
	
	//Tabelle auswählen
	echo "<table cellpadding='10px'><tr><td>Tabelle wählen:</td>";
	$result = mysqli_query($db, "SHOW TABLES");
	while ($row = mysqli_fetch_row($result)) {
		$fett_ein="";
		$fett_aus="";
		if($Tabelle==$row[0]) {
			$fett_ein="<b>";
			$fett_aus="</b>";
		}
		echo "<td><a href='./db_edit.php?Tabelle=".$row[0]."' target='_self'>".$fett_ein.$row[0].$fett_aus."</a></td>";
	}	
	echo "</table><br>";
	if ($Tabelle!=""	){
		echo "<a href='./db_edit.php?Tabelle=".$Tabelle."&Aktion=neu' target='_self'>neuer Datensatz</a><br><br>";
		echo "Felder:&nbsp;<table cellpadding='5px' Border='1'><tr>";
		$i=0;
		$db_schema = mysqli_connect($sqlhostname,$login,$password,'information_schema');
		$result = mysqli_query($db_schema, "SELECT DATA_TYPE, COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_KEY FROM `COLUMNS` WHERE `TABLE_SCHEMA` LIKE '".$Datenbank."' AND `TABLE_NAME` LIKE '".$Tabelle."'");
		mysqli_close($db_schema);
		while ($Ergebnis = mysqli_fetch_row($result)) {
			$Feld["Typ"][$i]=$Ergebnis[0];
			$Feld["Name"][$i]=$Ergebnis[1];
			$Feld["Vorgabewert"][$i]=$Ergebnis[2];
			$Feld["NULL_Wert"][$i]=$Ergebnis[3];
			$Feld["Schluessel"][$i]=$Ergebnis[4];
			if($Ergebnis[4]=="PRI") {
				$Primaerschluesselfeld=$Ergebnis[1];
			}
			echo "<td><b>".$Ergebnis[1]."</b><br>".$Ergebnis[0]."</td>";
			$i++;
		}
	}
	echo "</tr>";
	
	//neuer Datensatz einfügen?
	//Dieser Teil kommt erst hier, weil dafür das Array Feld gebraucht wird.
	if($Aktion=="neu") {
		$query = "INSERT INTO `".$Tabelle."` (";
		for($i=0; $i < count($Feld["Name"]); $i++) {
			if($Feld["NULL_Wert"][$i]==false) {
				$query = $query."`".$Feld["Name"][$i]."`, ";
			}
		}
		if($query != "INSERT INTO `".$Tabelle."` (") {
			$query = $query.") VALUES (";
			$query=str_replace(", ) VALUES", ") VALUES", $query);
			for($i=0; $i < count($Feld["Name"]); $i++) {
				if($Feld["NULL_Wert"][$i]==false) {
					$query = $query."'0', ";
				}
			}
			$query = $query.");";
			$query=str_replace(", );", ");", $query);
		} else {
			$query = $query."`".$Feld["Name"][0]."`) VALUES (NULL);";
		}
		$result = mysqli_query($db, $query);
	}	
	//Ende einfügen neuer Datensatz	
	

	//Anzahl der Datensaetze der Tabelle in der Variablen Anzahl_Datensaetze_gesamt ablegen
	$result = mysqli_query($db, "SELECT `".$Primaerschluesselfeld."` FROM `".$Tabelle."`;");
	$Anzahl_Datensaetze_gesamt = mysqli_num_rows($result);
	//Datensaetze in der Tabelle Anzahl_Datensaetze_zeigen
	$result = mysqli_query($db, "SELECT * FROM `".$Tabelle."` LIMIT ".$von.",".$Anzahl_Datensaetze_zeigen.";");

	while ($row = mysqli_fetch_row($result)) {
		if($ID<1) {
			echo "<tr><td><a href='./db_edit.php?Tabelle=".$Tabelle."&ID=".$row[0]."&von=".$von."' target='_self'>".$row[0]."</a></td>";
		}
		for($x=1; $x < count($Feld["Name"]); $x++) {
			if($ID<1) {
				echo "<td>".$row[$x]."</td>";
			}
		}
		echo "</tr>";
	}
	echo "</table><br>";

	if($ID>0) {
		//Spaltennamen aus dem dynamischen Feld lesen
		if($ID>0) {
			for($i=0; $i < count($Feld["Name"]); $i++) {
				if(strpos($Feld["Typ"][$i], "blob")>=0) {
					$query="SELECT `".$Feld["Name"][$i]."`, column_list(`".$Feld["Name"][$i]."`) FROM `".$Tabelle."` WHERE `".$Primaerschluesselfeld."` = ".$ID.";";
					$req = mysqli_query($db,$query);
					$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
					$Text=$line["column_list(`".$Feld["Name"][$i]."`)"];
					$dynFeld["Name"][$i]=explode("`,`", $Text);
					for($x=0; $x < count($dynFeld["Name"][$i]); $x++) {
						$dynFeld["Name"][$i][$x]=str_replace("`", "", $dynFeld["Name"][$i][$x]);
					}
				}
			}
		}

		//maximale Laenge des Blob Feldes feststellen
		$query="SELECT * FROM `".$Tabelle."` WHERE `".$Primaerschluesselfeld."` = ".$ID.";";
		$req = mysqli_query($db,$query);
		$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
		$Laengenarray = mysqli_fetch_lengths($req);
		$Laenge=0;
		for($x=0; $x < count($Laengenarray); $x++) {
			if($Laenge<$Laengenarray[$x]) {
				$Laenge=$Laengenarray[$x];
			}
		}
		
		//kompletter Datensatz lesen
		//zuerst die Abfrage zusammenbasteln
		$query="select ";
		for($i=0; $i < count($Feld["Name"]); $i++) {
			if($dynFeld["Name"][$i][0]=="") {
				$query=$query."`".$Feld["Name"][$i]."`, ";
			} else {
				for($x=0; $x < count($dynFeld["Name"][$i]); $x++) {
					if($dynFeld["Name"][$i][$x]!="") {
						$query=$query."column_get(`".$Feld["Name"][$i]."`, '".$dynFeld["Name"][$i][$x]."' as CHAR(".$Laenge.")) as `".$dynFeld["Name"][$i][$x]."`, ";
					}
				}
			}
		}

		$query=$query."FROM `".$Tabelle."` where `".$Primaerschluesselfeld."` = ".$ID.";";
		$query=str_replace(", FROM", " FROM", $query);
		$req = mysqli_query($db,$query);
		$line = mysqli_fetch_row($req);

		//Formular benutzen
		echo "<form id = 'formular' action='db_edit.php' method='post' target='_self'>";
		
		//Ausgabe der Tabelle
		echo "<table cellpadding='10px' border='1'><tr>";
		//Feldnamen als oberste Tabellenzeile schreiben
		for($i=0; $i < count($Feld["Name"]); $i++) {
			if(is_bool(strpos($Feld["Typ"][$i], "blob"))==true) {
				echo "<td>".$Feld["Name"][$i]."</td>";
			} else {
				if($dynFeld["Name"][$i][0]!="") {
					$Methode="add";
					for($x=0; $x < count($dynFeld["Name"][$i]); $x++) {
						echo "<td bgcolor=\"#F4FCC6\">".$Feld["Name"][$i]."&nbsp;&nbsp;-&nbsp;&nbsp;".$dynFeld["Name"][$i][$x]."&nbsp;&nbsp;&nbsp;<input type=\"button\" value = \"Feld löschen\" onclick=\"dyn_Feld_entfernen('".$Tabelle."','".$Feld["Name"][$i]."','".$dynFeld["Name"][$i][$x]."','".$Primaerschluesselfeld."','".$line[0]."');\"></td>";
					}
				} else {
					$Methode="create";
				}
				echo "<td>neues Feld einfügen in&nbsp;&nbsp;".$Feld["Name"][$i]."&nbsp;&nbsp;<input id='neuesfeldname".$Feld["Name"][$i]."' value='' type='text' name='neues_Feld_Name".$Feld["Name"][$i]."'><input type=\"button\" value = \"Feld einfügen\" onclick=\"dyn_Feld_einfuegen('".$Tabelle."','".$Feld["Name"][$i]."','".$line[0]."','".$Primaerschluesselfeld."','".$Methode."');\"></td>";
			}	
		}
		echo "</tr><tr>";
		//Datensatz in die zweite Zeile schreiben
		$Zaehler=0;
		for($i=0; $i < count($Feld["Name"]); $i++) {
			if(is_bool(strpos($Feld["Typ"][$i], "blob"))==true) {
				echo "<td><input value='".$line[$Zaehler]."' id = \"".strtolower($Feld["Name"][$i])."\" type='text' name='".$Feld["Name"][$i]."'></td>";
				$Zaehler++;
			} else {
					if($dynFeld["Name"][$i][0]!="") {
					for($x=0; $x < count($dynFeld["Name"][$i]); $x++) {
						echo "<td><textarea id = '".strtolower($Feld["Name"][$i]).strtolower($dynFeld["Name"][$i][$x])."' name='".$Feld["Name"][$i].$dynFeld["Name"][$i][$x]."' cols='60' rows='5'>".$line[$Zaehler]."</textarea></td>";
						$Zaehler++;					
					}
				} else {
					$Zaehler++;
				}
				//neues Feld hinzufuegen
				echo "<td><textarea id='neuesfeldinhalt".$Feld["Name"][$i]."' name='neues_Feld_Inhalt".$Feld["Name"][$i]."' cols='70' rows='5'></textarea></td>";
			}
		}
		
		echo "</tr><tr>";
		//Schalter zum uebernehmen hinzufuegen
		for($i=0; $i < count($Feld["Name"]); $i++) {
			if(is_bool(strpos($Feld["Typ"][$i], "blob"))==true) {
				echo "<td><input type=\"button\" value = \"Änderung übernehmen\" onclick=\"editieren('".$Tabelle."','".$Feld["Name"][$i]."','".$Primaerschluesselfeld."','".$line[0]."','".$Feld["Name"][$i]."','nein');\"></td>";
			} else {
				if($dynFeld["Name"][$i][0]!="") {
					for($x=0; $x < count($dynFeld["Name"][$i]); $x++) {
						echo "<td><input type=\"button\" value = \"Änderung übernehmen\" onclick=\"editieren('".$Tabelle."','".$Feld["Name"][$i]."','".$Primaerschluesselfeld."','".$line[0]."','".$dynFeld["Name"][$i][$x]."','ja');\"></td>";
					} 
				} 
				echo "<td></td>";
			}		
		}
		echo "</tr></table>";
		echo "<br><br><textarea id = 'abfrage' name='Abfrage' cols='120' rows='5'>".$query."</textarea>";
		echo "<br><br><table><tr><td><input value='ausführen' type='submit' name='Aktion'></td><td><input id='satz_entfernen' value='löschen' name='satz_entfernen' type='submit' onclick='loeschen();'></td></tr></table>";
		echo "<input value='".$ID."' type='hidden' name='ID'>";
		echo "<input value='".$Primaerschluesselfeld."' type='hidden' name='Primaerschluesselfeld'>";
		echo "<input value='".$Tabelle."' type='hidden' name='Tabelle'>";
		echo "</form>";
	} else {
		//Falls die Tabelle mehr Datensaetze enthaelt, als angezeigt werden, dann Navigationslinks einbauen
		echo "<table cellpadding='10'><tr><td>";
		if($von>0) {
			echo "<td><a href='./db_edit.php?Tabelle=".$Tabelle."&von=0' target='_self'>Anfang</a></td>";
		}
		if($von>$Anzahl_Datensaetze_zeigen) {
			$temp=$von-$Anzahl_Datensaetze_zeigen;
			echo "<td><a href='./db_edit.php?Tabelle=".$Tabelle."&von=".$temp."' target='_self'>zurück</a></td>";
		}
		if($von+$Anzahl_Datensaetze_zeigen<$Anzahl_Datensaetze_gesamt) {
			$temp=$von+$Anzahl_Datensaetze_zeigen;
			echo "<td><a href='./db_edit.php?Tabelle=".$Tabelle."&von=".$temp."' target='_self'>weiter</a></td>";
			$temp = round($Anzahl_Datensaetze_gesamt/$Anzahl_Datensaetze_zeigen, 0);
			$temp=($temp-1)*$Anzahl_Datensaetze_zeigen;
			echo "<td><a href='./db_edit.php?Tabelle=".$Tabelle."&von=".$temp."' target='_self'>Ende</a></td>";
		}
		echo "</tr></table>";
	}

?>
</body>
</html>
