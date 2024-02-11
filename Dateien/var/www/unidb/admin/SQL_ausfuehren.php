<?php
session_start();
include('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}

if($DB == "DH_unidb" or $DB == "DH") {
	Kol_schreiben($SQL_Text);
}
if($DB == "DH_unidb" or $DB == "unidb") {
	uKol_schreiben(1,$SQL_Text,"","");
}
?>