<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<title>unidb</title>
<script type="text/javascript" src="./jquery-1.11.2.min.js"></script>

<?php
	include('./Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include 'mobil.php';
	$Text = Translate("Baum2.php");
	//Array bauen
	$query="SELECT `Vorlage_ID`, `DE` AS `Bezeichnung`, column_get(`Eigenschaften`, 'Datei' as CHAR) as `Datei` FROM `Vorlagen` where `Typ` ='Dokument';";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$Vorlage[$line["Vorlage_ID"]]["Bezeichnung"] = $line["Bezeichnung"];
		$Vorlage[$line["Vorlage_ID"]]["Datei"] = $line["Datei"];
	}
	if ($mobil==1){
		if($_SESSION["Thema"] == "dark") {
			echo "<link rel='StyleSheet' href='mobil_dtree_dark.css' type='text/css' />";
		} else {
			echo "<link rel='StyleSheet' href='mobil_dtree.css' type='text/css' />";
		}
		echo "<script type='text/javascript' src='mobil_dtree.js'></script>";
	} else {
		if($_SESSION["Thema"] == "dark") {
			echo "<link rel='StyleSheet' href='dtree_dark.css' type='text/css' />";
		} else {
			echo "<link rel='StyleSheet' href='dtree.css' type='text/css' />";
		}
		echo "<script type='text/javascript' src='dtree.js'></script>";
	}
	echo "</head><body class='BaumHG'>";
	$Text=str_replace('"',"\"",$Text);
	$Text=str_replace("'","\'",$Text);
	if($Aktion !== null){
		require_once('./Baum2_func.php');
	}
	if ($mobil==1){
		$Ziel = "_self";
	} else {
		$Ziel = "Hauptrahmen";
	}
	echo "<div class='Inhalt'>\n<table><tr><td><img src='./stat_Seiten/Logo.png' alt='Logo'></td><td width='15px'></td><td><table width='145px'><tr><td><a href='./Hilfe.php?Hilfe_ID=6' target='".$Ziel."' onclick=\"Hilfe_Fenster('6');\">".$Text[0]."</a></td><td align='right'><a href='./about_unidb.php' target='".$Ziel."'>".$Text[37]."</a></td></tr><tr><td colspan='2'><a href='./Einstellungen_".$_SESSION["Sprache"].".html' target='".$Ziel."'>".$Text[1]."</a></td></tr><tr><td colspan='2'><a href='./index.php?benutzer=abmelden' target='_top'>".$Text[2]."</a></td></tr><tr><td colspan='2'><a href='neu.php' target='".$Ziel."'>".$Text[3]."</a></td></tr>";
	echo "<tr><td colspan='2'><a href='./DH_Handeingabe.php' target='Hauptrahmen'>".$Text[4]."</a></td></tr><tr>";
	if($geloescht == 1) {
		echo "<td colspan='2'><a href='./Baum2.php?geloescht=0' target='_self'>".$Text[29]."</a></td></tr><tr>";
	} else {
		echo "<td colspan='2'><a href='./Baum2.php?geloescht=1' target='_self'>".$Text[28]."</a></td></tr><tr>";
	}
	if($_SESSION['Admin'] == 1) {
		if($mobil == 1) {
			echo "<td colspan='2'><a href='./admin/admin_Auswahl.php' target='_blank'>".$Text[5]."</a></td></tr><tr>";
		} else {	
			echo "<td colspan='2'><a href='./admin/admin.html' target='_blank'>".$Text[5]."</a></td></tr><tr>";
		}
	}
	echo "</table></td></tr></table>";
	echo "<form action='Baum2.php' method='post' target='_self' id=formular' name='formular'>";
	echo "<hr><br><table><tr><td><input class='Text_Element' value='' type='input' size='25' name='Suchbegriff'></td><td><input class='Schalter_Element' value='".$Text[6]."' type='submit' name='Aktion'></td></tr></table>\n";
	echo "</form><br>\n</div>";

	//Begriff suchen
	if ($Aktion==$Text[6] AND $Suchbegriff!=""){
		//Überschriften durchsuchen und auflisten
		$Begriff = "%".mysqli_real_escape_string($db, strip_tags($Suchbegriff))."%";
		$query = "SELECT `Baum`.`Baum_ID`, `Baum`.`Server_ID`, `Baum`.`Path`, `Baum`.`Vorlage`, `Baum`.`Bezeichnung`, `User_Path`.`User_ID` FROM `Baum`, `User_Path` WHERE LEFT(`Baum`.`Path`, LENGTH(`User_Path`.`Path`)) LIKE `User_Path`.`Path` AND `User_Path`.`User_ID` = ".$_SESSION['User_ID']." AND `Bezeichnung` like ? AND `geloescht` = 0;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "s", $Begriff);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		echo "<font size='2' face='Arial'><b>".$Text[7].":</b><br><table>";
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			echo "<tr><td>".$Vorlage[$line["Vorlage"]]["Bezeichnung"]."</td><td><a href='".$Vorlage[$line["Vorlage"]]["Datei"]."?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."'target='".$Ziel."'>".$line["Bezeichnung"]."</a></td></tr>";
		}
		mysqli_stmt_close($stmt);
		echo "</table></font><hr><br>";
	}
	$query = "SELECT `Path` FROM `User_Path` WHERE `User_ID` = ".$_SESSION['User_ID']." AND `root` = 1 ORDER BY `User_Path_ID` asc;";
	$stmt1 = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt1);
	$result1 = mysqli_stmt_get_result($stmt1);
	$Zaehler = 0;
	while ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
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
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','".$Vorlage[$line["Vorlage"]]["Datei"]."?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."','','_self');\n";
		} else {
			echo "d".$Zaehler.".add('".$BaumID."','-1','".$Bezeichnung."','".$Vorlage[$line["Vorlage"]]["Datei"]."?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."','','Hauptrahmen');\n";
		}
		mysqli_stmt_close($stmt);
		$einfuegen = " AND `geloescht` = 0";
		if($geloescht == 1) {$einfuegen = "";}
		$query = "SELECT * FROM `Baum` WHERE `Path` LIKE '".$Pfad."%'".$einfuegen." ORDER BY `Bezeichnung` ASC;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			if($line["Baum_ID"] == 771) {$x = $line;}
			$Bezeichnung = html_entity_decode($line["Bezeichnung"]);
			$BaumID = strval($line["Server_ID"])."_".strval($line["Baum_ID"]);
			if($line["geloescht"] == 1) {$Bezeichnung = "<font color=\"#909090\">".$Bezeichnung."</font>";}
			if ($mobil==1){
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','".$Vorlage[$line["Vorlage"]]["Datei"]."?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."','','_self');\n";
				} else {
					echo "d".$Zaehler.".add('".$BaumID."','".$line["Eltern_ID"]."','".$Bezeichnung."','".$Vorlage[$line["Vorlage"]]["Datei"]."?Baum_ID=".$line["Baum_ID"]."&Server_ID=".$line["Server_ID"]."','','Hauptrahmen');\n";
			}
		}
		echo "document.write(d".$Zaehler.");\n</script>\n</div><br>";
		mysqli_stmt_close($stmt);
		$Zaehler = $Zaehler + 1;
	}
	mysqli_stmt_close($stmt1);
	mysqli_close($db);
?>
</body>
</html>
