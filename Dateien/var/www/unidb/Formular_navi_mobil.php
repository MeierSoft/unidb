<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 30%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[40].'</div>';
	echo '<div id="schaltfl_2" style="width: 44%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[40].'</div>';
	echo '<div id="schaltfl_3" style="width: 26%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[39].'</div>';
?>
</div>

<?php

if($Navigationsbereich == 1) {
	$Karte1 = "<table width='100%'><tr style='vertical-align: middle; height: 40px;'>\n";
	if($Darstellung == "Formular" or $Darstellung == $Text[16]) {
		$Schalter = "<td><input title='".$Text[8]."' class='Schalter_Element' name='Ansicht' id='ansicht' value='".$Text[9]."' type='button' onclick='Ansicht_umschalten();'></td>\n";
		$Karte1 = $Karte1."<td><input type='button' class='Schalter_Element' value='".$Text[10]."' onclick='speichern();'></td>\n";
		$Karte1 = $Karte1."<td style='width: 10px;'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[11]."' class='Schalter_Element' id='nav_Anfang' name='Navigation' value='|<' type='button' onclick='Nav_Anfang(\"Satz\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[12]."' class='Schalter_Element' name='Navigation' value='<' type='button' onclick='Nav_zurueck(\"Satz\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[13]."' class='Text_Element' id='nav_Satz' name='Navigation_Satz' size='3' value='".$Navigation_Satz."' type='Text' onchange='document.forms[\"Einstellungen\"].submit();'>&nbsp;/&nbsp;".$Saetze."</td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[14]."' class='Schalter_Element' name='Navigation' value='>' type='button' onclick='Nav_weiter(\"Satz\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[15]."' class='Schalter_Element' name='Navigation' value='>|' type='button' onclick='Nav_Ende(\"Satz\");'></td>\n";
	} else {
		$Schalter = "<td><input title='".$Text[8]."' class='Schalter_Element' name='Ansicht' id='ansicht' value='".$Text[16]."' type='button' onclick='Ansicht_umschalten();'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[17]."' class='Schalter_Element' name='Navigation' value='|<' type='submit' onclick='Nav_Anfang(\"Seite\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[18]."' class='Schalter_Element' name='Navigation' value='<' type='submit' onclick='Nav_zurueck(\"Seite\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[13]."' class='Text_Element' id='nav_Satz' name='Navigation_Satz' size='3' value='".$Navigation_Satz."' type='Text' onchange='document.forms[\"Einstellungen\"].submit();'> / <span id='saetze'>".$Saetze."</span></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[19]."' class='Schalter_Element' name='Navigation' value='>' type='submit' onclick='Nav_weiter(\"Seite\");'></td>\n";
		$Karte1 = $Karte1."<td><input title='".$Text[20]."' class='Schalter_Element' name='Navigation' value='>|' type='submit' onclick='Nav_Ende(\"Seite\");'></td>\n";
	}
	$Karte1 = $Karte1."<td><input title='".$Text[21]."' class='Schalter_Element' name='Navigation' value='x' type='submit' onclick='Satz_entfernen();'></td>\n";
	$Karte1 = $Karte1."<td><input title='".$Text[22]."' class='Schalter_Element' name='Navigation' value='*' type='submit' onclick='neuer_Satz();'></td>\n";
	$Karte1 = $Karte1."</tr></table>";
}

$Karte2 = "<table width='100%'><tr style='vertical-align: middle; height: 40px;'>";
if($Navigationsbereich == 1) {
	$Karte2 = $Karte2."<td>".$Text[23].": <select class='Auswahl_Liste_Element' id='form_filter_laden' name='Form_Filter_laden' value='".$Form_Filter_laden."' onchange='speichern();document.forms.Einstellungen.submit();'>".$Form_Filterliste."</select></td>\n";
	$Karte2 = $Karte2."<td><input title='".$Text[24]."' class='Schalter_Element' name='Filter_dialog_oeffnen' type='button' value = '".$Text[24]."' onclick='Filter_Dialog_oeffnen();'></td>\n";
	$Karte2 = $Karte2."<td><input title='".$Text[25]."' class='Schalter_Element' name='Sortierung_dialog_oeffnen' type='button' value = '".$Text[25]."' onclick='Sortierung_Dialog_oeffnen();'></td>\n";
	$Karte2 = $Karte2."<input id='Saetze' type='hidden' value='".$Saetze."'>\n";
	$Karte2 = $Karte2."<input id='UForm' name='UForm' type='hidden' value='".$UForm."'>\n";
	$Karte2 = $Karte2."<input id='neu_laden' name='neu_laden' type='hidden' value='".$neu_laden."'>\n";
	$Karte2 = $Karte2."<input id='tabellenname' name='Tabellenname' type='hidden' value='".$Tabellenname."'>\n";
	$Karte2 = $Karte2."<input id='indexfeld' name='Indexfeld' type='hidden' value='".$Indexfeld."'>\n";
	$Karte2 = $Karte2."<input id='indexwert' name='Indexwert' type='hidden' value='".$line_Satz[$Indexfeld]."'>\n";
	$Karte2 = $Karte2."<input id='sortierungen' name='Sortierungen' type='hidden' value='".$Sortierungen."'>\n";
} else {
	$Karte2 = $Karte2."<td><a href='./Formular_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[3]."</a></td>\n";
}
$Karte2 = $Karte2.$Schalter;
$Karte2 = $Karte2."</tr></table>";

echo '<div class="bereich_mobil" name="Navigation" id="navigation">';
echo $Karte1;
echo '</div>';

echo '<div class="bereich_mobil" name="Filter_Sortierung" id="filter_sortierung">';
echo $Karte2;
echo '</div>';

echo '<div class="bereich_mobil" name="Sonstiges" id="sonstiges">';
if($UForm != 1) {
	echo "<table width='100%' cellpadding = '5px'><tr style='vertical-align: middle; height: 40px;'>";
	if($anzeigen == 1) {
		echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('31');\">".$Text[1]."</a></td>";
		echo "<td><a href='./loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[2]."</a></td>";
  	 	echo "<td><a href='./verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[7]."</a></td>";
  	 	echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a></td>";
  		echo "<td><a href='Formular_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></td>";
	}
	echo "</tr></table>";
}
echo "</div>";
?>


