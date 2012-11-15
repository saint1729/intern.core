<?php
  $cli_id = $_SESSION['CLI_ID'];
	if ($view == '') {
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>$cli_id, 'Statut'=>array('À imprimer', 'En attente de reglement', 'Reglée', 'Lettre simple', 'Lettre AR', 'Impayée'));
	}
	$tab_factures = new liste_factures($_POST);
	echo $tab_factures->show(false);

?>
