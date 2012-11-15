<?php
	$cle=$_POST['cle'];
	$a_savoir=$_POST['a_savoir'];
	$cli_id =$_POST['cli_id'];
	
	$client = new client($cli_id);
	echo $client->set_a_savoir($a_savoir, $cle);
	echo $client->show();
?>
