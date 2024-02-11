<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//DE"
	"http://www.w3.org/TR/html4/frameset.dtd"><html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, minimum-scale=0.1, maximum-scale=5.0">
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./Hilfe.js"></script>
<?php
	include('Sitzung.php');
	header("X-XSS-Protection: 1");
	echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
	echo "</head>";
	echo "<body class='allgemein'>";
	$Text = Translate("Hilfe.php");
	echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
	echo "<div style='position: absolute; top: 5px; left: 5px; height: 30px;'>\n";
	echo "<table>\n<tr>\n";
	echo "<td><input class='Schalter_Element' value='".$Text[2]."' type='button' name='Inhaltsverzeichnis' id='inhaltsverzeichnis' onclick='Inhaltsverz();'></td>";
	echo "<td><input class='Schalter_Element' value='<' type='button' name='verschieben' id='links' onclick='schieben(\"links\");'></td>";
	echo "<td><input class='Schalter_Element' value='>' type='button' name='verschieben' id='rechts' onclick='schieben(\"rechts\");'></td>";
	echo "<td width='120px' align='right'>".$Text[3].": </td>";
	echo "<td><input class='Text_Element' value='' type='text' name='Suchen_Text' id='suchen_text'></td>";
	echo "<td><input class='Schalter_Element' value='".$Text[4]."' type='button' name='Suchen_starten' id='suchen_starten' onclick='suchen();'></td>";
?>
	</tr>
</table>
</div>
<div id='rahmen_inhalt' style='position: absolute; top: 35px; left: 5px; height: 100%; width: 0px;'>
<iframe id='if_inhalt' src='Hilfe2.php' width='0' height='100%' frameborder='0'></iframe>
</div>
<div id='rahmen_haupt' style='position: absolute; top: 35px; left: 5px; height: 100%; width: 100%;'>
<?php
	echo "<iframe id='if_haupt' name='Hilfe_Hauptrahmen' src='Hilfe3.php?Hilfe_ID=".$Hilfe_ID."' width='100%' height='100%' frameborder='0'></iframe>";
?>
</div>
</body>
</html>
