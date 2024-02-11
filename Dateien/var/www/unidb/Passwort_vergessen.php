<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<?php
foreach($_GET as $key => $value){
	${$key}=$value;
}
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>";
echo "<body class='allgemein'>";
//ini_set( 'session.use_only_cookies', '1','SameSite=None', 'Secure' );
ini_set( 'session.use_trans_sid', '0' );
session_start();
if ($_POST['Lang'] == null) {
	$_SESSION['Sprache'] = $Lang;
} else {
	$_SESSION['Sprache'] = $_POST['Lang'];
}
include('./funktionen.inc.php');
require_once('./class.phpmailer.php');
require_once('./class.smtp.php');
$Text = Translate("Passwort_vergessen.php");
function random_string() {
	if(function_exists('random_bytes')) {
		$bytes = random_bytes(16);
		$str = bin2hex($bytes); 
	} else if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(16);
		$str = bin2hex($bytes); 
	} else if(function_exists('mcrypt_create_iv')) {
 		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes); 
	} else {
		$str = md5(uniqid('hbtztrTZ753@gji00!huuh)(ö', true));
	} 
	return $str;
}
 
$showForm = true;

if(isset($_GET['send'])) {
	$db = db_connect();
	if(!isset($_POST['email']) || empty($_POST['email'])) {
		$error = "<b>".$Text[1]."</b>";
	} else {
		$query = "SELECT * FROM `User` WHERE `eMail` = ?;";
		$stmt = mysqli_prepare($db,$query);
		mysqli_stmt_bind_param($stmt, "s", $_POST['email']);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$Full_Name = $line["Full_Name"];
		mysqli_stmt_close($stmt);
		if($line["User_ID"] === null) {
			$error = "<b>".$Text[2]."</b>";
		} else {
			$passwortcode = random_string();
			$abfrage = "UPDATE `User` SET `Passwortcode` = ?, `Passwortcode_Zeit` = NOW() WHERE `User_ID` = ?;";
			$stmt = mysqli_prepare($db,$abfrage);
			mysqli_stmt_bind_param($stmt, "si", sha1($passwortcode), $line["User_ID"]);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$mail = new PHPMailer();
			//Eltern_ID auslesen
			$query = "SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'eMail Konfiguration';";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$Eltern_ID = $line["Einstellung_ID"];
			mysqli_stmt_close($stmt);
			//eMail Koniguration
			$query="SELECT * FROM `Einstellungen` WHERE `Eltern_ID` = ?;";
			$stmt = mysqli_prepare($db,$query);
			mysqli_stmt_bind_param($stmt, "i", $Eltern_ID);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				if($line["Parameter"] == "Host") {$mail->Host = $line["Wert"];}
				if($line["Parameter"] == "CharSet") {$mail->CharSet = $line["Wert"];}
				if($line["Parameter"] == "SMTPAuth" and $line["Wert"] == "true") {$mail->SMTPAuth = true;}
				if($line["Parameter"] == "SMTPSecure") {$mail->SMTPSecure = $line["Wert"];}
				if($line["Parameter"] == "Port") {$mail->Port = $line["Wert"];}
				if($line["Parameter"] == "Username") {$mail->Username = $line["Wert"];}
				if($line["Parameter"] == "Password") {$mail->Password = $line["Wert"];}
				if($line["Parameter"] == "SetFrom") {$mail->SetFrom($line["Wert"]);}
				if($line["Parameter"] == "addReplyTo") {$mail->addReplyTo($line["Wert"]);}
				if($line["Parameter"] == "addAddress") {$mail->addAddress($line["Wert"]);}
				if($line["Parameter"] == "url_passwortcode") {$url_passwortcode = $line["Wert"].'/Passwort_neu.php?code='.$passwortcode;}
			}
			mysqli_stmt_close($stmt);
         //$mail->SMTPDebug = 1; // Kann man zu debug Zwecken aktivieren
			$mail->IsSMTP();
         $mail->isHTML(true);
			$mail->Subject = $Text[10];
			$mailtext = "<html>".$Text[3]." ".$Full_Name.",<br><br>".$Text[4]."<br><br><a href='".$url_passwortcode."'>".$url_passwortcode."</a><br><br>";
			$mailtext = $mailtext.$Text[5]."<br><br>".$Text[11]."<br>".$Text[12]."</html>";
			$mail->Body = $mailtext;
			$mail->Send();
 			echo $Text[6];
			$showForm = false;
		}
	}
}
if($showForm) {
	echo "<h1>".$Text[7]."</h1><span class='Text_einfach'>".$Text[8]."</span><br><br>";
	if(isset($error) && !empty($error)) {echo $error;}
	echo "<form action='?send=1' method='post'>";
	echo "<input type='hidden' name='Lang' value='".$_SESSION['Sprache']."'>";
	echo "<table><tr><td class='Text_einfach'>eMail:</td><td><input class='Text_Element' style='height: 20px;width: 240px;' type='email' name='email' value='";
	echo isset($_POST['email']) ? htmlentities($_POST['email']) : '';
	echo "'></td></tr><tr style='height: 35px;'><td></td><td><input class='Schalter_Element' type='submit' value='".$Text[9]."'></td></tr></table</form>";
}
?>
</body></html>
