<?php
// attribution des variable récupérer dans le formulaire
	$client = new client;	
	$client->cli_societe = $_POST['cli_societe'];
	$client->cli_adresse = $_POST['cli_adresse'];
	$client->cli_code_postal = $_POST['cli_code_postal'];
	$client->cli_ville = $_POST['cli_ville'];
	$client->cli_telephone = $_POST['cli_telephone'];
	$client->cli_telecopie = $_POST['cli_telecopie'];
	$client->cli_siret = $_POST['cli_siret'];
	$client->cli_type = $_POST['cli_type'];
	$client->cli_tva = $_POST['cli_tva'];
	$client->cli_maintenance = $_POST['cli_maintenance'];
	$client->cli_echeances = $_POST['cli_echeances'];
	$client->cli_tech_id = $_POST['use_id'];
	$client->cli_nb_facture = $_POST['cli_nb_facture'];
	$client->cli_id = $_POST['cli_id'];
	$client->cli_rezo_box = $_POST['cli_rezo_box'];
	$client->cli_rezo_backup = $_POST['cli_rezo_backup'];
	$client->cli_mtt_maintenance = $_POST['cli_mtt_maintenance'];
	$client->cli_echeances_abo = $_POST['cli_echeance_abo'];
	$client->cli_premiere_echeance = $_POST['cli_premiere_echeance'];
  $client->cli_nb_poste = $_POST['cli_nb_poste'];
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
// controle des variables  et enregistrement
	if ($client->test4enreg() && ($contact->test4enreg() || $client->cli_id != "" )) {
		if ($client->cli_id=="") 
			$h1='nouveau client créé';
		else
			$h1='client modifié';
		$client->enreg();
		$clientwarn= $client->warning();
		if ($contact->test4enreg()) {
			$h2='nouveau personnel créé';
			$contact->enreg();
			relier($client->get_cli_id(), $contact->get_id());
			$contactwarn= $contact->warning();
		}
		else {
			$h2='Pas de création de personnel';	
		}
		$clientshow= $client->show();
	}
	else {
		$h1='Pas de création de client';	
	}
	echo '<div><h1>'.$h1.'</h1><h2>'.$h2.'</h2></div>';
	echo $clientwarn;
	echo $contactwarn;
	echo $clientshow;
?>
