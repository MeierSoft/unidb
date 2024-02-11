<?php
session_start();
header("X-XSS-Protection: 1");
include('Sitzung.php');
require_once 'conf_DH.php';
//Point_ID für den Tag finden
$Point_ID = Point_ID_finden($Tag_ID, $dbDH);

If ($Typ!="Tabelle" and $Typ!="Trend"){
	//Ausdruck oder Tag?
	if (strlen($Ausdruck) > 0){
		include('./DH_berechnen.php');
		if($Zeitpunkt == NULL) {$Zeitpunkt = "jetzt";}
		eval ('$Wert = '.berechnen($Zeitpunkt,html_entity_decode($Ausdruck)).';');
	}else{
		include('Trend_funktionen.php');
		//Es geht um einen Tag. Aktuell oder Archiv?
		if ($Zeitpunkt!="jetzt"){
			$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt));
			$query = "SELECT * FROM `akt` Where `Point_ID` = ? AND `Timestamp` <= ? order by `Timestamp` DESC Limit 1;";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_bind_param($stmt, "is", $Point_ID, $Zeitstempel);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$gefunden = mysqli_num_rows($result);
			mysqli_stmt_close($stmt);
			
			if($gefunden == 0) {
				$Werte = lesen("rV",$Point_ID, $Zeitstempel, $Zeitstempel,1 ,0, 0, 0, 0);
				$Wert = $Werte[1][0];
				$Zeitpunkt = $Werte[0][0];
			} else {
				$line_Wert = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Wert = $line_Wert["Value"];
				$Zeitpunkt = $line_Wert["Timestamp"];
			}
			$Zeitstempel1 = $Zeitpunkt;
			$Zeitpunkt = substr($Zeitpunkt, 11, 8);
			$Zeitzahl=strtotime($Zeitstempel);
		}else{
			$Zeitzahl = time();
			$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID = ? ORDER BY Timestamp DESC LIMIT 1;";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_bind_param($stmt, "i", $Point_ID);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
			mysqli_stmt_close($stmt);
			$Wert=$line_Value['Value'];
			$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitzahl));
			$Zeitstempel1 = $line_Value['Timestamp'];
			$Zeitstempel = substr($Zeitstempel1, 11, 8);
		}
		$query="SELECT * FROM Tags WHERE Point_ID = ?;";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_bind_param($stmt, "i", $Point_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$Wert=round($Wert,$line_Tag['Dezimalstellen']);
	}
}
//Tabelle?

If ($Typ=="Tabelle"){
	$query = "SELECT `Dezimalstellen`, `EUDESC` FROM `Tags` Where `Point_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "i", $Point_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	if ($Zeitpunkt!="jetzt"){
		$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt));
		$Zeitstempel2=strftime('%Y-%m-%d %H:%M:%S',($Zeitpunkt-$Zeitraum));
	}else{
		$Zeitstempel=strftime('%Y-%m-%d %H:%M:%S',(time()));
		$Zeitstempel2=strftime('%Y-%m-%d %H:%M:%S',(time()-$Zeitraum));
	}
	echo "<span style='font-family:  Verdana, Arial, Helvetica, sans-serif; font-size: 14px'>";
	echo "<font size='2'><table cellpadding=3>\n";
	echo "<tr><td>Zeitpunkt</td><td>Wert</td><td>Einheit</td></tr>\n";
	$Werte = lesen("rV",$Point_ID, $Zeitstempel2, $Zeitstempel,1 ,0, 0, 0, 0);
	$Wert = $Werte[1][0];
	$Zeitpunkt = $Werte[0][0];
	$i = 0;
	while ($i < count($Werte[0])) {
		echo "<tr bgcolor='#E5E5E5'><td>".$Werte[0][$i]."</td><td align='right'>".round($Werte[1][$i],$line_Tag["Dezimalstellen"])."</td><td>".$line_Tag["EUDESC"]."</td></tr>\n";
		$i = $i + 1;
	}
	echo "</table></font></span>\n@.";	
	$Zeitzahl=strtotime($Zeitstempel2);
}

//Multistate?
If ($Typ=="Multistate"){
	$query = "SELECT * FROM `Multistates_Detail` Where `Multistate_ID` = ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "i", $Gruppe);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Bild="leer.png";
	while ($line_States = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$pos1 = strpos($line_States["Operant"], "=");
 		if ($pos1 !== false) {
 			if ($Wert==$line_States["Wert"]){
				$Bild=$line_States["Bild"];
 			}
	 	}	
 		$pos1 = strpos($line_States["Operant"], ">");
 		if ($pos1 !== false) {
	 		if ($Wert>$line_States["Wert"]){
				$Bild=$line_States["Bild"];
	 		}
	 	}
 		$pos1 = strpos($line_States["Operant"], "<");
 		if ($pos1 !== false) {
	 		if ($Wert<$line_States["Wert"]){
				$Bild=$line_States["Bild"];
	 		}
	 	}
	}
	mysqli_stmt_close($stmt);
	if($verlinken==1){
		echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitzahl."' target='_blank'><img src='Multistates/".$Bild."' width='".$Breite."' height='".$Hoehe."'>@".$Zeitstempel1;
	}
	else {
		echo "<img src='Multistates/".$Bild."' width='".$Breite."' height='".$Hoehe."'>@".$Zeitstempel1;
	}
}

//Tag?
If ($Typ=="Tag"){
	if($verlinken==1){
		echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitzahl."' target='_blank'>".$Wert." ".$Einheit."</a>@".$Zeitstempel1;
	}
	else {
		echo $Wert." ".$Einheit."@".$Zeitstempel1;
	}
}


//Instrument?
If ($Typ=="Instrument"){
	echo $Wert."@".$Zeitstempel1;
}


//Trend?
If ($Typ=="Trend"){
	if ($Zeitpunkt=="jetzt"){
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".time()."' target='_blank'><IMG SRC='Ajax_Trend_zeichnen.php?Tag=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".time()."&akt=1'></a>@";
		}else{
			echo "<IMG SRC='Ajax_Trend_zeichnen.php?Tag_ID=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitzahl=".time()."&akt=1'>@";
		}
	}else{
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitpunkt."&Zeitpunkt=".$Zeitpunkt."' target='_blank'><IMG SRC='Ajax_Trend_zeichnen.php?Tag=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".$Zeitpunkt."&akt=0' target='_blank'></a>@";
		}else{
			echo "<IMG SRC='Ajax_Trend_zeichnen.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitpunkt."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".$Zeitpunkt."&akt=0'>@";
		}
	}
}

//Sparkline?
If ($Typ=="Sparkline"){
	if ($Zeitpunkt=="jetzt"){
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".time()."' target='_blank'><IMG SRC='./Sparkline/sparkline.php?Tag_ID=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".time()."&akt=1'></a>@";
		}else{
			echo "<IMG SRC='./Sparkline/sparkline.php?Tag_ID=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitzahl=".time()."&akt=1'>@";
		}
	}else{
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitpunkt."&Zeitpunkt=".$Zeitpunkt."' target='_blank'><IMG SRC='./Sparkline/sparkline.php?Tag_ID=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".$Zeitzahl."&akt=0' target='_blank'></a>@";
		}else{
			echo "<IMG SRC='./Sparkline/sparkline.php?Tag_ID=".$Tag_ID."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitraum=".$Zeitraum."&Zeitpunkt=".$Zeitzahl."&akt=0'>@";
		}
	}
}

//vertikaler Balken?
If ($Typ=="vert_Balken"){
	if ($wert_anzeigen == 1){
		$Wert_anzeigen = "<br>".$Wert." ".$Einheit;
	} else {
		$Wert_anzeigen = "";
	}
	if ($Zeitstempel==""){
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitzahl."' target='_blank'><IMG SRC='vert_Balken.php?Point_ID=".$Point_ID."&min=".$min."&max=".$max."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitpunkt=".time()."&Wert=".$Wert."' border='1'></a>".$Wert_anzeigen."@".$Zeitstempel1;
		}else{
			echo "<IMG SRC='vert_Balken.php?Point_ID=".$Point_ID."&min=".$min."&max=".$max."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitpunkt=".time()."&Wert=".$Wert."' border='1'>".$Wert_anzeigen."@".$Zeitstempel1;
		}
	}else{
		$Zeitzahl=strtotime($Zeitstempel);
		if ($verlinken==1){
			echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitzahl."' target='_blank'><IMG SRC='vert_Balken.php?Point_ID=".$Point_ID."&min=".$min."&max=".$max."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitpunkt=".$Zeitzahl."&Wert=".$Wert."' border='1'></a>".$Wert_anzeigen."@".$Zeitstempel1;
		}else{
			echo "<IMG SRC='vert_Balken.php?Point_ID=".$Point_ID."&min=".$min."&max=".$max."&Hoehe=".$Hoehe."&Breite=".$Breite."&Zeitpunkt=".$Zeitzahl."&Wert=".$Wert."' border='1'>".$Wert_anzeigen."@".$Zeitstempel1;
		}
	}
}

if ($Typ == "Statustext"){
	//Eigenschaft 2 auslesen
	$query = "SELECT `Property_2`, `Property_3` FROM `Points` WHERE Point_ID = ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "i", $Point_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Bereich = $line_Value['Property_2'];
	$Typ = $line_Value['Property_3'];
	$Werte = array();
	if($Typ == "b") {
		$Wert_bin = strval(decbin($Wert));
		$i = 0;
		$x = 0;
		while (strlen($Wert_bin) > 0) {
			$Bit = substr($Wert_bin, -1);
			$Wert_bin = substr($Wert_bin, 0, strlen($Wert_bin) - 1);
			if ($Bit == 1) {
				$Werte[$x] = $i;	
				$x = $x + 1;
			}		
			$i = $i + 1;		
		}
	} else {
		$Werte[0] = $Wert;
	}
	$Ausgabe = "";
	if(count($Werte) > 0) {
		$Vorz = "";
		if(count($Werte) > 1) {$Vorz = "- ";}
		$i = 0;
		$query = "SELECT `Text` FROM `Statusmeldungen` WHERE `Bereich` LIKE '".$Bereich."' AND (";
		while($i < count($Werte)) {
			$query = $query." `Wert` = ".$Werte[$i]." OR ";
			$i = $i + 1;
		}
		$query = substr($query, 0, strlen($query) - 4);
		$query = $query.");";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$Ausgabe = $Ausgabe.$Vorz.$line["Text"]."<br>";
		}
		$Ausgabe = substr($Ausgabe, 0, strlen($Ausgabe) - 4);
		mysqli_stmt_close($stmt);
	}
	if($verlinken==1){
		echo "<a href='Trend.php?Tag_ID=".$Tag_ID."&Zeitzahl=".$Zeitzahl."' target='_blank'>".$Ausgabe."</a>@".$Zeitstempel1;
	}
	else {
		echo $Ausgabe."@".$Zeitstempel1;
	}
}
// schliessen der Verbindung
mysqli_close($dbDH);
?>