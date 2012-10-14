<?php
$tab_devis = $_POST['selectionDevis'];
if (empty($tab_devis)) {
  echo 'Aucun devis selectionné';
}
elseif ($_POST['make_factures'] == 'créer les factures')
{
  while ($no_devis = array_pop($tab_devis)) {
    $devis = new devis($no_devis);
    $facture = new facture();
    $facture->fac_titre = $devis->dev_titre;
    $facture->client = $devis->client;
    $facture->contact = $devis->contact;
    $facture->acompte = $devis->acompte;
    $facture->fac_info = $devis->dev_info;
    $facture->fac_statut = 'À imprimer';
    $devis->dev_statut = 'Classé';
    $facture->fac_id = NULL;
    foreach ($devis->tab_lde as $no_ligne => $ligne_devis){
      $facture->tab_lfa[] =  new ligne_facture();
      $facture->tab_lfa[$no_ligne]->lfa_type = $devis->tab_lde[$no_ligne]->lde_type;
      $facture->tab_lfa[$no_ligne]->lfa_designation = $devis->tab_lde[$no_ligne]->lde_designation;
      $facture->tab_lfa[$no_ligne]->lfa_qtt = $devis->tab_lde[$no_ligne]->lde_qtt;
      $facture->tab_lfa[$no_ligne]->lfa_qtt = $devis->tab_lde[$no_ligne]->lde_qtt;
      $facture->tab_lfa[$no_ligne]->lfa_pu_achat = $devis->tab_lde[$no_ligne]->lde_prix_achat;
      $facture->tab_lfa[$no_ligne]->lfa_prix_ht = $devis->tab_lde[$no_ligne]->lde_prix_vente;
    }
    echo $facture->enreg();
    $devis->enreg();
    //echo $facture->fac_titre .' enregistrée';
    //echo $facture->edit();
  }
  echo 'Les Factures enregistrées sont modifiables par le menu factures';
}    
elseif ($_POST['sign_devis'] == 'marquer comme signé') {
  while ($no_devis = array_pop($tab_devis)) {
    $devis = new devis($no_devis);
    $devis->dev_statut = 'Signé';
    $devis->enreg();
  }
  echo 'Les devis sélectionnés sont marqués comme signé';
}
elseif ($_POST['fact_devis'] == 'marquer comme à facturer') {
  while ($no_devis = array_pop($tab_devis)) {
    $devis = new devis($no_devis);
    $devis->dev_statut = 'À facturer';
    $devis->enreg();
  }
  echo 'Les devis sélectionnés sont marqués comme à facturer';
}
elseif ($_POST['lost_devis'] == 'marquer comme perdu') {
  while ($no_devis = array_pop($tab_devis)) {
    $devis = new devis($no_devis);
    $devis->dev_statut = 'Perdu';
    $devis->enreg();
  }
  echo 'Les devis sélectionnés sont marqués comme perdu';
}
?>
