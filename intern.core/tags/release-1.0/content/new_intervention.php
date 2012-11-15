<?php
	$intervention= new intervention($_POST['int_id']);
	$intervention->cli_id = $_POST['cli_id'];
  if (isset($_POST['addPay']) && $_POST['addPay']) {
    $pay = 'pay';
  } else {
    $pay = 'free';
    }
	//$date = $intervention->set_int_date_intervention($_POST['int_date_intervention'], $_POST['heure']);
	$date = $intervention->set_int_date_intervention($_POST['int_date_intervention'], $_POST['timeEnd'], $_POST['timeStart']);
	if ($date) echo $date;
	$intervention->int_description = $_POST['int_description'];
	$intervention->use_id = $_POST['use_id'];
	$intervention->dev_id = $_POST['dev_id'];
	$intervention->int_createur_id = $_SESSION['ID'];
	if (!$intervention->test4enreg())
	{
    if (isset($_POST['prevenir']) && $_POST['prevenir'] == 'on') {
      echo $intervention->alertCustomer();
    }
		$intervention->enreg();
		echo '<div class="bandeau"><h1>Intervention Créée</h1></div>';
		echo $intervention ->show('', $pay);
		$client = new client($intervention->cli_id);
		echo $client->show_sum();
	}
	else
	{
		echo $intervention->test4enreg();
	}
?>
