<?php
  $tab_factures = $_POST['selectionFacture'];
  if (empty($tab_factures)) {
    echo 'Aucune facture selectionnée';
  } elseif ($_POST['print_facture']) {
    $current_copy = 0;
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $array[$current_copy] = $facture->write_pdf(false);
      $current_copy++;
    }
    printr($array);
    $pdf =& new concat_pdf();
    $pdf->setFiles($array);
    $pdf->concat();
    $pdf->Output('tmp/liste.pdf');
    echo '<form method = "get" action = "tmp/liste.pdf">
        <input type="submit" name="print" value = "Imprimer">
      </form>';
  } elseif ($_POST['mark_to_print']) {
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $facture->fac_statut = 'À imprimer';
      $facture->enreg();
    }
      echo 'Les factures sélectionnée sont marquée comme À imprimer';
  } elseif ($_POST['mark_wait']) {
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $facture->fac_statut = 'En attente de reglement';
      $facture->enreg();
    }
      echo 'Les factures sélectionnée sont marquée comme en attente de reglement';
  } elseif ($_POST['mark_paid']) {
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $facture->fac_statut = 'Reglée';
      $facture->enreg();
    }
      echo 'Les factures sélectionnée sont marquée comme payées';
  } elseif ($_POST['mark_unpaid']) {
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $facture->fac_statut = 'Impayée';
      $facture->enreg();
    }
      echo 'Les factures sélectionnée sont marquée comme impayées';
  } elseif ($_POST['send_letter']) {
    $current_copy = 0;
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $array[$current_copy] = $facture->write_letter();
      $current_copy++;
    }
    printr($array);
    $pdf =& new concat_pdf();
    $pdf->setFiles($array);
    $pdf->concat();
    $pdf->Output('tmp/lettres_simples.pdf');
    echo '<form method = "get" action = "tmp/lettres_simples.pdf">
      <input type="submit" name="print" value ="Imprimer">
      </form>';
  } elseif ($_POST['send_AR']) {
   $current_copy = 0;
   while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $array[$current_copy] = $facture->write_AR();
      $current_copy++;
    }
    $pdf =& new concat_pdf();
    $pdf->setFiles($array);
    $pdf->concat();
    $pdf->Output('tmp/lettres_AR.pdf');
    echo '<form method = "get" action = "tmp/lettres_AR.pdf">
      <input type="submit" name="print" value ="Imprimer">
      </form>';
  } elseif ($_POST['export']) {
    $file = "Journal;DateEcr;Compte;Piece;Libelle;Debit;Credit\n";
    while ($no_facture = (int) array_pop($tab_factures)) {
      $facture = new facture($no_facture);
      $file .= $facture->export();
    }
    file_put_contents('tmp/export.ebp',$file);
    echo '<form method = "get" action = "tmp/export.ebp">
      <input type="submit" name="print" value ="telecharger">
      </form>';
    echo str_replace("\n", "<br>", $file);
  }
?>
