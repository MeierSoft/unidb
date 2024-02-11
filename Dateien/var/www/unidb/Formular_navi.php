<?php
if($UForm != 1) {
 	echo "<table style='background: #FCEDD9; height: 30px;'><tr><td><input class='Schalter_Element' id='datei' name='Datei' value='".$Text[0]."' type='button' onclick='menue_sichtbar(\"menue1\")'>";
	echo "<div id='menue1' class='dropdown-content'>";
		if($anzeigen == 1) {
			echo "<p><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('31');\">".$Text[1]."</a></p>";
			echo "<p><a href='./loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[2]."</a></p>";
   	 	echo "<p><a href='./verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[7]."</a></p>";
   	 	echo "<p><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a></p>";
    		echo "<p><a href='Formular_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></p>";

    	}
	echo "</div></td><td style='width: 10px;'></td>";
}

if($Navigationsbereich == 1) {
	echo "<td><table id='navi' class='Text_einfach' style='display:none;'>\n";
	if($Darstellung == "Formular" or $Darstellung == $Text[16]) {
		echo "<tr><td><input title='".$Text[8]."' class='Schalter_Element' name='Ansicht' id='ansicht' value='".$Text[9]."' type='button' onclick='Ansicht_umschalten();'></td>\n";
		echo "<td><input type='button' class='Schalter_Element' value='".$Text[10]."' onclick='speichern();'></td>\n";
		echo "<td style='width: 10px;'></td>\n";
		echo "<td><input title='".$Text[11]."' class='Schalter_Element' id='nav_Anfang' name='Navigation' value='|<' type='button' onclick='Nav_Anfang(\"Satz\");'></td>\n";
		echo "<td><input title='".$Text[12]."' class='Schalter_Element' name='Navigation' value='<' type='button' onclick='Nav_zurueck(\"Satz\");'></td>\n";
		echo "<td><input title='".$Text[13]."' class='Text_Element' id='nav_Satz' name='Navigation_Satz' size='3' value='".$Navigation_Satz."' type='Text' onchange='document.forms[\"Einstellungen\"].submit();'>&nbsp;/&nbsp;".$Saetze."</td>\n";
		echo "<td><input title='".$Text[14]."' class='Schalter_Element' name='Navigation' value='>' type='button' onclick='Nav_weiter(\"Satz\");'></td>\n";
		echo "<td><input title='".$Text[15]."' class='Schalter_Element' name='Navigation' value='>|' type='button' onclick='Nav_Ende(\"Satz\");'></td>\n";
	} else {
		echo "<tr><td><input title='".$Text[8]."' class='Schalter_Element' name='Ansicht' id='ansicht' value='".$Text[16]."' type='button' onclick='Ansicht_umschalten();'></td>\n";
		echo "<td style='width: 10px;'></td>\n";
		echo "<td><input title='".$Text[17]."' class='Schalter_Element' name='Navigation' value='|<' type='submit' onclick='Nav_Anfang(\"Seite\");'></td>\n";
		echo "<td><input title='".$Text[18]."' class='Schalter_Element' name='Navigation' value='<' type='submit' onclick='Nav_zurueck(\"Seite\");'></td>\n";
		echo "<td><input title='".$Text[13]."' class='Text_Element' id='nav_Satz' name='Navigation_Satz' size='3' value='".$Navigation_Satz."' type='Text' onchange='document.forms[\"Einstellungen\"].submit();'> / <span id='saetze'>".$Saetze."</span></td>\n";
		echo "<td><input title='".$Text[19]."' class='Schalter_Element' name='Navigation' value='>' type='submit' onclick='Nav_weiter(\"Seite\");'></td>\n";
		echo "<td><input title='".$Text[20]."' class='Schalter_Element' name='Navigation' value='>|' type='submit' onclick='Nav_Ende(\"Seite\");'></td>\n";
	}
	echo "<td><input title='".$Text[21]."' class='Schalter_Element' name='Navigation' value='x' type='submit' onclick='Satz_entfernen();'></td>\n";
	echo "<td><input title='".$Text[22]."' class='Schalter_Element' name='Navigation' value='*' type='submit' onclick='neuer_Satz();'></td>\n";
	echo "<td style='width: 10px;'></td>\n";
	echo "<td>".$Text[23].": <select class='Auswahl_Liste_Element' id='form_filter_laden' name='Form_Filter_laden' value='".$Form_Filter_laden."' onchange='speichern();document.forms.Einstellungen.submit();'>".$Form_Filterliste."</select></td>\n";
	echo "<td style='width: 10px;'></td>\n";
	echo "<td><input title='".$Text[24]."' class='Schalter_Element' name='Filter_dialog_oeffnen' type='button' value = '".$Text[24]."' onclick='Filter_Dialog_oeffnen();'></td>\n";
	echo "<td style='width: 10px;'></td>\n";
	echo "<td><input title='".$Text[25]."' class='Schalter_Element' name='Sortierung_dialog_oeffnen' type='button' value = '".$Text[25]."' onclick='Sortierung_Dialog_oeffnen();'></td>\n";
	echo "</tr></table>\n";
	echo "</tr></table></td>\n";
	echo "<input id='Saetze' type='hidden' value='".$Saetze."'>\n";
	echo "<input id='UForm' name='UForm' type='hidden' value='".$UForm."'>\n";
	echo "<input id='neu_laden' name='neu_laden' type='hidden' value='".$neu_laden."'>\n";
	echo "<input id='tabellenname' name='Tabellenname' type='hidden' value='".$Tabellenname."'>\n";
	echo "<input id='indexfeld' name='Indexfeld' type='hidden' value='".$Indexfeld."'>\n";
	echo "<input id='indexwert' name='Indexwert' type='hidden' value='".$line_Satz[$Indexfeld]."'>\n";
	echo "<input id='sortierungen' name='Sortierungen' type='hidden' value='".$Sortierungen."'>\n";
} else {
	echo "<td><a href='./Formular_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[3]."</a></td></tr></table>\n";
}
?>