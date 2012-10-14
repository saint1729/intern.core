<?php

define('FPDF_FONTPATH','font/');
require('subwrite.php');

$b = $_POST['b'];
$D = $_POST['D'];
$cc = $_POST['cc'];
$n1 = $_POST['n1'];
$phi_1 = $_POST['phi_1'];
$n2 = $_POST['n2'];
$phi_2 = $_POST['phi_2'];
$phi_s = $_POST['phi_s'];
$nleg = $_POST['nleg'];
$s_v = $_POST['s_v'];

$f_ck = $_POST['f_ck'];
$f_y = $_POST['f_y'];

$d = $_POST['d'];
$Ast = $_POST['Ast'];
$pst = $_POST['pst'];
$Asv = $_POST['Asv'];

if($f_y == 250) {
	$eps_su = 0.0031;
	}
else if($f_y == 415) {
	$eps_su = 0.0038;
	}
else if($f_y == 500) {
	$eps_su = 0.0042;
}

$xumax_by_d = round(0.0035/(0.0035+$eps_su),3);
$pt_bal = round(100.0*0.36*$f_ck*$xumax_by_d/(0.87*$f_y),3);
$K = round(0.36*$xumax_by_d*(1.0-0.42*$xumax_by_d),3);
$R= round(0.36*$xumax_by_d*(1-0.42*$xumax_by_d),3);
$tau_c_max = round(0.62*sqrt($f_ck),3);
$beta = round(0.8*$f_ck/(6.89*$pst),3);
$tau_c = round(0.85*sqrt(0.8*$f_ck)*(sqrt(1.0+5.0*$beta)-1.0)/(6.0*$beta),3);
$Muc = $_POST['Muc'];
$Mus = $_POST['Mus'];
$Vuc = $_POST['Vuc'];
$Vus = $_POST['Vus'];
$Vu = $_POST['Vu'];


$html = 'Given :<br /><br />width (b) = '. $b .' mm. ; Overall depth (D) = ' . $D . ' mm. ; Clear cover (cc) = ' . $cc . ' mm. ;<br />No. of long. bars(n<sub1 />) = ' . $n1 . ' ; Bar diameter (φ<sub1 />) = ' . $phi_1 . ' mm. ; No. of long . bars(n<sub2 />) = ' . $n2 . ';<br />Bar diameter (φ<sub2 />) = '  . $phi_2 . ' mm. ; Stirrup diameter (φ<subs />) = ' . $phi_s . ' mm. ; No. of stirrup legs (n<supleg />) = ' .  $nleg . ';<br />Stirrup spacing (s<subv />) = ' . $nleg . ' mm.; Concrete strength (f<subck />) = ' . $f_ck . ' N/mm<sup2 />; Steel strength (f<suby />) = ' . $f_y . ' N/mm<sup2 />
<br /><br />
Effective Depth (d) = D - cc - 0.5φ<sub1 /> = ' . $D . ' - ' . $cc . ' - 0.5*' . $phi_1 . ' = ' . $d . ' mm.<br /><br />
Area of long. steel (A<subst />) = n<sub1 />*πφ<sub1 /><sup2 />/4 + n<sub2 />*πφ<sub2 /><sup2 />/4 = ' . $n1 . '*' . 'π*' . $phi_1 . '<sup2 />' . '/4' . '+' . $n2 . '*' . 'π*' . $phi_2 . '<sup2 />' . '/4 = ' . $Ast . ' mm<sup2 />.<br /><br />
Percentage of steel (p<subst />) = 100*A<subst />/(bD) = ' . 100 . '*' . $Ast . '/(' . $b . '*' . $D . ')' . '=' . $pst . '.<br /><br />
Area of shear reinforcement (A<subsv />) = n<subleg />*πφ<subs /><sup2 />/4 = ' . $nleg . '*' . 'π*' . $phi_1 . '<sup2 />' . '/4' . ' = ' . $Asv . ' mm<sup2 />.<br /><br />
(x<subu />)<submax />/d = 0.0035/(0.0035 + ε<subsu />) =  ' . 0.0035 . '/(' . 0.0035 . '+' . $eps_su . ')' . ' = ' . $xumax_by_d . '.<br /><br />
pt<subbal /> = 100*0.36*f<subck />*((x<subu />)<submax />/d)/(0.87*f<suby />) =  ' . 100.0 . '*' . 0.36 .'*' . $f_ck . '*' . $xumax_by_d . '/(' . 0.87 . '*' .$f_y . ')' . ' = ' . $pt_bal . '.<br /><br />
K = 0.36*((x<subu />)<submax />/d)*(1-0.42*((x<subu />)<submax />/d)) = ' . 0.36 . '*' . $xumax_by_d . '*' . '(1.0- 0.42*' . $xumax_by_d . ')' . ' = ' . $K . '.<br /><br />
M<subuc /> = K*f<subck />*b*d*10<sup-6 /> = ' . $K . '*' . $f_ck . '*' . $b . '*' . $d . '*' . $d . '*' . '10<sup-6 />' . ' = ' . $Muc . ' kNm.<br /><br />
R = (0.87*f<subck />*p<subst />/100)*(1-1.005*(f<suby />/f<subck />)*p<subst />/100) = <br />       (0.87*' . $f_y . '*' . $pst . '/100)*(1-1.005*(' . $f_y . '/' . $f_ck . ')*' . $pst . '/100)' . ' = ' . $R . '.<br /><br />
M<subus /> = R*b*d<sup2 />*10<sup-6 /> = ' . $R . '*' . $b . '*' . $d . '*' . $d . '*1.0<sup-6 />' . ' = ' . $Mus . ' kNm.<br /><br />
(τ<subc />)<submax /> = 0.62*(f<subck />)<sup.5 /> =  ' . '0.62*' . $f_ck . '<sup.5 /> = ' . $tau_c_max . '.<br /><br />
β = 0.8*f<subck />/(6.89*p<subst />) = ' . '0.8*' . $f_ck . '/(6.89*' . $pst. ')' . ' = ' . $beta . '.<br /><br />
τ<subc /> =  0.85*(0.8*f<subck />)<sup.5 />*((1+5*β)-1)<sup.5 />/(6*β) = ' . '0.85*(0.8*' . $f_ck . ')<sup.5 />*((1+5*' . $beta . '-1)<sup.5 />/(6*' . $beta . ')' . ' =                                                                                                                                     ' . $tau_c . '.<br /><br />
A<subsv /> = n<subleg />*π*φ<subs /><sup2 />/4 = ' . $nleg . '*π*' . $phi_s . '<sup2 />/4' . ' = ' . $Asv . ' mm<sup2 />.<br /><br />
V<subuc /> = τ<subc />*b*d*10<sup-3 /> = ' . $tau_c . '*' . $b . '*' . $d . '*10<sup-3 />' . ' = ' . $Vuc . ' kN.<br /><br />
V<subus /> = 0.87*f<subck />*A<subsv />*d*10<sup-3 />/s<subv /> = ' . '0.87*' . $f_y . '*' . $Asv . '*' . $d . '*' . '10<sup-3 />/' . $s_v . ' = ' . $Vus . ' kN.<br /><br />
V<subu /> = V<subuc />+V<subus /> = ' . $Vuc . '+' . $Vus . ' = ' . $Vu . 'kN.<br /><br />
' ;



$pdf=new PDF();
$pdf->AddFont('GalSILR','','GalSILR.php');
$pdf->AddPage();
$pdf->SetFont('GalSILR','',12);


if($Muc>$Mus && ($Muc-$Mus)<30)
	{
		$html = $html . '<br />                   The entered values looks fine and can be used for reinforcement.' ;
	}

elseif($Muc<$Mus)
	{
		$html = $html . '<br />Since, M<subuc /> ' . 'is less than' . ' M<subus />. Concrete fails first and so, area of reinforcement has to be decreased .';
		$html = $html . '<br />Hence the adjust of reinforcement must be made as follows:';
		$t1 = adjust($Muc, $Muc, $b, $D, $f_y, $f_ck, $d);
		$html = $html. '<br />  n<sub1 /> =  ' . $t1[0] . ' and φ<sub1 /> = ' . $t1[1] ;
		
	/*  f.f_ck.value = Math.round(f_ck*1000)/1000;
		f.f_y.value = Math.round(f_y*1000)/1000;
		f.d.value = Math.round(d*1000)/1000;
		f.Ast.value = Math.round(Ast*1000)/1000;
		f.pst.value = Math.round(pst*100000)/100000;
		f.Asv.value = Math.round(Asv*1000)/1000;
		f.Muc.value = Math.round(Muc*1000)/1000;
		f.Mus.value = Math.round(Mus*1000)/1000;
		f.Vuc.value = Math.round(Vuc*1000)/1000;
		f.Vus.value = Math.round(Vus*1000)/1000;
		f.Vu.value = Math.round(Vu*1000)/1000;*/

		$n1 = $t1[0];
		$phi_1 = $t1[1];
		$Ast = round(($n1*3.14159*$phi_1*$phi_1)/4, 3);
		$pst = round(100.0*$Ast/($b*$D), 5);
		$Asv = round($nleg*3.14159*$phi_s*$phi_s/4, 3);
		$xumax_by_d = round(0.0035/(0.0035+$eps_su), 3);
		$pt_bal = round(100.0*0.36*$f_ck*$xumax_by_d/(0.87*$f_y), 3);
		$K = round(0.36*$xumax_by_d*(1.0-0.42*$xumax_by_d), 3);
		$Muc = round($K*$f_ck*$b*$d*$d*pow(10,-6), 3);
		$R  = round((0.87*$f_y*$pst/100.0)*(1.0-1.005*($f_y/$f_ck)*$pst/100.0), 3);
		$Mus = round($R*$b*$d*$d*pow(10,-6), 3);
		$tau_c_max = round(0.62*sqrt($f_ck), 3);
		$beta = round(0.8*$f_ck/(6.89*$pst), 3);
		$tau_c = round(0.85*sqrt(0.8*$f_ck)*(sqrt(1.0+5.0*$beta)-1.0)/(6.0*$beta), 3);
		$Vuc = round($tau_c*$b*$d*1.0*pow(10,-3), 3);
		$Vus = round(0.87*$f_y*$Asv*$d*1.0*pow(10,-3)/$s_v, 3);
		$Vu = round($Vuc+$Vus, 3);

		$html = $html . '<br /><br />
Area of long. steel (A<subst />) = n<sub1 />*πφ<sub1 /><sup2 />/4 + n<sub2 />*πφ<sub2 /><sup2 />/4 = ' . $n1 . '*' . 'π*' . $phi_1 . '<sup2 />' . '/4' . '+' . $n2 . '*' . 'π*' . $phi_2 . '<sup2 />' . '/4 = ' . $Ast . ' mm<sup2 />.<br /><br />
Percentage of steel (p<subst />) = 100*A<subst />/(bD) = ' . 100 . '*' . $Ast . '/(' . $b . '*' . $D . ')' . '=' . $pst . '<br /><br />
Area of shear reinforcement (A<subsv />) = n<subleg />*πφ<subs /><sup2 />/4 = ' . $nleg . '*' . 'π*' . $phi_1 . '<sup2 />' . '/4' . ' = ' . $Asv . ' mm<sup2 />.<br /><br />
(x<subu />)<submax />/d = 0.0035/(0.0035 + ε<subsu />) =  ' . 0.0035 . '/(' . 0.0035 . '+' . $eps_su . ')' . ' = ' . $xumax_by_d . '.<br /><br />
pt<subbal /> = 100*0.36*f<subck />*((x<subu />)<submax />/d)/(0.87*f<suby />) =  ' . 100.0 . '*' . 0.36 .'*' . $f_ck . '*' . $xumax_by_d . '/(' . 0.87 . '*' .$f_y . ')' . ' = ' . $pt_bal . '.<br /><br />
K = 0.36*((x<subu />)<submax />/d)*(1-0.42*((x<subu />)<submax />/d)) = ' . 0.36 . '*' . $xumax_by_d . '*' . '(1.0- 0.42*' . $xumax_by_d . ')' . ' = ' . $K . '.<br /><br />
M<subuc /> = K*f<subck />*b*d*10<sup-6 /> = ' . $K . '*' . $f_ck . '*' . $b . '*' . $d . '*' . $d . '*' . '10<sup-6 />' . ' = ' . $Muc . ' kNm.<br /><br />
R = (0.87*f<subck />*p<subst />/100)*(1-1.005*(f<suby />/f<subck />)*p<subst />/100) = <br />       (0.87*' . $f_y . '*' . $pst . '/100)*(1-1.005*(' . $f_y . '/' . $f_ck . ')*' . $pst . '/100)' . ' = ' . $R . '.<br /><br />
M<subus /> = R*b*d<sup2 />*10<sup-6 /> = ' . $R . '*' . $b . '*' . $d . '*' . $d . '*1.0<sup-6 />' . ' = ' . $Mus . ' kNm.<br /><br />
(τ<subc />)<submax /> = 0.62*(f<subck />)<sup.5 /> =  ' . '0.62*' . $f_ck . '<sup.5 /> = ' . $tau_c_max . '.<br /><br />
β = 0.8*f<subck />/(6.89*p<subst />) = ' . '0.8*' . $f_ck . '/(6.89*' . $pst. ')' . ' = ' . $beta . '.<br /><br />
τ<subc /> =  0.85*(0.8*f<subck />)<sup.5 />*((1+5*β)-1)<sup.5 />/(6*β) = ' . '0.85*(0.8*' . $f_ck . ')<sup.5 />*((1+5*' . $beta . '-1)<sup.5 />/(6*' . $beta . ')' . ' =                                                                                                                                    ' . $tau_c . '.<br /><br />
A<subsv /> = n<subleg />*π*φ<subs /><sup2 />/4 = ' . $nleg . '*π*' . $phi_s . '<sup2 />/4' . ' = ' . $Asv . ' mm<sup2 />.<br /><br />
V<subuc /> = τ<subc />*b*d*10<sup-3 /> = ' . $tau_c . '*' . $b . '*' . $d . '*10<sup-3 />' . ' = ' . $Vuc . ' kN.<br /><br />
V<subus /> = 0.87*f<subck />*A<subsv />*d*10<sup-3 />/s<subv /> = ' . '0.87*' . $f_y . '*' . $Asv . '*' . $d . '*' . '10<sup-3 />/' . $s_v . ' = ' . $Vus . ' kN.<br /><br />
V<subu /> = V<subuc />+V<subus /> = ' . $Vuc . '+' . $Vus . ' = ' . $Vu . 'kN.<br /><br />
' ;
	}

function adjust($a, $b, $c, $d, $e, $f, $g)
{
	$t1 = $b;
	$b = $a;
	$R = $b/($c*$g*$g*pow(10,-6));
	$S = 0.87*$e/($c*$d);
	$T = 1.005*$e/($f*$c*$d);
	$A = ($S-sqrt($S*$S-4*$R*$S*$T))/(2*$S*$T);

	$rad = array(20,25);
	$n1 = array();
	$pi = 3.14159;
	$n1[0] = 4*$A/($pi*20*20);
	$n1[1] = 4*$A/($pi*25*25);

	$n2 = array($n1[0]-floor($n1[0]),$n1[1]-floor($n1[1]));
	$t2 = min($n2);
	$i = 0;
	while(1)
		{
			if ($t2==$n2[$i])
				{
					break;
				}
			$i++;
		}

	$A1 = array($n1[0]*$pi*20*20/4, $n1[1]*$pi*25*25/4);
	$j=0;
	while($j<3)
		{
			$pst = round(100.0*$A1[$i]/($c*$d), 5);
			$R  = round((0.87*$e*$pst/100.0)*(1.0-1.005*($e/$f)*$pst/100.0), 3);
			$Mus[$j] = round($R*$c*$g*$g*pow(10,-6), 3);
			
			$j++;
		}

	$t3 = max($Mus);
	$i = 0;
	while(1)
		{
			if ($t3==$Mus[$i])
				{
					break;
				}
			$i++;
		}
	$n1[$i] = floor($n1[$i])-1;
	return array($n1[$i],$rad[$i]);
}

$html = iconv("UTF-8", "ISO-8859-7//IGNORE", $html);
$pdf->SetLeftMargin(20);

//$t1 = adjust($Muc, $Muc, $d, $D, $f_y, $f_ck);
//echo $t1."<br />";
$pdf->WriteHTML($html);
$pdf->Output('beam.pdf','I');
?>