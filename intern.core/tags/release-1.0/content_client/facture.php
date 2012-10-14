<?php
  if (isset($_GET['fac_id']))
  {
    $fac_id = intval($_GET['fac_id']);
    $facture = new facture($fac_id);
  }
  if ($facture->client->cli_id == $_SESSION['CLI_ID']){
    echo $facture->preview();
    echo $facture->write_pdf();
  } else {
    echo "cette facture ne vous est pas destinée";
  }
?>
