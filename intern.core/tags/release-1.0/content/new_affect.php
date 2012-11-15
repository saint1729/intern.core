<?php
	$cli_id = $_POST['cli_id'];
	$affectation = new affectation($cli_id);
	$affectation->designation = $_POST['designation'];
	$affectation->SN = $_POST['SN'];
	$affectation->no_cmd = $_POST['no_cmd'];
	$affectation->type = $_POST['type']; 
	$affectation->qtt = $_POST['qtt'];
	echo $affectation->enreg();
	$client = new client ($cli_id);
	echo $client->show();
?>
