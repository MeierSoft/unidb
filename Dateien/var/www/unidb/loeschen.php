<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	$Text = Translate("loeschen.php");
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	if($anzeigen == 0) {
		echo $Text[4];
		exit;
	}
	include 'mobil.php';
	$query = "SELECT Bezeichnung FROM `Baum` WHERE `Baum_ID`=".$Baum_ID." AND `Server_ID` = ".$Server_ID.";";
	$req = mysqli_query($db,$query);
	$line = mysqli_fetch_array($req, MYSQLI_ASSOC);
	echo $Text[1]."<br><br>";
	echo "<b><font size=+1>".$line["Bezeichnung"]."</font></b><br><br>";
	echo "<form action='Baum2.php' method='post' target='Baum'>";
	echo"<input value='".$Text[2]."' type='submit' name='Aktion'>";
	echo "&nbsp&nbsp&nbsp";
	echo "<input value='".$Text[3]."' type='button' onclick='abbrechen();'>";
	echo "<input id='Baum_ID' value='".$Baum_ID."' type='hidden' name='Baum_ID'>";
	echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
	echo "</form>";
	// schliessen der Verbindung
	mysqli_close($db);
	echo "<script type='text/javascript'>\n";
	echo "function abbrechen() {\n";
		if ($mobil==1){
			echo"close();\n";
		} else {
			echo"window.history.back();\n";
		}
echo "}\n</script>\n";
?>
</body>
</html>