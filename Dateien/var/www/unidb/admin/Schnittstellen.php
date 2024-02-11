<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../jquery-ui.css">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script src="../jquery-3.3.1.min.js"></script>

<?php
	session_start();
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	include('../conf_DH_schreiben.php');
	$Text = Translate("Schnittstellen.php");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 10px; left: 150px;'><font size='+1'><b>".$Text[0]."</b></font></div>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 30px; left: 10px;'><font size='+1'>".$Text[1]."</div>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 30px; left: 300px;'>".$Text[2]."</font></div>\n";
	echo "<form action='Schnittstellen.php' method='post' target='_self' id='phpform' name='Phpform'>\n";
	echo "<input name='Points_bearbeiten' id='points_bearbeiten' value='' type='hidden'>\n";

	//Schnittstellen_ID ermitteln
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Schnittstellen' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Schnittstellen_ID = $line['Einstellung_ID'];
	echo "<input name='Schnittstellen_ID' id='schnittstellen_id' value='".$Schnittstellen_ID."' type='hidden'>\n";
	mysqli_stmt_close($stmt);
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'außer Betrieb' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Ausser_Betrieb_ID = $line['Einstellung_ID'];
	echo "<input name='Ausser_Betrieb_ID' id='ausser_Betrieb_id' value='".$Ausser_Betrieb_ID."' type='hidden'>\n";
	mysqli_stmt_close($stmt);
	//speichern
	if($speichern == $Text[3]) {
		if($Ausser_Betrieb > "") {
			$Schnittstellen = $Ausser_Betrieb;
			$query = "UPDATE `Einstellungen` SET `Eltern_ID` = ".$Schnittstellen_ID." WHERE `Eltern_ID` = ".$Ausser_Betrieb_ID." AND `Parameter` = '".$Ausser_Betrieb."';";
			Kol_schreiben($query);
			//Systemtags auf scan = 1 stellen
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
			Kol_schreiben($query);
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	}
	if($speichern == $Text[4]) {
		if($Schnittstellen > "") {
			$Ausser_Betrieb = $Schnittstellen;
			$query = "UPDATE `Einstellungen` SET `Eltern_ID` = ".$Ausser_Betrieb_ID." WHERE `Eltern_ID` = ".$Schnittstellen_ID." AND `Parameter` = '".$Schnittstellen."';";
			Kol_schreiben($query);
			//Systemtags auf scan = 0 stellen
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
			Kol_schreiben($query);
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	}
	if($speichern == $Text[5]) {
		$query="DELETE FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." OR `Einstellung_ID` = ".$Eltern_ID.";";
 	 	Kol_schreiben($query);
 	 	//Systemtags auf scan = 0 stellen
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
		Kol_schreiben($query);
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
		Kol_schreiben($query);
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
		Kol_schreiben($query);
		Meldung_schreiben("comp", "Points einlesen", "Server");
		Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
		Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
	}
	if($speichern == $Text[6]) {
		$query="INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Ausser_Betrieb_ID.",'neue_Schnittstelle');";
 	 	Kol_schreiben($query);
		$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'neue_Schnittstelle' AND `Eltern_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
 	 	mysqli_stmt_bind_param($stmt, "i", $Ausser_Betrieb_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
   	mysqli_stmt_close($stmt);
   	$Eltern_ID = $line['Einstellung_ID'];
   	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Script');";
		Kol_schreiben($query);
 	 	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Intervall');";
		Kol_schreiben($query);
		$Ausser_Betrieb = "neue_Schnittstelle";
	}
	if($speichern == "Schnittstelle starten") {
		Meldung_schreiben($Schnittstellen, "einschalten", $Script_Zusatz);
	}
	if($speichern == "Schnittstelle stoppen") {
		Meldung_schreiben($Schnittstellen, "abschalten", $Script_Zusatz);
	}
	
	//Schnittstellen in ein Listenfeld bringen
	echo "<select style='position: absolute; top: 60px; left: 10px;' id='schnittstellen' name='Schnittstellen' size='7' onclick='markieren(\"schnittstellen\");'>\n";
	$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Schnittstellen_ID." ORDER BY `Parameter` ASC;";
   $stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   	if($line['Parameter'] == $Schnittstellen) {
   		echo "<option selected>".$line['Parameter']."</option>\n";
   		$verst_Feld = "<input name='alter_Schnittstellenname' id='alter_schnittstellenname' value='".$line['Parameter']."' type='hidden'>\n";
   	} else {
			echo "<option>".$line['Parameter']."</option>\n";
		}
	}
	echo "</select>\n";
	
	echo "<select style='position: absolute; top: 60px; left: 300px;' id='ausser_Betrieb' name='Ausser_Betrieb' size='7' onclick='markieren(\"ausser_Betrieb\");'>\n";
	$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Ausser_Betrieb_ID." ORDER BY `Parameter` ASC;";
   $stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   	if($line['Parameter'] == $Ausser_Betrieb) {
   		echo "<option selected>".$line['Parameter']."</option>\n";
   		$verst_Feld = "<input name='alter_Schnittstellenname' id='alter_schnittstellenname' value='".$line['Parameter']."' type='hidden'>\n";
   	} else {
			echo "<option>".$line['Parameter']."</option>\n";
		}
	}
	echo "</select>\n";
	echo $verst_Feld;
	echo "<div  style='position: absolute; top: 60px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[3]."' type='submit' style='width: 100px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 92px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[4]."' type='submit' style='width: 100px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 156px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[5]."' type='submit' style='width: 75px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 188px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[6]."' type='submit' style='width: 75px; border-left: 0px;'></div>\n";
	
	mysqli_stmt_close($stmt);
	//Felder füllen
	if($Schnittstellen > "") {
		$Eltern_ID = $Schnittstellen_ID;
		$SName = $Schnittstellen;
	} else {
		$Eltern_ID = $Ausser_Betrieb_ID;
		$SName = $Ausser_Betrieb;
	}
	$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = '".$SName."';";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
  	$Eltern_ID = $line['Einstellung_ID'];
  	echo "<input name='Eltern_ID' id='einstellung_id' value='".$Eltern_ID."' type='hidden'>\n";
	echo "<input name='Parameter' id='parameter' value='' type='hidden'>\n";
	if($Schnittstellen > "" or $Ausser_Betrieb > "") {
		echo "<table class='Text_einfach' style='position: absolute; top: 280px; left: 10px;' cellspacing='3px'>\n";
		echo "<tr><td><b>".$Text[7]."</b></td><td><b>".$Text[8]."</b></td><td><b>".$Text[9]."</b></td><td></td></tr>\n";
		if(strlen($line['Parameter']) > 0) { echo "<tr><td align='right' class='Text_fett'>".$Text[10]."</td><td><input class='Text_Element' name='Schnittstellenname' id='schnittstellenname' value='".$line['Parameter']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",0);'></td><td><input class='Text_Element' name='Schnittstellenname_Zusatz' id='schnittstellenname_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",0);'></td></tr>\n";}
			$Schnittstellenname = $line['Parameter'];
			mysqli_stmt_close($stmt);
			$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." ORDER BY `Einstellung_ID` ASC;";
			$stmt = mysqli_prepare($dbDH, $query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
  			while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  				if($line['Parameter'] == "Intervall" || $line['Parameter'] == "Script") {
					echo "<tr><td align='right' class='Text_fett'>".$line['Parameter']."</td><td><input class='Text_Element' name='".$line['Parameter']."' id='".$line['Parameter']."' value='".$line['Wert']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Text_Element' name='".$line['Parameter']."_Zusatz' id='".$line['Parameter']."_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td></td></tr>\n";
				} else {  
  					echo "<tr><td align='right' class='Text_fett'>".$line['Parameter']."</td><td><input class='Text_Element' name='".$line['Parameter']."' id='".$line['Parameter']."' value='".$line['Wert']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Text_Element' name='".$line['Parameter']."_Zusatz' id='".$line['Parameter']."_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Schalter_Element' id='entfernen' name='Entfernen' value='".$Text[5]."' type='button' onclick='Parameter_entfernen(\"".$line['Parameter']."\");'></td></tr>\n";
  				}
			}
  			mysqli_stmt_close($stmt);
			echo "<div style='position: absolute; top: 230px; left: 0px; width: 450px;'><hr></div>\n";
			echo "<div class='Text_einfach' style='position: absolute; top: 250px; left: 150px;'><font size='+1'><b>".$Text[7]."</b></font></div>\n";
			echo "<tr><td><hr></td><td><hr></td><td><hr></td></tr>\n";
			echo "<tr><td align='right' class='Text_fett'>".$Text[11]."</td><td><input class='Schalter_Element' id='neu' name='Neu' value='".$Text[12]."' type='button' onclick='neuer_Parameter(".$Eltern_ID.");'></td></tr>\n";
	}
	echo "</table>\n";
	if($Points_bearbeiten == $Text[13]) {
		//ggf die Systempoints anpassen
		if ($alter_Schnittstellenname != $Schnittstellenname) {
			//Status-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) { 
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'sy_".$Schnittstellenname."_status' WHERE `Pointname` = 'sy_".$alter_Schnittstellenname."_status';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'sy_".$Schnittstellenname."_status' WHERE `Tagname` = 'sy_".$alter_Schnittstellenname."_status';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Werte pro Stunde-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) {
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'Wph_".$Schnittstellenname."' WHERE `Pointname` = 'Wph_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'Wph_".$Schnittstellenname."' WHERE `Tagname` = 'Wph_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Zeit seit letztem Wert-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) {
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'ZslW_".$Schnittstellenname."' WHERE `Pointname` = 'ZslW_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'ZslW_".$Schnittstellenname."' WHERE `Tagname` = 'ZslW_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
		}
		//Status-Point
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) == 0) {
			$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'sy_".$Schnittstellenname."_status', 'Status von ".$Schnittstellenname."', 0, 'watchdog', 1, 1, 0, 3600, '".$Rechner."', 1, 'double', 1, 0, 1, 0, 0, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
        	Kol_schreiben($SQL_Text);
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[0] = $line["Point_ID"];
			$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('sy_".$Schnittstellenname."_status', '/', ".$Point[0].");";
        	Kol_schreiben($SQL_Text);
			$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[0].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
      	Kol_schreiben($SQL_Text);
      } else {
      	$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[0] = $line["Point_ID"];
			$SQL_Text = "UPDATE `Points` SET `Description`='Status von ".$Schnittstellenname."', `Interface`='watchdog',`archive`=1,`step`=1,`compression`=0,`minarch`=3600,`Info`='".$Rechner."',`Property_1`=1,`Point_Type`='double',`Dezimalstellen`=1,`Scale_min`=0,`Scale_max`=1,`Intervall`=0,`Mittelwerte`=0 WHERE `Point_ID` = ".$Point[0].";";
			Kol_schreiben($SQL_Text);
			$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'sy_".$Schnittstellenname."_status' WHERE `Point_ID` = ".$Point[0].";";
			Kol_schreiben($SQL_Text);
		}
		mysqli_stmt_close($stmt);
		//Werte pro Stunde-Point
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
  		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) == 0) {
			$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'Wph_".$Schnittstellenname."', 'Werte pro h', 0, 'System', 1, 0, 0, 28800, '".$Schnittstellenname.": Werte_pro_h_Point', 1, 'double', 2, 0, 500, 0, 1, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
			Kol_schreiben($SQL_Text);
     		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
     		$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[1] = $line["Point_ID"];
			$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('Wph_".$Schnittstellenname."', '/', ".$Point[1].");";
	     	Kol_schreiben($SQL_Text);
			$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[1].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
			Kol_schreiben($SQL_Text);
		} else {
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[1] = $line["Point_ID"];
			$SQL_Text = "UPDATE `Points` SET `Description`='Werte pro h',`Interface`='System',`archive`=1,`step`=0,`compression`=0,`minarch`=28800,`Info`='".$Schnittstellenname.": Werte_pro_h_Point',`Property_1`=1,`Point_Type`='double',`Dezimalstellen`=2,`Scale_min`=0,`Scale_max`=500,`Intervall`=0,`Mittelwerte`=1 WHERE `Point_ID` = ".$Point[1].";";
			Kol_schreiben($SQL_Text);
			$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'Wph_".$Schnittstellenname."' WHERE `Point_ID` = ".$Point[1].";";
			Kol_schreiben($SQL_Text);
		}
		mysqli_stmt_close($stmt);
		//Zeit seit letztem Wert-Point
		if ($Schnittstellenname !="comp"){
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$Satz = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$query = "SELECT max(`Property_1`) as Location1 FROM `Points` WHERE `Interface` = 'calc';";
			$stmt = mysqli_prepare($dbDH,$query);
 			mysqli_stmt_execute($stmt);
			$result1 = mysqli_stmt_get_result($stmt);
			$Satz1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
			if (mysqli_num_rows($result) == 0) {
           	$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'ZslW_".$Schnittstellenname."', 'Zeit seit letztem Wert', 0, 'calc', 1, 1, 0, 3600, '', ".strval(floatval($Satz1[0]["Location1"]) + 1).", 'calc', 0, 0, 1000, 600, 0, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
           	Kol_schreiben($SQL_Text);
           	$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
           	$stmt = mysqli_prepare($dbDH,$query);
 				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Point[2] = $line["Point_ID"];
				$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('ZslW_".$Schnittstellenname."', '/', ".$Point[2].");";
				Kol_schreiben($SQL_Text);
           	$SQL_Text = "UPDATE `Points` SET `Property_1`='time.time()-ZP(".$Point.")' WHERE `Point_ID` = ".$Point.";";
				Kol_schreiben($SQL_Text);
           	$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[2].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
           	Kol_schreiben($SQL_Text);
     		} else {
  				$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
           	$stmt = mysqli_prepare($dbDH,$query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Point[2] = $line["Point_ID"];
				$SQL_Text = "UPDATE `Points` SET `Description`='Zeit seit letztem Wert', `Interface`='calc',`archive`=1,`step`=1,`compression`=0,`minarch`=3600,`Info`='time.time()-ZP(".$Point.")',`Point_Type`='calc',`Dezimalstellen`=0,`Scale_min`=0,`Scale_max`=1000,`Intervall`=600,`Mittelwerte`=0 WHERE `Point_ID` = ".$Point[2].";";
		     	Kol_schreiben($SQL_Text);
           	$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'ZslW_".$Schnittstellenname."' WHERE `Point_ID` = ".$Point[2].";";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Meldung ausgeben
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	} else {
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status' OR `Pointname` = 'Wph_".$Schnittstellenname."' OR `Pointname` = 'ZslW_".$Schnittstellenname."';";
      $stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$i = 0;
		while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$Point[$i] = $line["Point_ID"];
			$i = $i + 1;
		}
		mysqli_stmt_close($stmt);
	}
	$Points_bearbeiten = "";
	
	//Tags anzeigen
	echo "<div style='position: absolute; top: 60px; left: 500px;'>\n<table>";
	for($i=0;$i < 3; $i++) {
		$query= "SELECT Tag_ID, Tagname, Dezimalstellen, Description, EUDESC FROM Tags WHERE Point_ID = ? ORDER BY Tag_ID ASC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Point[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID =? ORDER BY Timestamp DESC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Point[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Wert=round($line_Value['Value'],$line_Tag["Dezimalstellen"]);
		$Zeitpunkt = $line_Value['Timestamp'];
		$Tagname = html_entity_decode($line_Tag["Tagname"]);
		$Description = html_entity_decode($line_Tag["Description"]);
		echo"<tr bgcolor='#E5E5E5'><td><a href='javascript:void(0);' onclick='Tagdetails(".$line_Tag["Tag_ID"].");'>";
		echo $Tagname."</a></td><td>".$Description."</td><td>".$Zeitpunkt."</td><td width=60pt>".$Wert."</td><td>".$line_Tag["EUDESC"]."</td><td><a href='../Trend.php?Tag_ID=".$line_Tag["Tag_ID"]."&Zeitpunkt=jetzt' target='_blank'>".$Text[14]."</a></td></tr>";
	}
	mysqli_close($dbDH);
?>
<tr height='50px'><td valign='bottom' colspan='6' width='100%'><table width='100%'><tr><td width='50%' align='center'><input class='Schalter_Element' name='speichern' value='Schnittstelle starten' type='submit' style='border-left: 0px;'></td><td width='50%' align='center'><input class='Schalter_Element' name='speichern' value='Schnittstelle stoppen' type='submit' style='border-left: 0px;'></td></tr></table></td></tr>
</table></form>
</body>
<script type="text/javascript">
var panel;
function Tagdetails(Tag_ID) {
	try {panel.close('tagdetails');} catch (err) {}
	jQuery.ajax({
		url: "./DH_Tagdetails.php?Tag_ID=" + Tag_ID,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	panel = jsPanel.create({
		id: 'tagdetails',
		position: 'left-top 10 10',
		theme: 'primary',
		contentSize: '600 600',
		headerTitle: 'Tagdetails',
		content: strReturn,
	});
}

function Parameter_entfernen(Parameter) {
	document.Phpform.elements.Parameter.value = Parameter;
	var SQL = "DELETE FROM `Einstellungen` WHERE `Eltern_ID` = " + document.Phpform.elements.Eltern_ID.value + " AND `Parameter` = '" + document.Phpform.elements.Parameter.value + "';"
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function neuer_Parameter(Eltern_ID) {
	var Parameter = prompt(<?php echo "'".$Text[15]."'";?>);
	var SQL = "Insert INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (" + document.Phpform.elements.Eltern_ID.value + ", '" + Parameter + "');"
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function abspeichern(Parameter,Knoten) {
	if (Knoten == 0) {
		var Feld = document.Phpform.elements["schnittstellenname"];
		var Zusatz = document.Phpform.elements["schnittstellenname_zusatz"];
		if (document.Phpform.elements["schnittstellen"].value > "") {
			var SQL = "UPDATE `Einstellungen` SET `Parameter` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.schnittstellen_id.value + " and `Parameter` = '" + Parameter + "';"
		} else {
			var SQL = "UPDATE `Einstellungen` SET `Parameter` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.ausser_Betrieb_id.value + " and `Parameter` = '" + Parameter + "';"
		}
		var newOption = new Option(Feld.value,Feld.value);
		document.Phpform.elements.ausser_Betrieb.add(newOption,undefined);
		document.Phpform.elements.ausser_Betrieb.value = Feld.value;
		document.Phpform.elements.points_bearbeiten.value = <?php echo "'".$Text[13]."'";?>;
	} else {
		var Feld = document.Phpform.elements[Parameter];
		var Zusatz = document.Phpform.elements[Parameter + "_Zusatz"];
		var SQL = "UPDATE `Einstellungen` SET `Wert` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.Eltern_ID.value + " and `Parameter` = '" + Parameter + "';"
	}
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function markieren(Liste) {
	if (Liste == "schnittstellen") {
		document.getElementById("ausser_Betrieb").value="";
	} else {
		document.getElementById("schnittstellen").value="";
	}
	document.getElementById("phpform").submit();
}
</script>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<link href="../../Fenster/dist/jspanel.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../jquery-ui.css">
<script src="../../Fenster/dist/jspanel.min.js"></script>
<script src="../jquery-3.3.1.min.js"></script>

<?php
	session_start();
	include('../Sitzung.php');
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	include('../conf_DH_schreiben.php');
	$Text = Translate("Schnittstellen.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 10px; left: 150px;'><font size='+1'><b>".$Text[0]."</b></font></div>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 30px; left: 10px;'><font size='+1'>".$Text[1]."</div>\n";
	echo "<div class='Text_einfach' style='position: absolute; top: 30px; left: 300px;'>".$Text[2]."</font></div>\n";
	echo "<form action='Schnittstellen.php' method='post' target='_self' id='phpform' name='Phpform'>\n";
	echo "<input name='Points_bearbeiten' id='points_bearbeiten' value='' type='hidden'>\n";

	//Schnittstellen_ID ermitteln
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Schnittstellen' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Schnittstellen_ID = $line['Einstellung_ID'];
	echo "<input name='Schnittstellen_ID' id='schnittstellen_id' value='".$Schnittstellen_ID."' type='hidden'>\n";
	mysqli_stmt_close($stmt);
	$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'außer Betrieb' AND `Eltern_ID` = 0;";
  	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Ausser_Betrieb_ID = $line['Einstellung_ID'];
	echo "<input name='Ausser_Betrieb_ID' id='ausser_Betrieb_id' value='".$Ausser_Betrieb_ID."' type='hidden'>\n";
	mysqli_stmt_close($stmt);
	//speichern
	if($speichern == $Text[3]) {
		if($Ausser_Betrieb > "") {
			$Schnittstellen = $Ausser_Betrieb;
			$query = "UPDATE `Einstellungen` SET `Eltern_ID` = ".$Schnittstellen_ID." WHERE `Eltern_ID` = ".$Ausser_Betrieb_ID." AND `Parameter` = '".$Ausser_Betrieb."';";
			Kol_schreiben($query);
			//Systemtags auf scan = 1 stellen
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 1 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
			Kol_schreiben($query);
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	}
	if($speichern == $Text[4]) {
		if($Schnittstellen > "") {
			$Ausser_Betrieb = $Schnittstellen;
			$query = "UPDATE `Einstellungen` SET `Eltern_ID` = ".$Ausser_Betrieb_ID." WHERE `Eltern_ID` = ".$Schnittstellen_ID." AND `Parameter` = '".$Schnittstellen."';";
			Kol_schreiben($query);
			//Systemtags auf scan = 0 stellen
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
			Kol_schreiben($query);
			$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
			Kol_schreiben($query);
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	}
	if($speichern == $Text[5]) {
		$query="DELETE FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." OR `Einstellung_ID` = ".$Eltern_ID.";";
 	 	Kol_schreiben($query);
 	 	//Systemtags auf scan = 0 stellen
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'sy_".$Schnittstellen."_status';";
		Kol_schreiben($query);
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'Wph_".$Schnittstellen."';";
		Kol_schreiben($query);
		$query = "UPDATE `Points` SET `scan` = 0 WHERE `Pointname` = 'ZslW_".$Schnittstellen."';";
		Kol_schreiben($query);
		Meldung_schreiben("comp", "Points einlesen", "Server");
		Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
		Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
	}
	if($speichern == $Text[6]) {
		$query="INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Ausser_Betrieb_ID.",'neue_Schnittstelle');";
 	 	Kol_schreiben($query);
		$query="SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'neue_Schnittstelle' AND `Eltern_ID` = ?;";
		$stmt = mysqli_prepare($dbDH, $query);
 	 	mysqli_stmt_bind_param($stmt, "i", $Ausser_Betrieb_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
   	mysqli_stmt_close($stmt);
   	$Eltern_ID = $line['Einstellung_ID'];
   	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Script');";
		Kol_schreiben($query);
 	 	$query = "INSERT INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (".$Eltern_ID.",'Intervall');";
		Kol_schreiben($query);
		$Ausser_Betrieb = "neue_Schnittstelle";
	}
	
	//Schnittstellen in ein Listenfeld bringen
	echo "<select style='position: absolute; top: 60px; left: 10px;' id='schnittstellen' name='Schnittstellen' size='7' onclick='markieren(\"schnittstellen\");'>\n";
	$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Schnittstellen_ID." ORDER BY `Parameter` ASC;";
   $stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   	if($line['Parameter'] == $Schnittstellen) {
   		echo "<option selected>".$line['Parameter']."</option>\n";
   		$verst_Feld = "<input name='alter_Schnittstellenname' id='alter_schnittstellenname' value='".$line['Parameter']."' type='hidden'>\n";
   	} else {
			echo "<option>".$line['Parameter']."</option>\n";
		}
	}
	echo "</select>\n";
	
	echo "<select style='position: absolute; top: 60px; left: 300px;' id='ausser_Betrieb' name='Ausser_Betrieb' size='7' onclick='markieren(\"ausser_Betrieb\");'>\n";
	$query="SELECT `Parameter` FROM `Einstellungen` WHERE `Eltern_ID` = ".$Ausser_Betrieb_ID." ORDER BY `Parameter` ASC;";
   $stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
   while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
   	if($line['Parameter'] == $Ausser_Betrieb) {
   		echo "<option selected>".$line['Parameter']."</option>\n";
   		$verst_Feld = "<input name='alter_Schnittstellenname' id='alter_schnittstellenname' value='".$line['Parameter']."' type='hidden'>\n";
   	} else {
			echo "<option>".$line['Parameter']."</option>\n";
		}
	}
	echo "</select>\n";
	echo $verst_Feld;
	echo "<div  style='position: absolute; top: 60px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[3]."' type='submit' style='width: 100px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 92px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[4]."' type='submit' style='width: 100px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 156px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[5]."' type='submit' style='width: 75px; border-left: 0px;'></div>\n";
	echo "<div  style='position: absolute; top: 188px; left: 150px;'><input class='Schalter_Element' name='speichern' value='".$Text[6]."' type='submit' style='width: 75px; border-left: 0px;'></div>\n";
	
	mysqli_stmt_close($stmt);
	//Felder füllen
	if($Schnittstellen > "") {
		$Eltern_ID = $Schnittstellen_ID;
		$SName = $Schnittstellen;
	} else {
		$Eltern_ID = $Ausser_Betrieb_ID;
		$SName = $Ausser_Betrieb;
	}
	$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." AND `Parameter` = '".$SName."';";
	$stmt = mysqli_prepare($dbDH, $query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
  	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
  	$Eltern_ID = $line['Einstellung_ID'];
  	echo "<input name='Eltern_ID' id='einstellung_id' value='".$Eltern_ID."' type='hidden'>\n";
	echo "<input name='Parameter' id='parameter' value='' type='hidden'>\n";
	if($Schnittstellen > "" or $Ausser_Betrieb > "") {
		echo "<table class='Text_einfach' style='position: absolute; top: 280px; left: 10px;' cellspacing='3px'>\n";
		echo "<tr><td><b>".$Text[7]."</b></td><td><b>".$Text[8]."</b></td><td><b>".$Text[9]."</b></td><td></td></tr>\n";
  		if(strlen($line['Parameter']) > 0) { echo "<tr><td align='right' class='Text_fett'>".$Text[10]."</td><td><input class='Text_Element' name='Schnittstellenname' id='schnittstellenname' value='".$line['Parameter']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",0);'></td><td><input class='Text_Element' name='Schnittstellenname_Zusatz' id='schnittstellenname_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",0);'></td></tr>\n";}
		$Schnittstellenname = $line['Parameter'];
		mysqli_stmt_close($stmt);
		$query = "SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ".$Eltern_ID." ORDER BY `Einstellung_ID` ASC;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			if($line['Parameter'] == "Intervall" || $line['Parameter'] == "Script") {
				echo "<tr><td align='right' class='Text_fett'>".$line['Parameter']."</td><td><input class='Text_Element' name='".$line['Parameter']."' id='".$line['Parameter']."' value='".$line['Wert']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Text_Element' name='".$line['Parameter']."_Zusatz' id='".$line['Parameter']."_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td></td></tr>\n";
			} else {  
  				echo "<tr><td align='right' class='Text_fett'>".$line['Parameter']."</td><td><input class='Text_Element' name='".$line['Parameter']."' id='".$line['Parameter']."' value='".$line['Wert']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Text_Element' name='".$line['Parameter']."_Zusatz' id='".$line['Parameter']."_zusatz' value='".$line['Zusatz']."' type='Text' onblur='abspeichern(\"".$line['Parameter']."\",1);'></td><td><input class='Schalter_Element' id='entfernen' name='Entfernen' value='".$Text[5]."' type='button' onclick='Parameter_entfernen(\"".$line['Parameter']."\");'></td></tr>\n";
  			}
		}
		mysqli_stmt_close($stmt);
		echo "<div style='position: absolute; top: 230px; left: 0px; width: 450px;'><hr></div>\n";
		echo "<div class='Text_einfach' style='position: absolute; top: 250px; left: 150px;'><font size='+1'><b>".$Text[7]."</b></font></div>\n";
		echo "<tr><td><hr></td><td><hr></td><td><hr></td></tr>\n";
		echo "<tr><td align='right' class='Text_fett'>".$Text[11]."</td><td><input class='Schalter_Element' id='neu' name='Neu' value='".$Text[12]."' type='button' onclick='neuer_Parameter(".$Eltern_ID.");'></td></tr>\n";
	}
	echo "</table>\n";
	if($Points_bearbeiten == $Text[13]) {
		//ggf die Systempoints anpassen
		if ($alter_Schnittstellenname != $Schnittstellenname) {
			//Status-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) { 
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'sy_".$Schnittstellenname."_status' WHERE `Pointname` = 'sy_".$alter_Schnittstellenname."_status';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'sy_".$Schnittstellenname."_status' WHERE `Tagname` = 'sy_".$alter_Schnittstellenname."_status';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Werte pro Stunde-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) {
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'Wph_".$Schnittstellenname."' WHERE `Pointname` = 'Wph_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'Wph_".$Schnittstellenname."' WHERE `Tagname` = 'Wph_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Zeit seit letztem Wert-Point
			$query = "SELECT `Pointname` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) == 0) {
				$SQL_Text = "UPDATE `Points` SET `Pointname` = 'ZslW_".$Schnittstellenname."' WHERE `Pointname` = 'ZslW_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
				$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'ZslW_".$Schnittstellenname."' WHERE `Tagname` = 'ZslW_".$alter_Schnittstellenname."';";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
		}
		//Status-Point
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) == 0) {
			$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'sy_".$Schnittstellenname."_status', 'Status von ".$Schnittstellenname."', 0, 'watchdog', 1, 1, 0, 3600, '".$Rechner."', 1, 'double', 1, 0, 1, 0, 0, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
        	Kol_schreiben($SQL_Text);
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[0] = $line["Point_ID"];
			$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('sy_".$Schnittstellenname."_status', '/', ".$Point[0].");";
        	Kol_schreiben($SQL_Text);
			$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[0].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
      	Kol_schreiben($SQL_Text);
      } else {
      	$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[0] = $line["Point_ID"];
			$SQL_Text = "UPDATE `Points` SET `Description`='Status von ".$Schnittstellenname."', `Interface`='watchdog',`archive`=1,`step`=1,`compression`=0,`minarch`=3600,`Info`='".$Rechner."',`Property_1`=1,`Point_Type`='double',`Dezimalstellen`=1,`Scale_min`=0,`Scale_max`=1,`Intervall`=0,`Mittelwerte`=0 WHERE `Point_ID` = ".$Point[0].";";
			Kol_schreiben($SQL_Text);
			$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'sy_".$Schnittstellenname."_status' WHERE `Point_ID` = ".$Point[0].";";
			Kol_schreiben($SQL_Text);
		}
		mysqli_stmt_close($stmt);
		//Werte pro Stunde-Point
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
		$stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) == 0) {
			$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'Wph_".$Schnittstellenname."', 'Werte pro h', 0, 'System', 1, 0, 0, 28800, '".$Schnittstellenname.": Werte_pro_h_Point', 1, 'double', 2, 0, 500, 0, 1, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
			Kol_schreiben($SQL_Text);
     		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
     		$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[1] = $line["Point_ID"];
			$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('Wph_".$Schnittstellenname."', '/', ".$Point[1].");";
			Kol_schreiben($SQL_Text);
			$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[1].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
			Kol_schreiben($SQL_Text);
		} else {
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'Wph_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Point[1] = $line["Point_ID"];
			$SQL_Text = "UPDATE `Points` SET `Description`='Werte pro h',`Interface`='System',`archive`=1,`step`=0,`compression`=0,`minarch`=28800,`Info`='".$Schnittstellenname.": Werte_pro_h_Point',`Property_1`=1,`Point_Type`='double',`Dezimalstellen`=2,`Scale_min`=0,`Scale_max`=500,`Intervall`=0,`Mittelwerte`=1 WHERE `Point_ID` = ".$Point[1].";";
			Kol_schreiben($SQL_Text);
			$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'Wph_".$Schnittstellenname."' WHERE `Point_ID` = ".$Point[1].";";
			Kol_schreiben($SQL_Text);
		}
		mysqli_stmt_close($stmt);
		//Zeit seit letztem Wert-Point
		if ($Schnittstellenname !="comp"){
			$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
			$stmt = mysqli_prepare($dbDH,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$Satz = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$query = "SELECT max(`Property_1`) as Location1 FROM `Points` WHERE `Interface` = 'calc';";
			$stmt = mysqli_prepare($dbDH,$query);
 			mysqli_stmt_execute($stmt);
			$result1 = mysqli_stmt_get_result($stmt);
			$Satz1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
			if (mysqli_num_rows($result) == 0) {
           	$SQL_Text = "INSERT INTO `Points`(`Path`, `Pointname`, `Description`, `scan`, `Interface`, `archive`, `step`, `compression`, `minarch`, `Info`, `Property_1`, `Point_Type`, `Dezimalstellen`, `Scale_min`, `Scale_max`, `Intervall`, `Mittelwerte`, `first_value`) VALUES ('/', 'ZslW_".$Schnittstellenname."', 'Zeit seit letztem Wert', 0, 'calc', 1, 1, 0, 3600, '', ".strval(floatval($Satz1[0]["Location1"]) + 1).", 'calc', 0, 0, 1000, 600, 0, '".strftime('%Y-%m-%d %H:%M:%S', time())."');";
           	Kol_schreiben($SQL_Text);
           	$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
           	$stmt = mysqli_prepare($dbDH,$query);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Point[2] = $line["Point_ID"];
				$SQL_Text = "INSERT INTO `Tagtable`(`Tagname`, `Path`, `Point_ID`) VALUES ('ZslW_".$Schnittstellenname."', '/', ".$Point[2].");";
				Kol_schreiben($SQL_Text);
           	$SQL_Text = "UPDATE `Points` SET `Property_1`='time.time()-ZP(".$Point.")' WHERE `Point_ID` = ".$Point.";";
				Kol_schreiben($SQL_Text);
           	$SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (".$Point[2].", '".strftime('%Y-%m-%d %H:%M:%S', time())."', 0);";
           	Kol_schreiben($SQL_Text);
		    } else {
				$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'ZslW_".$Schnittstellenname."';";
           	$stmt = mysqli_prepare($dbDH,$query);
 				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$Point[2] = $line["Point_ID"];
				$SQL_Text = "UPDATE `Points` SET `Description`='Zeit seit letztem Wert', `Interface`='calc',`archive`=1,`step`=1,`compression`=0,`minarch`=3600,`Info`='time.time()-ZP(".$Point.")',`Point_Type`='calc',`Dezimalstellen`=0,`Scale_min`=0,`Scale_max`=1000,`Intervall`=600,`Mittelwerte`=0 WHERE `Point_ID` = ".$Point[2].";";
           	Kol_schreiben($SQL_Text);
           	$SQL_Text = "UPDATE `Tagtable` SET `Tagname` = 'ZslW_".$Schnittstellenname."' WHERE `Point_ID` = ".$Point[2].";";
				Kol_schreiben($SQL_Text);
			}
			mysqli_stmt_close($stmt);
			//Meldung ausgeben
			Meldung_schreiben("comp", "Points einlesen", "Server");
			Meldung_schreiben("watchdog", "abschalten", $Script_Zusatz);
			Meldung_schreiben("watchdog", "einschalten", $Script_Zusatz);
		}
	} else {
		$query = "SELECT `Point_ID` FROM `Points` WHERE `Pointname` = 'sy_".$Schnittstellenname."_status' OR `Pointname` = 'Wph_".$Schnittstellenname."' OR `Pointname` = 'ZslW_".$Schnittstellenname."';";
      $stmt = mysqli_prepare($dbDH,$query);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$i = 0;
		while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$Point[$i] = $line["Point_ID"];
			$i = $i + 1;
		}
		mysqli_stmt_close($stmt);
	}
	$Points_bearbeiten = "";
	
	//Tags anzeigen
	echo "<div style='position: absolute; top: 60px; left: 500px;'>\n<table>";
	for($i=0;$i < 3; $i++) {
		$query= "SELECT Tag_ID, Tagname, Dezimalstellen, Description, EUDESC FROM Tags WHERE Point_ID = ? ORDER BY Tag_ID ASC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Point[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line_Tag = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		$query = "SELECT Value, Timestamp FROM akt WHERE Point_ID =? ORDER BY Timestamp DESC LIMIT 1;";
		$stmt = mysqli_prepare($dbDH, $query);
		mysqli_stmt_bind_param($stmt, "i", $Point[$i]);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);	
		$line_Value = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Wert=round($line_Value['Value'],$line_Tag["Dezimalstellen"]);
		$Zeitpunkt = $line_Value['Timestamp'];
		$Tagname = html_entity_decode($line_Tag["Tagname"]);
		$Description = html_entity_decode($line_Tag["Description"]);
		echo"<tr bgcolor='#E5E5E5'><td><a href='javascript:void(0);' onclick='Tagdetails(".$line_Tag["Tag_ID"].");'>";
		echo $Tagname."</a></td><td>".$Description."</td><td>".$Zeitpunkt."</td><td width=60pt>".$Wert."</td><td>".$line_Tag["EUDESC"]."</td><td><a href='../Trend.php?Tag_ID=".$line_Tag["Tag_ID"]."&Zeitpunkt=jetzt' target='_blank'>".$Text[14]."</a></td></tr>";
	}
	mysqli_close($dbDH);
?>
</table></div>
</body>
<script type="text/javascript">
var panel;
function Tagdetails(Tag_ID) {
	try {panel.close('tagdetails');} catch (err) {}
	jQuery.ajax({
		url: "./DH_Tagdetails.php?Tag_ID=" + Tag_ID,
		success: function (html) {
  			strReturn = html;
		},
		async: false
	});
	panel = jsPanel.create({
		id: 'tagdetails',
		position: 'left-top 10 10',
		theme: 'primary',
		contentSize: '600 600',
		headerTitle: 'Tagdetails',
		content: strReturn,
	});
}

function Parameter_entfernen(Parameter) {
	document.Phpform.elements.Parameter.value = Parameter;
	var SQL = "DELETE FROM `Einstellungen` WHERE `Eltern_ID` = " + document.Phpform.elements.Eltern_ID.value + " AND `Parameter` = '" + document.Phpform.elements.Parameter.value + "';"
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function neuer_Parameter(Eltern_ID) {
	var Parameter = prompt(<?php echo "'".$Text[15]."'";?>);
	var SQL = "Insert INTO `Einstellungen` (`Eltern_ID`, `Parameter`) VALUES (" + document.Phpform.elements.Eltern_ID.value + ", '" + Parameter + "');"
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function abspeichern(Parameter,Knoten) {
	if (Knoten == 0) {
		var Feld = document.Phpform.elements["schnittstellenname"];
		var Zusatz = document.Phpform.elements["schnittstellenname_zusatz"];
		if (document.Phpform.elements["schnittstellen"].value > "") {
			var SQL = "UPDATE `Einstellungen` SET `Parameter` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.schnittstellen_id.value + " and `Parameter` = '" + Parameter + "';"
		} else {
			var SQL = "UPDATE `Einstellungen` SET `Parameter` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.ausser_Betrieb_id.value + " and `Parameter` = '" + Parameter + "';"
		}
		var newOption = new Option(Feld.value,Feld.value);
		document.Phpform.elements.ausser_Betrieb.add(newOption,undefined);
		document.Phpform.elements.ausser_Betrieb.value = Feld.value;
		document.Phpform.elements.points_bearbeiten.value = <?php echo "'".$Text[13]."'";?>;
	} else {
		var Feld = document.Phpform.elements[Parameter];
		var Zusatz = document.Phpform.elements[Parameter + "_Zusatz"];
		var SQL = "UPDATE `Einstellungen` SET `Wert` = '" +  Feld.value + "', `Zusatz` = '" +  Zusatz.value + "' where `Eltern_ID` = " + document.Phpform.elements.Eltern_ID.value + " and `Parameter` = '" + Parameter + "';"
	}
	jQuery.ajax({
		url: "./SQL_ausfuehren.php?DB=DH",
		type: 'POST',
		data: {SQL_Text: SQL},
		async: false
	});
	document.getElementById("phpform").submit();
}
function markieren(Liste) {
	if (Liste == "schnittstellen") {
		document.getElementById("ausser_Betrieb").value="";
	} else {
		document.getElementById("schnittstellen").value="";
	}
	document.getElementById("phpform").submit();
}
</script>
</html>

