<?php
echo "<td align='center'>".$Text[18]."</td><td align='center'>".$Text[2]."</td><td>".$Text[3]."</td></td><td>".$Text[4]."</td><td></td><td>".$Text[5]."</td><td></td>\n";
echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('5');\">".$Text[6]."</a>&nbsp;&nbsp;&nbsp;</td>\n";
echo "<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>\n";
echo "<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>\n";
echo "<input type='hidden' id='gel' name='geloescht' value = '".$line["geloescht"]."'>\n";
echo "<input type='hidden' id='hintergrundfarbe' name='Hintergrundfarbe' value = '".$Hintergrundfarbe."'>\n";
echo "<input type='hidden' id='aktion' name='Aktion' value = ''>\n";
echo "<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>\n";
if($anzeigen == 1) {
	if ($mobil==1){
		if($line["geloescht"] != 1) {echo "<td><a href='Armaturenbrett_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[9]."</a>&nbsp;&nbsp;&nbsp;</td>";}
	} else {
		if($line["geloescht"] != 1) {echo "<td><a href='Armaturenbrett_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[9]."</a>&nbsp;&nbsp;&nbsp;</td>";}
	}
	echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[8]."</a>&nbsp;&nbsp;&nbsp;</td>";
	echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[10]."</a>&nbsp;&nbsp;&nbsp;</td>";
	if ($mobil==1){
		echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[7]."</a>&nbsp;&nbsp;&nbsp;</td>";
		if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a>&nbsp;&nbsp;&nbsp;</td>";}
	} else {
		echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[7]."</a></td>";
		if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>wiederherstellen</a>&nbsp;&nbsp;&nbsp;</td>";}
	}
}

echo "</tr>\n</form>\n";
echo "<form id='Einstellungen' name='Einstellungen' action='./Armaturenbrett.php' method='post' target='_self'>\n";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
$bed_Format = html_entity_decode($line["bed_Format"]);
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>\n";
echo "<tr><td><select class='Auswahl_Liste_Element' id='Spanne' name='Spanne' size='1'>\n";
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
		echo "<option selected>".$Text[12]."</option>\n";
	}else{
		echo "<option>".$Text[12]."</option>\n";
	}
if ($Spanne==172800){
		echo "<option selected>".$Text[13]."</option>\n";
	}else{
		echo "<option>".$Text[13]."</option>\n";
	}
if ($Spanne==604800){
		echo "<option selected>".$Text[14]."</option>\n";
	}else{
		echo "<option>".$Text[14]."</option>\n";
	}
if ($Spanne==1209600){
		echo "<option selected>".$Text[15]."</option>\n";
	}else{
		echo "<option>".$Text[15]."</option>\n";
	}
if ($Spanne==2592000){
		echo "<option selected>".$Text[16]."</option>\n";
	}else{
		echo "<option>".$Text[16]."</option>\n";
	}
if ($Spanne==31536000){
		echo "<option selected>".$Text[17]."</option>\n";
	}else{
		echo "<option>".$Text[17]."</option>\n";
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
echo "<td><input class='Text_Element' id='Zeitstempel' name='Zeitstempel' value= '".$Zeitstempel."' type='text' size='15' maxlength='31' onchange='lesen();'></td>\n";
echo "<td align='center'><input class='Schalter_fett_Element' value='<' id='zurueckg' type='button' name='schieben' onclick='Zeitschieben(\"<\");'></td>\n";
echo "<td align='center'><input class='Schalter_fett_Element' value='>' id='vorg' type='button' name='schieben' onclick='Zeitschieben(\">\");'></td>\n";
echo "<td align='center'><input class='Schalter_Element' value='".$Text[1]."' id='aktuelleZeit' type='button' name='schieben' onclick='Zeitschieben(\"".$Text[1]."\")';></td>\n";
echo "<td align='center'><input class='Text_Element' type='text' id='Intervall' name='Intervall' size='1' value= '".$Intervall."' onchange='Intervall=document.Einstellungen.Intervall.value;'></td>\n";
if ($Pos_Regler == ""){$Pos_Regler = "100";}
echo "<td colspan='6'><div class='slidecontainer'><input name = 'Pos_Regler' type='range' min='0' max='100' value='".$Pos_Regler."' class='slider' id='schieberegler'";
if($mobil == 1) {
	echo " ontouchend";
} else {
	echo " onclick";
}
echo "='Regler_schieben();'></div></td>\n";
echo "<input id='Baum_ID' type='hidden' value='".$Baum_ID."'>\n";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
echo "<input type = 'hidden' id = 'zeitspanne' value= '".$Spanne."'>\n";
if ($Pos_Regler_alt == ""){$Pos_Regler_alt = "100";}
echo "<input type = 'hidden' id = 'pos_Regler_alt' name = 'Pos_Regler_alt' value= '".$Pos_Regler_alt."'>\n";

echo "</tr></table>\n";
echo "</form></font>\n";
?>