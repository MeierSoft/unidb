<?php
include('./funktionen.inc.php');
foreach($_GET as $key => $value){
	${$key}=$value;
}
//ini_set( 'session.use_only_cookies', '1','SameSite=None', 'Secure' );
ini_set( 'session.use_trans_sid', '0' );
session_start();

//Abfrage des Nutzers
$db = db_connect();
$query = "SELECT * FROM `User` WHERE `Passwortcode` = ?;";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", sha1($_GET['code']));
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);

//Sprache einstellen
$_SESSION['Sprache'] = $line["Sprache"];
$Text = Translate("Passwort_neu.php");
if(!isset($_GET['code'])) {
	die($Text[0]);
}
$code = $_GET['code'];
//Überprüfe dass ein Nutzer gefunden wurde und dieser auch ein Passwortcode hat
if(mysqli_num_rows($result) == 0) {
	die($Text[1]);
}
 
if(strtotime($line["Passwortcode_Zeit"]) < (time()-3600)) {
	die($Text[2]);
}
mysqli_stmt_close($stmt); 
//Der Code war korrekt, der Nutzer darf ein neues Passwort eingeben
 
if(isset($_GET['send'])) {
	$passwort = $_POST['passwort'];
	$passwort2 = $_POST['passwort2'];
	if($passwort != $passwort2) {
		echo $Text[3];
	} else {
		// Wurde ein ausreichend langes Passwort angegeben?
		if (strlen($passwort)<7) {
		 	echo $Text[9];
			echo "<br><br><a href='Passwort_neu.php?code=".htmlentities($code)."'>".$Text[8]."</a>";
		 	exit;
		}
		$salt = substr(md5( microtime() ), 0, 10);
		$pw_mit_salt = md5($_POST['passwort'].$salt);
		$query = "UPDATE `User` SET `Password` = ?, `Passwortcode` = NULL, `Passwortcode_Zeit` = NULL, `Password_extension` = ? WHERE `User_ID` = ?;";
		uKol_schreiben(1,$query, "ssi", [$pw_mit_salt, $salt, $line["User_ID"]]);
		header("location: ./index.php");
	}
}
 
echo "<h1>".$Text[4]."</h1>";
echo "<form action='?send=1&code=".htmlentities($code)."' method='post'>";
echo $Text[5]."<br>";
echo "<input type='password' name='passwort'><br><br>";
echo $Text[6]."<br>";
echo "<input type='password' name='passwort2'><br><br>";
echo "<input type='submit' value='".$Text[7]."'>";
?>
</form>
