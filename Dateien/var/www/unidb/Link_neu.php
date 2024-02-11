<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />

<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("Link_neu.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo $Text[1]."<br><br>\n";
	include 'mobil.php';
	if ($Aktion == ""){$Aktion = $Text[2];}
	if ($Aktion == $Text[3]){$Aktion = $Text[4];}
	if(strlen($Bezeichnung) == 0) {$Bezeichnung = $_SESSION['Bezeichnung'];}
	if ($mobil==1){
		echo "<link rel='StyleSheet' href='mobil_dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='mobil_dtree.js'></script>";
	} else {
		echo "<link rel='StyleSheet' href='dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='dtree.js'></script>\n";
	}
	$query = "SELECT `Path` FROM `User_Path` WHERE `User_ID` = ".$_SESSION['User_ID']." ORDER BY `User_Path_ID` ASC;";
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
		$query = "SELECT * FROM `Baum` WHERE `Path` LIKE '".$Pfad."' AND `geloescht` = 0 ORDER BY `Baum_ID` ASC LIMIT 1;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Bezeichnung1 = html_entity_decode($line["Bezeichnung"]);
		if($line["Baum_ID"] < 1) {
			$line["Baum_ID"] = 0;
			if($Bezeichnung1 == "") {$Bezeichnung1 = "Inhalt";}
		}
		if($line["Server_ID"] == null) {$line["Server_ID"] = 1;}
		$BaumID = $line["Server_ID"]."_".intval(substr($Eltern_ID,strpos($Eltern_ID,"_"),strlen($Eltern_ID)));
		if ($mobil==1){
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung1."','Baum2.php?Eltern_ID=".$Eltern_ID."&Vorlage_ID=".$Vorlage_ID."&Baum_ID=".$line["Baum_ID"]."&Server_ID=".$Server_ID."&Bezeichnung=".$Bezeichnung."&Ziel=".$BaumID."&Aktion=".$Aktion."','','_self');\n";
		} else {
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung1."','Baum2.php?Eltern_ID=".$Eltern_ID."&Vorlage_ID=".$Vorlage_ID."&Baum_ID=".$line["Baum_ID"]."&Server_ID=".$Server_ID."&Bezeichnung=".$Bezeichnung."&Ziel=".$BaumID."&Aktion=".$Aktion."','','Baum');\n";
		}
		$query = "SELECT * FROM `Baum` WHERE LEFT(`Path`, LENGTH('".$Pfad."')) LIKE '".$Pfad."' AND `geloescht` = 0 ORDER BY `Eltern_ID`, `Bezeichnung` asc;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
			if ($mobil==1){
				echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$line["Bezeichnung"]."','Baum2.php?Eltern_ID=".$Eltern_ID."&Baum_ID=".$line["Baum_ID"]."&Server_ID=".$Server_ID."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$Bezeichnung."&Ziel=".$BaumID."&Aktion=".$Aktion."','','_self');\n";
			} else {
				echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$line["Bezeichnung"]."','Baum2.php?Eltern_ID=".$Eltern_ID."&Baum_ID=".$line["Baum_ID"]."&Server_ID=".$Server_ID."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$Bezeichnung."&Ziel=".$BaumID."&Aktion=".$Aktion."','','Baum');\n";
			}
		}
		mysqli_stmt_close($stmt);
		echo "document.write(d".$Zaehler.");\n</script>\n</div>\n";
	}
	// schliessen der Verbindung
	mysqli_stmt_close($stmt1);
	mysqli_close($db);
?>
</div></div>
</body>
</html>