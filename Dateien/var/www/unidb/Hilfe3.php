<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script type="text/javascript" src="./Hilfe.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<?php
	header("Content-Type: text/html; charset=utf-8");
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head>";
	if($nreg != 1) {
		if(strlen($_SESSION['Hilfeliste']) == 0) {
			$_SESSION['Hilfeliste'] = strval($Hilfe_ID);
		} else {
			$_SESSION['Hilfeliste'] = $_SESSION['Hilfeliste'].",".$Hilfe_ID;
		}
	}
	$query = "SELECT `".$_SESSION["Sprache"]."` FROM `Hilfe` WHERE `Hilfe_ID` = ?;";
	$stmt = mysqli_prepare($db,$query);
	mysqli_stmt_bind_param($stmt, "i", $Hilfe_ID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo "<div style='width: 90%;'>".html_entity_decode($line[$_SESSION["Sprache"]])."</div>";
	mysqli_stmt_close($stmt);
	mysqli_close($db);
?>
