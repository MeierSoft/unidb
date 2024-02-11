<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel='StyleSheet' href='../dtree.css' type='text/css'>
<script type='text/javascript' src='../dtree.js'></script>

<?php
	session_start();
	include('../Sitzung.php');
	header("X-XSS-Protection: 1");
	if($_SESSION['admin'] != 1) {exit;}
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>Einstellungen ".$DB."</title>";
	echo "</head><body class='BaumHG'>";
	if($DB == NULL) {$DB = "unidb";}

   //neue Einstellung speichern
	if ($Aktion=="neue Einstellung speichern"){
		if($DB == "unidb") {
			$query="INSERT INTO `Einstellungen` (`Parameter`, `Eltern_ID`, `Wert`) VALUES (?, ?, ?);";
			uKol_schreiben(1,$query, "sis", [$Parameter, $Eltern_ID, $Wert]);
		} else {
			$query = "INSERT INTO `Einstellungen` (`Parameter`, `Eltern_ID`, `Wert`, `Zusatz`) VALUES ('".$Parameter."', '".$Eltern_ID."', '".$Wert."', '".$Zusatz."');";
			Kol_schreiben($query);
		}	
	}
	//Einstellung aendern
	if ($Aktion=="uebernehmen"){
		if($DB == "unidb") {
			$query="UPDATE `Einstellungen` SET `Eltern_ID` = ?, `Parameter` = ?, `Wert` = ? WHERE `Einstellung_ID` = ?;";
			uKol_schreiben(1,$query, "issi", [$Eltern_ID, $Parameter, $Wert, $Einstellung_ID]);
		} else {
			$query = "UPDATE `Einstellungen` SET `Parameter` = '".$Parameter."', `Wert` = '".$Wert."', `Zusatz` = '".$Zusatz."' WHERE `Einstellung_ID` = '".$Einstellung_ID."';";
			Kol_schreiben($query);
		}
	}

	//Einstellung löschen
	if ($Aktion == "loeschen"){
		if($DB == "unidb") {
			$query="DELETE FROM `Einstellungen` WHERE `Einstellung_ID` = ?;";
			uKol_schreiben(1,$query,"i",[$Einstellung_ID]);
		} else {
			$query = "DELETE FROM `Einstellungen` WHERE `Einstellung_ID` =".$Einstellung_ID.";";
			Kol_schreiben($query);
		}
	}
	//Einstellung verschieben
	if ($Aktion=="verschieben"){
		if($DB == "unidb") {
			$query="UPDATE `Einstellungen` SET `Eltern_ID` = ? WHERE `Einstellung_ID` = ?;";
			uKol_schreiben(1,$query, "ii", [$Eltern_ID, $Einstellung_ID]);
		} else {
			$query = "UPDATE `Einstellungen` SET `Eltern_ID` = '".$Eltern_ID."' WHERE `Einstellung_ID` = '".$Einstellung_ID."';";
			Kol_schreiben($query);
		}
	}
	echo "<title>Einstellungen ".$DB."</title>";
	echo "<h3>Einstellungen</h3>";
	echo "<div class='dtree'>";
	echo "<p><a href='javascript: d.openAll();'>alle &ouml;ffnen</a> | <a href='javascript: d.closeAll();'>alle schliessen</a></p>";
	echo "<script type='text/javascript'>";
	echo "d = new dTree('d');";
	echo "d.add(0,-1,'Einstellungen','Einstellung.php','','Hauptrahmen');";
	$query = "SELECT * FROM `Einstellungen` ORDER BY `Eltern_ID`, `Parameter` asc;";
	if($DB == "DH") {
		include '../conf_DH_schreiben.php';
		$req = mysqli_query($dbDH,$query);
	} else {
		include '../conf_unidb.php';
		$req = mysqli_query($db,$query);
	}
	while ($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		if ($mobil==1){
			echo "d.add(".$line["Einstellung_ID"].",".$line["Eltern_ID"].",'".$line["Parameter"]."','Einstellung.php?Einstellung_ID=".$line["Einstellung_ID"]."&DB=".$DB."','','_blank');";
		} else {
			echo "d.add(".$line["Einstellung_ID"].",".$line["Eltern_ID"].",'".$line["Parameter"]."','Einstellung.php?Einstellung_ID=".$line["Einstellung_ID"]."&DB=".$DB."','','Hauptrahmen');";
		}
	}
	if($DB == "DH") {
		mysqli_close($dbDH);
	} else {
		mysqli_close($db);
	}
?>
		document.write(d);
	</script>
</div>
</body>
</html>
