<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Beam</title>



<?php

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


/*
b      "Width (mm) :"
D      "Overall depth (mm) :"
cc     "Clear cover (mm) :"
n1     "No. of long. bars :"
phi_1  "Bar dia(mm) :"
n2     "No. of long. bars :"
phi_2  "Bar dia(mm) :"
phi_s  "Stirrup dia (mm) :"
nleg   "No. of stirrup legs :"
s_v   "Stirrup spacing (mm) :"

f_ck  "Concrete strength (N/mm^2) :"
f_y   "Steel strength (N/mm^2) :"
d     "Effective depth (mm) :"
Ast   "Area of long. steel (mm^2) :"
pst   "Percentage of steel :"
Asv   "Area of shear reinf. (mm^2) :"

Muc "Mom. of resist. due to concrete (kNm) : "
Mus "Mom. of resist. due to steel (kNm) : "
Vuc "Critical shear due to concrete (kN) :"
Vus "Shear capacity due to stirrup (kN) :"
Vu  "Total shear capacity (kN) :"
*/




?>

<style type="text/css">
input#input {
	width: 102px;
}
table#center {
    margin-left:auto; 
    margin-right:auto;
  }
#given {
	width: 700px;
	margin-left: 50px;
}
</style>
</head>

<body>
<p>Given :</p>
<p id="given">

width (b) = <?php echo $b; ?> mm. ; Overall depth (D) = <?php echo $D; ?> mm. ; Clear cover (cc) = <?php echo $cc; ?> mm. ; No. of long. bars(n<sub>1</sub>) = <?php echo $n1; ?> ; Bar diameter (φ<sub>1</sub>) = <?php echo $phi_1; ?> ; No. of long. bars(n<sub>2</sub>) = <?php echo $n2; ?> ; Bar diameter (φ<sub>2</sub>) = <?php echo $phi_2; ?> mm. ; Stirrup diameter (φ<sub>s</sub>) = <?php echo $phi_s; ?> mm. ; No. of stirrup legs = <?php echo $nleg; ?> ; Stirrup spacing (s<sub>v</sub>) = <?php echo $nleg; ?> mm.; Concrete strength (ƒ<sub>ck</sub>) = <?php echo $f_ck; ?> N/mm<sup>2</sup>; Steel strength (ƒ<sub>y</sub>) = <?php echo $f_y; ?> N/mm<sup>2</sup>;

</p>
<p>Effective Depth (d) = D - cc - 0.5φ<sub>1</sub> = <?php echo $D . " - " . $cc . " - " . 0.5*$phi_1 ; ?> = <?php echo $d; ?> mm.</p>
<p>Area of long. steel (A<sub>st</sub>) = n<sub>1</sub>*πφ<sub>1</sub><sup>2</sup>/4 + n<sub>2</sub>*πφ<sub>2</sub><sup>2</sup>/4 = <?php echo $n1 . "*" . "π*" . $phi_1 . "<sup>2</sup>" . "/4" . "+" . $n2 . "*" . "π*" . $phi_2 . "<sup>2</sup>" . "/4" ; ?> = <?php echo $Ast; ?> mm<sup>2</sup>.</p>
<p>Percentage of steel (p<sub>st</sub>) = 100*A<sub>st</sub>/(bD) = <?php echo 100 . "*" . $Ast . "/(" . $b . "*" . $D . ")" ; ?> = <?php echo $pst; ?>.</p>
<p>Area of shear reinforcement (A<sub>sv</sub>) = n<sub>leg</sub>*πφ<sub>s</sub><sup>2</sup>/4 = <?php echo $n1 . "*" . "π*" . $phi_1 . "<sup>2</sup>" . "/4" ; ?> = <?php echo $Asv; ?> mm<sup>2</sup>.</p>
<p>(x<sub>u</sub>)<sub>max.</sub>/d = 0.0035/(0.0035 + ε<sub>su</sub>) = <?php echo 0.0035 . "/(" . 0.0035 . "+" . $eps_su . ")" ; ?> = <?php echo $xumax_by_d; ?>.</p>
<p> pt<sub>bal.</sub> = 100*0.36*ƒ<sub>ck</sub>*((x<sub>u</sub>)<sub>max.</sub>/d)/(0.87*ƒ<sub>y</sub>) =  <?php echo 100.0 . "*" . 0.36 ."*" . $f_ck . "*" . $xumax_by_d . "/(" . 0.87 . "*" .$f_y . ")"; ?> = <?php echo $pt_bal ; ?></p>
<p>K = 0.36*((x<sub>u</sub>)<sub>max.</sub>/d)*(1-0.42*((x<sub>u</sub>)<sub>max.</sub>/d)) = <?php echo 0.36 . "*" . $xumax_by_d . "*" . "(1.0- 0.42*" . $xumax_by_d . ")"; ?> = <?php echo $K ; ?></p>
<p>M<sub>uc</sub> = K*ƒ<sub>ck</sub>*b*d*10<sup>-6</sup> = <?php echo $K . "*" . $f_ck . "*" . $b . "*" . $d . "*" . $d . "*" . "10<sup>-6</sup>"; ?> = <?php echo $Muc; ?> kNm.</p>
<p>R = (0.87*ƒ<sub>ck</sub>*p<sub>st</sub>/100)*(1-1.005*(ƒ<sub>y</sub>/ƒ<sub>ck</sub>)*p<sub>st</sub>/100) = <?php echo "0.87*" . $f_y ."*" . $pst . "/100)*(1-1.005*(" . $f_y . "/" . $f_ck . ")*" . $pst . "/100)" ; ?> = <?php echo $R ; ?></p>
<p>M<sub>us</sub> = R*b*d<sup>2</sup>*10<sup>-6</sup> = <?php echo $R ."*" . $b ."*" . $d . "*" . $d . "*1.0<sup>-6</sup>" ;?> = <?php echo $Mus; ?> kNm.</p>
<p>(τ<sub>c</sub>)<sub>max.</sub> = 0.62*ƒ<sub>ck</sub>*
&radic;f<sub>ck</sub> =  <?php echo "0.62*&radic;" . $f_ck ; ?> = <?php echo $tau_c_max; ?> </p>
<p>β = 0.8*ƒ<sub>ck</sub>/(6.89*p<sub>st</sub>) = <?php echo "0.8*" . $f_ck . "/(6.89*" . $pst. ")" ;?> = <?php echo $beta; ?></p>
<p>τ<sub>c</sub> =  0.85*&radic;(0.8*ƒ<sub>ck</sub>)*(&radic;(1+5*β)-1)/(6*β) = <?php echo "0.85*&radic;(0.8*" . $f_ck . ")*(&radic;(1+5*" . $beta . "-1)/(6*" . $beta . ")"; ?> = <?php echo $tau_c; ?></p>
<p>A<sub>sv</sub> = n<sub>leg</sub>*π*φ<sub>s</sub><sup>2</sup>/4 = <?php echo $nleg . "*π*" . $phi_s . "<sup>2</sup>/4" ; ?> = <?php echo $Asv ?> mm<sup>2</sup>.</p>
<p>V<sub>uc</sub> = τ<sub>c</sub>*b*d*10<sup>-3</sup> = <?php echo $tau_c . "*" . $b . "*" . $d . "*10<sup>-3</sup>" ;?> = <?php echo $Vuc; ?> kN.</p>
<p>V<sub>us</sub> = 0.87*ƒ<sub>ck</sub>*A<sub>sv</sub>*d*10<sup>-3</sup>/s<sub>v</sub> = <?php echo "0.87*" . $f_y . "*" . $Asv . "*" . $d . "*" . "10<sup>-3</sup>/" . $s_v ; ?> = <?php echo $Vus; ?> kN.</p>
<p>V<sub>u</sub> = V<sub>u</sub>+V<sub>u</sub> = <?php echo $Vuc . "+" . $Vus;?> = <?php echo $Vu; ?> kN.</p>
</body>
</html>