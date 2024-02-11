<div class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 33%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[27].'</div>';
	echo '<div id="schaltfl_3" style="width: 33%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[26].'</div>';
?>
</div>
<div class="bereich_mobil" name="Zeit_einstellen" id="zeit_einstellen">
	<table width="100%" style="padding-top: 5px;">
		<tr>
			<?php
				echo "<td>".$Text[9]."</td><td>".$Text[10]."</td><td>".$Text[29]."</td></tr>\n";
				echo "<tr><td><select class='Auswahl_Liste_Element' id='Spanne' name='Spanne' size='1'>\n";
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
					echo "<option selected>".$Text[1]."</option>";
				}else{
					echo "<option>".$Text[1]."</option>";
				}
				if ($Spanne==172800){
					echo "<option selected>".$Text[2]."</option>";
				}else{
					echo "<option>".$Text[2]."</option>";
				}
				if ($Spanne==604800){
					echo "<option selected>".$Text[3]."</option>";
				}else{
					echo "<option>".$Text[3]."</option>";
				}
				if ($Spanne==1209600){
					echo "<option selected>".$Text[4]."</option>";
				}else{
					echo "<option>".$Text[4]."</option>";
				}
				if ($Spanne==2592000){
					echo "<option selected>".$Text[5]."</option>";
				}else{
					echo "<option>".$Text[5]."</option>";
				}
				if ($Spanne==31536000){
					echo "<option selected>".$Text[6]."</option>";
				}else{
					echo "<option>".$Text[6]."</option>";
				}
				echo "</select></td>";
			
				if ($Zeitstempel!=$Text[1]){
					$Zeitpunkt=strtotime($Zeitstempel);
				}else{
					$Zeitpunkt=$Text[1];
				}
				$_SESSION['Zeitpunkt'] = $Zeitpunkt;
				$_SESSION['Zeitstempel'] = $Zeitstempel;
				echo "<td><select class='Auswahl_Liste_Element' name='Art' size='1'>";
				echo "<option selected>".$Text[16]."</option>";
				if ($Art=="hMinMax"){
					echo "<option selected>hMinMax</option>";
				}else{
					echo "<option>hMinMax</option>";
				}
				if ($Art=="dMinMax"){
					echo "<option selected>dMinMax</option>";
				}else{
					echo "<option>dMinMax</option>";
				}
				if ($Art=="hMW"){
					echo "<option selected>hMW</option>";
				}else{
					echo "<option>hMW</option>";
				}
				if ($Art=="hMin"){
					echo "<option selected>hMin</option>";
				}else{
					echo "<option>hMin</option>";
				}if ($Art=="hMax"){
					echo "<option selected>hMax</option>";
				}else{
					echo "<option>hMax</option>";
				}if ($Art=="dMW"){
					echo "<option selected>dMW</option>";
				}else{
					echo "<option>dMW</option>";
				}if ($Art=="dMin"){
					echo "<option selected>dMin</option>";
				}else{
					echo "<option>dMin</option>";
				}if ($Art=="dMax"){
					echo "<option selected>dMax</option>";
				}else{
					echo "<option>dMax</option>";
				}
				echo "</select></td>";
				echo "<input name='Baum_ID' value='".$Baum_ID."' type='hidden'>\n";
				echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
				echo "<td><input class='Text_Element' id = 'zeitstpl' name='Ende' value= '".$Ende."' type='text' size='12' maxlength='20'></td>";
				echo "<td><input class='Schalter_fett_Element' value='<' type='submit' name='schieben'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='>' type='submit' name='schieben'></td>\n";
				echo "<td><input class='Schalter_fett_Element' value='".$Text[7]."' type='submit' name='schieben'></td>\n";
			?>
		</tr>
	</table>
</div>

<div class="bereich_mobil" align="center" name="Sonstiges" id="sonstiges">
	<table style="padding-top: 5px;" width="100%"><tr>
		<tr>
			<td></td>
		<?php
			echo "<td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('13');\">".$Text[19]."</a></td>";
			echo "<td><input class='Schalter_Element' value='".$Text[17]."' type='submit' name='Aktion'></td>";
			echo "<td width='100px' align='center'><input class='Schalter_Element' value='".$Text[34]."' type='button' name='Skalen_einstellen' onclick='skalen_einstellen();'></td>";
		?>
		</tr>
	</table>
</div>