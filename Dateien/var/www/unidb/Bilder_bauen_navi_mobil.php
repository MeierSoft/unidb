<?php
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
?>

<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[1].'</div>';
	echo '<div id="schaltfl_2" style="width: 34%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[3].'</div>'; 
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[104].'</div>';
?>
</div>
<div class="bereich_mobil" name="Bild" id="bild">
	<table width="100%">
		<tr>
		<?php
			if ($Timestamp == null) {
				if($line["geloescht"] != 1) {
					echo "<td>Version</td></tr><tr>".$Versionsauswahl;
					echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[6]."' type='submit' onclick='abspeichern();'></td>";
					echo "<td><input class='Schalter_Element' name='Bild_einstellen' value='".$Text[7]."' type='button' onclick='Bild_Dialog_oeffnen();'></td>";
					echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[74]."' type='button' onclick='bed_Format_Dialog();'></td>";
					echo "<td><input class='Schalter_Element' name='JS_Code' value='Code' type='button' onclick='Code_Dialog();'></td>\n";
				} else {
					echo "<td>".$Text[5]."</td></tr><tr>";
					echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[83]."</a></td>";
					echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[84]."</a></td>";
				}
			} else {
				echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[85]."</a></td>";
			}
		?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Elemente" id="elemente">
	<table width="100%">
		<tr height="40px;">
			<?php
				if ($Timestamp == null) {
					echo "<td><input class='Schalter_Element' name='erstellen' value='".$Text[8]."' type='button' onclick='Elementtyp_aussuchen();'></td>";
					echo "<td><input class='Schalter_Element' name='entfernen' value='".$Text[9]."' type='button' onclick='Element_entfernen();'></td>";
					echo "<td><input class='Schalter_Element' name='kopieren' value='".$Text[10]."' type='button' onclick='Element_kopieren();'></td>";
					echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[7]."' type='button' onclick='Element_Dialog_oeffnen();'></td>";
					echo "<td><input class='Schalter_Element' name='Baustein' value='".$Text[11]."' type='button' onclick='Baustein_aussuchen();'></td>";
				}
			?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Sonstiges" id="sonstiges">
	<table style="padding-top: 15px;" width="100%">
		<tr>
		<?php
			if ($Timestamp == null) {
				echo "<td>".$Text[2]."&nbsp;<input class='Text_Element' name='Raster' value='10' type='Text'  size='3'></td>";
				echo "<td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('17');\"></td>";
				echo "<td>".$Text[4]."&nbsp;<input class='Schalter_Element' name='Baustein' value='".$Text[11]."' type='button' onclick='Baustein_aussuchen();'></td>";
			}
		?>
		</tr>
	</table>
</div>
<?php } ?>





