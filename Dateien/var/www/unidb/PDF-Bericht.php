<?php
include('Sitzung.php');
header("X-XSS-Protection: 1");
require_once('../TCPDF/tcpdf_include.php');
$Seiten = explode("|||", $PDF_Inhalt);
$Seiteneigenschaften = json_decode($Seit_Eig);
foreach($Seiteneigenschaften as $key => $value){
	$temp = explode(":", $value);
	${$temp[0]} = $temp[1];
}

if($Format == "Hochformat" or $Format == "portrait" or $Format == "Portret" or $Format == "Portrait") {
	$Format = "P";
} else {
	$Format = "L";
}

$pdf = new TCPDF($Format, PDF_UNIT, substr($Groesse,4,2), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'../TCPDF/examples/lang/ger.php')) {
	require_once(dirname(__FILE__).'../TCPDF/examples/lang/ger.php');
	$pdf->setLanguageArray($l);
}
$pdf->SetFont('dejavusans', '', 9);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set JPEG quality
$pdf->setJPEGQuality(100);

//set margins
$pdf->SetMargins($linker_Rand, $oberer_Rand, $rechter_Rand);

$x = 0;
while($x < count($Seiten)) {
	$Inhalt = explode("@@@", $Seiten[$x]);
	$pdf->AddPage();
	$i = 0;
	while($i < count($Inhalt)) {
		$Element = json_decode($Inhalt[$i]);
		if($Element->SG > 0) {
			$pdf->SetFont('dejavusans',$Element->SB,$Element->SG);
		} else {
			$pdf->SetFont('dejavusans','',9);
		}
		$pdf->SetTextColor($Element->SF1, $Element->SF2, $Element->SF3);
		$pdf->SetFillColor($Element->HF1, $Element->HF2, $Element->HF3);
		$pdf->SetXY($Element->l, $Element->o);
		//writeHTML(html, ln = true, fill = 0, reseth = false, cell = false, align = '')
		//$pdf->writeHTML($Element->T, true, 1, true, true, $Element->Ausr);
		#writeHTMLCell(w, h, x, y, html = '', border = 0, ln = 0, fill = 0, reseth = true, align = '', autopadding = true)
		$pdf->writeHTMLCell($Element->b, $Element->h, $Element->l, $Element->o, $Element->T, $Element->Rand, 0, 1, true, $Element->Ausr, true);
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
		if($Element->Bild_Adresse != "") {
			$pdf->Image($Element->Bild_Adresse, $Element->l, $Element->o, $Element->b, $Element->h, $Element->Bild_Typ, $Element->Link, $align='', false, 300, '', false, false, $Element->Rand, false, false, false);
		}
		$i = $i + 1;
	}
	$x = $x + 1;
}

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');
?>

