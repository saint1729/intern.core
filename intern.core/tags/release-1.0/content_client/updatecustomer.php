<?php
// attribution des variable récupérer dans le formulaire
	$client = new client;	
	$client->cli_societe = $_POST['cli_societe'];
	$client->cli_adresse = $_POST['cli_adresse'];
	$client->cli_code_postal = $_POST['cli_code_postal'];
	$client->cli_ville = $_POST['cli_ville'];
	$client->cli_telephone = $_POST['cli_telephone'];
	$client->cli_telecopie = $_POST['cli_telecopie'];
	$client->cli_id = $_POST['cli_id'];
	$contact = new contact;
	$contact->con_responsable = $_POST['con_responsable'];
	$contact->con_nom = $_POST['con_nom'];
	$contact->con_prenom = $_POST['con_prenom'];
	$contact->con_email = $_POST['con_email'];
	$contact->con_portable= $_POST['con_portable'];
	$contact->con_autre = $_POST['con_autre'];
	$contactwarn='';
	$clientwarn=''; 
	$clientshow='';
	if ($_POST['pass1']==$_POST['pass2'] AND !empty($_POST['pass1']))
	{
		$client->cli_pwd = md5($_POST['pass1']);
		$pass_warn = '
			<div class="presentation"><h3> Le mot de passe a été changé!</h3></div>
		';
	}
	else
	{
		$pass_warn = '
			<div class="presentation"><h3> Le mot de passe reste identique</h3></div>
		';
	}
// controle des variables  et enregistrement
	if ($client->test4enreg())
	{
		if ($client->cli_id=="")
			$h1='nouveau client créé';
		else
			$h1='modification enregistré';
		$client->enreg('vue_client');
		if ($contact->test4enreg())
		{
			$h2='nouveau personnel créé';
			$contact->enreg();
			relier($client->get_cli_id(), $contact->get_id());
			$contactwarn= $contact->warning();
		}
		else
		{
			$h2='Pas de création de personnel';	
		}
		$clientshow= $client->show('vue_client');
	}
	else
	{
		$h1='Pas de création de client';	
	}
	echo '<div class="bandeau"><h1>'.$h1.'</h1><h2>'.$h2.'</h2></div>';
	echo $clientwarn;
	echo $contactwarn;
	echo $pass_warn;
	echo $clientshow;
?>
