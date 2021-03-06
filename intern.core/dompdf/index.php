<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Beam</title>

<script type="text/javascript">

	function compute(f){

	var b = parseFloat(f.b.value);
	var D = parseFloat(f.D.value);
	var cc = parseFloat(f.cc.value);
	var n1 = parseFloat(f.n1.value);
	var phi_1 = parseFloat(f.phi_1.value);
	var n2 = parseFloat(f.n2.value);
	var phi_2 = parseFloat(f.phi_2.value);
	var phi_s = parseFloat(f.phi_s.value);
	var nleg = parseFloat(f.nleg.value);
	var s_v = parseFloat(f.s_v.value);
	
	if(document.getElementById('M15').checked) {
		var f_ck = 15;
	}
	else if(document.getElementById('M20').checked) {
		var f_ck = 20;
	}
	else if(document.getElementById('M25').checked) {
		var f_ck = 25;
	}
	else if(document.getElementById('M30').checked) {
		var f_ck = 30;
	}
	
	if(document.getElementById('Fe250').checked) {
		var f_y = 250;
		var eps_su = 0.0031;
	}
	else if(document.getElementById('Fe415').checked) {
		var f_y = 415;
		var eps_su = 0.0038;
	}
	else if(document.getElementById('Fe500').checked) {
		var f_y = 500;
		var eps_su = 0.0042;
	}
		
		var d = D-cc-0.5*phi_1;
		var Ast = (n1*3.14159*phi_1*phi_1)/4+(n2*3.14159*phi_1*phi_1)/4;
		var pst = 100.0*Ast/(b*D);
		var Asv = nleg*3.14159*phi_s*phi_s/4;
        var xumax_by_d = 0.0035/(0.0035+eps_su);
   		var pt_bal = 100.0*0.36*f_ck*xumax_by_d/(0.87*f_y);
   		var K = 0.36*xumax_by_d*(1.0-0.42*xumax_by_d);
		var Muc = K*f_ck*b*d*d*1.0*Math.pow(10,-6);
		var R  = (0.87*f_y*pst/100.0)*(1.0-1.005*(f_y/f_ck)*pst/100.0);
   		var Mus = R*b*d*d*1.0*Math.pow(10,-6);
	   	var tau_c_max = 0.62*Math.sqrt(f_ck);
   		var beta = 0.8*f_ck/(6.89*pst);
	   	var tau_c = 0.85*Math.sqrt(0.8*f_ck)*(Math.sqrt(1.0+5.0*beta)-1.0)/(6.0*beta);
   		var Asv = nleg*3.14159*phi_s*phi_s/4.0;
   		var Vuc = tau_c*b*d*1.0*Math.pow(10,-3);
   		var Vus = 0.87*f_y*Asv*d*1.0*Math.pow(10,-3)/s_v;
   		var Vu = Vuc+Vus;

		f.f_ck.value = Math.round(f_ck*1000)/1000;
		f.f_y.value = Math.round(f_y*1000)/1000;
		f.d.value = Math.round(d*1000)/1000;
		f.Ast.value = Math.round(Ast*1000)/1000;
		f.pst.value = Math.round(pst*100000)/100000;
		f.Asv.value = Math.round(Asv*1000)/1000;
		f.Muc.value = Math.round(Muc*1000)/1000;
		f.Mus.value = Math.round(Mus*1000)/1000;
		f.Vuc.value = Math.round(Vuc*1000)/1000;
		f.Vus.value = Math.round(Vus*1000)/1000;
		f.Vu.value = Math.round(Vu*1000)/1000;

	}
</script>

<style type="text/css">
input#input {
	width: 102px;
}
table#center {
    margin-left:auto; 
    margin-right:auto;
  }
</style>
</head>

<body>
<form name="beam" action="beam_print.php" method="post">
<table id="center">
    <tr>
    	<td width="257"><div align="center" style="font-weight:bold;">Input Variables</div></td>
        <td width="257"><div align="center" style="font-weight:bold;">Results</div></td>
    </tr>
    <tr>
		<td>
            <table width="373" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td>Width (mm) :</td>
                <td><input name="b" type="text" id="input" value="275"/></td>
              </tr>
              <tr>
                <td>Overall depth (mm) :</td>
                <td><input name="D" type="text" id="input" value="650"/></td>
              </tr>
              <tr>
                <td>Clear cover (mm) :</td>
                <td><input name="cc" type="text" id="input" value="30"/></td>
              </tr>
              <tr>
                <td>No.of long bars :</td>
                <td><input name="n1" type="text" id="input" value="4"></td>
              </tr>
              <tr>
                <td>Bar dia (mm) :</td>
                <td><input name="phi_1" type="text" id="input" value="25"/></td>
              </tr>
              <tr>
                <td>No.of long bars :</td>
                <td><input name="n2" type="text" id="input" value="0"/></td>
              </tr>
              <tr>
                <td>Bar dia (mm) :</td>
                <td><input name="phi_2" type="text" id="input" value="0"/></td>
              </tr>
              <tr>
                <td>Stirrup dia (mm) :</td>
                <td><input name="phi_s" type="text" id="input" value="8"/></td>
              </tr>
              <tr>
                <td>Stirrup spacing (mm) :</td>
                <td><input name="s_v" type="text" id="input" value="150"/></td>
              </tr>
              <tr>
                <td>No. of stirrup legs :</td>
                <td><input name="nleg" type="text" id="input" value="4"/></td>
              </tr>
            </table>
            
			&nbsp;&nbsp;&nbsp;
            
            Concrete:

            <input type="radio" name="f_ck" id="M15" value="15"> M15
            <input type="radio" name="f_ck" id="M20" value="20"> M20
			<input type="radio" name="f_ck" id="M25" value="25" checked="checked"> M25
			<input type="radio" name="f_ck" id="M30" value="30"> M30


<br />


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			Steel:

            <input type="radio" name="f_y" id="Fe250" value="250"> Fe250
			<input type="radio" name="f_y" id="Fe415" value="415" checked="checked"> Fe415
			<input type="radio" name="f_y" id="Fe500" value="500"> Fe500
      </td>

		<td>
            <table width="373" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td>Concrete Strength (N/mm<sup>2</sup>) :</td>
                <td><input name="f_ck" type="text" id="input" value="25"/></td>
              </tr>
              <tr>
                <td>Steel Strength (N/mm<sup>2</sup>) :</td>
                <td><input name="f_y" type="text" id="input" value="415"/></td>
              </tr>
              <tr>
                <td>Effective depth (mm) :</td>
                <td><input name="d" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Area of long steel (mm<sup>2</sup>) :</td>
                <td><input name="Ast" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Percentage of steel :</td>
                <td><input name="pst" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Mom. of resist. due to concrete (kNm) :</td>
                <td><input name="Muc" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Mom. of resist. due to steel (kNm) :</td>
                <td><input name="Mus" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Area of shear reinf. (mm<sup>2</sup>) :</td>
                <td><input name="Asv" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Critical shear due to concrete (kN) :</td>
                <td><input name="Vuc" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Shear capacity due to stirrup (kN) :</td>
                <td><input name="Vus" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Total shear capacity (kN) :</td>
                <td><input name="Vu" type="text" id="input"/></td>
              </tr>
            </table>
		</td>
	</tr>
</table>
<p align="center">
  <input type="button" value="Compute" onclick="compute(beam)" />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="submit" value="Print as pdf" />
</p>
</form>
</body>
</html>