<?php

define('FPDF_FONTPATH','font/');
require('subwrite.php');

$b = $_POST['b'];
$d = $_POST['d'];
$d2 = $_POST['d2'];
$Ast = $_POST['Ast'];
$Asc0 = $_POST['Asc'];
	
	if($Asc0=='2-10 φ') {
		$Asc = 157;
	}
	else if($Asc0=='2-12 φ') {
		$Asc = 226;
	}
	else if($Asc0=='3-10 φ') {
		$Asc = 235.5;
	}
	else if($Asc0=='2-10 φ & 1-12 φ') {
		$Asc = 270;
	}
	else if($Asc0=='1-10 φ & 2-12 φ') {
		$Asc = 304.5;
	}
	else if($Asc0=='2-16 φ') {
		$Asc = 402;
	}
	else if($Asc0=='2-12 φ & 1-16 φ') {
		$Asc = 427;
	}
	else if($Asc0=='1-12 φ & 2-20 φ') {
		$Asc = 515;
	}
	else if($Asc0=='2-12 φ & 1-20 φ') {
		$Asc = 540;
	}
	else if($Asc0=='2-12 φ & 2-16 φ') {
		$Asc = 628;
	}
	else if($Asc0=='2-16 φ & 1-20 φ') {
		$Asc = 716;
	}
	else if($Asc0=='3-12 φ & 2-16 φ') {
		$Asc = 741;
	}
	else if($Asc0=='2-12 φ & 3-16 φ') {
		$Asc = 829;
	}
	else if($Asc0=='2-12 φ & 2-20 φ') {
		$Asc = 854;
	}
	else if($Asc0=='2-16 φ & 1-25 φ') {
		$Asc = 892;
	}
	else if($Asc0=='3-12 φ & 2-20 φ') {
		$Asc = 967;
	}
	else if($Asc0=='2-16 φ & 2-20 φ') {
		$Asc = 1030;
	}
	else if($Asc0=='2-20 φ & 1-25 φ') {
		$Asc = 1118;
	}
	else if($Asc0=='2-12 φ & 3-20 φ') {
		$Asc = 1168;
	}
	else if($Asc0=='1-16 φ & 2-25 φ') {
		$Asc = 1181;
	}
	else if($Asc0=='1-20 φ & 2-25 φ') {
		$Asc = 1294;
	}
	else if($Asc0=='3-12 φ & 2-25 φ') {
		$Asc = 1319;
	}
	else if($Asc0=='2-16 φ & 2-25 φ') {
		$Asc = 1382;
	}
	else if($Asc0=='2-20 φ & 1-32 φ') {
		$Asc = 1432;
	}
	else if($Asc0=='2-20 φ & 2-25 φ') {
		$Asc = 1608;
	}
	else if($Asc0=='3-20 φ & 2-25 φ') {
		$Asc = 1922;
	}
	else if($Asc0=='2-20 φ & 3-25 φ') {
		$Asc = 2098;
	}
	else if($Asc0=='2-20 φ & 2-32 φ') {
		$Asc = 2236;
	}
	else if($Asc0=='3-20 φ & 2-32 φ') {
		$Asc = 2550;
	}
	else if($Asc0=='2-20 φ & 3-32 φ') {
		$Asc = 3040;
	}
	else if($Asc0=='2-25 φ & 3-32 φ') {
		$Asc = 3392;
	}
$f_ck = $_POST['f_ck'];
$f_y = $_POST['f_y'];
$Mu1 = $_POST['Mu1'];
$Ast_bal = $_POST['Ast_bal'];
$fsc = $_POST['f_sc'];
$Mu2 = $_POST['Mu2'];
$Muc = $_POST['Muc'];
$Ast2 = $_POST['Ast2'];
$Mut = $_POST['Mut'];
$Mu = $_POST['Mu'];

$rat = round($d2/$d*100)/100.0;

$Cs = round($fsc*$Asc/1000.0,1) ;
$per = round($Ast2*100/($b*$d)*100)/100.0;

$html = 
"M<subu1 /> for concrete failur as singly reinforced(Fe 415)
<br />
<br />
M<subu1 /> = 0.138f<subck />bd<sup2 /> = 0.138*$f_ck*$b*($d)<sup2 /> = $Mu1 kNm
<br />
<br />
Balanced steel p = 1.43%
<br />
<br />
";

if($Ast>$Ast_bal)
{
$html = $html . "A<substbal /> = 1.43*$b*$d/100 = $Ast_bal mm<sup2 />
<br />
<br />
Stress in compression steel and compression in steel
<br />
<br />
d'/d = $d2/$d = $rat , f<subsc /> = $fsc N/mm<sup2 />
<br />
<br />
C<subs /> = 353*$Asc*10<sup-3 /> = $Cs kN
<br />
<br />
Value of M<subu2 /> due to compression steel
<br />
<br />
M<subu2 /> = $Cs*($d - $d2)*10<sup-3 /> = $Mu2 kNm
<br />
<br />
Total M<subuc /> for compression failure
<br />
<br />
M<subuc /> = M<subu1 /> + M<subu2 /> = $Mu1 + $Mu2 = $Muc kNm
<br />
<br />
Additional tension steel present as excess of balanced steel
<br />
<br />
A<subst2 /> = ($Ast - $Ast_bal) = $Ast2 mm<sup2 />
<br />
<br />
";
if($per > 0.20)
{
$html = $html . "Percentage = $Ast2*100/($b*$d) = $per% > 0.2% ------> Hence, more than nominal.
<br />
<br />
Ultimate moment capacity from steel beam theory(tension steel failure)
<br />
<br />
M<subut /> = M<subu1 /> + A<subst /> (0.87*f<suby />) (d - d<subdash />)
<br />
    = $Mu1 + $Ast2 (0.87*$f_y) ($d - $d2)*10<sup-6 />
<br />
    = $Mut kNm
<br />
<br />
Lesser of M<subuc /> and M<subut /> controls
<br />
<br />
Ultimate moments are more or less of equal capacity = ";
if($Muc<$Mut)
{
$html = $html . "
$Muc kNm
<br />
<br />
Hence, M<subu /> = $Muc kNm;
" ;
}

else
{
$html = $html . "
$Mut kNm
<br />
<br />
Hence, M<subu /> = $Mut kNm
" ;
} ;

}

else
{
	$html = $html . "Percentage = $Ast2*100/($b*$d) = $per% \< 0.2% ------> which is less than nominal." ;
}

}

else
{
	$html = $html . "Since A<subst />A<substbal /> , provide more reinforcement, so that A<subst /> becomes greater than A<substbal />" ;
}


$pdf=new PDF();
$pdf->AddFont('GalSILR','','GalSILR.php');
$pdf->AddPage();
$pdf->SetFont('GalSILR','',12);
$html = iconv("UTF-8", "ISO-8859-7//IGNORE", $html);
$pdf->SetLeftMargin(20);
$pdf->WriteHTML($html);
$pdf->Output('beam.pdf','I');
?>