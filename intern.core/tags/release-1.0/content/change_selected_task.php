<?php
	$tab_inter = $_POST['SELECTION'];
  if (isset($_POST['print']) && $_POST['print'] == 'Imprimer') {
    $selected_inter = new liste_inter($tab_inter);
    echo $selected_inter->make_pdf();
  }
  elseif (isset($_POST['delete']) && $_POST['delete'] == 'Supprimer') {
    foreach ($tab_inter as $no_inter)
    {
      $inter = new intervention($no_inter);
      echo $inter->delete();
    }
  }
?>
