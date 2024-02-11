<?php
	//Get und POST einlesen
	foreach($_GET as $key => $value){
		${$key}=$value;
	}
//$Ausdruck = "round(AW(40,86400s)/akt(40)*-10000/100+100,2)";
	session_start();
	require '../conf_DH.php';
	include('../funktionen.inc.php');
	include('../Trend_funktionen.php');
	if($_SESSION['admin'] != 1) {exit;}
	$ZS = 0;
	$Ausdruck = str_replace("@@@", "+", $Ausdruck);
	$Text="";
	$i=0;
	$Wert = "";
	$rechts="";
	$links="";
	global $Bezugszeitpunkt;

   $Bezugszeitpunkt = time();
   $Bezugszeitstempel = strftime('%Y-%m-%d %H:%M:%S',$Bezugszeitpunkt);

   while(strpos($Ausdruck, "time.time()") > -1 and $fertig == 0) {
   	$Position = strpos($Ausdruck, "time.time()");
   	$Ausdruck = substr($Ausdruck, 0, $Position)."time()".substr($Ausdruck, $Position + 11, strlen($Ausdruck) - 11 - $Position);
		if(strpos($Ausdruck, "time.time()") == FALSE) {
			$fertig = 1;
		}
	}

	function Point_ID_ermitteln($Ausdruck, $pos) {
		require '../conf_DH.php';
   	$Ergebnis = "";
    	$nehmen = ord(substr($Ausdruck,$pos,$pos + 1));
	   while ($nehmen > 47 and $nehmen < 58) {
			$Ergebnis = $Ergebnis.chr($nehmen);
      	$pos = $pos +1;
        	$nehmen = ord(substr($Ausdruck,$pos,$pos + 1));
	   }
	   //Point_ID für den Tag finden
		$Point_ID = Point_ID_finden($Ergebnis, $dbDH);
   	return $Ergebnis;
	}

	function Zeitpunkt_ermitteln($rechts, $Bezugszeitpunkt) {
   	$rel_Zeit = $rechts;
	   if ($rel_Zeit == "jetzt") {
   	    $Zeitpunkt_Zahl = time();
      	 $Text = strftime("%Y-%m-%d %H:%M:%S", $Zeitpunkt_Zahl);
	       return array($Text, $Zeitpunkt_Zahl);
  	 	}
		//Multiplikator ermitteln
   	$Multiplikator1 = "";
   	$pos = 0;
   	$Text = "";
   	$rel = 1;
   	$nehmen = 0;
   	if (substr($rechts, 0, 1) == "," or substr($rechts, 0, 1) == "(") {$rechts = substr($rechts, 1);}
   	while ($nehmen != 44 and $nehmen != 41 and strlen($Text) < strlen($rel_Zeit)) {
			$nehmen = ord(substr($rechts,$pos, 1));
	      if (($nehmen < 48 or $nehmen >57) and $nehmen != 32 and $nehmen != 100 and $nehmen != 115 and $nehmen != 109 and $nehmen != 104 and $nehmen != 41 and $nehmen != 44) {
   	     	$rel = 0;
      	}
	      $Text = $Text.substr($rechts, $pos, 1);
   	   $pos = $pos + 1;
	   }
   	while (substr($Text, 0, 1) == " " or substr($Text, 0, 1) == ",") {
			$Text = substr($Text, 1);
		}
    	while (substr($Text, - 1) == "," or substr($Text, - 1) == " " or substr($Text, - 1) == ")") {
      	$Text = substr($Text, 0, - 1);
    	}
		$rechts = substr($rechts,strlen($Text));
		while(substr($rechts,0,1) == "," or substr($rechts,0,1) == ")") {
			$rechts = substr($rechts,1);
		}
	   $Text = str_replace('"','',$Text);
		if ($rel == 1) {
			$pos = 0;
			$nehmen = ord(substr($Text, $pos, $pos + 1));
			while ($nehmen>47 and $nehmen <58) {
				$Multiplikator1 = $Multiplikator1.chr($nehmen);
				$pos = $pos +1;
				$nehmen = ord(substr($Text, $pos, $pos + 1));
			}
			$rel_Zeit = substr($Text,-1);
			$Multiplikator1=intval($Multiplikator1);
			if ($rel_Zeit== "s") {
				$Multiplikator2 = 1;
			} elseif ($rel_Zeit == "m") {
				$Multiplikator2 = 60;
			} elseif ($rel_Zeit== "h") {
				$Multiplikator2 = 3600;
			} elseif  ($rel_Zeit== "d") {
				$Multiplikator2 = 86400;
			}
			$Zeitpunkt_Zahl = $Bezugszeitpunkt-$Multiplikator1*$Multiplikator2;
			return array(strftime("%Y-%m-%d %H:%M:%S", $Zeitpunkt_Zahl), $Zeitpunkt_Zahl,$rechts);
    	} else {
			if ($Text == "jetzt") {
				$Zeitpunkt_Zahl = time();
				$Text = strftime("%Y-%m-%d %H:%M:%S", $Zeitpunkt_Zahl);
			} else {
				$Zeitpunkt_Zahl = strtotime($Text);
			}
			return array($Text, $Zeitpunkt_Zahl,$rechts);
		}
	}

	function interpolieren($Point_ID, $Zeitpunkt) {
		require '../conf_DH.php';
	   $Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 0, 0, 0, 0, 0);
	   if ($Ergebnis[0][0] == NULL) {
		   $Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 1, 0, 0, 0, 0);
   	   $Wert_vor = floatval($Ergebnis[1][0]);
      	$Zeit_vor = $Ergebnis[0][0];
	      $Zeit_vor = strtotime($Zeit_vor);
			$Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 0, 1, 0, 0, 0);
   	   if ($Ergebnis[0][0] == NULL) {
      	   //Den aktuellen Wert aus akt herauslesen und den Tag durch den Wert ersetzen
         	$stmt = mysqli_prepare($dbDH,"SELECT * from akt where Point_ID = ? ORDER BY Timestamp DESC LIMIT 1;");
	      	mysqli_stmt_bind_param($stmt, "i", $Point_ID);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
   			$akt_Werte = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  			mysqli_stmt_close($stmt);
   	   	$Wert_nach = floatval($akt_Werte["Value"]);
		      $Zeit_nach = $akt_Werte["Timestamp"];
      		$Zeit_nach = strtotime($Zeit_nach);
	      } else {
	      	$Wert_nach = $Ergebnis[1][0];
      	   $Zeit_nach = $Ergebnis[0][0];
	      }
   	   $Zeitdifferenz = $Zeit_nach - $Zeit_vor;
      	if ($Zeitdifferenz > 0) {
         	$Steigung = ($Wert_nach - $Wert_vor) / $Zeitdifferenz;
	      } else {
   	      $Steigung=0;
      	}
	      $Zeitpunkt_Zahl = strtotime($Zeitpunkt);
   	   return ($Zeitpunkt_Zahl - $Zeit_vor) * $Steigung + $Wert_vor;
    	} else {
      	 return floatval($Ergebnis[1][0]);
    	}
	}

	function Vorlauf($Ausdruck, $Suchtext, $links, $Dat, $Bezugszeitstempel, $Bezugszeitpunkt) {
		$Position = strpos($Ausdruck, $Suchtext);
	   $links = substr($Ausdruck, strlen($links), $Position - strlen($links));
	   $rechts = substr($Ausdruck, $Position + strlen($Suchtext));
	   $Point_ID = Point_ID_ermitteln($Ausdruck, $Position + strlen($Suchtext));
	   $rechts = substr($rechts,0 + strlen(strval($Point_ID)));
		while(substr($rechts,0,1) == "," or substr($rechts,0,1) == ")") {
			$rechts = substr($rechts,1);
		}
   	$BZeit = Zeitpunkt_ermitteln($rechts, $Bezugszeitpunkt);
   	$BZeitpunkt = $BZeit[0];
   	$BZeitpunkt_Zahl = $BZeit[1];
   	$rechts = $BZeit[2];
   	if($Dat == 2) {
   		$Zeit = Zeitpunkt_ermitteln($rechts, $Bezugszeitpunkt);
	   	$Zeitpunkt_Zahl = $Zeit[1];
   		$Zeitpunkt = strftime('%Y-%m-%d %H:%M:%S',$Zeitpunkt_Zahl);
   	}
		$Zeit = substr($rechts, 0, strpos($rechts, ")"));
   	if($Dat == 2) {
			//$rechts = substr($rechts,0,strpos($rechts,")"));
   		$Zeit = Zeitpunkt_ermitteln($rechts,$Bezugszeitpunkt);
		   //$Zeitpunkt_Zahl = $Zeit[1] - $BZeitpunkt_Zahl;
   		$Zeitpunkt = strftime('%Y-%m-%d %H:%M:%S',$Zeit[1]);
   		$rechts = $Zeit[2];
   		while((substr($rechts,0,1) == '"' or substr($rechts,0,1) == ')') and strlen($rechts) > 0) {
   			$rechts = substr($rechts,1);
   		}
   	}
   	$Ergebnis = array();
   	$Ergebnis[] = $links;
   	$Ergebnis[] = $rechts;
   	$Ergebnis[] = $Point_ID;
   	$Ergebnis[] = $BZeitpunkt;
   	$Ergebnis[] = $BZeitpunkt_Zahl;
   	$Ergebnis[] = $Zeitpunkt;
   	$Ergebnis[] = $Zeit[1];
   	return $Ergebnis;
	}

	//Zeitpunkt Wert
	$Suchtext = "ZP(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
   	$VErgebnis = Vorlauf($Ausdruck, $Suchtext, $links, 1, $Bezugszeitstempel, $Bezugszeitpunkt);
		$links = $VErgebnis[0];
   	$rechts = $VErgebnis[1];
   	$Point_ID = $VErgebnis[2];
   	$Zeitpunkt = $VErgebnis[3];
   	$stmt = mysqli_prepare($dbDH,"select * from akt where Point_ID = ? AND Timestamp < ? ORDER BY Timestamp DESC LIMIT 1");
	   mysqli_stmt_bind_param($stmt, "is", $Point_ID, $Zeitpunkt);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
   	$Ergebnis = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
   	if (count($Ergebnis) == 0) {
			$Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 1, 0, 1, 0, 0);
			$Wert = $Ergebnis[2][0];
		} else {
			$Wert = $Ergebnis["Timestamp"];
		}
		$Wert = strtotime($Ergebnis["Timestamp"]);
	   //Ausdruck aktualisieren
   	$Ausdruck = $links.strval($Wert).$rechts;
	}

	//Zeitstempel Wert
	$Suchtext = "ZS(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
   	$VErgebnis = Vorlauf($Ausdruck, $Suchtext, $links, 1, $Bezugszeitstempel, $Bezugszeitpunkt);
		$links = $VErgebnis[0];
   	$rechts = $VErgebnis[1];
   	$Point_ID = $VErgebnis[2];
   	$Zeitpunkt = $VErgebnis[3];
   	$stmt = mysqli_prepare($dbDH,"select * from akt where Point_ID = ? AND Timestamp < ? ORDER BY Timestamp DESC LIMIT 1");
	 	mysqli_stmt_bind_param($stmt, "is", $Point_ID, $Zeitpunkt);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
 		$Ergebnis = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		if (count($Ergebnis) == 0) {
			$Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 1, 0, 0, 0, 0);
			$Wert = $Ergebnis[0][0];
		} else {
			$Wert = $Ergebnis["Timestamp"];
		}
   	while (substr($rechts,0,1) == ")") {
      	$rechts = substr($rechts,1);
    	}
    	//Ausdruck aktualisieren
   	$Ausdruck = $links.strval($Wert).$rechts;
   	$ZS = 1;
	}

	//Archivwert
	$Suchtext = "AW(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
		$VErgebnis = Vorlauf($Ausdruck, $Suchtext, $links, 1, $Bezugszeitstempel, $Bezugszeitpunkt);
		$links = $VErgebnis[0];
   	$rechts = $VErgebnis[1];
   	$Point_ID = intval($VErgebnis[2]);
   	$Zeitpunkt = $VErgebnis[3];
  		$Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt, $Zeitpunkt, 1, 0, 0, 0, 0);
	   $Wert = $Ergebnis[1][0];
   	//Ausdruck aktualisieren
   	$Ausdruck = $links.strval($Wert).$rechts;
	}

	//interpolierte Werte, Format=intp(Tag,relativer Zeitpunkt) Beispiel intp(22,1h) = interpolierter Wert vom Tag 22 von vor einer Stunde
	$Suchtext = "intp(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
		$VErgebnis = Vorlauf($Ausdruck, $Suchtext, $links, 1, $Bezugszeitstempel, $Bezugszeitpunkt);
		$links = $VErgebnis[0];
   	$rechts = $VErgebnis[1];
   	$Point_ID = $VErgebnis[2];
   	$Zeitpunkt = $VErgebnis[3];
	   $Wert = interpolieren($Point_ID, $Zeitpunkt);
   	//Ausdruck aktualisieren
   	$Ausdruck = $links.strval($Wert).$rechts;
	}

	//Mittelwert
	$Suchtext = "MW(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
		$VErgebnis = Vorlauf($Ausdruck, $Suchtext, $links, 2, $Bezugszeitstempel, $Bezugszeitpunkt);
		$links = $VErgebnis[0];
   	$rechts = $VErgebnis[1];
   	$Point_ID = $VErgebnis[2];
   	$Zeitpunkt_Start = $VErgebnis[5];
   	$Zeitpunkt_Zahl_Start = $VErgebnis[6];
   	$Zeitpunkt_Ende = $VErgebnis[3];
   	$Zeitpunkt_Zahl_Ende= $VErgebnis[4];
   	//Den interpolierten Wert aus aus dem Archiv ermitteln und den Tag durch den Wert ersetzen
   	$Wert_Punkt_Start = interpolieren($Point_ID, $Zeitpunkt_Start);
   	$Wert_Punkt_Ende = interpolieren($Point_ID, $Zeitpunkt_Ende);
   	//Zwischenschritte
   	$Zeit_mal_Wert = 0;
   	$erster_Archivzeitpunkt = 0;
   	$i = 0;
		if($Zeitpunkt_Start > $Zeitpunkt_Ende) {
			$tempz = $Zeitpunkt_Ende;
			$Zeitpunkt_Ende = $Zeitpunkt_Start;
			$Zeitpunkt_Start = $tempz;
		}
   	$Ergebnis = lesen("rV", $Point_ID, $Zeitpunkt_Start, $Zeitpunkt_Ende, 1, 1, 1, 1, 0);

		for ($i = 0; $i < count($Ergebnis[0]); $i++) {
			if ($erster_Archivzeitpunkt == 0) {
           	$erster_Archivwert = floatval($Ergebnis[1][$i]);
           	$erster_Archivzeitpunkt = $Ergebnis[2][$i];
           	$Zeit_mal_Wert = (floatval($Ergebnis[2][$i]) - $Zeitpunkt_Zahl_Start) * $Wert_Punkt_Start;
			} else {
         	$Zeit_mal_Wert = $Zeit_mal_Wert + floatval($Ergebnis[3][$i]);
  	      	$letzter_Archivwert = floatval($Ergebnis[1][$i]);
     	   	$letzter_Archivzeitpunkt = $Ergebnis[2][$i];
			}
		}
     	$i = $i + 1;
		$Zeit_mal_Wert = $Zeit_mal_Wert + floatval($letzter_Archivwert) * ($Zeitpunkt_Zahl_Ende - floatval($letzter_Archivzeitpunkt));
		if($Zeit_mal_Wert == 0) {
			$Wert = interpolieren($Point_ID, $Zeitpunkt_Start);
		} else {
  			$Wert = $Zeit_mal_Wert / ($Zeitpunkt_Zahl_Ende - $Zeitpunkt_Zahl_Start);
  		}
		//Ausdruck aktualisieren
		$Ausdruck = $links.strval($Wert).$rechts;
	}

	//aktueller Wert
	$Suchtext = "akt(";
	while (strpos($Ausdruck, $Suchtext) > -1) {
		$Position = strpos($Ausdruck, $Suchtext);
		$links = substr($Ausdruck, 0, $Position);
		$rechts = substr($Ausdruck, strlen($links) + strlen($Suchtext),strlen($Ausdruck) - strlen($links) - strlen($Suchtext));
		$Position2 = 0;
		while(substr($rechts, $Position2,1) != ")" and $Position2 <= strlen($rechts)) {
			$Position2 = $Position2 + 1;
		}
		$Point_ID = substr($rechts, 0, $Position2);
		$rechts = substr($rechts, strlen($Point_ID) + 1, strlen($rechts) - strlen($Point_ID) - 1);
	   #Den aktuellen Wert aus akt herauslesen und den Tag durch den Wert ersetzen
   	$stmt = mysqli_prepare($dbDH,"select * from akt where Point_ID = ? ORDER BY Timestamp DESC LIMIT 1");
		mysqli_stmt_bind_param($stmt, "i", $Point_ID);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
	 	$Ergebnis = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
   	$links = $links.strval($Ergebnis["Value"]);
	   #Ausdruck aktualisieren
   	$Ausdruck = $links.$rechts;
	}
	if($ZS == 0) {
		eval('$Ergebnis='.$Ausdruck.';');
	} else {
		$Ergebnis = $Ausdruck;
	}
	if(gettype($Ergebnis) == "double" or gettype($Ergebnis) == "integer" or $ZS == 1) {
		echo $Ergebnis;
	} else {
		echo "Fehler";
	}
	// schliessen der Verbindung
	mysqli_close($dbDH);
?>