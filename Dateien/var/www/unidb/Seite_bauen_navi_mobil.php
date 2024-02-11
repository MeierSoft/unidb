<?php
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
?>

<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[15].'</div>';
	echo '<div id="schaltfl_2" style="width: 34%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[14].'</div>';
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[13].'</div>';
?>
</div>
<div class="bereich_mobil" name="Seite" id="seite">
	<table width="100%">
		<tr>
		<?php
			if ($Timestamp == "") {
				if ($line["geloescht"] != 1) {
					echo "<td>".$Text[4].": </td></tr><tr>";
					echo "<td><input name='Bezeichnung' value='".$Bezeichnung."' type='text'></td><td><input name='Aktion1' value='".$Text[5]."' type='submit' onclick='speichern();'></td>";
				} else {
					echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[11]."</a></td>";
				}
			} else {
				echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[9]."</a></td>";
				echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[10]."</a></td>";
			}
				
	?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Elemente" id="elemente">
	<table width="100%">
		<tr height="40px;">
			<?php
				if ($Timestamp == "") {
					if ($line["geloescht"] != 1) {
						echo "<td align='center'><input name='bearbeiten' value='".$Text[1]."' type='button' onclick='Element_bauen(this);'></td><td align='center'><input name='bearbeiten' value='".$Text[2]."' type='button' onclick='Dialog_oeffnen();'></td><td align='center'><input name='bearbeiten' value='".$Text[3]."' type='button' onclick='entfernen();'></td>";
					}
				}
			?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Sonstiges" id="sonstiges">
	<table style="padding-top: 15px;" width="100%">
		<tr>
		<?php
			echo "<td><input name='Hilfe' value='".$Text[6]."' type='button' onclick=\"Hilfe_Fenster('18');\";></td>";
			if ($line["geloescht"] != 1) {
				$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
				$stmt1 = mysqli_prepare($db,$abfrage);
				mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
				mysqli_stmt_execute($stmt1);
				$result1 = mysqli_stmt_get_result($stmt1);
				if(mysqli_num_rows($result1) > 0) {
					echo "<td>".$Text[8].":&nbsp;<select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
					while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
						if($line1["Timestamp"] == $Timestamp) {
							echo "<option selected>".$line1["Timestamp"]."</option>";
						} else {
							echo "<option>".$line1["Timestamp"]."</option>";
						}
					}
					echo "</select></td>";
				}
				mysqli_stmt_close($stmt1);
			}
		?>
		</tr>
	</table>
</div>
</tr></table></font>
<?php } ?>




