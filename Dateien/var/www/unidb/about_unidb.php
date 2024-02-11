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
	<a href="./about_unidb_en.php"><img src="./Bilder/Flaggen/EN.png" alt="english"><br>english</a>
	<div style="text-align: center;">
		<H1>über unidb</h1>
		copyright <?php echo strftime("%Y", time()); ?> by <a href="https://MeierSoft.de">MeierSoft.de</a><br><br>
		<img src="stat_Seiten/Logo.png" alt="Logo" longdesc="Logo unidb"><br><br>
	</div>
	<b>DH</b> steht für </b>D</b>ata<b>H</b>isorian. Der DH sammelt nummerische Werte, bezogen auf einen definierten Punkt und speichert diese Werte zusammen mit den Zeitpunkten ab, an denen die Werte gelesen wurden. Somit läßt sich für jeden Zeitpunkt, der Wert, der für einen definierten Punkt gespeichert wurde, wieder auslesen und weiter verarbeiten.
	<br><br>Die <b>unidb</b> ist die grafische Benutzeroberfläche für den DH. Sie ermölicht die Darstellung der gessammelten Datenreihen in Form von Linien - Diagrammen, als Tabellen, innerhalb einer bildlischen Darstellung als "lebende Daten" oder aber als Export für Tabellenkalkulationen.<br>
	Desweiteren stellt die unidb Formulare zur Verfügung, welche es ermölichen, komplette Datenbankanwendungen zu erstellen, welche auf MariaDB basieren. Es werden folgende Dokumenttypen bereitgestellt: Formular (inklusive Unterfiormulare), Bericht, grafischer SQL Editor, Tabellenkalkulation.<br><br>
	Mehr Informationen finden Sie im <a href="./Notiz.php?Baum_ID=497&amp;Server_ID=1">Anwenderhandbuch</a>.
	<h2 style="text-align: center;">Lizenz</h2>
	Sowohl die unidb, als auch der DH inklusive seiner Schnittstellen, stehen unter der GPL Version 3. Das bedeutet, dass Sie die Software frei verwenden, ändern und weitergeben dürfen. Nehmen Sie allerdings Änderungen vor und geben diese weiter, dann muss die weitergegebene Software ebenfalls unter der GPL Version 3 stehen.<br><br>
	<b>Sie sind somit nicht berechtigt, eine kommerzielle Version dieser Software zu vertreiben. Auch Ihre eigenen Erweiterungen oder Änderungen dürfen grundsätzlich nur mit dem Quellcode kostenlos weitergegeben werden. Diese Lizenz schließt jegliche Garantie und Haftung aus. Damit benutzen Sie die Software auf eigenes Risiko.</b><br><br>
	Die vollständige Lizenz finden Sie auf diesem Server: <a href="LICENSE.TXT">Lizenz GPL3</a>.<br>
	Mehr Infos zur GPL3: <a href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">Free Software Foundation</a><br><br>
	<div style="text-align: center;">
		<h2>Kontakt</h2>
		<a href="https://MeierSoft.de" target="_blank">MeierSoft.de</a>
		<br><br>
		<h2>Version</h2>
		02/2024
	</div>
</div>
</body>
</html>
