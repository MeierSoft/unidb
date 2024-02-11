<?php
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
		echo "<table class='Text_einfach' style='background: #FCEDD9; height: 50px;'><tr><td>".$Text[1].$Versionsauswahl1."</td>";
		if ($Timestamp == null) {
			echo "<td></td><td></td><td></td><td></td><td>".$Text[2]."</td><td></td><td>".$Text[3]."</td><td></td><td></td><td></td><td></td><td>".$Text[4]."</td><td></td><td>".$Text[5]."</td></tr><tr>\n";
		} else {
			echo "<td></td><td></td><td>".$Text[5]."</td></tr><tr>\n";
		}
		if($line["geloescht"] != 1) {
			echo $Versionsauswahl;			
		} else {
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[85]."</a>&nbsp;&nbsp;&nbsp;</td>";
		}
		if ($Timestamp == null) {
			echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[6]."' type='submit' onclick='abspeichern();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bild_einstellen' value='".$Text[7]."' type='button' onclick='Bild_Dialog_oeffnen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[74]."' type='button' onclick='bed_Format_Dialog();'></td>\n";
			echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
			echo "<td width='20px'></td>\n";
			echo "<td><input class='Text_Element' name='Raster' value='10' type='Text'  size='3'></td>\n";
			echo "<td width='20px'></td>\n";
			echo "<td><input class='Schalter_Element' name='erstellen' value='".$Text[8]."' type='button' onclick='Elementtyp_aussuchen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[9]."' type='button' onclick='Element_entfernen();'></td>\n";
			echo "<td><input class='Schalter_Element' name='kopieren' value='".$Text[10]."' type='button' onclick='Element_kopieren();'></td>\n";
			echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[7]."' type='button' onclick='Element_Dialog_oeffnen();'></td>\n";
			echo "<td width = '20px'> </td>\n";
			echo "<td><input class='Schalter_Element' name='Baustein' value='".$Text[11]."' type='button' onclick='Baustein_aussuchen();'></td>\n";
		} else {
			echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[83]."</a></td>";
			echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[84]."</a></td>";
		}
	}
	echo "<td width = '20px'> </td>\n";
	echo "<td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('17');\"></td>\n";
	echo "</tr></table>\n";
?>