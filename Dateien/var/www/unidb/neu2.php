<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("neu2.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	include 'mobil.php';
	$_SESSION['Bezeichnung'] = strip_tags($Bezeichnung);
	if ($mobil==1){
		echo "<link rel='StyleSheet' href='mobil_dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='mobil_dtree.js'></script>";
	} else {
		echo "<link rel='StyleSheet' href='dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='dtree.js'></script>\n";
	}
	echo "<a href='javascript:void(0);' onclick='Hilfe_Fenster(\"12\");'>".$Text[1]."</a>&nbsp;&nbsp;&nbsp;";

	echo '"<font size="3" face="Arial">';
	echo $Text[2]."<br><br>";
	$stmt = mysqli_prepare($db,"SELECT `Vorlage_ID`, `DE` AS `Bezeichnung`, column_get(`Eigenschaften`, 'Datei' AS CHAR) AS `Datei` FROM `Vorlagen` WHERE `Vorlage_ID` = ?;");
	mysqli_stmt_bind_param($stmt, "i", $Vorlage_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);
	$Form_neu = str_replace(".php", "", $line["Datei"])."_neu.php";

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
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','".$Form_neu."?Eltern_ID=".$line["Server_ID"]."_".$line["Baum_ID"]."&Server_ID=".$_SESSION['Server_ID']."&original=".$original."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$_SESSION['Bezeichnung']."','','_self');\n";
		} else {
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','".$Form_neu."?Eltern_ID=".$line["Server_ID"]."_".$line["Baum_ID"]."&Server_ID=".$_SESSION['Server_ID']."&original=".$original."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$_SESSION['Bezeichnung']."','','Hauptrahmen');\n";
		}
		$query = "SELECT * FROM `Baum` WHERE `Path` LIKE '".$Pfad."%' AND `geloescht` = 0 ORDER BY `Bezeichnung` ASC;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
			$ElternID = $line["Server_ID"]."_".$line["Baum_ID"];
			$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
			if ($mobil==1){
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','".$Form_neu."?Eltern_ID=".$ElternID."&Server_ID=".$_SESSION['Server_ID']."&original=".$original."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$_SESSION['Bezeichnung']."','','_self');\n";
				} else {
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','".$Form_neu."?Eltern_ID=".$ElternID."&Server_ID=".$_SESSION['Server_ID']."&original=".$original."&Vorlage_ID=".$Vorlage_ID."&Bezeichnung=".$_SESSION['Bezeichnung']."','','_self');\n";
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
</font>
</body>
</html>