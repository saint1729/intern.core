<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Beam</title>

<script type="text/javascript">

	function compute(f){

	var b = parseFloat(f.b.value);
	var d = parseFloat(f.d.value);
	var d2 = parseFloat(f.d2.value);
	var Ast = parseFloat(f.Ast.value);
	//var Asc = parseFloat(f.Asc.value);
	var x=1;
	var y=1;
	var z=1;
	
	var x0=document.getElementById("mySelect").selectedIndex;
	var y0=document.getElementById("mySelect").options;
	
	if(y0[x0].text=='2-10 φ') {
		var Asc = 157;
	}
	else if(y0[x0].text=='2-12 φ') {
		var Asc = 226;
	}
	else if(y0[x0].text=='3-10 φ') {
		var Asc = 235.5;
	}
	else if(y0[x0].text=='2-10 φ & 1-12 φ') {
		var Asc = 270;
	}
	else if(y0[x0].text=='1-10 φ & 2-12 φ') {
		var Asc = 304.5;
	}
	else if(y0[x0].text=='2-16 φ') {
		var Asc = 402;
	}
	else if(y0[x0].text=='2-12 φ & 1-16 φ') {
		var Asc = 427;
	}
	else if(y0[x0].text=='1-12 φ & 2-20 φ') {
		var Asc = 515;
	}
	else if(y0[x0].text=='2-12 φ & 1-20 φ') {
		var Asc = 540;
	}
	else if(y0[x0].text=='2-12 φ & 2-16 φ') {
		var Asc = 628;
	}
	else if(y0[x0].text=='2-16 φ & 1-20 φ') {
		var Asc = 716;
	}
	else if(y0[x0].text=='3-12 φ & 2-16 φ') {
		var Asc = 741;
	}
	else if(y0[x0].text=='2-12 φ & 3-16 φ') {
		var Asc = 829;
	}
	else if(y0[x0].text=='2-12 φ & 2-20 φ') {
		var Asc = 854;
	}
	else if(y0[x0].text=='2-16 φ & 1-25 φ') {
		var Asc = 892;
	}
	else if(y0[x0].text=='3-12 φ & 2-20 φ') {
		var Asc = 967;
	}
	else if(y0[x0].text=='2-16 φ & 2-20 φ') {
		var Asc = 1030;
	}
	else if(y0[x0].text=='2-20 φ & 1-25 φ') {
		var Asc = 1118;
	}
	else if(y0[x0].text=='2-12 φ & 3-20 φ') {
		var Asc = 1168;
	}
	else if(y0[x0].text=='1-16 φ & 2-25 φ') {
		var Asc = 1181;
	}
	else if(y0[x0].text=='1-20 φ & 2-25 φ') {
		var Asc = 1294;
	}
	else if(y0[x0].text=='3-12 φ & 2-25 φ') {
		var Asc = 1319;
	}
	else if(y0[x0].text=='2-16 φ & 2-25 φ') {
		var Asc = 1382;
	}
	else if(y0[x0].text=='2-20 φ & 1-32 φ') {
		var Asc = 1432;
	}
	else if(y0[x0].text=='2-20 φ & 2-25 φ') {
		var Asc = 1608;
	}
	else if(y0[x0].text=='3-20 φ & 2-25 φ') {
		var Asc = 1922;
	}
	else if(y0[x0].text=='2-20 φ & 3-25 φ') {
		var Asc = 2098;
	}
	else if(y0[x0].text=='2-20 φ & 2-32 φ') {
		var Asc = 2236;
	}
	else if(y0[x0].text=='3-20 φ & 2-32 φ') {
		var Asc = 2550;
	}
	else if(y0[x0].text=='2-20 φ & 3-32 φ') {
		var Asc = 3040;
	}
	else if(y0[x0].text=='2-25 φ & 3-32 φ') {
		var Asc = 3392;
	}

	if(b<200 || b>350)
		{
			alert('Value of Width should be between 200 and 350');
			x = 0;
		}
	if(d<200 || d>700)
		{
			alert('Value of Width should be between 200 and 700');
			y = 0;
		}
	
	var Ast_bal = 1.43*b*d/100.0;
	if(Ast_bal > Ast)
		{
			alert('Value of Ast < balanced, Hence not feasible.');
			z = 0;
		}

	if (x==1 && y==1 && z==1)
		{
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
			}
			else if(document.getElementById('Fe415').checked) {
				var f_y = 415;
			}
			else if(document.getElementById('Fe500').checked) {
				var f_y = 500;
			}


			var Mu1 = 0.138*f_ck*b*d*d/1000000.0;


			var rat = Math.round(d2/d*100)/100.0;
			//alert(rat);
			if(rat==0.03 || rat==0.04 || rat==0.05 || rat==0.06 || rat==0.07)
				{
					var f_sc = 355.0;
				}
			else if(rat==0.08 || rat==0.09 || rat==0.1 || rat==0.11 || rat==0.12)
				{
					var f_sc = 353.0;
					//alert(f_sc);
				}
			else if(rat==0.13 || rat==0.14 || rat==0.15 || rat==0.16 || rat==0.17)
				{
					var f_sc = 342.0;
				}
			else if(rat==0.18 || rat==0.19 || rat==0.2 || rat==0.21 || rat==0.22)
				{
					var f_sc = 329.0;
				}

				
				
				var Cs = f_sc*Asc/1000.0;
				var Mu2 = Cs*(d-d2)/1000.0;
				var Muc = Mu1 + Mu2;
				var Ast2 = Ast - Ast_bal;
				var per = Ast2*100/(b*d);
				var w=1;
				
				if (per<0.2)
					{
						alert('Percentage of steel < 0.2% i.e., less than nominal.');
						w=0;
					}
				
				if(w == 1)
					{
						//alert('1');
						f.Mu1.value = Math.round(Mu1*10)/10;
						f.Ast_bal.value = Math.round(Ast_bal);
						f.f_sc.value = f_sc;
						f.Mu2.value = Math.round(Mu2*10)/10;
						f.Muc.value = Math.round(Muc*10)/10;
						f.Ast2.value = Math.round(Ast2);
						var Mut = Mu1 + Ast2*(0.87*f_y)*(d-d2)/1000000.0;
						
						if(Mut<=Muc)
							{
								var Mu = Mut;
							}
						else
							{
								var Mu = Muc;
							}

						f.Mut.value = Math.round(Mut*10)/10;
						f.Mu.value = Math.round(Mu*10)/10;
					}

				else
					{
						f.b.value = 280;
						f.D.value = 510;
					}
        }
	else
		{
			f.b.value = 280;
			f.D.value = 510;
		}
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
<form name="beam" action="beam_print2.php" method="post">
<table id="center">
    <tr>
    	<td width="257"><div align="center" style="font-weight:bold;">Input Variables</div></td>
        <td width="257"><div align="center" style="font-weight:bold;">Results</div></td>
    </tr>
    <tr>
		<td>
            <table width="373" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td>Width (<em>b</em> mm.) :</td>
                <td><input name="b" type="text" id="input" value="280"/></td>
              </tr>
              <tr>
                <td> Effective depth (<em>d</em> mm.) :</td>
                <td><input name="d" type="text" id="input" value="510"/></td>
              </tr>
              <tr>
                <td><em>d'</em> (mm.):</td>
                <td><input name="d2" type="text" id="input" value=""/></td>
              </tr>
              <tr>
                <td><em>A</em><sub>st</sub> (mm<sup>2</sup>):</td>
                <td><input name="Ast" type="text" id="input" value=""/></td>
              </tr>
              <tr>
                <td><em>A</em><sub>sc</sub> (mm<sup>2</sup>):</td>
                <td><select name="Asc" id="mySelect">
                      <option value="2-10 φ" id="2-10" value="157" >2-10 φ</option>
                      <option value="2-12 φ" id="2-12" value="226" >2-12 φ</option>
                      <option value="3-10 φ" id="3-10" value="235.5" >3-10 φ</option>
                      <option value="2-10 φ & 1-12 φ" id="2-10_1-12" value="270" >2-10 φ &amp; 1-12 φ</option>
                      <option value="1-10 φ & 2-12 φ" id="1-10_2-12" value="304.5" >1-10 φ &amp; 2-12 φ</option>
                      <option value="2-16 φ" selected="selected" id="2-16" value="402" >2-16 φ</option>
                      <option value="2-12 φ & 1-16 φ" id="2-12_1-16" value="427" >2-12 φ &amp; 1-16 φ</option>
                      <option value="1-12 φ & 2-20 φ" id="1-12_2-20" value="515" >1-12 φ &amp; 2-20 φ</option>
                      <option value="2-12 φ & 1-20 φ" id="2-12_1-20" value="540" >2-12 φ &amp; 1-20 φ</option>
                      <option value="2-12 φ & 2-16 φ" id="2-12_2-16" value="628" >2-12 φ &amp; 2-16 φ</option>
                      <option value="2-16 φ & 1-20 φ" id="2-16_1-20" value="716" >2-16 φ &amp; 1-20 φ</option>
                      <option value="3-12 φ & 2-16 φ" id="3-12_2-16" value="741" >3-12 φ &amp; 2-16 φ</option>
                      <option value="2-12 φ & 3-16 φ" id="2-12_3-16" value="829" >2-12 φ &amp; 3-16 φ</option>
                      <option value="2-12 φ & 2-20 φ" id="2-12_2-20" value="854" >2-12 φ &amp; 2-20 φ</option>
                      <option value="2-16 φ & 1-25 φ" id="2-16_1-25" value="892" >2-16 φ &amp; 1-25 φ</option>
                      <option value="3-12 φ & 2-20 φ" id="3-12_2-20" value="967" >3-12 φ &amp; 2-20 φ</option>
                      <option value="2-16 φ & 2-20 φ" id="2-16_2-20" value="1030" >2-16 φ &amp; 2-20 φ</option>
                      <option value="2-20 φ & 1-25 φ" id="2-20_1-25" value="1118" >2-20 φ &amp; 1-25 φ</option>
                      <option value="2-12 φ & 3-20 φ" id="2-12_3-20" value="1168" >2-12 φ &amp; 3-20 φ</option>
                      <option value="2-16 φ & 2-25 φ" id="2-16_2-25" value="1181" >2-16 φ &amp; 2-25 φ</option>
                      <option value="1-20 φ & 2-25 φ" id="1-20_2-25" value="1294" >1-20 φ &amp; 2-25 φ</option>
                      <option value="3-12 φ & 2-25 φ" id="3-12_2-25" value="1319" >3-12 φ &amp; 2-25 φ</option>
                      <option value="2-16 φ & 2-25 φ" id="2-16_2-25" value="1382" >2-16 φ &amp; 2-25 φ</option>
                      <option value="2-20 φ & 1-32 φ" id="2-20_1-32" value="1432" >2-20 φ &amp; 1-32 φ</option>
                      <option value="2-20 φ & 2-25 φ" id="2-20_2-25" value="1608" >2-20 φ &amp; 2-25 φ</option>
                      <option value="3-20 φ & 2-25 φ" id="3-20_2-25" value="1922" >3-20 φ &amp; 2-25 φ</option>
                      <option value="2-20 φ & 3-25 φ" id="2-20_3-25" value="2098" >2-20 φ &amp; 3-25 φ</option>
                      <option value="2-20 φ & 2-32 φ" id="2-20_2-32" value="2236" >2-20 φ &amp; 2-32 φ</option>
                      <option value="3-20 φ & 2-32 φ" id="3-20_2-32" value="2550" >3-20 φ &amp; 2-32 φ</option>
                      <option value="2-20 φ & 3-32 φ" id="2-20_3-32" value="3040" >2-20 φ &amp; 3-32 φ</option>
                      <option value="2-25 φ & 3-32 φ" id="2-25_3-32" value="3392" >2-25 φ &amp; 3-32 φ</option>
                    </select>
                </td>
              </tr>


              <!--<tr>
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
              </tr>-->
            </table>
            
			&nbsp;&nbsp;&nbsp;
            
            Concrete:

            <input type="radio" name="f_ck" id="M15" value="15"> M15
            <input type="radio" name="f_ck" id="M20" value="20"> M20
			<input type="radio" name="f_ck" id="M25" value="25"> M25
			<input type="radio" name="f_ck" id="M30" value="30" checked="checked"> M30


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
                <td><input name="f_ck" type="text" id="input" value="30"/></td>
              </tr>
              <tr>
                <td>Steel Strength (N/mm<sup>2</sup>) :</td>
                <td><input name="f_y" type="text" id="input" value="415"/></td>
              </tr>
              
              <tr>
                <td>Mom. of resist. due to concrete (<em>M</em><sub>u1</sub>) (kNm) :</td>
                <td><input name="Mu1" type="text" id="input"/></td>
              </tr>
              
              <tr>
                <td><em>A</em><sub>st(bal.)</sub> (mm<sup>2</sup>) :</td>
                <td><input name="Ast_bal" type="text" id="input"/></td>
              </tr>
              
              <tr>
                <td><em>f</em><sub>sc</sub> (N/mm<sup>2</sup>) :</td>
                <td><input name="f_sc" type="text" id="input"/></td>
              </tr>
              
              <tr>
                <td>Mom. of resist. due to steel (<em>M</em><sub>u2</sub>) (kNm) :</td>
                <td><input name="Mu2" type="text" id="input"/></td>
              </tr>
              
              <tr>
                <td>Compression Failure (<em>M</em><sub>uc</sub>) (kNm<sup></sup>) :</td>
                <td><input name="Muc" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Additional Steel (<em>A</em><sub>st2</sub>) (mm<sup>2</sup>) :</td>
                <td><input name="Ast2" type="text" id="input"/></td>
              </tr>
              <tr>
                <td>Ultimate moment capacity (<em>M</em><sub>ut</sub>) (kNm) :</td>
                <td><input name="Mut" type="text" id="input"/></td>
              </tr>
              <tr>
                <td><em>M</em><sub>u</sub>(kNm) :</td>
                <td><input name="Mu" type="text" id="input"/></td>
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