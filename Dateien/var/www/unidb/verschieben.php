<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include 'mobil.php';
	if ($mobil==1){
		echo "<link rel='StyleSheet' href='mobil_dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='mobil_dtree.js'></script>";
	} else {
		echo "<link rel='StyleSheet' href='dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='dtree.js'></script>";
	}
	$Text = Translate("verschieben.php");
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[2];
		exit;
	}
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<h3>".$Text[0]."</h3>\n";
	$query = "SELECT `Path` FROM `User_Path` WHERE `User_ID` = ".$_SESSION['User_ID']." AND `root` = 1 ORDER BY `User_Path_ID` ASC;";
	$stmt1 = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt1);
	$result1 = mysqli_stmt_get_result($stmt1);
	$Zaehler = 0;
	while ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
		$Zaehler = $Zaehler + 1;
		$Pfad = html_entity_decode($line1["Path"]);
		echo "<div class='dtree'>\n";
		echo "<script type='text/javascript'>\n";
		echo "d".$Zaehler." = new dTree('d".$Zaehler."');\n";
		$Bezeichnung = $Pfad;
		while(strpos($Bezeichnung,"/") > -1) {
			$Bezeichnung = substr($Bezeichnung,strpos($Bezeichnung,"/") + 1,strlen($Bezeichnung));
		}
		$Pfad = substr($Pfad, 0, strlen($Pfad) - strlen($Bezeichnung) - 1);
		if($Pfad == "") {$Pfad = "/";}
		$query = "SELECT `Baum_ID`, `Server_ID`, `Vorlage` FROM `Baum` WHERE `Path` = '".$Pfad."' AND `Bezeichnung` = '".$Bezeichnung."';";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if($line["Baum_ID"] < 1) {
			$BaumID = "1_0";
			$Bezeichnung = "<b>".$Text[8]."</b>";
		} else {
			$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
		}
		if ($mobil==1){
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','Baum2.php?Aktion=verschieben&Baum_ID=".$Baum_ID."&Eltern_ID=".$BaumID."&Server_ID=".$Server_ID."','','_self');\n";
			} else {
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','Baum2.php?Aktion=verschieben&Baum_ID=".$Baum_ID."&Eltern_ID=".$BaumID."&Server_ID=".$Server_ID."','','Hauptrahmen');\n";
		}
		mysqli_stmt_close($stmt);
		$query = "SELECT * FROM `Baum` WHERE `Path` LIKE '".$Pfad."%' AND `geloescht` = 0 ORDER BY `Bezeichnung` ASC;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
			$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
			if ($mobil==1){
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','Baum2.php?Aktion=verschieben&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Eltern_ID=".$BaumID."','','_self');\n";
				} else {
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','Baum2.php?Aktion=verschieben&Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&Eltern_ID=".$BaumID."','','Baum');\n";
			}
		}
		echo "document.write(d".$Zaehler.");\n</script>\n</div>";
		mysqli_stmt_close($stmt);
	}
	mysqli_stmt_close($stmt1);
	mysqli_close($db);
?>
</div></div>
</body>
</html>
