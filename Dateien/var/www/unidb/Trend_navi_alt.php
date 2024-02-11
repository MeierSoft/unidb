<?php
echo "<font size='2'><table class='Text_einfach'>";
if ($Pos_Regler == ""){$Pos_Regler = "100";}
echo "<tr bgcolor='#EDFFC1'><td>".$Text[8].":</td><td colspan='9'><div class='slidecontainer'><input name = 'Pos_Regler' type='range' min='0' max='100' value='".$Pos_Regler."' class='slider' id='schieberegler' onclick='Regler_schieben();'></div></td></tr>\n";
echo "<tr><td>".$Text[9]."</td><td width='80px' align='center'>".$Text[10]."</td><td>min</td><td>max</td><td>".$Text[11]."</td><td>".$Text[12]."</td><td>".$Text[13]."</td><td>".$Text[14]."</td></td><td>".$Text[15]."</td></tr></font>";
echo "<tr><td><select class='Auswahl_Liste_Element' name='Spanne' size='1'>";
if ($Spanne==3600){
		echo "<option selected>1 h</option>";
	}else{
		echo "<option>1 h</option>";
	}
if ($Spanne==14400){
		echo "<option selected>4 h</option>";
	}else{
		echo "<option>4 h</option>";
	}
if ($Spanne==28800){
		echo "<option selected>8 h</option>";
	}else{
		echo "<option>8 h</option>";
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
echo "<td><input class='Text_Element' name='Smin' value= '".$Smin."' type='text' size='4' maxlength='7'></td>";
echo "<td width='50px'><input class='Text_Element' name='Smax' value= '".$Smax."' type='text' size='4' maxlength='7'></td>";
echo "<td><input class='Text_Element' value='".$Breite."' type='Text' name='Breite' size='4' maxlength='7'></td>";
echo "<td width='50px'><input class='Text_Element' value='".$Hoehe."' type='Text' name='Hoehe' size='4' maxlength='7'></td>";
echo "<td><input class='Text_Element' id = 'zeitstpl' name='Ende' value= '".$Ende."' type='text' size='13' maxlength='31'></td>";
echo "<td><input class='Schalter_Element' value='<' type='submit' name='schieben'></td>";
echo "<td><input class='Schalter_Element' value='>' type='submit' name='schieben'></td>";
echo "<td><input class='Schalter_Element' value='".$Text[7]."' type='submit' name='schieben'></td>";


echo "</tr></table>";
echo "<table><tr><td width='100px' align='left'><input class='Schalter_Element' value='".$Text[17]."' type='submit' name='Aktion'></td><td>".$Text[18].": <input value='1' type='checkbox' name='Legende' checked></td>";
echo "<td width = '40'></td><td><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('13');\">".$Text[19]."</a></td>";
echo "<td width = '40'></td><td><A href='./Tabelle.php?Tag_ID=".$Tag_ID."&akt=".$akt."&Start=".$Start."&Ende=".$Ende."&Tag=".$Tag."&Bezeichnung=".$Bezeichnung."&EUDESC=".$EUDESC."&Dezimalstellen=".$Dezimalstellen."&Breite=".$Breite."&Art=".$Art."&Bezeichnung=".$Bezeichnung."' target='_self'>".$Text[20]."</A></td>";
//echo "<td width = '40'></td><td><a href='./JS_Trend.php?Tag_ID=".$Tag_ID."&Baum_ID=".$Baum_ID."'>JS Trend</a></td></tr></table>";
echo "</table>";
?>