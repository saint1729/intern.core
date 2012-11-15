<div class="bandeau">
	<h1>CLIENTS</h1>
	<h2>Modifier le client</h2>
</div>
<span class="clear">&nbsp;</span>
<?php
	$cli_id = $_POST['cli_id'];
	$client = new client($cli_id);
	echo $client->edit();
?>
