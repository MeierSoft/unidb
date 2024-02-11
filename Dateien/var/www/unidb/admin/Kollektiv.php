<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../jquery-ui.css">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="../jquery.min.js"></script>
<script type="text/javascript">
function laden(Typ) {
	if (Typ == "Server") {
		document.Phpform.elements["schnittstellenrechner"].value = "";
		document.Phpform.elements["schnittstellenrechner_id"].value = 0;
	} else {
		document.Phpform.elements["kollektivmitglieder"].value = "";
		document.Phpform.elements["kollektiv_id"].value = 0;
	}
	document.getElementById("phpform").submit();	
}
</script>
<?php
	//session_start();
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	include('../conf_DH_schreiben.php');
	$Text = Translate("Kollektiv.php");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<div class='Text_fett' style='position: absolute; top: 10px; left: 10px;'><font size='+1'>".$Text[11]."</div>\n";
	echo "<div class='Text_fett' style='position: absolute; top: 10px; left: 200px;'><font size='+1'>".$Text[12]."</div>\n";
	echo "<div class='Text_fett' style='position: absolute; top: 250px; left: 115px;'>Parameter</font></div>\n";
	echo "<form action='Kollektiv.php' method='post' target='_self' id='phpform' name='Phpform'>\n";
	echo "<table class='Text_einfach' style='position: absolute; top: 275px; left: 10px;' cellpadding='3px'>\n";
	//speichern
	if($speichern == $Text[1]) {
		$query = "UPDATE `Einstellungen` SET `Wert` = '".$Database."' WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = 'Database';";
		Kol_schreiben($query);
		$query = "UPDATE `Einstellungen` SET `Wert` = '".$User."' WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = 'User';";
		Kol_schreiben($query);
		$query = "UPDATE `Einstellungen` SET `Wert` = '".$Password."' WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = 'Password';";
		Kol_schreiben($query);
		$query = "UPDATE `Einstellungen` SET `Wert` = '".$IP."' WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = 'IP';";
		Kol_schreiben($query);
		$query = "UPDATE `Einstellungen` SET `Parameter` = '".$Servername."' WHERE `Einstellung_ID` = ".$Eltern_ID.";";
 	 	Kol_schreiben($query);
	}
	if($speichern == $Text[2]) {
		$query="DELETE FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." OR `Einstellung_ID` = ".$Eltern_ID.";";
 	 	Kol_schreiben($query);
	}
	if($speichern == $Text[3] or $speichern == $Text[4]){
		if($speichern == $Text[3]) {
			$Parent_ID = $Kollektiv_ID;
		} else {
			$Parent_ID = $Schnittstellenrechner_ID;
		}
		$query="INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Parent_ID.",'".$Text[5]."');";
 	 	Kol_schreiben($query);
		$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = '".$Text[5]."' AND `Eltern_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
 	 	mysqli_stmt_bind_param($stmt, "i", $Parent_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
   	mysqli_stmt_close($stmt);
   	$Eltern_ID = $line['Einstellung_ID'];
   	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Database');";
		Kol_schreiben($query);
 	 	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'IP');";
		Kol_schreiben($query);
		$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'User');";
		Kol_schreiben($query);
		$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Password');";
		Kol_schreiben($query);
		if($speichern == $Text[3]) {
			$Kollektivmitglieder = $Text[5];
		} else {
			$Schnittstellenrechner = $Text[5];
		}
	}

	//Kollektiv
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Kollektiv' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Kollektiv_ID = $line['Einstellung_ID'];
	mysqli_stmt_close($stmt);
	//Kollektivmitglieder in ein Listenfeld bringen
	echo "<select style='position: absolute; top: 70px; left: 10px;' id='kollektivmitglieder' name='Kollektivmitglieder' size='7' onclick='laden(\"Server\");'>\n";
	$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Kollektiv_ID.";";
   $stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   	if($line['Parameter'] == $Kollektivmitglieder) {
   echo "<option selected>".$line['Parameter']."</option>\n";
   	} else {
	echo "<option>".$line['Parameter']."</option>\n";
}
	}
	echo "</select>\n";
	
	//Schnittstellenrechner
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Schnittstellenrechner' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Schnittstellenrechner_ID = $line['Einstellung_ID'];
	mysqli_stmt_close($stmt);
	echo "<select style='position: absolute; top: 70px; left: 200px;' id='schnittstellenrechner' name='Schnittstellenrechner' size='7' onclick='laden(\"Schnittstelle\");'>\n";
	if($Schnittstellenrechner_ID != NULL) {
		//Kollektivmitglieder in ein Listenfeld bringen
		$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Schnittstellenrechner_ID.";";
   	$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   		if($line['Parameter'] == $Schnittstellenrechner) {
   			echo "<option selected>".$line['Parameter']."</option>\n";
   		} else {
				echo "<option>".$line['Parameter']."</option>\n";
			}
		}
		mysqli_stmt_close($stmt);
	}
	echo "</select>\n";
	echo "<input name='Kollektiv_ID' id='kollektiv_id' value='".$Kollektiv_ID."' type='hidden'>\n";
	echo "<input name='Schnittstellenrechner_ID' id='schnittstellenrechner_id' value='".$Schnittstellenrechner_ID."' type='hidden'>\n";
	echo "<div  style='position: absolute; top: 450px; left: 112px;' class='Text_einfach'><input name='speichern' value='".$Text[1]."' type='submit' style='width: 70px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 450px; left: 198px;' class='Text_einfach'><input name='speichern' value='".$Text[2]."' type='submit' style='width: 70px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 32px; left: 10px;' class='Text_einfach'><input name='speichern' value='".$Text[3]."' type='submit' style='border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 32px; left: 200px;' class='Text_einfach'><input name='speichern' value='".$Text[4]."' type='submit' style='border-left: 0px;'></div>\n";
	
	//Felder füllen
	if($Kollektivmitglieder > "" and $Kollektivmitglieder != NULL) {
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Kollektiv_ID." AND `Parameter` = '".$Kollektivmitglieder."';";
	} else {
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Schnittstellenrechner_ID." AND `Parameter` = '".$Schnittstellenrechner."';";
	}
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
  	$Eltern_ID = $line['Einstellung_ID'];
  	echo "<input name='Eltern_ID' id='einstellung_id' value='".$Eltern_ID."' type='hidden'>\n";
  	if(strlen($line['Parameter']) > 0) { echo "<tr><td align='right'><div style='width: 90px;' class='Text_fett'>".$Text[6]."</td><td><input class='Text_Element' name='Servername' id='servername' value='".$line['Parameter']."' type='Text'></div></td></tr>\n";}
  	mysqli_stmt_close($stmt);
  	$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID.";";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  		if($line['Parameter'] == "Database") {echo "<tr><td align='right' class='Text_fett'>".$Text[7]."</td><td><input class='Text_Element' name='Database' id='datenbank' value='".$line['Wert']."' type='Text'></td></tr>\n";}
  		if($line['Parameter'] == "IP") {echo "<tr><td align='right' class='Text_fett'>".$Text[8]."</td><td><input class='Text_Element' name='IP' id='ip' value='".$line['Wert']."' type='Text'></td></tr>\n";}
		if($line['Parameter'] == "User") {echo "<tr><td align='right' class='Text_fett'>".$Text[9]."</td><td><input class='Text_Element' name='User' id='benutzer' value='".$line['Wert']."' type='Text'></td></tr>\n";}
		if($line['Parameter'] == "Password") {echo "<tr><td align='right' class='Text_fett'>".$Text[10]."</td><td><input class='Text_Element' name='Password' id='passwort' value='".$line['Wert']."' type='Text'></td></tr>\n";}
	}
  	mysqli_stmt_close($stmt);
	echo "</table>\n";

?>
</form>
</body>
</html>
