<div class="bandeau">
	<h1>CLIENTS</h1>
	<h2>Voir le client</h2>
</div>
<span class="clear">&nbsp;</span>
<?php
	$cli_id = $_GET['cli_id'];
	$client = new client($cli_id);
	echo $client->show();
?>
