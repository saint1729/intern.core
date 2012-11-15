<?php
	$intervention = new intervention($_POST['int_id']);
	$sujet = 'Inforezo prévoit une intervention chez vous le '.$intervention->get_int_date_intervention();
	$contenu = 'Pour effectuer les taches suivantes:
	'.$intervention->int_description;
	$expediteur = $intervention->get_tech_mail();
	echo $intervention->send_mail($expediteur, $sujet, $contenu);
?>
