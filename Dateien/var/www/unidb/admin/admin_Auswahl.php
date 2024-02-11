<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta http-equiv="content-style-type" content="text/css">

<?php
session_start();
include ('../Sitzung.php');
if($_SESSION['admin'] != 1) {exit;}
header("X-XSS-Protection: 1");
echo "<link href='../css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
$Text = Translate("admin_Auswahl.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='BaumHG'>\n";
echo "<font face='Arial'>\n";
echo "<a href='../../phpmyadmin/' target='_blank'>phpMyAdmin</a><br><br>\n";
echo "<br><font size='+1'><b>DH</b></font><br>\n";
echo "<a href='./Point_Tagkonfiguration.php' target='Hauptrahmen'>".$Text[3]."</a><br><br>\n";
echo "<a href='./calc_Konfiguration.php' target='Hauptrahmen'>".$Text[4]."</a><br><br>\n";
echo "<a href='./Schnittstellen.php' target='Hauptrahmen'>".$Text[5]."</a><br><br>\n";
echo "<a href='./Kollektiv.php' target='Hauptrahmen'>".$Text[6]."</a><br><br>\n";
echo "<a href='./DH_Handeingabe.php' target='Hauptrahmen'>".$Text[8]."</a><br><br>\n";
echo "<a href='./DH_Einstellungen.html' target='_blank'>".$Text[10]."</a><br><br>\n";
echo "<a href='../Geraete_Einstellungen.php' target='Hauptrahmen'>".$Text[11]."</a><br><br>\n";
echo "<a href='../Bausteine_verwalten.php' target='_blank'>".$Text[12]."</a><br><br>\n";
echo "<a href='./Meldungen.php' target='Hauptrahmen'>".$Text[13]."</a><br><br>\n";
echo "<a href='./Puffer.php' target='Hauptrahmen'>".$Text[14]."</a><br><br>\n";
echo "<a href='./Log.php' target='Hauptrahmen'>Log</a><br><br>\n";
echo "<br><font size='+1'><b>unidb</b></font><br>\n";
echo "<a href='./Benutzerverwaltung.php' target='Hauptrahmen'>".$Text[2]."</a><br><br>\n";
echo "<a href='./Einstellungen.html' target='_blank'>".$Text[16]."</a><br><br>\n";
echo "<a href='./Kollektiv_unidb.php' target='Hauptrahmen'>".$Text[6]."</a><br><br>\n";
echo "<a href='./Puffer_unidb.php' target='Hauptrahmen'>".$Text[14]."</a><br><br>\n";
echo "<a href='./Themen.php' target='Hauptrahmen'>".$Text[15]."</a><br><br>\n";
echo "<a href='./db_edit.php' target='Hauptrahmen'>".$Text[7]."</a><br><br>\n";
echo "<a href='../Formular.php?Baum_ID=756&Server_ID=1' target='Hauptrahmen'>".$Text[9]."</a><br><br>\n";
?>
</font>
</body>
</html>
