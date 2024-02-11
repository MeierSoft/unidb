<?php
echo "</form>";
echo "<form id='Einstellungen' name='Einstellungen' action='./Armaturenbrett.php' method='post' target='_self'>";
echo "<input type='hidden' id='Intervall' name='Intervall' value= '".$Intervall."'>";
echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>";
$bed_Format = html_entity_decode($line["bed_Format"]);
echo "<input id='bed_Format' name='Bed_Format' type='hidden' value='".$bed_Format."'>";
if ($Zeitstempel!=$Text[1]){
	$Zeitpunkt=strtotime($Zeitstempel);
	}else{
	$Zeitpunkt=$Text[1];
}
$_SESSION['Zeitpunkt'] = $Zeitpunkt;
$_SESSION['Zeitstempel'] = $Zeitstempel;
echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>";
echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>";
echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
echo "<input type = 'hidden' id = 'zeitspanne' value= '".$Spanne."'>";
if ($Pos_Regler_alt == ""){$Pos_Regler_alt = "100";}
echo "<input type = 'hidden' id = 'pos_Regler_alt' name = 'Pos_Regler_alt' value= '".$Pos_Regler_alt."'>";

$Karte1 = "";
if ($Spanne==3600){
		$Karte1 = $Karte1."<option selected>1 h</option>";
	}else{
		$Karte1 = $Karte1."<option>1 h</option>";
	}
if ($Spanne==14400){
		$Karte1 = $Karte1."<option selected>4 h</option>";
	}else{
		$Karte1 = $Karte1."<option>4 h</option>";
	}
if ($Spanne==28800){
		$Karte1 = $Karte1."<option selected>8 h</option>";
	}else{
		$Karte1 = $Karte1."<option>8 h</option>";
	}
if ($Spanne==86400){
		$Karte1 = $Karte1."<option selected>".$Text[12]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[12]."</option>";
	}
if ($Spanne==172800){
		$Karte1 = $Karte1."<option selected>".$Text[13]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[13]."</option>";
	}
if ($Spanne==604800){
		$Karte1 = $Karte1."<option selected>".$Text[14]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[14]."</option>";
	}
if ($Spanne==1209600){
		$Karte1 = $Karte1."<option selected>".$Text[15]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[15]."</option>";
	}
if ($Spanne==2592000){
		$Karte1 = $Karte1."<option selected>".$Text[16]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[16]."</option>";
	}
if ($Spanne==31536000){
		$Karte1 = $Karte1."<option selected>".$Text[17]."</option>";
	}else{
		$Karte1 = $Karte1."<option>".$Text[17]."</option>";
	}
$Karte1 = $Karte1."</select></td>";
$Karte1 = $Karte1."<td><input class='Text_Element' id='Zeitstempel' name='Zeitstempel' value= '".$Zeitstempel."' type='text' size='15' maxlength='31' onchange='lesen();'></td>";
$Karte1 = $Karte1."<td align='center'><input class='Schalter_fett_Element' value='<' id='zurueckg' type='button' name='schieben' onclick='Zeitschieben(\"<\");'></td>";
$Karte1 = $Karte1."<td align='center'><input class='Schalter_fett_Element' value='>' id='vorg' type='button' name='schieben' onclick='Zeitschieben(\">\");'></td>";
$Karte1 = $Karte1."<td align='center'><input class='Schalter_Element' value='".$Text[1]."' id='aktuelleZeit' type='button' name='schieben' onclick='Zeitschieben(\"".$Text[1]."\")';></td>";
$Karte1 = $Karte1."</tr><tr><td>&nbsp;</td></tr>";

if ($Pos_Regler == ""){$Pos_Regler = "100";}
$Karte2 = "<input name = 'Pos_Regler' type='range' min='0' max='100' value='".$Pos_Regler."' class='slider' id='schieberegler' onclick='Regler_schieben();'>";
$Karte2 = $Karte2."</tr><tr><td>&nbsp;</td></tr>";

$Karte3 = "";
//echo "<td align='center'>".$Text[18]."</td><td align='center'>".$Text[2]."</td><td>".$Text[3]."</td></td><td>".$Text[4]."</td><td></td><td>".$Text[5]."</td><td></td>";
$Karte3 = $Karte3."<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('5');\">".$Text[6]."</a></td>";
$Karte3 = $Karte3."<input type='hidden' id='baum_id' name='Baum_ID' value = '".$Baum_ID."'>";
$Karte3 = $Karte3."<input type='hidden' id='server_id' name='Server_ID' value = '".$Server_ID."'>";
$Karte3 = $Karte3."<input type='hidden' id='gel' name='geloescht' value = '".$line["geloescht"]."'>";
$Karte3 = $Karte3."<input type='hidden' id='hintergrundfarbe' name='Hintergrundfarbe' value = '".$Hintergrundfarbe."'>";
$Karte3 = $Karte3."<input type='hidden' id='aktion' name='Aktion' value = ''>";
$Karte3 = $Karte3."<input type='hidden' id='timestamp' name='Timestamp' value = '".$Timestamp."'>";
if($anzeigen == 1) {
	if($line["geloescht"] != 1) {$Karte3 = $Karte3."<td><a href='Armaturenbrett_bauen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[9]."</a></td>";}
}
$Karte3 = $Karte3."<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[8]."</a></td>";
$Karte3 = $Karte3."<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[10]."</a></td>";
$Karte3 = $Karte3."<td><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[7]."</a></td>";
if($line["geloescht"] == 1) {$Karte3 = $Karte3."<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>wiederherstellen</a></td>";}
$Karte3 = $Karte3."</tr><tr><td>&nbsp;</td>";
?>


<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[20].'</div>';
	echo '<div id="schaltfl_2" style="width: 34%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[21].'</div>';
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[22].'</div>';

echo '</div>';
echo '<div class="bereich_mobil" name="Zeit_einstellen" id="zeit_einstellen">';
	echo '<table width="100%" style="padding-top: 15px;">';
		echo '<tr>';
			echo '<td align="center">'.$Text[18].'</td>';
			echo '<td align="center">'.$Text[2].'</td>';
			echo '<td align="center">'.$Text[3].'</td>';
			echo '<td align="center">'.$Text[4].'</td>';
?>
			<td></td>
		</tr>
		<tr>
			<td><select class='Auswahl_Liste_Element' id='Spanne' name='Spanne' size='1'>
			<?php echo $Karte1;?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Zeit_schieben" id="zeit_schieben">
	<table width="100%" style="padding-top: 15px;">
		<tr>
			<?php echo '<td>'.$Text[21].'</td>';?>
		</tr>
		<tr>
			<td>
				<div class="slidecontainer">
					<?php echo $Karte2;?>
					</div>
			</td>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Links" id="links">
	<table style="padding-top: 15px;" width="100%">
		<tr>
			<?php echo $Karte3; ?>
		</tr>
	</table>
</div>
</form></font>





