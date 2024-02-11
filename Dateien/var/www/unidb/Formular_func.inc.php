<?php
function FKol_schreiben($query,$DB) {
	$Verb = array();
	if($DB == "") {$DB = $_SESSION['Datenbank'];}
	$query = str_replace('\"', '"', $query);
	if($_SESSION['V_Daten'] == "_" || $_SESSION['V_Daten'] == null || $_SESSION['V_Daten'] == NULL) {
		$_SESSION['V_Daten'] = FKol_verbinden();
	}
	for ($i=0;$i<count($_SESSION['V_Daten']);$i++){
		$Verb[] = mysqli_connect($_SESSION['V_Daten'][$i]["IP"],$_SESSION['V_Daten'][$i]["User"],$_SESSION['V_Daten'][$i]["Password"],$DB);
		if ($Verb[$i] == false) {
			 return 1;
		}
		mysqli_query($Verb[$i], 'set character set utf8;');
	}
	for ($i=0;$i<count($_SESSION['V_Daten']);$i++){
		$Verb[$i] = mysqli_connect($_SESSION['V_Daten'][$i]["IP"],$_SESSION['V_Daten'][$i]["User"],$_SESSION['V_Daten'][$i]["Password"],$DB);
		$query = str_replace("\'","'",$query);
		$result = mysqli_query($Verb[$i], $query);
		mysqli_close($Verb[$i]);
	}
	return 0;
}

function FKol_verbinden() {
	$db = db_connect();
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` ='Kollektiv';";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);	
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Eltern_ID = $line["Einstellung_ID"];
	$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "i", $Eltern_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$Verb_daten1 = array();
	$Verb_daten = array();
	$Server = array();
	while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Verb = array();
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
		$stmt1 = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt1, "i", $line["Einstellung_ID"]);
		mysqli_stmt_execute($stmt1);
		$result1 = mysqli_stmt_get_result($stmt1);
		while($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
			$Verb[$line1["Parameter"]] = $line1["Wert"];
		}
		$Verb_daten1[] = $Verb;
	}
	$i = 0;
	while($i < count($Verb_daten1)) {
		if($Verb_daten1[$i]["Server_ID"] == $_SESSION["Server_ID"]) {
			$Verb_daten[] = $Verb_daten1[$i];
			array_splice($Verb_daten1,$i,1);
			$i = count($Verb_daten1) + 1;
		} else {
			$i = $i + 1;
		}
	}
	$i = 0;
	while($i < count($Verb_daten1)) {
		$Verb_daten[] = $Verb_daten1[$i];
		$i = $i + 1;
	}
	return $Verb_daten;
	mysqli_stmt_close($stmt);
	mysqli_stmt_close($stmt1);
}
?>