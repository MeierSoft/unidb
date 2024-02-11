<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	if($Modus == "lesen") {
		echo $_SESSION['Hilfeliste'];
	} else {
		$_SESSION['Hilfeliste'] = $_SESSION['Hilfeliste'].",".$Hilfe_ID;
	}
?>