<?php
  if (isset($_GET['vue'])) {
    $view = $_GET['vue'];
  } else {
    $view = '';
  }
	if ($view == '')
	{
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>'tous', 'Statut'=>array('À imprimer', 'En attente de reglement', 'Lettre simple', 'Lettre AR'));
	}
	elseif ($view == 'client')
	{
		$ajout = array('TEMPS'=>'tout','Statut'=>array('En cours', 'À facturer','Signé','Perdu'));
		$_POST = $_GET + $ajout;
	}
	if (isset($_POST['marge']) && $_POST['marge'] == "marge"){
		$switch = true;
	} else {
    $switch = false;
  }
  if (isset($_POST['all']) && $_POST['all'])
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>'tous', 'Statut'=>array('À imprimer', 'En attente de reglement', 'Lettre simple', 'Lettre AR','Reglée', 'Impayée'));
  if (isset($_POST['waiting']) && $_POST['waiting'])
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>'tous', 'Statut'=>array('En attente de reglement'));
	$tab_factures = new liste_factures($_POST);
	echo $tab_factures->display_menu($_POST);
	echo $tab_factures->show($switch);

?>
