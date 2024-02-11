<?php
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
?>

<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[13].'</div>';
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
					echo "<td><input class='Schalter_Element' name='Aktion' value='".$Text[107]."' type='submit' onclick='abspeichern();'></td>";
					echo "<td><input class='Schalter_Element' name='Bild_einstellen' value='".$Text[7]."' type='button' onclick='Bild_Dialog_oeffnen();'></td>";
					echo "<td><input class='Schalter_Element' name='Bed_Format' value='".$Text[74]."' type='button' onclick='bed_Format_Dialog();'></td>";
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
					echo "<td><input class='Schalter_Element' name='Element_einstellen' value='".$Text[7]."' type='button' onclick='Element_Dialog_oeffnen();'></td>";
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
				echo "<td><input class='Schalter_Element' name='Hilfe' value='".$Text[12]."' type='button' onclick=\"Hilfe_Fenster('44');\"></td>";
				echo "<td align='right'>Version</td><td>".$Versionsauswahl;
			}
		?>
		</tr>
	</table>
</div>
<?php } ?>





