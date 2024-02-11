<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
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
	$Text = Translate("Link.php");
	$query = "SELECT column_get(`Inhalt`, 'Ziel' as CHAR) as `Ziel`, `Server_ID`, `Bezeichnung` FROM `Baum` WHERE `Baum_ID` = ".$Baum_ID." AND `Server_ID` = ".$Server_ID.";";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<title>".$line["Bezeichnung"]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[6];
		exit;
	}
	if($anzeigen == 1) {
		echo "<font size='4'><b>Link&nbsp;&nbsp;&nbsp;&nbsp;</b></font>\n";
		echo "<font size='2'><a href='javascript:void(0);' onclick='Hilfe_Fenster(\"8\");'>".$Text[1]."&nbsp;</a>\n";
		echo "<a href='Link_bearbeiten.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[2]."</a>&nbsp;<a href='loeschen.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."&DB_ID=0' target='_self'>".$Text[3]."</a>&nbsp;<a href='verschieben.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a><br><hr>";
	}
	include ('mobil.php');
	echo "<input id ='mobil' name='mobil' value='".$mobil."' type='hidden'>\n";
	$Ziel = $line["Ziel"];
	$ServerID = intval(substr($Ziel,0,strpos($Ziel,"_")));
	$BaumID = intval(substr($Ziel,strpos($Ziel,"_") + 1,strlen($Ziel)));
	mysqli_stmt_close($stmt);
	//Vorlage des Ziels ermitteln
	$query = "SELECT `Vorlage` FROM `Baum` WHERE `Baum_ID` = ".$BaumID." AND `Server_ID` = ".$ServerID.";";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$Vorlage = $line["Vorlage"];
	if($Vorlage == NULL)	{
		echo $Text[5];
		exit;
	}
	mysqli_stmt_close($stmt);
	//Vorlagendatei fuer das Ziel ermitteln
	$query="select column_get(Eigenschaften, 'Datei' as CHAR) as Datei FROM `Vorlagen` where `Vorlage_ID` = ".$Vorlage.";";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<iframe id = 'iRahmen' src='".$line["Datei"]."?Baum_ID=".$BaumID."&Server_ID=".$ServerID."' marginheight='0px' marginwidth='0px' frameborder='0' height='100%' width='100%'></iframe>";
	echo "<script type='text/javascript'>\n";
	echo "	document.getElementById('iRahmen').height = self.innerHeight;\n";
	echo "</script>";
	mysqli_stmt_execute($stmt);
// schliessen der Verbindung
mysqli_close($db);
?>
</div></div>
</body>
</html>