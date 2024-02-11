<?php
echo "<font size='2'><table class='Text_einfach'>";
echo "<tr><td>".$Text[9]."</td><td width='80px' align='center'>".$Text[10]."</td><td>".$Text[13]."</td><td>".$Text[14]."</td></td><td>".$Text[15]."</td></tr></font>";
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
echo "<td><input class='Text_Element' id = 'zeitstpl' name='Ende' value= '".$Ende."' type='text' size='15' maxlength='31'></td>";
echo "<td><input class='Schalter_Element' value='<' type='submit' name='schieben'></td>";
echo "<td><input class='Schalter_Element' value='>' type='submit' name='schieben'></td>";
echo "<td><input class='Schalter_Element' value='".$Text[7]."' type='submit' name='schieben'></td>";

echo "<td width='100px' align='center'><input class='Schalter_Element' value='".$Text[17]."' type='submit' name='Aktion'></td>";
echo "<td width='100px' align='center'><input class='Schalter_Element' value='".$Text[34]."' type='button' name='Skalen_einstellen' onclick='skalen_einstellen();'></td>";
echo "<td width='60px' align='center'><a href='javascript:void(0);' onclick=\"Hilfe_Fenster('13');\">".$Text[19]."</a></td>";
//echo "<td width = '40'></td><td><a href='./JS_Trend.php?Tag_ID=".$Tag_ID."&Baum_ID=".$Baum_ID."'>JS Trend</a></td></tr></table>";
echo "</table>";
?>