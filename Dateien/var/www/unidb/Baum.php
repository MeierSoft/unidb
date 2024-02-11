<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
</head>
<body>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	include 'mobil.php';
	if ($mobil==1){
		header("location: Baum2.php");
	}else{
		header("location: Baum.html");
	}
	mysqli_close($db);
?>
</body>
</html>
