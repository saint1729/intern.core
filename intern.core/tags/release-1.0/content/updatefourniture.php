<?php
	// attribution des variable récupérer dans le formulaire
	$fou_id =$_POST['fou_id'];
	$cli_id = $_POST['cli_id'];
	$fou_designation = $_POST['fou_designation'];
	$fou_status = $_POST['fou_status'];
	$dea_id = $_POST['dea_id'];
	$fou_reference =  $_POST['fou_reference'];
	$fou_qtt = $_POST['fou_qtt'];
	
   // controle des variables   
   if(empty($fou_designation))
   {
   print("<center>La '<b>designation</b>' est vide !</center>");
   exit();
   }
	// Conection à la base de donnée
	mysqlinforezo();
	//insertion des données dans la base
	$sql = "UPDATE T_FOURNITURE  SET CLI_ID=$cli_id , FOU_DESIGNATION='$fou_designation', FOU_STATUS='$fou_status', DEA_ID=$dea_id, FOU_REFERENCE = '$fou_reference', FOU_QTT=$fou_qtt WHERE FOU_ID = $fou_id;";
	
	
	
	$query = mysql_query($sql) or die( 'Erreur lors de l\'insertion des données la commande n\'a pas été insérée'.$sql );
	// information pour l'utilisateur
	
	echo 'La commande vient d\'être modifier';
	mysql_close();
?>
