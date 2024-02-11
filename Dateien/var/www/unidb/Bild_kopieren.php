<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<title>Bild kopieren</title>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=5.0" />
<link href="../Fenster/jspanel.css" rel="stylesheet">
<script src="../Fenster/jspanel.js"></script>
<script src="./jquery-3.3.1.min.js"></script>
<script type="text/javascript">
	function Hilfe_Fenster(Hilfe_ID) {
		jQuery.ajax({
			url: "./Hilfe.php?Hilfe_ID=" + Hilfe_ID,
			success: function (html) {
   			strReturn = html;
			},
  			async: false
  		});
		jsPanel.create({
			dragit: {
        		snap: true
        	},
			id: 'Hilfe_Fenster',
			theme: 'primary',
			contentSize: '600 600',
			headerTitle: 'Hilfe',
			content:  "<div style = 'position: relative; top: 20px; left: 20px'>" + strReturn + "</div>",
		});
	}
</script>
<?php
include('Sitzung.php');
echo "<link href='./css/DH/".$_SESSION["Thema"].".css' rel='stylesheet'>";
echo "</head>";
echo "<body class='allgemein'>";
echo '<font color="#0000ff" size="3" face="Arial">Bild kopieren</font><br><br><font size="3" face="Arial">';
echo '<form action="neu2.php" method="post" target="_self">';
echo "<input type='hidden' name='Master' value = '".$Master."'>";
echo "<input type='hidden' name='Vorlage_ID' value = '6'>";
$Vorlage_ID
?>
neue Bezeichnung:<br>
<input type='text' name='Bezeichnung' size='50'><br><br>
<input type='submit' name='Aktion' value ='weiter'>
<input id=\"Hilfe\" value='Hilfe' type='button' name='Hilfe' onclick='Hilfe_Fenster("22");'>
</form>
</font>
</body>
</html>