<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
</head>
<body>
<?php
	include '../mobil.php';
	if ($mobil==1){
		header("location: Einstellungen2.php?DB=".$DB);
	}else{
		header("location: Einstellungen.html");
	}
?>
</body>
</html>
