<?php

function plusMon($Monat, $Jahr) {
	$Monat = intval($Monat) + 1;
	if ($Monat == 13) {
		$Monat = "01";
		$Jahr = strval(intval($Jahr) + 1);
	} else {
		$Monat = strval($Monat);
		if (strlen($Monat) == 1) {$Monat = "0".$Monat;}
	}
	return $Jahr.$Monat;
}

function minusMon($Monat, $Jahr) {
	$Monat = intval($Monat) - 1;
	if ($Monat == 0) {
		$Monat = "12";
		$Jahr = strval(intval($Jahr) - 1);
	} else {
		$Monat = strval($Monat);
		if (strlen($Monat) == 1) {$Monat = "0".$Monat;}
	}
	return $Jahr.$Monat;
}

function lesen($Art, $Point_ID, $Startzeit, $Endezeit, $vor, $nach, $uTime, $vt_interpol, $vt) {
	$Art2 = array();
	if($Art == "hMinMax" or $Art == "dMinMax") {
		$Art2[] = $Art[0]."Min";
		$Art2[] = $Art[0]."Max";
	} else {
		$Art2[] = $Art;
	}
	$Wert = array();
	$Zeit = array();
	$Ergebnis = array();
	for ($r = 0; $r < count($Art2); $r++){
		$Pfad = "/var/lib/DH/";
		if ($Endezeit < $Startzeit) {exit;}
		$aktJahr = substr($Startzeit,0,4);
		$aktMonat = substr($Startzeit,5,2);
		$zielJahr = substr($Endezeit,0,4);
		$zielMonat = substr($Endezeit,5,2);
		//erster Wert ermitteln
		$Zeilen = file($Pfad.$aktJahr."/".$aktMonat."/".$Art2[$r]."/".$Point_ID);
		if($Zeilen != false) {
			$i = 0;
			$temp = explode(",",$Zeilen[0]);
			if($temp[0] > $Startzeit) {
				$JahrMonat = minusMon($aktMonat, $aktJahr);
				$aktMonat = substr($JahrMonat,4,2);
				$aktJahr = substr($JahrMonat,0,4);
				$Zeilenx = file($Pfad.$aktJahr."/".$aktMonat."/".$Art2[$r]."/".$Point_ID);
				if($Zeilenx) {
					$temp = explode(",",$Zeilenx[0]);
					$Zeilen = $Zeilenx;
				}
			}
			while($i < count($Zeilen) and $temp[0] <= $Startzeit){
				$temp = explode(",",$Zeilen[$i]);
				$i = $i + 1;
			}
			if($vor == 1) {
				$i = $i - 1;
				$temp = explode(",",$Zeilen[$i]);
				while($temp[0] < $Startzeit and $i < count($Zeilen)) {
					$temp = explode(",",$Zeilen[$i]);
					$i = $i + 1;
				}
				$i = $i - 2;
				$temp = explode(",",$Zeilen[$i]);
				$temp2  = $temp[0].",".$temp[1];
				if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
				if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
				if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
				$Ergebnis[] = $temp2;
			} else {
				$temp = explode(",",$Zeilen[$i]);
			}
			if($i == count($Zeilen)) {
				$JahrMonat = plusMon($aktMonat, $aktJahr);
				$aktMonat = substr($JahrMonat,4,2);
				$aktJahr = substr($JahrMonat,0,4);
				$Zeilen = file($Pfad.$aktJahr."/".$aktMonat."/".$Art2[$r]."/".$Point_ID);
				$temp2  = $temp[0].",".$temp[1];
				if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
				if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
				if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
				$Ergebnis[] = $temp2;
				$i = 0;
			}
			while($i < count($Zeilen) and $temp[0] <= $Endezeit){
				$temp = explode(",",$Zeilen[$i]);
				$temp2  = $temp[0].",".$temp[1];
				if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
				if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
				if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
				$Ergebnis[] = $temp2;
				$i = $i + 1;
			}
			if($nach == 1) {
				$temp = explode(",",$Zeilen[$i]);
				$temp2  = $temp[0].",".$temp[1];
				if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
				if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
				if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
				$Ergebnis[] = $temp2;
			}
		}
		//Falls es ueber mehrere Monate geht, dann die kompletten Monate an das Ergebnis anhaengen
		while (plusMon($aktMonat, $aktJahr) < $zielJahr.$zielMonat) {
			$JahrMonat = plusMon($aktMonat, $aktJahr);
			$aktMonat = substr($JahrMonat,4,2);
			$aktJahr = substr($JahrMonat,0,4);
			$Zeilen = file($Pfad.$aktJahr."/".$aktMonat."/".$Art2[$r]."/".$Point_ID);
			if($Zeilen != false) {
				for ($i = 0; $i < count($Zeilen); $i++){
					$temp = explode(",",$Zeilen[$i]);
					$temp2  = $temp[0].",".$temp[1];
					if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
					if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
					if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
					$Ergebnis[] = $temp2;
				}
			}
		}
		//Den letzten Monat verarbeiten
		$JahrMonat = plusMon($aktMonat, $aktJahr);
		$aktMonat = substr($JahrMonat,4,2);
		$aktJahr = substr($JahrMonat,0,4);
		if ($JahrMonat == $zielJahr.$zielMonat) {
			$Zeilen = file($Pfad.$aktJahr."/".$aktMonat."/".$Art2[$r]."/".$Point_ID);
			if($Zeilen != false) {
				for ($i = 0; $i < count($Zeilen); $i++){
					$temp = explode(",",$Zeilen[$i]);
					if ($temp[0] <= $Endezeit) {
						$temp2  = $temp[0].",".$temp[1];
						if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
						if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
						if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
						$Ergebnis[] = $temp2;
					} else {
						if($nach == 1) {
							$temp = explode(",",$Zeilen[$i]);
							$temp2  = $temp[0].",".$temp[1];
							if($uTime > 0) {$temp2  = $temp2.",".$temp[2];}
							if($vt_interpol > 0) {$temp2  = $temp2.",".$temp[3];}
							if($vt > 0) {$temp2  = $temp2.",".$temp[4];}
							$Ergebnis[] = $temp2;
						}
						break;
					}
				}
			}
		}
	}
	sort($Ergebnis);
	$uTimes = array();
	$vts_interpol = array();
	$vts = array();
	$Wert = array();
	$Zeit = array();
	for ($r = 0; $r < count($Ergebnis); $r++){
		if($r > 0 or count($Ergebnis) == 1) {
			if($Ergebnis[$r] != $Ergebnis[$r - 1] or count($Ergebnis) == 1) {
				$temp = explode(",",$Ergebnis[$r]);
				if(strlen($temp[0]) > 0) {
					$Zeit[] = $temp[0];
					$Wert[] = $temp[1];
					if($uTime > 0) {$uTimes[] = $temp[2];}
					if($uTime > 0) {$vts_interpol[] = $temp[3];}
					if($uTime > 0) {$vts[] = $temp[4];}
				}
			}
		}
	}
	return [$Zeit, $Wert, $uTimes, $vts_interpol, $vts];
}

?>