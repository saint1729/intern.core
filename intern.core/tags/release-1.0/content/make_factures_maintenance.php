<?php
    echo '<form method = "get" action = "tmp/maintenance.pdf">
        <input type="submit" name="print" value = "Imprimer">
      </form>';
    echo '<form method = "post" action = "index2.php?contenu=view_factures">
        <input type="submit" name="back" value = "Retour">
      </form>';
$tab_client = new liste_client();
$tab_client->select();
$current_copy = 0;
foreach ($tab_client->tableau_client as $client)
  if ($client->haveToPay()) {
    echo '<p>' . 
    $client->cli_societe . ' ' .
    $client->cli_mtt_maintenance . ' ' .
    $client->cli_rezo_box . ' ' .
    $client->cli_rezo_backup . ' ' .
    $client->cli_echeances_abo . ' ' .
    $client->cli_premiere_echeance . ' ' .
    '</p>';
    $array[$current_copy] = $client->makeFacture();
    $current_copy++;
  }
  $pdf =& new concat_pdf();
  $pdf->setFiles($array);
  $pdf->concat();
  $pdf->Output('tmp/maintenance.pdf');
?>
