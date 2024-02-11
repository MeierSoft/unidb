<?php
function Meldung_schreiben($Interface, $Meldung, $Zielrechner) {
	include('../conf_DH_schreiben.php');
	if ($Zielrechner == "Standard") {
		$Knotenliste = array();
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Schnittstellen_ID.";";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$i = 0;
		while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$line["Einstellung_ID"].";";
			$stmt2 = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt2);
			$result2 = mysqli_stmt_get_result($stmt2);
			while($line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
				if ($line2["Parameter"] == "Script"){
					$Knotenliste[$i] = $line2["Zusatz"];
					$i = $i + 1;
				}
			}
			$i = 0;
			while ($i < count($Knotenliste)) {
				if ($Knotenliste[$i] != "") {
					$query = "INSERT INTO Meldungen (Rechner, Schnittstelle, Meldung) Values('".$Knotenliste[$i]."', '".$Interface."', '".$Meldung."');";
					$stmt3 = mysqli_prepare($dbDH, $query);
					mysqli_stmt_execute($stmt3);
					mysqli_stmt_close($stmt3);
					$i = $i + 1;
				}
			}
		}
		mysqli_stmt_close($stmt);
		mysqli_stmt_close($stmt2);
	} else {
		if ($Zielrechner == "Server") {
			//Kollektiv_ID ermitteln
			$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Kollektiv' AND `Eltern_ID` = 0;";
  			$stmt = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
  			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Kollektiv_ID = $line['Einstellung_ID'];
			mysqli_stmt_close($stmt);
			$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Kollektiv_ID.";";
			$stmt = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$query = "INSERT INTO Meldungen (Rechner, Schnittstelle, Meldung) Values('".$line['Parameter']."', '".$Interface."', '".$Meldung."');";
				$stmt3 = mysqli_prepare($dbDH, $query);
				mysqli_stmt_execute($stmt3);
				mysqli_stmt_close($stmt3);
			}
			mysqli_stmt_close($stmt);
		} else {
			$query = "INSERT INTO Meldungen (Rechner, Schnittstelle, Meldung) Values ('".$Zielrechner."', '".$Interface."', '".$Meldung."');";
			$stmt3 = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt3);
			mysqli_stmt_close($stmt3);
		}
	}
}

function Schnittstelle_Rechner($Schnittstelle) {
	include('../conf_DH.php');
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = 0 AND (`Parameter` = 'Schnittstellen' OR `Parameter` = 'außer Betrieb');";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$i = 0;
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		${"Eltern_ID".$i} = $line["Einstellung_ID"];
		$i = $i + 1;
	}
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE (`Eltern_ID` = ? OR `Eltern_ID` = ?) AND `Parameter` = ?;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "iis", $Eltern_ID0, $Eltern_ID1, $Schnittstelle);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$query = "SELECT `Zusatz` FROM `Einstellungen` WHERE `Eltern_ID` = ".$line["Einstellung_ID"]." AND `Parameter` = 'Script';";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH);
	return $line["Zusatz"];
}
?>