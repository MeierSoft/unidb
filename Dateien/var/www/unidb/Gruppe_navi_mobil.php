<?php
	echo "</form><form id='Einstellungen' name='Einstellungen' action='./Gruppe.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' method='post' target='_self'>\n";
?>


<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[21].'</div>';
	echo '<div id="schaltfl_2" style="width: 34%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[22].'</div>';
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[23].'</div>';
?>
</div>
<div class="bereich_mobil" name="Zeit_einstellen" id="zeit_einstellen">
	<table width="100%" style="padding-top: 5px;">
		<tr>
			<?php
				echo "<tr height='25px' style='vertical-align:bottom;'><td>".$Text[9]."</td><td>".$Text[10]."</td><td></td><td></td><td></td><td></td></tr>\n";
				echo "<tr height='30px' style='vertical-align:top;'><td><select class='Auswahl_Liste_Element' id='Spanne' name='Spanne' size='1'>\n";
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
				echo "<td><input class='Text_Element' id = 'zeitstpl' name='Zeitstempel' value= '".$Zeitstempel."' type='text' size='12' maxlength='20'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='<' type='submit' name='schieben'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='>' type='submit' name='schieben'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='".$Text[1]."' type='submit' name='schieben'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='".$Text[11]."' type='submit' name='Aktion'></td>";
			?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Zeit_schieben" id="zeit_schieben">
	<table width="100%" style="padding-top: 5px;">
		<tr>
			<td>
				<?php
					if ($Pos_Regler == ""){$Pos_Regler = "100";}
					echo "<tr height='35px' style='vertical-align:top;'><td><div class='slidecontainer'><input name = 'Pos_Regler' type='range' min='0' max='100' value='".$Pos_Regler."' class='slider' id='schieberegler' onclick='Regler_schieben();'></div></td></tr>\n";
				?>
			</td>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Links" id="links">
	<table style="padding-top: 5px;" width="100%">
		<tr>
			<?php
				echo "<table width='100%'><tr style='vertical-align:top; height: 25px;'><td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('7');\">".$Text[13]."</a></td>\n";
				if($anzeigen == 1) {
					if ($Timestamp == null) {
						if($line["geloescht"] != 1) {echo "<td><a href='Gruppe_editieren.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[14]."</a></td>";}
						if ($line["geloescht"] != 1) {
							echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[16]."</a></td>";
							echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[17]."</a></td>";
							echo "<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[15]."</a></td>";
							if($line["geloescht"] == 1) {echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";}
						} else {
							echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";
						}
					} else {
						echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>Aktuelle Version durch diese hier ersetzen.</a></td>";
						echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>Diese Version löschen.</a></td>";
					}
					if($line["geloescht"] != 1) {
						$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
						$stmt1 = mysqli_prepare($db,$abfrage);
						mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
						mysqli_stmt_execute($stmt1);
						$result1 = mysqli_stmt_get_result($stmt1);
						if(mysqli_num_rows($result1) > 0) {
							echo "</tr><tr style='vertical-align:top; height: 35px;'><td colspan='2' align='right'>Version vom:&nbsp;</td><td colspan='2'><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
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
				}
				echo "</tr>\n";
			?>
		</tr>
	</table>
</div>
<font size='4'><b><br>
<?php echo $Bezeichnung; ?>
</b>




