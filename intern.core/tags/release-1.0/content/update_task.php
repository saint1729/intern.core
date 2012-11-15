<?php
	$intervention = new intervention($_POST['int_id']);
	$intervention->int_rapport = $_POST['int_rapport'];
	$result=$intervention->set_int_tps_passe($_POST['int_tps_passe']);
	if ($result!=0) echo $result;
	$intervention->int_status = $_POST['int_status'];
  $intervention->int_deplacement = $_POST['int_deplacement'];
  if  ($intervention->checkForUpdate()){
    echo $intervention->checkForUpdate();
    echo $intervention->edit();
    die();
  }
	echo $intervention->update();
	echo $intervention->show('show_rapport');
	$email_tech = $intervention->get_tech_mail();
	echo $intervention->send_mail($email_tech);
  if ($_POST['pay']) {
    $cli_id = $intervention->cli_id;
    $devis = new devis(array("CLI_ID" => "$cli_id", "DEV_ACOMPTE" => "30"));
    $devis->dev_titre = $intervention->int_description;
    $devis->dev_statut = 'À facturer';
    $devis->tab_lde[] = new ligne_devis;
    $devis->tab_lde[0]->lde_designation = $intervention->int_rapport;
    $devis->tab_lde[0]->lde_qtt = $intervention->int_tps_passe/60;
    $devis->tab_lde[0]->lde_type = 1;
    echo $devis->edit();
  }
?>
