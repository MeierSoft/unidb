<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1, maximum-scale=5.0" />
<script src="./scripts/fabric.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<link href="../Fenster/dist/jspanel.min.css" rel="stylesheet">
<script src="../Fenster/dist/jspanel.min.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.ui.position.js"></script>
<script type="text/javascript" src="./contextMenu/jquery.contextMenu.js"></script>
<link rel="stylesheet" href="./contextMenu/jquery.contextMenu.css" type="text/css" media="screen">
<link href="Navigation.css" rel="stylesheet">
<link href="MS.css" rel="stylesheet">
<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
include 'mobil.php';
$Text = Translate("Zeichnung_bearbeiten.php");
echo "<title>".$Text[0]."</title>\n";
echo "</head>\n";
echo "<body class='allgemein'>\n";
$anzeigen = Berechtigung($Baum_ID, $Server_ID);
if($anzeigen == 0) {
	echo $Text[5];
	exit;
}

//editierte Zeichnung speichern
if ($Aktion == $Text[2]){
	$anzeigen = Berechtigung($Baum_ID, $Server_ID);
	$SVG = htmlentities($SVG, ENT_QUOTES);
	$Zeichnung = htmlentities($Zeichnung, ENT_QUOTES);
	if($anzeigen == 0) {
		echo $Text[27];
		exit;
	}
	$Bezeichnung = strip_tags($Bezeichnung);
	$query = "UPDATE Baum SET Bezeichnung = ?, Inhalt = COLUMN_ADD(Inhalt, 'Zeichnung', ?), Inhalt = COLUMN_ADD(Inhalt, 'SVG', ?) WHERE `Baum_ID` = ? AND `Server_ID` = ?;";
	uKol_schreiben(1,$query, "sssii", [$Bezeichnung, $Zeichnung, $SVG, $Baum_ID, $Server_ID]);
}

//Daten einlesen
$abfrage = "SELECT `Server_ID`, Bezeichnung, column_get(Inhalt, 'Zeichnung' as CHAR) as Zeichnung, column_get(Inhalt, 'SVG' as CHAR) as SVG  FROM `Baum` where Baum_ID = ? AND `Server_ID` = ?;";
$stmt = mysqli_prepare($db,$abfrage);
mysqli_stmt_bind_param($stmt, "ii", $Baum_ID, $Server_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
//Form bauen
echo "<form id='phpform' action='Zeichnung_bearbeiten.php' method='post' target='_self'>";
$Zeichnung = $line["Zeichnung"];
$SVG = $line["SVG"];
$Bezeichnung = html_entity_decode($line["Bezeichnung"]);

?>
<div style="position: absolute; top: 40px;">
	<script>
		try {
			var Breite = window.frameElement.clientWidth - 20;
		} catch (err) {
			var Breite = window.innerWidth - 20;
		}
		var Hoehe = Breite/210*297;
		document.write('<canvas id="c" width="' + (Breite).toString() + '" height="' + (Hoehe).toString() + '"></canvas>');
	</script>
</div>
<div style="position: absolute; top: 0px; left: 10px; width: 100%">
	<table frame='box' bgcolor='#F5F4D4'>
		<tr>
			<td>
				<table class='Text_einfach'>
					<tr>
			  			<td>
							<div class='dropdown'>
								<div class='dropbtn'>Dokument</div>
								<div class='dropdown-content'>
									<?php
										echo "						<a href='Zeichnung.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' target='_self'>".$Text[4]."</a>\n";
										echo "						<a href='./SVG_Export.php?Baum_ID=".$Baum_ID."&Server_ID=".$Server_ID."' download='".$Server_ID."_".$Baum_ID.".svg'>SVG Export</a>\n";
										echo "						<div id='import_svg' class='btn btn-info'><a href='#'>SVG Import</a></div>\n";
										echo "						<br>&nbsp;&nbsp;".$Text[1].": <input value='".$Bezeichnung."' type='text' name='Bezeichnung'><br><br>\n";
										echo "						&nbsp;&nbsp;<input class='btn btn-info' id='speichern' value='".$Text[2]."' type='submit' name='Aktion'><br><br>\n";
									?>
								</div>
							</div>	
						</td>
						<td>
							<div class='dropdown'>
								<div class='dropbtn'>bearbeiten</div>
								<div class='dropdown-content'>
			  						<div id="clear-canvas" class="btn btn-info"><a href='#'>leeren</a></div>
	  								<div id="kopieren" class="btn btn-info"><a href='#'>kopieren</a></div>
					 				<div id="einfuegen" class="btn btn-info"><a href='#'>einfügen</a></div>
		  							<div id="loeschen" class="btn btn-info"><a href='#'>markiertes löschen</a></div>
		  							<div id="lloeschen" class="btn btn-info"><a href='#'>letztes löschen</a></div>
								</div>
							</div>
						</td>
	  					<td>
							<div class='dropdown'>
								<div class='dropbtn'>anordnen</div>
								<div class='dropdown-content'>
	  		  						<div id="weiterzurueck" class="btn btn-info"><a href='#'>weiter zurück</a></div>
	  								<div id="weitervor" class="btn btn-info"><a href='#'>weiter vor</a></div>
						  			<div id="nachhinten" class="btn btn-info"><a href='#'>nach hinten</a></div>
  									<div id="nachvorne" class="btn btn-info"><a href='#'>nach vorne</a></div>
								</div>
							</div>
						</td>
		  				<td>
							<div class='dropdown'>
								<div class='dropbtn'>einfügen</div>
								<div class='dropdown-content'>
									<div id="textrahmen" class="btn btn-info"><a href='#'>Textrahmen</a></div>
					  				<div id="liniew" class="btn btn-info"><a href='#'>Linie waagerecht</a></div>
					  				<div id="linies" class="btn btn-info"><a href='#'>Linie senkrecht</a></div>
			  						<div id="rechteck" class="btn btn-info"><a href='#'>Rechteck</a></div>
  									<div id="kreis" class="btn btn-info"><a href='#'>Kreis</a></div>
  									<div id="dreieck" class="btn btn-info"><a href='#'>Dreieck</a></div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</td>
			<td style="width: 20px;"></td>
			<td>
				<table class='Text_einfach'>
					<tr>
						<td align="right">
  							<div class='dropbtn'>Zoom:</div>
	  					</td>
		  				<td width="20px" align="center">
  							<span id="vergrAnz" class="info">1</span>
  						</td>
						<td>
							<input style="width: 190px;" type="range" value="1" min="0.1" max="10" step="0.1" id="vergr">
						</td>
					</tr>
				</table>
			</td>
			<td style="width: 20px;"></td>
			<td>
				<table class='Text_einfach'>
					<tr>
  						<td><input type = "button" id="drawing-mode" class="btn btn-info" value = "zeichnen"></td>
  						<td style="width: 20px;"></td>
  						<td><input type = "button" id="eigenschaften" class="btn btn-info" value = "Eigenschaften"></td>
						<td style="width: 20px;"></td>
  						<td><input type = "button" id="ansicht" class="btn btn-info" value = "volle Höhe"></td>
			  		</tr>
		  		</table>
			</td>
			<td style="width: 20px;"></td>
			<td>	
				<table class='Text_einfach'>
					<tr>
  						<td><input type = "button" id="links" class="btn btn-info" value = " < "></td>
  						<td><input type = "button" id="rechts" class="btn btn-info" value = " > "></td>
  						<td><input type = "button" id="startpos" class="btn btn-info" value = "zurücksetzen"></td>
  						<td><input type = "button" id="rauf" class="btn btn-info" value = "/\"></td>
  						<td><input type = "button" id="runter" class="btn btn-info" value = "\/"></td>
			  		</tr>
		  		</table>
			</td>
  		</tr>
  	</table>
	<?php
		echo "<input id='baum_id' value='".$Baum_ID."' type='hidden' name='Baum_ID'>";
		echo "<input id='server_id' name='Server_ID' type='hidden' value='".$Server_ID."'>\n";
		echo "<input id='zeichnung' name='Zeichnung' type='hidden' value='".$Zeichnung."'>\n";
		echo "<input id='svg' name='SVG' type='hidden' value='".$SVG."'>\n";
		echo "<input id='modus' name='Modus' type='hidden' value='".$Modus."'>\n";
		echo "<input id='translation' name='Translation' type='hidden' value='".json_encode($Text)."'>\n";
		mysqli_close($db);
	?>
  	</form>
</div>
<div id="drawing-mode-options"></div>
<script src="./scripts/Zeichnung.js"></script>
<div id='import_output' style="display: none;"></div>
</body>
</html>