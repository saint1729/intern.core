<?php
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$pdf=new FPDF();
$pdf->AddFont('GalSILR','','GalSILR.php');
$pdf->AddPage();
$pdf->SetFont('GalSILR','',15);

$str = 'φ AfqwertyuiopasdfghjklzxcvbnmsIaA';

//$str = utf8_decode($str);
//$str = iconv('UTF-8', 'ISO-8859-7', $str);

//$string = "Löic & René";
$str = iconv("UTF-8", "ISO-8859-7", $str);

//echo $str;

//$pdf->Cell(0,10, iconv('UTF-8', 'ISO-8859-7', 'Buňka jedna φ'),1);

$pdf->Cell(0,5,$str);
$pdf->Output();
?>