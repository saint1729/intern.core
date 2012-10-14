<?php
	// connexion à la base de donnée
	// information pour l'utilisateur
	if ($_SESSION['pwd']=='6f7ed1189244954a7b2a9178807abca0')
	{
		$client=new client($_SESSION['CLI_ID']);
		echo $client->change_pass();
	}
	else
	{
		$inter_id = $_GET['inter_id'];
		if (!empty($inter_id))
		{
			$inter = new intervention($inter_id);
			echo $inter->show('show_rapport_no_modif');
		}
		else
		{
			$client=new client($_SESSION['CLI_ID']);
			echo $client->show('vue_client');
		}
	}
		
?>
