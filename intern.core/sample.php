<html>

<script type="text/javascript">
	
	function compute(smpl){
		var D = parseFloat(smpl.b.value);
        alert(D);
		smpl.b.value=D+1/2;
	}
</script>

<form name="sample">
<table>
<input name="b" type="text" id="input" value="275" />
<br />
<input type="button" value="Compute" onclick="compute(sample)" />
</table>
</form>
</html>