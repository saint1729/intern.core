<?php
  $client = new client($_GET['cli_id']);
  echo $client->write_contrat($_GET['ctype']);
?>
