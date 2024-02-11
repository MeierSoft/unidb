<div class="schaltfl_mobil">
	<div class="schaltfl_mobil" style="width: 800px;">
<?php
		echo '<div id="schaltfl_1" style="width: 20%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[40].'</div>';
		echo '<div id="schaltfl_2" style="width: 20%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[41].'</div>';
		echo '<div id="schaltfl_3" style="width: 20%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[42].'</div>';
		echo '<div id="schaltfl_4" style="width: 20%;" onclick="umschalten(4);" class="schaltfl_mobil">'.$Text[33].'</div>';
		echo '<div id="schaltfl_5" style="width: 20%;" onclick="umschalten(5);" class="schaltfl_mobil">'.$Text[106].'</div>';
?>
	</div>
</div>

<div class="bereich_mobil" name="Dokument_nav" id="dokument_nav">
<table class='Text_einfach' cellpadding='5'><tr style="height: 50px;">

<?php
	echo "<td><div onclick='neu_berechnen();'><a href='#'><input type='button' name='berechnen' value='".$Text[2]."'></a></div></td>";
	if($anzeigen == 1) {
		if($geloescht == 1) {
			echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[101]."</a></td>";
		} else {
			if ($Abfrage == "Baum"){
				echo "<td><a href='./loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[3]."</a></td>";
   		 	echo "<td><a href='./verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a></td>";
    			echo "<td><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[5]."</a></td>";
    		}
   		echo "<td>".$Text[6].":<input type='text' name='Bezeichnung' value='".$line["Bezeichnung"]."'></td>";
   		if ($Abfrage == "Baum"){echo "<td><a href='#'><input type='submit' name='Aktion' value='".$Text[7]."' onclick='schreiben()'></a></td>";}
   	}
   }
   $abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
	$stmt1 = mysqli_prepare($db,$abfrage);
	mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
	mysqli_stmt_execute($stmt1);
	$result1 = mysqli_stmt_get_result($stmt1);
	if(mysqli_num_rows($result1) > 0) {
		echo "<td>Version vom:&nbsp;<select name='Timestamp' id='timestamp' onchange='Vers_zeigen();'><option></option>";
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
	mysqli_close($db);
	if($Abfrage == "Baumhistorie") {
		//echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[103].".</a>&nbsp;&nbsp;&nbsp;</td>";
		//echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[102]."</a>&nbsp;&nbsp;&nbsp;</td>";
	}
?>
</tr></table>
</div>

<div class="bereich_mobil" name="Spalte_Zeilen_nav" id="spalte_zeilen_nav">
<table class='Text_einfach' cellpadding='5'><tr style="height: 50px;">
<?php
	echo "<td><div onclick='Spalte_einfuegen(\"after\");'><a href='#'>".$Text[8]."</a></div></td>";
   echo "<td><div onclick='Spalte_einfuegen(\"before\");'><a href='#'>".$Text[9]."</a></div></td>";
   echo "<td><div onclick='Spalte_entfernen();'><a href='#'>".$Text[10]."</a></div></td>";
   echo "<td><div onclick='Zeile_einfuegen(\"before\");'><a href='#'>".$Text[11]."</a></div></td>";
   echo "<td><div onclick='Zeile_einfuegen(\"after\");'><a href='#'>".$Text[12]."</a></div></td>";
   echo "<td><div onclick='Zeile_entfernen();'><a href='#'>".$Text[13]."</a></div></td>";
?>
</tr></table>
</div>

<div class="bereich_mobil" name="Format_nav" id="format_nav">
<table class='Text_einfach' cellpadding='3px'><tr style="height: 50px;">
<?php
	echo "<td style='border-bottom: 1px; border-bottom-style: solid;'><b>Schrift:</b></td><td style='border-bottom: 1px; border-bottom-style: solid;' colspan='10'><table cellpadding='5px' width='100%'><tr><td align='right'>".$Text[14].":</td><td align='left'><div onchange='Zelle_Hintergrund_Farbe();'><input class='jscolor' name='Farbauswahl_Hintergrund' value='#FFFFFF' style='width:40px;'></div></td>";
  	echo "<td align='right'>".$Text[15].":</td><td align='left'><div onchange='Zelle_Schriftfarbe();'><input class='jscolor' name='Farbauswahl_Schrift' value='#000000' style='width:40px;'></div></td>";
  	echo "<td><div onclick='formatieren(\"font-weight:\",\"bold\");'><a href='#'>".$Text[16]."</a></div></td>";
  	echo "<td><div onclick='formatieren(\"font-weight:\",\"regular\");'><a href='#'>".$Text[17]."</a></div></td>";
  	echo "<td><div onclick='formatieren(\"font-style:\",\"italic\");'><a href='#'>".$Text[18]."</a></div></td>";
  	echo "<td><div onclick='formatieren(\"font-style:\",\"regular\");'><a href='#'>".$Text[19]."</a></div></td>";
	echo "</td></tr></table></tr><tr style='height: 50px;'>";

	echo "<td><b>Rahmen:</b></td><td><div onclick='Rahmen(\"borderPlacement\",\"none\");'><a href='#'>".$Text[21]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"all\");'><a href='#'>".$Text[22]."</a></</td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"top\");'><a href='#'>".$Text[23]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"right\");'><a href='#'>".$Text[24]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"bottom\");'><a href='#'>".$Text[25]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"left\");'><a href='#'>".$Text[26]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"inner\");'><a href='#'>".$Text[27]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"outer\");'><a href='#'>".$Text[28]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"horizontal\");'><a href='#'>".$Text[29]."</a></div></td>";
	echo "<td><div onclick='Rahmen(\"borderPlacement\",\"vertical\");'><a href='#'>".$Text[30]."</a></div></td>";
  	echo "<td><div onclick='Rahmen(\"borderColor\",\"#f97848\");'><a href='#'>".$Text[31]."</a></div></td>";
  	echo "<td><div onclick='Rahmen(\"borderSize\",\"3\");'><a href='#'>".$Text[32]."</a></div></td>";
?>
</tr></table>
</div>

<div class="bereich_mobil" name="DH_Funktionen_nav" id="dh_funktionen_nav">
<table class='Text_einfach' cellpadding='5'><tr style="height: 50px;">
<?php
	echo "<td><div onclick='Tag_suchen_Dialog();'><a href='#'>".$Text[34]."</a></div></td>";
	echo "<td><div onclick='aktueller_Wert_Dialog();'><a href='#'>".$Text[35]."</a></div></td>";
	echo "<td><div onclick='Archivwert_Dialog();'><a href='#'>".$Text[36]."</a></div></td>";
	echo "<td><div onclick='Mittelwert_Dialog();'><a href='#'>".$Text[37]."</a></div></td>";
	echo "<td><div onclick='Archivwerte_Werte_Dialog();'><a href='#'>".$Text[38]."</a></div></td>";
?>
</tr></table>
</div>

<div class="bereich_mobil" name="Sonstiges_nav" id="sonstiges_nav">
<table class='Text_einfach' cellpadding='5'><tr style="height: 50px;">
<?php
	echo "<td><div onclick='Hilfe_Fenster(\"16\");'><a href='#'>".$Text[39]."</a></div></td>";
?>
</tr></table>
</div>