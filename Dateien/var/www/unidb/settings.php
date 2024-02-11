<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("settings.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
echo "<h3>".$Text[0]."</h3>\n";

//Daten schreiben
if ($speichern==$Text[1]){
	//Stammdaten ggf ändern
	$Meldung_Benutzer="";
	$Abfrage = "SELECT * FROM `User` WHERE `User_ID`=".$_SESSION['User_ID'].";";
	$req_Abfrage = mysqli_query($db, $Abfrage);
	$line_Abfrage = mysqli_fetch_array($req_Abfrage);
	//Sprache
	$Sprache = "DE";
	$_SESSION['Sprache'] = "DE";
	if($_POST['S_EN'] == "EN"){
		$Sprache = "EN";
		$_SESSION['Sprache'] = "EN";
	}
	if($_POST['S_NL'] == "NL"){
		$Sprache = "NL";
		$_SESSION['Sprache'] = "NL";
	}
	if($_POST['S_FR'] == "FR"){
		$Sprache = "FR";
		$_SESSION['Sprache'] = "FR";
	}

	if ($line_Abfrage['Sprache']!=$Sprache){
		$sql = "UPDATE `User` SET `Sprache` = ? WHERE `User_ID` = ?;";
		uKol_schreiben(1, $sql, "si",[$Sprache, $_SESSION['User_ID']]);
	}
	//Wenn ein Server nicht erreichbar ist, dann die Aktion abbrechen
	if($_SESSION['uV_Daten'] == "_" || $_SESSION['uV_Daten'] == null || $_SESSION['uV_Daten'] == NULL) {
		$_SESSION['uV_Daten'] = uKol_verbinden();
	}
	for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
		if($_SESSION['uV_Daten'][$i]["Database"] == "") {
			echo $Text[20];
			exit;
		}
	}
	//voller Name
	if ($line_Abfrage['Full_Name']!=$_POST['Full_Name']){
		if (strlen($_POST['Full_Name'])<5){ 
			$Meldung_Benutzer = $Meldung_Benutzer.$Text[4]."<br>";
		}else{ 
			$Test = "SELECT `User_ID` FROM `User` WHERE `Full_Name`='".$_POST['Full_Name']."';";
			$req_Test = mysqli_query($db, $Test);
			$line_Test = mysqli_fetch_array($req_Test);
			if ($line_Test['User_ID']==NULL) {
				$sql = "UPDATE `User` SET `Full_Name` = ? WHERE `User_ID` = ?;";
				uKol_schreiben(1, $sql, "si",[$_POST['Full_Name'], $_SESSION['User_ID']]);
			}else{
				$Meldung_Benutzer = $Meldung_Benutzer.$Text[5]."<br>";
			}
		}
	}
	//eMail
	if ($line_Abfrage['eMail']!=$_POST['eMail']){
		$s = '/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i';
		if(preg_match($s, $_POST['eMail'])){
			$sql = "UPDATE `User` SET `eMail` = ? WHERE `User_ID` = ?;";
			uKol_schreiben(1, $sql, "si",[$_POST['eMail'], $_SESSION['User_ID']]);
		}else{
			$Meldung_Benutzer = $Meldung_Benutzer.$Text[6]."<br>"; 
		 }
	}
	//Passwort
	//Wenn ein Server nicht erreichbar ist, dann die Aktion abbrechen
	if($_SESSION['uV_Daten'] == "_" || $_SESSION['uV_Daten'] == null || $_SESSION['uV_Daten'] == NULL) {
		$_SESSION['uV_Daten'] = uKol_verbinden();
	}
	for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
		if($_SESSION['uV_Daten'][$i]["Database"] == "") {
			echo $Text[21];
			exit;
		}
	}
$_SESSION['uV_Daten'] = uKol_verbinden();
	if ($_POST['Password_1']!=NULL and $_POST['Password_2']!=NULL){
		if ($_POST['Password_1'] != $_POST['Password_2']){
			 $Meldung_Benutzer = $Meldung_Benutzer.$Text[7]."<br>";
		}else{
			if (strlen($_POST['Password_1'])<7){
				$Meldung_Benutzer = $Meldung_Benutzer.$Text[8]."<br>";
			}else{
				$salt = substr( md5( microtime() ), 0, 10 );
				$pw_mit_salt = md5( $_POST['Password_1'] . $salt );
				$sql = "UPDATE `User` SET `Password` = ?, `Password_extension` = '".$salt."' WHERE `User_ID` = ?;";
				uKol_schreiben(1, $sql, "si",[$pw_mit_salt, $_SESSION['User_ID']]);
				//MariaDB User Passwort anpassen
				$sql = "SET PASSWORD = PASSWORD('".$pw_mit_salt."');";
				for ($i=0;$i<count($_SESSION['uV_Daten']);$i++){
					if($_SESSION['uV_Daten'][$i]["Database"] > "") {
						$uVerb = mysqli_connect($_SESSION['uV_Daten'][$i]["IP"],$_SESSION['uV_Daten'][$i]["User"],$_SESSION['uV_Daten'][$i]["Password"],"mysql");
						if ($uVerb == false) {
							$_SESSION['uV_Daten'][$i]["Database"] = "";
						} else {
							mysqli_query($uVerb, 'set character set utf8;');
							$insert = $uVerb->prepare($sql);
							$insert->execute();
							$insert = $uVerb->prepare("flush privileges;");
							$insert->execute();
						}
					}
				}
				$_SESSION['uV_Daten'] = uKol_verbinden();
				$Meldung_Benutzer = $Meldung_Benutzer.$Text[9]."<br>";
			}
		}
	}
	//Thema
	if ($line_Abfrage['Thema']!=$_POST['Thema']){
		$sql = "UPDATE `User` SET `Thema` = ? WHERE `User_ID` = ?;";
		uKol_schreiben(1, $sql, "si",[$_POST['Thema'], $_SESSION['User_ID']]);
		$_SESSION['Thema'] = $_POST['Thema'];
	}
}
//Stammdaten
$Abfrage = "SELECT * FROM `User` WHERE `User_ID`=".$_SESSION['User_ID'].";";
$req_Abfrage = mysqli_query($db, $Abfrage);
$line_Abfrage = mysqli_fetch_array($req_Abfrage);
if ($Meldung_Benutzer>"") echo "<font color='red'>".$Meldung_Benutzer."</font>"; 
echo "<form action='./settings.php' method='post' target='_self'>";
echo "<table cellpadding='3px' class='Text_einfach'>";
$DE = "";
$EN = "";
$NL = "";
$FR = "";
if($line_Abfrage['Sprache'] == "DE") {$DE = " checked";}
if($line_Abfrage['Sprache'] == "EN") {$EN = " checked";}
if($line_Abfrage['Sprache'] == "NL") {$NL = " checked";}
if($line_Abfrage['Sprache'] == "FR") {$FR = " checked";}
echo "<tr><td align='right'>".$Text[10]."</td><td>".$line_Abfrage['UserName']."</td></tr>";
echo "<tr><td align='right'>".$Text[11]."</td><td><input class='Text_Element' name='Full_Name' type='text' value='".$line_Abfrage['Full_Name']."'></td></tr>";
echo "<tr><td align='right'><br><br>".$Text[19]."</td><td><table><tr><td align='center' width='40px'><img src='Bilder/Flaggen/DE.png' alt='deutsch'><br><input type='checkbox' id='s_DE' name='S_DE' value='DE'".$DE." onchange='umschalten(\"DE\");'><br>DE</td>";
echo "<td align='center' width='40px'><img src='Bilder/Flaggen/EN.png' alt='english'><br><input type='checkbox' id='s_EN' name='S_EN' value='EN'".$EN." onchange='umschalten(\"EN\");'><br>EN</td>";
echo "<td align='center' width='40px'><img src='Bilder/Flaggen/NL.png' alt='nederlands'><br><input type='checkbox' id='s_NL' name='S_NL' value='NL'".$NL." onchange='umschalten(\"NL\");'><br>NL</td>";
echo "<td align='center' width='40px'><img src='Bilder/Flaggen/FR.png' alt='francais'><br><input type='checkbox' id='s_FR' name='S_FR' value='FR'".$FR." onchange='umschalten(\"FR\");'><br>FR</td></tr></table></td></tr>";
echo "<tr><td align='right'>".$Text[12]."</td><td><input class='Text_Element' name='eMail' type='text' value='".$line_Abfrage['eMail']."'></td></tr>";
echo "<tr><td align='right'>".$Text[13]."</td><td><input class='Text_Element' name='Password_1' type='password' value=''></td></tr>";
echo "<tr><td align='right'>".$Text[14]."</td><td><input class='Text_Element' name='Password_2' type='password' value=''></td></tr>";
echo "<tr><td align='right'>".$Text[16]."</td><td><select class='Text_Element' style='height: 22px;' name='Thema' type='text' value='".$line_Abfrage['Thema']."' size='1'>";
$query = "SELECT DISTINCT(`Thema`) AS Thema FROM `Themen_Parameter`, `Themen` ORDER BY `Thema` ASC;";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	if($line['Thema'] == $_SESSION["Thema"]) {
	echo "<option selected>".$line['Thema']."</option>";
	} else {
		echo "<option>".$line['Thema']."</option>";
	}
}
echo "</select></td></tr>";
mysqli_stmt_close($stmt);
echo "<tr height='50' valign='bottom'><td></td><td><input class='Schalter_Element' value='".$Text[18]."' type='submit' name='speichern'></td></tr></table>";
echo "</form>";
mysqli_close($db);
?>
</span>
<script type="text/javascript">
function umschalten(Sp) {
	if (Sp != "DE") {document.getElementById("s_DE").checked = false;}
	if (Sp != "EN") {document.getElementById("s_EN").checked = false;}
	if (Sp != "NL") {document.getElementById("s_NL").checked = false;}
	if (Sp != "FR") {document.getElementById("s_FR").checked = false;}
}
</script>
</body>
</html>
