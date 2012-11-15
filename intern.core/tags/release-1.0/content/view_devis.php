<?php
  if (isset($_GET['show_marge'])) {
    $switch = $_GET['show_marge'];
  } else {
    $switch ="";
  }
  if (isset($_GET['vue'])) {
    $view = $_GET['vue'];
  } else {
    $view = '';
  }
  if (isset($_POST['quickSearch'])) {
    $_POST = array('TEMPS' => 'tout', 'cli_id'=>'tous', 'Statut'=>array($_POST['quickSearch']));
  }

	if ($view == '') {
		$_POST = array('TEMPS'=>'tout', 'cli_id'=>'tous', 'Statut'=>array('En cours'));
	}
	elseif ($view == 'client') {
		$ajout = array('TEMPS'=>'tout','Statut'=>array('En cours', 'À facturer','Signé','Perdu'));
		$_POST = $_GET + $ajout;
	}
	if (($view == 'formulaire') && ($_POST['marge'] == 'marge')) {
		$switch = 1;
  }
	

	$tab_devis = new liste_devis($_POST);
	echo $tab_devis->display_menu($_POST);
	echo $tab_devis->show($switch);

?>
