<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[3].'</div>';
	echo '<div id="schaltfl_2" style="width: 34%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[5].'</div>';
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[89].'</div>';
?>
</div>

<?php
	echo '<div class="bereich_mobil" name="Formular" id="formular">';
	echo "<table width='100%'>\n";
	if($geloescht == 1) {
		echo "<tr style='vertical-align: middle; height: 40px;'><td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[52]."</a></td>";
	} else {
		echo "<input id='aktion' name='Aktion' value='' type='hidden'>\n";
		echo "<tr style='vertical-align: middle; height: 40px;'>\n";
		if ($Timestamp > ""){
			echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[50]."</a></td>";
			echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[51]."</a></td>";
		} else {
			echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[7]."' type='submit' onclick='abspeichern();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Formular_einstellen' value='".$Text[8]."' type='button' onclick='Formular_Dialog_oeffnen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Navigationsdialog' value='Navi' type='button' onclick='Navi_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[41]."' type='button' onclick='bed_Format_Dialog();'></td>\n";
		}
	}
	echo "</tr></table>\n";
	echo '</div>';

	echo '<div class="bereich_mobil" name="Elemente" id="elemente">';
	if ($Timestamp == ""){
		echo "<table width='100%'><tr style='vertical-align: middle; height: 40px;'>";
		echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[8]."' type='button' onclick='Element_Dialog_oeffnen();'></td>\n";
		echo "<td><input class='Schalter_Element' name='erstellen' value='".$Text[9]."' type='button' onclick='Elementtyp_aussuchen();'></td>\n";
		echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[10]."' type='button' onclick='Element_entfernen();'></td>\n";
		echo "<td><input class='Schalter_Element' name='kopieren' value='".$Text[11]."' type='button' onclick='Element_kopieren();'></td>\n";
		echo "</tr></table>\n";
	}
	echo '</div>';
	
	echo '<div class="bereich_mobil" name="Sonstiges" id="sonstiges">';
	echo "<table width='100%'>";
	echo "<tr style='vertical-align: middle; height: 40px;'><td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('32');\"></td>\n";
	if ($Timestamp == ""){
		echo "<td>".$Text[4].":&nbsp;<input class='Text_Element' name='Raster' value='0' type='Text' size='3'></td>\n";
	}
	echo $Versionsliste;
	echo "</tr></table>\n";
	echo "</div>";
?>



