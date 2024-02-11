<?php
	session_start();
	include('../Sitzung.php');
	if($_SESSION['admin'] != 1) {exit;}
	include('./DH_Admin_func.php');
	Meldung_schreiben($Schnittstelle, $Meldung, $Rechner);
?>