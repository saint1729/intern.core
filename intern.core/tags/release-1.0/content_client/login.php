<?php
$inter_id=$_GET['inter_id'];
if (!empty($inter_id)) $fin_lien = '&inter_id='.$inter_id;

echo $inter_id.'
<div class="formlogin"> 
	<form action="./index2.php?contenu=veriflogin'.$fin_lien.'" method="post">
		<table align="center" border="0">
			<tr>
				<td>Login :</td>
				<td><input type="text" name="login" maxlength="50"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password"name="pass" maxlength="50"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" value="login"></td>
			</tr>
		</table>
	</form>
</div>';
?>
