<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>about unidb</title>
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<meta name="copyright" content="copyright 2024, Ralf Meier, https://MeierSoft.de">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">
<?php
	include('./Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
?>
</head>
<body class='allgemein'>

<div class="Inhalt" style="width: 800px; ">
	<a href="./about_unidb.php"><img src="./Bilder/Flaggen/DE.png" alt="deutsch"><br>deutsch</a>
	<div style="text-align: center;">
		<H1>about unidb</h1>
		copyright <?php echo strftime("%Y", time()); ?> by <a href="https://MeierSoft.de">MeierSoft.de</a><br><br>
		<img src="stat_Seiten/Logo.png" alt="Logo" longdesc="Logo unidb"><br><br>
	</div>
	<b>DH</b> stands for <b>D</b>ata<b>H</b>isorian. The DH collects numerical values based on a defined point and saves these values together with the times at which the values were read. This means that the value that was saved for a defined point can be read out again at any time and processed further.<br><br>
	The <b>unidb</b> is the graphical user interface for the DH. It enables the collected data series to be displayed in the form of line diagrams, as tables, within a pictorial representation as "living data" or as an export for spreadsheets.<br>
	Furthermore, unidb provides forms that make it possible to create complete database applications based on MariaDB. The following document types are provided: form (including subforms), report, graphical SQL editor, spreadsheet.<br><br>
	You can find more information in the <a href="./Notiz.php?Baum_ID=497&amp;Server_ID=1">user manual</a>.<br><br>
	<h2 style="text-align: center;">License</h2>
	Both the unidb and the DH including its interfaces are under GPL version 3. This means that you are free to use, change and distribute the software. However, if you make changes and pass them on, then the software passed on must also be under GPL version 3.<br><br>
	<b>You are therefore not authorized to distribute a commercial version of this software. Your own extensions or changes may generally only be passed on free of charge with the source code. This license excludes all warranties and liability. You use the software at your own risk.</b><br><br>
	You can find the full license on this server: <a href="LICENSE.TXT">License GPL3</a>.<br>
	More information about GPL3: <a href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">Free Software Foundation</a><br><br>
	<div style="text-align: center;">
		<h2>Contact</h2>
		<a href="https://MeierSoft.de" target="_blank">MeierSoft.de</a>
		<br><br>
		<h2>Version</h2>
		02/2024
	</div>
</div>
</body>
</html>