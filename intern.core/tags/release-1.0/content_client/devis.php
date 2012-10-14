<?php
  if (isset($_GET['dev_id']))
  {
    $dev_id = intval($_GET['dev_id']);
    $devis = new devis($dev_id);
  }
  if ($devis->client->cli_id == $_SESSION['CLI_ID']){
    echo $devis->preview();
    echo $devis->write_pdf();
  } else {
    echo "ce devis ne vous est pas destiné";
  }
?>
