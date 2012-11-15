<?php
	$intervention= new intervention($_POST['int_id']);
	$intervention->cli_id = $_POST['cli_id'];
	$intervention->client = new client($intervention->cli_id);
	$jour = $_POST['jour'];
	$date = $intervention->set_date_by_day($jour);
	if ($date) echo $date;
	$intervention->int_description = $_POST['int_description'];
	$intervention->use_id = $intervention->client->cli_tech_id;
	$intervention->int_createur_id =1000;
	
	if (!$intervention->test4enreg())
	{
		$intervention->enreg();
		echo '<div class="bandeau"><h1>Intervention Créée</h1></div>';
		echo $intervention->show('no_rapport');
		echo $intervention->alert_tech();
	}
	else
	{
		echo $intervention->test4enreg();
	}
?>
