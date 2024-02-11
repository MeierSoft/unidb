<?php
//Get einlesen
foreach($_GET as $key => $value){
	${$key}=$value;
}
header("X-XSS-Protection: 1");
header("Content-type: image/png");
if(strlen($min)==0) {$min=0;}
if(strlen($max)==0) {$max=100;}
$min1=(float)$min;
$max1=(float)$max;
$Bandbreite=$max1-$min1;
$Wert1=(float)$Wert;
$von_oben=$max1 - $Wert1;
$Hoehe_temp=(float)$Hoehe;
$Hoehe2=$Hoehe_temp/$Bandbreite*$von_oben;
$Bild = imageCreate($Breite, $Hoehe);
$weiss = imageColorAllocate($Bild, 228, 228, 228);
imageFill($Bild, 0, 0, $weiss);
$blau = imageColorAllocate($Bild, 0, 0, 255);
imagefilledrectangle($Bild, 0, $Hoehe2, $Breite, $Hoehe, $blau);
imagePng($Bild);
imageDestroy($Bild);
?>