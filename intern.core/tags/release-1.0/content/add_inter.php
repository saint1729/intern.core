<?php
$TITLE = '
<div class="bandeau">
	<h1>INTERVENTION</h1>
	<h2>Nouvelle intervention' . /*$pay .*/'</h2>
</div>
<span class="clear">&nbsp;</span>
';
	$intervention = new intervention();
  if (isset($_POST['assign']) && $_POST['assign']) {
    $param = stripslashes($_POST['objet_devis']);
    $devis = unserialize($param);
    $cli_id = $devis->client->cli_id;
    $intervention->dev_id = $devis->dev_id;
    $intervention->int_description = 'intervention lié au devis n° ' . $devis->dev_id . ': ' . $devis->dev_titre . "\n";
    foreach ($devis->tab_lde as $line) {
      if ($line->lde_type == 'Installation')
        $intervention->int_description .= $line->lde_designation . "\n";
    }
  } else {
    if (isset($_POST['cli_id'])) {
      $cli_id = $_POST['cli_id'];
    } else {
      $cli_id = '';
    }
  }
  echo $TITLE;
  if (isset($_POST['inter_fac']) && $_POST['inter_fac']) {
    echo $intervention->add_new($cli_id, '','addPay');
  } else {
    echo $intervention->add_new($cli_id);
  }
?>
