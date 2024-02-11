<div id="nav_mobil" class="schaltfl_mobil">
<?php
	echo '<div id="schaltfl_1" style="width: 20%;" onclick="umschalten(1);" class="schaltfl_mobil">'.$Text[40].'</div>';
	echo '<div id="schaltfl_2" style="width: 15%;" onclick="umschalten(2);" class="schaltfl_mobil">'.$Text[112].'</div>'; 
	echo '<div id="schaltfl_3" style="width: 15%;" onclick="umschalten(3);" class="schaltfl_mobil">'.$Text[42].'</div>';
	echo '<div id="schaltfl_4" style="width: 30%;" onclick="umschalten(4);" class="schaltfl_mobil">'.$Text[33].'</div>';
	echo '<div id="schaltfl_5" style="width: 20%;" onclick="umschalten(5);" class="schaltfl_mobil">'.$Text[105].'</div>';
?>
</div>

<div class="bereich_mobil" name="Dokument_nav" id="dokument_nav">
<table class='Text_einfach' cellpadding='5'><tr>
<?php
	if($anzeigen == 1) {
	   $query = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			$Kombifeld = "<br>".$Text[149].":&nbsp;<select name='Timestamp1' id='timestamp1' onchange='Vers_zeigen();' value='".$Zeitstempel."'><option></option>";
			while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				if($line1["Timestamp"] == $Timestamp) {
					$Kombifeld = $Kombifeld."<option selected>".$line1["Timestamp"]."</option>";
				} else {
					$Kombifeld = $Kombifeld."<option>".$line1["Timestamp"]."</option>";
				}
			}
			$Kombifeld = $Kombifeld."</select>";
		}
		mysqli_stmt_close($stmt1);
		mysqli_close($db);
		if($Zeitstempel == "") {
			echo "<td><table><tr><td align='right'>".$Text[40].": <input type='text' name='Bezeichnung' value='".$line["Bezeichnung"]."' style='width: 193px;'></td>";
			echo '<td align="left"; style="vertical-align: bottom; width: 80px;"><input type="submit" name="Aktion", id="speichern_schalter" value="'.$Text[108].'" onclick="speichern();"></td></tr>';
			echo "<td align='right'>".$Kombifeld."</td></tr></table></td>";
			echo "<td><a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."'>".$Text[4]."</a><br><br><a href='./kopieren.php?original=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[5]."</a><br><br><a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='Hauptrahmen'>".$Text[3]."</a></td>";
		}
		if($geloescht == 1) {
				echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[101]."</a></td>";
		}
   }
	if($Abfrage == "Baumhistorie") {
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[103]."</a>&nbsp;&nbsp;&nbsp;</td>";
		echo "<td><a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[102]."</a>&nbsp;&nbsp;&nbsp;</td>";
	}
?>
</table>
</div>

<div class="bereich_mobil" name="Spalte_Zeilen_nav" id="spalte_zeilen_nav">
<table class='Text_einfach' cellpadding='5'><tr style="height: 50px;">
<?php
	echo "<td><input type='button' value='".$Text[114]."' onclick='Menu(\"".$Text[114]."\");'></td>";
	echo "<td><input type='button' value='".$Text[148]."' onclick='Menu(\"".$Text[148]."\");'></td>";
	echo "<td><input type='button' value='".$Text[147]."' onclick='Menu(\"".$Text[147]."\");'></td>";
	echo "<td><input type='button' value='".$Text[150]."' onclick='Zellen_verbinden();'></td>";
	echo "<td><input type='button' value='".$Text[151]."' onclick='Zellverbindung_aufheben();'></td>";
?>
</tr></table>
</div>

<div class="bereich_mobil" name="Format_nav" id="format_nav">
<table style="width: 100%;" class='Text_einfach' cellpadding='3px'><tr style="height: 50px;">
<?php
	echo "<td><input type='button' value='".$Text[115]."' onclick='Menu(\"".$Text[115]."\");'></td>";
	echo "<td><input type='button' value='".$Text[20]."' onclick='Menu(\"".$Text[20]."\");'></td>";
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
	echo "<td><input type='button' value='".$Text[159]."' onclick='filterschaltung();'></td>";
	echo "<td><input type='button' value='".$Text[158]."' onclick='kommentar();'></td>";
	echo "<td><input type='button' value='".$Text[39]."' onclick='Hilfe_Fenster(\"46\");'></td>";
?>
</tr></table>
</div>