<?php
	$client=new client($_POST['cli_id']);
	echo $client->show_a_savoir($_POST['cle']);
?>
