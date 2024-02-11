<?php
	echo "<table><tr><td>\n";
	echo "<table style='position: relative; height: 33px; background: #FCEDD9;' class='Text_einfach'>\n";
	echo "<tr><td width='2px'></td><td>".$Text[3]."</td>\n";
	$Versionen = 0;
	if($geloescht == 1) {
		echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[52]."</a></td>";
	} else {
		echo $Versionsliste;
		if ($Timestamp > ""){
			echo "<td width='20px'></td><td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[50]."</a></td>";
			echo "<td width='20px'></td><td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[51]."</a></td>";
			echo "<td width='2px'></td></tr></table>\n";
			echo "<input id='aktion' name='Aktion' value='' type='hidden'>\n";
		} else {
			echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[7]."' type='submit' onclick='abspeichern();'></td>\n";
			echo "<td width='100px'><input class='Schalter_Element' name='Formular_einstellen' value='".$Text[8]."' type='button' onclick='Formular_Dialog_oeffnen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Navigationsdialog' value='Navi' type='button' onclick='Navi_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[41]."' type='button' onclick='bed_Format_Dialog();'></td>\n";
			echo "<td width='2px'></td></tr></table>\n";
			echo "</td><td width='20px'></td><td>\n";
			echo "<table style='position: relative; height: 33px; background: #FCEDD9;' class='Text_einfach'><tr><td width='2px'></td><td>".$Text[4]."</td><td><input class='Text_Element' name='Raster' value='0' type='Text'  size='3'></td>\n";
			echo "<td width='2px'></td></tr></table>\n";
			echo "</td><td width='20px'></td><td>\n";
			echo "<table style='position: relative; height: 33px; background: #FCEDD9;' class='Text_einfach'><tr><td width='2px'></td><td>".$Text[5]."</td><td><input class='Schalter_Element' name='erstellen' value='".$Text[9]."' type='button' onclick='Elementtyp_aussuchen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[10]."' type='button' onclick='Element_entfernen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='kopieren' value='".$Text[11]."' type='button' onclick='Element_kopieren();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[8]."' type='button' onclick='Element_Dialog_oeffnen();'></td>\n";
			echo "<td width='2px'></td></tr></table>\n";
		}
	}
	echo "</td><td width='20px'></td><td>\n";
	echo "<table style='position: relative; height: 33px; background: #FCEDD9;' class='Text_einfach''>";
	echo "<td width='2px'></td><td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('32');\"></td>\n";
	echo "<td width='2px'></td></tr></table>\n";
	echo "</td></tr></table>\n";

?>