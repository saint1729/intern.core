<div class="bandeau">
	<h1>INTERVENTION</h1>
	<h2>Modifier la demande</h2>
</div>
<span class="clear">&nbsp;</span>
<?php
	$intervention= new intervention($_POST['int_id']);
	echo $intervention->add_new();
?>
