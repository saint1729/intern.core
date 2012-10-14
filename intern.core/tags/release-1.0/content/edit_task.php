<?php
  if ($_POST['pay']) {
    $pay= 'pay';
  } else {
    $pay = 'free';
  }
	$int_id = $_POST['int_id'];
	$intervention = new intervention($int_id);
	echo $intervention->edit(true, $pay);
