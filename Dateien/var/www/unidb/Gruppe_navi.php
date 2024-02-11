<?php
	echo "</form>\n";
	echo "<font size='2'>\n";
	echo "<table cellpadding='3px' style='background: #d6d6d6;'>\n";
	$Ausgabe = "<td>".$Text[9]."</td><td>".$Text[10]."</td><td></td><td></td><td></td><td></td><td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('7');\">".$Text[13]."</a></td>\n";
	if($anzeigen == 1) {
		if ($Timestamp == null) {
			if($line["geloescht"] != 1) {$Ausgabe = $Ausgabe."<td><a href='Gruppe_editieren.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[14]."</a></td>";}
			if ($line["geloescht"] != 1) {
				$Ausgabe = $Ausgabe."<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[16]."</a></td>";
				$Ausgabe = $Ausgabe."<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[17]."</a></td>";
				$Ausgabe = $Ausgabe."<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[15]."</a></td>";
				if($line["geloescht"] == 1) {$Ausgabe = $Ausgabe."<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";}
			} else {
				$Ausgabe = $Ausgabe."<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a></td>";
			}
		} else {
			$Ausgabe = $Ausgabe."<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>Aktuelle Version durch diese hier ersetzen.</a></td>";
			$Ausgabe = $Ausgabe."<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>Diese Version löschen.</a></td>";
		}
	}
	
	$Ausgabe = $Ausgabe."</tr><tr>\n";
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
				$Ausgabe = $Ausgabe."<td><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
				while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
					if($line1["Timestamp"] == $Timestamp) {
						$Ausgabe = $Ausgabe."<option selected>".$line1["Timestamp"]."</option>";
					} else {
						$Ausgabe = $Ausgabe."<option>".$line1["Timestamp"]."</option>";
					}
				}
				$Ausgabe = $Ausgabe."</select></td>";
			}
			mysqli_stmt_close($stmt1);
		}
	}
	if($erweitern == 1) {
		$Ausgabe = "<tr><td>Version</td>".$Ausgabe;
	} else {
		$Ausgabe = "<tr>".$Ausgabe;
	}
	echo $Ausgabe;
	echo "<form id='Einstellungen' name='Einstellungen' action='./Gruppe.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='_self'>\n";

	echo "<td><select class='Auswahl_Liste_Element' id='Spanne' name='Spanne' size='1'>\n";
	if ($Spanne==3600){
		echo "<option selected>1 h</option>\n";
	}else{
		echo "<option>1 h</option>\n";
	}
	if ($Spanne==14400){
		echo "<option selected>4 h</option>\n";
	}else{
		echo "<option>4 h</option>\n";
	}
	if ($Spanne==28800){
		echo "<option selected>8 h</option>\n";
	}else{
		echo "<option>8 h</option>\n";
	}
	if ($Spanne==86400){
		echo "<option selected>".$Text[2]."</option>\n";
	}else{
		echo "<option>".$Text[2]."</option>\n";
	}
	if ($Spanne==172800){
		echo "<option selected>".$Text[3]."</option>\n";
	}else{
		echo "<option>".$Text[3]."</option>\n";
	}
	if ($Spanne==604800){
		echo "<option selected>".$Text[4]."</option>\n";
	}else{
		echo "<option>".$Text[4]."</option>\n";
	}
	if ($Spanne==1209600){
		echo "<option selected>".$Text[5]."</option>\n";
	}else{
		echo "<option>".$Text[5]."</option>\n";
	}
	if ($Spanne==2592000){
		echo "<option selected>".$Text[6]."</option>\n";
	}else{
		echo "<option>".$Text[6]."</option>\n";
	}
	if ($Spanne==31536000){
		echo "<option selected>".$Text[7]."</option>\n";
	}else{
		echo "<option>".$Text[7]."</option>\n";
	}
	echo "</select></td>\n";

	if ($Zeitstempel!=$Text[1]){
		$Zeitpunkt=strtotime($Zeitstempel);
		}else{
		$Zeitpunkt=$Text[1];
	}
	$_SESSION['Zeitpunkt'] = $Zeitpunkt;
	$_SESSION['Zeitstempel'] = $Zeitstempel;
	echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "<td><input class='Text_Element' id = 'zeitstpl' name='Zeitstempel' value= '".$Zeitstempel."' type='text' size='15' maxlength='31'></td>\n";
	echo "<td><input class='Schalter_fett_Element' value='<' type='submit' name='schieben'></td>\n";
	echo "<td><input class='Schalter_fett_Element' value='>' type='submit' name='schieben'></td>\n";
	echo "<td><input class='Schalter_fett_Element' value='".$Text[1]."' type='submit' name='schieben'></td>\n";
	echo "<td><input class='Schalter_fett_Element' value='".$Text[11]."' type='submit' name='Aktion'></td>";
	if ($Pos_Regler == ""){$Pos_Regler = "100";}
	echo "<td colspan='5'><div class='slidecontainer'><input name = 'Pos_Regler' style='width: 300px;' type='range' min='0' max='100' value='".$Pos_Regler."' class='slider' id='schieberegler' onclick='Regler_schieben();'></div></td>\n";
	
	

	echo "</tr></table>\n";
?>
