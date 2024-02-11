<?php
	if(Berechtigung($Baum_ID, $Server_ID) == 1) {
		if ($Timestamp == "") {
			if ($line["geloescht"] != 1) {
				echo "<table><tr><td align='right'><input name='bearbeiten' value='".$Text[1]."' type='button' onclick='Element_bauen(this);'></td><td align='right'><input name='bearbeiten' value='".$Text[2]."' type='button' onclick='Dialog_oeffnen();'></td><td align='left'><input name='bearbeiten' value='".$Text[3]."' type='button' onclick='entfernen();'></td><td>".$Text[4].": </td><td><input name='Bezeichnung' value='".$Bezeichnung."' type='text'></td><td><input name='Aktion1' value='".$Text[5]."' type='submit' onclick='speichern();'></td><td><input name='Hilfe' value='".$Text[6]."' type='button' onclick=\"Hilfe_Fenster('18');\";></td>";
			} else {
				if ($mobil==1){
					echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='_self'>".$Text[11]."</a>&nbsp;&nbsp;&nbsp;</td>";
				} else {
					echo "<td><a href='Baum2.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Aktion=wiederherstellen' target='Baum'>".$Text[11]."</a>&nbsp;&nbsp;&nbsp;</td>";
				}
			}
		} else {
			echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"wiederherstellen\");'>".$Text[9]."</a></td>";
			echo "<td>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='Vers_wiederherstellen(\"loeschen\");'>".$Text[10]."</a></td>";
		}
	}
	if($line["geloescht"] != 1) {
		$abfrage = "SELECT `Hist_ID`, `Timestamp` FROM `Baumhistorie` WHERE `Baum_ID` = ? AND `Server_ID` = ? ORDER BY `Timestamp` DESC;";
		$stmt1 = mysqli_prepare($db,$abfrage);
		mysqli_stmt_bind_param($stmt1, "ii", $Baum_ID, $Server_ID);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		if(mysqli_num_rows($result1) > 0) {
			echo "<td>&nbsp;&nbsp;&nbsp;".$Text[8].": </td><td><select name='Timestamp' id='timestamp' onchange='document.forms[\"phpform\"].submit();'><option></option>";
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
	echo "</tr></table></font>";
?>