<?php
	$intervention= new intervention($_GET['int_id']);
	echo '<div class="bandeau"><h1>Intervention</h1><h2>Voir</h2></div>';
	if ($intervention->int_rapport == '')
	{
		echo $intervention ->show();
	}
	else
	{
		echo $intervention ->show('show_rapport');
	}	
	$client = new client($intervention->cli_id);
	echo '<div class="nouveau">&nbsp;</div>';
	echo $client->show_sum();
?>
