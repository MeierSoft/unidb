<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<?php
	include('../Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	include '../mobil.php';
	if ($mobil==1){
		echo "<link rel='StyleSheet' href='../mobil_dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='../mobil_dtree.js'></script>";
	} else {
		echo "<link rel='StyleSheet' href='../dtree.css' type='text/css' />";
		echo "<script type='text/javascript' src='../dtree.js'></script>";
		echo "</head><body class='BaumHG'>";
	}
?>
<title>Einstellung verschieben</title>

<h3>Einstellung verschieben</h3>
<div class="dtree">
	<p><a href="javascript: d.openAll();">alle &ouml;ffnen</a> | <a href="javascript: d.closeAll();">alle schliessen</a></p>
<?php
	echo "<script type='text/javascript'>";
	echo "d = new dTree('d');";
	echo "d.add(0,-1,'Einstellungen','Einstellungen2.php?Aktion=verschieben&Einstellung_ID=".$Einstellung_ID."&Eltern_ID=0&DB=".$DB."','','Gruppenbaum');";
	include '../conf_unidb.php';
	$query = "SELECT * FROM `Einstellungen` ORDER BY `Eltern_ID`, `Parameter` asc;";
	if($DB == "DH") {
		include '../conf_DH_schreiben.php';
		$req = mysqli_query($dbDH,$query);
	} else {
		include '../conf_unidb.php';
		$req = mysqli_query($db,$query);
	}
	while ($line = mysqli_fetch_array($req, MYSQLI_ASSOC)) {
		if ($mobil==1){
				echo "d.add(".$line["Einstellung_ID"].",".$line["Eltern_ID"].",'".$line["Parameter"]."','Einstellungen2.php?Aktion=verschieben&Einstellung_ID=".$Einstellung_ID."&Eltern_ID=".$line["Einstellung_ID"]."&DB=".$DB."','','_blank');";
			} else {
				echo "d.add(".$line["Einstellung_ID"].",".$line["Eltern_ID"].",'".$line["Parameter"]."','Einstellungen2.php?Aktion=verschieben&Einstellung_ID=".$Einstellung_ID."&Eltern_ID=".$line["Einstellung_ID"]."&DB=".$DB."','','Gruppenbaum');";
			}
	}
	if($DB == "DH") {
		mysqli_close($dbDH);
	} else {
		mysqli_close($db);
	}
?>
document.write(d);
</script>
</div>
</body>
</html>
