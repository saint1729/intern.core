<?php
  $cli_id = $_SESSION['CLI_ID'];
	if ($view == '') {
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>$cli_id, 'Statut'=>array('En cours', 'Classé'));
	}
	$tab_devis = new liste_devis($_POST);
	echo $tab_devis->show(false);

?>
