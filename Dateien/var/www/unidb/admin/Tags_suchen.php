<html>
<head>
<?php
	//Get und POST einlesen
	foreach($_GET as $key => $value){
${$key}=$value;
	}
	session_start();
	include('./funktionen.inc.php');
	if($_SESSION['admin'] != 1) {exit;}
	header("Content-Type: text/html; charset=utf-8");
	$Text = Translate("Tags_suchen.php");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "<title>".$Text[0]."</title>\n";
	echo "</head>\n";
	echo "<body class='allgemein'>\n";
	require '../conf_DH.php';
	echo "<div style='position: absolute; left: 10px; top: 10px;'><form name='Tag_finden'>\n";
	echo "<b>".$Text[1]."</b><br>\n";
	echo "<select id='Liste' name='Ergebnis' size='15' onclick='uebertragen()'>\n";
	$Pointname = htmlentities(mysqli_real_escape_string($dbDH, $Pointname));
	$Path = htmlentities(mysqli_real_escape_string($dbDH, $Path));
	$query = "SELECT * FROM `Tags` WHERE `Tagname` LIKE ? ORDER BY `Path` ASC, `Tagname` ASC;";
	$stmt = mysqli_prepare($dbDH,$query);
	mysqli_stmt_bind_param($stmt, "s", $Tagname);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
if($line["Tag_ID"] > 0) {
	$Tagname = html_entity_decode($line["Tagname"]);
	$Description = html_entity_decode($line["Description"]);
	$Path = html_entity_decode($line["Path"]);
	$Interface = html_entity_decode($line["Interface"]);
	echo "<option value='".$Path.$Tagname."'>".$Path." - ".$Tagname." - ".$Description." - ".$Interface."</option>";
}
	}
	echo "</select>\n";
	echo "</form></div>\n";
	mysqli_stmt_close($stmt);
	mysqli_close($dbDH)
?>
</body>
</html>
