<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./scripts/fabric.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include 'mobil.php';
$Text = Translate("Zeichnung_neu.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
?>
<form id='phpform' action='Baum2.php' method='post' target='Baum'>
	<table cellpadding="5px">
		<tr>
			<td>
				<?php
					echo "				<input class='Schalter_Element' id='speichern' value='neue Zeichnung speichern' type='submit' name='Aktion'>\n";
					echo "				</td>\n";
					echo "				<td>\n";
					if ($mobil==1){
						echo "<a href='Baum2.php' target='_self'>abbrechen</a>\n";
					} else {
						echo "<a href='Zeichnung.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>abbrechen</a>\n";
					}
				?>
			</td>
		</tr>
	</table>
	<?php
		echo "<input value='".$Vorlage_ID."' type='hidden' name='Vorlage_ID'>";
		echo "<input value='".$original."' type='hidden' name='original'>";
		echo "<input id='bezeichnung' name='Bezeichnung' type='hidden' value='".$Bezeichnung."'>\n";
		echo "<input id='Server_ID' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
		echo "<input id='zeichnung' name='Zeichnung' type='hidden' value='".$Zeichnung."'>\n";
		echo "<input id='svg' name='SVG' type='hidden' value='".$SVG."'>\n";
		echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
		echo "<input value='".$Eltern_ID."' type='hidden' name='Eltern_ID'>";
		mysqli_close($db);
	?>
</form>
</body>
</html>
