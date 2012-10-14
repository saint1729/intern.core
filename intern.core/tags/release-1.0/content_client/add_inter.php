<div class="bandeau">
	<h1>INTERVENTION</h1>
	<h2>Nouvelle intervention</h2>
</div>
<span class="clear">&nbsp;</span>
<?php
	$cli_id = $_POST['cli_id'];
	$intervention= new intervention();
	echo $intervention->add_new($cli_id, 'client');
?>
