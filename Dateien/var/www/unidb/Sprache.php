<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo $_SESSION['Sprache'];
?>